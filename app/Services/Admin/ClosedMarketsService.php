<?php

namespace App\Services\Admin;

use App\Criteria\ClosedMarket\AdminIndexCriteria;
use App\Domain\StockInterface as StockService;
use App\Exceptions\InvalidInputException;
use App\Repositories\ClosedMarketRepository;
use App\Repositories\ItemDetailRepository;
use App\Repositories\ItemRepository;
use Exception;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpKernel\Exception\HttpException;

class ClosedMarketsService extends Service implements ClosedMarketsServiceInterface
{
    /**
     * @var ClosedMarketRepository
     */
    private $closedMarketRepository;

    /**
     * @var ItemDetailRepository
     */
    private $itemDetailRepository;

    /**
     * @var ItemRepository
     */
    private $itemRepository;

    /**
     * @var StockService
     */
    private $stockService;

    /**
     * @param ClosedMarketRepository $closedMarketRepository
     * @param ItemDetailRepository $itemDetailRepository
     */
    public function __construct(
        ClosedMarketRepository $closedMarketRepository,
        ItemDetailRepository $itemDetailRepository,
        ItemRepository $itemRepository,
        StockService $stockService
    ) {
        $this->closedMarketRepository = $closedMarketRepository;
        $this->itemDetailRepository = $itemDetailRepository;
        $this->itemRepository = $itemRepository;
        $this->stockService = $stockService;
    }

    /**
     * @param int $itemId
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function find(int $itemId)
    {
        $item = $this->itemRepository->find($itemId);

        $this->closedMarketRepository->pushCriteria(new AdminIndexCriteria(['item_id' => $item->id]));

        $closedMarkets = $this->closedMarketRepository->with(['itemDetail'])->paginate();

        $closedMarkets->each(function ($closedMarket) use ($item) {
            $closedMarket->url = \App\Domain\Utils\ClosedMarket::computeUrlPath($item, $closedMarket);
        });

        return $closedMarkets;
    }

    /**
     * 闇市設定を新規作成
     *
     * @param array $params
     * @param int $itemId
     *
     * @return \App\Models\ClosedMarket
     */
    public function store(array $params, int $itemId)
    {
        try {
            DB::beginTransaction();

            $itemDetail = $this->itemDetailRepository->with('item')->findWhere([
                'item_id' => $itemId,
                'size_id' => $params['size_id'],
                'color_id' => $params['color_id'],
            ])->first();

            if (empty($itemDetail)) {
                throw new HttpException(Response::HTTP_NOT_FOUND, error_format('error.model_not_found', [
                    'item_id' => $itemId,
                    'size_id' => $params['size_id'],
                    'color_id' => $params['color_id'],
                ]));
            }

            if (!$this->stockService->lockAndValidateEcStock($itemDetail->id, $params['num'])) {
                throw new InvalidInputException(['num' => __('validation.closed_market.stock_shortage')]);
            }

            $closedMarket = $this->closedMarketRepository->create([
                'item_detail_id' => $itemDetail->id,
                'title' => $params['title'],
                'password' => $params['password'],
                'num' => $params['num'],
                'stock' => $params['num'],
                'limit_at' => $params['limit_at'],
            ]);

            $closedMarket->url = \App\Domain\Utils\ClosedMarket::computeUrlPath($itemDetail->item, $closedMarket);

            $closedMarket->load('itemDetail');

            DB::commit();

            return $closedMarket;
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * @param array $params
     * @param int $itemId
     * @param int $id
     *
     * @return \App\Models\ClosedMarket
     */
    public function update(array $params, int $itemId, int $id)
    {
        try {
            DB::beginTransaction();

            $itemDetail = $this->itemDetailRepository->with('item')->findWhere([
                'item_id' => $itemId,
                'size_id' => $params['size_id'],
                'color_id' => $params['color_id'],
            ])->first();

            if (empty($itemDetail)) {
                throw new HttpException(Response::HTTP_NOT_FOUND, error_format('error.model_not_found', [
                    'item_id' => $itemId,
                    'size_id' => $params['size_id'],
                    'color_id' => $params['color_id'],
                ]));
            }

            $closedMarket = $this->closedMarketRepository->scopeQuery(function ($query) {
                return $query->lockForUpdate();
            })->find($id);

            $stock = $this->computeUpdatingStock($closedMarket, $itemDetail, $params['num']);

            if ($stock < 0) {
                throw new InvalidInputException(['num' => __('validation.closed_market.aleady_sold')]);
            }

            $requestedStock = $stock - $closedMarket->stock;

            if (!$this->stockService->lockAndValidateEcStock($itemDetail->id, $requestedStock)) {
                throw new InvalidInputException(['num' => __('validation.closed_market.stock_shortage')]);
            }

            if (!$closedMarket->hasStock(-$requestedStock)) {
                throw new InvalidInputException(['num' => __('validation.closed_market.aleady_secured')]);
            }

            $closedMarket = $this->closedMarketRepository->update([
                'item_detail_id' => $itemDetail->id,
                'title' => $params['title'],
                'password' => $params['password'],
                'num' => $params['num'],
                'limit_at' => $params['limit_at'],
            ], $id);

            $this->closedMarketRepository->addStock($id, $stock - $closedMarket->stock);

            $closedMarket->url = \App\Domain\Utils\ClosedMarket::computeUrlPath($itemDetail->item, $closedMarket);

            $closedMarket->load('itemDetail');

            DB::commit();

            return $closedMarket;
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * @param \App\Models\ClosedMarket $closedMarket
     * @param \App\Models\ItemDetail $itemDetail
     * @param int $num
     *
     * @return int
     */
    private function computeUpdatingStock(
        \App\Models\ClosedMarket $closedMarket,
        \App\Models\ItemDetail $itemDetail,
        int $num
    ) {
        if ($closedMarket->item_detail_id !== $itemDetail->id) {
            return $num;
        }

        if ($closedMarket->num === $num) {
            return $closedMarket->stock;
        }

        return $closedMarket->stock + ($num - $closedMarket->num);
    }
}

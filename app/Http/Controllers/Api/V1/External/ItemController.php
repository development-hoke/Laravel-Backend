<?php

namespace App\Http\Controllers\Api\V1\External;

use App\Domain\Exceptions\StockShortageException;
use App\Domain\StockInterface as StockService;
use App\Enums\Order\OrderType;
use App\Exceptions\InvalidInputException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Front\Item\ArriveBackOrderedRequest;
use App\Http\Requests\Api\V1\Front\Item\ArriveReservedRequest;
use App\Http\Requests\Api\V1\Front\Item\EcDataRequest;
use App\Http\Requests\Api\V1\Front\Item\FoundBackOrderedRequest;
use App\Http\Requests\Api\V1\Front\Item\UpdateStockRequest;
use App\Http\Response;
use App\Repositories\ItemDetailIdentificationRepository;
use App\Repositories\ItemRecommendRepository;
use App\Repositories\ItemRepository;
use App\Repositories\ItemsUsedSameStylingRepository;
use App\Services\Front\ItemServiceInterface;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpKernel\Exception\HttpException;

class ItemController extends Controller
{
    /**
     * @var ItemServiceInterface
     */
    private $itemService;

    /**
     * @var ItemRepository
     */
    private $itemRepository;

    /**
     * @var ItemRecommendRepository
     */
    private $itemRecommendRepository;

    /**
     * @var ItemsUsedSameStylingRepository
     */
    private $itemsUsedSameStylingRepository;

    /**
     * @var ItemDetailIdentificationRepository
     */
    private $itemDetailIdentificationRepo;

    /**
     * @param ItemServiceInterface $itemService
     */

    /**
     * @var StockService
     */
    private $stockService;

    public function __construct(
        ItemServiceInterface $itemService,
        ItemRepository $itemRepository,
        ItemRecommendRepository $itemRecommendRepository,
        ItemsUsedSameStylingRepository $itemsUsedSameStylingRepository,
        ItemDetailIdentificationRepository $itemDetailIdentificationRepo,
        StockService $stockService
    ) {
        $this->itemService = $itemService;
        $this->itemRepository = $itemRepository;
        $this->itemRecommendRepository = $itemRecommendRepository;
        $this->itemsUsedSameStylingRepository = $itemsUsedSameStylingRepository;
        $this->itemDetailIdentificationRepo = $itemDetailIdentificationRepo;
        $this->stockService = $stockService;
    }

    /**
     * 予約商品在庫入荷処理
     *
     * @param  $request
     *
     * @return mixed
     */
    public function arriveReserved(ArriveReservedRequest $request)
    {
        $params = $request->validated();

        return $this->itemService->storeArriveItems($params, OrderType::Reserve);
    }

    /**
     * 取り寄せ商品在庫入荷処理
     *
     * @param arriveBackOrderedRequest $request
     *
     * @return mixed
     */
    public function arriveBackOrdered(arriveBackOrderedRequest $request)
    {
        $params = $request->validated();

        return $this->itemService->storeArriveItems($params, OrderType::BackOrder);
    }

    /**
     * 商品情報取得
     *
     * @return mixed
     */
    public function ecData(EcDataRequest $request)
    {
        $params = $request->validated();

        return $this->itemService->ecData($params);
    }

    /**
     * 取り寄せ商品が見つからない通知
     *
     * @param FoundBackOrderedRequest $request
     *
     * @return mixed
     */
    public function foundBackOrdered(FoundBackOrderedRequest $request)
    {
        $params = $request->validated();

        return $this->itemService->findOrderedItems($params);
    }

    /**
     * 在庫更新
     *
     * @param FoundBackOrderedRequest $request
     *
     * @return mixed
     */
    public function updateStocks(UpdateStockRequest $request)
    {
        try {
            $params = $request->validated();

            $itemDetailIdentification = $this->itemDetailIdentificationRepo->findByField('jan_code', $params['item_jan_id'])->first();

            if (empty($itemDetailIdentification)) {
                throw new HttpException(Response::HTTP_NOT_FOUND);
            }

            DB::beginTransaction();

            if ($params['ec_stock'] < 0) {
                try {
                    $this->stockService->secureEcStock($itemDetailIdentification->id, -$params['ec_stock']);
                } catch (StockShortageException $e) {
                    throw new InvalidInputException(['ec_stock' => __('error.not_exists_enouch_stock')]);
                }
            } else {
                $this->stockService->addEcStock($itemDetailIdentification->id, $params['ec_stock']);
            }

            if ($params['reservable_stock'] < 0) {
                try {
                    $this->stockService->secureReservableStock($itemDetailIdentification->id, -$params['reservable_stock']);
                } catch (StockShortageException $e) {
                    throw new InvalidInputException(['reservable_stock' => __('error.not_exists_enouch_stock')]);
                }
            } else {
                $this->stockService->addReservableStock($itemDetailIdentification->id, $params['reservable_stock']);
            }

            DB::commit();

            return response(null, Response::HTTP_OK);
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}

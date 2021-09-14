<?php

namespace App\Http\Controllers\Api\V1\Front;

use App\Enums\Order\OrderType;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Front\Item\arriveBackOrderedRequest;
use App\Http\Requests\Api\V1\Front\Item\ArriveReservedRequest;
use App\Http\Requests\Api\V1\Front\Item\IndexRequest;
use App\Http\Requests\Api\V1\Front\Item\VerifyClosedMarketRequest;
use App\Http\Resources\Front\Item as ItemResource;
use App\Repositories\ItemRecommendRepository;
use App\Repositories\ItemRepository;
use App\Repositories\ItemsUsedSameStylingRepository;
use App\Services\Front\ItemServiceInterface;
use App\Services\Front\TopContentServiceInterface;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
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
     * @var TopContentServiceInterface
     */
    private $topContentService;

    /**
     * @param ItemServiceInterface $itemService
     */
    public function __construct(
        ItemServiceInterface $itemService,
        ItemRepository $itemRepository,
        ItemRecommendRepository $itemRecommendRepository,
        ItemsUsedSameStylingRepository $itemsUsedSameStylingRepository,
        TopContentServiceInterface $topContentService
    ) {
        $this->itemService = $itemService;
        $this->itemRepository = $itemRepository;
        $this->itemRecommendRepository = $itemRecommendRepository;
        $this->itemsUsedSameStylingRepository = $itemsUsedSameStylingRepository;
        $this->topContentService = $topContentService;
    }

    /**
     * 商品一覧
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index(IndexRequest $request)
    {
        $params = $request->validated();

        $items = $this->itemService->search($params);

        return ItemResource::collection($items);
    }

    /**
     * 商品詳細
     *
     * @param Request $request
     * @param string $productNumber
     *
     * @return ItemResource
     */
    public function show(Request $request, string $productNumber)
    {
        $item = $this->itemService->fetchDetail($productNumber);

        return new ItemResource($item);
    }

    /**
     * 闇市商品詳細
     *
     * @param Request $request
     * @param string $productNumber
     * @param int $closedMarketId
     *
     * @return ItemResource
     */
    public function showClosedMarket(Request $request, string $productNumber, int $closedMarketId)
    {
        $item = $this->itemService->fetchDetail($productNumber, $closedMarketId);

        return new ItemResource($item);
    }

    /**
     * 闇市パスワード認証
     *
     * @param VerifyClosedMarketRequest $request
     * @param int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function verifyClosedMarket(VerifyClosedMarketRequest $request, int $id)
    {
        $this->itemService->verifyEnteringClosedMarket($id, $request->validated());

        return response(null, Response::HTTP_OK);
    }

    /**
     * @param string $productNumber
     *
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function recommends(string $productNumber)
    {
        $item = $this->itemRepository->findWhere(['product_number' => $productNumber])->first();

        if (empty($item)) {
            throw new HttpException(Response::HTTP_NOT_FOUND, error_format('error.model_not_found'));
        }

        if ($item->is_manually_setting_recommendation) {
            $items = $this->itemRepository->with([
                'itemImages',
                'salesTypes',
                'brand',
            ])
            ->scopeQuery(function ($query) use ($item) {
                return $query->public()
                    ->whereIn('id', function ($subquery) use ($item) {
                        $subquery->select('recommend_item_id')
                            ->from(with(new \App\Models\ItemRecommend())->getTable())
                            ->where('item_id', $item->id);
                    })
                    ->orderby('sales_period_from', 'desc');
            })
            ->all()->take(10);
        } else {
            $storeBrand = $item->main_store_brand;
            $items = $this->topContentService->fetchNewItemsByStoreBrand($storeBrand);
            if (count($items) === 0) {
                return [];
            }
        }
        $this->itemService->fillAdditionalItemRecommendationsAttributes($items);

        return ItemResource::collection($items);
    }

    /**
     * @param string $productNumber
     *
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function usedSameStylings(string $productNumber)
    {
        $item = $this->itemRepository->findWhere(['product_number' => $productNumber])->first();

        if (empty($item)) {
            throw new HttpException(Response::HTTP_NOT_FOUND, error_format('error.model_not_found'));
        }

        $items = $this->itemsUsedSameStylingRepository->with([
            'item.itemImages',
            'item.salesTypes',
            'item.brand',
        ])
        ->whereHas('item', function ($query) {
            return $query->public();
        })
        ->findWhere(['item_id' => $item->id])->take(10)->pluck('item');

        $this->itemService->fillAdditionalItemRecommendationsAttributes($items);

        return ItemResource::collection($items);
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
}

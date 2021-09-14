<?php

namespace App\Http\Controllers\Api\V1\Front;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Front\Store\ItemStoresRequest;
use App\Http\Resources\Store as StoreResource;
use App\Repositories\ItemDetailRepository;
use App\Repositories\StoreRepository;
use Illuminate\Support\Facades\DB;

class StoreController extends Controller
{
    /**
     * @var StoreRepository
     */
    private $storeRepository;

    /**
     * @var ItemDetailRepository
     */
    private $itemDetailRepository;

    /**
     * @param StoreRepository $storeRepository
     */
    public function __construct(StoreRepository $storeRepository, ItemDetailRepository $itemDetailRepository)
    {
        $this->storeRepository = $storeRepository;
        $this->itemDetailRepository = $itemDetailRepository;
    }

    /**
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index()
    {
        $stores = $this->storeRepository->all();

        return StoreResource::collection($stores);
    }

    /**
     * @param ItemStoresRequest $request
     *
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function itemStores(ItemStoresRequest $request, int $itemId)
    {
        $params = $request->validated();

        $params['item_id'] = $itemId;

        $this->storeRepository->pushCriteria(new \App\Criteria\Store\FrontSearchCriteria($params));
        $this->storeRepository->pushCriteria(new \App\Criteria\Store\FrontSortCriteria($params));

        $stores = $this->storeRepository->scopeQuery(function ($query) {
            $expression = DB::raw('ASTEXT(stores.location) AS location');

            if (empty($query->getQuery()->columns)) {
                return $query->select(['*', $expression]);
            }

            return $query->addSelect($expression);
        })->paginate(config('constants.per_page.stores'));

        $itemDetails = $this->itemDetailRepository->findWhere(['item_id' => $itemId]);

        $stores->load([
            'itemDetailStores' => function ($query) use ($itemDetails) {
                return $query->whereIn('item_detail_stores.item_detail_id', $itemDetails->pluck('id')->toArray());
            },
        ]);

        return StoreResource::collection($stores);
    }
}

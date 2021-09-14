<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Criteria\ItemSort\AdminIndexCriteria;
use App\Http\Controllers\Api\V1\Admin\Controller as ApiAdminController;
use App\Http\Requests\Api\V1\Admin\ItemSort\IndexRequest;
use App\Http\Requests\Api\V1\Admin\ItemSort\StoreRequest;
use App\Http\Requests\Api\V1\Admin\ItemSort\UpdateRequest;
use App\Http\Resources\ItemSort as ItemSortResource;
use App\Repositories\ItemSortRepository;
use Illuminate\Http\Response;

class ItemSortController extends ApiAdminController
{
    /**
     * @var ItemSortRepository
     */
    private $itemSortRepository;

    /**
     * @param ItemSortRepository $itemSortRepository
     */
    public function __construct(ItemSortRepository $itemSortRepository)
    {
        $this->itemSortRepository = $itemSortRepository;
    }

    /**
     * @param IndexRequest $request
     *
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index(IndexRequest $request)
    {
        $this->itemSortRepository->pushCriteria(new AdminIndexCriteria($request->validated()));

        $itemSorts = $this->itemSortRepository->with([
            'item.itemImages',
            'item.itemDetails',
        ])->all();

        return ItemSortResource::collection($itemSorts);
    }

    /**
     * @param UpdateRequest $request
     * @param int $id
     *
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function update(UpdateRequest $request, int $id)
    {
        $itemSorts = $this->itemSortRepository->updateWithAdjustmentSort($request->validated(), $id);

        $itemSorts->load([
            'item.itemImages',
            'item.itemDetails',
        ]);

        return ItemSortResource::collection($itemSorts);
    }

    /**
     * @param StoreRequest $request
     *
     * @return \App\Http\Resources\ItemSort
     */
    public function store(StoreRequest $request)
    {
        $itemSorts = $this->itemSortRepository->createBatchAndAssignSort($request->validated());

        $itemSorts->load([
            'item.itemImages',
            'item.itemDetails',
        ]);

        return ItemSortResource::collection($itemSorts);
    }

    /**
     * @param int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(int $id)
    {
        $this->itemSortRepository->deleteWithAdjustmentSort($id);

        return response(null, Response::HTTP_NO_CONTENT);
    }
}

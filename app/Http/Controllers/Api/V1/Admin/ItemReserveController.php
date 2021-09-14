<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Api\V1\Admin\Controller as ApiAdminController;
use App\Http\Requests\Api\V1\Admin\ItemReserve\CreateRequest;
use App\Http\Requests\Api\V1\Admin\ItemReserve\ShowRequest;
use App\Http\Requests\Api\V1\Admin\ItemReserve\UpdateRequest;
use App\Http\Resources\ItemReserve as ItemReserveResource;
use App\Repositories\ItemReserveRepository;
use App\Services\Admin\ItemReserveServiceInterface as ItemReserveService;
use Illuminate\Http\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

class ItemReserveController extends ApiAdminController
{
    /**
     * @var ItemReserveRepository
     */
    private $repository;

    /**
     * @var ItemReserveService
     */
    private $itemReserveService;

    /**
     * @param ItemReserveRepository $repository
     */
    public function __construct(ItemReserveRepository $repository, ItemReserveService $itemReserveService)
    {
        $this->repository = $repository;
        $this->itemReserveService = $itemReserveService;
    }

    /**
     * @param ShowRequest $request
     * @param int $itemId
     * @param int $id
     *
     * @return ItemReserveResource
     */
    public function show(ShowRequest $request, int $itemId)
    {
        $itemReserve = $this->repository->with([
            'item.itemDetails',
        ])->findWhere([
            'item_id' => $itemId,
        ])->first();

        if (empty($itemReserve)) {
            throw new HttpException(Response::HTTP_NOT_FOUND, error_format('error.model_not_found', compact('itemId')));
        }

        return new ItemReserveResource($itemReserve);
    }

    /**
     * @param CreateRequest $request
     * @param int $itemId
     *
     * @return ItemReserveResource
     */
    public function store(CreateRequest $request, int $itemId)
    {
        $itemReserve = $this->itemReserveService->create($request->validated(), $itemId);

        $itemReserve->load('item.itemDetails');

        return new ItemReserveResource($itemReserve);
    }

    /**
     * @param UpdateRequest $request
     * @param int $itemId
     * @param int $id
     *
     * @return ItemReserveResource
     */
    public function update(UpdateRequest $request, int $itemId)
    {
        $itemReserve = $this->itemReserveService->update($request->validated(), $itemId);

        $itemReserve->load('item.itemDetails');

        return new ItemReserveResource($itemReserve);
    }
}

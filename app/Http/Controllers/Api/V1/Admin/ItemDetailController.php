<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Api\V1\Admin\Controller as ApiAdminController;
use App\Http\Requests\Api\V1\Admin\ItemDetail\IndexIdentificationsRequest;
use App\Http\Requests\Api\V1\Admin\ItemDetail\IndexRequest;
use App\Http\Resources\ItemDetail as ItemDetailResource;
use App\Http\Resources\ItemDetailIdentification as ItemDetailIdentificationResource;
use App\Repositories\ItemDetailIdentificationRepository;
use App\Repositories\ItemDetailRepository;
use App\Services\Admin\ItemDetailServiceInterface as ItemDetailService;
use Illuminate\Http\Response;

class ItemDetailController extends ApiAdminController
{
    /**
     * @var ItemDetailRepository
     */
    private $repository;

    /**
     * @var ItemDetailIdentificationRepository
     */
    private $itemDetailIdentRepository;

    /**
     * @var ItemDetailService
     */
    private $service;

    public function __construct(
        ItemDetailRepository $repository,
        ItemDetailIdentificationRepository $itemDetailIdentRepository,
        ItemDetailService $service
    ) {
        $this->repository = $repository;
        $this->itemDetailIdentRepository = $itemDetailIdentRepository;
        $this->service = $service;
    }

    /**
     * @param IndexRequest $request
     *
     * @return \Illuminate\Http\Resources\Json\ResourceCollection
     */
    public function index(IndexRequest $request)
    {
        $params = $request->validated();

        $this->repository->pushCriteria(new \App\Criteria\ItemDetail\AdminSearchCriteria($params));
        $this->repository->pushCriteria(new \App\Criteria\ItemDetail\AdminSortCriteria($params));

        $itemDetails = $this->repository->with([
            'item.itemImages',
            'redisplayRequests',
        ])->paginateWithDistinct(
            'item_details.id',
            config('repository.pagination.admin_limit')
        );

        return ItemDetailResource::collection($itemDetails);
    }

    /**
     * @param IndexIdentificationsRequest $request
     *
     * @return \Illuminate\Http\Resources\Json\ResourceCollection
     */
    public function indexIdentifications(IndexIdentificationsRequest $request)
    {
        $params = $request->validated();

        $this->itemDetailIdentRepository->pushCriteria(new \App\Criteria\ItemDetailIdentification\AdminSearchCriteria($params));
        $this->itemDetailIdentRepository->pushCriteria(new \App\Criteria\ItemDetailIdentification\AdminSortCriteria($params));

        $identifications = $this->itemDetailIdentRepository->with([
            'itemDetail.item.itemImages',
            'itemDetail.item.season',
            'itemDetail.redisplayRequests',
        ])->paginateWithDistinct(
            'item_detail_identifications.id',
            config('repository.pagination.admin_limit')
        );

        return ItemDetailIdentificationResource::collection($identifications);
    }

    /**
     * @param int $itemId
     *
     * @return \Illuminate\Http\Resources\Json\ResourceCollection
     */
    public function indexByItemId(int $itemId)
    {
        $itemDetails = $this->repository->with(['color', 'size'])->findWhere(['item_id' => $itemId]);

        return ItemDetailResource::collection($itemDetails);
    }

    /**
     * @param IndexRequest $request
     *
     * @return \Symfony\Component\HttpFoundation\StreamedResponse
     */
    public function exportCsv(IndexRequest $request)
    {
        $fileName = __('file.csv.admin.item_detail', ['datetime' => date('YmdHis')]);

        $headers = \App\Utils\FileDownloadUtil::getExportFileHeaders(
            $fileName,
            \App\Utils\FileUtil::MIME_TYPE_CSV
        );

        return response()->stream(
            $this->service->exportStockCsv($request->validated()),
            Response::HTTP_OK,
            $headers
        );
    }
}

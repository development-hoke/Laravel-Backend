<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Api\V1\Admin\Controller as ApiAdminController;
use App\Http\Requests\Api\V1\Admin\Item\IndexRequest;
use App\Http\Requests\Api\V1\Admin\Item\ShowRequest;
use App\Http\Requests\Api\V1\Admin\Item\StorePreviewRequest;
use App\Http\Requests\Api\V1\Admin\Item\UpdateRequest;
use App\Http\Requests\Api\V1\Admin\Item\UpdateStatusRequest;
use App\Http\Requests\Api\V1\Admin\Item\UploadImageRequest;
use App\Http\Resources\Item as ItemResource;
use App\Repositories\ItemRepository;
use App\Services\Admin\ItemPreviewServiceInterface as ItemPreviewService;
use App\Services\Admin\ItemServiceInterface as ItemService;
use Illuminate\Http\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

class ItemController extends ApiAdminController
{
    /**
     * @var ItemRepository
     */
    private $itemRepository;

    /**
     * @var ItemService
     */
    private $itemService;

    /**
     * @var ItemPreviewService
     */
    private $itemPreviewService;

    /**
     * @param ItemRepository $itemRepository
     * @param ItemService $itemService
     * @param ItemPreviewService $itemPreviewService
     */
    public function __construct(
        ItemRepository $itemRepository,
        ItemService $itemService,
        ItemPreviewService $itemPreviewService
    ) {
        $this->itemRepository = $itemRepository;
        $this->itemService = $itemService;
        $this->itemPreviewService = $itemPreviewService;
    }

    /**
     * Display a listing of the resource.
     *
     * @param IndexRequest
     *
     * @return ResourceCollection
     */
    public function index(IndexRequest $request)
    {
        $items = $this->itemService->search($request->validated());

        return ItemResource::collection($items);
    }

    /**
     * @param UpdateStatusRequest $request
     * @param int $id
     *
     * @return ItemResource
     */
    public function updateStatus(UpdateStatusRequest $request, int $id)
    {
        $item = $this->itemRepository->update($request->validated(), $id);

        $item->load(['itemDetails', 'itemImages']);

        return new ItemResource($item);
    }

    /**
     * @param UploadImageRequest $request
     * @param int $id
     *
     * @return ItemResource
     */
    public function uploadImage(UploadImageRequest $request, int $id)
    {
        $item = $this->itemService->uploadImage($request->file('image'), $id);

        $item->load(['itemDetails', 'itemImages']);

        return new ItemResource($item);
    }

    /**
     * Display the specified resource.
     *
     * @param ShowRequest $request
     * @param int $itemId
     *
     * @return ItemResource
     */
    public function show(ShowRequest $request, int $itemId)
    {
        $item = $this->itemRepository->find($itemId);

        $item = $this->loadItemDetailRelations($item);

        if (empty($item)) {
            throw new HttpException(Response::HTTP_NOT_FOUND, error_format('error.model_not_found', ['id' => $itemId]));
        }

        return new ItemResource($item);
    }

    /**
     * Update
     *
     * @param UpdateRequest $request
     * @param int $id
     *
     * @return ItemResource
     */
    public function update(UpdateRequest $request, int $id)
    {
        $params = $request->validated();

        $item = $this->itemService->update($params, $id);

        $item = $this->loadItemDetailRelations($item);

        $item = new ItemResource($item);

        return $item;
    }

    /**
     * @param \App\Models\Item $item
     *
     * @return \App\Models\Item
     */
    private function loadItemDetailRelations(\App\Models\Item $item)
    {
        $item->load([
            'itemDetails.size',
            'itemDetails.color',
            'salesTypes',
            'onlineTags',
            'onlineCategories',
            'itemsUsedSameStylings.itemImages',
            'itemSubBrands',
            'brand',
            'recommendItems.itemImages',
            'itemImages',
            'backendItemImages',
            'itemReserve',
        ]);

        return $item;
    }

    /**
     * @param IndexRequest $request
     *
     * @return \Symfony\Component\HttpFoundation\StreamedResponse
     */
    public function exportCsv(IndexRequest $request)
    {
        $fileName = __('file.csv.admin.item', ['datetime' => date('YmdHis')]);

        $headers = \App\Utils\FileDownloadUtil::getExportFileHeaders(
            $fileName,
            \App\Utils\FileUtil::MIME_TYPE_CSV
        );

        return response()->stream(
            $this->itemService->getCsvExporter($request->validated()),
            Response::HTTP_OK,
            $headers
        );
    }

    /**
     * @param IndexRequest $request
     *
     * @return \Symfony\Component\HttpFoundation\StreamedResponse
     */
    public function exportInfoCsv(IndexRequest $request)
    {
        $fileName = __('file.csv.admin.item_info', ['datetime' => date('YmdHis')]);

        $headers = \App\Utils\FileDownloadUtil::getExportFileHeaders(
            $fileName,
            \App\Utils\FileUtil::MIME_TYPE_CSV
        );

        return response()->stream(
            $this->itemService->getInfoCsvExporter($request->validated()),
            Response::HTTP_OK,
            $headers
        );
    }

    /**
     * @param IndexRequest $request
     *
     * @return \Symfony\Component\HttpFoundation\StreamedResponse
     */
    public function exportImageCsv(IndexRequest $request)
    {
        $fileName = __('file.csv.admin.item_image', ['datetime' => date('YmdHis')]);

        $headers = \App\Utils\FileDownloadUtil::getExportFileHeaders(
            $fileName,
            \App\Utils\FileUtil::MIME_TYPE_CSV
        );

        return response()->stream(
            $this->itemService->getImageCsvExporter($request->validated()),
            Response::HTTP_OK,
            $headers
        );
    }

    /**
     * @param StorePreviewRequest $request
     * @param int $id
     *
     * @return \Illuminate\Http\Resources\Json\JsonResource
     */
    public function storePreview(StorePreviewRequest $request, int $id)
    {
        $cachedInfo = $this->itemPreviewService->store($id, $request->validated());

        return new \Illuminate\Http\Resources\Json\JsonResource($cachedInfo);
    }

    /**
     * @param string $key
     *
     * @return \Illuminate\Http\Resources\Json\JsonResource
     */
    public function showPreview(string $key)
    {
        $preview = $this->itemPreviewService->fetch($key);

        return new \Illuminate\Http\Resources\Json\JsonResource($preview);
    }
}

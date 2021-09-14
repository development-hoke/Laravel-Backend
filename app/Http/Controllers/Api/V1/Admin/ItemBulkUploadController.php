<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Admin\ItemBulkUpload\StoreItemCsvRequest;
use App\Http\Requests\Api\V1\Admin\ItemBulkUpload\StoreItemImagesRequest;
use App\Http\Resources\ItemBulkUpload as ItemBulkUploadResource;
use App\Repositories\ItemBulkUploadRepository;
use App\Services\Admin\ItemBulkUploadServiceInterface;
use Illuminate\Http\Response;

class ItemBulkUploadController extends Controller
{
    /**
     * @var ItemBulkUploadServiceInterface
     */
    private $service;

    /**
     * @var ItemBulkUploadRepository
     */
    private $itemBulkUploadRepository;

    public function __construct(
        ItemBulkUploadServiceInterface $service,
        ItemBulkUploadRepository $itemBulkUploadRepository
    ) {
        $this->service = $service;
        $this->itemBulkUploadRepository = $itemBulkUploadRepository;
    }

    /**
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index()
    {
        $itemBulkUploads = $this->itemBulkUploadRepository->scopeQuery(function ($query) {
            return $query->orderBy('id', 'desc');
        })->all();

        return ItemBulkUploadResource::collection($itemBulkUploads);
    }

    /**
     * @param StoreItemCsvRequest $request
     *
     * @return ItemBulkUploadResource
     */
    public function storeItemCsv(StoreItemCsvRequest $request)
    {
        $itemBulkUpload = $this->service->storeItemCsv($request->validated());

        return new ItemBulkUploadResource($itemBulkUpload);
    }

    /**
     * @param StoreItemImagesRequest $request
     *
     * @return ItemBulkUploadResource
     */
    public function storeItemImages(StoreItemImagesRequest $request)
    {
        $itemBulkUpload = $this->service->importItemImages(
            $request->file('content'),
            $request->validated()
        );

        return new ItemBulkUploadResource($itemBulkUpload);
    }

    /**
     * @param int $id
     *
     * @return \Symfony\Component\HttpFoundation\StreamedResponse
     */
    public function exportErrorCsv(int $id)
    {
        list($exporter, $itemBulkUpload) = $this->service->getErrorCsvExporter($id);

        $fileName = __('file.csv.admin.item_bulk_upload_errors', ['file_name' => $itemBulkUpload->file_name]);

        $headers = \App\Utils\FileDownloadUtil::getExportFileHeaders(
            $fileName,
            \App\Utils\FileUtil::MIME_TYPE_CSV
        );

        return response()->stream(
            $exporter,
            Response::HTTP_OK,
            $headers
        );
    }

    /**
     * @return \Symfony\Component\HttpFoundation\StreamedResponse
     */
    public function exportItemCsvFormat()
    {
        $exporter = $this->service->getItemCsvFormatExporter();

        $fileName = __('file.csv.admin.item_bulk_upload_csv_format_item');

        $headers = \App\Utils\FileDownloadUtil::getExportFileHeaders(
            $fileName,
            \App\Utils\FileUtil::MIME_TYPE_CSV
        );

        return response()->stream(
            $exporter,
            Response::HTTP_OK,
            $headers
        );
    }

    /**
     * @return \Symfony\Component\HttpFoundation\StreamedResponse
     */
    public function exportItemImageCsvFormat()
    {
        $exporter = $this->service->getItemImageCsvFormatExporter();

        $fileName = __('file.csv.admin.item_bulk_upload_csv_format_item_image');

        $headers = \App\Utils\FileDownloadUtil::getExportFileHeaders(
            $fileName,
            \App\Utils\FileUtil::MIME_TYPE_CSV
        );

        return response()->stream(
            $exporter,
            Response::HTTP_OK,
            $headers
        );
    }
}

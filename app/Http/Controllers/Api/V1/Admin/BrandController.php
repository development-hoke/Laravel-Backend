<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Api\V1\Admin\Controller as ApiAdminController;
use App\Http\Requests\Api\V1\Admin\Brand\CreateRequest;
use App\Http\Requests\Api\V1\Admin\Brand\DeleteRequest;
use App\Http\Requests\Api\V1\Admin\Brand\IndexRequest;
use App\Http\Requests\Api\V1\Admin\Brand\ShowRequest;
use App\Http\Requests\Api\V1\Admin\Brand\UpdateRequest;
use App\Http\Requests\Api\V1\Admin\Brand\UpdateSortRequest;
use App\Http\Resources\Brand as BrandResource;
use App\Repositories\BrandRepository;
use App\Services\Admin\BrandServiceInterface;
use App\Utils\Csv\ExportCsvInterface as ExportCsvUtil;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

class BrandController extends ApiAdminController
{
    /**
     * @var BrandRepository
     */
    private $repository;

    /**
     * @var ExportCsvUtil
     */
    private $exportCsvUtil;

    /**
     * @var BrandServiceInterface
     */
    private $brandService;

    /**
     * @param BrandRepository $repository
     * @param ExportCsvUtil $exportCsvUtil
     */
    public function __construct(
        BrandRepository $repository,
        ExportCsvUtil $exportCsvUtil,
        BrandServiceInterface $brandService
    ) {
        $this->repository = $repository;
        $this->brandService = $brandService;
        $this->exportCsvUtil = $exportCsvUtil;
    }

    /**
     * @param IndexRequest $request
     *
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index(IndexRequest $request)
    {
        $brands = $this->repository->paginate(config('repository.pagination.admin_limit'));

        return BrandResource::collection($brands);
    }

    /**
     * @param CreateRequest $request
     *
     * @return BrandResource
     */
    public function store(CreateRequest $request): BrandResource
    {
        $brand = $this->repository->create($request->except('id'));

        return new BrandResource($brand);
    }

    /**
     * @param ShowRequest $request
     * @param int $id
     *
     * @return BrandResource
     */
    public function show(ShowRequest $request, int $id): BrandResource
    {
        try {
            $brand = $this->repository->find($id);

            return new BrandResource($brand);
        } catch (ModelNotFoundException $e) {
            throw new HttpException(Response::HTTP_NOT_FOUND, error_format('error.model_not_found', compact('id')), $e);
        }
    }

    /**
     * @param UpdateRequest $request
     * @param int $id
     *
     * @return BrandResource
     */
    public function update(UpdateRequest $request, int $id): BrandResource
    {
        try {
            $brand = $this->repository->update($request->except('id'), $id);

            return new BrandResource($brand);
        } catch (ModelNotFoundException $e) {
            throw new HttpException(Response::HTTP_NOT_FOUND, error_format('error.model_not_found', compact('id')), $e);
        }
    }

    /**
     * @param DeleteRequest $request
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(DeleteRequest $request, int $id)
    {
        $this->repository->delete($id);

        return response(null, Response::HTTP_NO_CONTENT);
    }

    /**
     * @param UpdateSortRequest $request
     *
     * @return \Illuminate\Http\Response
     */
    public function updateSort(UpdateSortRequest $request, int $id)
    {
        try {
            $params = $request->validated();
            $brand = $this->brandService->updateSort($id, $params);

            return BrandResource::collection($brand);
        } catch (ModelNotFoundException $e) {
            throw new HttpException(Response::HTTP_NOT_FOUND, error_format('error.model_not_found', compact('id')), $e);
        }
    }

    /**
     * @return \Symfony\Component\HttpFoundation\StreamedResponse
     */
    public function exportCsv()
    {
        $this->exportCsvUtil->setHeaders([
            'id' => __('validation.attributes.brand.id'),
            'store_brand' => __('validation.attributes.brand.store_brand'),
            'section' => __('validation.attributes.brand.section'),
            'name' => __('validation.attributes.brand.name'),
            'kana' => __('validation.attributes.brand.kana'),
            'category' => __('validation.attributes.brand.category'),
        ]);

        $exporter = $this->exportCsvUtil->getExporter(function ($exporter) {
            $this->repository->chunk(config('repository.chunk.default'), function ($chunk) use ($exporter) {
                foreach ($chunk as $row) {
                    $exporter([
                        'id' => $row->id,
                        'store_brand' => \App\Enums\Common\StoreBrand::getDescription($row->store_brand),
                        'section' => \App\Enums\Brand\Section::getDescription($row->section),
                        'name' => $row->name,
                        'kana' => $row->kana,
                        'category' => \App\Enums\Brand\Category::getDescription($row->category),
                    ]);
                }
            });
        });

        $headers = \App\Utils\FileDownloadUtil::getExportFileHeaders(
            __('file.csv.admin.brand', ['datetime' => date('YmdHis')]),
            \App\Utils\FileUtil::MIME_TYPE_CSV
        );

        return response()->stream($exporter, Response::HTTP_OK, $headers);
    }
}

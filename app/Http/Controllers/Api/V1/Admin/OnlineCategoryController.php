<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Api\V1\Admin\Controller as ApiAdminController;
use App\Http\Requests\Api\V1\Admin\OnlineCategory\DeleteRequest;
use App\Http\Requests\Api\V1\Admin\OnlineCategory\IndexRequest;
use App\Http\Requests\Api\V1\Admin\OnlineCategory\StoreRequest;
use App\Http\Requests\Api\V1\Admin\OnlineCategory\UpdateRequest;
use App\Http\Resources\OnlineCategory as OnlineCategoryResource;
use App\Repositories\OnlineCategoryRepository;
use App\Utils\Csv\ExportCsvInterface as ExportCsvUtil;
use Illuminate\Http\Response;

class OnlineCategoryController extends ApiAdminController
{
    /**
     * @var OnlineCategoryRepository
     */
    private $repository;

    /**
     * @var ExportCsvUtil
     */
    private $exportCsvUtil;

    /**
     * @param OnlineCategoryRepository $repository
     * @param ExportCsvUtil $exportCsvUtil
     */
    public function __construct(OnlineCategoryRepository $repository, ExportCsvUtil $exportCsvUtil)
    {
        $this->repository = $repository;
        $this->exportCsvUtil = $exportCsvUtil;
    }

    /**
     * @param IndexRequest $request
     *
     * @return \Illuminate\Http\Resources\Json\ResourceCollection
     */
    public function index(IndexRequest $request)
    {
        $onlineCategories = $this->repository->all();

        return OnlineCategoryResource::collection($onlineCategories);
    }

    /**
     * @param StoreRequest $request
     *
     * @return \Illuminate\Http\Resources\Json\ResourceCollection
     */
    public function store(StoreRequest $request)
    {
        $onlineCategories = $this->repository->create($request->validated());

        return OnlineCategoryResource::collection($onlineCategories);
    }

    /**
     * @param UpdateRequest $request
     * @param int $id
     *
     * @return \Illuminate\Http\Resources\Json\ResourceCollection
     */
    public function update(UpdateRequest $request, int $id)
    {
        $onlineCategories = $this->repository->update($request->validated(), $id);

        return OnlineCategoryResource::collection($onlineCategories);
    }

    /**
     * @param DeleteRequest $request
     * @param int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(DeleteRequest $request, int $id)
    {
        $this->repository->delete($id);

        return response(null, Response::HTTP_NO_CONTENT);
    }

    /**
     * @return \Symfony\Component\HttpFoundation\StreamedResponse
     */
    public function exportCsv()
    {
        $this->exportCsvUtil->setHeaders([
            'id' => __('validation.attributes.online_category.id'),
            'name' => __('validation.attributes.online_category.name'),
            'level' => __('validation.attributes.online_category.level'),
            'parent' => __('validation.attributes.online_category.parent'),
        ]);

        $this->repository->with(['parent']);

        $exporter = $this->exportCsvUtil->getExporter(function ($exporter) {
            $this->repository->chunk(config('repository.chunk.default'), function ($chunk) use ($exporter) {
                foreach ($chunk as $row) {
                    $exporter([
                        'id' => $row->id,
                        'name' => $row->name,
                        'level' => $row->level,
                        'parent' => !empty($row->parent) ? $row->parent->name : null,
                    ]);
                }
            });
        });

        $headers = \App\Utils\FileDownloadUtil::getExportFileHeaders(
            __('file.csv.admin.online_category', ['datetime' => date('YmdHis')]),
            \App\Utils\FileUtil::MIME_TYPE_CSV
        );

        return response()->stream($exporter, Response::HTTP_OK, $headers);
    }
}

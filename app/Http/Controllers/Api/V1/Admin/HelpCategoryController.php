<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Api\V1\Admin\Controller as ApiAdminController;
use App\Http\Requests\Api\V1\Admin\HelpCategory\DeleteRequest;
use App\Http\Requests\Api\V1\Admin\HelpCategory\IndexRequest;
use App\Http\Requests\Api\V1\Admin\HelpCategory\StoreRequest;
use App\Http\Requests\Api\V1\Admin\HelpCategory\UpdateRequest;
use App\Http\Resources\HelpCategory as HelpCategoryResource;
use App\Repositories\HelpCategoryRepository;
use App\Utils\Csv\ExportCsvInterface as ExportCsvUtil;
use Illuminate\Http\Response;

class HelpCategoryController extends ApiAdminController
{
    /**
     * @var HelpCategoryRepository
     */
    private $repository;

    /**
     * @var ExportCsvUtil
     */
    private $exportCsvUtil;

    /**
     * @param HelpCategoryRepository $repository
     * @param ExportCsvUtil $exportCsvUtil
     */
    public function __construct(HelpCategoryRepository $repository, ExportCsvUtil $exportCsvUtil)
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
        $helpCategories = $this->repository->all();

        return HelpCategoryResource::collection($helpCategories);
    }

    /**
     * @param StoreRequest $request
     *
     * @return \Illuminate\Http\Resources\Json\ResourceCollection
     */
    public function store(StoreRequest $request)
    {
        $helpCategories = $this->repository->create($request->validated());

        return HelpCategoryResource::collection($helpCategories);
    }

    /**
     * @param UpdateRequest $request
     * @param int $id
     *
     * @return \Illuminate\Http\Resources\Json\ResourceCollection
     */
    public function update(UpdateRequest $request, int $id)
    {
        $helpCategories = $this->repository->update($request->validated(), $id);

        return HelpCategoryResource::collection($helpCategories);
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
            'id' => __('validation.attributes.help_category.id'),
            'name' => __('validation.attributes.help_category.name'),
            'level' => __('validation.attributes.help_category.level'),
            'parent' => __('validation.attributes.help_category.parent'),
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
            __('file.csv.admin.help_category', ['datetime' => date('YmdHis')]),
            \App\Utils\FileUtil::MIME_TYPE_CSV
        );

        return response()->stream($exporter, Response::HTTP_OK, $headers);
    }
}

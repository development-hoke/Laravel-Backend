<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Api\V1\Admin\Controller as ApiAdminController;
use App\Http\Requests\Api\V1\Admin\OnlineTag\DeleteRequest;
use App\Http\Requests\Api\V1\Admin\OnlineTag\IndexRequest;
use App\Http\Requests\Api\V1\Admin\OnlineTag\StoreRequest;
use App\Http\Requests\Api\V1\Admin\OnlineTag\UpdateRequest;
use App\Http\Resources\OnlineTag as OnlineTagResource;
use App\Repositories\OnlineTagRepository;
use App\Utils\Csv\ExportCsvInterface as ExportCsvUtil;
use Illuminate\Http\Response;

class OnlineTagController extends ApiAdminController
{
    /**
     * @var OnlineTagRepository
     */
    private $repository;

    /**
     * @var ExportCsvUtil
     */
    private $exportCsvUtil;

    /**
     * @param OnlineTagRepository $repository
     */
    public function __construct(OnlineTagRepository $repository, ExportCsvUtil $exportCsvUtil)
    {
        $this->repository = $repository;
        $this->exportCsvUtil = $exportCsvUtil;
    }

    /**
     * @param IndexRequest $request
     *
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index(IndexRequest $request)
    {
        $onlineTags = $this->repository->all();

        return OnlineTagResource::collection($onlineTags);
    }

    /**
     * @param UpdateRequest $request
     * @param int $id
     *
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function update(UpdateRequest $request, int $id)
    {
        $onlineTags = $this->repository->update($request->validated(), $id);

        return OnlineTagResource::collection($onlineTags);
    }

    /**
     * @param StoreRequest $request
     *
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function store(StoreRequest $request)
    {
        $onlineTags = $this->repository->create($request->validated());

        return OnlineTagResource::collection($onlineTags);
    }

    /**
     * @param StoreRequest $request
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
            'id' => __('validation.attributes.online_tag.id'),
            'name' => __('validation.attributes.online_tag.name'),
            'parent' => __('validation.attributes.online_tag.parent'),
        ]);

        $this->repository->with(['parent']);

        $exporter = $this->exportCsvUtil->getExporter(function ($exporter) {
            $this->repository->chunk(config('repository.chunk.default'), function ($chunk) use ($exporter) {
                foreach ($chunk as $row) {
                    $exporter([
                        'id' => $row->id,
                        'name' => $row->name,
                        'parent' => !empty($row->parent) ? $row->parent->name : null,
                    ]);
                }
            });
        });

        $headers = \App\Utils\FileDownloadUtil::getExportFileHeaders(
            __('file.csv.admin.online_tag', ['datetime' => date('YmdHis')]),
            \App\Utils\FileUtil::MIME_TYPE_CSV
        );

        return response()->stream($exporter, Response::HTTP_OK, $headers);
    }
}

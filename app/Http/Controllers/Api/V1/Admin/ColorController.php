<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Api\V1\Admin\Controller as ApiAdminController;
use App\Http\Requests\Api\V1\Admin\Color\IndexRequest;
use App\Http\Requests\Api\V1\Admin\Color\UpdateRequest;
use App\Http\Resources\Color as ColorResource;
use App\Repositories\ColorRepository;
use App\Utils\Csv\ExportCsvInterface as ExportCsvUtil;
use Illuminate\Http\Response;

class ColorController extends ApiAdminController
{
    /**
     * @var ColorRepository
     */
    private $repository;

    /**
     * @var ExportCsvUtil
     */
    private $exportCsvUtil;

    /**
     * @param ColorRepository $repository
     * @param ExportCsvUtil $exportCsvUtil
     */
    public function __construct(ColorRepository $repository, ExportCsvUtil $exportCsvUtil)
    {
        $this->repository = $repository;
        $this->exportCsvUtil = $exportCsvUtil;
    }

    public function index(IndexRequest $request)
    {
        $params = $request->validated();

        $all = isset($params['all']) && $params['all'];

        $colors = $all
            ? $this->repository->all()
            : $this->repository->paginate(config('repository.pagination.admin_limit'));

        return ColorResource::collection($colors);
    }

    public function update(UpdateRequest $request)
    {
        $color = $this->repository->update([
            'name' => $request->name,
            'color_panel' => $request->color_panel,
            'display_name' => $request->display_name,
        ], $request->id);

        return new ColorResource($color);
    }

    /**
     * @return \Symfony\Component\HttpFoundation\StreamedResponse
     */
    public function exportCsv()
    {
        $this->exportCsvUtil->setHeaders([
            'name' => __('validation.attributes.color.name'),
            'color_panel' => __('validation.attributes.color.color_panel'),
        ]);

        $exporter = $this->exportCsvUtil->getExporter(function ($exporter) {
            $this->repository->chunk(config('repository.chunk.default'), function ($chunk) use ($exporter) {
                foreach ($chunk as $row) {
                    $exporter($row);
                }
            });
        });

        $headers = \App\Utils\FileDownloadUtil::getExportFileHeaders(
            __('file.csv.admin.color', ['datetime' => date('YmdHis')]),
            \App\Utils\FileUtil::MIME_TYPE_CSV
        );

        return response()->stream($exporter, Response::HTTP_OK, $headers);
    }
}

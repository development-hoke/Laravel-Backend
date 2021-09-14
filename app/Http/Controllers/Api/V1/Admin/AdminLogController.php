<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Criteria\AdminLog\AdminSearchCriteria;
use App\Http\Controllers\Api\V1\Admin\Controller as ApiAdminController;
use App\Http\Requests\Api\V1\Admin\AdminLog\IndexRequest;
use App\Http\Resources\AdminLog;
use App\Repositories\AdminLogRepository;
use App\Utils\Csv\ExportCsvInterface as ExportCsvUtil;
use Illuminate\Http\Response;

class AdminLogController extends ApiAdminController
{
    /**
     * @var AdminLogRepository
     */
    private $adminLogRepository;

    /**
     * @var ExportCsvUtil
     */
    private $exportCsvUtil;

    /**
     * @param AdminLogRepository $adminLogRepository
     * @param ExportCsvUtil $exportCsvUtil
     */
    public function __construct(AdminLogRepository $adminLogRepository, ExportCsvUtil $exportCsvUtil)
    {
        $this->adminLogRepository = $adminLogRepository;
        $this->exportCsvUtil = $exportCsvUtil;
    }

    /**
     * @param IndexRequest
     *
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index(IndexRequest $request)
    {
        $this->adminLogRepository->pushCriteria(new AdminSearchCriteria($request->validated()));

        $adminLogs = $this->adminLogRepository->with(['staff'])
            ->scopeQuery(function ($query) {
                return $query->orderBy('id', 'desc');
            })
            ->paginate(config('repository.pagination.admin_limit'));

        return AdminLog::collection($adminLogs);
    }

    /**
     * @param IndexRequest
     *
     * @return \Symfony\Component\HttpFoundation\StreamedResponse
     */
    public function exportCsv(IndexRequest $request)
    {
        $this->exportCsvUtil->setHeaders([
            'action_text' => __('validation.attributes.admin_log.action'),
            'type' => __('validation.attributes.admin_log.type'),
            'url' => __('validation.attributes.admin_log.url'),
            'ip' => __('validation.attributes.admin_log.ip'),
            'referer' => __('validation.attributes.admin_log.referer'),
            'staff' => __('resource.staff'),
            'created_at' => __('validation.attributes.created_at'),
        ]);

        $this->adminLogRepository->pushCriteria(new AdminSearchCriteria($request->validated()));
        $this->adminLogRepository->with(['staff']);

        $exporter = $this->exportCsvUtil->getExporter(function ($exporter) {
            $this->adminLogRepository->chunk(config('repository.chunk.default'), function ($chunk) use ($exporter) {
                foreach ($chunk as $row) {
                    $exporter([
                        'action_text' => $row->action_text,
                        'type' => \App\Enums\AdminLog\Type::getDescription($row->type),
                        'url' => $row->url,
                        'ip' => $row->ip,
                        'referer' => $row->referer,
                        'staff' => !empty($row->staff) ? $row->staff->name : null,
                        'created_at' => $row->created_at,
                    ]);
                }
            });
        });

        $headers = \App\Utils\FileDownloadUtil::getExportFileHeaders(
            __('file.csv.admin.admin_log', ['datetime' => date('YmdHis')]),
            \App\Utils\FileUtil::MIME_TYPE_CSV
        );

        return response()->stream($exporter, Response::HTTP_OK, $headers);
    }
}

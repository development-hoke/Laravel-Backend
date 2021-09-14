<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Api\V1\Admin\Controller as ApiAdminController;
use App\Http\Requests\Api\V1\Admin\SalesType\CreateRequest;
use App\Http\Requests\Api\V1\Admin\SalesType\DeleteRequest;
use App\Http\Requests\Api\V1\Admin\SalesType\ReadRequest;
use App\Http\Requests\Api\V1\Admin\SalesType\UpdateRequest;
use App\Http\Resources\SalesType as SalesTypeResource;
use App\Repositories\SalesTypeRepository;
use App\Services\Admin\SalesTypeServiceInterface as SalesTypeService;
use App\Utils\Csv\ExportCsvInterface as ExportCsvUtil;
use Illuminate\Http\Response;

class SalesTypeController extends ApiAdminController
{
    /**
     * @var SalesTypeRepository
     */
    private $repository;

    /**
     * @var SalesTypeService
     */
    private $salesTypeService;

    /**
     * @var ExportCsvUtil
     */
    private $exportCsvUtil;

    /**
     * @param SalesTypeRepository $repository
     * @param SalesTypeService $salesTypeService
     * @param ExportCsvUtil $exportCsvUtil
     */
    public function __construct(SalesTypeRepository $repository, SalesTypeService $salesTypeService, ExportCsvUtil $exportCsvUtil)
    {
        $this->repository = $repository;
        $this->salesTypeService = $salesTypeService;
        $this->exportCsvUtil = $exportCsvUtil;
    }

    public function index(ReadRequest $request)
    {
        $all = $request->get('all', false);

        $types = $all
            ? $this->repository->all()
            : $this->repository->paginate(config('repository.pagination.admin_limit'));

        return SalesTypeResource::collection($types);
    }

    public function create(CreateRequest $request)
    {
        $type = $this->repository->create([
            'name' => $request->name,
            'text_color' => $request->text_color,
        ]);

        return new SalesTypeResource($type);
    }

    public function update(UpdateRequest $request)
    {
        $type = $this->repository->update([
            'name' => $request->name,
            'text_color' => $request->text_color,
        ], $request->id);

        return new SalesTypeResource($type);
    }

    public function destroy(DeleteRequest $request)
    {
        $this->salesTypeService->delete($request->id);

        return response(null, 204);
    }

    /**
     * @return \Symfony\Component\HttpFoundation\StreamedResponse
     */
    public function exportCsv()
    {
        $fileName = __('file.csv.admin.item', ['datetime' => date('YmdHis')]);

        $headers = \App\Utils\FileDownloadUtil::getExportFileHeaders(
            $fileName,
            \App\Utils\FileUtil::MIME_TYPE_CSV
        );

        return response()->stream(
            $this->salesTypeService->getCsvExporter(),
            Response::HTTP_OK,
            $headers
        );
    }
}

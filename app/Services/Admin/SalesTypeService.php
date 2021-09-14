<?php

namespace App\Services\Admin;

use App\Repositories\ItemSalesTypesRepository;
use App\Repositories\SalesTypeRepository;
use App\Utils\Csv\ExportCsvInterface as ExportCsvUtil;
use Illuminate\Support\Facades\DB;

class SalesTypeService extends Service implements SalesTypeServiceInterface
{
    /**
     * @var SalesTypeRepository
     */
    private $salesTypeRepository;

    /**
     * @var ItemSalesTypesRepository
     */
    private $itemSalesTypesRepository;

    /**
     * @var ExportCsvUtil
     */
    private $exportCsvUtil;

    /**
     * @param SalesTypeRepository $salesTypeRepository
     * @param ItemSalesTypesRepository $itemSalesTypesRepository
     * @param ExportCsvUtil $exportCsvUtil
     */
    public function __construct(
        SalesTypeRepository $salesTypeRepository,
        ItemSalesTypesRepository $itemSalesTypesRepository,
        ExportCsvUtil $exportCsvUtil
    ) {
        $this->salesTypeRepository = $salesTypeRepository;
        $this->itemSalesTypesRepository = $itemSalesTypesRepository;
        $this->exportCsvUtil = $exportCsvUtil;
    }

    /**
     * sales_types, item_sales_typesをまとめて削除
     *
     * @param int $id
     *
     * @return void
     */
    public function delete($id)
    {
        DB::transaction(function () use ($id) {
            $this->salesTypeRepository->delete($id);
            $this->itemSalesTypesRepository->deleteWhere(['sales_type_id' => $id]);
        }, 3);
    }

    /**
     * CSVエクスポート
     *
     * @return \Closure
     */
    public function getCsvExporter()
    {
        $this->exportCsvUtil->setHeaders([
            'id' => __('validation.attributes.sales_type.id'),
            'name' => __('validation.attributes.sales_type.name'),
            'text_color' => __('validation.attributes.sales_type.text_color'),
        ]);

        $exporter = $this->exportCsvUtil->getExporter(function ($exporter) {
            $this->salesTypeRepository->chunk(config('repository.chunk.default'), function ($chunk) use ($exporter) {
                foreach ($chunk as $row) {
                    $exporter($row);
                }
            });
        });

        return $exporter;
    }
}

<?php

namespace App\Services\Admin;

use App\Criteria\ItemDetailIdentification\AdminSearchCriteria;
use App\Criteria\ItemDetailIdentification\AdminSortCriteria;
use App\Repositories\ItemDetailIdentificationRepository;
use App\Utils\Csv\ExportCsvInterface as ExportCsvUtil;

class ItemDetailService extends Service implements ItemDetailServiceInterface
{
    /**
     * @var ItemDetailIdentificationRepository
     */
    private $identRepository;

    /**
     * @var ExportCsvUtil
     */
    private $exportCsvUtil;

    /**
     * @param ItemDetailRepository $identRepository
     */
    public function __construct(
        ItemDetailIdentificationRepository $identRepository,
        ExportCsvUtil $exportCsvUtil
    ) {
        $this->identRepository = $identRepository;
        $this->exportCsvUtil = $exportCsvUtil;
    }

    /**
     * CSVエクスポートを実行するコールバックを取得する
     *
     * @param array $params
     *
     * @return \Closure
     */
    public function exportStockCsv(array $params)
    {
        $this->exportCsvUtil->setHeaders([
            'term_id' => __('resource.term'),
            'season_id' => __('validation.attributes.item.season_id'),
            'organization_id' => __('validation.attributes.item.organization_id'),
            'division_id' => __('validation.attributes.item.division_id'),
            'department_id' => __('validation.attributes.item.department_id'),
            'product_number' => __('validation.attributes.item.product_number'),
            'maker_product_number' => __('validation.attributes.item.maker_product_number'),
            'fashion_speed' => __('validation.attributes.item.fashion_speed'),
            'name' => __('validation.attributes.item.name'),
            'main_store_brand' => __('validation.attributes.item.main_store_brand'),
            'jan_code' => __('validation.attributes.item_detail_identification.jan_code'),
            'color_id' => __('validation.attributes.color.id'),
            'color_name' => __('validation.attributes.color.name'),
            'size_id' => __('validation.attributes.size.id'),
            'ec_stock' => __('validation.attributes.item_detail.ec_stock'),
            'reservable_stock' => __('validation.attributes.item_detail.reservable_stock'),
            'slow_moving_inventory_days' => __('validation.attributes.item_detail.slow_moving_inventory_days'),
            'dead_inventory_days' => __('validation.attributes.item_detail.dead_inventory_days'),
            'latest_added_stock' => __('validation.attributes.item_detail.latest_added_stock'),
            'latest_stock_added_at' => __('validation.attributes.item_detail.latest_stock_added_at'),
            'redisplay_requested' => __('validation.attributes.item_detail.redisplay_requested'),
            'status' => __('validation.attributes.item_detail.status'),
            'sales_status' => __('validation.attributes.item.sales_status'),
        ]);

        $this->identRepository->pushCriteria(new AdminSearchCriteria($params));

        $this->identRepository->pushCriteria(new AdminSortCriteria($params));

        $this->identRepository->with(['itemDetail.item', 'itemDetail.color']);

        return $this->exportCsvUtil->getExporter(function ($exporter) {
            $this->identRepository->chunk(config('repository.chunk.default'), function ($chunk) use ($exporter) {
                foreach ($chunk as $row) {
                    $data = [];
                    $data['term_id'] = $row->itemDetail->item->term_id;
                    $data['season_id'] = $row->itemDetail->item->season_id;
                    $data['organization_id'] = $row->itemDetail->item->organization_id;
                    $data['division_id'] = $row->itemDetail->item->division_id;
                    $data['department_id'] = $row->itemDetail->item->department_id;
                    $data['product_number'] = $row->itemDetail->item->product_number;
                    $data['maker_product_number'] = $row->itemDetail->item->maker_product_number;
                    $data['fashion_speed'] = $row->itemDetail->item->fashion_speed;
                    $data['name'] = $row->itemDetail->item->name;
                    $data['sales_status'] = \App\Enums\Item\SalesStatus::getDescription($row->itemDetail->item->sales_status);

                    if (!empty($row->itemDetail->item->main_store_brand)) {
                        $data['main_store_brand'] = \App\Enums\Common\StoreBrand::getDescription($row->itemDetail->item->main_store_brand);
                    }

                    $data['jan_code'] = $row->jan_code;
                    $data['color_id'] = $row->itemDetail->color->id;
                    $data['color_name'] = $row->itemDetail->color->name;
                    $data['size_id'] = $row->itemDetail->size_id;
                    $data['ec_stock'] = $row->ec_stock;
                    $data['reservable_stock'] = $row->reservable_stock;
                    $data['slow_moving_inventory_days'] = $row->slow_moving_inventory_days;
                    $data['dead_inventory_days'] = $row->dead_inventory_days;
                    $data['latest_added_stock'] = $row->latest_added_stock;
                    $data['latest_stock_added_at'] = $row->latest_stock_added_at;
                    $data['redisplay_requested'] = \App\Utils\Csv::fomatBoolean($row->itemDetail->redisplay_requested);
                    $data['status'] = \App\Enums\Common\Status::getDescription($row->itemDetail->status);

                    $exporter($data);
                }
            });
        });
    }
}

<?php

namespace App\Services\Admin;

use App\Criteria\SalesAggregation\AdminItemCriteria;
use App\Criteria\SalesAggregation\AdminOrderCriteria;
use App\Criteria\SalesAggregation\AdminSortItemCriteria;
use App\Exceptions\FatalException;
use App\Pagination\LengthAwarePaginator;
use App\Repositories\ItemDetailRepository;
use App\Repositories\OnlineCategoryRepository;
use App\Repositories\OrderDetailRepository;
use App\Repositories\SalesAggregation\ItemRepository;
use App\Repositories\SalesAggregation\OrderRepository;
use App\Utils\Arr;
use App\Utils\Csv\ExportCsvInterface as ExportCsvUtil;
use Carbon\Carbon;
use Illuminate\Database\Query\JoinClause;

class SalesAggregationService extends Service implements SalesAggregationServiceInterface
{
    /**
     * @var ExportCsv
     */
    private $exportCsvUtil;

    /**
     * @var OrderRepository
     */
    private $orderRepository;

    /**
     * @var OrderDetailRepository
     */
    private $orderDetailRepository;

    /**
     * @var ItemRepository
     */
    private $itemRepository;

    /**
     * @var ItemDetailRepository
     */
    private $itemDetailRepository;

    /**
     * @var OnlineCategoryRepository
     */
    private $onlineCategoryRepository;

    /**
     * @param ExportCsvUtil $exportCsvUtil
     * @param OrderRepository $orderRepository
     * @param ItemRepository $itemRepository
     */
    public function __construct(
        ExportCsvUtil $exportCsvUtil,
        OrderRepository $orderRepository,
        OrderDetailRepository $orderDetailRepository,
        ItemRepository $itemRepository,
        ItemDetailRepository $itemDetailRepository,
        OnlineCategoryRepository $onlineCategoryRepository
    ) {
        $this->exportCsvUtil = $exportCsvUtil;
        $this->orderRepository = $orderRepository;
        $this->orderDetailRepository = $orderDetailRepository;
        $this->itemRepository = $itemRepository;
        $this->itemDetailRepository = $itemDetailRepository;
        $this->onlineCategoryRepository = $onlineCategoryRepository;
    }

    /**
     * @param array $params
     *
     * @return LengthAwarePaginator
     */
    public function aggregateOrders(array $params)
    {
        $this->orderRepository->pushCriteria(new AdminOrderCriteria($params));

        // カテゴリありの場合と処理を分ける
        if ((int) $params['group2'] !== \App\Enums\OrderAggregation\Group2::OnlineCategory) {
            return $this->orderRepository->aggregate(
                $params,
                $params['per_page'] ?? config('repository.pagination.admin_limit')
            );
        }

        $itemGroups = $this->itemRepository->getItemCategoryGroups($params['online_category_id']);

        $data = $this->orderRepository->aggregateWithCategory(
            array_merge($params, ['item_category_group' => $itemGroups]),
            $params['per_page'] ?? config('repository.pagination.admin_limit')
        );

        return $data;
    }

    /**
     * @param array $params
     *
     * @return array
     */
    public function aggregateItems($params)
    {
        $this->itemRepository->pushCriteria(new AdminItemCriteria($params));
        $this->itemRepository->pushCriteria(new AdminSortItemCriteria($params));

        $items = $this->itemRepository->aggregate(
            $params['perPage'] ?? config('repository.pagination.admin_limit')
        );

        $items->load(['itemImages']);

        $items = $this->loadItemDetails($items, $params);

        return $items;
    }

    /**
     * @return collection
     */
    public function aggreateDailyOrder()
    {
        $retailTotalByBrand = $this->orderDetailRepository->whereHas('order', function ($query) {
            $query->whereDate('order_date', Carbon::yesterday()->toDateString());
        })->get()->groupBy('itemDetail.item.main_store_brand')->map(function ($orderDetailsByBrand) {
            return $orderDetailsByBrand->sum('retail_price');
        })->toArray();

        return $retailTotalByBrand;
    }

    /**
     * @return collection
     */
    public function aggreateMonthlyOrder($by)
    {
        $retailTotalByBrand = $this->orderDetailRepository
        ->whereHas('order', function ($query) use ($by) {
            $query->whereDate($by, '<=', Carbon::yesterday());
            $query->whereDate($by, '>=', Carbon::now()->startOfMonth());
        })->get()->groupBy('itemDetail.item.main_store_brand')->map(function ($orderDetailsByBrand) {
            return $orderDetailsByBrand->sum('retail_price');
        })->toArray();

        return $retailTotalByBrand;
    }

    /**
     * @param \Illuminate\Database\Eloquent\Collection $items
     * @param array $params
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    private function loadItemDetails($items, $params)
    {
        $itemDetails = $this->itemDetailRepository->findWhereIn('item_id', $items->pluck('id')->toArray());

        $itemDetails->load([
            'orderDetails' => function ($query) use ($params) {
                return $query->join('orders', function (JoinClause $join) use ($params) {
                    $join = $join->on('order_details.order_id', '=', 'orders.id');
                    $join = AdminItemCriteria::appliyOrderConditions($join, $params, $this->itemRepository);

                    return $join;
                })->select(['order_details.*']);
            },
            'orderDetails.orderDetailUnits',
            'orderDetails.displayedDiscount',
            'orderDetails.bundleSaleDiscount',
        ]);

        $itemDetailGroups = Arr::group($itemDetails, 'item_id');

        foreach ($items as $item) {
            if (!isset($itemDetailGroups[$item->id])) {
                continue;
            }

            $group = $itemDetailGroups[$item->id];
            $processed = [];

            foreach ($group as $itemDetail) {
                $amount = $itemDetail->orderDetails->filter(function ($orderDetail) use ($item) {
                    return (int) $orderDetail->price_before_order === (int) $item->contracted_price;
                })->sum('amount');

                if ($amount === 0) {
                    continue;
                }

                $clone = $itemDetail->replicate();
                $clone->amount = $amount;

                $processed[] = $clone;
            }

            $item->setRelation('itemDetails', $processed);
        }

        return $items;
    }

    /**
     * @param array $params
     *
     * @return \Closure
     */
    public function getSalesOrderCsvExporter(array $params)
    {
        // 日次で固定
        $params['unit'] = \App\Enums\OrderAggregation\Unit::Daily;

        $this->exportCsvUtil->setHeaders([
            'organization' => __('validation.attributes.item.organization_id'),
            'division' => __('validation.attributes.item.division_id'),
            'main_store_brand' => __('validation.attributes.item.main_store_brand'),
            'department' => __('validation.attributes.item.department_id'),
            'online_category' => __('resource.online_category'),
            'date' => __('validation.attributes.date'),
            'sale_type' => __('validation.attributes.order.sale_type'),
            'by_type' => __('validation.attributes.sales_aggregation_order.by_type'),
            'total_price' => __('validation.attributes.sales_aggregation_order.total_price'),
            'total_amount' => __('validation.attributes.sales_aggregation_order.total_amount'),
        ]);

        $this->orderRepository->pushCriteria(
            new \App\Criteria\SalesAggregation\AdminOrderCriteria($params)
        );

        return $this->exportCsvUtil->getExporter(function ($exporter) use ($params) {
            if ((int) $params['group2'] !== \App\Enums\OrderAggregation\Group2::OnlineCategory) {
                return $this->exportOrderCsv($exporter, $params);
            }

            $itemGroups = $this->itemRepository->getItemCategoryGroups($params['online_category_id']);

            foreach ($itemGroups as $id => $items) {
                $onlineCategory = $this->onlineCategoryRepository->find($id);

                $this->exportOrderCsv($exporter, array_merge($params, [
                    'item_id' => $items->pluck('id')->toArray(),
                ]), $onlineCategory);
            }
        });
    }

    /**
     * @param \Closure $exporter
     * @param array $params
     * @param \App\Models\OnlineCategory $onlineCategory (default: null)
     *
     * @return void
     */
    private function exportOrderCsv(\Closure $exporter, array $params, $onlineCategory = null)
    {
        $this->orderRepository->setAggregateCsvScopeQuery($params);

        $this->orderRepository->chunk(config('repository.chunk.default'), function ($chunk) use ($exporter, $params, $onlineCategory) {
            foreach ($chunk as $row) {
                $data = [];
                $data['organization'] = $row->organization_name;
                $data['department'] = $row->department_name;
                $data['division'] = $row->division_name;
                $data['online_category'] = empty($onlineCategory) ? $row->online_category_name : $onlineCategory->name;

                if (!empty($row->main_store_brand)) {
                    $data['main_store_brand'] = \App\Enums\Common\StoreBrand::getDescription($row->main_store_brand);
                }

                $data['date'] = \App\Utils\Csv::formatDate($row->date);
                $data['sale_type'] = \App\Enums\Order\SaleType::getDescription($row->sale_type);
                $data['by_type'] = \App\Enums\OrderAggregation\By::getDescription($params['by']);
                $data['total_price'] = number_format($row->total_price);
                $data['total_amount'] = $row->total_amount;

                $exporter($data);
            }
        });
    }

    /**
     * @param array $params
     *
     * @return \Closure
     */
    public function getSalesOrderDetailCsvExporter(array $params)
    {
        $this->exportCsvUtil->setHeaders([
            'organization' => __('validation.attributes.item.organization_id'),
            'division' => __('validation.attributes.item.division_id'),
            'main_store_brand' => __('validation.attributes.item.main_store_brand'),
            'department' => __('validation.attributes.item.department_id'),
            'online_category' => __('resource.online_category'),
            'date' => __('validation.attributes.date'),
            'sale_type' => __('validation.attributes.order.sale_type'),
            'product_number' => __('validation.attributes.item.product_number'),
            'season_id' => __('validation.attributes.item.season_id'),
            'maker_product_number' => __('validation.attributes.item.maker_product_number'),
            'brand_name' => __('validation.attributes.brand.name'),
            'item_name' => __('validation.attributes.item.name'),
            'by_type' => __('validation.attributes.sales_aggregation_order.by_type'),
            'contracted_price' => __('validation.attributes.sales_aggregation_order.contracted_price'),
            'total_price' => __('validation.attributes.sales_aggregation_order.total_price'),
            'total_amount' => __('validation.attributes.sales_aggregation_order.total_amount'),
            'item_url' => __('validation.attributes.sales_aggregation_order.item_url'),
        ]);

        $this->orderRepository->pushCriteria(
            new \App\Criteria\SalesAggregation\AdminOrderCriteria($params)
        );

        return $this->exportCsvUtil->getExporter(function ($exporter) use ($params) {
            if ((int) $params['group2'] !== \App\Enums\OrderAggregation\Group2::OnlineCategory) {
                return $this->exportOrderDetailCsv($exporter, $params);
            }

            $itemGroups = $this->itemRepository->getItemCategoryGroups($params['online_category_id']);

            foreach ($itemGroups as $id => $items) {
                $onlineCategory = $this->onlineCategoryRepository->find($id);

                $this->exportOrderDetailCsv($exporter, array_merge($params, [
                    'item_id' => $items->pluck('id')->toArray(),
                ]), $onlineCategory);
            }
        });
    }

    /**
     * @param \Closure $exporter
     * @param array $params
     * @param \App\Models\OnlineCategory $onlineCategory (default: null)
     *
     * @return void
     */
    private function exportOrderDetailCsv(\Closure $exporter, array $params, $onlineCategory = null)
    {
        $this->orderRepository->setAggregateOrderDetailCsvScopeQuery($params);

        $this->orderRepository->chunk(config('repository.chunk.default'), function ($chunk) use ($exporter, $params, $onlineCategory) {
            foreach ($chunk as $row) {
                $data = [];
                $data['organization'] = $row->organization_name;
                $data['department'] = $row->department_name;
                $data['division'] = $row->division_name;
                $data['online_category'] = empty($onlineCategory) ? $row->online_category_name : $onlineCategory->name;
                $data['product_number'] = $row->product_number;
                $data['season_id'] = $row->season_id;
                $data['maker_product_number'] = $row->maker_product_number;
                $data['brand_name'] = $row->brand_name;
                $data['item_name'] = $row->item_name;

                if (!empty($row->main_store_brand)) {
                    $data['main_store_brand'] = \App\Enums\Common\StoreBrand::getDescription($row->main_store_brand);
                }

                $data['date'] = $this->formateCsvDate($row->date, $params['unit']);
                $data['sale_type'] = \App\Enums\Order\SaleType::getDescription($row->sale_type);
                $data['by_type'] = \App\Enums\OrderAggregation\By::getDescription($params['by']);
                $data['contracted_price'] = number_format($row->contracted_price);
                $data['total_price'] = number_format($row->total_price);
                $data['total_amount'] = $row->total_amount;
                $data['item_url'] = \App\Utils\Url::resolveFrontUrl('item_detail', ['id' => $row->item_id]);

                $exporter($data);
            }
        });
    }

    /**
     * @param string $date
     * @param int $unit
     *
     * @return string
     */
    private function formateCsvDate($date, $unit)
    {
        switch ($unit) {
            case \App\Enums\OrderAggregation\Unit::Daily:
                return \App\Utils\Csv::formatDate($date);

            case \App\Enums\OrderAggregation\Unit::Weekly:
                return date('Y_m (週', strtotime($date));

            case \App\Enums\OrderAggregation\Unit::Monthly:
                return date('Y/m', strtotime($date));

            default:
                throw new FatalException(error_format('error.invalid_arguments', ['unit' => $unit]));
        }
    }

    /**
     * @param array $params
     *
     * @return \Closure
     */
    public function getSalesItemCsvExporter(array $params)
    {
        $this->exportCsvUtil->setHeaders([
            'organization' => __('validation.attributes.item.organization_id'),
            'division' => __('validation.attributes.item.division_id'),
            'main_store_brand' => __('validation.attributes.item.main_store_brand'),
            'department' => __('validation.attributes.item.department_id'),
            'online_category_1' => sprintf('%s-%s', __('resource.online_category'), \App\Enums\OnlineCategory\Level::getDescription(\App\Enums\OnlineCategory\Level::Level_1)),
            'online_category_2' => sprintf('%s-%s', __('resource.online_category'), \App\Enums\OnlineCategory\Level::getDescription(\App\Enums\OnlineCategory\Level::Level_2)),
            'online_category_3' => sprintf('%s-%s', __('resource.online_category'), \App\Enums\OnlineCategory\Level::getDescription(\App\Enums\OnlineCategory\Level::Level_3)),
            'online_category_4' => sprintf('%s-%s', __('resource.online_category'), \App\Enums\OnlineCategory\Level::getDescription(\App\Enums\OnlineCategory\Level::Level_4)),
            'online_category_5' => sprintf('%s-%s', __('resource.online_category'), \App\Enums\OnlineCategory\Level::getDescription(\App\Enums\OnlineCategory\Level::Level_5)),
            'term' => __('validation.attributes.sales_aggregation_order.term'),
            'sale_type' => __('validation.attributes.order.sale_type'),
            'product_number' => __('validation.attributes.item.product_number'),
            'season_id' => __('validation.attributes.item.season_id'),
            'maker_product_number' => __('validation.attributes.item.maker_product_number'),

            'jan_code' => __('validation.attributes.item_detail_identification.jan_code'),
            'color_id' => __('validation.attributes.color.id'),
            'color_name' => __('validation.attributes.color.name'),
            'brand_name' => __('validation.attributes.brand.name'),
            'item_name' => __('validation.attributes.item.name'),
            'retail_price' => __('validation.attributes.item.retail_price'),
            'contracted_price' => __('validation.attributes.sales_aggregation_order.contracted_price'),
            'total_price' => __('validation.attributes.sales_aggregation_order.total_price'),
            'total_amount' => __('validation.attributes.sales_aggregation_order.total_amount'),
            'by_type' => __('validation.attributes.sales_aggregation_order.by_type'),
            'item_url' => __('validation.attributes.sales_aggregation_order.item_url'),
        ]);

        $this->itemRepository->pushCriteria(
            new \App\Criteria\SalesAggregation\AdminItemCriteria($params)
        );
        $this->itemRepository->with([
            'brand',
            'onlineCategories',
        ])->setCsvScopeQuery();

        return $this->exportCsvUtil->getExporter(function ($exporter) use ($params) {
            $this->itemRepository->chunk(config('repository.chunk.default'), function ($chunk) use ($exporter, $params) {
                foreach ($chunk as $row) {
                    $data = [];

                    $data['organization'] = $row->organization_name;
                    $data['division'] = $row->division_name;
                    $data['department'] = $row->department_name;

                    if (!empty($row->main_store_brand)) {
                        $data['main_store_brand'] = \App\Enums\Common\StoreBrand::getDescription($row->main_store_brand);
                    }

                    $onlineCategories = $row->onlineCategories->toArray();

                    $data['online_category_1'] = implode('・', array_reduce($onlineCategories, function ($categories, $item) {
                        return $item['level'] === \App\Enums\OnlineCategory\Level::Level_1
                            ? array_merge($categories, [$item['name']])
                            : $categories;
                    }, []));
                    $data['online_category_2'] = implode('・', array_reduce($onlineCategories, function ($categories, $item) {
                        return $item['level'] === \App\Enums\OnlineCategory\Level::Level_2
                            ? array_merge($categories, [$item['name']])
                            : $categories;
                    }, []));
                    $data['online_category_3'] = implode('・', array_reduce($onlineCategories, function ($categories, $item) {
                        return $item['level'] === \App\Enums\OnlineCategory\Level::Level_3
                            ? array_merge($categories, [$item['name']])
                            : $categories;
                    }, []));
                    $data['online_category_4'] = implode('・', array_reduce($onlineCategories, function ($categories, $item) {
                        return $item['level'] === \App\Enums\OnlineCategory\Level::Level_4
                            ? array_merge($categories, [$item['name']])
                            : $categories;
                    }, []));
                    $data['online_category_5'] = implode('・', array_reduce($onlineCategories, function ($categories, $item) {
                        return $item['level'] === \App\Enums\OnlineCategory\Level::Level_5
                            ? array_merge($categories, [$item['name']])
                            : $categories;
                    }, []));

                    $data['term'] = sprintf('%s〜%s', \App\Utils\Csv::formatDatetime($params['date_from']), \App\Utils\Csv::formatDatetime($params['date_to']));
                    $data['sale_type'] = \App\Enums\Order\SaleType::getDescription($row->sale_type);
                    $data['product_number'] = $row->product_number;
                    $data['season_id'] = $row->season_id;
                    $data['maker_product_number'] = $row->maker_product_number;

                    $data['jan_code'] = $row->jan_code;
                    $data['color_id'] = $row->color_id;
                    $data['color_name'] = $row->color_name;
                    $data['brand_name'] = $row->brand_name;
                    $data['item_name'] = $row->item_name;

                    $data['retail_price'] = number_format($row->retail_price);
                    $data['contracted_price'] = number_format($row->contracted_price);
                    $data['total_price'] = number_format($row->total_price);
                    $data['total_amount'] = $row->total_amount;
                    $data['by_type'] = \App\Enums\OrderAggregation\By::getDescription($params['by']);
                    $data['item_url'] = \App\Utils\Url::resolveFrontUrl('item_detail', ['id' => $row->item_id]);

                    $exporter($data);
                }
            });
        });
    }
}

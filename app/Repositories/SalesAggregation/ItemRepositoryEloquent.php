<?php

namespace App\Repositories\SalesAggregation;

use App\Database\Utils\Query as QueryUtil;
use App\Models\Item;
use App\Repositories\Traits\QueryBuilderMethodTrait;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Support\Facades\DB;

/**
 * 商品売上集計用リポジトリ
 *
 * @package namespace App\Repositories\SalesAggregation;
 */
class ItemRepositoryEloquent extends SalesAggregationRepository implements ItemRepository
{
    use QueryBuilderMethodTrait;

    /**
     * @return string
     */
    public function model()
    {
        return Item::class;
    }

    public function boot()
    {
    }

    /**
     * @return void
     */
    public function applyAggregationQuery()
    {
        $columns = [
            'items.id',
            'items.division_id',
            'items.main_store_brand',
            'items.department_id',
            'items.product_number',
            'items.maker_product_number',
            'items.retail_price',
        ];

        $model = $this->model;

        $sub = \App\Models\OrderDetailUnit::query()->select([
            'order_detail_units.order_detail_id',
            DB::raw('SUM(order_detail_units.amount) as amount'),
        ])->groupBy(['order_detail_units.order_detail_id']);

        $model = $model->select(
            array_merge($columns, [
                DB::raw('SUM(order_detail_units2.amount) as total_amount'),
                DB::raw('@contractedPrice := (order_details.retail_price - IFNULL(order_discounts2.unit_applied_price, 0)) as contracted_price'),
                DB::raw('SUM(order_detail_units2.amount * @contractedPrice) as total_price'),
            ])
        )
            ->join('item_details', 'items.id', '=', 'item_details.item_id')
            ->join('order_details', function (JoinClause $join) {
                return $join->on('item_details.id', '=', 'order_details.item_detail_id')->whereNull('order_details.deleted_at');
            })
            ->joinSub($sub, 'order_detail_units2', function (JoinClause $join) {
                return $join->on('order_details.id', '=', 'order_detail_units2.order_detail_id');
            })
            ->join('orders', function (JoinClause $join) {
                return $join->on('order_details.order_id', '=', 'orders.id')->whereNull('orders.deleted_at');
            })
            ->leftJoinSub($this->createOrderDiscountQuery(), 'order_discounts2', function (JoinClause $join) {
                return $join->on('order_details.id', '=', 'order_discounts2.order_detail_id');
            })
            ->groupBy(array_merge($columns, ['contracted_price']));

        $this->model = $model;
    }

    /**
     * @param int $perPage
     *
     * @return \Illuminate\Database\Eloquent\Collection|\Illuminate\Pagination\LengthAwarePaginator
     */
    public function aggregate(int $perPage = null)
    {
        $this->applyCriteria();
        $this->applyScope();
        $this->applyAggregationQuery();

        $model = $this->model;

        $results = isset($perPage) ? $model->paginateWithGroupBy('items.id', $perPage) : $model->get();

        $this->resetModel();
        $this->resetScope();

        return $results;
    }

    /**
     * @return $this
     */
    public function setCsvScopeQuery()
    {
        return $this->scopeQuery(function ($query) {
            $columns = [
                'items.division_id',
                'items.main_store_brand',
                'items.department_id',
                'items.product_number',
                'items.maker_product_number',
                'items.retail_price',
                'items.id as item_id',
                'items.name as item_name',
                'item_detail_identifications.jan_code',
                'order_details.sale_type',
                'colors.id as color_id',
                'colors.name as color_name',
                'organizations.name as organization_name',
                'departments.name as department_name',
                'divisions.name as division_name',
            ];

            return $query->select(
                array_merge($columns, [
                    DB::raw('SUM(order_detail_units.amount) as total_amount'),
                    DB::raw('@contractedPrice := (order_details.retail_price - IFNULL(order_discounts2.unit_applied_price, 0)) as contracted_price'),
                    DB::raw('SUM(order_detail_units.amount * @contractedPrice) as total_price'),
                ])
            )
                ->join('organizations', 'items.organization_id', '=', 'organizations.id')
                ->join('departments', 'items.department_id', '=', 'departments.id')
                ->join('divisions', 'items.division_id', '=', 'divisions.id')
                ->join('item_details', 'items.id', '=', 'item_details.item_id')
                ->join('item_detail_identifications', 'item_details.id', '=', 'item_detail_identifications.item_detail_id')
                ->join('colors', 'item_details.color_id', '=', 'colors.id')
                ->join('order_details', function (JoinClause $join) {
                    return $join->on('item_details.id', '=', 'order_details.item_detail_id')->whereNull('order_details.deleted_at');
                })
                ->join('order_detail_units', function (JoinClause $join) {
                    return $join->on('order_details.id', '=', 'order_detail_units.order_detail_id')
                        ->where('order_detail_units.item_detail_identification_id', DB::raw('item_detail_identifications.id'))
                        ->whereNull('order_detail_units.deleted_at');
                })
                ->join('orders', function (JoinClause $join) {
                    return $join->on('order_details.order_id', '=', 'orders.id')->whereNull('orders.deleted_at');
                })
                ->leftJoinSub($this->createOrderDiscountQuery(), 'order_discounts2', function (JoinClause $join) {
                    return $join->on('order_details.id', '=', 'order_discounts2.order_detail_id');
                })
                ->groupBy(array_merge(QueryUtil::removeSelectAliases($columns), ['contracted_price']))
                ->orderBy('item_id');
        });
    }

    /**
     * @param array $onlineCategoryIds
     *
     * @return array
     */
    public function getItemCategoryGroups(array $onlineCategoryIds)
    {
        $itemGroups = [];

        foreach ($onlineCategoryIds as $onlineCategoryId) {
            $items = $this->model->hasOnlineCategories([$onlineCategoryId])->get();

            if ($items->count() === 0) {
                continue;
            }

            $itemGroups[$onlineCategoryId] = $items;
            $this->resetModel();
        }

        return $itemGroups;
    }
}

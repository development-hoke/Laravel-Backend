<?php

namespace App\Repositories\SalesAggregation;

use App\Database\Utils\Query as QueryUtil;
use App\Exceptions\FatalException;
use App\Models\OrderDetail;
use App\Pagination\LengthAwarePaginator;
use App\Repositories\Traits\QueryBuilderMethodTrait;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Support\Facades\DB;

/**
 * 注文集計用リポジトリ
 *
 * @package namespace App\Repositories\SalesAggregation;
 */
class OrderRepositoryEloquent extends SalesAggregationRepository implements OrderRepository
{
    use QueryBuilderMethodTrait;

    protected $aggregationGroup1Fields = [
        \App\Enums\OrderAggregation\Group1::Organization => 'items.organization_id',
        \App\Enums\OrderAggregation\Group1::Division => 'items.division_id',
        \App\Enums\OrderAggregation\Group1::MainStoreBrand => 'items.main_store_brand',
    ];

    protected $aggregationGroup2Fields = [
        \App\Enums\OrderAggregation\Group2::Department => 'items.department_id',
        \App\Enums\OrderAggregation\Group2::OnlineCategory => 'item_online_categories.online_category_id',
    ];

    private $paginatingParams;

    /**
     * @return string
     */
    public function model()
    {
        return OrderDetail::class;
    }

    public function boot()
    {
    }

    /**
     * groupbyを適用する
     *
     * @param array $params
     * @param int $perPage
     *
     * @return \Illuminate\Database\Eloquent\Collection|\App\Pagination\LengthAwarePaginator
     */
    public function aggregate(array $params, int $perPage = null)
    {
        if (isset($perPage)) {
            $range = $this->preparePagination($params, $perPage);
        }

        $this->applyCriteria();
        $this->applyScope();

        $model = $this->model;

        if (!empty($range)) {
            [$start, $end] = $range;
            $model = $model->having('date', '>=', $start)->having('date', '<=', $end);
        }

        $model = $this->applyBaseAggregationQuery($params, $model);

        $results = $model->get();

        if (isset($perPage)) {
            $results = $this->createPaginator($results);
        }

        $this->resetModel();
        $this->resetScope();

        return $results;
    }

    /**
     * @param array $params
     * @param \App\Models\Model|\Illuminate\Database\Eloquent\Builder $model
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    private function applyBaseAggregationQuery(array $params, $model = null, $onlineCategoryId = false)
    {
        $model = $model ?? $this->model;

        $columns = $this->compileSelectStatement($params);

        if ($onlineCategoryId !== false) {
            $columns[1] = DB::raw($onlineCategoryId . ' as group2');
        }

        $group = $this->compileGroups();

        $sub = \App\Models\OrderDetailUnit::query()->select([
            'order_detail_units.order_detail_id',
            DB::raw('SUM(order_detail_units.amount) as amount'),
        ])->groupBy(['order_detail_units.order_detail_id']);

        $model = $model->select(
            array_merge($columns, [
                DB::raw('SUM(order_detail_units2.amount) as total_amount'),
                DB::raw('SUM(
                    order_detail_units2.amount * (
                        order_details.retail_price - IFNULL(order_discounts2.unit_applied_price, 0)
                    )
                ) as total_price'),
            ])
        )
            ->joinSub($sub, 'order_detail_units2', function (JoinClause $join) {
                return $join->on('order_details.id', '=', 'order_detail_units2.order_detail_id');
            })
            ->join('orders', function (JoinClause $join) {
                return $join->on('order_details.order_id', '=', 'orders.id')->whereNull('orders.deleted_at');
            })
            ->join('item_details', 'order_details.item_detail_id', '=', 'item_details.id')
            ->join('items', 'item_details.item_id', '=', 'items.id')
            ->leftJoinSub($this->createOrderDiscountQuery(), 'order_discounts2', function (JoinClause $join) {
                return $join->on('order_details.id', '=', 'order_discounts2.order_detail_id');
            })
            ->groupBy($group)
            ->orderBy('date', 'desc');

        return $model;
    }

    /**
     * groupbyを適用する
     *
     * @param array $params
     * @param int $perPage
     *
     * @return \Illuminate\Database\Eloquent\Collection|\App\Pagination\LengthAwarePaginator
     */
    public function aggregateWithCategory(array $params, int $perPage = null)
    {
        $itemGroups = $params['item_category_group'];
        $range = [];

        if (isset($perPage)) {
            $itemIds = array_reduce($itemGroups, function ($itemIds, $items) {
                return array_merge($itemIds, $items->pluck('id')->toArray());
            }, []);

            $range = $this->preparePagination(array_merge($params, ['item_id' => array_unique($itemIds)]), $perPage);
        }

        $results = new Collection();

        foreach ($itemGroups as $onlineCategoryId => $items) {
            $this->applyCriteria();
            $this->applyScope();

            $model = $this->model;

            if (!empty($range)) {
                [$start, $end] = $range;
                $model = $model->having('date', '>=', $start)->having('date', '<=', $end);
            }

            $model = $this->applyBaseAggregationQuery($params, $model, $onlineCategoryId);

            $model = $model->join('item_online_categories', 'items.id', '=', 'item_online_categories.item_id')
                ->whereIn('items.id', $items->pluck('id')->toArray());

            $results = $results->concat($model->get());

            $this->resetModel();
        }

        if (isset($perPage)) {
            $results = $this->createPaginator($results);
        }

        $this->resetScope();

        return $results;
    }

    /**
     * CSV出力クエリを設定
     *
     * @param array $params
     *
     * @return $this
     */
    public function setAggregateCsvScopeQuery(array $params)
    {
        return $this->scopeQuery(function ($query) use ($params) {
            $columns = [
                'items.organization_id',
                'items.division_id',
                'items.main_store_brand',
                'items.department_id',
                'order_details.sale_type',
                'online_categories.id as online_category_id',
                'online_categories.name as online_category_name',
                'organizations.name as organization_name',
                'departments.name as department_name',
                'divisions.name as division_name',
            ];

            if (!empty($params['item_id'])) {
                $query = $query->whereIn('items.id', $params['item_id']);
            }

            $sub = \App\Models\OrderDetailUnit::query()->select([
                'order_detail_units.order_detail_id',
                DB::raw('SUM(order_detail_units.amount) as amount'),
            ])->groupBy(['order_detail_units.order_detail_id']);

            return $query->select(
                array_merge($columns, [
                    $this->compileDateUnitStatement($params, true),
                    DB::raw('SUM(order_detail_units2.amount) as total_amount'),
                    DB::raw('SUM(
                        order_detail_units2.amount * (
                            order_details.retail_price - IFNULL(order_discounts2.unit_applied_price, 0)
                        )
                    ) as total_price'),
                ])
            )
                ->joinSub($sub, 'order_detail_units2', function (JoinClause $join) {
                    return $join->on('order_details.id', '=', 'order_detail_units2.order_detail_id');
                })
                ->join('orders', function (JoinClause $join) {
                    return $join->on('order_details.order_id', '=', 'orders.id')->whereNull('orders.deleted_at');
                })
                ->join('item_details', 'order_details.item_detail_id', '=', 'item_details.id')
                ->join('items', 'item_details.item_id', '=', 'items.id')
                ->join('organizations', 'items.organization_id', '=', 'organizations.id')
                ->join('departments', 'items.department_id', '=', 'departments.id')
                ->join('divisions', 'items.division_id', '=', 'divisions.id')
                ->leftJoinSub($this->createOrderDiscountQuery(), 'order_discounts2', function (JoinClause $join) {
                    return $join->on('order_details.id', '=', 'order_discounts2.order_detail_id');
                })
                ->leftJoin('item_online_categories', 'items.id', '=', 'item_online_categories.item_id')
                ->leftJoin('online_categories', 'item_online_categories.online_category_id', '=', 'online_categories.id')
                ->groupBy(array_merge(QueryUtil::removeSelectAliases($columns), ['date']))
                ->orderBy('date', 'desc');
        });
    }

    /**
     * CSV出力クエリを設定（商品あり）
     *
     * @param array $params
     *
     * @return $this
     */
    public function setAggregateOrderDetailCsvScopeQuery(array $params)
    {
        return $this->scopeQuery(function ($query) use ($params) {
            $columns = [
                'order_details.sale_type',
                'items.organization_id',
                'items.division_id',
                'items.main_store_brand',
                'items.department_id',
                'items.brand_id',
                'items.season_id',
                'items.product_number',
                'items.maker_product_number',
                'online_categories.id as online_category_id',
                'online_categories.name as online_category_name',
                'organizations.name as organization_name',
                'departments.name as department_name',
                'divisions.name as division_name',
                'items.id as item_id',
                'items.name as item_name',
                'brands.name as brand_name',
            ];

            if (!empty($params['item_id'])) {
                $query = $query->whereIn('items.id', $params['item_id']);
            }

            $sub = \App\Models\OrderDetailUnit::query()->select([
                'order_detail_units.order_detail_id',
                DB::raw('SUM(order_detail_units.amount) as amount'),
            ])->groupBy(['order_detail_units.order_detail_id']);

            return $query->select(
                array_merge($columns, [
                    $this->compileDateUnitStatement($params, true),
                    DB::raw('SUM(order_detail_units2.amount) as total_amount'),
                    DB::raw('@contractedPrice := (order_details.retail_price - IFNULL(order_discounts2.unit_applied_price, 0)) as contracted_price'),
                    DB::raw('SUM(order_detail_units2.amount * @contractedPrice) as total_price'),
                ])
            )
                ->joinSub($sub, 'order_detail_units2', function (JoinClause $join) {
                    return $join->on('order_details.id', '=', 'order_detail_units2.order_detail_id');
                })
                ->join('orders', function (JoinClause $join) {
                    return $join->on('order_details.order_id', '=', 'orders.id')->whereNull('orders.deleted_at');
                })
                ->join('item_details', 'order_details.item_detail_id', '=', 'item_details.id')
                ->join('items', 'item_details.item_id', '=', 'items.id')
                ->join('organizations', 'items.organization_id', '=', 'organizations.id')
                ->join('departments', 'items.department_id', '=', 'departments.id')
                ->join('divisions', 'items.division_id', '=', 'divisions.id')
                ->join('brands', 'items.brand_id', '=', 'brands.id')
                ->leftJoinSub($this->createOrderDiscountQuery(), 'order_discounts2', function (JoinClause $join) {
                    return $join->on('order_details.id', '=', 'order_discounts2.order_detail_id');
                })
                ->leftJoin('item_online_categories', 'items.id', '=', 'item_online_categories.item_id')
                ->leftJoin('online_categories', 'item_online_categories.online_category_id', '=', 'online_categories.id')
                ->groupBy(array_merge(QueryUtil::removeSelectAliases($columns), ['date', 'contracted_price']))
                ->orderBy('date', 'desc');
        });
    }

    /**
     * Paginationを適用する
     *
     * @param array $params
     * @param int $perPage
     *
     * @return void
     */
    private function preparePagination(array $params, $perPage)
    {
        $currentPage = ($params['page'] ?? 1) - 1;

        $this->applyCriteria();
        $this->applyScope();

        $applyItemCondition = function ($model, $params) {
            $model = $model->join('orders', 'orders.id', '=', 'order_details.order_id');

            if (empty($params['item_id'])) {
                return $model;
            }

            return $model
                ->join('item_details', 'order_details.item_detail_id', '=', 'item_details.id')
                ->whereIn('item_details.item_id', $params['item_id']);
        };

        $model = $applyItemCondition($this->model, $params);
        $select = $this->compileDateUnitStatement($params, false);
        $totalCount = $model->distinct($select)->count();

        $this->resetModel();
        $this->applyCriteria();
        $this->applyScope();

        $model = $applyItemCondition($this->model, $params);
        $by = $this->resolveAggregationDateField($params['by']);
        $select = $this->compileDateUnitStatement($params, true);
        $page = $model->distinct()->select($select)->orderBy($by, 'desc')->limit($perPage)->offset($currentPage * $perPage)->get();

        $this->resetModel();

        $range = $page->count() === 0 ? [] : [$page->last()->date, $page->first()->date];

        $this->paginatingParams = [
            'total_count' => $totalCount,
            'per_page' => $perPage,
            'current_page' => $currentPage,
            'range' => $range,
        ];

        return $range;
    }

    /**
     * @param \Illuminate\Database\Eloquent\Collection $items
     *
     * @return LengthAwarePaginator
     */
    private function createPaginator($items)
    {
        $totalCount = $this->paginatingParams['total_count'];
        $currentPage = $this->paginatingParams['current_page'];
        $perPage = $this->paginatingParams['per_page'];
        $range = $this->paginatingParams['range'];

        return new LengthAwarePaginator($items, $totalCount, $perPage, $currentPage, ['range' => $range]);
    }

    /**
     * @param array $params
     *
     * @return array
     */
    protected function compileGroups()
    {
        return ['group1', 'group2', 'sale_type', 'date'];
    }

    /**
     * @param array $params
     *
     * @return array
     */
    protected function compileSelectStatement(array $params)
    {
        $groups = $this->getBaseGroupColumns($params, true);

        $dateUnit = $this->compileDateUnitStatement($params, true);

        return array_merge($groups, [$dateUnit]);
    }

    /**
     * @param array $params
     * @param bool $withAlias
     *
     * @return array
     */
    protected function getBaseGroupColumns(array $params, bool $withAlias = false)
    {
        $columns = [
            $this->aggregationGroup1Fields[$params['group1']] . ($withAlias ? ' as group1' : ''),
            $this->aggregationGroup2Fields[$params['group2']] . ($withAlias ? ' as group2' : ''),
            'order_details.sale_type' . ($withAlias ? ' as sale_type' : ''),
        ];

        return $columns;
    }

    /**
     * @param array $params
     * @param bool $withAlias
     *
     * @return \Illuminate\Database\Query\Expression
     */
    protected function compileDateUnitStatement(array $params, bool $withAlias)
    {
        $dateColumn = $this->resolveAggregationDateField($params['by']);

        $expression = $this->resolveUnitExpressions($params, 'orders.'.$dateColumn);

        if ($withAlias) {
            $expression .= ' as date';
        }

        return DB::raw($expression);
    }

    /**
     * @param array $params
     * @param string $column
     *
     * @return array
     */
    protected function resolveUnitExpressions(array $params, string $column)
    {
        switch ($params['unit']) {
            case \App\Enums\OrderAggregation\Unit::Monthly:
                return "STR_TO_DATE(CONCAT(YEAR({$column}), ',', MONTH({$column}), ',', '1'), '%Y,%m,%d ')";
            case \App\Enums\OrderAggregation\Unit::Weekly:
                return "DATE_FORMAT(DATE_SUB({$column}, INTERVAL WEEKDAY({$column}) DAY), '%Y-%m-%d')";
            case \App\Enums\OrderAggregation\Unit::Daily:
                return "DATE_FORMAT({$column}, '%Y-%m-%d')";
            default:
                throw new FatalException(error_format('error.invalid_arguments', ['unit' => $params['unit']]));
        }
    }
}

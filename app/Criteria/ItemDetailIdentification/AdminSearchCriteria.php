<?php

namespace App\Criteria\ItemDetailIdentification;

use App\Database\Utils\Query as QueryUtil;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\JoinClause;
use Prettus\Repository\Contracts\CriteriaInterface;
use Prettus\Repository\Contracts\RepositoryInterface;

/**
 * Class AdminSearchCriteria.
 *
 * @package namespace App\Criteria\ItemDetailIdentification;
 * @SuppressWarnings(PHPMD.CyclomaticComplexity)
 * @SuppressWarnings(PHPMD.NPathComplexity)
 */
class AdminSearchCriteria implements CriteriaInterface
{
    private $itemColumns = ['organization_id', 'division_id', 'department_id', 'term_id', 'fashion_speed', 'product_number', 'maker_product_number'];

    /**
     * @var array
     */
    protected $params;

    public function __construct(array $params)
    {
        $this->params = $params;
    }

    /**
     * Apply criteria in query repository
     *
     * @param Builder|Model $model
     * @param RepositoryInterface $repository
     *
     * @return mixed
     */
    public function apply($model, RepositoryInterface $repository)
    {
        $params = $this->params;

        if (!QueryUtil::joined('item_details', $model)) {
            $model = $model->join('item_details', 'item_detail_identifications.item_detail_id', '=', 'item_details.id');
        }

        $model = $model->join('items', function (JoinClause $join) use ($params) {
            $join->on('items.id', '=', 'item_details.item_id');

            foreach ($this->itemColumns as $column) {
                if (isset($params[$column])) {
                    if (is_array($params[$column])) {
                        $join->whereIn("items.{$column}", $params[$column]);
                    } else {
                        $join->where("items.{$column}", $params[$column]);
                    }
                }
            }

            $join->whereIn('sales_status', $this->convertSalesStatusConditions($params));
        })->select('item_detail_identifications.*');

        $model = $this->applyItemDetailConditions($model, $params);

        if (isset($params['has_stock'])) {
            $model = $model->where([['ec_stock', '>', 0]]);
        }

        if (isset($params['stock_type'])) {
            $model = $model->whereStockType($params['stock_type']);
        }

        if (isset($params['reservable_stock_type'])) {
            $model = $model->whereReservableStockType($params['reservable_stock_type']);
        }

        if (isset($params['dead_inventory_day_type'])) {
            $model = $model->whereDeadInventoryDayType($params['dead_inventory_day_type']);
        }

        if (isset($params['slow_moving_inventory_day_type'])) {
            $model = $model->whereSlowMovingInventoryDayType($params['slow_moving_inventory_day_type']);
        }

        return $model;
    }

    /**
     * items.sales_statusの条件にパラメタ−を変換する
     *
     * @param array $params
     *
     * @return array
     */
    private function convertSalesStatusConditions(array $params): array
    {
        $conditions = [
            \App\Enums\Item\SalesStatus::InStoreNow,
        ];

        if (isset($params['containing_sales_status_stop']) && $params['containing_sales_status_stop']) {
            $conditions[] = \App\Enums\Item\SalesStatus::Stop;
        }

        if (isset($params['containing_sales_status_sold_out']) && $params['containing_sales_status_sold_out']) {
            $conditions[] = \App\Enums\Item\SalesStatus::SoldOut;
        }

        return $conditions;
    }

    private function applyItemDetailConditions($model, array $params)
    {
        $conditions = [];

        if (isset($params['item_id'])) {
            $conditions[] = ['item_id', '=', $params['item_id']];
        }

        if (isset($params['status'])) {
            $conditions[] = ['status', '=', $params['status']];
        }

        if (isset($params['last_added_stock_date_from'])) {
            $conditions[] = ['latest_stock_added_at', '>=', $params['last_added_stock_date_from']];
        }

        if (isset($params['last_added_stock_date_to'])) {
            $conditions[] = ['latest_stock_added_at', '<=', $params['last_added_stock_date_to']];
        }

        if (empty($conditions)) {
            return $model;
        }

        return $model->whereItemDetail($conditions);
    }
}

<?php

namespace App\Repositories\SalesAggregation;

use App\Repositories\BaseRepositoryEloquent;
use Illuminate\Support\Facades\DB;

/**
 * 売上集計用リポジトリ抽象クラス
 *
 * @package namespace App\Repositories\SalesAggregation;
 */
abstract class SalesAggregationRepository extends BaseRepositoryEloquent
{
    protected $aggregationDateFields = [
        \App\Enums\OrderAggregation\By::Ordered => 'order_date',
        \App\Enums\OrderAggregation\By::Delivered => 'deliveryed_date',
    ];

    /**
     * @param int $by
     *
     * @return string
     */
    public function resolveAggregationDateField($by)
    {
        return $this->aggregationDateFields[$by];
    }

    /**
     * @return \Illuminate\Database\Query\Builder
     */
    protected function createOrderDiscountQuery()
    {
        return \App\Models\OrderDiscount::query()->select([
            'order_discounts.orderable_id as order_detail_id',
            DB::raw('SUM(order_discounts.unit_applied_price) as unit_applied_price'),
        ])
            ->groupBy(['order_discounts.orderable_id'])
            ->where('order_discounts.orderable_type', \App\Models\OrderDetail::class)
            ->whereIn('order_discounts.type', \App\Domain\Utils\OrderDiscount::getDiscountTypesAppliedBeforeOrder());
    }
}

<?php

namespace App\Criteria\ItemDetail;

use App\Criteria\SortCriteria;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Support\Facades\DB;

abstract class BaseAdminSortCriteria extends SortCriteria
{
    /**
     * @var array
     */
    protected static $sortTypes = [
        'ec_stock',
        'reservable_stock',
        'dead_inventory_days',
        'slow_moving_inventory_days',
        'item_detail_request_count',
        'item_detail_records',
        'addedd_stock_yesterday',
    ];

    /**
     * 再入荷リクエストのソート
     *
     * @param mixed $model
     * @param string $orderType
     *
     * @return mixed
     */
    protected function applyItemDetailRequestCountSort($model, $orderType)
    {
        $sub = \App\Models\ItemDetail::query()->select([
            'item_details.id as item_detail_id',
            DB::raw('COUNT(item_detail_redisplay_requests.id) as item_detail_request_count'),
        ])
        ->leftJoin('item_detail_redisplay_requests', 'item_details.id', '=', 'item_detail_redisplay_requests.item_detail_id')
        ->whereNull('item_detail_redisplay_requests.deleted_at')
        ->groupBy('item_details.id');

        $model = $model->leftJoinSub($sub, 'redisplay_requests', function (JoinClause $join) {
            return $join->on('item_details.id', '=', 'redisplay_requests.item_detail_id');
        });

        return $model->orderBy('item_detail_request_count', $orderType);
    }
}

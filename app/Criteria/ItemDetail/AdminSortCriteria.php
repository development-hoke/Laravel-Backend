<?php

namespace App\Criteria\ItemDetail;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Query\JoinClause;
use Prettus\Repository\Contracts\CriteriaInterface;
use Prettus\Repository\Contracts\RepositoryInterface;

/**
 * Class AdminSortCriteria.
 *
 * @package namespace App\Criteria\ItemDetail;
 * @SuppressWarnings(PHPMD.CyclomaticComplexity)
 */
class AdminSortCriteria extends BaseAdminSortCriteria implements CriteriaInterface
{
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

        if (!isset($params['sort'])) {
            return $model;
        }

        list($sortType, $orderType) = static::extractParams($params['sort']);

        if ($sortType === 'item_detail_request_count') {
            return $this->applyItemDetailRequestCountSort($model, $orderType);
        }

        if ($sortType === 'item_detail_records') {
            return $model
                ->leftJoin('item_detail_records', 'item_details.id', '=', 'item_detail_records.item_detail_id')
                ->orderBy('item_detail_records.id', $orderType)
                ->select('item_details.*')
                ->distinct();
        }

        if ($sortType === 'addedd_stock_yesterday') {
            return $model
                ->leftJoin('item_detail_records', function (JoinClause $join) {
                    $join->on('item_details.id', '=', 'item_detail_records.item_detail_id')
                        ->whereBetween('item_detail_records.created_at', [
                            date('Y-m-d 00:00:00', strtotime('-1 day')),
                            date('Y-m-d 00:00:00'),
                        ]);
                })
                ->orderBy('item_detail_records.stock', $orderType)
                ->select('item_details.*')
                ->distinct();
        }

        return $model->orderBy($sortType, $orderType);
    }
}

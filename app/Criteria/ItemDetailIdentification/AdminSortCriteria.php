<?php

namespace App\Criteria\ItemDetailIdentification;

use App\Criteria\ItemDetail\BaseAdminSortCriteria;
use App\Database\Utils\Query as QueryUtil;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Query\JoinClause;
use Prettus\Repository\Contracts\CriteriaInterface;
use Prettus\Repository\Contracts\RepositoryInterface;

/**
 * Class AdminSortCriteria.
 *
 * @package namespace App\Criteria\ItemDetailIdentification;
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

        if ($this->hasToJoinWithItemDetail($sortType) && !QueryUtil::joined('item_details', $model)) {
            $model = $model->join('item_details', 'item_detail_identifications.item_detail_id', '=', 'item_details.id');
        }

        if ($sortType === 'item_detail_request_count') {
            return $this->applyItemDetailRequestCountSort($model, $orderType);
        }

        if ($sortType === 'item_detail_records') {
            return $model
                ->leftJoin('item_detail_records', 'item_details.id', '=', 'item_detail_records.item_detail_id')
                ->orderBy('item_detail_identifications.latest_stock_added_at', $orderType)
                ->select('item_detail_identifications.*')
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
                ->orderBy('item_detail_identifications.latest_added_stock', $orderType)
                ->select('item_detail_identifications.*')
                ->distinct();
        }

        if ($sortType === 'item_detail_records') {
            return $model
                ->orderBy('item_detail_records.id', $orderType)
                ->select('item_detail_identifications.*');
        }

        return $model->orderBy($sortType, $orderType);
    }

    /**
     * @param string $sortType
     *
     * @return bool
     */
    private function hasToJoinWithItemDetail($sortType)
    {
        return in_array($sortType, [
            'item_detail_records',
            'addedd_stock_yesterday',
            'item_detail_records',
            'item_detail_request_count',
        ]);
    }
}

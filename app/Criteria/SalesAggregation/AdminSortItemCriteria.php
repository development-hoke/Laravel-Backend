<?php

namespace App\Criteria\SalesAggregation;

use App\Criteria\SortCriteria;
use Illuminate\Database\Eloquent\Builder;
use Prettus\Repository\Contracts\CriteriaInterface;
use Prettus\Repository\Contracts\RepositoryInterface;

/**
 * Class AdminSortItemCriteria.
 *
 * @package namespace App\Criteria\SalesAggregation;
 */
class AdminSortItemCriteria extends SortCriteria implements CriteriaInterface
{
    /**
     * @var array
     */
    protected static $sortTypes = [
        'total_price',
        'total_amount',
    ];

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

        [$sortType, $orderType] = static::extractParams($params['sort']);

        return $model->orderBy($sortType, $orderType);
    }
}

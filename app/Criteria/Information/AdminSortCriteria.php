<?php

namespace App\Criteria\Information;

use App\Criteria\SortCriteria;
use Illuminate\Database\Eloquent\Builder;
use Prettus\Repository\Contracts\CriteriaInterface;
use Prettus\Repository\Contracts\RepositoryInterface;

/**
 * Class AdminSortCriteria.
 *
 * @package namespace App\Criteria\Information;
 */
class AdminSortCriteria extends SortCriteria implements CriteriaInterface
{
    /**
     * @var array
     */
    protected static $sortTypes = [
        'priority',
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

        if (isset($params['status'])) {
            $model = $model->where('status', '=', $params['status']);
        }

        if (!isset($params['sort'])) {
            return $model;
        }

        list($sortType, $orderType) = static::extractParams($params['sort']);

        return $model->orderBy($sortType, $orderType);
    }
}

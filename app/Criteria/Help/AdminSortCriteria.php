<?php

namespace App\Criteria\Help;

use App\Criteria\SortCriteria;
use Illuminate\Database\Eloquent\Builder;
use Prettus\Repository\Contracts\CriteriaInterface;
use Prettus\Repository\Contracts\RepositoryInterface;

/**
 * Class FrontHelpCriteria.
 *
 * @package namespace App\Criteria\Help;
 */
class AdminSortCriteria extends SortCriteria implements CriteriaInterface
{
    /**
     * @var array
     */
    protected static $sortTypes = [
        'good',
        'bad',
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

        list($sortType, $orderType) = static::extractParams($params['sort']);

        return $model->orderBy($sortType, $orderType);
    }
}

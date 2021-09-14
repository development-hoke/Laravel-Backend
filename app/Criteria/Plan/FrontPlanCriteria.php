<?php

namespace App\Criteria\Plan;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Prettus\Repository\Contracts\CriteriaInterface;
use Prettus\Repository\Contracts\RepositoryInterface;

/**
 * Class FrontSearchCriteria.
 *
 * @package namespace App\Criteria;
 */
class FrontPlanCriteria implements CriteriaInterface
{
    /**
     * @var \Illuminate\Http\Request
     */
    protected $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
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
        $columns = ['place', 'store_brand'];
        $params = $this->request->all();

        // Public
        $model = $model->public();

        foreach ($columns as $column) {
            if (!empty($params[$column])) {
                if (is_array($params[$column])) {
                    $model = $model->whereIn($column, $params[$column]);
                } else {
                    $model = $model->where($column, $params[$column]);
                }
            }
        }

        return $model;
    }
}

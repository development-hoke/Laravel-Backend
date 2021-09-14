<?php

namespace App\Criteria\PastItem;

use Illuminate\Database\Eloquent\Builder;
use Prettus\Repository\Contracts\CriteriaInterface;
use Prettus\Repository\Contracts\RepositoryInterface;

/**
 * Class SearchItemCriteria.
 *
 * @package namespace App\Criteria;
 */
class AdminSearchCriteria implements CriteriaInterface
{
    /**
     * @var array
     */
    protected $params;

    /**
     * @param array $params
     */
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
        $columns = ['product_number', 'maker_product_number', 'name', 'old_jan_code'];
        $useLike = array_flip(['name']);

        $params = $this->params;

        foreach ($columns as $column) {
            if (isset($params[$column])) {
                if (is_array($params[$column])) {
                    $model = $model->whereIn($column, $params[$column]);
                } elseif (isset($useLike[$column])) {
                    $model = $model->where($column, 'like', "%{$params[$column]}%");
                } else {
                    $model = $model->where($column, $params[$column]);
                }
            }
        }

        return $model;
    }
}

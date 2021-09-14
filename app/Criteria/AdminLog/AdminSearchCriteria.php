<?php

namespace App\Criteria\AdminLog;

use Prettus\Repository\Contracts\CriteriaInterface;
use Prettus\Repository\Contracts\RepositoryInterface;

/**
 * Class AdminSearchCriteria.
 *
 * @package namespace App\Criteria\AdminLog;
 */
class AdminSearchCriteria implements CriteriaInterface
{
    /**
     * @var array
     */
    private $params;

    public function __construct(array $params)
    {
        $this->params = $params;
    }

    /**
     * Apply criteria in query repository
     *
     * @param mixed $model
     * @param RepositoryInterface $repository
     *
     * @return mixed
     */
    public function apply($model, RepositoryInterface $repository)
    {
        $params = $this->params;

        $columns = ['staff_id', 'action'];

        foreach ($columns as $column) {
            if (isset($params[$column])) {
                $model = $model->whereIn($column, $params[$column]);
            }
        }

        if (isset($params['term_from'])) {
            $model = $model->where('created_at', '>=', $params['term_from']);
        }

        if (isset($params['term_to'])) {
            $model = $model->where('created_at', '<', date('Y-m-d 00:00:00', strtotime("{$params['term_to']} + 1 day")));
        }

        return $model;
    }
}

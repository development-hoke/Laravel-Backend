<?php

namespace App\Criteria\Staff;

use Prettus\Repository\Contracts\CriteriaInterface;
use Prettus\Repository\Contracts\RepositoryInterface;

/**
 * Class AdminSearchCriteria.
 *
 * @package namespace App\Criteria\Staff;
 */
class AdminSearchCriteria implements CriteriaInterface
{
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

        if (isset($params['name'])) {
            $model = $model->where('name', 'like', '%' . $params['name'] . '%');
        }

        if (isset($params['id'])) {
            $model = $model->whereIn('id', (array) $params['id']);
        }

        return $model;
    }
}

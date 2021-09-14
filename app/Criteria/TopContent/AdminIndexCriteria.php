<?php

namespace App\Criteria\TopContent;

use Prettus\Repository\Contracts\CriteriaInterface;
use Prettus\Repository\Contracts\RepositoryInterface;

/**
 * Class AdminIndexCriteria.
 *
 * @package namespace App\Criteria\TopContent;
 */
class AdminIndexCriteria implements CriteriaInterface
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

        if (isset($params['store_brand'])) {
            $model = $model->where('top_contents.store_brand', $params['store_brand']);
        } else {
            $model = $model->whereNull('top_contents.store_brand');
        }

        return $model;
    }
}

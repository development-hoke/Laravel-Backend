<?php

namespace App\Criteria\ItemSort;

use Prettus\Repository\Contracts\CriteriaInterface;
use Prettus\Repository\Contracts\RepositoryInterface;

/**
 * Class AdminIndexCriteria.
 *
 * @package namespace App\Criteria\ItemSort;
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
            $model = $model->where('item_sorts.store_brand', $params['store_brand']);
        } else {
            $model = $model->whereNull('item_sorts.store_brand');
        }

        $model = $model->orderBy('item_sorts.sort');

        return $model;
    }
}

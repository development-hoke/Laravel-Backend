<?php

namespace App\Criteria\Store;

use Prettus\Repository\Contracts\CriteriaInterface;
use Prettus\Repository\Contracts\RepositoryInterface;

/**
 * Class FrontSortCriteria.
 *
 * @package namespace App\Criteria\Store;
 */
class FrontSortCriteria implements CriteriaInterface
{
    /**
     * @var array
     */
    private $params;

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
     * @param mixed $model
     * @param RepositoryInterface $repository
     *
     * @return mixed
     */
    public function apply($model, RepositoryInterface $repository)
    {
        $params = $this->params;

        if (!isset($params['near_loc_lon']) || !isset($params['near_loc_lat'])) {
            return $model;
        }

        $model = $model->orderByDistance([$params['near_loc_lon'], $params['near_loc_lat']]);

        return $model;
    }
}

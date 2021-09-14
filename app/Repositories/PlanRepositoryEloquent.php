<?php

namespace App\Repositories;

use App\Models\Plan;
use App\Repositories\Traits\CopyTrait;
use Prettus\Repository\Criteria\RequestCriteria;

/**
 * Class PlanRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class PlanRepositoryEloquent extends BaseRepositoryEloquent implements PlanRepository
{
    use CopyTrait;

    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return Plan::class;
    }

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }
}

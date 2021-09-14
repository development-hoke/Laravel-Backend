<?php

namespace App\Repositories;

use App\Models\PlanItem;

/**
 * Class PlanItemRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class PlanItemRepositoryEloquent extends BaseRepositoryEloquent implements PlanItemRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return PlanItem::class;
    }

    /**
     * Boot up the repository
     */
    public function boot()
    {
    }
}

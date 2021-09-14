<?php

namespace App\Repositories;

use App\Models\DepartmentGroup;

/**
 * Class DepartmentGroupRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class DepartmentGroupRepositoryEloquent extends BaseRepositoryEloquent implements DepartmentGroupRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return DepartmentGroup::class;
    }

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
    }
}

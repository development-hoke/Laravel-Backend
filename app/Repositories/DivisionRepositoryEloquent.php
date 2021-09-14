<?php

namespace App\Repositories;

use App\Models\Division;

/**
 * Class DivisionRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class DivisionRepositoryEloquent extends BaseRepositoryEloquent implements DivisionRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return Division::class;
    }

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
    }
}

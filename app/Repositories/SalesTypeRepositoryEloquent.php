<?php

namespace App\Repositories;

use App\Models\SalesType;

/**
 * Class SalesTypeRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class SalesTypeRepositoryEloquent extends BaseRepositoryEloquent implements SalesTypeRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return SalesType::class;
    }

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
    }
}

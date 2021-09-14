<?php

namespace App\Repositories;

use App\Models\Size;

/**
 * Class SizeRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class SizeRepositoryEloquent extends BaseRepositoryEloquent implements SizeRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return Size::class;
    }

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
    }
}

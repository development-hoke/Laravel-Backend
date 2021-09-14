<?php

namespace App\Repositories;

use App\Models\Brand;

/**
 * Class BrandRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class BrandRepositoryEloquent extends BaseRepositoryEloquent implements BrandRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return Brand::class;
    }

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
    }
}

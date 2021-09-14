<?php

namespace App\Repositories;

use App\Models\SeasonGroup;

/**
 * Class SeasonGroupRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class SeasonGroupRepositoryEloquent extends BaseRepositoryEloquent implements SeasonGroupRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return SeasonGroup::class;
    }

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
    }
}

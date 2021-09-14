<?php

namespace App\Repositories;

use App\Models\Season;

/**
 * Class SeasonRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class SeasonRepositoryEloquent extends BaseRepositoryEloquent implements SeasonRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return Season::class;
    }

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
    }
}

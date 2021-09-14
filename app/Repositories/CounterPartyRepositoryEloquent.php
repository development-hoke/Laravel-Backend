<?php

namespace App\Repositories;

use App\Models\CounterParty;

/**
 * Class CounterPartyRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class CounterPartyRepositoryEloquent extends BaseRepositoryEloquent implements CounterPartyRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return CounterParty::class;
    }

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
    }
}

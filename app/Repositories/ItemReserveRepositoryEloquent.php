<?php

namespace App\Repositories;

use App\Models\ItemReserve;

/**
 * Class ItemReserveRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class ItemReserveRepositoryEloquent extends BaseRepositoryEloquent implements ItemReserveRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return ItemReserve::class;
    }

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
    }
}

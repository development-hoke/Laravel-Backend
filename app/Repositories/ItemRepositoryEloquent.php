<?php

namespace App\Repositories;

use App\Models\Item;

/**
 * Class ItemRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class ItemRepositoryEloquent extends BaseRepositoryEloquent implements ItemRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return Item::class;
    }

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
    }
}

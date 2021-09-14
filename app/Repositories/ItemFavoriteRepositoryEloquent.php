<?php

namespace App\Repositories;

use App\Models\ItemFavorite;

/**
 * Class ItemFavoriteRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class ItemFavoriteRepositoryEloquent extends BaseRepositoryEloquent implements ItemFavoriteRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return ItemFavorite::class;
    }

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
    }
}

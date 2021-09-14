<?php

namespace App\Repositories;

use App\Models\PastItem;

/**
 * Class ItemRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class PastItemRepositoryEloquent extends BaseRepositoryEloquent implements PastItemRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return PastItem::class;
    }

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
    }
}

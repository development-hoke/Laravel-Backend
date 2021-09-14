<?php

namespace App\Repositories;

use App\Models\ItemDetailStore;

/**
 * Class ItemDetailStoreRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 *
 * @SuppressWarnings(PHPMD.LongClassName)
 */
class ItemDetailStoreRepositoryEloquent extends BaseRepositoryEloquent implements ItemDetailStoreRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return ItemDetailStore::class;
    }

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
    }
}

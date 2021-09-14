<?php

namespace App\Repositories;

use App\Models\ItemSalesTypes;
use App\Repositories\Traits\DeleteAndInsertBatchTrait;

/**
 * Class ItemSalesTypesRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class ItemSalesTypesRepositoryEloquent extends BaseRepositoryEloquent implements ItemSalesTypesRepository
{
    use DeleteAndInsertBatchTrait;

    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return ItemSalesTypes::class;
    }

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
    }
}

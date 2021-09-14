<?php

namespace App\Repositories;

use App\Models\EventBundleSale;
use App\Repositories\Traits\DeleteAndInsertBatchTrait;

/**
 * Class EventBundleSaleRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class EventBundleSaleRepositoryEloquent extends BaseRepositoryEloquent implements EventBundleSaleRepository
{
    use DeleteAndInsertBatchTrait;

    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return EventBundleSale::class;
    }

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
    }
}

<?php

namespace App\Repositories;

use App\Models\OrderChangeHistory;

/**
 * Class OrderChangeHistoryRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class OrderChangeHistoryRepositoryEloquent extends BaseRepositoryEloquent implements OrderChangeHistoryRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return OrderChangeHistory::class;
    }

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
    }
}

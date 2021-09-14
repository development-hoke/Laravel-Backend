<?php

namespace App\Repositories;

use App\Models\OrderDetail;

/**
 * Class OrderDetailRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class OrderDetailRepositoryEloquent extends BaseRepositoryEloquent implements OrderDetailRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return OrderDetail::class;
    }

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
    }
}

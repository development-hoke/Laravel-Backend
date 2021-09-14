<?php

namespace App\Repositories;

use App\Models\OrderDiscount;

/**
 * Class OrderDiscountRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class OrderDiscountRepositoryEloquent extends BaseRepositoryEloquent implements OrderDiscountRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return OrderDiscount::class;
    }

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
    }
}

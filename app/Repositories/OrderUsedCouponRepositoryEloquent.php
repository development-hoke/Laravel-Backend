<?php

namespace App\Repositories;

use App\Models\OrderUsedCoupon;
use App\Repositories\Traits\DeleteAndInsertBatchTrait;

/**
 * Class OrderUsedCouponRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class OrderUsedCouponRepositoryEloquent extends BaseRepositoryEloquent implements OrderUsedCouponRepository
{
    use DeleteAndInsertBatchTrait;

    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return OrderUsedCoupon::class;
    }

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
    }
}

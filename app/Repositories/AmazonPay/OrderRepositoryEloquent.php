<?php

namespace App\Repositories\AmazonPay;

use App\Models\AmazonPayOrder;
use App\Repositories\Traits\QueryBuilderMethodTrait;

/**
 * Class OrderRepositoryEloquent.
 *
 * @package namespace App\Repositories\AmazonPay;
 */
class OrderRepositoryEloquent extends BaseRepositoryEloquent implements OrderRepository
{
    use QueryBuilderMethodTrait;

    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return AmazonPayOrder::class;
    }

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
    }
}

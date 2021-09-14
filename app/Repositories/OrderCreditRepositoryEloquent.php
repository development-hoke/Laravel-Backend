<?php

namespace App\Repositories;

use App\Models\OrderCredit;
use App\Repositories\Traits\QueryBuilderMethodTrait;

/**
 * Class OrderCreditRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class OrderCreditRepositoryEloquent extends BaseRepositoryEloquent implements OrderCreditRepository
{
    use QueryBuilderMethodTrait;

    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return OrderCredit::class;
    }

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
    }

    /**
     * @param int $orderId
     *
     * @return \App\Models\OrderCredit
     */
    public function findByOrderId(int $orderId)
    {
        return $this->findOrFail(['order_id' => $orderId]);
    }
}

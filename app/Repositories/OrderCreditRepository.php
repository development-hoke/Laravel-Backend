<?php

namespace App\Repositories;

use Prettus\Repository\Contracts\RepositoryInterface;

/**
 * Interface OrderCreditRepository
 *
 * @package App\Repositories
 */
interface OrderCreditRepository extends RepositoryInterface
{
    /**
     * @param int $orderId
     *
     * @return \App\Models\OrderCredit
     */
    public function findByOrderId(int $orderId);
}

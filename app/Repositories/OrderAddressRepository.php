<?php

namespace App\Repositories;

use Prettus\Repository\Contracts\RepositoryInterface;

/**
 * Interface OrderAddressRepository.
 *
 * @package namespace App\Repositories;
 */
interface OrderAddressRepository extends RepositoryInterface
{
    /**
     * @param int $orderId
     *
     * @return OrderAddress
     */
    public function findDeliveryAddress($orderId);

    /**
     * @param int $orderId
     *
     * @return OrderAddress
     */
    public function findBillingAddress($orderId);
}

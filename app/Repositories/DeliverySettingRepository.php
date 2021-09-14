<?php

namespace App\Repositories;

use Prettus\Repository\Contracts\RepositoryInterface;

/**
 * Interface DeliverySettingRepository.
 *
 * @package namespace App\Repositories;
 */
interface DeliverySettingRepository extends RepositoryInterface
{
    /**
     * @return int
     */
    public static function getDefaultDeliveryFee();
}

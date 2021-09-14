<?php

namespace App\Enums\Order;

use App\Enums\BaseEnum;

/**
 * @method static static ReadyForDelivery()
 * @method static static Deliveryed()
 */
final class DeliveryStatus extends BaseEnum
{
    const ReadyForDelivery = 1;
    const Deliveryed = 2;
}

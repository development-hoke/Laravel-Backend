<?php

namespace App\Enums\Order;

use App\Enums\BaseEnum;

/**
 * @method static static Ordered()
 * @method static static Arrived()
 * @method static static ReadyForDelivery()
 * @method static static Deliveryed()
 * @method static static Pending()
 * @method static static Provided()
 * @method static static Canceled()
 * @method static static Returned()
 * @method static static Changed()
 */
final class Status extends BaseEnum
{
    const Ordered = 1;
    const Arrived = 2;
    const ReadyForDelivery = 3;
    const Deliveryed = 4;
    const Pending = 5;
    const Provided = 6;
    const Canceled = 7;
    const Returned = 8;
    const Changed = 99;
}

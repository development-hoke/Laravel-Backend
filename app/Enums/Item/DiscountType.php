<?php

namespace App\Enums\Item;

use App\Enums\BaseEnum;

/**
 * @method static static None()
 * @method static static Normal()
 * @method static static Member()
 * @method static static Event()
 * @method static static Staff()
 */
final class DiscountType extends BaseEnum
{
    const None = 1;
    const Normal = 2;
    const Member = 3;
    const Event = 4;
    const Staff = 5;
    const Reservation = 6;
}

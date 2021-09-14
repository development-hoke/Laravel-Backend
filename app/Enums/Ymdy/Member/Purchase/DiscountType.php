<?php

namespace App\Enums\Ymdy\Member\Purchase;

use App\Enums\BaseEnum;

/**
 * @method static static EventSale()
 * @method static static Member()
 * @method static static Normal()
 * @method static static Staff()
 * @method static static EventBundle()
 * @method static static Coupon()
 * @method static static Reservation()
 */
final class DiscountType extends BaseEnum
{
    const EventSale = 1;
    const Member = 2;
    const Normal = 3;
    const Staff = 4;
    const EventBundle = 5;
    const Coupon = 6;
    const Reservation = 7;
}

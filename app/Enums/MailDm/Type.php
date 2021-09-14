<?php

namespace App\Enums\MailDm;

use App\Enums\BaseEnum;

/**
 * @method static static Receive()
 * @method static static OnlyEventCoupon()
 * @method static static NotReceive()
 */
final class Type extends BaseEnum
{
    const Receive = 1;
    const OnlyEventCoupon = 2;
    const NotReceive = 3;
}

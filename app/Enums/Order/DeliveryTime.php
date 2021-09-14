<?php

namespace App\Enums\Order;

use App\Enums\BaseEnum;

/**
 * @method static static Am()
 * @method static static Time1()
 * @method static static Time2()
 * @method static static Time3()
 * @method static static Time4()
 */
final class DeliveryTime extends BaseEnum
{
    const Am = 1;
    const Time1 = 2;
    const Time2 = 3;
    const Time3 = 4;
    const Time4 = 5;
}

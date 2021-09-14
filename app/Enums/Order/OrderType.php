<?php

namespace App\Enums\Order;

use App\Enums\BaseEnum;

/**
 * @method static static Normal()
 * @method static static Reserve()
 */
final class OrderType extends BaseEnum
{
    const Normal = 1;
    const Reserve = 2;
    const BackOrder = 3;
}

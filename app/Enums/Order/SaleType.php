<?php

namespace App\Enums\Order;

use App\Enums\BaseEnum;

/**
 * @method static static Sale()
 * @method static static Employee()
 */
final class SaleType extends BaseEnum
{
    const Sale = 1;
    const Employee = 2;
}

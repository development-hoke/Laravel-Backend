<?php

namespace App\Enums\ItemDetail;

use App\Enums\BaseEnum;

/**
 * @method static static GreatorThanOrEqual14()
 * @method static static GreatorThanOrEqual30()
 */
final class DeadInventoryDayType extends BaseEnum
{
    const GreatorThanOrEqual14 = 1;
    const GreatorThanOrEqual30 = 2;
}

<?php

namespace App\Enums\OrderDiscount;

use App\Enums\BaseEnum;

/**
 * @method static static Fixed()
 * @method static static Percentile()
 * @method static static Point()
 */
final class Method extends BaseEnum
{
    const Fixed = 1; // 定額
    const Percentile = 2; // 定率
}

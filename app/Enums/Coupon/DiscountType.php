<?php

namespace App\Enums\Coupon;

use App\Enums\BaseEnum;

/**
 * Class Type
 *
 * @package App\Enums\Coupon
 */
final class DiscountType extends BaseEnum
{
    const Fixed = 1; // 定額
    const Percentile = 2; // 定率
}

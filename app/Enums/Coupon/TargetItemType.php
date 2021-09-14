<?php

namespace App\Enums\Coupon;

use App\Enums\BaseEnum;

/**
 * Class Type
 *
 * @package App\Enums\Coupon
 */
final class TargetItemType extends BaseEnum
{
    const All = 1; // 全商品対象
    const Specified = 2; // 商品番号指定
}

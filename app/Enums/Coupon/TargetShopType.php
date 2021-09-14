<?php

namespace App\Enums\Coupon;

use App\Enums\BaseEnum;

/**
 * Class Type
 *
 * @package App\Enums\Coupon
 */
final class TargetShopType extends BaseEnum
{
    const All = 1; // 全店舗対象
    const Specified = 2; // :店舗指定
}

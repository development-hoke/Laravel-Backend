<?php

namespace App\Enums\Coupon;

use App\Enums\BaseEnum;

/**
 * Class Type
 *
 * @package App\Enums\Coupon
 */
final class DiscountItemFlag extends BaseEnum
{
    const DeliveryFeeFree = 0; // 送料無料
    const ItemDiscount = 1; // 商品値引き
}

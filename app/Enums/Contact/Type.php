<?php

namespace App\Enums\Contact;

use App\Enums\BaseEnum;

/**
 * Class Type
 *
 * @package App\Enums\Coupon
 */
final class Type extends BaseEnum
{
    const Membership = 1;
    const Product = 2;
    const ProductStock = 3;
    const Order = 4;
    const Shipping = 5;
    const Return = 6;
    const Other = 7;
}

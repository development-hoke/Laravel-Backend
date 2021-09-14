<?php

namespace App\Enums\Event;

use App\Enums\BaseEnum;

/**
 * @method static static Flat()
 * @method static static EachProduct()
 */
final class DiscountType extends BaseEnum
{
    const Flat = 1;
    const EachProduct = 2;
}

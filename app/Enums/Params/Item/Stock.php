<?php

namespace App\Enums\Params\Item;

use App\Enums\BaseEnum;

/**
 * @method static static Zero()
 * @method static static One()
 * @method static static TwoOrMore()
 * @method static static TenOrMore()
 */
final class Stock extends BaseEnum
{
    const Zero = 0;
    const One = 1;
    const TwoOrMore = 2;
    const TenOrMore = 10;
}

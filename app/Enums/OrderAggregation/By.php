<?php

namespace App\Enums\OrderAggregation;

use App\Enums\BaseEnum;

/**
 * @method static static Ordered()
 * @method static static Delivered()
 */
final class By extends BaseEnum
{
    const Ordered = 1;
    const Delivered = 2;
}

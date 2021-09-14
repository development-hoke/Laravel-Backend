<?php

namespace App\Enums\Item;

use App\Enums\BaseEnum;

/**
 * @method static static InStoreNow()
 * @method static static Stop()
 * @method static static SoldOut()
 */
final class SalesStatus extends BaseEnum
{
    const InStoreNow = 1;
    const Stop = 2;
    const SoldOut = 3;
}

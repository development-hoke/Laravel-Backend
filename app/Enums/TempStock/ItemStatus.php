<?php

namespace App\Enums\TempStock;

use App\Enums\BaseEnum;

/**
 * Class ItemStatus
 *
 * @package App\Enums\Store
 */
final class ItemStatus extends BaseEnum
{
    const Stock = 1;
    const OnSale = 2;
    const Sold = 3;
    const StoreStock = 4;
    const Reserve = 5;
}

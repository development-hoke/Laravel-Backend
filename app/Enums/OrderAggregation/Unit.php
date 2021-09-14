<?php

namespace App\Enums\OrderAggregation;

use App\Enums\BaseEnum;

/**
 * @method static static Monthly()
 * @method static static Weekly()
 * @method static static Daily()
 */
final class Unit extends BaseEnum
{
    const Daily = 1;
    const Weekly = 2;
    const Monthly = 3;
}

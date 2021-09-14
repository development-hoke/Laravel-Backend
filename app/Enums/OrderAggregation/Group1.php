<?php

namespace App\Enums\OrderAggregation;

use App\Enums\BaseEnum;

/**
 * @method static static Organization()
 * @method static static Division()
 * @method static static MainStoreBrand()
 */
final class Group1 extends BaseEnum
{
    const Organization = 1;
    const Division = 2;
    const MainStoreBrand = 3;
}

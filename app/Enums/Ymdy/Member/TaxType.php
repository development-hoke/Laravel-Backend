<?php

namespace App\Enums\Ymdy\Member;

use App\Enums\BaseEnum;

/**
 * @method static static Excluded()
 * @method static static Included()
 * @method static static None()
 */
final class TaxType extends BaseEnum
{
    const Excluded = 1;
    const Included = 2;
    const None = 3;
}

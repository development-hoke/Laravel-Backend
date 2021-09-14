<?php

namespace App\Enums\Event;

use App\Enums\BaseEnum;

/**
 * @method static static Normal()
 * @method static static Bundle()
 */
final class SaleType extends BaseEnum
{
    const Normal = 1;
    const Bundle = 2;
}

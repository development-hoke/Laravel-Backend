<?php

namespace App\Enums\Common;

use App\Enums\BaseEnum;

/**
 * @method static static Unpublished()
 * @method static static Published()
 */
final class Status extends BaseEnum
{
    const Unpublished = 0;
    const Published = 1;
}

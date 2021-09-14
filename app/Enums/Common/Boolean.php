<?php

namespace App\Enums\Common;

use App\Enums\BaseEnum;

/**
 * @method static static IsTrue()
 * @method static static IsFalse()
 */
final class Boolean extends BaseEnum
{
    const IsTrue = 1;
    const IsFalse = 0;
}

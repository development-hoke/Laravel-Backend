<?php

namespace App\Enums\Params;

use App\Enums\BaseEnum;

/**
 * @method static static Count50()
 * @method static static Count100()
 * @method static static Count200()
 */
final class PerPage extends BaseEnum
{
    const Count50 = 50;
    const Count100 = 100;
    const Count200 = 200;
}

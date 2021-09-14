<?php

namespace App\Enums\Event;

use App\Enums\BaseEnum;

/**
 * @method static static Employee()
 * @method static static Sale()
 */
final class Target extends BaseEnum
{
    const Employee = 1;
    const Sale = 2;
}

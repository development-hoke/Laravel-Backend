<?php

namespace App\Enums\PostDm;

use App\Enums\BaseEnum;

/**
 * @method static static Receive()
 * @method static static NotReceive()
 */
final class Type extends BaseEnum
{
    const Receive = 1;
    const NotReceive = 0;
}

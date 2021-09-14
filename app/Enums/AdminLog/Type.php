<?php

namespace App\Enums\AdminLog;

use App\Enums\BaseEnum;

/**
 * @method static static Read()
 * @method static static Create()
 * @method static static Update()
 * @method static static Delete()
 */
final class Type extends BaseEnum
{
    const Read = 1;
    const Create = 2;
    const Update = 3;
    const Delete = 4;
}

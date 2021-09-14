<?php

namespace App\Enums\OrderLog;

use App\Enums\BaseEnum;

/**
 * @method static static Create()
 * @method static static Change()
 */
final class AddressEventType extends BaseEnum
{
    const Create = 1;
    const Change = 2;
}

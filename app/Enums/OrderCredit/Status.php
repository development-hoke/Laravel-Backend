<?php

namespace App\Enums\OrderCredit;

use App\Enums\BaseEnum;

/**
 * @method static static Rate10()
 */
final class Status extends BaseEnum
{
    const Authorized = 1;
    const Captured = 2;
    const Canceled = 3;
    const Expired = 4;
}

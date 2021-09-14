<?php

namespace App\Enums\MemberCreditCard;

use App\Enums\BaseEnum;

/**
 * @method static static Default()
 * @method static static Keep()
 */
final class Priority extends BaseEnum
{
    const Default = 1;
    const Keep = 100;
}

<?php

namespace App\Enums\OrderAddress;

use App\Enums\BaseEnum;

/**
 * Class Type
 *
 * @package App\Enums\OrderAddress
 */
final class Type extends BaseEnum
{
    const Member = 1;
    const Delivery = 2;
    const Bill = 3;
}

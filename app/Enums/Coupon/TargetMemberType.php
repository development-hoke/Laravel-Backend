<?php

namespace App\Enums\Coupon;

use App\Enums\BaseEnum;

/**
 * Class TargetMemberType
 *
 * @package App\Enums\Coupon
 */
final class TargetMemberType extends BaseEnum
{
    const All = 1;
    const Group = 2;
    const Member = 3;
}

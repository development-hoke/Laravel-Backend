<?php

namespace App\Enums\Event;

use App\Enums\BaseEnum;

/**
 * @method static static All()
 * @method static static MemberOnly()
 * @method static static Limit()
 */
final class TargetUserType extends BaseEnum
{
    const All = 1;
    const MemberOnly = 2;
    const Limit = 3;
}

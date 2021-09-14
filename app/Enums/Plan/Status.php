<?php

namespace App\Enums\Plan;

use App\Enums\BaseEnum;

/**
 * @method static static Unpublished()
 * @method static static Published()
 */
final class Status extends BaseEnum
{
    const Unpublished = 0;
    const Published = 1;
    const WaitingPublish = 2;
    const FinishPublish = 3;
}

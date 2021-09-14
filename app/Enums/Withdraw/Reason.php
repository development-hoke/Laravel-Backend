<?php

namespace App\Enums\Withdraw;

use App\Enums\BaseEnum;

/**
 * @method static static NoGoods()
 * @method static static NoPoints()
 * @method static static NoService()
 * @method static static NoSupport()
 * @method static static NoFunction()
 * @method static static NoInformation()
 */
final class Reason extends BaseEnum
{
    const NoGoods = 1;
    const NoPoints = 2;
    const NoService = 3;
    const NoSupport = 4;
    const NoFunction = 5;
    const NoInformation = 6;
}

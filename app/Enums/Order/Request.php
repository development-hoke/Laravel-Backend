<?php

namespace App\Enums\Order;

use App\Enums\BaseEnum;

/**
 * Class Request
 *
 * @package App\Enums\Order
 */
final class Request extends BaseEnum
{
    const Package = 1;
    const BoxDuringAbsence = 2;
    const Box = 3;
    const Tel = 4;
    const SalesOffice = 5;
    const DesignatedDate = 6;
    const Weekdays = 7;
    const WeekendAndHoliday = 8;
    const Present = 9;
}

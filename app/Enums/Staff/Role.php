<?php

namespace App\Enums\Staff;

use App\Enums\BaseEnum;

/**
 * @method static static Privilege()
 * @method static static OperationManager()
 * @method static static CSStaff()
 * @method static static OperationStaff()
 * @method static static DeliveryStaff()
 * @method static static CreativeStaff()
 * @method static static Outside()
 */
final class Role extends BaseEnum
{
    const Privilege = 1;
    const OperationManager = 2;
    const CSStaff = 3;
    const OperationStaff = 4;
    const DeliveryStaff = 5;
    const CreativeStaff = 6;
    const Outside = 99;
}

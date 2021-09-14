<?php

namespace App\Enums\OrderLog;

use App\Enums\BaseEnum;

/**
 * @method static static Create()
 * @method static static AddCoupon()
 * @method static static RemoveCoupon()
 * @method static static AddItem()
 * @method static static CancelItem()
 * @method static static ChangePrice()
 * @method static static OtherChange()
 */
final class EventType extends BaseEnum
{
    const Create = 1;

    const AddCoupon = 11;
    const RemoveCoupon = 12;

    const AddItem = 21;
    const CancelItem = 22;

    const ChangePrice = 91;
    const OtherChange = 92;
}

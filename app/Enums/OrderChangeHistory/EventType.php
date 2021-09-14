<?php

namespace App\Enums\OrderChangeHistory;

use App\Enums\BaseEnum;

/**
 * @method static static AddItem()
 * @method static static RemoveItem()
 * @method static static AddCoupon()
 * @method static static RemoveCoupon()
 * @method static static ChangePrice()
 */
final class EventType extends BaseEnum
{
    const AddItem = 1;
    const RemoveItem = 2;
    const AddCoupon = 3;
    const RemoveCoupon = 4;
    const ChangePrice = 5;
    const RecalculateBundleSale = 6;
    const ReturnItem = 7;
}

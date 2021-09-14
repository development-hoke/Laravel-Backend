<?php

namespace App\Enums\Common;

use App\Enums\BaseEnum;

/**
 * @method static static NoValidCartItem()
 */
final class ErrorCode extends BaseEnum
{
    const NoValidCartItem = 'NoValidCartItem';
    const EmailAlreadyInUse = 'EmailAlreadyInUse';
    const EcStockShortageButAvailableBackOrder = 'EcStockShortageButAvailableBackOrder';
    const StockShortage = 'StockShortage';
}

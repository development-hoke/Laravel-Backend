<?php

namespace App\Enums\Item;

use App\Enums\BaseEnum;

/**
 * @method static static Normal()
 * @method static static Reserve()
 */
final class ReserveStatus extends BaseEnum
{
    const Normal = 0;
    const Reserve = 1;
}

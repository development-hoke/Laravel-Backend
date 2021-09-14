<?php

namespace App\Enums\ItemBulkUpload;

use App\Enums\BaseEnum;

/**
 * @method static static Processing()
 * @method static static Complete()
 */
final class Status extends BaseEnum
{
    const Processing = 1;
    const Complete = 2;
}

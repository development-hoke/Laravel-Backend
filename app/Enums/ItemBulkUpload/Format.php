<?php

namespace App\Enums\ItemBulkUpload;

use App\Enums\BaseEnum;

/**
 * @method static static Image()
 * @method static static Item()
 */
final class Format extends BaseEnum
{
    const Image = 1;
    const Item = 2;
}

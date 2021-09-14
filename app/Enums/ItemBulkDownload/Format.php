<?php

namespace App\Enums\ItemBulkDownload;

use App\Enums\BaseEnum;

/**
 * @method static static Image()
 * @method static static Item()
 */
final class Format extends BaseEnum
{
    const All = 1;
    const Image = 2;
    const Info = 3;
}

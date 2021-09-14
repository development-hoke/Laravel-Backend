<?php

namespace App\Entities\Ymdy\Shohin;

use App\Entities\Entity;

/**
 * @property int $item_status_id \App\Enums\TempStock\ItemStatus
 * @property int $shop_id
 * @property string $code2241
 * @property string $code
 */
class Item extends Entity
{
    protected $prefix = 'Item';
}

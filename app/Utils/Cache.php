<?php

namespace App\Utils;

use Illuminate\Support\Facades\Cache as BaseCache;

class Cache extends BaseCache
{
    const KEY_ADMIN_ITEM_DETAIL_PREVIW = 'admin_item_detail_previw:%s';
    const KEY_ADMIN_INFORMATION_PREVIW = 'admin_information_previw:%s';
    const KEY_STORE_ID = 'store_id:%s';
    const KEY_ORDER_CODE = 'order_code:%s';
    const KEY_ORDER_DELIVERED_LOCK = 'order_delivered_lock:%s';
}

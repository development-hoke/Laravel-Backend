<?php

return [
    'delete_item_preview_images' => env('DELETE_ITEM_PREVIEW_IMAGES', '0 0 */1 * *'),
    'delete_expired_plans' => env('DELETE_EXPIRED_PLANS', '0 * * * *'),
    'sync_color_master' => env('SYNC_COLOR_MASTER', '0 3 * * *'),
    'sync_division_master' => env('SYNC_DIVISION_MASTER', '5 3 * * *'),
    'sync_department_master' => env('SYNC_DEPARTMENT_MASTER', '10 3 * * *'),
    'sync_store_master' => env('SYNC_STORE_MASTER', '15 3 * * *'),
    'sync_season_master' => env('SYNC_SEASON_MASTER', '20 3 * * *'),
    'sync_size_master' => env('SYNC_SIZE_MASTER', '25 3 * * *'),
    'sync_counter_party_master' => env('SYNC_COUNTER_PARTY_MASTER', '30 3 * * *'),
    'sync_item' => env('SYNC_ITEM', '2 */6 * * *'),
    'sync_temp_stock' => env('SYNC_TEMP_STOCK', '23 */6 * * *'),
    'sync_stock' => env('SYNC_STOCK', '45 */6 * * *'),
    'sync_dead_inventory' => env('SYNC_DEAD_INVENTORY', '10 8,18 * * *'),
    'sync_slow_moving_inventory' => env('SYNC_SLOW_MOVING_INVENTORY', '40 8,18 * * *'),
    'sync_low_inventory' => env('SYNC_LOW_INVENTORY', '0 */12 * * *'),
];

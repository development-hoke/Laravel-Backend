<?php

namespace App\Models;

use App\Models\Traits\BulkInsertTrait;

class TempStock extends Model
{
    use BulkInsertTrait;

    protected $fillable = [
        'store_id',
        'jan_code',
        'item_status_id',
        'imported',
        'imported_store_stock',
        'imported_store_stock_by_jan',
    ];

    protected $casts = [
        'imported' => 'boolean',
        'imported_store_stock' => 'boolean',
        'imported_store_stock_by_jan' => 'boolean',
    ];
}

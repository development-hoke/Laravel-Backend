<?php

namespace App\Models;

class ItemSalesTypes extends Model
{
    protected $fillable = [
        'item_id',
        'sales_type_id',
        'sort',
    ];
}

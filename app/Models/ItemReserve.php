<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class ItemReserve extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'item_id',
        'is_enable',
        'period_from',
        'period_to',
        'reserve_price',
        'is_free_delivery',
        'limited_stock_threshold',
        'out_of_stock_threshold',
        'expected_arrival_date',
        'note',
    ];

    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class);
    }
}

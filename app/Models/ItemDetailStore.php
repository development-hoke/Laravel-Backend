<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ItemDetailStore extends Model
{
    protected $fillable = [
        'item_detail_id',
        'store_id',
        'stock',
    ];

    /**
     * @return BelongsTo
     */
    public function itemDetail(): BelongsTo
    {
        return $this->belongsTo(ItemDetail::class);
    }
}

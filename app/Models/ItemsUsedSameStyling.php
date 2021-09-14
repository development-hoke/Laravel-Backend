<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ItemsUsedSameStyling extends Model
{
    protected $fillable = [
        'item_id',
        'used_item_id',
    ];

    /**
     * @return BelongsTo
     */
    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class, 'used_item_id');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ItemFavorite extends Model
{
    protected $fillable = [
        'item_id',
        'member_id',
    ];

    /**
     * @return BelongsTo
     */
    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class);
    }
}

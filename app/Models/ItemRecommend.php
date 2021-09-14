<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ItemRecommend extends Model
{
    protected $fillable = [
        'item_id',
        'recommend_item_id',
    ];

    /**
     * @return BelongsTo
     */
    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class, 'recommend_item_id');
    }
}

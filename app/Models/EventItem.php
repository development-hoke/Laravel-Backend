<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EventItem extends Model
{
    protected $fillable = [
        'event_id',
        'item_id',
        'discount_rate',
    ];

    /**
     * @return BelongsTo
     */
    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class);
    }

    /**
     * @return BelongsTo
     */
    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    /**
     * イベントIDとproduct_numberを条件に加える
     *
     * @param Builder $query
     * @param int $eventId
     * @param mix $productNumber
     *
     * @return Builder
     */
    public function scopeWhereEventIdAndProductNumber(Builder $query, int $eventId, $productNumber): Builder
    {
        return $query->join('items', function ($query) use ($eventId, $productNumber) {
            return $query->on('event_items.item_id', '=', 'items.id')
                ->where('event_items.event_id', $eventId)
                ->where('items.product_number', $productNumber);
        });
    }
}

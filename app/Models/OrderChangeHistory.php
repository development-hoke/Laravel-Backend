<?php

namespace App\Models;

use App\Models\Collections\OrderChangeHistoryCollection;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class OrderChangeHistory extends Model
{
    protected $fillable = [
        'order_id',
        'log_type',
        'log_id',
        'staff_id',
        'event_type',
        'diff_json',
        'memo',
    ];

    protected $casts = [
        'diff_json' => 'json',
    ];

    private $itemEventTypes = [
        \App\Enums\OrderChangeHistory\EventType::AddItem,
        \App\Enums\OrderChangeHistory\EventType::RemoveItem,
        \App\Enums\OrderChangeHistory\EventType::ReturnItem,
        \App\Enums\OrderChangeHistory\EventType::RecalculateBundleSale,
    ];

    /**
     * Create a new Eloquent Collection instance.
     *
     * @param array $models
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function newCollection(array $models = [])
    {
        return new OrderChangeHistoryCollection($models);
    }

    /**
     * @return bool|null
     */
    public function getIsItemEventAttribute()
    {
        if (is_null($this->event_type)) {
            return null;
        }

        return in_array($this->event_type, $this->itemEventTypes, true);
    }

    /**
     * @return bool|int|null
     */
    public function getItemDetailIdAttribute()
    {
        if (is_null($this->is_item_event)) {
            return null;
        }

        if (!$this->is_item_event) {
            return false;
        }

        return $this->diff_json['item_detail_id'] ?? null;
    }

    /**
     * @return BelongsTo
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * @return MorphTo
     */
    public function log(): MorphTo
    {
        return $this->morphTo('log');
    }

    /**
     * @return BelongsTo
     */
    public function staff(): BelongsTo
    {
        return $this->belongsTo(Staff::class);
    }
}

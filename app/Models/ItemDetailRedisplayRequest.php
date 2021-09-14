<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class ItemDetailRedisplayRequest extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'item_detail_id',
        'member_id',
        'user_token',
        'user_name',
        'email',
        'is_notified',
    ];

    /**
     * @return BelongsTo
     */
    public function itemDetail(): BelongsTo
    {
        return $this->belongsTo(ItemDetail::class);
    }

    /**
     * 送信対象を絞り込み
     *
     * @param Builder $query
     * @param int $itemDetailId
     *
     * @return Builder
     */
    public function scopeSendTarget($query, int $itemDetailId)
    {
        return $query
            ->where('is_notified', false)
            ->where('item_detail_id', $itemDetailId);
    }
}

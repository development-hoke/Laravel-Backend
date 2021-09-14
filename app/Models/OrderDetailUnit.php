<?php

namespace App\Models;

use App\Models\Contracts\Loggable;
use App\Models\Traits\Logging;
use App\Models\Traits\OrderSoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class OrderDetailUnit extends Model implements Loggable
{
    use Logging;
    use OrderSoftDeletes;

    protected $fillable = [
        'order_detail_id',
        'item_detail_identification_id',
        'amount',
        'tax',
        'update_staff_id',
    ];

    protected $dispatchesEvents = [
        'saved' => \App\Events\Model\SavedOrderDetailUnit::class,
    ];

    /**
     * @return HasMany
     */
    public function orderDetailUnitLogs(): HasMany
    {
        return $this->hasMany(OrderDetailUnitLog::class);
    }

    /**
     * @return BelongsTo
     */
    public function itemDetailIdentification(): BelongsTo
    {
        return $this->belongsTo(ItemDetailIdentification::class);
    }
}

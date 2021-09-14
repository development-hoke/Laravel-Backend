<?php

namespace App\Models;

use App\Exceptions\FatalException;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class RefundDetail extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'order_id',
        'refundable_type',
        'refundable_id',
        'unit_price',
        'amount',
        'update_staff_id',
    ];

    /**
     * @return int
     */
    public function getPriceAttribute()
    {
        return $this->unit_price * $this->amount;
    }

    public function setPriceAttribute()
    {
        throw new FatalException(__('error.not_allowed_to_set'));
    }

    /**
     * @return MorphTo
     */
    public function refundable(): MorphTo
    {
        return $this->morphTo();
    }
}

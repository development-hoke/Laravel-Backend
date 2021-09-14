<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class OrderNp extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'order_id',
        'shop_transaction_id',
        'np_transaction_id',
        'authori_result',
        'authori_ng',
        'authori_required_date',
        'authori_hold',
        'status',
    ];

    protected $casts = [
        'authori_required_date' => 'datetime',
        'authori_hold' => 'array',
    ];

    /**
     * @return BelongsTo
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }
}

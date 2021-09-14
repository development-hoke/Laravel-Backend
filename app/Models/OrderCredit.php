<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class OrderCredit extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'order_id',
        'member_credit_card_id',
        'authorization_number',
        'transaction_number',
        'status',
        'payment_method',
    ];

    /**
     * @return BelongsTo
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * @return BelongsTo
     */
    public function memberCreditCard(): BelongsTo
    {
        return $this->belongsTo(MemberCreditCard::class);
    }
}

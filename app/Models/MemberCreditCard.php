<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class MemberCreditCard extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'member_id',
        'payment_method',
        'priority',
    ];

    protected $casts = [
        'payment_method' => 'string',
    ];

    /**
     * @return HasMany
     */
    public function orderCredits(): HasMany
    {
        return $this->hasMany(OrderCredit::class);
    }
}

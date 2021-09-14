<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class AmazonPayOrder.
 *
 * @package namespace App\Models;
 */
class AmazonPayOrder extends Model
{
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'order_id',
        'order_reference_id',
        'status',
        'status_reason_code',
        'last_status_updated_at',
        'amount',
        'expiration_at',
    ];

    /**
     * @return BelongsTo
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * @return HasMany
     */
    public function authorizations(): HasMany
    {
        return $this->hasMany(AmazonPayAuthorization::class);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class AmazonPayAuthorization.
 *
 * @package namespace App\Models;
 */
class AmazonPayAuthorization extends Model
{
    use SoftDeletes;

    /**
     * @var array
     */
    protected $fillable = [
        'amazon_pay_order_id',
        'authorization_reference_id',
        'amazon_authorization_id',
        'status',
        'status_reason_code',
        'soft_decline',
        'last_status_updated_at',
        'amount',
        'capturing_amount',
        'fee',
        'expiration_at',
    ];

    /**
     * @return HasMany
     */
    public function captures(): HasMany
    {
        return $this->hasMany(AmazonPayCapture::class);
    }
}

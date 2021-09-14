<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class AmazonPayRefund.
 *
 * @package namespace App\Models;
 */
class AmazonPayRefund extends Model
{
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'amazon_pay_capture_id',
        'refund_reference_id',
        'amazon_refund_id',
        'status',
        'status_reason_code',
        'last_status_updated_at',
        'amount',
        'fee',
    ];
}

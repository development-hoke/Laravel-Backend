<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class AmazonPayCapture.
 *
 * @package namespace App\Models;
 */
class AmazonPayCapture extends Model
{
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'amazon_pay_authorization_id',
        'capture_reference_id',
        'amazon_capture_id',
        'status',
        'status_reason_code',
        'last_status_updated_at',
        'amount',
        'fee',
    ];
}

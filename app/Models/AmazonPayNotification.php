<?php

namespace App\Models;

/**
 * Class AmazonPayNotification.
 *
 * @package namespace App\Models;
 */
class AmazonPayNotification extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'message_id',
        'notification_reference_id',
        'status',
        'failed_info',
        'requested_body',
        'type',
        'amazon_object_id',
    ];
}

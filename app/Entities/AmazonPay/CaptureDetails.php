<?php

namespace App\Entities\AmazonPay;

use App\Entities\Entity;

/**
 * @see https://developer.amazon.com/ja/docs/amazon-pay-api/capturedetails.html
 *
 * @property string $amazon_capture_id
 * @property string $capture_reference_id
 * @property string|null $seller_capture_note
 * @property Price $capture_amount
 * @property Price $refund_amount
 * @property Price|null $capture_fee
 * @property \Carbon\Carbon $creation_timestamp
 * @property Status $capture_status
 */
class CaptureDetails extends Entity
{
    protected $cast = [
        'creation_timestamp' => 'date',
        'expiration_timestamp' => 'date',
        'soft_decline' => 'bool',
        'capture_now' => 'bool',
    ];

    protected $relatedEntities = [
        'capture_amount' => Price::class,
        'refund_amount' => Price::class,
        'capture_fee' => Price::class,
        'capture_status' => Status::class,
    ];
}

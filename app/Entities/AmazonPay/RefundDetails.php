<?php

namespace App\Entities\AmazonPay;

use App\Entities\Entity;

/**
 * @see https://developer.amazon.com/ja/docs/amazon-pay-api/refunddetails.html
 *
 * @property string $amazon_refund_id
 * @property string $refund_reference_id
 * @property string|null $seller_refund_note
 * @property Price $refund_amount
 * @property Price|null $fee_refunded
 * @property \Carbon\Carbon $creation_timestamp
 * @property Status $refund_status
 */
class RefundDetails extends Entity
{
    protected $cast = [
        'creation_timestamp' => 'date',
    ];

    protected $relatedEntities = [
        'refund_amount' => Price::class,
        'fee_refunded' => Price::class,
        'refund_status' => Status::class,
    ];
}

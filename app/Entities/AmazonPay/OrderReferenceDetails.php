<?php

namespace App\Entities\AmazonPay;

use App\Entities\Entity;

/**
 * @see https://developer.amazon.com/ja/docs/amazon-pay-api/orderreferencedetails.html
 *
 * @property string $amazon_order_reference_id
 * @property Buyer $buyer
 * @property Price $order_total
 * @property string|null $seller_note
 * @property Destination $destination
 * @property SellerOrderAttributes $seller_order_attributes
 * @property Status $order_reference_status
 * @property \App\Entities\Collection $constraints Constraint[]
 * @property \Carbon\Carbon $creation_timestamp
 * @property \Carbon\Carbon $expiration_timestamp
 */
class OrderReferenceDetails extends Entity
{
    protected $cast = [
        'creation_timestamp' => 'date',
        'expiration_timestamp' => 'date',
    ];

    protected $relatedEntities = [
        'buyer' => Buyer::class,
        'order_total' => Price::class,
        'destination' => Destination::class,
        'seller_order_attributes' => SellerOrderAttributes::class,
        'order_reference_status' => Status::class,
        'constraints' => [Constraint::class],
    ];
}

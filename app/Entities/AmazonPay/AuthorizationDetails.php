<?php

namespace App\Entities\AmazonPay;

use App\Entities\Entity;

/**
 * @see https://developer.amazon.com/ja/docs/amazon-pay-api/authorizationdetails.html
 *
 * @property string $amazon_authorization_id
 * @property string $authorization_reference_id
 * @property string|null $seller_authorization_note
 * @property Price $authorization_amount
 * @property Price $capture_amount
 * @property Price $authorization_fee
 * @property \Carbon\Carbon $creation_timestamp
 * @property \Carbon\Carbon $expiration_timestamp
 * @property Status $authorization_status
 * @property bool $soft_decline
 * @property bool $capture_now
 */
class AuthorizationDetails extends Entity
{
    protected $cast = [
        'creation_timestamp' => 'date',
        'expiration_timestamp' => 'date',
        'soft_decline' => 'bool',
        'capture_now' => 'bool',
    ];

    protected $relatedEntities = [
        'authorization_amount' => Price::class,
        'capture_amount' => Price::class,
        'authorization_fee' => Price::class,
        'authorization_status' => Status::class,
    ];
}

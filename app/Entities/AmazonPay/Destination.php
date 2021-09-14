<?php

namespace App\Entities\AmazonPay;

use App\Entities\Entity;

/**
 * @property string $destination_type
 * @property Address $physical_destination
 */
class Destination extends Entity
{
    protected $relatedEntities = [
        'physical_destination' => Address::class,
    ];
}

<?php

namespace App\Entities\Np;

use App\Entities\Entity;

/**
 * @property string $base_np_transaction_id
 * @property string $shop_transaction_id
 * @property string $np_transaction_id
 * @property \Carbon\Carbon $sales_date
 */
class ReregisteredTransaction extends Entity
{
    protected $cast = [
        'sales_date' => 'date',
    ];
}

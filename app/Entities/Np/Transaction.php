<?php

namespace App\Entities\Np;

use App\Entities\Entity;

/**
 * @property string|null $shop_transaction_id
 * @property string $np_transaction_id
 * @property string $authori_result
 * @property \Carbon\Carbon $authori_required_date
 * @property string|null $authori_ng
 * @property array|null $authori_hold
 */
class Transaction extends Entity
{
    protected $cast = [
        'authori_required_date' => 'date',
    ];
}

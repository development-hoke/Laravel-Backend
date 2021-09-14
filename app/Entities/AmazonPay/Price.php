<?php

namespace App\Entities\AmazonPay;

use App\Entities\Entity;

/**
 * @property string $currency_code ISO4217形式の通貨コード
 * @property int $amount
 */
class Price extends Entity
{
    protected $cast = [
        'amount' => 'int',
    ];
}

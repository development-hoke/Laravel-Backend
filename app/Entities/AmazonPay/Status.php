<?php

namespace App\Entities\AmazonPay;

use App\Entities\Entity;

/**
 * オブジェクトの状態（Order Reference、 オーソリ、売上請求、返金で共通）
 *
 * @property string $state
 * @property \Carbon\Carbon $last_update_timestamp
 * @property string|null $reason_code
 * @property string|null $reason_description
 */
class Status extends Entity
{
    protected $cast = [
        'last_update_timestamp' => 'date',
    ];
}

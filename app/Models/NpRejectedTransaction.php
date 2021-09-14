<?php

namespace App\Models;

class NpRejectedTransaction extends Model
{
    protected $fillable = [
        'member_id',
        'cart_id',
        'shop_transaction_id',
        'np_transaction_id',
        'authori_result',
        'authori_required_date',
        'authori_ng',
        'authori_hold',
        'error_codes',
    ];

    protected $casts = [
        'authori_required_date' => 'datetime',
        'authori_hold' => 'array',
        'error_codes' => 'array',
    ];
}

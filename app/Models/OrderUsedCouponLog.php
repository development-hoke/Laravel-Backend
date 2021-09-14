<?php

namespace App\Models;

class OrderUsedCouponLog extends Model
{
    protected $guarded = ['id', 'updated_at', 'created_at'];

    protected $casts = [
        'applied_order_detail_ids' => 'array',
    ];
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class OrderMessage extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'order_id',
        'title',
        'body',
        'type',
    ];
}

<?php

namespace App\Models;

class Size extends Model
{
    protected $fillable = [
        'code',
        'name',
        'search_code',
    ];

    public $incrementing = false;
}

<?php

namespace App\Models;

class Brand extends Model
{
    protected $fillable = [
        'store_brand',
        'section',
        'name',
        'kana',
        'category',
        'sort',
    ];
}

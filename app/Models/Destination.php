<?php

namespace App\Models;

class Destination extends Model
{
    protected $fillable = [
        'member_id',
        'last_name',
        'first_name',
        'last_name_kana',
        'first_name_kana',
        'phone',
        'postal',
        'pref_id',
        'address',
        'building',
    ];
}

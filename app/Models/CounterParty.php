<?php

namespace App\Models;

class CounterParty extends Model
{
    public $incrementing = false;

    protected $fillable = [
        'code',
        'name',
    ];
}

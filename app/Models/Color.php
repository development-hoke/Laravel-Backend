<?php

namespace App\Models;

class Color extends Model
{
    public $incrementing = false;

    protected $fillable = [
        'name',
        'color_panel',
        'brightness',
        'display_name',
    ];
}

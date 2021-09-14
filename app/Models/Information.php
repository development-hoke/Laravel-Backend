<?php

namespace App\Models;

class Information extends Model
{
    protected $table = 'informations';

    protected $fillable = [
        'title',
        'body',
        'is_store_top',
        'status',
        'priority',
        'publish_at',
    ];
}

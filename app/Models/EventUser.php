<?php

namespace App\Models;

class EventUser extends Model
{
    protected $fillable = [
        'event_id',
        'member_id',
    ];
}

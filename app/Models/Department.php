<?php

namespace App\Models;

class Department extends Model
{
    protected $fillable = [
        'department_group_id',
        'name',
        'code',
        'short_name',
        'sign',
    ];
}

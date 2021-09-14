<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class SalesType extends Model
{
    use SoftDeletes;

    protected $fillable = ['name', 'text_color', 'sort'];
}

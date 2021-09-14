<?php

namespace App\Models;

class ItemBulkUpload extends Model
{
    protected $fillable = [
        'file_name',
        'format',
        'status',
        'success',
        'failure',
        'errors',
    ];
}

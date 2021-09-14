<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Kalnoy\Nestedset\NodeTrait;

class OnlineCategory extends Model
{
    use NodeTrait;

    public $incrementing = false;

    protected $fillable = [
        'parent_id',
        'root_id',
        'level',
        'name',
        'sort',
        '_lft',
        '_rgt',
    ];

    /**
     * ルートカテゴリ
     *
     * @return BelongsTo
     */
    public function root(): BelongsTo
    {
        return $this->belongsTo(static::class, 'root_id');
    }
}

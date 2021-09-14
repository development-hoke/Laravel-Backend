<?php

namespace App\Models;

class HelpCategoryRelation extends Model
{
    protected $fillable = [
        'help_id',
        'help_category_id',
    ];

    /**
     * @return BelongsTo
     */
    public function help(): BelongsTo
    {
        return $this->belongsTo(Help::class);
    }

    /**
     * @return BelongsTo
     */
    public function helpCategory(): BelongsTo
    {
        return $this->belongsTo(HelpCategory::class);
    }
}

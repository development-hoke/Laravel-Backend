<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Help extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'title',
        'body',
        'sort',
        'is_faq',
        'good',
        'bad',
        'status',
    ];

    /**
     * @return BelongsToMany
     */
    public function helpCategories(): BelongsToMany
    {
        return $this->belongsToMany(HelpCategory::class, 'help_category_relations');
    }

    /**
     * localscope
     */
    public function scopePublished($query)
    {
        return $query->where('status', true);
    }
}

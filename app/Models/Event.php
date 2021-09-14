<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Event extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'title',
        'period_from',
        'period_to',
        'target',
        'sale_type',
        'target_user_type',
        'discount_type',
        'discount_rate',
        'published',
    ];

    /**
     * @return HasMany
     */
    public function eventBundleSales(): HasMany
    {
        return $this->hasMany(EventBundleSale::class);
    }

    /**
     * @return HasMany
     */
    public function eventItems(): HasMany
    {
        return $this->hasMany(EventItem::class);
    }
}

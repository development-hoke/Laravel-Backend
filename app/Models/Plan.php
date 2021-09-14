<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\belongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Plan extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'store_brand',
        'slug',
        'title',
        'status',
        'period_from',
        'period_to',
        'thumbnail',
        'place',
        'body',
        'is_item_setting',
    ];

    /**
     * @return HasMany
     */
    public function planItems(): HasMany
    {
        return $this->hasMany(PlanItem::class);
    }

    /**
     * @return belongsToMany
     */
    public function items(): belongsToMany
    {
        return $this->belongsToMany(Item::class, 'plan_items');
    }

    /**
     * @return HasOne
     */
    public function planBrand(): HasOne
    {
        return $this->hasOne(PlanBrand::class);
    }

    /**
     * localscope
     */
    public function scopePublished($query)
    {
        return $query->where('status', true);
    }

    /**
     * localscope
     */
    public function scopeActive($query)
    {
        return $query->where(function ($query) {
            $query->where($this->getTable().'.period_from', '<', Carbon::now()->format('Y-m-d H:i:s'))
                ->where($this->getTable().'.period_to', '>', Carbon::now()->format('Y-m-d H:i:s'));
        })
         ->orWhere(function ($query) {
             $query->where($this->getTable().'.period_from', '=', null)
                ->where($this->getTable().'.period_to', '=', null);
         })
         ->orWhere(function ($query) {
             $query->where($this->getTable().'.period_from', '=', null)
                ->where($this->getTable().'.period_to', '>', Carbon::now()->format('Y-m-d H:i:s'));
         })
         ->orWhere(function ($query) {
             $query->where($this->getTable().'.period_from', '<', Carbon::now()->format('Y-m-d H:i:s'))
                ->where($this->getTable().'.period_to', '=', null);
         });
    }

    /**
     * @param Builder $query
     * @param string $from
     * @param string $to
     *
     * @return Builder
     */
    public function scopePublic(Builder $query): Builder
    {
        return $this->scopePublished($query)
            ->where('period_from', '<=', date('Y-m-d H:i:s'))
            ->where('period_to', '>=', date('Y-m-d H:i:s'));
    }
}

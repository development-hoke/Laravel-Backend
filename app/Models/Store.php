<?php

namespace App\Models;

use App\Models\Traits\HasGeometryAttributes;
use App\Models\Traits\QueryHelperTrait;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Store extends Model
{
    use QueryHelperTrait;
    use HasGeometryAttributes;

    const GEOMETRY_COLUMN = 'location';

    protected $fillable = [
        'code',
        'name',
        'title',
        'zip_code',
        'address1',
        'address2',
        'phone_number_1',
        'phone_number_2',
        'email',
        'location',
        'open_time',
        'close_time',
    ];

    /**
     * @param array ['longitude' => $longitude, 'latitude' => $latitude]
     *
     * @return void
     */
    public function setLocationAttribute($value)
    {
        $this->setAsGeometry(self::GEOMETRY_COLUMN, $value);
    }

    /**
     * @return array ['longitude' => $longitude, 'latitude' => $latitude]
     */
    public function getLocationAttribute()
    {
        return $this->getFromGeometryColumn(self::GEOMETRY_COLUMN);
    }

    /**
     * @return HasMany
     */
    public function itemDetailStores(): HasMany
    {
        return $this->hasMany(ItemDetailStore::class);
    }

    /**
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param array $conditions
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeWhereItemDetailStore($query, array $conditions)
    {
        $subQuery = $this->newRelatedInstance(ItemDetailStore::class)
            ->getQuery()
            ->select('item_detail_stores.store_id');

        $subQuery = $this->applyConditions($subQuery, $conditions);

        return $query->whereIn('stores.id', $subQuery);
    }

    /**
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param array $conditions
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeWhereItemDetail($query, array $conditions)
    {
        $subQuery = $this->newRelatedInstance(ItemDetailStore::class)
            ->getQuery()
            ->select('item_detail_stores.store_id')
            ->join('item_details', 'item_detail_stores.item_detail_id', '=', 'item_details.id');

        $subQuery = $this->applyConditions($subQuery, $conditions);

        return $query->whereIn('stores.id', $subQuery);
    }
}

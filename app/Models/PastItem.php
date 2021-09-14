<?php

namespace App\Models;

use App\Database\Eloquent\CustomPaginationBuilder;
use App\Models\Contracts\Timestampable;
use App\Models\Traits\HasOptionalTimestampsTrait;
use App\Models\Traits\QueryHelperTrait;

/**
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 * @SuppressWarnings(PHPMD.TooManyMethods)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class PastItem extends Model implements Timestampable
{
    // use SoftDeletes;
    use QueryHelperTrait;
    use HasOptionalTimestampsTrait;

    protected $fillable = [
        'name',
        'old_jan_code',
        'jan_code',
        'product_number',
        'maker_product_number',
        'sort',
        'retail_price',
        'price',
        'image_url',
    ];

    /**
     * クエリビルダの上書き
     *
     * @return CustomPaginationBuilder
     */
    public function newEloquentBuilder($query)
    {
        return new CustomPaginationBuilder($query);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

/**
 * Class TopContent.
 *
 * @package namespace App\Models;
 */
class TopContent extends Model implements Transformable
{
    use TransformableTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'store_brand',
        'main_visuals',
        'new_items',
        'pickups',
        'background_color',
        'features',
        'news',
        'styling_sort',
        'stylings',
    ];

    protected $casts = [
        'main_visuals' => 'array',
        'new_items' => 'array',
        'pickups' => 'array',
        'features' => 'array',
        'news' => 'array',
        'stylings' => 'array',
    ];
}

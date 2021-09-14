<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class ItemImage extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'item_id',
        'type',
        'url',
        'file_name',
        'caption',
        'color_id',
        'sort',
    ];

    /**
     * @return array
     */
    public function toArray()
    {
        $array = parent::toArray();

        $array['url_s'] = $this->url_s;
        $array['url_m'] = $this->url_m;
        $array['url_l'] = $this->url_l;
        $array['url_xl'] = $this->url_xl;

        return $array;
    }

    /**
     * @ruturn HasMany
     */
    public function color()
    {
        return $this->hasOne(Color::class, 'id', 'color_id');
    }

    /**
     * @return string
     */
    public function getUrlSAttribute()
    {
        return \App\Domain\Utils\ItemImage::toS($this->url);
    }

    /**
     * @return string
     */
    public function getUrlMAttribute()
    {
        return \App\Domain\Utils\ItemImage::toM($this->url);
    }

    /**
     * @return string
     */
    public function getUrlLAttribute()
    {
        return \App\Domain\Utils\ItemImage::toL($this->url);
    }

    /**
     * @return string
     */
    public function getUrlXlAttribute()
    {
        return \App\Domain\Utils\ItemImage::toXL($this->url);
    }
}

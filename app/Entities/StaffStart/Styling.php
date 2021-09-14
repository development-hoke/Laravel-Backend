<?php

namespace App\Entities\StaffStart;

use App\Entities\Entity;

class Styling extends Entity
{
    /**
     * @var array
     */
    private $attributeMap = [
        'cid' => 'coordinate_id',
    ];

    /**
     * 元データを取り込むための変換処理
     *
     * @param array $data
     *
     * @return array
     */
    protected function toAttributes($data)
    {
        $attributes = translate($data, $this->attributeMap);

        $attributes = array_merge($attributes, [
            'products' => Product::collection($attributes['products']),
        ]);

        return parent::toAttributes($attributes);
    }
}

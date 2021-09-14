<?php

namespace App\Entities\Ymdy\Member;

use App\Entities\Entity;

class EcDetail extends Entity
{
    /**
     * 元データを取り込むための変換処理
     *
     * @param array $data
     *
     * @return array
     */
    protected function toAttributes($data)
    {
        $attributes = array_merge($data, [
            'discounts' => EcDiscount::collection($data['discounts'] ?? []),
        ]);

        return parent::toAttributes($attributes);
    }
}

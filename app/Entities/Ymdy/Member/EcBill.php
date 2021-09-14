<?php

namespace App\Entities\Ymdy\Member;

use App\Entities\Entity;

class EcBill extends Entity
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
            'details' => EcDetail::collection($data['details'] ?? []),
        ]);

        return parent::toAttributes($attributes);
    }
}

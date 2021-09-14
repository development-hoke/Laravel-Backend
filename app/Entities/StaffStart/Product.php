<?php

namespace App\Entities\StaffStart;

use App\Entities\Entity;

class Product extends Entity
{
    /**
     * @var array
     */
    private $attributeMap = [
        'cid' => 'coordinate_id',
        'product_code' => 'product_number',
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
        $data = translate($data, $this->attributeMap);

        return parent::toAttributes($data);
    }
}

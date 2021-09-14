<?php

namespace App\Entities\AmazonPay;

use App\Entities\Entity;

/**
 * @property string $constraint_id \App\Enums\AmazonPay\Constraint
 * @property string $description
 */
class Constraint extends Entity
{
    /**
     * @var array
     */
    private $attributeMap = [
        'ConstraintID' => 'constraint_id',
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

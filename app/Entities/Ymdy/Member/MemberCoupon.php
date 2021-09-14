<?php

namespace App\Entities\Ymdy\Member;

use App\Entities\Entity;

/**
 * @property int $id
 * @property int $member_id
 * @property int $coupon_id
 * @property \App\Entities\Ymdy\Member\Coupon $coupon
 */
class MemberCoupon extends Entity
{
    protected $cast = [
        'member_id' => 'int',
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
        $attributes = array_merge($data, [
            'coupon' => new Coupon($data['coupon']),
        ]);

        return parent::toAttributes($attributes);
    }
}

<?php

namespace App\Entities\Ymdy\Member;

use App\Entities\Collection;
use App\Entities\Entity;

/**
 * @property int $id
 * @property string $name
 * @property string $image_path
 * @property string $start_dt
 * @property string $end_dt
 * @property int $discount_type
 * @property int|null $discount_amount
 * @property float|null $discount_rate
 * @property float|null $discount_price
 * @property int $discount_item_flag
 * @property int $target_item_type
 * @property \App\Entities\Collection $item_data ItemMaster.id
 * @property \App\Entities\Collection $_item_cd_data JANコード
 * @property int $free_shipping_flag
 * @property int $usage_amount_term_flag
 * @property int|null $usage_amount_minimum
 * @property int|null $usage_amount_maximum
 * @property int $is_combinable
 * @property string|null $description
 * @property string|null $discount_text
 * @property string|null $usage_condition
 */
class Coupon extends Entity
{
    protected $visible = [
        'id',
        'name',
        'image_path',
        'start_dt',
        'end_dt',
        'discount_type',
        'discount_amount',
        'discount_rate',
        'discount_price',
        'discount_item_flag',
        'target_item_type',
        'item_data',
        '_item_cd_data',
        'free_shipping_flag',
        'usage_amount_term_flag',
        'usage_amount_minimum',
        'usage_amount_maximum',
        'is_combinable',
        'description',
        'discount_text',
        'usage_condition',
    ];

    protected $attributes = [
        'discount_price' => 0, // NOTE: 計算する必要があるので注意する
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
            'item_data' => Collection::make($data['item_data'] ?? []),
            '_item_cd_data' => Collection::make($data['_item_cd_data'] ?? []),
        ]);

        $attributes['discount_text'] = \App\Domain\Coupon::getDiscountText($attributes);
        $attributes['usage_condition'] = \App\Domain\Coupon::getUsageCondition($attributes);

        return parent::toAttributes($attributes);
    }
}

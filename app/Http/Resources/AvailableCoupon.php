<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class AvailableCoupon extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return array
     */
    public function toArray($request)
    {
        $usageCondition = \App\Domain\Coupon::getUsageCondition($this['coupon']) || null;
        $discountText = \App\Domain\Coupon::getDiscountText($this['coupon']);

        return [
            'id' => $this['id'],
            'name' => $this['coupon']['name'],
            'image_path' => $this['coupon']['image_path'],
            'start_dt' => $this['coupon']['start_dt'],
            'end_dt' => $this['coupon']['end_dt'],
            'discount_type' => $this['coupon']['discount_type'],
            'discount_amount' => $this['coupon']['discount_amount'],
            'discount_rate' => $this['coupon']['discount_rate'],
            'discount_item_flag' => $this['coupon']['discount_item_flag'],
            'discount_text' => $discountText,
            'target_item_type' => $this['coupon']['target_item_type'],
            'item_data' => $this['coupon']['item_data'],
            'target_shop_type' => $this['coupon']['target_shop_type'],
            'shop_data' => $this['coupon']['shop_data'],
            'usage_amount_term_flag' => $this['coupon']['usage_amount_term_flag'],
            'usage_amount_minimum' => $this['coupon']['usage_amount_minimum'],
            'usage_amount_maximum' => $this['coupon']['usage_amount_maximum'],
            'is_combinable' => $this['coupon']['is_combinable'],
            'usage_condition' => $usageCondition,
            'description' => $this['coupon']['description'],
            'usage_number_limit' => $this['coupon']['usage_number_limit'],
            // todo: 会員ポイント側で返却値追加してもらう予定
            'usage_number_rest' => $this['coupon']['usage_number_rest'] ?? $this['coupon']['usage_number_limit'],
        ];
    }
}

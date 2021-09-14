<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class OrderUsedCoupon extends JsonResource
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
        return [
            'id' => $this->id,
            'order_id' => $this->order_id,
            'coupon_id' => $this->coupon_id,
            'item_applied_price' => $this->item_applied_price,
            'coupon' => $this->coupon instanceof \App\Entities\Ymdy\Member\Coupon ? $this->coupon->toArray() : null,
            'delivery_fee_discount' => $this->whenLoaded('deliveryFeeDiscount'),
            'item_discount' => $this->whenLoaded('itemDiscount'),
            $this->mergeWhen($this->resource->relationLoaded('itemDiscount'), function () {
                return [
                    'item_applied_price' => $this->item_applied_price,
                ];
            }),
        ];
    }
}

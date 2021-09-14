<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class Cart extends JsonResource
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
            'items' => $this->cartItems->toArray(),
            'coupons' => $this->coupons,
            'use_coupon_ids' => $this->use_coupon_ids,
            'items_total' => $this->items_total,
            'campaign_discount' => $this->campaign_discount,
            'coupon_discount' => $this->coupon_discount,
            'employee_discount' => $this->employee_discount,
            'postage' => $this->postage,
            'total_price' => $this->total_price,
            'point' => $this->point,
        ];
    }
}

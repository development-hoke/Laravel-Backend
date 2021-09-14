<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class Order extends JsonResource
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
            'member_id' => $this->member_id,
            'member' => $this->member,
            'code' => $this->code,
            'order_date' => $this->order_date,
            'payment_type' => $this->payment_type,
            'delivery_type' => $this->delivery_type,
            'delivery_token' => $this->delivery_token,
            'delivery_hope_date' => $this->delivery_hope_date,
            'delivery_hope_time' => $this->delivery_hope_time,
            'price' => $this->price,
            'fee' => $this->fee,
            'delivery_fee' => $this->delivery_fee,
            'use_point' => $this->use_point,
            'order_type' => $this->order_type,
            'paid' => $this->paid,
            'paid_date' => $this->paid_date,
            'inspected' => $this->inspected,
            'inspected_date' => $this->inspected_date,
            'deliveryed' => $this->deliveryed,
            'deliveryed_date' => $this->deliveryed_date,
            'status' => $this->status,
            'add_point' => $this->add_point,
            'delivery_number' => $this->delivery_number,
            'delivery_company' => $this->delivery_company,
            'memo1' => $this->memo1,
            'memo2' => $this->memo2,
            'shop_memo' => $this->shop_memo,
            'is_guest' => $this->is_guest,
            'acceptable_payment_types' => $this->acceptable_payment_types, // 受注詳細のみ
            'order_used_coupons' => OrderUsedCoupon::collection($this->whenLoaded('orderUsedCoupons')),
            'order_details' => OrderDetail::collection($this->whenLoaded('orderDetails')),
            'order_messages' => OrderMessage::collection($this->whenLoaded('orderMessages')),
            'order_change_histories' => OrderChangeHistory::collection($this->whenLoaded('orderChangeHistories')),
            'delivery_fee_discount' => $this->whenLoaded('deliveryFeeDiscount'),
            'delivery_address' => $this->whenLoaded('deliveryOrderAddress'),
            'billing_address' => $this->whenLoaded('billingOrderAddress'),
            'member' => $this->whenLoaded('memberOrderAddress'),
            $this->mergeWhen($this->resource->relationLoaded('deliveryFeeDiscount'), function () {
                return [
                    'delivery_fee_discount_type' => $this->delivery_fee_discount_type,
                    'delivery_fee_discount_price' => $this->delivery_fee_discount_price,
                ];
            }),
        ];
    }
}

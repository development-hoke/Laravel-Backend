<?php

namespace App\Http\Resources\Front;

use App\Enums\Order\DeliveryCompany;
use App\Enums\Order\Status;
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
        $couponDiscount = $this->resource->orderUsedCoupons->sum('item_applied_price');

        $campaignDiscount = $this->resource->orderDetails->reduce(function ($sum, $orderDetail) {
            if ((int) $orderDetail->displayed_discount_type === \App\Enums\OrderDiscount\Type::EventSale) {
                $sum += $orderDetail->displayed_discount_price;
            }

            return $sum + $orderDetail->bundle_discount_price;
        }, 0);

        $employeeDiscount = $this->resource->orderDetails->reduce(function ($sum, $orderDetail) {
            if ((int) $orderDetail->displayed_discount_type === \App\Enums\OrderDiscount\Type::Staff) {
                $sum += $orderDetail->displayed_discount_price;
            }

            return $sum;
        }, 0);

        $expectedArrivalDate = '';
        if ($this->order_type === \App\Enums\Order\OrderType::Reserve) {
            $expectedArrivalDate = $this->resource->orderDetails[0]->itemDetail->item->itemReserve->expected_arrival_date ?? '';
        } elseif ($this->order_type === \App\Enums\Order\OrderType::BackOrder) {
            $expectedArrivalDate = '購入から通常3日~7日後';
        }

        return [
            'id' => $this->id,
            'member_id' => $this->member_id,
            'code' => $this->code,
            'order_date' => (clone $this->order_date)->format('Y/m/d'),
            'payment_type' => $this->payment_type,
            'delivery_type' => $this->delivery_type,
            'delivery_token' => $this->delivery_token,
            'delivery_hope_date' => $this->delivery_hope_date,
            'delivery_hope_time' => $this->delivery_hope_time,
            'price' => $this->price,
            'fee' => $this->fee,
            'use_point' => $this->use_point,
            'order_type' => $this->order_type,
            'deliveryed' => $this->deliveryed,
            'deliveryed_date' => $this->deliveryed_date,
            'status' => [
                'value' => $this->status,
                'label' => Status::getDescription($this->status),
                // TODO 問合せ先link
                'link' => '',
            ],
            'delivery_number' => $this->delivery_number,
            'delivery_company' => [
                'value' => $this->delivery_company,
                'label' => DeliveryCompany::getDescription($this->delivery_company),
            ],
            'items_total' => $this->items_total,
            'campaign_discount' => $campaignDiscount,
            'coupon_discount' => $couponDiscount,
            'employee_discount' => $employeeDiscount,
            'postage' => $this->discounted_delivery_fee,
            'order_details' => OrderDetail::collection($this->whenLoaded('orderDetails')),
            'can_cancel' => $this->can_cancel,
            'expected_arrival_date' => $expectedArrivalDate,
        ];
    }
}

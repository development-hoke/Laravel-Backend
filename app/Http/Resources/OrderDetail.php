<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class OrderDetail extends JsonResource
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
            'item_detail_id' => $this->item_detail_id,
            'retail_price' => $this->retail_price,
            'amount' => $this->amount,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'price_before_order' => $this->price_before_order,
            'order_detail_units' => OrderDetailUnit::collection($this->whenLoaded('orderDetailUnits')),
            'item_detail' => new ItemDetail($this->whenLoaded('itemDetail')),
        ];
    }
}

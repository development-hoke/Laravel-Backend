<?php

namespace App\Http\Resources\Front;

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
            'item' => new OrderDetailItem($this->itemDetail),
        ];
    }
}

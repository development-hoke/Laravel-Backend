<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ItemDetailIdentification extends JsonResource
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
            'item_detail_id' => $this->item_detail_id,
            'jan_code' => $this->jan_code,
            'ec_stock' => $this->ec_stock,
            'reservable_stock' => $this->reservable_stock,
            'dead_inventory_days' => $this->dead_inventory_days,
            'slow_moving_inventory_days' => $this->slow_moving_inventory_days,
            'latest_added_stock' => $this->latest_added_stock,
            'latest_stock_added_at' => $this->latest_stock_added_at,
            'arrival_date' => $this->arrival_date,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'item_detail' => new ItemDetail($this->whenLoaded('itemDetail')),
            'order_details' => OrderDetail::collection($this->whenLoaded('orderDetails')),
        ];
    }
}

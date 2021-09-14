<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ItemReserve extends JsonResource
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
            'item_id' => $this->item_id,
            'is_enable' => $this->is_enable,
            'period_from' => $this->period_from,
            'period_to' => $this->period_to,
            'reserve_price' => $this->reserve_price,
            'is_free_delivery' => $this->is_free_delivery,
            'limited_stock_threshold' => $this->limited_stock_threshold,
            'out_of_stock_threshold' => $this->out_of_stock_threshold,
            'expected_arrival_date' => $this->expected_arrival_date,
            'note' => $this->note,
            'item' => new Item($this->whenLoaded('item')),
            'status_text' => $this->is_enable ? '予約販売' : '通常販売',
        ];
    }
}

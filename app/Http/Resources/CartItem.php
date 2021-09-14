<?php

namespace App\Http\Resources;

use App\Http\Resources\Front\ItemDetail;
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
            'id' => $this->id,
            'cart_id' => $this->cart_id,
            'item_detail_id' => $this->item_detail_id,
            'closed_market_id' => $this->closed_market_id,
            'is_closed_market' => $this->is_closed_market,
            'count' => $this->count,
            'item_detail' => new ItemDetail($this->whenLoaded('itemDetail')),
        ];
    }
}

<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ItemDetailStore extends JsonResource
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
            'store_id' => $this->store_id,
            'stock' => $this->stock,
            'item_detail' => new ItemDetail($this->whenLoaded('itemDetail')),
        ];
    }
}

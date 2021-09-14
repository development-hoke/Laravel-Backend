<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ClosedMarket extends JsonResource
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
            'item_detail_id' => $this->item_detail_id,
            'url' => $this->url,
            'title' => $this->title,
            'password' => $this->password,
            'num' => $this->num,
            'stock' => $this->stock,
            'limit_at' => $this->limit_at,
            'item_detail' => new ItemDetail($this->whenLoaded('itemDetail')),
        ];
    }
}

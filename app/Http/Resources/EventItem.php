<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class EventItem extends JsonResource
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
            'event_id' => $this->event_id,
            'item_id' => $this->item_id,
            'discount_rate' => $this->discount_rate,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'item' => new Item($this->whenLoaded('item')),
        ];
    }
}

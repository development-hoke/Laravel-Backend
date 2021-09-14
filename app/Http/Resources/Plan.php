<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class Plan extends JsonResource
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
            'store_brand' => $this->store_brand,
            'slug' => $this->slug,
            'title' => $this->title,
            'status' => (bool) $this->status,
            'period_from' => $this->period_from,
            'period_to' => $this->period_to,
            'thumbnail' => $this->thumbnail,
            'place' => $this->place,
            'body' => $this->body,
            'is_item_setting' => (bool) $this->is_item_setting,
            'items' => Item::collection($this->whenLoaded('items')),
        ];
    }
}

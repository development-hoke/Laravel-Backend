<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class Event extends JsonResource
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
            'title' => $this->title,
            'period_from' => $this->period_from,
            'period_to' => $this->period_to,
            'target' => $this->target,
            'sale_type' => $this->sale_type,
            'target_user_type' => $this->target_user_type,
            'discount_type' => $this->discount_type,
            'discount_rate' => $this->discount_rate,
            'published' => (bool) $this->published,
            'event_bundle_sales' => JsonResource::collection($this->whenLoaded('eventBundleSales')),
        ];
    }
}

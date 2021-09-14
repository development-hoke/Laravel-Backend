<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class TopContent extends JsonResource
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
            'main_visuals' => $this->main_visuals,
            'new_items' => $this->new_items,
            'pickups' => $this->pickups,
            'background_color' => $this->background_color,
            'features' => $this->features,
            'news' => $this->news,
            'styling_sort' => $this->styling_sort,
            'stylings' => $this->stylings,
        ];
    }
}

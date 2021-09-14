<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ItemImage extends JsonResource
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
            'type' => $this->type,
            'url' => $this->url,
            'url_s' => $this->url_s,
            'url_m' => $this->url_m,
            'url_l' => $this->url_l,
            'url_xl' => $this->url_xl,
            'file_name' => $this->file_name,
            'caption' => $this->caption,
            'color_id' => $this->color_id,
            'sort' => $this->sort,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}

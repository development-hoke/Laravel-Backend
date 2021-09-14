<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class Brand extends JsonResource
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
            'section' => $this->section,
            'name' => $this->name,
            'kana' => $this->kana,
            'category' => $this->category,
            'sort' => $this->sort,
        ];
    }
}

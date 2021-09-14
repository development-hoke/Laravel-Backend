<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PastItem extends JsonResource
{
    /**
     * Transform the resource collection into an array.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'old_jan_code' => $this->old_jan_code,
            'jan_code' => $this->jan_code,
            'product_number' => $this->product_number,
            'maker_product_number' => $this->maker_product_number,
            'sort' => $this->sort,
            'retail_price' => $this->retail_price,
            'price' => $this->price,
            'image_url' => $this->image_url,
        ];
    }
}

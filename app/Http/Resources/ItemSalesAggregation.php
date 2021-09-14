<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ItemSalesAggregation extends JsonResource
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
            'department_id' => $this->department_id,
            'division_id' => $this->division_id,
            'main_store_brand' => $this->main_store_brand,
            'maker_product_number' => $this->maker_product_number,
            'product_number' => $this->product_number,
            'total_amount' => $this->total_amount,
            'total_price' => $this->total_price,
            'retail_price' => $this->retail_price,
            'contracted_price' => $this->contracted_price,
            'item_images' => ItemImage::collection($this->whenLoaded('itemImages')),
            'item_details' => ItemDetailSalesAggregation::collection($this->whenLoaded('itemDetails')),
        ];
    }
}

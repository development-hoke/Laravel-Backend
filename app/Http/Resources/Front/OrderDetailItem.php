<?php

namespace App\Http\Resources\Front;

use App\Domain\Utils\ItemPrice;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderDetailItem extends JsonResource
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
            'id' => $this->item->id,
            'brand' => [
                'id' => $this->item->brand_id,
                'name' => $this->item->brand->name,
            ],
            'name' => $this->item->name,
            'product_number' => $this->item->product_number,
            'color' => [
                'id' => $this->color->id,
                'code' => $this->color->code,
                'display_name' => $this->color->display_name,
            ],
            'size' => [
                'id' => $this->size_id,
                'name' => $this->size->name,
            ],
            'price' => ItemPrice::calcDiscountedPrice($this->item),
            'retail_price' => $this->item->retail_price,
            'image_url' => $this->image_url,
        ];
    }
}

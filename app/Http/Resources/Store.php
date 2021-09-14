<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class Store extends JsonResource
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
            'code' => $this->code,
            'name' => $this->name,
            'zip_code' => $this->zip_code,
            'address1' => $this->address1,
            'address2' => $this->address2,
            'phone_number_1' => $this->phone_number_1,
            'phone_number_2' => $this->phone_number_2,
            'email' => $this->email,
            'location' => $this->location,
            'item_detail_stores' => ItemDetailStore::collection($this->whenLoaded('itemDetailStores')),
        ];
    }
}

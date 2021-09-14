<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ItemDetailRedisplayRequest extends JsonResource
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
            'item_detail_id' => $this->item_detail_id,
            'user_token' => $this->user_token,
            'is_account_related' => !empty($this->member_id),
            'user_name' => $this->user_name,
            'email' => $this->email,
            'is_notified' => $this->is_notified,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'item_id' => $this->when($this->resource->relationLoaded('itemDetail'), function () {
                return $this->itemDetail->item_id;
            }),
        ];
    }
}

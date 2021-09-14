<?php

namespace App\Http\Resources;

use App\Domain\Store;
use Illuminate\Http\Resources\Json\JsonResource;

class PointHistory extends JsonResource
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
        $store = resolve(Store::class);

        return [
            'id' => $this['id'],
            'shop' => $store->get($this['shop_id'], true),
            'point_adjustment_reason' => $this['point_adjustment_reason'],
            'point_adjustment_reason_description' => $this['_point_adjustment_reason_description'],
            'amount' => $this['amount'],
            'issued_date' => $this['created_at'],
        ];
    }
}

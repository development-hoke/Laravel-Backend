<?php

namespace App\Http\Resources\Front;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * NOTE: フロント用闇市のJSON。パスワードを入れないこと。
 */
class ClosedMarket extends JsonResource
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
            'member_id' => $this->member_id,
            'item_detail_id' => $this->item_detail_id,
            'url' => $this->url,
            'title' => $this->title,
            'num' => $this->num,
            'stock' => $this->stock,
            'limit_at' => $this->limit_at,
        ];
    }
}

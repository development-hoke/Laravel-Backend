<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class OrderChangeHistory extends JsonResource
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
            'order_id' => $this->order_id,
            'log_type' => $this->log_type,
            'log_id' => $this->log_id,
            'staff_id' => $this->staff_id,
            'event_type' => $this->event_type,
            'diff_json' => $this->diff_json,
            'memo' => $this->memo,
            'is_item_event' => $this->is_item_event,
            'created_at' => $this->created_at,
            'staff' => new Staff($this->whenLoaded('staff')),
            'item_detail' => new ItemDetail($this->whenLoaded('itemDetail')),
        ];
    }
}

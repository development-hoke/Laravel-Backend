<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class Information extends JsonResource
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
            'title' => $this->title,
            'body' => $this->body,
            'priority' => $this->priority,
            'is_store_top' => (bool) $this->is_store_top,
            'status' => (bool) $this->status,
            'publish_at' => $this->publish_at,
        ];
    }
}

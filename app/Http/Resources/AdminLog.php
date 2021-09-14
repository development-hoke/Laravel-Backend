<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class AdminLog extends JsonResource
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
            'staff_id' => $this->staff_id,
            'action' => $this->action,
            'url' => $this->url,
            'type' => $this->type,
            'action_text' => $this->action_text,
            'ip' => $this->ip,
            'referer' => $this->referer,
            'memo' => $this->memo,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'staff' => new Staff($this->whenLoaded('staff')),
        ];
    }
}

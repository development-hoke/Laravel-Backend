<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class Color extends JsonResource
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
            'color_panel' => $this->color_panel,
            'display_name' => $this->display_name,
        ];
    }
}

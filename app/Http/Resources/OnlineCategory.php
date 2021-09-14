<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class OnlineCategory extends JsonResource
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
            'parent_id' => $this->parent_id,
            'name' => $this->name,
            'sort' => $this->sort,
            'level' => $this->level,
            'root_id' => $this->root_id,
            'is_leaf' => $this->resource->isLeaf(),
            'children' => static::collection($this->whenLoaded('children')),
            'parent' => new static($this->whenLoaded('parent')),
            'ancestors' => static::collection($this->whenLoaded('ancestors')),
            'root' => new static($this->whenLoaded('root')),
        ];
    }
}

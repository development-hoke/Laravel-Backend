<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class Help extends JsonResource
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
            'sort' => $this->sort,
            'is_faq' => (bool) $this->is_faq,
            'help_categories' => HelpCategory::collection($this->whenLoaded('helpCategories')),
            'good' => $this->good,
            'bad' => $this->bad,
            'status' => (bool) $this->status,
        ];
    }
}

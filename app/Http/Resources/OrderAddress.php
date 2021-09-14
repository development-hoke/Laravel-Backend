<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class OrderAddress extends JsonResource
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
            'type' => $this->type,
            'fname' => $this->fname,
            'lname' => $this->lname,
            'fkana' => $this->fkana,
            'lkana' => $this->lkana,
            'tel' => $this->tel,
            'pref_id' => $this->pref_id,
            'zip' => $this->zip,
            'city' => $this->city,
            'town' => $this->town,
            'address' => $this->address,
            'building' => $this->building,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'pref' => new Pref($this->whenLoaded('pref')),
        ];
    }
}

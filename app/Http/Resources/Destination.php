<?php

namespace App\Http\Resources;

use App\Models\Pref;
use Illuminate\Http\Resources\Json\JsonResource;

class Destination extends JsonResource
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
        $pref = Pref::find($this['pref_id']);

        return [
            'id' => $this['id'],
            'lname' => $this['lname'],
            'fname' => $this['fname'],
            'lkana' => $this['lkana'],
            'fkana' => $this['fkana'],
            'zip' => $this['zip'],
            'pref_id' => $this['pref_id'],
            'pref' => [
                'id' => $pref->id ?? null,
                'name' => $pref->name ?? null,
            ],
            'city' => $this['city'],
            'town' => $this['town'],
            'address' => $this['address'],
            'building' => $this['building'],
            'tel' => $this['tel'],
        ];
    }
}

<?php

namespace App\Http\Resources;

use App\Repositories\PrefRepository;
use Illuminate\Http\Resources\Json\JsonResource;

class User extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param array $data
     *
     * @return array
     */
    public function toArray($data)
    {
        $member = $this['member'];
        $prefRepository = resolve(PrefRepository::class);

        try {
            $pref = $prefRepository->find($member['pref_id']);
        } catch (\Exception $e) {
            $pref = null;
        }

        return [
            'id' => $member['id'],
            'lname' => $member['lname'],
            'fname' => $member['fname'],
            'lkana' => $member['lkana'],
            'fkana' => $member['fkana'],
            'birthday' => $member['birthday'],
            'gender' => $member['gender'],
            'zip' => $member['zip'],
            'pref' => $pref ? new Pref($pref) : null,
            'pref_id' => $pref ? $pref->id : null,
            'city' => $member['city'],
            'town' => $member['town'],
            'address' => $member['address'],
            'building' => $member['building'],
            'tel' => $member['tel'],
            'email' => $member['email'],
            'mail_dm' => $member['mail_dm'],
            'post_dm' => $member['post_dm'],
            'card_id' => $member['card_id'],
            'pin' => $member['pin'],
            'is_amazon_linked' => $member['is_amazon_linked'] ?? null,
        ];
    }
}

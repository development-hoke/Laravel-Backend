<?php

namespace App\Http\Requests\Api\V1\Front\Member;

use App\Http\Requests\Api\V1\Front\BaseMemberRequest;
use App\Validation\Rules\Kana;

class UpdateRequest extends BaseMemberRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'lname' => 'required|min:1|max:255',
            'fname' => 'required|min:1|max:255',
            'lkana' => [
                'required',
                'min:1',
                'max:255',
                new Kana(),
            ],
            'fkana' => [
                'required',
                'min:1',
                'max:255',
                new Kana(),
            ],
            'tel' => 'required|digits_between:10,11',
            'zip' => 'required|digits:7',
            'pref_id' => 'required|exists:prefs,id',
            'city' => 'required|min:1|max:50',
            'town' => 'required|min:1|max:50',
            'address' => 'required|min:1|max:100',
            'building' => 'sometimes|nullable|max:100',
        ] + parent::rules();
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array
     */
    public function attributes()
    {
        return [
            'lname' => __('validation.attributes.destination.lname'),
            'fname' => __('validation.attributes.destination.fname'),
            'lkana' => __('validation.attributes.destination.lkana'),
            'fkana' => __('validation.attributes.destination.fkana'),
            'tel' => __('validation.attributes.destination.tel'),
            'zip' => __('validation.attributes.destination.zip'),
            'pref_id' => __('validation.attributes.destination.pref_id'),
            'city' => __('validation.attributes.destination.city'),
            'town' => __('validation.attributes.destination.town'),
            'address' => __('validation.attributes.destination.address'),
            'building' => __('validation.attributes.destination.building'),
        ] + parent::attributes();
    }
}

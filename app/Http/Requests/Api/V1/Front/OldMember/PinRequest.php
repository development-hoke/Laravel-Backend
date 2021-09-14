<?php

namespace App\Http\Requests\Api\V1\Front\OldMember;

use Illuminate\Foundation\Http\FormRequest;

class PinRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'card_id' => 'required',
            'pin_code' => 'required|digits:4',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array
     */
    public function attributes()
    {
        return [
            'card_id' => __('validation.attributes.old_member.pin.card_id'),
            'pin_code' => __('validation.attributes.old_member.pin.pin_code'),
        ];
    }
}

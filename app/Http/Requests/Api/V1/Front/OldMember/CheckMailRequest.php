<?php

namespace App\Http\Requests\Api\V1\Front\OldMember;

use Illuminate\Foundation\Http\FormRequest;

class CheckMailRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'email' => 'required|email',
            'email_confirm' => 'required|email|same:email',
            'card_id' => 'required',
            'pin' => 'required|digits:4',
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
            'email' => __('validation.attributes.old_member.check_mail.email'),
            'email_confirm' => __('validation.attributes.old_member.check_mail.email_confirm'),
            'card_id' => __('validation.attributes.old_member.pin.card_id'),
            'pin_code' => __('validation.attributes.old_member.pin.pin_code'),
        ];
    }
}

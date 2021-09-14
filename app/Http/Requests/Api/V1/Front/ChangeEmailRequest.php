<?php

namespace App\Http\Requests\Api\V1\Front;

class ChangeEmailRequest extends BaseMemberRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'email' => 'required|email|confirmed',
            'email_confirmation' => 'required|email',
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
            'email' => __('validation.attributes.change_email.email'),
            'email_confirmation' => __('validation.attributes.change_email.email_confirmation'),
        ] + parent::attributes();
    }
}

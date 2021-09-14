<?php

namespace App\Http\Requests\Api\V1\Front\OldMember;

use Illuminate\Foundation\Http\FormRequest;

class ForgetMailRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'lname' => 'required|string',
            'fname' => 'required|string',
            'birthday' => 'required|string',
            'tel' => 'required|string',
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
            'lname' => __('validation.attributes.forget_mail.lname'),
            'fname' => __('validation.attributes.forget_mail.fname'),
            'birthday' => __('validation.attributes.forget_mail.birthday'),
            'tel' => __('validation.attributes.forget_mail.tel'),
        ];
    }
}

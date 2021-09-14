<?php

namespace App\Http\Requests\Api\V1\Front\OldMember;

use Illuminate\Foundation\Http\FormRequest;

class ForgetAllRequest extends FormRequest
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
            'tel' => 'string',
            'member_id' => 'nullable',
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
            'email' => __('validation.attributes.forget_all.email'),
            'tel' => __('validation.attributes.forget_all.tel'),
            'member_id' => __('validation.attributes.forget_all.member_id'),
        ];
    }
}

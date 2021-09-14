<?php

namespace App\Http\Requests\Api\V1\Front\Auth;

use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'email' => 'required|string|email',
            'password' => 'required|string',
            'keep_login' => 'required',
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
            'email' => __('validation.attributes.front_auth.email'),
            'password' => __('validation.attributes.front_auth.passowrd'),
            'keep_login' => __('validation.attributes.front_auth.keep_login'),
        ];
    }
}

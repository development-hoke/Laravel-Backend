<?php

namespace App\Http\Requests\Api\V1\Front\AuthAmazon;

use App\Http\Requests\Api\V1\Front\BaseRequest;

class LoginRequest extends BaseRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return !empty($this->access_token);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'access_token' => 'required|string|max:2000',
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
            'access_token' => __('validation.attributes.amazon_auth.access_token'),
        ];
    }
}

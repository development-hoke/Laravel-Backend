<?php

namespace App\Http\Requests\Api\V1\Front\Item;

use App\Http\Requests\Api\V1\Front\BaseRequest;

class VerifyClosedMarketRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'password' => 'required|max:255',
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
            'password' => __('validation.attributes.closed_market.password'),
        ];
    }
}

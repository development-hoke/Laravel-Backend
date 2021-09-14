<?php

namespace App\Http\Requests\Api\V1\Front\Cart;

use App\Http\Requests\Api\V1\Front\BaseRequest;
use App\Http\Requests\Api\V1\Front\Cart\Traits\CartMemberAuthorize;

class UpdateUsePointRequest extends BaseRequest
{
    use CartMemberAuthorize;

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'use_point' => 'required|integer',
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
            'use_point' => __('validation.attributes.purchase.use_point'),
        ];
    }

    /**
     * @return array
     */
    public function messages()
    {
        return [
            'cart_id.*' => __('validation.cart.not_found'),
        ];
    }
}

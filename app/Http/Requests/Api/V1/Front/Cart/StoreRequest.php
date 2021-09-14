<?php

namespace App\Http\Requests\Api\V1\Front\Cart;

use App\Http\Requests\Api\V1\Front\BaseRequest;
use Illuminate\Validation\Rule;

class StoreRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'cart_id' => 'nullable|integer',
            'product_number' => 'required|string',
            'member_id' => 'sometimes|numeric',
            'token' => 'sometimes|string|max:64',
            'color_id' => 'required|exists:colors,id',
            'size_id' => 'required|exists:sizes,id',
            'closed_market_id' => 'nullable|exists:closed_markets,id,deleted_at,NULL',
            'status' => [
                'required',
                Rule::in(\App\Enums\Order\OrderType::getValues()),
            ],
            'count' => 'sometimes|numeric|min:1',
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
            'product_number' => __('validation.attributes.item.product_number'),
            'member_id' => __('validation.attributes.member.id'),
            'token' => __('validation.attributes.cart.token'),
            'color_id' => __('validation.attributes.color.id'),
            'size_id' => __('validation.attributes.size.id'),
            'closed_market_id' => __('validation.attributes.closed_market.id'),
            'count' => __('validation.attributes.cart.items.count'),
        ];
    }
}

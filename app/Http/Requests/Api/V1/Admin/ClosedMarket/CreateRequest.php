<?php

namespace App\Http\Requests\Api\V1\Admin\ClosedMarket;

use App\Http\Requests\Api\V1\Request;

class CreateRequest extends Request
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'size_id' => 'required|integer|exists:sizes,id',
            'color_id' => 'required|integer|exists:colors,id',
            'title' => 'required|max:255',
            'limit_at' => 'required|date',
            'password' => 'required|alpha_dash|min:6|max:255',
            'num' => 'required|integer',
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
            'size_id' => __('validation.attributes.size.id'),
            'color_id' => __('validation.attributes.color.id'),
            'title' => __('validation.attributes.closed_market.title'),
            'limit_at' => __('validation.attributes.closed_market.limit_at'),
            'password' => __('validation.attributes.closed_market.password'),
            'num' => __('validation.attributes.closed_market.num'),
        ];
    }
}

<?php

namespace App\Http\Requests\Api\V1\Front\Member\Destination;

use App\Http\Requests\Api\V1\Front\BaseRequest;

class IndexRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'memberId' => 'required|numeric',
            'billing_address_flag' => 'boolean',
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
            'memberId' => __('validation.attributes.member.id'),
            'billing_address_flag' => __('validation.attributes.destination.billing_address_flag'),
        ];
    }
}

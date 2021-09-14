<?php

namespace App\Http\Requests\Api\V1\Front\Member\Destination;

use App\Http\Requests\Api\V1\Front\BaseRequest;

class DestroyRequest extends BaseRequest
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
            'destinationId' => 'required|numeric',
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
            'destinationId' => __('validation.attributes.destination.id'),
        ];
    }
}

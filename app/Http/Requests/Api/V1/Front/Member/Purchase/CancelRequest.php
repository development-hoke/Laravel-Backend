<?php

namespace App\Http\Requests\Api\V1\Front\Member\Purchase;

use App\Http\Requests\Api\V1\Front\BaseRequest;

class CancelRequest extends BaseRequest
{
    public function authorize()
    {
        return (int) auth('api')->id() === (int) $this->memberId;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'memberId' => 'required|numeric',
            'orderCode' => 'required|exists:orders,code,deleted_at,NULL',
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
            'orderCode' => __('validation.attributes.purchase.order.code'),
        ];
    }
}

<?php

namespace App\Http\Requests\Api\V1\External\Purchase;

use App\Enums\Order\DeliveryCompany;
use App\Enums\Order\DeliveryStatus;
use App\Http\Requests\Api\V1\Front\BaseRequest;
use Illuminate\Validation\Rule;

class DeliveredRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'delivery_number' => 'required|max:100',
            'delivery_company' => [
                'required',
                Rule::in(DeliveryCompany::getValues()),
            ],
            'delivery_date' => 'required|date_format:Y-m-d H:i:s',
            'status' => [
                'required',
                Rule::in([DeliveryStatus::Deliveryed]),
            ],
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
            'delivery_number' => __('validation.attributes.purchase.order.delivery_number'),
            'delivery_company' => __('validation.attributes.purchase.order.delivery_company'),
            'delivery_date' => __('validation.attributes.purchase.order.delivery_date'),
            'status' => __('validation.attributes.purchase.order.status'),
        ];
    }
}

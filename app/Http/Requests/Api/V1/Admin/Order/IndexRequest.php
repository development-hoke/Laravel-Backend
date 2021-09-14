<?php

namespace App\Http\Requests\Api\V1\Admin\Order;

use App\Http\Requests\Api\V1\Request;
use Illuminate\Validation\Rule;

class IndexRequest extends Request
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'order_date_from' => 'date',
            'order_date_to' => 'date',
            'code' => 'max:32',
            'product_number.*' => 'string|max:255',
            'delivery_number.*' => 'string|max:255',
            'jan_code.*' => 'string|max:255',
            'status' => [Rule::in(\App\Enums\Order\Status::getValues())],
            'payment_type' => [Rule::in(\App\Enums\Order\PaymentType::getValues())],
            'delivery_type' => [Rule::in(\App\Enums\Order\DeliveryType::getValues())],
            'order_type' => [Rule::in(\App\Enums\Order\OrderType::getValues())],
            'paid' => 'boolean',
            'deliveryed' => 'boolean',
            'inspected' => 'boolean',
            'member_name' => 'string',
            'member_phone_number' => 'string',
            'member_email' => 'string',
            'page' => 'integer',
            'per_page' => ['integer', Rule::in([
                \App\Enums\Params\PerPage::Count50,
                \App\Enums\Params\PerPage::Count100,
                \App\Enums\Params\PerPage::Count200,
            ])],
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
            'order_date_from' => __('validation.attributes.order.order_date_from'),
            'order_date_to' => __('validation.attributes.order.order_date_to'),
            'code' => __('validation.attributes.order.code'),
            'product_number' => __('validation.attributes.order.product_number'),
            'delivery_number' => __('validation.attributes.order.delivery_number'),
            'jan_code' => __('validation.attributes.order.jan_code'),
            'status' => __('validation.attributes.order.status'),
            'payment_type' => __('validation.attributes.order.payment_type'),
            'delivery_type' => __('validation.attributes.order.delivery_type'),
            'order_type' => __('validation.attributes.order.order_type'),
            'paid' => __('validation.attributes.order.paid'),
            'deliveryed' => __('validation.attributes.order.deliveryed'),
            'inspected' => __('validation.attributes.order.inspected'),
            'member_name' => __('validation.attributes.order.member_name'),
            'member_phone_number' => __('validation.attributes.order.member_phone_number'),
            'member_email' => __('validation.attributes.order.member_email'),
            'page' => __('validation.attributes.page'),
            'per_page' => __('validation.attributes.per_page'),
        ];
    }
}

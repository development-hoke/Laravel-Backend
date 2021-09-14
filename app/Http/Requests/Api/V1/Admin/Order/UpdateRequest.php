<?php

namespace App\Http\Requests\Api\V1\Admin\Order;

use App\Http\Requests\Api\V1\Request;
use App\Validation\Rules\Kana;
use Illuminate\Validation\Rule;

class UpdateRequest extends Request
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'payment_type' => [Rule::in(\App\Services\Admin\OrderService::getAcceptablePaymentTypes())],
            'delivery_type' => [Rule::in(\App\Enums\Order\DeliveryType::getValues())],
            'delivery_hope_date' => 'date',
            'delivery_hope_time' => [Rule::in(\App\Enums\Order\DeliveryTime::getValues())],
            'order_type' => [Rule::in(\App\Enums\Order\OrderType::getValues())],
            'status' => [Rule::in(\App\Enums\Order\Status::getValues())],
            'delivery_company' => 'nullable|max:100',
            'memo1' => 'nullable|max:10000',
            'memo2' => 'nullable|max:10000',
            'shop_memo' => 'nullable|max:10000',
            'paid' => 'boolean',
            'log_memo' => 'max:10000',
            'delivery_address.lname' => 'max:255',
            'delivery_address.fname' => 'max:255',
            'delivery_address.lkana' => ['max:255', new Kana()],
            'delivery_address.fkana' => ['max:255', new Kana()],
            'delivery_address.zip' => 'max:8',
            'delivery_address.pref_id' => 'exists:prefs,id',
            'delivery_address.city' => 'max:50',
            'delivery_address.town' => 'max:50',
            'delivery_address.address' => 'max:100',
            'delivery_address.building' => 'max:100|nullable',
            'delivery_address.tel' => 'max:50',
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
            'payment_type' => __('validation.attributes.order.payment_type'),
            'delivery_type' => __('validation.attributes.order.delivery_type'),
            'delivery_hope_date' => __('validation.attributes.order.delivery_hope_date'),
            'delivery_hope_time' => __('validation.attributes.order.delivery_hope_time'),
            'order_type' => __('validation.attributes.order.order_type'),
            'paid' => __('validation.attributes.order.paid'),
            'status' => __('validation.attributes.order.status'),
            'delivery_company' => __('validation.attributes.order.delivery_company'),
            'memo1' => __('validation.attributes.order.memo1'),
            'memo2' => __('validation.attributes.order.memo2'),
            'shop_memo' => __('validation.attributes.order.shop_memo'),
            'log_memo' => __('validation.attributes.order_log.log_memo'),
            'delivery_address.lname' => __('validation.attributes.order.delivery_address.lname'),
            'delivery_address.fname' => __('validation.attributes.order.delivery_address.fname'),
            'delivery_address.lkana' => __('validation.attributes.order.delivery_address.lkana'),
            'delivery_address.fkana' => __('validation.attributes.order.delivery_address.fkana'),
            'delivery_address.zip' => __('validation.attributes.order.delivery_address.zip'),
            'delivery_address.prefId' => __('validation.attributes.order.delivery_address.prefId'),
            'delivery_address.city' => __('validation.attributes.order.delivery_address.city'),
            'delivery_address.town' => __('validation.attributes.order.delivery_address.town'),
            'delivery_address.address' => __('validation.attributes.order.delivery_address.address'),
            'delivery_address.building' => __('validation.attributes.order.delivery_address.building'),
            'delivery_address.tel' => __('validation.attributes.order.delivery_address.tel'),
        ];
    }
}

<?php

namespace App\Http\Requests\Api\V1\Admin\Event;

use App\Http\Requests\Api\V1\Request;
use Illuminate\Validation\Rule;

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
            'title' => 'required|max:255',
            'period_from' => "required|date|before:{$this->period_to}",
            'period_to' => 'required|date',
            'target' => ['required', Rule::in(\App\Enums\Event\Target::getValues())],
            'target_user_type' => [Rule::in(\App\Enums\Event\TargetUserType::getValues())],
            'sale_type' => ['required', Rule::in(\App\Enums\Event\SaleType::getValues())],
            'discount_type' => [
                sprintf('required_if:sale_type,%s', \App\Enums\Event\SaleType::Normal),
                Rule::in(\App\Enums\Event\DiscountType::getValues()),
            ],
            'discount_rate' => ['nullable', 'numeric', Rule::requiredIf(function () {
                return (int) $this->discount_type === \App\Enums\Event\DiscountType::Flat
                    && (int) $this->sale_type === \App\Enums\Event\SaleType::Normal;
            })],
            'published' => 'required|boolean',
            'event_bundle_sales' => sprintf('array|required_if:sale_type,%s', \App\Enums\Event\SaleType::Bundle),
            'event_bundle_sales.*.count' => 'required|integer',
            'event_bundle_sales.*.rate' => 'required|numeric',
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
            'title' => __('validation.attributes.event.title'),
            'period_from' => __('validation.attributes.event.period_from'),
            'period_to' => __('validation.attributes.event.period_to'),
            'target' => __('validation.attributes.event.target'),
            'target_user_type' => __('validation.attributes.target_user_type'),
            'sale_type' => __('validation.attributes.event.sale_type'),
            'discount_type' => __('validation.attributes.event.discount_type'),
            'discount_rate' => __('validation.attributes.event.discount_rate'),
            'published' => __('validation.attributes.event.published'),
            'event_bundle_sales.*.count' => __('validation.attributes.event.event_bundle_sales'),
            'event_bundle_sales.*.count' => __('validation.attributes.event_bundle_sale.count'),
            'event_bundle_sales.*.rate' => __('validation.attributes.event_bundle_sale.rate'),
        ];
    }
}

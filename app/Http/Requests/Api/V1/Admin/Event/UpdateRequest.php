<?php

namespace App\Http\Requests\Api\V1\Admin\Event;

use App\Http\Requests\Api\V1\Request;
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
            'id' => 'integer|required',
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
            'event_bundle_sales' => 'array',
            'event_bundle_sales.*.count' => 'required|integer|min:0',
            'event_bundle_sales.*.rate' => 'required|numeric|min:0',
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
            'id' => __('validation.attributes.event.id'),
            'title' => __('validation.attributes.title'),
            'period_from' => __('validation.attributes.period_from'),
            'period_to' => __('validation.attributes.period_to'),
            'target' => __('validation.attributes.target'),
            'sale_type' => __('validation.attributes.sale_type'),
            // 'target_user_type' => __('validation.attributes.target_user_type'),
            'discount_type' => __('validation.attributes.discount_type'),
            'discount_rate' => __('validation.attributes.discount_rate'),
            'published' => __('validation.attributes.published'),
            'event_bundle_sales.*.count' => __('validation.attributes.event.event_bundle_sales'),
            'event_bundle_sales.*.count' => __('validation.attributes.event_bundle_sale.count'),
            'event_bundle_sales.*.rate' => __('validation.attributes.event_bundle_sale.rate'),
        ];
    }

    /**
     * ルート引数は対象にならないのでマージする
     * DEPRECATED: この方法は今後使用しない。(参照: https://github.com/u2ku2k/store.ymdy/pull/260)
     *
     * @return array
     */
    public function validationData()
    {
        return array_merge($this->request->all(), [
            'id' => $this->route('id'),
        ]);
    }
}

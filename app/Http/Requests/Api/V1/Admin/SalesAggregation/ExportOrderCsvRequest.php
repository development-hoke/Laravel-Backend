<?php

namespace App\Http\Requests\Api\V1\Admin\SalesAggregation;

use App\Enums\OrderAggregation\By as AggregationBy;
use App\Enums\OrderAggregation\Unit as AggregationUnit;
use App\Http\Requests\Api\V1\Request;
use Illuminate\Validation\Rule;

class ExportOrderCsvRequest extends Request
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'date_from' => 'required_if:use_default,0|required_without:use_default|date',
            'date_to' => 'required_if:use_default,0|required_without:use_default|date',
            'by' => ['required_if:use_default,0', 'required_without:use_default', Rule::in(AggregationBy::getValues())],
            'unit' => ['required_if:use_default,0', 'required_without:use_default', Rule::in(AggregationUnit::getValues())],
            'use_default' => 'boolean',
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
            'date_from' => __('validation.attributes.sales_aggregation_order.date_from'),
            'date_to' => __('validation.attributes.sales_aggregation_order.date_to'),
            'by' => __('validation.attributes.sales_aggregation_order.by'),
            'unit' => __('validation.attributes.sales_aggregation_order.unit'),
        ];
    }

    /**
     * デフォルトのパラメータ
     *
     * @return array
     */
    public function getDefaultParams()
    {
        return [
            'date_from' => date('Y-m-d 00:00:00', strtotime('-1 month')),
            'date_to' => date('Y-m-d 00:00:00'),
            'by' => AggregationBy::Ordered,
            'unit' => AggregationUnit::Daily,
        ];
    }
}

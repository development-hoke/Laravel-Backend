<?php

namespace App\Http\Requests\Api\V1\Admin\Plan;

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
            'status' => ['nullable', Rule::in(\App\Enums\Plan\Status::getValues())],
            'brand' => 'nullable|integer',
            'page' => 'nullable|integer',
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
            'status' => __('validation.attributes.plan.status'),
            'brand' => __('validation.attributes.plan.brand'),
            'page' => __('validation.page'),
        ];
    }
}

<?php

namespace App\Http\Requests\Api\V1\Admin\Plan;

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
            'slug' => ['required', Rule::unique('plans', 'slug')->whereNull('deleted_at'), 'regex:/^[a-z0-9\-_\/]+$/i', 'max:255'],
            'thumbnail.type' => [Rule::in(\App\Enums\ItemImage\Type::getValues())],
            'thumbnail.raw_image' => sprintf(
                'required|max:%s',
                config('fileupload.default_max_size.csv')
            ),
            'thumbnail.file_name' => 'nullable|max:255',
            'brand' => ['nullable', Rule::in(\App\Enums\Common\StoreBrand::getValues())],
            'place' => ['nullable', Rule::in(\App\Enums\Plan\Place::getValues())],
            'body' => 'required|max:10000',
            'status' => 'required|boolean',
            'period_from' => 'nullable|date',
            'period_to' => 'nullable|date',
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
            'title' => __('validation.attributes.plan.title'),
            'slug' => __('validation.attributes.plan.slug'),
            'thumbnail.type' => __('validation.attributes.plan_thumbnail.type'),
            'brand' => __('validation.attributes.plan.store_brand'),
            'place' => __('validation.attributes.plan.place'),
            'body' => __('validation.attributes.plan.body'),
            'status' => __('validation.attributes.plan.status'),
            'period_from' => __('validation.attributes.plan.period_from'),
            'period_to' => __('validation.attributes.plan.period_to'),
        ];
    }
}

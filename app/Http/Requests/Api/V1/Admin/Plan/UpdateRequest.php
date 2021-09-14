<?php

namespace App\Http\Requests\Api\V1\Admin\Plan;

use App\Http\Requests\Api\V1\Request;
use App\Models\TopContent;
use Carbon\Carbon;
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
            'title' => 'required|max:255',
            'slug' => ['required', 'regex:/^[a-z0-9\-_\/]+$/i', 'max:255', Rule::unique('plans')->where(function ($query) {
                return $query->whereNull('deleted_at');
            })->ignore($this->id)],
            'thumbnail.type' => [Rule::in(\App\Enums\ItemImage\Type::getValues())],
            'thumbnail.raw_image' => sprintf(
                'required_if:thumbnail.is_new,1|max:%s',
                config('fileupload.default_max_size.csv')
            ),
            'thumbnail.file_name' => 'nullable|max:255',
            'store_brand' => ['nullable', Rule::in(\App\Enums\Common\StoreBrand::getValues())],
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
            'store_brand' => __('validation.attributes.plan.store_brand'),
            'place' => __('validation.attributes.plan.place'),
            'body' => __('validation.attributes.plan.body'),
            'status' => __('validation.attributes.plan.status'),
            'period_from' => __('validation.attributes.plan.period_from'),
            'period_to' => __('validation.attributes.plan.period_to'),
        ];
    }

    public function withValidator($validator)
    {
        $features = TopContent::pluck('features')->toArray();
        $news = TopContent::pluck('news')->toArray();

        $plans = array_merge(array_column($features[0], 'plan_id'), array_column($news[0], 'plan_id'));

        if (!in_array($this->id, $plans)) {
            return;
        }

        $validator->after(function ($validator) {
            $message = trans('validation.attributes.plan.displayed_in_top');
            if ($this->filled(['period_from']) && Carbon::now() < new Carbon($this->input('period_from'))) {
                $validator->errors()->add('period_from', $message);
            } elseif (!$this->status) {
                $validator->errors()->add('status', $message);
            }
        });
    }
}

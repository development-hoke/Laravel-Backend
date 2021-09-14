<?php

namespace App\Http\Requests\Api\V1\Admin\Page;

use App\Http\Requests\Api\V1\Request;
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
            'slug' => ['required', 'regex:/^[a-z0-9\-_\/]+$/i', 'max:255', Rule::unique('pages')->where(function ($query) {
                return $query->whereNull('deleted_at');
            })->ignore($this->id)],
            'title' => 'required|max:255',
            'body' => 'required|max:10000',
            'status' => 'required|boolean',
            'publish_from' => 'required|date',
            'publish_to' => 'nullable|date',
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
            'slug' => __('validation.attributes.page.slug'),
            'title' => __('validation.attributes.page.title'),
            'body' => __('validation.attributes.page.body'),
            'status' => __('validation.attributes.page.status'),
            'publish_from' => __('validation.attributes.page.publish_from'),
            'publish_to' => __('validation.attributes.page.publish_to'),
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            if ($this->filled(['publish_to'])) {
                if (new Carbon($this->input('publish_from')) > new Carbon($this->input('publish_to'))) {
                    $validator->errors()->add('publish_from', '公開終了日時よりも前の日時を指定してください。');
                }
            }
        });
    }
}

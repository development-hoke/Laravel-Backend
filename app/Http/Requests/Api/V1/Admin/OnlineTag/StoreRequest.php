<?php

namespace App\Http\Requests\Api\V1\Admin\OnlineTag;

use App\Http\Requests\Api\V1\Request;
use Illuminate\Validation\Rule;

class StoreRequest extends Request
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'id' => 'required|integer|unique:online_tags,id',
            'parent_id' => [
                'nullable',
                'integer',
                Rule::exists('online_tags', 'id')->where(function ($query) {
                    return $query->where('id', '!=', $this->id)
                        ->whereNull('parent_id');
                }),
            ],
            'name' => 'required|max:255',
            'sort' => 'nullable|integer',
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
            'id' => __('validation.attributes.online_tag.id'),
            'parent_id' => __('validation.attributes.online_tag.parent_id'),
            'name' => __('validation.attributes.online_tag.name'),
            'sort' => __('validation.attributes.online_tag.sort'),
        ];
    }
}

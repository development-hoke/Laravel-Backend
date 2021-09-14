<?php

namespace App\Http\Requests\Api\V1\Admin\ItemSort;

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
            'store_brand' => ['nullable', Rule::in(\App\Enums\Common\StoreBrand::getValues())],
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
            'store_brand' => __('validation.attributes.item.main_store_brand'),
        ];
    }
}

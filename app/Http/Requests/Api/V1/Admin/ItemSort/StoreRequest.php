<?php

namespace App\Http\Requests\Api\V1\Admin\ItemSort;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreRequest extends FormRequest
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
            'item_id.*' => [
                'required',
                'integer',
                Rule::exists('items', 'id')->where(function ($query) {
                    $query = $query->whereNull('deleted_at');

                    return !$this->store_brand
                        ? $query
                        : $query->where('main_store_brand', $this->store_brand);
                }),
                Rule::unique('item_sorts', 'item_id')->where(function ($query) {
                    $query = $query->whereNull('deleted_at');

                    return !$this->store_brand
                        ? $query->whereNull('store_brand')
                        : $query->where('store_brand', $this->store_brand);
                }),
            ],
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
            'item_id.*' => __('validation.attributes.item.id'),
        ];
    }
}

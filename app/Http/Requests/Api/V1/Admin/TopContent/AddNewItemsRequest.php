<?php

namespace App\Http\Requests\Api\V1\Admin\TopContent;

use App\Http\Requests\Api\V1\Request;
use App\Models\TopContent;
use Illuminate\Validation\Rule;

class AddNewItemsRequest extends Request
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $topContent = TopContent::findOrFail($this->id);

        return [
            'item_id.*' => [
                'required',
                'integer',
                Rule::exists('items', 'id')->where(function ($query) use ($topContent) {
                    $query = $query->whereNull('deleted_at');

                    return $topContent->store_brand === null
                        ? $query
                        : $query->where('main_store_brand', $topContent->store_brand);
                }),
                Rule::notIn(array_column($topContent->new_items, 'item_id')),
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
            'item_id.*' => __('validation.attributes.top_content.item_id'),
        ];
    }

    /**
     * @return array
     */
    public function messages()
    {
        return [
            'item_id.*.not_in' => __('validation.top_content.not_in'),
        ];
    }
}

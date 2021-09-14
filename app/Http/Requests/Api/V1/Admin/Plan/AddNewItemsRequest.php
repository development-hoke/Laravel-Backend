<?php

namespace App\Http\Requests\Api\V1\Admin\Plan;

use App\Http\Requests\Api\V1\Request;
use App\Models\Plan;
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
        $plan = Plan::findOrFail($this->id);

        return [
            'item_id.*' => [
                'required',
                'integer',
                Rule::exists('items', 'id')->where(function ($query) use ($plan) {
                    $query = $query->whereNull('deleted_at');

                    return $plan->store_brand === null
                        ? $query
                        : $query->where('main_store_brand', $plan->store_brand);
                }),
                Rule::notIn(array_column($plan->planItems->toArray(), 'item_id')),
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
            'item_id.*' => __('validation.attributes.plan.item_id'),
        ];
    }

    /**
     * @return array
     */
    public function messages()
    {
        return [
            'item_id.*.not_in' => __('validation.plan.not_in'),
        ];
    }
}

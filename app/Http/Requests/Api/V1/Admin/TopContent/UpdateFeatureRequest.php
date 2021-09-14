<?php

namespace App\Http\Requests\Api\V1\Admin\TopContent;

use App\Http\Requests\Api\V1\Request;
use App\Models\TopContent;
use Illuminate\Validation\Rule;

class UpdateFeatureRequest extends Request
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
            'plan_id.*' => [
                'required',
                'integer',
                Rule::exists('plans', 'id')->where(function ($query) use ($topContent) {
                    $query = $query->whereNull('deleted_at');

                    return $topContent->store_brand === null
                        ? $query
                        : $query->where('store_brand', $topContent->store_brand);
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
            'plan_id.*' => __('validation.attributes.top_content.plan_id'),
        ];
    }
}

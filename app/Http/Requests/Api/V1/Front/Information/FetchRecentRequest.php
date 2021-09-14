<?php

namespace App\Http\Requests\Api\V1\Front\Information;

use App\Http\Requests\Api\V1\Request;

class FetchRecentRequest extends Request
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'excluded_id' => 'nullable|integer|exists:informations,id',
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
            'excluded_id' => __('validation.attributes.information.excluded_id'),
        ];
    }
}

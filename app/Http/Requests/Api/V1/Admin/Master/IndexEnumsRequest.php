<?php

namespace App\Http\Requests\Api\V1\Admin\Master;

use App\Http\Requests\Api\V1\Request;

class IndexEnumsRequest extends Request
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array
     */
    public function attributes()
    {
        return [];
    }
}

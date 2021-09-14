<?php

namespace App\Http\Requests\Api\V1\Front\Member\Favorite;

use App\Http\Requests\Api\V1\Front\BaseMemberRequest;

class IndexRequest extends BaseMemberRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
        ] + parent::rules();
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array
     */
    public function attributes()
    {
        return [
        ] + parent::attributes();
    }
}

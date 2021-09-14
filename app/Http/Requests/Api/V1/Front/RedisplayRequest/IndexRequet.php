<?php

namespace App\Http\Requests\Api\V1\Front\RedisplayRequest;

use App\Http\Requests\Api\V1\Front\BaseRequest;
use Illuminate\Validation\Rule;

class IndexRequet extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'user_token' => ['string', 'max:255', Rule::requiredIf(function () {
                return !auth('api')->check();
            })],
        ];
    }

    /**
     * @return array
     */
    public function attributes()
    {
        return [
            'user_token' => __('validation.attributes.item_detail_redisplay_request.user_token'),
        ];
    }
}

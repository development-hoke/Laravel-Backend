<?php

namespace App\Http\Requests\Api\V1\Front\RedisplayRequest;

use App\Http\Requests\Api\V1\Front\BaseRequest;
use Illuminate\Validation\Rule;

class ValidateEmailRequet extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'item_detail_id' => 'required|integer|exists:item_details,id,deleted_at,NULL',
            'email' => ['required', 'string', 'max:255', Rule::unique('item_detail_redisplay_requests', 'email')->where(function ($query) {
                return $query->where('item_detail_id', $this->item_detail_id)->whereNull('deleted_at')->where('is_notified', false);
            })],
        ];
    }

    /**
     * @return array
     */
    public function attributes()
    {
        return [
            'item_detail_id' => __('validation.attributes.item_detail_redisplay_request.item_detail_id'),
            'email' => __('validation.attributes.item_detail_redisplay_request.email'),
        ];
    }
}

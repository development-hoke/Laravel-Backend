<?php

namespace App\Http\Requests\Api\V1\Front\RedisplayRequest;

use App\Http\Requests\Api\V1\Front\BaseRequest;
use Illuminate\Validation\Rule;

class StoreRequet extends BaseRequest
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
            'user_token' => 'required|string|min:32|max:255',
            'user_name' => 'required|string|max:255',
            'email' => ['required', 'string', 'max:255', 'email', Rule::unique('item_detail_redisplay_requests', 'email')->where(function ($query) {
                return $query->where('item_detail_id', $this->item_detail_id)->whereNull('deleted_at')->where('is_notified', false);
            })],
            'email_confirmation' => 'required|same:email',
        ];
    }

    /**
     * @return array
     */
    public function attributes()
    {
        return [
            'item_detail_id' => __('validation.attributes.item_detail_redisplay_request.item_detail_id'),
            'user_token' => __('validation.attributes.item_detail_redisplay_request.user_token'),
            'user_name' => __('validation.attributes.item_detail_redisplay_request.user_name'),
            'email' => __('validation.attributes.item_detail_redisplay_request.email'),
            'email_confirmation' => __('validation.attributes.item_detail_redisplay_request.email_confirmation'),
        ];
    }
}

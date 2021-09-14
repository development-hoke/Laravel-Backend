<?php

namespace App\Http\Requests\Api\V1\Admin\EventItem;

use App\Http\Requests\Api\V1\Request;
use App\Models\Item as ItemModel;
use App\Validation\Rules\Unique;

class StoreRequest extends Request
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $item = ItemModel::where('product_number', $this->product_number)->first();
        $max = (float) $item->price_change_rate;

        return [
            'event_id' => 'required|integer|exists:events,id,deleted_at,NULL',
            'product_number' => [
                'required',
                'string',
                'max:255',
                'exists:items,product_number,deleted_at,NULL',
                Unique::newInstance(\App\Models\EventItem::class)->where(function ($query) {
                    return $query->whereEventIdAndProductNumber($this->event_id, $this->product_number);
                }),
            ],
            'discount_rate' => "required|numeric|max:{$max}",
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
            'event_id' => __('validation.attributes.event_item.event_id'),
            'product_number' => __('validation.attributes.item.product_number'),
            'discount_rate' => __('validation.attributes.event_item.discount_rate'),
        ];
    }

    /**
     * ルート引数は対象にならないのでマージする
     * DEPRECATED: この方法は今後使用しない。(参照: https://github.com/u2ku2k/store.ymdy/pull/260)
     *
     * @return array
     */
    public function validationData()
    {
        return array_merge($this->request->all(), [
            'event_id' => $this->route('event_id'),
        ]);
    }

    public function messages()
    {
        return [
            'discount_rate.max' => __('validation.item_discount_rate_max'),
        ];
    }
}

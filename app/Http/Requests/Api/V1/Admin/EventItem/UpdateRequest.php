<?php

namespace App\Http\Requests\Api\V1\Admin\EventItem;

use App\Http\Requests\Api\V1\Request;
use App\Models\Item as ItemModel;
use App\Validation\Rules\Unique;

class UpdateRequest extends Request
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
            'id' => 'required|integer',
            'event_id' => 'required|integer',
            'product_number' => [
                'string',
                'max:255',
                'exists:items,product_number,deleted_at,NULL',
                Unique::newInstance(\App\Models\EventItem::class)->where(function ($query) {
                    return $query->where('event_items.id', '!=', $this->id)
                        ->whereEventIdAndProductNumber($this->event_id, $this->product_number);
                }),
            ],
            'discount_rate' => "numeric|max:{$max}",
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
            'id' => __('validation.attributes.event_item.id'),
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
            'id' => $this->route('id'),
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

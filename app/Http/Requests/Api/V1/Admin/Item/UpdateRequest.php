<?php

namespace App\Http\Requests\Api\V1\Admin\Item;

use App\Domain\Utils\ItemPrice;
use App\Http\Requests\Api\V1\Request;
use App\Models\Item as ItemModel;
use Illuminate\Validation\Rule;

class UpdateRequest extends Request
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $item = ItemModel::findOrFail($this->id);
        $max = ItemPrice::computeMaximumDiscountRate($item);
        $salesPeriodFromRule = [
            'required',
            'date',
            "before:{$this->sales_period_to}",
        ];
        if ($this->input('discount_rate') > 0 || $this->input('member_discount_rate') > 0) {
            array_push($salesPeriodFromRule, "after_or_equal:{$item->price_change_period}");
        }

        return [
            'status' => 'required|boolean',
            'main_store_brand' => ['required', Rule::in(\App\Enums\Common\StoreBrand::getValues())],
            'name' => 'required|max:255',
            'display_name' => 'required|max:255',
            'discount_rate' => [
                'required',
                'numeric',
                "max:{$max}",
            ],
            'is_member_discount' => 'required|boolean',
            'member_discount_rate' => [
                'nullable',
                'numeric',
                "max:{$max}",
                Rule::requiredIf((bool) ((int) $this->input('is_member_discount'))),
            ],
            'sales_period_from' => $salesPeriodFromRule,
            'sales_period_to' => 'required|date',
            'sales_status' => ['required', Rule::in(\App\Enums\Item\SalesStatus::getValues())],
            'description' => 'required|max:10000',
            'size_optional_info' => 'nullable|max:10000',
            'size_caution' => 'nullable|max:10000',
            'material_info' => 'nullable|max:10000',
            'material_caution' => 'nullable|max:10000',
            'is_manually_setting_recommendation' => 'required|boolean',
            'back_orderble' => 'required|boolean',
            'returnable' => 'boolean',

            // item_details
            'item_details' => 'array|required',
            'item_details.*.id' => [
                'required',
                'integer',
                Rule::exists('item_details', 'id')->where(function ($query) {
                    $query->where('item_id', $this->id)->whereNull('deleted_at');
                }),
            ],
            'item_details.*.sort' => 'required|integer',
            'item_details.*.status' => ['required', Rule::in(\App\Enums\Common\Status::getValues())],
            'item_details.*.redisplay_requested' => 'required|boolean',

            // item_images
            'item_images' => 'array',
            'item_images.*.type' => [Rule::in(\App\Enums\ItemImage\Type::getValues())],
            'item_images.*.url' => 'required_if:item_images.*.is_new,0|max:255',
            'item_images.*.raw_image' => sprintf(
                'required_if:item_images.*.is_new,1|max:%s',
                config('fileupload.default_max_size.csv')
            ),
            'item_images.*.caption' => 'nullable|max:255',
            'item_images.*.file_name' => 'nullable|max:255',
            'item_images.*.color_id' => 'nullable|integer|exists:colors,id',
            'item_images.*.sort' => 'required|integer',
            'item_images.*.is_new' => 'required|boolean',

            // その他関連テーブル
            'online_category_id.*' => 'integer|exists:online_categories,id',
            'online_tag_id.*' => 'integer|exists:online_tags,id',

            'item_sub_brands.*' => [Rule::in(\App\Enums\Common\StoreBrand::getValues())],
            'brand_id' => 'required|integer',
            'sales_types' => 'array',
            'sales_types.*.id' => 'integer|required|exists:sales_types,id',
            'sales_types.*.sort' => 'integer|required',

            'coordinate_id' => 'array',
            'coordinate_id.*' => 'integer|required',

            'items_used_same_styling_used_item_id.*' => 'required|integer|exists:items,id,deleted_at,NULL',

            'recommend_item_id.*' => 'required|integer|exists:items,id,deleted_at,NULL',
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
            'status' => __('validation.attributes.item.status'),
            'main_store_brand' => __('validation.attributes.item.main_store_brand'),
            'item_sub_brands.*' => __('validation.attributes.item_sub_brand.sub_store_brand'),

            'brand_id' => __('validation.attributes.item.brand_id'),
            'name' => __('validation.attributes.item.name'),
            'discount_rate' => __('validation.attributes.item.discount_rate'),
            'is_member_discount' => __('validation.attributes.item.is_member_discount'),
            'member_discount_rate' => __('validation.attributes.item.member_discount_rate'),
            'sales_period_from' => __('validation.attributes.item.sales_period_from'),
            'sales_period_to' => __('validation.attributes.item.sales_period_to'),
            'sales_status' => __('validation.attributes.item.sales_status'),
            'sales_type.*.id' => __('validation.attributes.sales_type.id'),
            'sales_type.*.sort' => __('validation.attributes.sales_type.sort'),
            'online_category_id.*' => __('validation.attributes.online_category_id'),
            'online_tag_id.*' => __('validation.attributes.online_tag_id'),
            'description' => __('validation.attributes.item.description'),

            'size_optional_info' => __('validation.attributes.item.size_optional_info'),
            'size_caution' => __('validation.attributes.item.size_caution'),

            'material_info' => __('validation.attributes.item.material_info'),
            'material_caution' => __('validation.attributes.item.material_caution'),
            'is_manually_setting_recommendation' => __('validation.attributes.item.is_manually_setting_recommendation'),

            'item_images.*.type' => __('validation.attributes.item_image.type'),
            'item_images.*.url' => __('validation.attributes.item_image.url'),
            'item_images.*.caption' => __('validation.attributes.item_image.caption'),
            'item_images.*.color_id' => __('validation.attributes.item_image.color_id'),
            'item_images.*.sort' => __('validation.attributes.item_image.sort'),

            'item_details.*.sort' => __('validation.attributes.item_detail.sort'),
            'item_details.*.status' => __('validation.attributes.item_detail.status'),
            'item_details.*.redisplay_requested' => __('validation.attributes.item_detail.redisplay_requested'),

            'items_used_same_stylings.*.item_id' => __('validation.attributes.items_used_same_styling.item_id'),
            'recommend_item_id.*' => __('validation.attributes.recommend_item_id'),

            'sales_types' => __('resource.sales_type'),
            'sales_types.*.id' => __('validation.attributes.sales_type.id'),
            'sales_types.*.sort' => __('validation.attributes.sales_type.sort'),
        ];
    }

    public function messages()
    {
        return [
            'discount_rate.max' => __('validation.item_discount_rate_max'),
            'member_discount_rate.max' => __('validation.item_discount_rate_max'),
            'sales_period_from.after_or_equal' => __('validation.event.product_date'),
        ];
    }
}

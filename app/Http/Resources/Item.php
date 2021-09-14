<?php

namespace App\Http\Resources;

use App\Domain\Utils\ItemPrice;
use Illuminate\Http\Resources\Json\JsonResource;

class Item extends JsonResource
{
    /**
     * Transform the resource collection into an array.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return array
     */
    public function toArray($request)
    {
        $reserveText = '通常販売';
        if ($this->itemReserve && $this->itemReserve->is_enable) {
            $reserveText = '予約販売';
        }

        return [
            'id' => $this->id,
            'term_id' => $this->term_id,
            'season_id' => $this->season_id,
            'organization_id' => $this->organization_id,
            'division_id' => $this->division_id,
            'department_id' => $this->department_id,
            'product_number' => $this->product_number,
            'maker_product_number' => $this->maker_product_number,
            'fashion_speed' => $this->fashion_speed,
            'name' => $this->name,
            'old_jan_code' => $this->old_jan_code,
            'retail_price' => $this->retail_price,
            'discounted_price' => ItemPrice::calcDiscountedPrice($this),
            'price_change_period' => $this->price_change_period,
            'price_change_rate' => $this->price_change_rate,
            'main_store_brand' => $this->main_store_brand,
            'brand_id' => $this->brand_id,
            'display_name' => $this->display_name,
            'discount_rate' => $this->discount_rate,
            'is_member_discount' => $this->is_member_discount,
            'member_discount_rate' => $this->member_discount_rate,
            'point_rate' => $this->point_rate,
            'sales_period_from' => $this->sales_period_from,
            'sales_period_to' => $this->sales_period_to,
            'description' => $this->description,
            'note_staff_ok' => $this->note_staff_ok,
            'size_optional_info' => $this->size_optional_info,
            'size_caution' => $this->size_caution,
            'material_info' => $this->material_info,
            'material_caution' => $this->material_caution,
            'sales_status' => $this->sales_status,
            'status' => $this->status,
            'status_text' => $this->status == 1 ? '公開中' : '非公開',
            'is_manually_setting_recommendation' => $this->is_manually_setting_recommendation,
            'returnable' => $this->returnable,
            'back_orderble' => $this->back_orderble,
            'ec_stock' => $this->when($this->resource->relationLoaded('itemDetails'), function () {
                return $this->itemDetails->sum('ec_stock');
            }),
            'item_details' => ItemDetail::collection($this->whenLoaded('itemDetails')),
            'item_images' => ItemImage::collection($this->whenLoaded('itemImages')),
            'backend_item_images' => ItemImage::collection($this->whenLoaded('backendItemImages')),
            'sales_types' => SalesType::collection($this->whenLoaded('salesTypes')),
            'online_tags' => OnlineTag::collection($this->whenLoaded('onlineTags')),
            'online_categories' => OnlineCategory::collection($this->whenLoaded('onlineCategories')),
            'items_used_same_stylings' => static::collection($this->whenLoaded('itemsUsedSameStylings')),
            'item_sub_brands' => ItemSubBrand::collection($this->whenLoaded('itemSubBrands')),
            'reserveText' => $reserveText,
            'recommend_items' => static::collection($this->whenLoaded('recommendItems')),
            'brand' => new Brand($this->whenLoaded('brand')),
            'department' => new Department($this->whenLoaded('department')),
            'season' => new Season($this->whenLoaded('season')),
        ];
    }
}

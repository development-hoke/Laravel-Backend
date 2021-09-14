<?php

namespace App\Http\Resources\Front;

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
        $array = [
            // items
            'id' => $this->id,
            'term_id' => $this->term_id,
            'season_id' => $this->season_id,
            'organization_id' => $this->organization_id,
            'division_id' => $this->division_id,
            'department_id' => $this->department_id,
            'product_number' => $this->product_number,
            'maker_product_number' => $this->maker_product_number_display,
            'fashion_speed' => $this->fashion_speed,
            'name' => $this->name,
            'retail_price' => $this->retail_price,
            'price_change_period' => $this->price_change_period,
            'price_change_rate' => $this->price_change_rate,
            'main_store_brand' => $this->main_store_brand,
            'brand_id' => $this->brand_id,
            'display_name' => $this->display_name,
            'discount_rate' => $this->discount_rate,
            'discount_rate_updated_at' => $this->discount_rate_updated_at,
            'is_member_discount' => $this->is_member_discount,
            'member_discount_rate' => $this->member_discount_rate,
            'member_discount_rate_updated_at' => $this->member_discount_rate_updated_at,
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
            'returnable' => $this->returnable,

            // 付加データ
            'is_favorite' => $this->is_favorite ?? null,
            'displayed_sale_price' => $this->displayed_sale_price ?? null,
            'price_before_order' => $this->price_before_order ?? null,
            'is_sold_out' => $this->is_sold_out ?? null,
            'displayed_discount_type' => $this->displayed_discount_type ?? null,
            'can_display_original_price' => $this->can_display_original_price ?? null,
            'is_reservation' => $this->is_reservation ?? null,

            // リレーション
            'item_details' => ItemDetail::collection($this->whenLoaded('itemDetails')),
            'sales_types' => \App\Http\Resources\SalesType::collection($this->whenLoaded('salesTypes')),
            'online_tags' => \App\Http\Resources\OnlineTag::collection($this->whenLoaded('onlineTags')),
            'online_categories' => \App\Http\Resources\OnlineCategory::collection($this->whenLoaded('onlineCategories')),
            'items_used_same_stylings' => static::collection($this->whenLoaded('itemsUsedSameStylings')),
            'item_sub_brands' => \App\Http\Resources\ItemSubBrand::collection($this->whenLoaded('itemSubBrands')),
            'recommend_items' => static::collection($this->whenLoaded('recommendItems')),
            'brand' => new \App\Http\Resources\Brand($this->whenLoaded('brand')),
            'department' => new \App\Http\Resources\Department($this->whenLoaded('department')),
            'applied_reservation' => new \App\Http\Resources\ItemReserve($this->whenLoaded('appliedReservation')),
        ];

        if ($this->resource->relationLoaded('itemImages')) {
            $array['item_images'] = \App\Http\Resources\ItemImage::collection($this->itemImages);
        } elseif ($this->resource->relationLoaded('nonSortItemImages')) {
            $array['item_images'] = \App\Http\Resources\ItemImage::collection($this->nonSortItemImages);
        }

        return $array;
    }
}

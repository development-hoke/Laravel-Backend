<?php

namespace App\Http\Resources;

use App\Enums\Coupon\DiscountItemFlag;
use App\Enums\Coupon\TargetItemType;
use App\Enums\Coupon\TargetShopType;
use App\Http\Resources\ItemDetail as ItemDetailResource;
use App\Http\Resources\Store as StoreResource;
use App\Models\ItemDetail;
use App\Models\ItemDetailIdentification;
use App\Models\Store;
use Illuminate\Http\Resources\Json\JsonResource;

class Coupon extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return array
     */
    public function toArray($request)
    {
        $usageCondition = null;
        if ($this['usage_amount_term_flag']) {
            $usageAmountMin = $this['usage_amount_minimum'];
            $usageAmountMax = $this['usage_amount_maximum'];
            if ($usageAmountMin > 0 && $usageAmountMax > 0) {
                $usageCondition = number_format($usageAmountMin) . '円以上' . number_format($usageAmountMax) . '円以下の購買の場合に利用できます。';
            } elseif ($usageAmountMin > 0) {
                $usageCondition = number_format($usageAmountMin) . '円以上購買の場合に利用できます。';
            } elseif ($usageAmountMax > 0) {
                $usageCondition = number_format($usageAmountMax) . '円以下購買の場合に利用できます。';
            }
        }

        if ($this['discount_item_flag'] === DiscountItemFlag::ItemDiscount) {
            $discount_text = $this['discount_rate'] ? '商品合計金額の'.$this['discount_rate'] .'%分' : $this['discount_amount'].'円分';
        } else {
            $discount_text = '送料無料';
        }

        if (is_array($this['shop_data'])) {
            $stores = Store::whereIn('id', $this['shop_data'])->get();
        }

        if (is_array($this['_item_cd_data'])) {
            $janCodeData = $this['_item_cd_data'];
            $items = ItemDetail::whereExists(function ($query) use ($janCodeData) {
                $query->select('*')
                    ->from(with(new ItemDetailIdentification())->getTable())
                    ->whereIn('jan_code', $janCodeData);
            })->whereIn('id', $this['item_data'])->get()
            ->load(['color', 'size', 'item', 'item.brand']);
        }

        return [
            'id' => $this['id'],
            'name' => $this['name'],
            'image_path' => $this['image_path'],
            'start_dt' => $this['start_dt'],
            'end_dt' => $this['end_dt'],
            'discount_amount' => $this['discount_amount'],
            'discount_rate' => $this['discount_rate'],
            'discount_item_flag' => $this['discount_item_flag'],
            // todo: 会員ポイント側で　discount_item_flag　が1固定で返ってきてるバグがあるので、rate/amontで判定
            // 'discount_text' => $this['discount_item_flag'] === \App\Enums\Coupon\DiscountType::Percentile ? ($this['discount_rate'] * 10).'%分' : $this['discount_amount'].'円分',
            'discount_text' => $discount_text,
            'target_item_type' => $this['target_item_type'],
            'is_target_item_specified' => $this['target_item_type'] === TargetItemType::Specified,
            'item_data' => $this['item_data'],
            'items' => ItemDetailResource::collection($items ?? []),
            'target_shop_type' => $this['target_shop_type'],
            'is_target_shop_specified' => $this['target_shop_type'] === TargetShopType::Specified,
            'shop_data' => StoreResource::collection($stores ?? []),
            'usage_condition' => $usageCondition ?? '',
            'description' => $this['description'],
            'usage_number_limit' => $this['usage_number_limit'],
            // todo: 会員ポイント側で返却値追加してもらう予定
            'usage_number_rest' => $this['usage_number_rest'] ?? $this['usage_number_limit'],
        ];
    }
}

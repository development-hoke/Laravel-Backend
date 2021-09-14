<?php

namespace App\Models;

use App\Models\Contracts\Loggable;
use App\Models\Traits\Logging;
use App\Models\Traits\OrderSoftDeletes;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Log;

class OrderUsedCoupon extends Model implements Loggable
{
    use OrderSoftDeletes;
    use SoftDeletes;
    use Logging;

    protected $fillable = [
        'order_id',
        'coupon_id',
        'target_order_detail_ids',
        'update_staff_id',
    ];

    protected $casts = [
        'target_order_detail_ids' => 'array',
    ];

    protected $dispatchesEvents = [
        'saved' => \App\Events\Model\SavedOrderUsedCoupon::class,
    ];

    /**
     * 商品に適用された割引額
     *
     * @return int
     */
    public function getItemAppliedPriceAttribute()
    {
        if (!$this->relationLoaded('itemDiscount')) {
            Log::warning(__('error.getter_lazyload'), [__METHOD__]);
        }

        if (empty($this->itemDiscount)) {
            return 0;
        }

        return (int) $this->itemDiscount->applied_price;
    }

    /**
     * 配送料に適用された割引額
     *
     * @return int
     */
    public function getDeliveryFeeAppliedPriceAttribute()
    {
        if (!$this->relationLoaded('deliveryFeeDiscount')) {
            Log::warning(__('error.getter_lazyload'), [__METHOD__]);
        }

        if (empty($this->deliveryFeeDiscount)) {
            return 0;
        }

        return (int) $this->deliveryFeeDiscount->applied_price;
    }

    /**
     * 配送料と商品金額に適用された割引額合計
     *
     * @return int
     */
    public function getTotalAppliedPriceAttribute()
    {
        return $this->item_applied_price + $this->delivery_fee_applied_price;
    }

    /**
     * @return MorphOne
     */
    public function itemDiscount(): MorphOne
    {
        return $this->morphOne(OrderDiscount::class, 'discountable')
            ->where('type', \App\Domain\Utils\OrderDiscount::getCouponItemDiscountType());
    }

    /**
     * @return MorphOne
     */
    public function deliveryFeeDiscount(): MorphOne
    {
        return $this->morphOne(OrderDiscount::class, 'discountable')
            ->where('type', \App\Enums\OrderDiscount\Type::CouponDeliveryFee);
    }

    /**
     * @param int|null $staffId
     *
     * @return \App\Models\OrderUsedCoupon
     */
    public function deleteRelatedItemOrderDiscount($staffId = null)
    {
        if (!empty($this->itemDiscount)) {
            $this->itemDiscount->softDeleteBy($staffId);
        }

        $this->unsetRelation('itemDiscount');

        return $this;
    }

    /**
     * @param int|null $staffId
     *
     * @return \App\Models\OrderUsedCoupon
     */
    public function deleteRelatedDeliveryFeeOrderDiscount($staffId = null)
    {
        if (!empty($this->deliveryFeeDiscount)) {
            $this->deliveryFeeDiscount->softDeleteBy($staffId);
        }

        $this->unsetRelation('deliveryFeeDiscount');

        return $this;
    }
}

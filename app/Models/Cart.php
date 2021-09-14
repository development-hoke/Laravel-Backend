<?php

namespace App\Models;

use App\Domain\Utils\ItemPrice;
use App\Exceptions\FatalException;
use App\Models\Contracts\Timestampable;
use App\Models\Traits\HasOptionalTimestampsTrait;
use App\Utils\Tax;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Cart extends Model implements Timestampable
{
    use SoftDeletes;
    use HasOptionalTimestampsTrait;

    protected $fillable = [
        'token',
        'member_id',
        'use_coupon_ids',
        'order_type',
        'guest_token',
        'guest_token_created_at',
        'guest_verified',
        'guest_verified_at',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $casts = [
        'use_coupon_ids' => 'json',
        'guest_verified' => 'boolean',
        'guest_token_created_at' => 'datetime',
        'guest_verified_at' => 'datetime',
    ];

    /**
     * キーのカラムが更新されると値のカラムに新しいタイムスタンプが入るように登録する。
     *
     * @var array
     */
    protected $optionalTimestampMap = [
        'guest_token' => 'guest_token_created_at:if_true',
        'guest_verified' => 'guest_verified_at:if_true',
    ];

    /**
     * @var array
     */
    protected $dispatchesEvents = [
        'updating' => \App\Events\Model\UpdatingCart::class,
    ];

    /**
     * MemberCoupon
     *
     * @return \App\Entities\Collection
     */
    public function getMemberCouponsAttribute()
    {
        if (empty($this->attributes['member_coupons'])) {
            return \App\Entities\Ymdy\Member\MemberCoupon::collection([]);
        }

        return $this->attributes['member_coupons'];
    }

    public function setMemberCouponsAttribute($memberCoupons)
    {
        $this->attributes['member_coupons'] = is_array($memberCoupons)
            ? \App\Entities\Ymdy\Member\MemberCoupon::collection($memberCoupons)
            : $memberCoupons;
    }

    /**
     * @return bool
     */
    public function getIsGuestAttribute()
    {
        return !empty($this->guest_token);
    }

    public function setIsGuestAttribute()
    {
        throw new FatalException(__('error.not_allowed_to_set'));
    }

    /**
     * @return HasMany
     */
    public function cartItems(): HasMany
    {
        return $this->hasMany(CartItem::class);
    }

    /**
     * @return int
     */
    public function getTotalItemPriceBeforeOrderAttribute()
    {
        return $this->getSecuredCartItems()->sum('total_price_before_order');
    }

    public function setTotalItemPriceBeforeOrderAttribute($value)
    {
        throw new FatalException(__('error.not_allowed_to_set'));
    }

    /**
     * 在庫確保が有効なcartItems
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getSecuredCartItems(): Collection
    {
        $cartItems = $this->cartItems;

        if ($cartItems->isEmpty()) {
            return $cartItems;
        }

        if (!\App\Domain\Utils\Stock::isSecurableOrderType($this->order_type)) {
            return $cartItems;
        }

        return $cartItems->filter(function ($item) {
            return !$item->lapsed;
        });
    }

    /**
     * 無効になったcartItems
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getLapsedCartItems(): Collection
    {
        $cartItems = $this->cartItems;

        if ($cartItems->isEmpty()) {
            return $cartItems;
        }

        if (!\App\Domain\Utils\Stock::isSecurableOrderType($this->order_type)) {
            return (new CartItem())->newCollection([]);
        }

        return $cartItems->filter(function ($item) {
            return $item->lapsed;
        });
    }

    /**
     * カート商品データを会員ポイントシステム向けに整形
     *
     * @return array[]
     */
    public function getItemsForYmdyAttribute()
    {
        return array_map(function ($cartItem) {
            $itemDetail = ItemDetail::find($cartItem['item_detail_id']);
            $item = $itemDetail->item;
            $price = ItemPrice::calcDiscountedPrice($item);

            return [
                'item_id' => $cartItem['item_id'],
                'unit_price' => $item->retail_price,
                'sales_unit_price' => $price,
                'sales_num' => $cartItem['count'],
                'tax' => Tax::calcTax($price),
                'not_item_sales_div' => config('constants.point.not_item_sales_div'),
            ];
        }, $this->cartItems);
    }

    /**
     * CartItemにorder_typeを設定する
     *
     * @return static
     */
    public function assginOrderTypeToCartItems()
    {
        $orderType = $this->order_type;

        foreach ($this->cartItems as $cartItem) {
            $cartItem->order_type = $orderType;
        }

        return $this;
    }
}

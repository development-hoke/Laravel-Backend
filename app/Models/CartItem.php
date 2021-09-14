<?php

namespace App\Models;

use App\Exceptions\FatalException;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Log;

class CartItem extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'cart_id',
        'item_detail_id',
        'closed_market_id',
        'count',
        'invalid',
        'invalid_reason',
        'posted_at',
    ];

    /**
     * NOTE: price_before_orderはテーブルの持つ値ではなく、ItemPrice->fillPriceBeforeOrderで計算された値
     *
     * @return int
     *
     * @throws FatalException
     */
    public function getPriceBeforeOrderAttribute()
    {
        if ($this->relationLoaded('itemDetail')) {
            Log::warning(__('error.getter_lazyload'), [__METHOD__]);
        }

        if (!isset($this->itemDetail->item->price_before_order)) {
            throw new FatalException(__('error.invalid_access_to_custom_attribute', ['method' => __METHOD__]));
        }

        return $this->itemDetail->item->price_before_order;
    }

    public function setPriceBeforeOrderAttribute($value)
    {
        throw new FatalException(__('error.not_allowed_to_set'));
    }

    /**
     * NOTE: price_before_orderはテーブルの持つ値ではなく、ItemPrice->fillPriceBeforeOrderで計算された値
     *
     * @return int
     */
    public function getTotalPriceBeforeOrderAttribute()
    {
        return $this->price_before_order * $this->count;
    }

    public function setTotalPriceBeforeOrderAttribute($value)
    {
        throw new FatalException(__('error.not_allowed_to_set'));
    }

    /**
     * @return bool
     */
    public function getIsClosedMarketAttribute()
    {
        return !empty($this->closed_market_id);
    }

    public function setIsClosedMarketAttribute($value)
    {
        throw new FatalException(__('error.not_allowed_to_set'));
    }

    /**
     * @return bool
     */
    public function getExpiredAttribute()
    {
        return \App\Domain\Utils\Cart::hasNoTime($this);
    }

    public function setExpiredAttribute()
    {
        throw new FatalException(__('error.not_allowed_to_set'));
    }

    /**
     * 失効した商品。論理削除とは別のロジック。
     *
     * @return bool
     */
    public function getLapsedAttribute()
    {
        return $this->invalid || $this->getExpiredAttribute();
    }

    public function setLapsedAttribute()
    {
        throw new FatalException(__('error.not_allowed_to_set'));
    }

    /**
     * order_typeの値を取得
     *
     * @return int
     */
    public function getOrderTypeAttribute()
    {
        if (isset($this->attributes['order_type'])) {
            return $this->attributes['order_type'];
        }

        if (!$this->relationLoaded('cart')) {
            Log::warning(__('error.getter_lazyload'), [__METHOD__]);
        }

        $this->attributes['order_type'] = $this->cart->order_type;

        return $this->attributes['order_type'];
    }

    /**
     * order_typeの設定
     *
     * @param int $value
     *
     * @return void
     */
    public function setOrderTypeAttribute(int $value)
    {
        if (!in_array($value, \App\Enums\Order\OrderType::getValues(), true)) {
            throw new \InvalidArgumentException(__('error.invalid_argument_value'));
        }

        $this->attributes['order_type'] = $value;
    }

    /**
     * @return BelongsTo
     */
    public function itemDetail(): BelongsTo
    {
        return $this->belongsTo(ItemDetail::class);
    }

    /**
     * @return BelongsTo
     */
    public function cart(): BelongsTo
    {
        return $this->belongsTo(Cart::class);
    }

    /**
     * @return BelongsTo
     */
    public function closedMarket(): BelongsTo
    {
        return $this->belongsTo(ClosedMarket::class);
    }
}

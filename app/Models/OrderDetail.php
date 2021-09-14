<?php

namespace App\Models;

use App\Database\Eloquent\CustomPaginationBuilder;
use App\Domain\Utils\OrderPrice;
use App\Exceptions\FatalException;
use App\Models\Contracts\Loggable;
use App\Models\Traits\Logging;
use App\Models\Traits\OrderSoftDeletes;
use App\Models\Traits\QueryHelperTrait;
use App\Models\Traits\RelationTrait;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Log;

class OrderDetail extends Model implements Loggable
{
    use Logging;
    use SoftDeletes;
    use OrderSoftDeletes;
    use RelationTrait;
    use QueryHelperTrait;

    protected $dispatchesEvents = [
        'saved' => \App\Events\Model\SavedOrderDetail::class,
    ];

    protected $fillable = [
        'order_id',
        'item_detail_id',
        'retail_price',
        'sale_type',
        'tax_rate_id',
        'discount_type',
        'update_staff_id',
    ];

    protected $casts = [
        'discount_type' => 'integer',
    ];

    /**
     * クエリビルダの上書き
     *
     * @return CustomPaginationBuilder
     */
    public function newEloquentBuilder($query)
    {
        return new CustomPaginationBuilder($query);
    }

    /**
     * @return int
     */
    public function getAmountAttribute()
    {
        if (!$this->relationLoaded('orderDetailUnits')) {
            Log::warning(__('error.getter_lazyload'), [__METHOD__]);
        }

        return $this->orderDetailUnits->sum('amount');
    }

    public function setAmountAttribute($value)
    {
        throw new FatalException(__('error.not_allowed_to_set'));
    }

    /**
     * retail_price合計
     *
     * @return int
     */
    public function getTotalRetailPriceAttribute()
    {
        return $this->retail_price * $this->amount;
    }

    public function setTotalRetailPriceAttribute($value)
    {
        throw new FatalException(__('error.not_allowed_to_set'));
    }

    /**
     * 商品展示時（カート投入前）の割引率 (通常割引, 会員割引, 社員割引, イベントセール)
     * NOTE: 定額値引のときは利用できない
     *
     * @return float
     */
    public function getDisplayedDiscountTypeAttribute()
    {
        if (!$this->relationLoaded('displayedDiscount')) {
            Log::warning(__('error.getter_lazyload'), [__METHOD__]);
        }

        if (!isset($this->displayedDiscount)) {
            return null;
        }

        return (float) $this->displayedDiscount->type;
    }

    public function setDisplayedDiscountTypeAttribute($value)
    {
        throw new FatalException(__('error.not_allowed_to_set'));
    }

    /**
     * 商品展示時（カート投入前）の割引率 (通常割引, 会員割引, 社員割引, イベントセール)
     * NOTE: 定額値引のときは利用できない
     *
     * @return float
     */
    public function getDisplayedDiscountRateAttribute()
    {
        if (!$this->relationLoaded('displayedDiscount')) {
            Log::warning(__('error.getter_lazyload'), [__METHOD__]);
        }

        if (!isset($this->displayedDiscount)) {
            return 0.0;
        }

        return (float) $this->displayedDiscount->discount_rate;
    }

    public function setDisplayedDiscountRateAttribute($value)
    {
        throw new FatalException(__('error.not_allowed_to_set'));
    }

    /**
     * 商品展示時（カート投入前）の割引金額 (通常割引, 会員割引, 社員割引, イベントセール)
     * NOTE: 定率値引のときは利用できない
     *
     * @return int
     */
    public function getDisplayedDiscountPriceAttribute()
    {
        if (!$this->relationLoaded('displayedDiscount')) {
            Log::warning(__('error.getter_lazyload'), [__METHOD__]);
        }

        if (!isset($this->displayedDiscount)) {
            return 0;
        }

        return $this->displayedDiscount->discount_price;
    }

    public function setDisplayedDiscountPriceAttribute($value)
    {
        throw new FatalException(__('error.not_allowed_to_set'));
    }

    /**
     * 商品展示時（カート投入前）の割引金額 (通常割引, 会員割引, 社員割引, イベントセール)
     *
     * @return int|null
     */
    public function getDisplayedDiscountMethodAttribute()
    {
        if (!$this->relationLoaded('displayedDiscount')) {
            Log::warning(__('error.getter_lazyload'), [__METHOD__]);
        }

        if (!isset($this->displayedDiscount)) {
            return null;
        }

        return (int) $this->displayedDiscount->method;
    }

    public function setDisplayedDiscountMethodAttribute($value)
    {
        throw new FatalException(__('error.not_allowed_to_set'));
    }

    /**
     * 商品展示時（カート投入前）の割引金額 (通常割引, 会員割引, 社員割引, イベントセールのどれか)
     *
     * @return int
     */
    public function getDisplayedSalePriceAttribute()
    {
        return OrderPrice::computeDisplayedSalePrice($this);
    }

    public function setDisplayedSalePriceAttribute($value)
    {
        throw new FatalException(__('error.not_allowed_to_set'));
    }

    /**
     * バンドル販売値引きの割引金額
     * NOTE: [step1: 割引] → [step2: バンドル販売割引]
     *
     * @return int
     */
    public function getBundleDiscountPriceAttribute()
    {
        return OrderPrice::computeBundleDiscountPrice($this);
    }

    public function setBundleDiscountPriceAttribute($value)
    {
        throw new FatalException(__('error.not_allowed_to_set'));
    }

    /**
     * バンドル販売値引き適用後の金額
     * NOTE: [step1: 割引] → [step2: バンドル販売割引]
     *
     * @return int
     */
    public function getBundleSalePriceAttribute()
    {
        return OrderPrice::computeBundleSalePrice($this);
    }

    public function setBundleSalePriceAttribute($value)
    {
        throw new FatalException(__('error.not_allowed_to_set'));
    }

    /**
     * カート内での表示販売価格
     *
     * @return int
     */
    public function getPriceBeforeOrderAttribute()
    {
        return OrderPrice::computePriceBeforeOrder($this);
    }

    public function setPriceBeforeOrderAttribute($value)
    {
        throw new FatalException(__('error.not_allowed_to_set'));
    }

    /**
     * バンドル販売値引き適用後の金額 * 数量
     * NOTE: [step1: 割引] → [step2: バンドル販売割引]
     *
     * @return int
     */
    public function getTotalPriceBeforeOrderAttribute()
    {
        return $this->price_before_order * $this->amount;
    }

    public function setTotalPriceBeforeOrderAttribute()
    {
        throw new FatalException(__('error.not_allowed_to_set'));
    }

    /**
     * バンドル販売割引率
     *
     * @return float
     */
    public function getBundleDiscountRateAttribute()
    {
        if (!$this->relationLoaded('bundleSaleDiscount')) {
            Log::warning(__('error.getter_lazyload'), [__METHOD__]);
        }

        if (!isset($this->bundleSaleDiscount)) {
            return 0.0;
        }

        return (float) $this->bundleSaleDiscount->discount_rate;
    }

    public function setBundleDiscountRateAttribute($value)
    {
        throw new FatalException(__('error.not_allowed_to_set'));
    }

    /**
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getOrderDiscounts()
    {
        if (!$this->relationLoaded('displayedDiscount') || !$this->relationLoaded('bundleSaleDiscount')) {
            Log::warning(__('error.getter_lazyload'), [__METHOD__]);
        }

        return (new OrderDiscount())->newCollection([$this->displayedDiscount, $this->bundleSaleDiscount])->filter();
    }

    /**
     * @return HasMany
     */
    public function orderDetailLogs(): HasMany
    {
        return $this->hasMany(OrderDetailLog::class);
    }

    /**
     * @return BelongsTo
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * @return BelongsTo
     */
    public function itemDetail(): BelongsTo
    {
        return $this->belongsTo(ItemDetail::class);
    }

    /**
     * @return HasMany
     */
    public function orderDetailUnits(): HasMany
    {
        return $this->hasMany(OrderDetailUnit::class);
    }

    /**
     * @return MorphMany
     */
    public function refundDetails(): MorphMany
    {
        return $this->morphMany(RefundDetail::class, 'refundable');
    }

    /**
     * 商品展示時（カート投入前）の割引
     * (通常割引, 会員割引, 社員割引, イベントセールのどれか)
     *
     * @return MorphOne
     */
    public function displayedDiscount(): MorphOne
    {
        return $this->morphOne(OrderDiscount::class, 'orderable')
            ->whereIn('type', \App\Domain\Utils\OrderDiscount::getDisplayedDiscountTypes());
    }

    /**
     * バンドル販売割引
     *
     * @return MorphOne
     */
    public function bundleSaleDiscount(): MorphOne
    {
        return $this->morphOne(OrderDiscount::class, 'orderable')
            ->where('type', \App\Domain\Utils\OrderDiscount::getBundleSaleDiscountType());
    }

    /** 商品の条件を追加
     *
     * @param Builder $query
     * @param array $conditions
     *
     * @return Builder
     */
    public function scopeWhereItem(Builder $query, array $conditions)
    {
        $subQuery = $this->newRelatedInstance(ItemDetail::class)
            ->getQuery()
            ->select('item_details.id')
            ->join('items', 'item_details.item_id', '=', 'items.id');

        $subQuery = $this->applyConditions($subQuery, $conditions);

        return $query->whereIn('order_details.item_detail_id', $subQuery);
    }

    /**
     * ordersの条件を追加
     *
     * @param Builder $query
     * @param array $conditions
     *
     * @return Builder
     */
    public function scopeWhereOrder(Builder $query, array $conditions)
    {
        $subQuery = $this->newRelatedInstance(Order::class)->getQuery()->select('orders.id');
        $subQuery = $this->applyConditions($subQuery, $conditions);

        return $query->whereIn('order_details.order_id', $subQuery);
    }
}

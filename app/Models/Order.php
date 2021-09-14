<?php

namespace App\Models;

use App\Database\Eloquent\CustomPaginationBuilder;
use App\Domain\Utils\OrderCancel;
use App\Enums\Common\Boolean;
use App\Enums\Order\DeliveryTime;
use App\Exceptions\FatalException;
use App\Models\Contracts\Loggable;
use App\Models\Contracts\Timestampable;
use App\Models\Traits\HasOptionalTimestampsTrait;
use App\Models\Traits\Logging;
use App\Models\Traits\OrderSoftDeletes;
use App\Models\Traits\QueryHelperTrait;
use App\Models\Traits\RelationTrait;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Log;

class Order extends Model implements Loggable, Timestampable
{
    use SoftDeletes;
    use OrderSoftDeletes;
    use QueryHelperTrait;
    use Logging;
    use HasOptionalTimestampsTrait;
    use RelationTrait;

    protected $dispatchesEvents = [
        'saved' => \App\Events\Model\SavedOrder::class,
        'updating' => \App\Events\Model\UpdatingOrder::class,
    ];

    /**
     * キーのカラムが更新されると値のカラムに新しいタイムスタンプが入るように登録する。
     *
     * @var array
     */
    protected $optionalTimestampMap = [
        'paid' => 'paid_date:if_true',
        'inspected' => 'inspected_date:if_true',
    ];

    protected $fillable = [
        'member_id',
        'code',
        'order_date',
        'payment_type',
        'delivery_type',
        'delivery_hope_date',
        'delivery_hope_time',
        'delivery_fee',
        'price',
        'changed_price',
        'tax',
        'fee',
        'use_point',
        'order_type',
        'paid',
        'paid_date',
        'inspected',
        'inspected_date',
        'deliveryed',
        'deliveryed_date',
        'status',
        'add_point',
        'delivery_number',
        'delivery_company',
        'memo1',
        'memo2',
        'shop_memo',
        'device_type',
        'is_guest',
        'update_staff_id',
    ];

    protected $attributes = [
        'paid' => Boolean::IsFalse,
        'paid_date' => null,
        'inspected' => Boolean::IsFalse,
        'inspected_date' => null,
        'deliveryed' => Boolean::IsFalse,
        'deliveryed_date' => null,
        'delivery_fee' => 0,
    ];

    protected $dates = [
        'order_date',
        'deliveryed_date',
        'paid_date',
        'delivery_hope_date',
    ];

    protected $casts = [
        'is_guest' => 'boolean',
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
     * @param array $value
     *
     * @return void
     */
    public function setMemberAttribute($value)
    {
        $this->attributes['member'] = $value;
    }

    /**
     * @return array|null
     */
    public function getMemberAttribute()
    {
        return $this->attributes['member'] ?? null;
    }

    /**
     * order_detailsからtaxを合計して取得する
     *
     * @return int
     */
    public function getTotalItemTaxAttribute()
    {
        return $this->orderDetails->sum('tax');
    }

    /**
     * @param mixed $value
     *
     * @return void
     *
     * @throws FatalException
     */
    public function setTotalItemTaxAttribute($value)
    {
        throw new FatalException(__('error.not_allowed_to_set'));
    }

    /**
     * @return string|null
     */
    public function getDeliveryFeeDiscountTypeAttribute()
    {
        if (!$this->relationLoaded('deliveryFeeDiscount')) {
            Log::warning(__('error.getter_lazyload'), [__METHOD__]);
        }

        if (empty($this->deliveryFeeDiscount)) {
            return null;
        }

        return $this->deliveryFeeDiscount->type;
    }

    public function setDeliveryFeeDiscountTypeAttribute()
    {
        throw new FatalException(__('error.not_allowed_to_set'));
    }

    /**
     * @return int
     */
    public function getDeliveryFeeDiscountPriceAttribute()
    {
        if (!$this->relationLoaded('deliveryFeeDiscount')) {
            Log::warning(__('error.getter_lazyload'), [__METHOD__]);
        }

        if (empty($this->deliveryFeeDiscount)) {
            return 0;
        }

        return (int) $this->deliveryFeeDiscount->discount_price;
    }

    public function setDeliveryFeeDiscountPriceAttribute()
    {
        throw new FatalException(__('error.not_allowed_to_set'));
    }

    /**
     * @return int
     */
    public function getDiscountedDeliveryFeeAttribute()
    {
        return $this->delivery_fee - $this->delivery_fee_discount_price;
    }

    public function setDiscountedDeliveryFeeAttribute()
    {
        throw new FatalException(__('error.not_allowed_to_set'));
    }

    /**
     * @return HasMany
     */
    public function orderAddresses(): HasMany
    {
        return $this->hasMany(OrderAddress::class);
    }

    /**
     * @return HasOne
     */
    public function memberOrderAddress(): HasOne
    {
        return $this->hasOne(OrderAddress::class)->where('type', \App\Enums\OrderAddress\Type::Member);
    }

    /**
     * @return HasOne
     */
    public function deliveryOrderAddress(): HasOne
    {
        return $this->hasOne(OrderAddress::class)->where('type', \App\Enums\OrderAddress\Type::Delivery);
    }

    /**
     * @return HasOne
     */
    public function billingOrderAddress(): HasOne
    {
        return $this->hasOne(OrderAddress::class)->where('type', \App\Enums\OrderAddress\Type::Bill);
    }

    /**
     * @return HasMany
     */
    public function orderDetails(): HasMany
    {
        return $this->hasMany(OrderDetail::class);
    }

    /**
     * @return HasMany
     */
    public function orderLogs(): HasMany
    {
        return $this->hasMany(OrderLog::class);
    }

    /**
     * @return HasMany
     */
    public function orderMessages(): HasMany
    {
        return $this->hasMany(OrderMessage::class);
    }

    /**
     * @return HasOne
     */
    public function orderCredit(): HasOne
    {
        return $this->hasOne(OrderCredit::class);
    }

    /**
     * @return HasOne
     */
    public function orderNp(): HasOne
    {
        return $this->hasOne(OrderNp::class);
    }

    /**
     * @return HasMany
     */
    public function orderChangeHistories(): HasMany
    {
        return $this->hasMany(OrderChangeHistory::class);
    }

    /**
     * @return HasMany
     */
    public function orderUsedCoupons(): HasMany
    {
        return $this->hasMany(OrderUsedCoupon::class);
    }

    /**
     * @return MorphOne
     */
    public function deliveryFeeDiscount(): MorphOne
    {
        return $this->morphOne(OrderDiscount::class, 'orderable')
            ->whereIn('type', \App\Domain\Utils\OrderDiscount::getDeliveryFeeDiscountTypes())
            ->orderBy('priority');
    }

    /**
     * 事部品番をクエリに追加する
     *
     * @param Builder $query
     * @param string|string[] $productNumber
     *
     * @return Builder
     */
    public function scopeWhereItemProductNumber(Builder $query, $productNumber)
    {
        $subQuery = $this->newRelatedInstance(OrderDetail::class)
            ->getQuery()
            ->select('order_details.order_id')
            ->join('item_details', 'order_details.item_detail_id', '=', 'item_details.id')
            ->join('items', 'item_details.item_id', '=', 'items.id')
            ->whereIn('items.product_number', $productNumber);

        return $query->whereIn('orders.id', $subQuery);
    }

    /**
     * 事部品番をクエリに追加する
     *
     * @param Builder $query
     * @param string|string[] $janCode
     *
     * @return Builder
     */
    public function scopeWhereItemJanCode(Builder $query, $janCode)
    {
        $subQuery = $this->newRelatedInstance(OrderDetail::class)
            ->getQuery()
            ->select('order_details.order_id')
            ->join('item_details', 'order_details.item_detail_id', '=', 'item_details.id')
            ->join('item_detail_identifications', 'item_details.id', '=', 'item_detail_identifications.item_detail_id')
            ->whereIn('item_detail_identifications.jan_code', $janCode);

        return $query->whereIn('orders.id', $subQuery);
    }

    /**
     * 商品の条件を追加
     *
     * @param Builder $query
     * @param array $conditions
     *
     * @return Builder
     */
    public function scopeWhereItem(Builder $query, array $conditions)
    {
        $subQuery = $this->newRelatedInstance(OrderDetail::class)
            ->getQuery()
            ->select('order_details.order_id')
            ->join('item_details', 'order_details.item_detail_id', '=', 'item_details.id')
            ->join('items', 'item_details.item_id', '=', 'items.id');

        $subQuery = $this->applyConditions($subQuery, $conditions);

        return $query->whereIn('orders.id', $subQuery);
    }

    /**
     * @return mixed
     */
    public function getOrderItemsAttribute()
    {
        $orderItems = [];
        $this->orderDetails->each(function ($orderDetail) use (&$orderItems) {
            $janCode = $orderDetail->orderDetailUnits->first()->itemDetailIdentification->jan_code;
            $count = $orderDetail->orderDetailUnits->count();
            $orderItems[$janCode] = $count;
        });

        return $orderItems;
    }

    /** ダミー値を返却 Todo
     * @return mixed
     */
    public function getCampaignDiscountAttribute()
    {
        $campaignDiscount = 0;

        return $campaignDiscount;
    }

    /** ダミー値を返却 Todo
     * @return mixed
     */
    public function getCouponDiscountAttribute()
    {
        $couponDiscount = 0;

        return $couponDiscount;
    }

    /** オンライン分類の条件を追加
     *
     * @param Builder $query
     * @param array|int $id
     *
     * @return Builder
     */
    public function scopeWhereOnlinCategoryId(Builder $query, $id)
    {
        $subQuery = $this->newRelatedInstance(OrderDetail::class)
            ->getQuery()
            ->select('order_details.order_id')
            ->join('item_details', 'order_details.item_detail_id', '=', 'item_details.id')
            ->join('items', 'item_details.item_id', '=', 'items.id')
            ->join('item_online_categories', 'items.id', '=', 'item_online_categories.item_id');

        if (is_array($id)) {
            $subQuery = $subQuery->whereIn('online_category_id', $id);
        } else {
            $subQuery = $subQuery->where('online_category_id', $id);
        }

        return $query->whereIn('orders.id', $subQuery);
    }

    /**
     * 商品合計金額
     *
     * @return int
     */
    public function getItemsTotalAttribute()
    {
        return $this->orderDetails->sum('total_price_before_order');
    }

    /**
     * キャンセル可能な受注であるかを返却
     *
     * @return bool
     * @SuppressWarnings(PHPMD.BooleanGetMethodName)
     */
    public function getCanCancelAttribute()
    {
        return OrderCancel::canCancelDatetime($this->order_date) &&
            OrderCancel::canCancelStatus($this->status);
    }

    /**
     * お届け指定日
     *
     * @return string
     */
    public function getDeliveryHopeDateDescriptionAttribute()
    {
        if ($this->delivery_hope_date) {
            Carbon::setLocale('ja');
            $date = Carbon::parse($this->delivery_hope_date)->isoFormat('M月D日(ddd)');
        } else {
            $date = '指定無し';
        }

        return $date;
    }

    /**
     * お届け指定時間
     *
     * @return string
     */
    public function getDeliveryHopeTimeDescriptionAttribute()
    {
        $time = DeliveryTime::getDescription($this->delivery_hope_time);

        return $time;
    }

    /**
     * 0件以上の受注詳細を返す
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getCountedOrderDetails()
    {
        return $this->orderDetails->filter(function ($orderDetail) {
            return $orderDetail->amount > 0;
        });
    }
}

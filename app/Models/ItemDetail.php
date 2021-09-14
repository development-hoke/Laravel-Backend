<?php

namespace App\Models;

use App\Database\Eloquent\CustomPaginationBuilder;
use App\Exceptions\FatalException;
use App\Models\Traits\QueryHelperTrait;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Support\Facades\Log;

class ItemDetail extends Model
{
    use SoftDeletes;
    use QueryHelperTrait;

    protected $fillable = [
        'sort',
        'status',
        'redisplay_requested',
    ];

    /**
     * EC在庫
     *
     * @return int
     */
    public function getEcStockAttribute()
    {
        if (!$this->relationLoaded('itemDetailIdentifications')) {
            Log::warning(__('error.getter_lazyload'), [__METHOD__]);
        }

        return $this->itemDetailIdentifications->sum('ec_stock') - $this->ec_stock_assigned_to_closed_market;
    }

    public function setEcStockAttribute($value)
    {
        throw new FatalException(__('error.not_allowed_to_set'));
    }

    /**
     * 店舗在庫
     *
     * @return int
     */
    public function getStoreStockAttribute()
    {
        if (!$this->relationLoaded('itemDetailIdentifications')) {
            Log::warning(__('error.getter_lazyload'), [__METHOD__]);
        }

        return $this->itemDetailIdentifications->sum('store_stock');
    }

    public function setStockAttribute($value)
    {
        throw new FatalException(__('error.not_allowed_to_set'));
    }

    /**
     * EC在庫 (闇市含む)
     *
     * @return int
     */
    public function getAllEcStockAttribute()
    {
        if (!$this->relationLoaded('itemDetailIdentifications')) {
            Log::warning(__('error.getter_lazyload'), [__METHOD__]);
        }

        return $this->itemDetailIdentifications->sum('ec_stock');
    }

    public function setAllEcStockAttribute($value)
    {
        throw new FatalException(__('error.not_allowed_to_set'));
    }

    /**
     * 闇市在庫
     *
     * @return int
     */
    public function getEcStockAssignedToClosedMarketAttribute()
    {
        if (!$this->relationLoaded('enableClosedMarkets')) {
            Log::warning(__('error.getter_lazyload'), [__METHOD__]);
        }

        return $this->enableClosedMarkets->sum('stock');
    }

    public function setEcStockAssignedToClosedMarketAttribute($value)
    {
        throw new FatalException(__('error.not_allowed_to_set'));
    }

    /**
     * 全在庫 EC在庫 (闇市含む) + 店舗在庫
     *
     * @return int
     */
    public function getAllStockAttribute()
    {
        return $this->all_ec_stock + $this->store_stock;
    }

    /**
     * 再入荷リクエスト数
     *
     * @return int
     */
    public function getItemDetailRequestCountAttribute()
    {
        if (!$this->relationLoaded('redisplayRequests')) {
            Log::warning(__('error.getter_lazyload'), [__METHOD__]);
        }

        return $this->redisplayRequests->count();
    }

    public function setItemDetailRequestCountAttribute($value)
    {
        throw new FatalException(__('error.not_allowed_to_set'));
    }

    /**
     * 予約在庫
     *
     * @return int
     */
    public function getReservableStockAttribute()
    {
        return $this->itemDetailIdentifications->sum('reservable_stock');
    }

    public function setReservableStockAttribute($value)
    {
        throw new FatalException(__('error.not_allowed_to_set'));
    }

    /**
     * カート割り当て済みEC在庫（闇市除く）
     *
     * @return int
     */
    public function getCartAssignedEcStockAttribute()
    {
        if (!$this->relationLoaded('assignedNormalOrderCartItems')) {
            Log::warning(__('error.getter_lazyload'), [__METHOD__]);
        }

        return $this->assignedNormalOrderCartItems->sum('count');
    }

    public function setCartAssignedEcStockAttribute($value)
    {
        throw new FatalException(__('error.not_allowed_to_set'));
    }

    /**
     * カート割り当て済み予約在庫
     *
     * @return int
     */
    public function getCartAssignedReservableStockAttribute()
    {
        if (!$this->relationLoaded('assignedReserveOrderCartItems')) {
            Log::warning(__('error.getter_lazyload'), [__METHOD__]);
        }

        return $this->assignedReserveOrderCartItems->sum('count');
    }

    public function setCartAssignedReservableStockAttribute($value)
    {
        throw new FatalException(__('error.not_allowed_to_set'));
    }

    /**
     * 確保可能な在庫 (EC在庫)
     *
     * @return int
     */
    public function getSecuarableEcStockAttribute()
    {
        return $this->getEcStockAttribute() - $this->getCartAssignedEcStockAttribute();
    }

    public function setSecuarableEcStockAttribute()
    {
        throw new FatalException(__('error.not_allowed_to_set'));
    }

    /**
     * 確保可能な在庫 (予約在庫)
     *
     * @return int
     */
    public function getSecuarableReservableStockAttribute()
    {
        return $this->getReservableStockAttribute() - $this->getCartAssignedReservableStockAttribute();
    }

    public function setSecuarableReservableStockAttribute()
    {
        throw new FatalException(__('error.not_allowed_to_set'));
    }

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
     * @return BelongsTo
     */
    public function color(): BelongsTo
    {
        return $this->belongsTo(Color::class);
    }

    /**
     * @return BelongsTo
     */
    public function size(): BelongsTo
    {
        return $this->belongsTo(Size::class);
    }

    /**
     * @return BelongsTo
     */
    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class);
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
    public function itemDetailIdentifications(): HasMany
    {
        return $this->hasMany(ItemDetailIdentification::class);
    }

    /**
     * @return HasMany
     */
    public function itemDetailStores(): HasMany
    {
        return $this->hasMany(ItemDetailStore::class);
    }

    /**
     * @return HasMany
     */
    public function redisplayRequests(): HasMany
    {
        return $this->hasMany(ItemDetailRedisplayRequest::class);
    }

    /**
     * @return HasMany
     */
    public function closedMarkets(): HasMany
    {
        return $this->hasMany(ClosedMarket::class);
    }

    /**
     * @return HasMany
     */
    public function cartItems(): HasMany
    {
        return $this->hasMany(CartItem::class);
    }

    /**
     * 割り当て済みカート商品
     *
     * @return HasMany
     */
    public function assignedCartItems(): HasMany
    {
        return $this->cartItems()
            ->where('cart_items.posted_at', '>=', \App\Domain\Utils\Cart::computeAliveItemPostedAtBound())
            ->where('cart_items.invalid', false);
    }

    /**
     * 割り当て済みカート商品 (通常商品 闇市以外)
     *
     * @return HasMany
     */
    public function assignedNormalOrderCartItems(): HasMany
    {
        return $this->assignedCartItems()->join('carts', function (JoinClause $join) {
            $join->on('cart_items.cart_id', '=', 'carts.id')
                ->where('carts.order_type', \App\Enums\Order\OrderType::Normal)
                ->whereNull('carts.deleted_at');
        })->whereNull('cart_items.closed_market_id');
    }

    /**
     * 割り当て済みカート商品 (予約商品)
     *
     * @return HasMany
     */
    public function assignedReserveOrderCartItems(): HasMany
    {
        return $this->assignedCartItems()->join('carts', function (JoinClause $join) {
            $join->on('cart_items.cart_id', '=', 'carts.id')
                ->where('carts.order_type', \App\Enums\Order\OrderType::Reserve)
                ->whereNull('carts.deleted_at');
        });
    }

    /**
     * 有効な闇市設定
     *
     * @return HasMany
     */
    public function enableClosedMarkets(): HasMany
    {
        return $this->closedMarkets()->where('limit_at', '>', $this->freshTimestamp());
    }

    /**
     * @return string
     */
    public function getImageUrlAttribute()
    {
        $images = $this->item
            ->itemImages
            ->map(function ($itemImage) {
                return [
                    'id' => $itemImage->id,
                    'type' => $itemImage->type,
                    'url' => $itemImage->url,
                ];
            });
        foreach ($images as $image) {
            if (isset($image['url'])) {
                return $image['url'];
            }
        }

        return '';
    }

    /**
     * 在庫数を条件に追加。
     *
     * @param Builder $query
     * @param int $stockType
     *
     * @return Builder
     */
    public function scopeWhereStockType(Builder $query, int $stockType): Builder
    {
        switch ($stockType) {
            case \App\Enums\Params\Item\Stock::Zero:
                return $query->where('ec_stock', '=', 0);

            case \App\Enums\Params\Item\Stock::One:
                return $query->where('ec_stock', '=', 1);

            case \App\Enums\Params\Item\Stock::TwoOrMore:
                return $query->where('ec_stock', '>=', 2);

            case \App\Enums\Params\Item\Stock::TenOrMore:
                return $query->where('ec_stock', '>=', 10);

            default:
                throw new FatalException(error_format('error.invalid_arguments', compact('stockType')));
        }
    }

    /**
     * 前回の販売日の期間を条件に追加。
     *
     * @param Builder $query
     * @param string $from
     * @param string $to
     *
     * @return Builder
     */
    public function scopeWhereLastSalesDate(Builder $query, string $from = null, string $to = null): Builder
    {
        if (!empty($from) && !empty($to)) {
            return $query->whereBetween('last_sales_date', [$from, $to]);
        }

        if (!empty($from)) {
            return $query->where('last_sales_date', '>=', $from);
        }

        return $query->where('last_sales_date', '<=', $to);
    }

    /**
     * item_detail_identificationsの条件を追加
     *
     * @param Builder $query
     * @param array $conditions
     *
     * @return Builder
     */
    public function scopeWhereItemDetailIdentification($query, array $conditions)
    {
        $subQuery = $this->newRelatedInstance(ItemDetailIdentification::class)
            ->getQuery()
            ->select('item_detail_identifications.item_detail_id');

        $subQuery = $this->applyConditions($subQuery, $conditions);

        return $query->whereIn('item_details.id', $subQuery);
    }

    /**
     * オンラインカテゴリを条件に追加
     *
     * @param Builder $query
     * @param array $onlineCategories
     *
     * @return Builder
     */
    public function scopeHasOnlineCategories(Builder $query, array $onlineCategoryIds, bool $onlyDescendant = false): Builder
    {
        $categories = $this->newRelatedInstance(OnlineCategory::class)
            ->whereIn('id', $onlineCategoryIds)
            ->get();

        $subQuery = $this->newRelatedInstance(ItemOnlineCategory::class)
            ->getQuery()
            ->select('item_online_categories.item_id');

        $categoryIds = [];

        foreach ($categories as $category) {
            if (!$onlyDescendant) {
                $categoryIds[$category->id] = $category->id;
            }

            foreach ($category->descendants()->pluck('id') as $id) {
                $categoryIds[$id] = $id;
            }
        }

        $subQuery = $subQuery->whereIn('item_online_categories.online_category_id', array_keys($categoryIds));

        return  $query->whereIn('item_details.item_id', $subQuery);
    }

    /**
     * @return string
     */
    public function getVariationNameAttribute()
    {
        return 'サイズ:' . $this->size->name . ' 色:' . $this->color->name;
    }
}

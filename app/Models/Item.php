<?php

namespace App\Models;

use App\Database\Eloquent\CustomPaginationBuilder;
use App\Models\Contracts\Timestampable;
use App\Models\Traits\HasOptionalTimestampsTrait;
use App\Models\Traits\QueryHelperTrait;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Support\Facades\Log;

/**
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 * @SuppressWarnings(PHPMD.TooManyMethods)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Item extends Model implements Timestampable
{
    use SoftDeletes;
    use QueryHelperTrait;
    use HasOptionalTimestampsTrait;

    protected $fillable = [
        'status',
        'main_store_brand',
        'name',
        'display_name',
        'brand_id',
        'discount_rate',
        'discount_rate_updated_at',
        'is_member_discount',
        'member_discount_rate',
        'member_discount_rate_updated_at',
        'sales_period_from',
        'sales_period_to',
        'sales_status',
        'back_orderble',
        'returnable',
        'description',
        'size_optional_info',
        'size_caution',
        'material_info',
        'material_caution',
        'is_manually_setting_recommendation',
    ];

    protected $casts = [
        'is_free_delivery' => 'boolean',
    ];

    /**
     * キーのカラムが更新されると値のカラムに新しいタイムスタンプが入るように登録する。
     *
     * @var array
     */
    protected $optionalTimestampMap = [
        'discount_rate' => 'discount_rate_updated_at',
        'member_discount_rate' => 'member_discount_rate_updated_at',
    ];

    /**
     * @var array
     */
    protected $dispatchesEvents = [
        'updating' => \App\Events\Model\UpdatingItem::class,
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
     * @return bool
     */
    public function getIsReservationAttribute()
    {
        if (isset($this->attributes['is_reservation'])) {
            return $this->attributes['is_reservation'];
        }

        if (!$this->relationLoaded('appliedReservation')) {
            Log::warning(__('error.getter_lazyload'), [__METHOD__]);
        }

        return !empty($this->appliedReservation);
    }

    /**
     * @return string
     */
    public function getMakerProductNumberDisplayAttribute()
    {
        $makerProductNumber = $this->fashion_speed === \App\Enums\Item\FashionSpeed::Speed4 ? '#' : '';
        $makerProductNumber .= $this->season->sign;
        $makerProductNumber .= join('-', str_split($this->maker_product_number, 4));

        return $makerProductNumber;
    }

    /**
     * @return BelongsTo
     */
    public function season(): BelongsTo
    {
        return $this->belongsTo(Season::class);
    }

    /**
     * @return BelongsTo
     */
    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class);
    }

    /**
     * @return BelongsTo
     */
    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    /**
     * @return BelongsTo
     */
    public function division(): BelongsTo
    {
        return $this->belongsTo(Division::class);
    }

    /**
     * @return BelongsTo
     */
    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    /**
     * @return BelongsToMany
     */
    public function onlineCategories(): BelongsToMany
    {
        return $this->belongsToMany(OnlineCategory::class, 'item_online_categories');
    }

    /**
     * @return BelongsToMany
     */
    public function onlineTags(): BelongsToMany
    {
        return $this->belongsToMany(OnlineTag::class, 'item_online_tags');
    }

    /**
     * @return BelongsToMany
     */
    public function salesTypes(): BelongsToMany
    {
        return $this->belongsToMany(SalesType::class, 'item_sales_types');
    }

    /**
     * @return HasMany
     */
    public function itemFavorites(): HasMany
    {
        return $this->hasMany(ItemFavorite::class);
    }

    /**
     * @return HasMany
     */
    public function itemDetails(): HasMany
    {
        return $this->hasMany(ItemDetail::class);
    }

    /**
     * @return HasOne
     */
    public function itemReserve(): HasOne
    {
        return $this->hasOne(ItemReserve::class);
    }

    /**
     * 適用済み予約販売設定のリレーション
     *
     * @return HasOne
     */
    public function appliedReservation(): HasOne
    {
        return \App\Domain\ItemPrice::applyAppliedReservationCondition($this->itemReserve());
    }

    /**
     * @return BelongsToMany
     */
    public function itemsUsedSameStylings(): BelongsToMany
    {
        return $this->belongsToMany(Item::class, 'items_used_same_stylings', 'item_id', 'used_item_id');
    }

    /**
     * @return HasMany
     */
    public function itemSubBrands(): HasMany
    {
        return $this->hasMany(ItemSubBrand::class);
    }

    /**
     * @return BelongsToMany
     */
    public function recommendItems(): BelongsToMany
    {
        return $this->belongsToMany(Item::class, 'item_recommends', 'item_id', 'recommend_item_id');
    }

    /**
     * @return HasMany
     */
    public function itemImages(): HasMany
    {
        return $this->nonSortItemImages()->orderBy('sort');
    }

    /**
     * フロント用写真
     *
     * @return HasMany
     */
    public function nonSortItemImages(): HasMany
    {
        return $this->hasMany(ItemImage::class)->where('type', \App\Domain\Utils\ItemImage::getNormalImageType());
    }

    /**
     * 展開用写真
     *
     * @return HasMany
     */
    public function backendItemImages(): HasMany
    {
        return $this->hasMany(ItemImage::class)->whereIn('type', \App\Domain\Utils\ItemImage::getBackendImageTypes());
    }

    /**
     * @return HasMany
     */
    public function eventItems(): HasMany
    {
        return $this->hasMany(EventItem::class);
    }

    /**
     * @return BelongsToMany
     */
    public function bundleSaleEvents(): BelongsToMany
    {
        return $this->belongsToMany(Event::class, 'event_items')
            ->where('sale_type', \App\Enums\Event\SaleType::Bundle);
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

        return  $query->whereIn('items.id', $subQuery);
    }

    /**
     * オンラインタグを条件に追加
     *
     * @param Builder $query
     * @param array $onlineTagIds
     *
     * @return Builder
     */
    public function scopeHasOnlineTags(Builder $query, array $onlineTagIds): Builder
    {
        $subQuery = $this->newRelatedInstance(ItemOnlineTag::class)
            ->getQuery()
            ->select('item_id')
            ->whereIn('online_tag_id', $onlineTagIds);

        return  $query->whereIn('id', $subQuery);
    }

    /**
     * お気に入り数を条件に追加
     *
     * @param Builder $query
     * @param string $comparison
     * @param int $count
     *
     * @return void
     */
    public function scopeCompareFavoriteCount(Builder $query, string $comparison, int $count): Builder
    {
        $subQuery = $this->newRelatedInstance(ItemFavorite::class)
            ->selectRaw('count(item_favorites.id) as id_count, item_id')
            ->groupBy('item_favorites.item_id')
            ->having('id_count', $comparison, $count);

        return  $query->joinSub($subQuery, 'item_favorites_count', function (JoinClause $join) {
            return $join->on('item_favorites_count.item_id', '=', 'items.id');
        });
    }

    /**
     * お気に入り一覧
     *
     * @param Builder $query
     * @param int $memberId
     *
     * @return void
     */
    public function scopeFavorites(Builder $query, int $memberId): Builder
    {
        return $query->whereExists(function ($query) use ($memberId) {
            $query
                ->select(\DB::raw(1))
                ->from('item_favorites')
                ->whereRaw('item_favorites.item_id = items.id')
                ->where('item_favorites.member_id', $memberId);
        });
    }

    /**
     * 在庫数を条件に追加。$stockTypeは数量ではなく `App\Enums\Params\Item\Stock` なことに注意。
     * NOTE: 闇市の値は反映されない
     *
     * @param Builder $query
     * @param int $stockType
     *
     * @return Builder
     */
    public function scopeHasStockType(Builder $query, int $stockType): Builder
    {
        $subQuery = $this->newRelatedInstance(ItemDetail::class)
            ->join('item_detail_identifications', 'item_details.id', '=', 'item_detail_identifications.item_detail_id')
            ->selectRaw('sum(item_detail_identifications.ec_stock) as sum_stock, item_details.item_id')
            ->groupBy('item_details.item_id');

        switch ($stockType) {
            case \App\Enums\Params\Item\Stock::Zero:
                $subQuery = $subQuery->having('sum_stock', '=', 0);
                break;
            case \App\Enums\Params\Item\Stock::One:
                $subQuery = $subQuery->having('sum_stock', '=', 1);
                break;
            default:
                $subQuery = $subQuery->having('sum_stock', '>=', 2);
                break;
        }

        return $query->joinSub($subQuery, 'item_detail_stock_sum', function (JoinClause $join) {
            return $join->on('item_detail_stock_sum.item_id', '=', 'items.id');
        });
    }

    /**
     * 注文の条件を追加
     *
     * @param Builder $query
     * @param array $conditions
     *
     * @return Builder
     */
    public function scopeWhereOrder($query, array $conditions)
    {
        $subQuery = $this->newRelatedInstance(ItemDetail::class)
            ->getQuery()
            ->select('item_details.item_id')
            ->join('order_details', 'order_details.item_detail_id', '=', 'item_details.id')
            ->join('orders', 'order_details.order_id', '=', 'orders.id');

        $subQuery = $this->applyConditions($subQuery, $conditions);

        return $query->whereIn('items.id', $subQuery);
    }

    /**
     * オンライン分類の条件を追加（子要素は含まれない）
     *
     * @param Builder $query
     * @param array|int $id
     *
     * @return Builder
     */
    public function scopeWhereOnlinCategoryId(Builder $query, $id)
    {
        $subQuery = $this->newRelatedInstance(ItemOnlineCategory::class)->getQuery()->select('item_online_categories.item_id');

        if (is_array($id)) {
            $subQuery = $subQuery->whereIn('item_online_categories.online_category_id', $id);
        } else {
            $subQuery = $subQuery->where('item_online_categories.online_category_id', $id);
        }

        return $query->whereIn('items.id', $subQuery);
    }

    /**
     * セールスタイプIDを条件に追加
     *
     * @param Builder $query
     * @param array|int $salesTypeId
     *
     * @return Builder
     */
    public function scopeWhereSalesTypeId($query, $salesTypeId)
    {
        $subQuery = $this->newRelatedInstance(ItemSalesTypes::class)
            ->getQuery()
            ->select('item_sales_types.item_id')
            ->whereIn('item_sales_types.sales_type_id', (array) $salesTypeId);

        return $query->whereIn('items.id', $subQuery);
    }

    /**
     * item_detailsの条件を追加
     *
     * @param Builder $query
     * @param array $conditions
     *
     * @return Builder
     */
    public function scopeWhereItemDetail($query, array $conditions)
    {
        $subQuery = $this->newRelatedInstance(ItemDetail::class)
            ->getQuery()
            ->select('item_details.item_id');

        $subQuery = $this->applyConditions($subQuery, $conditions);

        return $query->whereIn('items.id', $subQuery);
    }

    /**
     * 受注詳細の条件追加
     *
     * @param Builder $query
     * @param array $conditions
     *
     * @return Builder
     */
    public function scopeWhereOrderDetail(Builder $query, array $conditions)
    {
        $subQuery = $this->newRelatedInstance(OrderDetail::class)
            ->getQuery()
            ->select('item_details.item_id')
            ->join('item_details', 'order_details.item_detail_id', '=', 'item_details.id');

        $subQuery = $this->applyConditions($subQuery, $conditions);

        return $query->whereIn('items.id', $subQuery);
    }

    /**
     * 受注詳細の条件追加
     *
     * @param Builder $query
     * @param array $conditions
     *
     * @return Builder
     */
    public function scopeWhereCode(Builder $query, $column, $code)
    {
        ini_set('memory_limit', '-1');
        $subQuery = $this->newRelatedInstance(ItemDetail::class)
            ->getQuery()
            ->select('item_details.item_id')
            ->join('item_detail_identifications', 'item_detail_identifications.item_detail_id', '=', 'item_details.id')
            ->where('item_detail_identifications.'.$column, 'like', '%'.$code.'%');

        return $query->whereIn('items.id', $subQuery);
    }

    /**
     * @param Builder $query
     *
     * @return Builder
     */
    public function scopePublic(Builder $query): Builder
    {
        return $query->where('status', true)
            ->where('sales_period_from', '<=', Carbon::now())
            ->where('sales_period_to', '>=', Carbon::now());
    }
}

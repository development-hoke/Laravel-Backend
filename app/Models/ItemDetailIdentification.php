<?php

namespace App\Models;

use App\Database\Eloquent\CustomPaginationBuilder;
use App\Database\Utils\Query as QueryUtil;
use App\Exceptions\FatalException;
use App\Models\Traits\QueryHelperTrait;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ItemDetailIdentification extends Model
{
    use QueryHelperTrait;

    protected $fillable = [
        'item_detail_id',
        'jan_code',
        'old_jan_code',
        'store_stock',
        'ec_stock',
        'reservable_stock',
        'dead_inventory_days',
        'slow_moving_inventory_days',
        'latest_added_stock',
        'latest_stock_added_at',
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
     * @return BelongsTo
     */
    public function itemDetail(): BelongsTo
    {
        return $this->belongsTo(ItemDetail::class);
    }

    /**
     * @return
     */
    public function orderDetails(): HasMany
    {
        return $this->hasMany(OrderDetail::class);
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
     * 予約可能在庫数を条件に追加。
     *
     * @param Builder $query
     * @param int $reservableStockType
     *
     * @return Builder
     */
    public function scopeWhereReservableStockType(Builder $query, int $reservableStockType): Builder
    {
        switch ($reservableStockType) {
            case \App\Enums\Params\Item\Stock::Zero:
                return $query->where('reservable_stock', '=', 0);

            case \App\Enums\Params\Item\Stock::One:
                return $query->where('reservable_stock', '=', 1);

            case \App\Enums\Params\Item\Stock::TwoOrMore:
                return $query->where('reservable_stock', '>=', 2);

            case \App\Enums\Params\Item\Stock::TenOrMore:
                return $query->where('reservable_stock', '>=', 10);

            default:
                throw new FatalException(error_format('error.invalid_arguments', compact('reservableStockType')));
        }
    }

    /**
     * 不動在庫の条件追加 1:14日以上, 2:30日以上
     *
     * @param Builder $query
     * @param int $deadInventoryDayType
     *
     * @return Builder
     */
    public function scopeWhereDeadInventoryDayType(Builder $query, int $deadInventoryDayType): Builder
    {
        switch ($deadInventoryDayType) {
            case \App\Enums\ItemDetail\DeadInventoryDayType::GreatorThanOrEqual14:
                return $query->where('dead_inventory_days', '>=', 14);

            case \App\Enums\ItemDetail\DeadInventoryDayType::GreatorThanOrEqual30:
                return $query->where('dead_inventory_days', '>=', 30);

            default:
                throw new FatalException(error_format('error.invalid_arguments', compact('deadInventoryDayType')));
        }
    }

    /**
     * 滞留在庫の条件追加。1:14日以上, 2:30日以上
     *
     * @param Builder $query
     * @param int $slowMovingInventoryDayType
     *
     * @return Builder
     */
    public function scopeWhereSlowMovingInventoryDayType(Builder $query, int $slowMovingInventoryDayType): Builder
    {
        switch ($slowMovingInventoryDayType) {
            case \App\Enums\ItemDetail\SlowMovingInventoryDayType::GreatorThanOrEqual14:
                return $query->where('slow_moving_inventory_days', '>=', 14);

            case \App\Enums\ItemDetail\SlowMovingInventoryDayType::GreatorThanOrEqual30:
                return $query->where('slow_moving_inventory_days', '>=', 30);

            default:
                throw new FatalException(error_format('error.invalid_arguments', compact('slowMovingInventoryDayType')));
        }
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
        if (!QueryUtil::joined('item_details', $query)) {
            $query = $query->join('item_details', 'item_detail_identifications.item_detail_id', '=', 'item_details.id');
        }

        $query = $this->applyConditions($query, $conditions);

        return $query->select(['item_detail_identifications.*']);
    }
}

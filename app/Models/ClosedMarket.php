<?php

namespace App\Models;

use App\Exceptions\FatalException;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Support\Facades\Log;

class ClosedMarket extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'item_detail_id',
        'title',
        'password',
        'num',
        'stock',
        'limit_at',
    ];

    /**
     * カート割り当て済み闇市在庫
     *
     * @return int
     */
    public function getCartAssignedClosedMarketEcStockAttribute()
    {
        if (!$this->relationLoaded('assignedClosedMarketCartItems')) {
            Log::warning(__('error.getter_lazyload'), [__METHOD__]);
        }

        return $this->assignedClosedMarketCartItems->sum('count');
    }

    /**
     * 確保可能な在庫 (闇市在庫)
     *
     * @return int
     */
    public function getSecuarableStockAttribute()
    {
        return $this->stock - $this->getCartAssignedClosedMarketEcStockAttribute();
    }

    public function setSecuarableStockAttribute()
    {
        throw new FatalException(__('error.not_allowed_to_set'));
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
     * 割り当て済みカート商品 (闇市)
     *
     * @return HasMany
     */
    public function assignedClosedMarketCartItems(): HasMany
    {
        return $this->assignedCartItems()->join('carts', function (JoinClause $join) {
            $join->on('cart_items.cart_id', '=', 'carts.id')
                ->where('carts.order_type', \App\Enums\Order\OrderType::Normal)
                ->whereNull('carts.deleted_at');
        })->whereNotNull('cart_items.closed_market_id');
    }

    /**
     * item_idと一致するデータを取得する
     *
     * @param Builder $query
     * @param int $itemId
     *
     * @return Builder
     */
    public function scopeWhereItemId(Builder $query, int $itemId): Builder
    {
        return $query->join('item_details', 'closed_markets.item_detail_id', '=', 'item_details.id')
            ->where('item_details.item_id', $itemId)
            ->select('closed_markets.*');
    }

    /**
     * 在庫チェック
     *
     * @param int $requestedStock
     *
     * @return bool
     */
    public function hasStock(int $requestedStock)
    {
        return $this->secuarable_stock >= $requestedStock;
    }
}

<?php

namespace App\Domain;

interface ItemPriceInterface
{
    /**
     * 販売価格の代入をする
     *
     * @param \Illuminate\Database\Eloquent\Collection|\App\Models\Item $item
     * @param array|null $member
     * @param string|null $orderedDate
     *
     * @return \Illuminate\Database\Eloquent\Collection|\App\Models\Item
     */
    public function fillDisplayedSalePrice($items, ?array $member = null, ?string $orderedDate = null);

    /**
     * 商品検索scopeQuery（非会員・ゲスト会員）
     *
     * @return \Closure
     */
    public function getNonMemberSearchScopeQuery();

    /**
     * 商品検索scopeQuery（会員）
     *
     * @param array $member
     * @param \App\Models\Order|null $order
     *
     * @return \Closure
     */
    public function getMemberSearchScopeQuery(array $member, ?\App\Models\Order $editingOrder = null);

    /**
     * 注文以前（カート投入後）の商品販売価格を代入する (新規注文用)
     *
     * @param \App\Models\Cart $cart
     * @param string $orderDate
     *
     * @return \App\Models\Cart
     */
    public function fillPriceBeforeOrderToCreateNewOrder(\App\Models\Cart $cart, $orderDate = null);

    /**
     * 注文以前（カート投入後）の商品販売価格を代入する (注文後用)
     *
     * @param \Illuminate\Database\Eloquent\Collection|\App\Models\Item $targetItems
     * @param \App\Models\Order $order
     * @param array $member
     * @param int $addingCount
     *
     * @return \Illuminate\Database\Eloquent\Collection|\App\Models\Item
     */
    public function fillPriceBeforeOrderAfterOrdered(
        $targetItems,
        \App\Models\Order $order,
        array $member,
        int $addingCount
    );
}

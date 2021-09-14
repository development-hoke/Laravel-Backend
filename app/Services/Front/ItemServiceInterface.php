<?php

namespace App\Services\Front;

interface ItemServiceInterface
{
    /**
     * @param array $params
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function search(array $params);

    /**
     * 商品詳細のitem取得
     *
     * @param string $productNumber
     * @param int|null $closedMarketId 闇市ID
     * @param bool|null $preview 管理画面のプレビュー機能による実行
     *
     * @return \App\Models\Item
     */
    public function fetchDetail(string $productNumber, ?int $closedMarketId = null, ?bool $preview = false);

    /**
     * @param array $items
     *
     * @return mixed
     */
    public function ecData(array $items);

    /**
     * @param array $items
     *
     * @return mixed
     */
    public function findOrderedItems(array $items);

    /**
     * 予約商品・取り寄せ商品入荷処理
     *
     * @param array $items
     * @param int $orderType
     */
    public function storeArriveItems(array $items, int $orderType);

    /**
     * レコメンド商品などに価格関連のパラメータを追加する
     *
     * @param mixed $items
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function fillAdditionalItemRecommendationsAttributes($items);

    /**
     * 商品の還元ポイントを計算してpointプロパティに追加する
     *
     * @param \App\Models\Item $item
     * @param array $member (default: null)
     *
     * @return \App\Models\Item
     */
    public function fillAdditionalItemAttributes(\App\Models\Item $item, array $member = null);

    /**
     * @param int $id
     * @param array $params
     *
     * @return bool
     */
    public function verifyEnteringClosedMarket(int $id, array $params);
}

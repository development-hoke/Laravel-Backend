<?php

namespace App\Domain;

interface StockInterface
{
    /**
     * お取り寄せ可能かを商品基幹のデータで判定する
     *
     * @param int $itemDetailId
     *
     * @return bool
     */
    public function isBackOrderbleSecuredItem(int $itemDetailId);

    /**
     * お取り寄せに指定するJANに紐づく商品データを取得する
     *
     * @param int $itemDetailId
     *
     * @return void
     */
    public function findBackOrderbleSecuredItems(int $itemDetailId);

    /**
     * SKUを指定して在庫を確保する
     *
     * @param int $itemDetailId
     * @param int $requestedStock
     * @param array $options
     *
     * @return \App\Entities\Collection|\App\Entities\Ymdy\Ec\SecuredItem[]
     *
     * @throws \App\Domain\Exceptions\StockShortageException
     */
    public function secureStock(int $itemDetailId, int $requestedStock, array $options = []);

    /**
     * item_detail_identifications.idを指定して在庫を加算する
     *
     * @param int $identId
     * @param int $requestedStock
     *
     * @return void
     */
    public function addEcStock(int $identId, int $requestedStock);

    /**
     * item_detail_identifications.idを指定してEC在庫を確保する
     *
     * @param int $identId
     * @param int $requestedStock
     *
     * @return void
     */
    public function secureEcStock(int $identId, int $requestedStock);

    /**
     * item_detail_identifications.idを指定して在庫を加算する
     *
     * @param int $identId
     * @param int $requestedStock
     *
     * @return void
     */
    public function addReservableStock(int $identId, int $requestedStock);

    /**
     * item_detail_identifications.idを指定して予約在庫を確保する
     *
     * @param int $identId
     * @param int $requestedStock
     *
     * @return void
     */
    public function secureReservableStock(int $identId, int $requestedStock);

    /**
     * 在庫確認をする
     *
     * @param \App\Models\Cart $cart
     * @param bool|null $isAlreadyAdded
     *
     * @return bool
     */
    public function hasStockForCart(\App\Models\Cart $cart, ?bool $isAlreadyAdded = false);

    /**
     * item_detailへの排他ロックと、在庫確認
     *
     * @param \App\Models\CartItem $cartItem
     * @param int $orderType
     *
     * @return bool
     */
    public function lockAndValidateCartItemCount(\App\Models\CartItem $cartItem, int $orderType);

    /**
     * item_detailへの排他ロックと、EC在庫の確認をする
     *
     * @param int $itemDetailId
     * @param int $requestCount
     *
     * @return bool
     */
    public function lockAndValidateEcStock(int $itemDetailId, int $requestCount);
}

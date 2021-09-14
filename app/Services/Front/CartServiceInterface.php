<?php

namespace App\Services\Front;

interface CartServiceInterface
{
    /**
     * 権限検証済みカートデータ取得
     *
     * @param int $cartId
     * @param string|null $token
     *
     * @return \App\Models\Cart
     */
    public function findAuthorizedCart(int $cartId, ?string $token = null);

    /**
     * 権限検証済みカートデータ取得
     *
     * @param int $cartId
     * @param string|null $token
     *
     * @return \App\Models\Cart
     */
    public function findCartByToken(int $cartId, string $token);

    /**
     * カート取得
     * カートが存在しなければ新規作成
     *
     * @param array $params
     *
     * @return \App\Models\Cart
     */
    public function findOrNew(array $params);

    /**
     * 期限切れのカート商品を削除する
     *
     * @param int $cartId
     *
     * @return void
     */
    public function deleteExpiredCartItems(int $cartId);

    /**
     * 予約注文で商品が予約商品ではなくなった場合、通常注文の商品に移行する
     * 在庫確保はせずに、無効となった商品としてカートで表示する。
     *
     * @param int $cartId
     * @param bool|null $skipValidation
     *
     * @return \App\Models\Cart
     */
    public function transitionReservationToNormalOrder(int $cartId, ?bool $skipValidation = false);

    /**
     * 予約商品の検証
     *
     * @param \App\Models\Cart $cart
     *
     * @return bool
     */
    public function validateReservation(\App\Models\Cart $cart);

    /**
     * カートに追加(= 90分間在庫を抑える)
     *
     * @param array $params
     *
     * @return \App\Models\Cart
     *
     * @throws InvalidInputException
     */
    public function addItem(array $params);

    /**
     * カート商品更新
     *
     * @param int $cartId
     * @param int $cartItemId
     * @param array $params
     *
     * @return \App\Models\Cart
     */
    public function updateItem(int $cartId, int $cartItemId, array $params);

    /**
     * カート再投入
     *
     * @param int $cartId
     * @param int $cartItemId
     * @param array $params
     *
     * @return \App\Models\Cart
     */
    public function restoreItem(int $cartId, int $cartItemId, array $params);

    /**
     * カート商品削除処理
     *
     * @param int $cartId
     * @param int $cartItemId
     * @param array $params
     *
     * @return \App\Models\Cart
     */
    public function removeItem(int $cartId, int $cartItemId, array $params);

    /**
     * 利用できなくなった商品詳細を取得
     *
     * @param \App\Models\Cart $cart
     *
     * @return array
     */
    public function getDisabledItemDetails(\App\Models\Cart $cart);

    /**
     * 検証済みメンバークーポンを取得する
     *
     * @param int $cartId
     * @param array $useCouponIds
     *
     * @return \App\Entities\Collection
     *
     * @throws InvalidInputException
     */
    public function fetchValidatedMemberCoupons(int $cartId, array $useCouponIds);

    /**
     * クーポン適用
     *
     * @param array $params
     *
     * @return \App\Models\Cart
     */
    public function updateUseCouponIds(array $params);

    /**
     * @param \App\Models\Cart $cart
     * @param array $pointData
     * @param array $prices
     *
     * @return array
     *
     * @todo リファクタリング
     * @todo ユニットテスト作成
     */
    public function createCartData(\App\Models\Cart $cart, array $pointData, array $prices);

    /**
     * 費用計算
     *
     * @param \App\Models\Cart $cart
     * @param array|null $options
     *
     * @return array
     */
    public function calculatePrices(\App\Models\Cart $cart, ?array $options = []);

    /**
     * memberCouponの読み込み（バリデーションはなし）
     *
     * @param \App\Models\Cart $cart
     *
     * @return \App\Models\Cart
     */
    public function loadMemberCoupons(\App\Models\Cart $cart);

    /**
     * バリデーション済みのmemberCouponの読み込み
     *
     * @param \App\Models\Cart $cart
     *
     * @return \App\Models\Cart
     */
    public function loadValidatedMemberCoupons(\App\Models\Cart $cart);

    /**
     * @param array $params
     * @param int $totalAmount
     * @param string $accessToken
     *
     * @return \App\Entities\AmazonPay\OrderReferenceDetails
     */
    public function amazonPayOrderConfirm(array $params, int $totalAmount, string $accessToken);
}

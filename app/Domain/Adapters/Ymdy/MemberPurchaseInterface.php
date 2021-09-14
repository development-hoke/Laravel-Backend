<?php

namespace App\Domain\Adapters\Ymdy;

use App\Domain\Contracts\AssignableCrediencalToken;
use App\Entities\Ymdy\Member\EcBill;
use App\Entities\Ymdy\Member\PurchasingBill;
use App\Models\Order as OrderModel;

interface MemberPurchaseInterface extends AssignableCrediencalToken
{
    /**
     * 会員・ポイント受注情報作成
     *
     * @param \App\Models\Order $order
     *
     * @return PurchasingBill
     */
    public function createMemberPurchaseAndUpdateTax(OrderModel $order, EcBill $ecBill);

    /**
     * 会員・ポイント受注情報更新
     *
     * @param \App\Models\Order $order
     *
     * @return void
     */
    public function updateMemberPurchaseAndUpdateTax(OrderModel $order, EcBill $ecBill);

    /**
     * 付与ポイントの計算
     *
     * @param \App\Models\Order $order
     *
     * @return PurchasingBill
     */
    public function calculatePointByOrder(OrderModel $order, EcBill $ecBill);

    /**
     * 一部返品
     *
     * @param string $orderCode
     * @param EcBill $ecBill
     *
     * @return void
     */
    public function returnPartially(string $orderCode, EcBill $ecBill);

    /**
     * 返品処理
     *
     * @param string $orderCode
     *
     * @return void
     */
    public function returnOrder(string $orderCode);
}

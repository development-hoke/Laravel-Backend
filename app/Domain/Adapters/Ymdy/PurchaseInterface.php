<?php

namespace App\Domain\Adapters\Ymdy;

use App\Domain\Contracts\AssignableCrediencalToken;
use App\Entities\Ymdy\Member\EcBill;
use App\Exceptions\FatalException;
use App\Models\Order as OrderModel;
use App\Models\OrderDetail;

interface PurchaseInterface extends AssignableCrediencalToken
{
    /**
     * @param OrderModel $order
     *
     * @return EcBill
     */
    public function makeEcBill(OrderModel $order);

    /**
     * 消費税の保存
     *
     * @param OrderModel $order
     * @param EcBill $ecBill
     *
     * @return OrderModel
     */
    public function syncOrderTax(OrderModel $order, EcBill $ecBill);

    /**
     * @param int $type
     *
     * @return int
     *
     * @throws FatalException
     */
    public static function convertEcDiscountTypeToMemberDiscountType($type);

    /**
     * @param OrderDetail $orderDetail
     *
     * @return string \App\Enums\Ymdy\Member\CrosspointPvDiv
     */
    public static function getCrosspointPbDiv(OrderDetail $orderDetail);

    /**
     * ポイント按分計算
     *
     * @param \App\Models\Order $order
     * @param bool|null $loadRelation
     *
     * @return \Illuminate\Support\Collection
     */
    public function portionPoints(OrderModel $order, ?bool $loadRelation = true);
}

<?php

namespace App\Domain\Adapters\Ymdy;

use App\Domain\OrderPortionInterface as OrderPortion;
use App\Entities\Ymdy\Member\EcBill;
use App\Entities\Ymdy\Member\PurchasingBill;
use App\HttpCommunication\Ymdy\MemberInterface as MemberHttpCommunication;
use App\HttpCommunication\Ymdy\PurchaseInterface as PurchaseHttpCommunication;
use App\Models\Order as OrderModel;
use App\Repositories\OrderRepository;

class MemberPurchase extends Purchase implements MemberPurchaseInterface
{
    /**
     * @var MemberHttpCommunication
     */
    private $memberHttpCommunication;

    /**
     * @var PurchaseHttpCommunication
     */
    private $purchaseHttpCommunication;

    /**
     * @param OrderPortion $orderPortion
     * @param OrderRepository $orderRepository
     * @param MemberHttpCommunication $memberHttpCommunication
     * @param PurchaseHttpCommunication $purchaseHttpCommunication
     */
    public function __construct(
        OrderPortion $orderPortion,
        OrderRepository $orderRepository,
        MemberHttpCommunication $memberHttpCommunication,
        PurchaseHttpCommunication $purchaseHttpCommunication
    ) {
        parent::__construct($orderPortion);

        $this->orderRepository = $orderRepository;
        $this->memberHttpCommunication = $memberHttpCommunication;
        $this->purchaseHttpCommunication = $purchaseHttpCommunication;
    }

    /**
     * メンバートークンの設定
     *
     * @param string $token
     *
     * @return static
     */
    public function setMemberToken(string $token)
    {
        $this->memberHttpCommunication->setMemberTokenHeader($token);
        $this->purchaseHttpCommunication->setMemberTokenHeader($token);

        return $this;
    }

    /**
     * スタッフトークンの設定
     *
     * @param string $token
     *
     * @return static
     */
    public function setStaffToken(string $token)
    {
        $this->memberHttpCommunication->setStaffToken($token);
        $this->purchaseHttpCommunication->setStaffToken($token);

        return $this;
    }

    /**
     * 会員・ポイント受注情報作成
     * TODO: 消費税計算と処理を切り離す。
     *
     * @param \App\Models\Order $order
     *
     * @return PurchasingBill
     */
    public function createMemberPurchaseAndUpdateTax(OrderModel $order, EcBill $ecBill)
    {
        $order = $this->orderRepository->syncOrderTax($order, $ecBill);

        $response = $this->memberHttpCommunication->storePurchase(
            $order->member_id,
            $ecBill->toArray()
        )->getBody();

        return new PurchasingBill($response['purchasing_bill']);
    }

    /**
     * 会員・ポイント受注情報更新
     * TODO: 消費税計算と処理を切り離す。
     *
     * @param \App\Models\Order $order
     *
     * @return void
     */
    public function updateMemberPurchaseAndUpdateTax(OrderModel $order, EcBill $ecBill)
    {
        $order = $this->orderRepository->syncOrderTax($order, $ecBill);

        $this->memberHttpCommunication->updatePurchase(
            $order->member_id,
            $order->code,
            $ecBill->toArray()
        )->getBody();
    }

    /**
     * 付与ポイントの計算
     *
     * @param \App\Models\Order $order
     *
     * @return array
     */
    public function calculatePointByOrder(OrderModel $order, EcBill $ecBill)
    {
        // // エラーが返ってくるためモックを返す
        // return [
        //     'base_grant_point' => 0,
        //     'special_grant_point' => 0,
        //     'effective_point' => 0,
        // ];

        $response = $this->purchaseHttpCommunication->calculatePoint($order->member_id, $ecBill->toArray());

        return $response->getBody();
    }

    /**
     * 一部返品
     *
     * @param string $orderCode
     * @param EcBill $ecBill
     *
     * @return void
     */
    public function returnPartially(string $orderCode, EcBill $ecBill)
    {
        $this->purchaseHttpCommunication->markdown($orderCode, $ecBill->toArray());
    }

    /**
     * 返品処理
     *
     * @param string $orderCode
     *
     * @return void
     */
    public function returnOrder(string $orderCode)
    {
        $ecBill = $this->makeReturnEcBill();

        $this->purchaseHttpCommunication->markdown($orderCode, $ecBill->toArray());
    }

    /**
     * @param OrderModel $order
     *
     * @return EcBill
     */
    private function makeReturnEcBill()
    {
        $ecDetails = \App\Entities\Ymdy\Member\EcDetail::collection();
        $ecBill = new EcBill();
        $ecBill->total_price = 0;
        $ecBill->total_tax = 0;
        $ecBill->tax_excluded_total_price = 0;
        $ecBill->details = $ecDetails;

        return $ecBill;
    }
}

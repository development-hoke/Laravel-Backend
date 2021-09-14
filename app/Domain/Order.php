<?php

namespace App\Domain;

use App\Domain\Adapters\Ymdy\MemberPurchaseInterface as MemberPurchaseAdapter;
use App\Domain\Adapters\Ymdy\Purchase as PurchaseAdapter;
use App\Domain\OrderChangeHistoryInterface as OrderChangeHistoryService;
use App\Domain\StockInterface as StockService;
use App\Exceptions\FatalException;
use App\HttpCommunication\Shohin\ItemInterface as ItemHttpCommunication;
use App\HttpCommunication\Ymdy\PurchaseInterface as PurchaseHttpCommunication;
use App\Repositories\OrderDetailRepository;
use App\Repositories\OrderDetailUnitRepository;
use App\Repositories\OrderRepository;
use App\Repositories\RefundDetailRepository;
use App\Utils\Arr;
use Illuminate\Database\Eloquent\Collection;

/**
 * 受注関連の更新を行う
 */
class Order implements OrderInterface
{
    /**
     * @var OrderRepository
     */
    private $orderRepository;

    /**
     * @var OrderDetailRepository
     */
    private $orderDetailRepository;

    /**
     * @var OrderDetailUnitRepository
     */
    private $orderDetailUnitRepository;

    /**
     * @var RefundDetailRepository
     */
    private $refundDetailRepository;

    /**
     * @var OrderChangeHistoryService
     */
    private $orderChangeHistoryService;

    /**
     * @var PurchaseAdapter
     */
    private $purchaseAdapter;

    /**
     * @var MemberPurchaseAdapter
     */
    private $memberPurchaseAdapter;

    /**
     * @var StockService
     */
    private $stockService;

    /**
     * @var PurchaseHttpCommunication
     */
    private $purchaseHttpCommunication;

    /**
     * @var ItemHttpCommunication
     */
    private $itemHttpCommunication;

    /**
     * @var PaymentInterface
     */
    private $paymentService;

    /**
     * @param OrderRepository $orderRepository
     */
    public function __construct(
        OrderRepository $orderRepository,
        OrderDetailRepository $orderDetailRepository,
        OrderDetailUnitRepository $orderDetailUnitRepository,
        RefundDetailRepository $refundDetailRepository,
        OrderChangeHistoryService $orderChangeHistoryService,
        PurchaseAdapter $purchaseAdapter,
        MemberPurchaseAdapter $memberPurchaseAdapter,
        StockService $stockService,
        PurchaseHttpCommunication $purchaseHttpCommunication,
        ItemHttpCommunication $itemHttpCommunication,
        PaymentInterface $paymentService
    ) {
        $this->orderRepository = $orderRepository;
        $this->orderDetailRepository = $orderDetailRepository;
        $this->orderDetailUnitRepository = $orderDetailUnitRepository;
        $this->refundDetailRepository = $refundDetailRepository;
        $this->orderChangeHistoryService = $orderChangeHistoryService;
        $this->purchaseAdapter = $purchaseAdapter;
        $this->memberPurchaseAdapter = $memberPurchaseAdapter;
        $this->stockService = $stockService;
        $this->purchaseHttpCommunication = $purchaseHttpCommunication;
        $this->itemHttpCommunication = $itemHttpCommunication;
        $this->paymentService = $paymentService;
    }

    /**
     * @param string $token
     *
     * @return static
     */
    public function setStaffToken(string $token)
    {
        $this->purchaseHttpCommunication->setStaffToken($token);
        $this->memberPurchaseAdapter->setStaffToken($token);

        return $this;
    }

    /**
     * @param string $token
     *
     * @return static
     */
    public function setMemberToken(string $token)
    {
        $this->purchaseHttpCommunication->setMemberTokenHeader($token);
        $this->memberPurchaseAdapter->setMemberToken($token);

        return $this;
    }

    /**
     * 受注詳細を新規作成する
     *
     * @param \App\Models\Order $order
     * @param \App\Models\Item $requestedItem
     * @param int $staffId
     *
     * @return \App\Models\OrderDetail
     */
    public function createOrderDetail(\App\Models\Order $order, \App\Models\ItemDetail $itemDetail, $staffId = null)
    {
        $attributes = [];
        $attributes['order_id'] = $order->id;
        $attributes['item_detail_id'] = $itemDetail->id;
        $attributes['retail_price'] = $itemDetail->item->retail_price;
        $attributes['tax_rate_id'] = \App\Enums\OrderDetail\TaxRateId::Rate10;
        $attributes['sale_type'] = \App\Domain\Utils\OrderSaleType::getSaleTypeByItem($itemDetail->item);
        $attributes['update_staff_id'] = $staffId;

        $orderDetail = $this->orderDetailRepository->create($attributes);

        return $orderDetail;
    }

    /**
     * 商品詳細ごとに在庫割当。order_detail_unitsを新規作成する（新規注文用）
     *
     * @param \App\Models\OrderDetail $orderDetail
     * @param \App\Models\CartItem $cartItem
     *
     * @return Collection
     *
     * @throws \App\Domain\Exceptions\StockShortageException
     */
    public function addUnitsByFront(\App\Models\OrderDetail $orderDetail, \App\Models\CartItem $cartItem)
    {
        $units = $this->addUnits($orderDetail, [
            'amount' => $cartItem->count,
            'closed_market_id' => $cartItem->closed_market_id,
            'order_type' => $cartItem->order_type,
            'is_aleady_secured' => true,
        ], [
            'create_histories' => false,
        ]);

        return $units;
    }

    /**
     * order_detail_unitsに商品を追加する
     *
     * @param \App\Models\OrderDetail $orderDetail
     * @param array $params ['amount' => int, 'order_type' => int, 'price' => int (optional), 'closed_market_id' => int (optional), 'is_aleady_secured' => (optional)]
     * @param array|null $options ['staff_id' => int, 'create_histories' => bool]
     *
     * @return Collection
     */
    public function addUnits(\App\Models\OrderDetail $orderDetail, array $params, ?array $options = [])
    {
        $staffId = $options['staff_id'] ?? null;
        $createHistories = $options['create_histories'] ?? true;

        $securedCollection = $this->secureStock($orderDetail, $params);

        $units = $this->orderDetailUnitRepository->makeModel()->newCollection();
        $remaining = $params['amount'];

        foreach ($securedCollection as $secured) {
            $securedNum = $secured['secured_num'];

            $unit = $this->orderDetailUnitRepository->findWhere([
                'item_detail_identification_id' => $secured['id'],
                'order_detail_id' => $orderDetail->id,
            ])->first();

            $remaining -= $securedNum;

            if (empty($unit)) {
                $unit = $this->orderDetailUnitRepository->create([
                    'amount' => $securedNum,
                    'order_detail_id' => $orderDetail->id,
                    'item_detail_identification_id' => $secured['id'],
                    'update_staff_id' => $staffId,
                ]);
            } else {
                $unit = $this->orderDetailUnitRepository->update([
                    'amount' => $unit->amount + $securedNum,
                    'update_staff_id' => $staffId,
                ], $unit->id);
            }

            if ($createHistories) {
                $this->orderChangeHistoryService->createAddingUnitHistory(
                    $orderDetail,
                    $unit,
                    $securedNum,
                    $params['price'],
                    $staffId
                );
            }

            $units->add($unit);

            if ($remaining === 0) {
                return $units;
            }
        }

        throw new FatalException('error.failed_to_secure_stock');
    }

    /**
     * 在庫を確保し確保済みの商品データを取得する
     *
     * @param \App\Models\OrderDetail $orderDetail
     * @param array $params
     *
     * @return \App\Entities\Collection|\App\Entities\Ymdy\Ec\SecuredItem[]
     */
    private function secureStock(\App\Models\OrderDetail $orderDetail, array $params)
    {
        if ((int) $params['order_type'] === \App\Enums\Order\OrderType::BackOrder) {
            return $this->stockService->findBackOrderbleSecuredItems($orderDetail->item_detail_id);
        }

        return $this->stockService->secureStock(
            $orderDetail->item_detail_id,
            $params['amount'],
            [
                'closed_market_id' => $params['closed_market_id'] ?? null,
                'is_reservation' => (int) $params['order_type'] === \App\Enums\Order\OrderType::Reserve,
                'is_aleady_secured' => $params['is_aleady_secured'] ?? false,
            ]
        );
    }

    /**
     * order_detail_unitsを削除する
     *
     * @param \App\Models\OrderDetail $orderDetail
     * @param int|null $staffId
     * @param bool $restoreStock
     *
     * @return \App\Models\OrderDetail
     */
    public function removeUnits(\App\Models\OrderDetail $orderDetail, ?int $staffId = null, ?bool $restoreStock = true)
    {
        $removedUnits = (new \App\Models\OrderDetail())->newCollection([]);

        foreach ($orderDetail->orderDetailUnits as $unit) {
            $removed = $this->orderDetailUnitRepository->update([
                'amount' => 0,
                'update_staff_id' => $staffId,
            ], $unit->id);

            $removedUnits->add($removed);

            if ($restoreStock) {
                $this->stockService->addEcStock(
                    $unit->item_detail_identification_id,
                    $unit->amount
                );
            }
        }

        $orderDetail->setRelation('orderDetailUnits', $removedUnits);

        return $orderDetail;
    }

    /**
     * 注文合計金額を更新する
     *
     * @param \App\Models\Order $order
     * @param bool|null $staffId
     * @param bool|null $skipLoadRelation
     *
     * @return \App\Models\Order
     */
    public function updateTotalPrice(\App\Models\Order $order, ?bool $staffId = null, ?bool $skipLoadRelation = false)
    {
        $price = $this->calculateTotalPrice($order, $skipLoadRelation);

        $order->price = $price;

        $order->update_staff_id = $staffId;

        $order->save();

        return $order;
    }

    /**
     * @param Order $order
     * @param bool|null $skipLoadRelation
     *
     * @return int
     */
    private function calculateTotalPrice(\App\Models\Order $order, ?bool $skipLoadRelation = false)
    {
        !$skipLoadRelation && $order->load([
            'orderDetails.orderDetailUnits',
            'orderDetails.displayedDiscount',
            'orderDetails.bundleSaleDiscount',
            'orderUsedCoupons.itemDiscount',
            'deliveryFeeDiscount',
        ]);

        $totalPrice = 0;

        $totalPrice += $order->fee;

        $totalPrice += $order->discounted_delivery_fee;

        $totalPrice += $order->changed_price;

        $totalPrice += $order->orderDetails->sum('total_price_before_order');

        $totalPrice -= $order->orderUsedCoupons->sum('item_applied_price');

        if (!empty($order->use_point)) {
            $totalPrice -= $order->use_point;
        }

        return $totalPrice;
    }

    /**
     * @param \App\Models\Order $order
     * @param bool|null $staffId
     *
     * @return \App\Models\Order
     */
    public function updateOrderDetailSaleTypes(\App\Models\Order $order, ?int $staffId = null)
    {
        $order->load([
            'orderUsedCoupons',
            'orderDetails.displayedDiscount.discountable',
            'orderDetails.bundleSaleDiscount.discountable',
        ]);

        $targetOrderDetailIds = Arr::uniq($order->orderUsedCoupons->pluck('target_order_detail_ids')->flatten());

        foreach ($order->orderDetails as $orderDetail) {
            $saleType = \App\Domain\Utils\OrderSaleType::getSaleTypeByOrderDetail($orderDetail, $targetOrderDetailIds);

            if ((int) $orderDetail->sale_type !== $saleType) {
                $orderDetail->sale_type = $saleType;
                $orderDetail->update_staff_id = $staffId;
                $orderDetail->save();
            }
        }

        return $order;
    }

    /**
     * 付与ポイントを最新の情報で会員ポイントシステムに問い合わせ
     *
     * @param \App\Models\Order $order
     * @param int|null $staffId
     *
     * @return \App\Models\Order
     */
    public function updateAddPoint(\App\Models\Order $order, ?int $staffId = null)
    {
        $ecBill = $this->purchaseAdapter->makeEcBill($order);
        $pointInfo = $this->memberPurchaseAdapter->calculatePointByOrder($order, $ecBill);

        $addPoint = $pointInfo['base_grant_point'] + $pointInfo['special_grant_point'];

        $order->add_point = $addPoint;
        $order->update_staff_id = $staffId;

        $order->save();

        return $order;
    }

    /**
     * キャンセル
     *
     * @param \App\Models\Order $order
     * @param int|null $staffId
     *
     * @return \App\Models\Order
     */
    public function cancel(\App\Models\Order $order, ?int $staffId = null)
    {
        try {
            // 外部システムのみ更新されないように先にDBを更新する。
            $order->load('orderDetails.orderDetailUnits');
            $order->status = \App\Enums\Order\Status::Canceled;
            $order->update_staff_id = $staffId;
            $order->save();

            foreach ($order->orderDetails as $orderDetail) {
                foreach ($orderDetail->orderDetailUnits as $unit) {
                    $this->stockService->addEcStock(
                        $unit->item_detail_identification_id,
                        $unit->amount
                    );
                }
            }

            // FIXME: 会員ポイント、商品基幹は注文時の連携ができない状態なのでキャンセル処理も実行できない。
            // 一時的にコメントアウトする。
            // $this->itemHttpCommunication->purchaseCancel($order->code);

            $this->purchaseHttpCommunication->cancel($order->code);

            $this->paymentService->cancelOrder($order);

            return $order;
        } catch (\Exception $e) {
            \App\Exceptions\ErrorUtil::report('キャンセルに失敗しました。', $e, [
                'order_id' => $order->id,
            ]);

            throw $e;
        }
    }

    /**
     * 注文全返品
     *
     * @param int $orderId
     * @param int|null $staffId
     *
     * @return \App\Models\Order
     */
    public function return(int $orderId, ?int $staffId = null)
    {
        $order = $this->orderRepository->update([
            'status' => \App\Enums\Order\Status::Returned,
            'update_staff_id' => $staffId,
        ], $orderId);

        return $order;
    }

    /**
     * 一部商品返品
     *
     * @param int $orderDetailId
     * @param int|null $staffId
     *
     * @return \App\Models\RefundDetail
     */
    public function returnItem(int $orderDetailId, ?int $staffId = null)
    {
        $orderDetail = $this->orderDetailRepository->find($orderDetailId);

        $refundDetail = $this->refundDetailRepository->create([
            'order_id' => $orderDetail->order_id,
            'refundable_type' => get_class($orderDetail),
            'refundable_id' => $orderDetail->id,
            'unit_price' => $orderDetail->price_before_order,
            'amount' => $orderDetail->amount,
            'update_staff_id' => $staffId,
        ]);

        $restoreStock = false;

        $this->removeUnits($orderDetail, $staffId, $restoreStock);

        return $refundDetail;
    }
}

<?php

namespace App\Domain;

use App\Repositories\OrderChangeHistoryRepository;
use App\Repositories\OrderRepository;

/**
 * 受注変更履歴の作成処理
 */
class OrderChangeHistory implements OrderChangeHistoryInterface
{
    /**
     * @var OrderChangeHistoryRepository
     */
    private $orderChangeHistoryRepository;

    /**
     * @param OrderRepository $orderRepository
     */
    public function __construct(OrderChangeHistoryRepository $orderChangeHistoryRepository)
    {
        $this->orderChangeHistoryRepository = $orderChangeHistoryRepository;
    }

    /**
     * @param int $eventType
     * @param int $orderId
     * @param \App\Models\Contracts\Loggable $loggable
     * @param int $staffId
     * @param array $diffJson
     *
     * @return \App\Models\OrderChangeHistory
     */
    private function create(
        int $eventType,
        int $orderId,
        \App\Models\Contracts\Loggable $loggable,
        int $staffId,
        array $diffJson
    ) {
        $log = $loggable->getLatestLog();

        $history = $this->orderChangeHistoryRepository->create([
            'order_id' => $orderId,
            'log_type' => get_class($log),
            'log_id' => $log->id,
            'staff_id' => $staffId,
            'event_type' => $eventType,
            'diff_json' => $diffJson,
        ]);

        return $history;
    }

    /**
     * 商品追加履歴の保存
     *
     * @param \App\Models\OrderDetail $orderDetail
     * @param \App\Models\OrderDetailUnit $unit
     * @param int $amount
     * @param int $price
     * @param int $staffId
     *
     * @return \App\Models\OrderChangeHistory
     */
    public function createAddingUnitHistory(
        \App\Models\OrderDetail $orderDetail,
        \App\Models\OrderDetailUnit $unit,
        int $amount,
        int $price,
        int $staffId
    ) {
        return $this->create(
            \App\Enums\OrderChangeHistory\EventType::AddItem,
            $orderDetail->order_id,
            $unit,
            $staffId,
            [
                'amount' => $amount,
                'price' => $price,
                'item_detail_id' => $orderDetail->item_detail_id,
            ]
        );
    }

    /**
     * バンドル販売再計算のときに生じた差額を履歴として保存する
     *
     * @param \App\Models\OrderDetail $orderDetail
     * @param \App\Models\OrderDiscount $orderDiscount
     * @param int $originalAppliedPrice
     * @param int $amount
     * @param int $staffId
     *
     * @return \App\Models\OrderChangeHistory
     */
    public function createRecalculateBundleSaleHistory(
        \App\Models\OrderDetail $orderDetail,
        \App\Models\OrderDiscount $orderDiscount,
        int $originalAppliedPrice,
        int $amount,
        int $staffId
    ) {
        return $this->create(
            \App\Enums\OrderChangeHistory\EventType::RecalculateBundleSale,
            $orderDetail->order_id,
            $orderDiscount,
            $staffId,
            [
                'amount' => $amount,
                'price' => $originalAppliedPrice - $orderDiscount->unit_applied_price,
                'item_detail_id' => $orderDetail->item_detail_id,
            ]
        );
    }

    /**
     * 商品キャンセル
     *
     * @param \App\Models\Order $order
     * @param array $diffInfo
     * @param int $staffId
     *
     * @return \App\Models\OrderChangeHistory
     */
    public function createRemoveItemHistory(
        \App\Models\Order $order,
        array $diffInfo,
        int $staffId
    ) {
        return $this->create(
            \App\Enums\OrderChangeHistory\EventType::RemoveItem,
            $order->id,
            $order,
            $staffId,
            $diffInfo
        );
    }

    /**
     * 商品返品
     *
     * @param \App\Models\Order $order
     * @param array $diffInfo
     * @param int $staffId
     *
     * @return \App\Models\OrderChangeHistory
     */
    public function createReturnItemHistory(
        \App\Models\Order $order,
        array $diffInfo,
        int $staffId
    ) {
        return $this->create(
            \App\Enums\OrderChangeHistory\EventType::ReturnItem,
            $order->id,
            $order,
            $staffId,
            $diffInfo
        );
    }
}

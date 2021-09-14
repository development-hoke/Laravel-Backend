<?php

namespace App\Domain;

use App\Domain\Exceptions\PaymentException;
use App\Domain\Exceptions\PaymentRefundException;
use App\Domain\Exceptions\PaymentRefundPartiallyException;
use App\Domain\Exceptions\PaymentSaleException;
use App\Domain\Exceptions\PaymentUpdateBillingAmountException;
use App\Domain\Exceptions\PaymentUpdateShippingAddressException;

/**
 * 支払い方法別の処理をまとめるクラス
 */
class Payment implements PaymentInterface
{
    /**
     * @var AmazonPayInterface
     */
    private $amazonPayService;

    /**
     * @var CreditCardInterface
     */
    private $creditCardService;

    /**
     * @var NpPaymentInterface
     */
    private $npPaymentService;

    /**
     * @param AmazonPayInterface $amazonPayService
     * @param CreditCardInterface $creditCardService
     * @param NpPaymentInterface $npPaymentService
     */
    public function __construct(
        AmazonPayInterface $amazonPayService,
        CreditCardInterface $creditCardService,
        NpPaymentInterface $npPaymentService
    ) {
        $this->amazonPayService = $amazonPayService;
        $this->creditCardService = $creditCardService;
        $this->npPaymentService = $npPaymentService;
    }

    /**
     * 注文キャンセルを実行する
     *
     * @param \App\Models\Order $order
     *
     * @return bool
     */
    public function cancelOrder(\App\Models\Order $order)
    {
        try {
            switch ((int) $order->payment_type) {
                case \App\Enums\Order\PaymentType::AmazonPay:
                    $this->amazonPayService->cancelOrder($order->id);

                    return true;

                case \App\Enums\Order\PaymentType::CreditCard:
                    // 予約は注文時に売上確定するため売上に対してキャンセル処理をする
                    if ((int) $order->order_type === \App\Enums\Order\OrderType::Reserve) {
                        return $this->creditCardService->saleCancel($order->id);
                    }

                    return $this->creditCardService->authCancel($order->id);

                case \App\Enums\Order\PaymentType::NP:
                    return $this->npPaymentService->cancel($order->id);

                default:
                    return true;
            }
        } catch (\Exception $e) {
            \App\Exceptions\ErrorUtil::report('キャンセル処理中に決済システムとの連携に失敗しました。', $e, [
                'order_id' => $order->id,
                'payment_type' => $order->payment_type,
            ]);

            throw new PaymentException($order, $e->getMessage(), null, $e);
        }
    }

    /**
     * 売上確定前の請求金額変更
     *
     * @param \App\Models\Order $order
     *
     * @return mixed
     */
    public function updateBillingAmount(\App\Models\Order $order)
    {
        try {
            switch ((int) $order->payment_type) {
                case \App\Enums\Order\PaymentType::AmazonPay:
                    return $this->amazonPayService->changeAuthorizationAmount($order->id, $order->price);

                case \App\Enums\Order\PaymentType::CreditCard:
                    // 予約は決済時に売上確定するため変更は売上に対して行う
                    if ((int) $order->order_type === \App\Enums\Order\OrderType::Reserve) {
                        return $this->creditCardService->changeSalePrice($order->id, $order->price);
                    }

                    return $this->creditCardService->changeAuthPrice($order->id, $order->price);

                case \App\Enums\Order\PaymentType::NP:
                    return $this->npPaymentService->updateTransactionBilledAmount($order);

                default:
                    return true;
            }
        } catch (\Exception $e) {
            \App\Exceptions\ErrorUtil::report('請求金額変更処理中に決済システムとの連携に失敗しました。', $e, [
                'order_id' => $order->id,
                'payment_type' => $order->payment_type,
            ]);

            throw new PaymentUpdateBillingAmountException($order, $e->getMessage(), null, $e);
        }
    }

    /**
     * 売上確定処理
     *
     * @param \App\Models\Order $order
     *
     * @return mixed
     */
    public function sale(\App\Models\Order $order)
    {
        try {
            switch ($order->payment_type) {
                case \App\Enums\Order\PaymentType::AmazonPay:
                    return $this->amazonPayService->capture($order->id);

                case \App\Enums\Order\PaymentType::CreditCard:
                    // クレカでの予約注文は注文時に売上確定を実行するため処理をスキップする。
                    if ((int) $order->order_type === \App\Enums\Order\OrderType::Reserve) {
                        return;
                    }

                    return $this->creditCardService->sale($order->id);

                case \App\Enums\Order\PaymentType::NP:
                    return $this->npPaymentService->shipment($order->id);

                default:
                    return;
            }
        } catch (\Exception $e) {
            \App\Exceptions\ErrorUtil::report('売上確定処理に失敗しました。', $e, [
                'order_id' => $order->id,
                'payment_type' => $order->payment_type,
            ]);

            throw new PaymentSaleException($order, $e->getMessage(), null, $e);
        }
    }

    /**
     * 一部返金
     *
     * @param \App\Models\Order $order
     * @param int $amount
     *
     * @return mixed
     */
    public function refundPartially(\App\Models\Order $order, int $amount)
    {
        try {
            switch ((int) $order->payment_type) {
                case \App\Enums\Order\PaymentType::AmazonPay:
                    return $this->amazonPayService->refund($order->id, $amount);

                case \App\Enums\Order\PaymentType::CreditCard:
                    return $this->creditCardService->changeSalePrice($order->id, $order->price);

                case \App\Enums\Order\PaymentType::NP:
                    return $this->npPaymentService->reregister($order);

                default:
                    return true;
            }
        } catch (\Exception $e) {
            \App\Exceptions\ErrorUtil::report('決済システムとの一部返金処理の連携に失敗しました。', $e, [
                'order_id' => $order->id,
                'payment_type' => $order->payment_type,
            ]);

            throw new PaymentRefundPartiallyException($order, $e->getMessage(), null, $e);
        }
    }

    /**
     * 返金処理
     *
     * @param \App\Models\Order $order
     *
     * @return mixed
     */
    public function refund(\App\Models\Order $order)
    {
        try {
            switch ((int) $order->payment_type) {
                case \App\Enums\Order\PaymentType::AmazonPay:
                    return $this->amazonPayService->refund($order->id, $order->price);

                case \App\Enums\Order\PaymentType::CreditCard:
                    return $this->creditCardService->saleCancel($order->id);

                case \App\Enums\Order\PaymentType::NP:
                    return $this->npPaymentService->cancel($order->id);

                default:
                    return true;
            }
        } catch (\Exception $e) {
            \App\Exceptions\ErrorUtil::report('決済システムとの返金処理の連携に失敗しました。', $e, [
                'order_id' => $order->id,
                'payment_type' => $order->payment_type,
            ]);

            throw new PaymentRefundException($order, $e->getMessage(), null, $e);
        }
    }

    /**
     * 配送先住所の更新
     *
     * @param \App\Models\Order $order
     *
     * @return mixed
     */
    public function updateShippingAddress(\App\Models\Order $order)
    {
        try {
            switch ((int) $order->payment_type) {
                case \App\Enums\Order\PaymentType::NP:
                    return $this->npPaymentService->updateDestination($order);

                default:
                    return true;
            }
        } catch (\Exception $e) {
            \App\Exceptions\ErrorUtil::report('決済システムとの配送先の連携に失敗しました。', $e, [
                'order_id' => $order->id,
                'payment_type' => $order->payment_type,
            ]);

            throw new PaymentUpdateShippingAddressException($order, $e->getMessage(), null, $e);
        }
    }
}

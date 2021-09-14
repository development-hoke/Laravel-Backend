<?php

namespace App\Domain;

use App\Domain\Exceptions\NpFailedAuthorizationException;
use App\Domain\Exceptions\NpFailedCancelException;
use App\Domain\Exceptions\NpFailedCancelForReregisteringException;
use App\Domain\Exceptions\NpPaymentException;
use App\Domain\Exceptions\NpPaymentReregisterResponseException;
use App\Domain\Exceptions\NpPaymentResponseException;
use App\Domain\Exceptions\NpPaymentUnsolvedFailedTransactionException;
use App\Domain\Exceptions\NpPaymentValidationException;
use App\Domain\OrderPortionInterface as OrderPortion;
use App\Exceptions\FatalException;
use App\HttpCommunication\Exceptions\HttpException;
use App\HttpCommunication\NP\PurchaseInterface as NPPurchaseHttpCommunication;
use App\Notifications\SlackNotification;
use App\Repositories\NpRejectedTransactionRepository;
use App\Repositories\OrderNpRepository;
use App\Utils\Arr;
use Carbon\Carbon;
use Illuminate\Notifications\AnonymousNotifiable;
use Illuminate\Support\Facades\Notification;

class NpPayment implements NpPaymentInterface
{
    /**
     * @var NPPurchaseHttpCommunication
     */
    private $nPPurchaseHttpCommunication;

    /**
     * @var OrderNpRepository
     */
    private $orderNpRepository;

    /**
     * @var NpRejectedTransactionRepository
     */
    private $npRejectedTransactionRepository;

    /**
     * @var OrderPortion
     */
    private $orderPortion;

    /**
     * @param NPPurchaseHttpCommunication $nPPurchaseHttpCommunication
     * @param OrderNpRepository $orderNpRepository
     * @param NpRejectedTransactionRepository $npRejectedTransactionRepository
     * @param OrderPortion $orderPortion
     */
    public function __construct(
        NPPurchaseHttpCommunication $nPPurchaseHttpCommunication,
        OrderNpRepository $orderNpRepository,
        NpRejectedTransactionRepository $npRejectedTransactionRepository,
        OrderPortion $orderPortion
    ) {
        $this->nPPurchaseHttpCommunication = $nPPurchaseHttpCommunication;
        $this->orderNpRepository = $orderNpRepository;
        $this->npRejectedTransactionRepository = $npRejectedTransactionRepository;
        $this->orderPortion = $orderPortion;
    }

    /**
     * @param \App\Models\Order $order
     *
     * @return \App\Models\OrderNp
     */
    public function createTransaction(\App\Models\Order $order)
    {
        $params = $this->makeCreateTransactionPayload($order);

        try {
            $response = $this->nPPurchaseHttpCommunication->transactions($params);

            $result = $response->getBody();

            $transaction = new \App\Entities\Np\Transaction($result['results'][0]);

            // 保留の場合、確保した与信が残り続けるため、解放するためにキャンセルをする。
            // 参照: https://manual-update.np-payment-gateway.com/function_supplement
            if ($transaction->authori_result === \App\Enums\Np\AuthoriResult::Pending) {
                try {
                    $this->handleUnsolvedFailedTransaction($transaction, $order, $params, $result['errors'] ?? []);
                } catch (NpPaymentUnsolvedFailedTransactionException $e) {
                    $this->sendNotification('取引登録・更新時にNGまたは保留となりましたが、注文キャンセルに失敗しました。', [
                        'exception' => NpPaymentUnsolvedFailedTransactionException::class,
                        'message' => $e->getMessage(),
                    ]);
                    throw $e;
                }
            }

            if ($transaction->authori_result !== \App\Enums\Np\AuthoriResult::OK) {
                throw new NpFailedAuthorizationException($transaction, $order, $result['errors'] ?? [], $params);
            }

            $orderNp = $this->createOrderNp($order->id, $transaction);

            return $orderNp;
        } catch (\Exception $e) {
            $this->handleTransactionError($e, $params, $order);
        }
    }

    /**
     * 取引登録のパラメータ作成
     *
     * @param \App\Models\Order $order
     *
     * @return array
     */
    private function makeCreateTransactionPayload(\App\Models\Order $order)
    {
        $order = $order->replicateWithKey();

        $order->load([
            'memberOrderAddress.pref',
            'deliveryFeeDiscount',
            'orderDetails.orderDetailUnits.itemDetailIdentification',
            'orderDetails.displayedDiscount',
            'orderDetails.bundleSaleDiscount',
            'orderUsedCoupons.itemDiscount',
        ]);

        $goods = $this->makeTransactionGoodsPayload($order);

        $customer = $this->makeTransactionAddressPayload($order->memberOrderAddress);

        $params = [
            'shop_transaction_id' => $order->code,
            'shop_order_date' => $order->order_date->format('Y-m-d'),
            'settlement_type' => \App\Enums\Np\SettlementType::NP,
            'billed_amount' => $order->price,
            'customer' => $customer,
            'goods' => $goods,
        ];

        return $params;
    }

    /**
     * 取引請求金額変更
     *
     * @param \App\Models\Order $order
     *
     * @return \App\Models\OrderNp
     */
    public function updateTransactionBilledAmount(\App\Models\Order $order)
    {
        $orderNp = $this->orderNpRepository->findOrFail([
            'order_id' => $order->id,
            'status' => \App\Enums\OrderNp\Status::Authorized,
        ]);

        $params = $this->makeUpdateTransactionBilledAmountPayload($order);

        $orderNp = $this->updateTransaction($orderNp, $order, $params);

        return $orderNp;
    }

    /**
     * 取引更新処理
     *
     * @param \App\Models\OrderNp $orderNp
     * @param \App\Models\Order $order
     * @param array $params
     *
     * @return \App\Models\OrderNp
     */
    private function updateTransaction(\App\Models\OrderNp $orderNp, \App\Models\Order $order, array $params)
    {
        try {
            $response = $this->nPPurchaseHttpCommunication->updateTransaction($orderNp->np_transaction_id, $params);

            $result = $response->getBody();

            $transaction = new \App\Entities\Np\Transaction($result['results'][0]);

            // 取引変更時にNG/保留となった場合別の決済種別への変更を促す。
            if ($transaction->authori_result !== \App\Enums\Np\AuthoriResult::OK) {
                try {
                    $this->handleUnsolvedFailedTransaction($transaction, $order, $params, $result['errors'] ?? []);
                } catch (NpPaymentUnsolvedFailedTransactionException $e) {
                    $this->sendNotification('取引登録・更新時にNGまたは保留となりましたが、注文キャンセルに失敗しました。', [
                        'exception' => NpPaymentUnsolvedFailedTransactionException::class,
                        'message' => $e->getMessage(),
                    ]);
                    throw $e;
                }
            }

            $orderNp = $this->orderNpRepository->update([
                'np_transaction_id' => $transaction->np_transaction_id,
                'authori_result' => $transaction->authori_result,
                'authori_required_date' => $transaction->authori_required_date,
            ], $orderNp->id);

            return $orderNp;
        } catch (\Exception $e) {
            $this->handleTransactionError($e, $params, $order);
        }
    }

    /**
     * 取引登録のパラメータ作成
     *
     * @param \App\Models\Order $order
     *
     * @return array
     */
    private function makeUpdateTransactionBilledAmountPayload(\App\Models\Order $order)
    {
        $order = $order->replicateWithKey();

        $order->load([
            'deliveryFeeDiscount',
            'orderDetails.orderDetailUnits.itemDetailIdentification',
            'orderDetails.displayedDiscount',
            'orderDetails.bundleSaleDiscount',
            'orderUsedCoupons.itemDiscount',
        ]);

        $goods = $this->makeTransactionGoodsPayload($order);

        $params = [
            'goods' => $goods,
        ];

        return $params;
    }

    /**
     * 配送先の更新
     *
     * @param \App\Models\Order $order
     *
     * @return \App\Models\OrderNp
     */
    public function updateDestination(\App\Models\Order $order)
    {
        $orderNp = $this->orderNpRepository->findOrFail([
            'order_id' => $order->id,
            'status' => \App\Enums\OrderNp\Status::Authorized,
        ]);

        $params = $this->makeUpdateDestinationPayload($order);

        $orderNp = $this->updateTransaction($orderNp, $order, $params);

        return $orderNp;
    }

    /**
     * 取引登録のパラメータ作成
     *
     * @param \App\Models\Order $order
     *
     * @return array
     */
    private function makeUpdateDestinationPayload(\App\Models\Order $order)
    {
        $order = $order->replicateWithKey();

        $order->load(['deliveryOrderAddress.pref']);

        $destCustomer = $this->makeTransactionAddressPayload($order->deliveryOrderAddress, true);

        $params = [
            'dest_customer' => $destCustomer,
        ];

        return $params;
    }

    /**
     * 一部返品取引再登録処理
     *
     * @param \App\Models\Order $order
     *
     * @return \App\Models\OrderNp
     */
    public function reregister(\App\Models\Order $order)
    {
        try {
            // キャンセル処理を実行するため、一部返品取引再登リクエストで発生しそうな
            // エラーはここでできるだけバリデーションして通さないようにする。
            $this->validateReregister($order);

            // キャンセル完了以前でのエラーとキャンセル完了後のエラーを区別できるようにする。
            // このブロック以降にエラーが発生した場合、NP後払い側の取引ステータスはキャンセル済み・再登録失敗の状態になっている可能性がある。
            try {
                $orderNp = $this->orderNpRepository->findOrFail([
                    'order_id' => $order->id,
                ]);

                $oldOrderNp = $orderNp->replicate();

                $syncRemote = (int) $orderNp->status !== \App\Enums\OrderNp\Status::CanceledButFailedReregister;

                $this->cancelAndDeleteRecreatingTransaction($orderNp->id, $syncRemote);
            } catch (\Exception $e) {
                throw new NpFailedCancelForReregisteringException($e->getMessage(), null, $e);
            }

            $payload = $this->makeReregisterPayload($order);

            $response = $this->nPPurchaseHttpCommunication->reregister($orderNp->np_transaction_id, $payload);

            $body = $response->getBody();

            $reregisteredTransaction = new \App\Entities\Np\ReregisteredTransaction($body['results'][0]);

            $newOrderNp = $this->recreateOrderNp($reregisteredTransaction, $oldOrderNp);

            return $newOrderNp;
        } catch (\Exception $e) {
            // この分岐で処理するのは、バリデーションエラー(NpPaymentValidationException) と
            // キャンセルの失敗 (NpFailedCancelForReregisteringException)。
            // 変換せずにここでそのまま投げる。
            if ($e instanceof NpPaymentException) {
                throw $e;
            }

            // この処理以降はキャンセル処理成功後のエラー
            // HTTPエラーの場合はNP後払い側のデータはキャンセル済み・再登録失敗の状態になる。
            $requestParams = $payload ?? [];

            if ($e instanceof HttpException) {
                $body = $e->getResponseBody();
                $errorCodes = $body['errors'][0]['codes'] ?? [];

                $this->sendNotification('一部返品再登録処理に失敗しキャンセル済み・再登録失敗の状態になりました。', [
                    'exception' => NpPaymentReregisterResponseException::class,
                    'order_id' => $order->id,
                    'error_codes' => json_encode($errorCodes),
                ]);

                throw new NpPaymentReregisterResponseException($order, $errorCodes, $requestParams, null, null, $e);
            }

            throw new NpPaymentException(error_format($e->getMessage(), [
                'request_params' => $requestParams,
            ]), null, $e);
        }
    }

    /**
     * order_npをキャンセル済み・再登録失敗のステータスに変更する
     *
     * @param int $orderId
     *
     * @return \App\Models\OrderNp
     */
    public function updateOrderNpToCanceledButFailedReregister(int $orderId)
    {
        $orderNp = $this->orderNpRepository->findOrFail(['order_id' => $orderId]);

        $orderNp = $this->orderNpRepository->update([
            'status' => \App\Enums\OrderNp\Status::CanceledButFailedReregister,
        ], $orderNp->id);

        return $orderNp;
    }

    /**
     * 一部返品再登録実行時のバリデーション
     *
     * @param \App\Models\Order $order
     *
     * @return void
     */
    private function validateReregister(\App\Models\Order $order)
    {
        $yesterday = Carbon::now()->subDays(1)->format('Y-m-d');

        if (Carbon::parse($yesterday)->gte($order->deliveryed_date) === false) {
            throw new NpPaymentValidationException(__('validation.np.reregister.too_early_to_request'));
        }
    }

    /**
     * 一部返品再登録
     *
     * @param \App\Models\Order $order
     *
     * @return array
     */
    private function makeReregisterPayload(\App\Models\Order $order)
    {
        $order = $order->replicateWithKey();

        $order->load([
            'deliveryFeeDiscount',
            'orderDetails.orderDetailUnits.itemDetailIdentification',
            'orderDetails.displayedDiscount',
            'orderDetails.bundleSaleDiscount',
            'orderUsedCoupons.itemDiscount',
        ]);

        $goods = $this->makeTransactionGoodsPayload($order);

        $params = [
            'shop_transaction_id' => $order->code,
            'shop_order_date' => $order->order_date->format('Y-m-d'),
            'billed_amount' => (int) $order->price,
            'goods' => $goods,
        ];

        return $params;
    }

    /**
     * goodsパラメータの作成
     *
     * @param \App\Models\Order $order
     *
     * @return array
     */
    private function makeTransactionGoodsPayload(\App\Models\Order $order)
    {
        // 商品合計金額 > 顧客請求金額である必要があるため、ポイント、クーポン値引きの按分処理をする。
        // また、商品合計金額と顧客請求金額の差額は3000円まで許容されているので、ここでは厳密な値は求めない。
        // 参照: https://manual-update.np-payment-gateway.com/reference_register
        $portionedPoints = $this->orderPortion->portionPoints($order);
        $portionedPoints = Arr::dict($portionedPoints, 'order_detail_unit_id');
        $portionedCoupons = $this->orderPortion->portionCoupons($order);

        $goods = [];

        foreach ($order->orderDetails as $orderDetail) {
            if ((int) $orderDetail->amount === 0) {
                continue;
            }

            $itemName = $orderDetail->itemDetail->item->name;
            $orderDetailUnits = $orderDetail->orderDetailUnits;

            $pointAmount = $orderDetailUnits->map(function ($unit) use ($portionedPoints) {
                return $portionedPoints[$unit->id] ?? 0;
            })->sum();

            $couponDiscountPrice = $orderDetailUnits->map(function ($unit) use ($portionedCoupons) {
                return isset($portionedCoupons[$unit->id]) ? $portionedCoupons[$unit->id]->sum('price') : 0;
            })->sum();

            $discountPrice = (int) ceil(($pointAmount + $couponDiscountPrice) / $orderDetail->amount);

            $goods[] = [
                'goods_name' => mb_convert_kana($itemName, 'ASKV'),
                'goods_price' => (int) ($orderDetail->price_before_order - $discountPrice),
                'quantity' => (int) $orderDetail->amount,
            ];
        }

        return $goods;
    }

    /**
     * @param \App\Models\Order $order
     *
     * @return array
     */
    private function makeTransactionAddressPayload(\App\Models\OrderAddress $orderAddress, bool $isDestination = false)
    {
        $address = $orderAddress->pref->name
            . $orderAddress->city
            . $orderAddress->town
            . $orderAddress->address
            . ($orderAddress->building ? '　' . $orderAddress->building : '');

        $params = [
            'customer_name' => $orderAddress->lname . $orderAddress->fname,
            'customer_name_kana' => $orderAddress->lkana . $orderAddress->fkana,
            'zip_code' => $orderAddress->zip,
            'address' => mb_convert_kana($address, 'ASKV'),
            'tel' => $orderAddress->tel,
        ];

        if ($isDestination === false) {
            $params['email'] = $orderAddress->email;
        }

        return $params;
    }

    /**
     * 取引保留時の処理
     *
     * @param \App\Entities\Np\Transaction $transaction
     * @param \App\Models\Order $order
     * @param array $requestParams
     * @param array $errors
     *
     * @return void
     */
    private function handleUnsolvedFailedTransaction(\App\Entities\Np\Transaction $transaction, \App\Models\Order $order, array $requestParams, array $errors = [])
    {
        try {
            $this->cancel($order->id);
        } catch (\Exception $e) {
            throw new NpPaymentUnsolvedFailedTransactionException($order, $transaction, error_format('error.np_payment_unsolved_failed_transaction', [
                'transaction' => $transaction->toArray(),
                'order_id' => $order->id,
                'member_id' => $order->member_id,
                'original_errors' => $errors,
            ]), null, $e);
        }

        throw new NpFailedAuthorizationException($transaction, $order, $errors, $requestParams);
    }

    /**
     * 取引登録・取引失敗時のエラー処理
     *
     * @param \Exception $exception
     *
     * @return void
     */
    private function handleTransactionError(\Exception $exception, array $requestParams, \App\Models\Order $order)
    {
        if ($exception instanceof NpPaymentException) {
            throw $exception;
        }

        if ($exception instanceof \App\HttpCommunication\Exceptions\HttpException) {
            $this->handleTransactionHttpError($exception, $requestParams, $order);
        }

        throw new NpPaymentException(error_format($exception->getMessage(), [
            'request_params' => $requestParams,
        ]), null, $exception);
    }

    /**
     * @param \App\HttpCommunication\Exceptions\HttpException $exception
     * @param array $requestParams
     * @param \App\Models\Order $order
     *
     * @return void
     */
    private function handleTransactionHttpError(
        \App\HttpCommunication\Exceptions\HttpException $exception,
        array $requestParams,
        \App\Models\Order $order
    ) {
        $body = $exception->getResponseBody();

        $errorCodes = $body['errors'][0]['codes'] ?? [];

        if (isset($result['results'][0])) {
            throw new NpFailedAuthorizationException(
                new \App\Entities\Np\Transaction($result['results'][0]),
                $order,
                $errorCodes,
                $requestParams,
                null,
                $exception
            );
        }

        throw new NpPaymentResponseException($errorCodes, $requestParams, null, null, $exception);
    }

    /**
     * NP後払い決済情報保存
     *
     * @param int $orderId
     * @param \App\Entities\Np\Transaction $transaction
     * @param int|null $status
     *
     * @return \App\Models\OrderNp
     */
    private function createOrderNp(int $orderId, \App\Entities\Np\Transaction $transaction, ?int $status = null)
    {
        $status = $status ?? \App\Enums\OrderNp\Status::Authorized;

        return $this->orderNpRepository->create([
            'order_id' => $orderId,
            'shop_transaction_id' => $transaction->shop_transaction_id,
            'np_transaction_id' => $transaction->np_transaction_id,
            'authori_result' => $transaction->authori_result,
            'authori_required_date' => $transaction->authori_required_date,
            'authori_ng' => $transaction->authori_ng,
            'authori_hold' => $transaction->authori_hold,
            'status' => $status,
        ]);
    }

    /**
     * OrderNpの再作成
     *
     * @param \App\Entities\Np\ReregisteredTransaction $reregisteredTransaction
     * @param \App\Models\OrderNp $orderNp
     * @param int|null $status
     *
     * @return \App\Models\OrderNp
     */
    private function recreateOrderNp(
        \App\Entities\Np\ReregisteredTransaction $reregisteredTransaction,
        \App\Models\OrderNp $orderNp,
        ?int $status = null
    ) {
        $status = $status ?? \App\Enums\OrderNp\Status::Shipped;

        return $this->orderNpRepository->create([
            'order_id' => $orderNp->order_id,
            'shop_transaction_id' => $orderNp->shop_transaction_id,
            'np_transaction_id' => $reregisteredTransaction->np_transaction_id,
            'authori_result' => $orderNp->authori_result,
            'authori_required_date' => $orderNp->authori_required_date,
            'authori_ng' => $orderNp->authori_ng,
            'authori_hold' => $orderNp->authori_hold,
            'status' => $status,
        ]);
    }

    /**
     * NP後払い決済情報保存
     *
     * @param \App\Entities\Np\Transaction $failedTransaction
     * @param int|null $status
     *
     * @return \App\Models\OrderNp
     */
    public function importFailedTransactionStatus(\App\Entities\Np\Transaction $failedTransaction, ?int $status = null)
    {
        if (in_array($failedTransaction->authori_result, [\App\Enums\OrderNp\Status::NG, \App\Enums\OrderNp\Status::Pending], true)) {
            throw new FatalException(error_format('error.np_not_failed_transaction', $failedTransaction->toArray()));
        }

        $orderNp = $this->orderNpRepository->findOrFail([
            'np_transaction_id' => $failedTransaction->np_transaction_id,
        ]);

        if (is_null($status)) {
            $status = $this->getOrderNpStatusByTransaction($failedTransaction);
        }

        return $this->orderNpRepository->update([
            'shop_transaction_id' => $failedTransaction->shop_transaction_id,
            'np_transaction_id' => $failedTransaction->np_transaction_id,
            'authori_result' => $failedTransaction->authori_result,
            'authori_required_date' => $failedTransaction->authori_required_date,
            'authori_ng' => $failedTransaction->authori_ng,
            'authori_hold' => $failedTransaction->authori_hold,
            'status' => $status,
        ], $orderNp->id);
    }

    /**
     * @param \App\Entities\Np\Transaction $transaction
     *
     * @return int|null
     */
    private function getOrderNpStatusByTransaction(\App\Entities\Np\Transaction $transaction)
    {
        switch ($transaction->authori_result) {
            case \App\Enums\Np\AuthoriResult::Pending:
                return \App\Enums\OrderNp\Status::Pending;

            case \App\Enums\Np\AuthoriResult::NG:
                return \App\Enums\OrderNp\Status::NG;

            case \App\Enums\Np\AuthoriResult::OK:
                return \App\Enums\OrderNp\Status::Authorized;
        }
    }

    /**
     * np_rejected_transactionsの作成
     *
     * @param int $cartId
     * @param int $memberId
     * @param \App\Entities\Np\Transaction $transaction
     * @param array $errorCodes
     *
     * @return \App\Models\NpRejectedTransaction
     */
    public function createNpRejectedTransaction(int $cartId, int $memberId, \App\Entities\Np\Transaction $transaction, array $errorCodes = null)
    {
        return $this->npRejectedTransactionRepository->create(array_merge([
            'cart_id' => $cartId,
            'member_id' => $memberId,
            'error_codes' => $errorCodes,
        ], $transaction->toArray()));
    }

    /**
     * @param int $orderId
     *
     * @return \App\Entities\Np\Shipment
     */
    public function shipment(int $orderId)
    {
        $orderNp = $this->orderNpRepository->with(['order'])->findOrFail(['order_id' => $orderId]);
        $order = $orderNp->order;

        $shipmentParam = [
            'np_transaction_id' => $orderNp->np_transaction_id,
            'pd_company_code' => \App\Enums\Np\PdCompanyCode::Sagawa,
            'slip_no' => $order->delivery_number,
        ];

        try {
            $response = $this->nPPurchaseHttpCommunication->shipments($shipmentParam);
        } catch (HttpException $e) {
            // NP後払い側のみキャンセル状態になっている場合、期限切れと見做す。
            // 既存のデータは削除して、新たに取引登録を実行する。
            if (!$this->hasCanceledTransactionErrorCode($e)) {
                throw $e;
            }

            $this->cancelAndDeleteRecreatingTransaction($orderNp->id, false);

            $orderNp = $this->createTransaction($order);

            $response = $this->nPPurchaseHttpCommunication->shipments($shipmentParam);
        }

        $this->orderNpRepository->update([
            'status' => \App\Enums\OrderNp\Status::Shipped,
        ], $orderNp->id);

        $result = $response->getBody();

        return new \App\Entities\Np\Shipment($result['results'][0]);
    }

    /**
     * 取引再作成用のキャンセル処理。
     * キャンセルと一緒にレコードの削除も実行し、注文IDに対して複数のレコードができないようにする。
     * (1) 発送報告時の再与信
     * (2) 一部返品再登録
     *
     * @param int $orderNpId
     *
     * @return \App\Models\OrderNp
     */
    private function cancelAndDeleteRecreatingTransaction(int $orderNpId, bool $syncRemote = true)
    {
        $orderNp = $this->orderNpRepository->update([
            'status' => \App\Enums\OrderNp\Status::Canceled,
        ], $orderNpId);

        $this->orderNpRepository->delete($orderNpId);

        if ($syncRemote) {
            $this->sendCancelRequest($orderNp->np_transaction_id);
        }

        return $orderNp;
    }

    /**
     * @param HttpException $exception
     *
     * @return bool
     */
    private function hasCanceledTransactionErrorCode(HttpException $exception)
    {
        $body = $exception->getResponseBody();

        $errorCodes = $body['errors'][0]['codes'] ?? [];

        return in_array(\App\Enums\Np\ErrorCode\Shipment::Canceled, $errorCodes, true);
    }

    /**
     * スラック通知を送る
     *
     * @param array $fields
     *
     * @return void
     */
    private static function sendNotification(string $title, array $fields)
    {
        Notification::send(
            (new AnonymousNotifiable())->route('slack', config('slack.webhook')),
            new SlackNotification('<!channel> NP Payment Error', [
                'error' => true,
                'url' => '',
                'title' => $title,
                'fields' => $fields,
            ])
        );
    }

    /**
     * キャンセル処理
     *
     * @param int $orderId
     *
     * @return bool
     */
    public function cancel(int $orderId)
    {
        $orderNp = $this->orderNpRepository->findOrFail(['order_id' => $orderId]);

        $orderNp = $this->orderNpRepository->update([
            'status' => \App\Enums\OrderNp\Status::Canceled,
        ], $orderNp->id);

        $done = $this->sendCancelRequest($orderNp->np_transaction_id);

        return $done;
    }

    /**
     * NP後払い側へのキャンセル処理の実行
     *
     * @param string $npTransactionId
     *
     * @return bool
     */
    private function sendCancelRequest(string $npTransactionId)
    {
        try {
            $response = $this->nPPurchaseHttpCommunication->cancel($npTransactionId);
            $result = $response->getBody();
            $canceled = collect($result['results'])->where('np_transaction_id', $npTransactionId);

            if ($canceled->isEmpty()) {
                throw new NpFailedCancelException($result['errors'] ?? []);
            }

            return true;
        } catch (\App\HttpCommunication\Exceptions\HttpException $e) {
            $result = $e->getResponseBody();
            throw new NpFailedCancelException($result['errors'] ?? [], [], $e->getMessage(), null, $e);
        }
    }
}

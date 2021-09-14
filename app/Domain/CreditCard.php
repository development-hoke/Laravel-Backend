<?php

namespace App\Domain;

use App\Domain\Adapters\FRegiAdapterInterface as FRegiAdapter;
use App\Exceptions\InvalidArgumentValueException;
use App\HttpCommunication\Exceptions\FRegiResponseException;
use App\Repositories\MemberCreditCardRepository;
use App\Repositories\OrderCreditRepository;

class CreditCard implements CreditCardInterface
{
    /**
     * @var OrderCreditRepository
     */
    private $orderCreditRepository;

    /**
     * @var MemberCreditCardRepository
     */
    private $memberCreditCardRepository;

    /**
     * @var FRegiAdapter
     */
    private $fRegiAdapter;

    public function __construct(
        OrderCreditRepository $orderCreditRepository,
        MemberCreditCardRepository $memberCreditCardRepository,
        FRegiAdapter $fRegiAdapter
    ) {
        $this->orderCreditRepository = $orderCreditRepository;
        $this->memberCreditCardRepository = $memberCreditCardRepository;
        $this->fRegiAdapter = $fRegiAdapter;
    }

    /**
     * クレジット決済 承認処理
     *
     * @param \App\Models\Order $order
     * @param array $params
     *
     * @return \App\Models\OrderCredit
     *
     * @throws \App\Exceptions\InvalidArgumentValueException
     */
    public function auth(\App\Models\Order $order, array $params)
    {
        $payload = [
            'total_price' => $order->price,
            'order_id' => $order->id,
            'payment_method' => $params['payment_method'],
            'use_saved_card_info' => $params['use_saved_card_info'],
            'ip' => $params['ip'] ?? null,
        ];

        if ($params['use_saved_card_info']) {
            $memberCreditCard = $this->memberCreditCardRepository->findWhere([
                'id' => $params['member_credit_card_id'],
                'member_id' => $order->member_id,
            ])->first();

            if (empty($memberCreditCard)) {
                throw new InvalidArgumentValueException(
                    error_format('error.invalid_argument_value', [
                        'member_credit_card_id' => $params['member_credit_card_id'],
                        'member_id' => $order->member_id,
                    ]),
                    self::ERR_MEMBER_CREDIT_CARD_NOT_FOUND
                );
            }

            $payload['member_credit_card_id'] = $memberCreditCard->id;
            $payload['is_save_card_info'] = false;
        } else {
            $payload['token'] = $params['token'];
            $payload['is_save_card_info'] = $params['is_save_card_info'];

            if ($params['is_save_card_info']) {
                $memberCreditCard = $this->memberCreditCardRepository->updateOrCreate(
                    ['member_id' => $order->member_id],
                    ['payment_method' => $params['payment_method']]
                );

                $payload['member_credit_card_id'] = $memberCreditCard->id;
            }
        }

        $result = $this->fRegiAdapter->auth($payload);

        $orderCredit = $this->createOrderCredit($order->id, $result, [
            'payment_method' => $payload['payment_method'],
            'member_credit_card_id' => $payload['member_credit_card_id'] ?? null,
        ]);

        return $orderCredit;
    }

    /**
     * 決済情報保存
     *
     * @param int $orderId
     * @param \App\Entities\FRegi\AuthResult $authResult
     * @param array $params
     *
     * @return \App\Models\OrderCredit
     */
    private function createOrderCredit(int $orderId, \App\Entities\FRegi\AuthResult $authResult, array $params)
    {
        return $this->orderCreditRepository->create([
            'order_id' => $orderId,
            'authorization_number' => $authResult->authorization_number,
            'transaction_number' => $authResult->transaction_number,
            'status' => \App\Enums\OrderCredit\Status::Authorized,
            'payment_method' => $params['payment_method'],
            'member_credit_card_id' => $params['member_credit_card_id'] ?? null,
        ]);
    }

    /**
     * 顧客情報取得
     *
     * @param int $memberId
     *
     * @return \App\Models\MemberCreditCard|null
     */
    public function fetchCustomerInfo(int $memberId)
    {
        $memberCreditCard = $this->memberCreditCardRepository->findDefaultInfo($memberId);

        if (empty($memberCreditCard)) {
            return null;
        }

        $customerInfo = $this->fRegiAdapter->fetchCustomerInfo($memberCreditCard->id);

        $memberCreditCard->customerInfo = $customerInfo;

        return $memberCreditCard;
    }

    /**
     * オーソリキャンセル
     *
     * @param int $orderId
     *
     * @return bool
     */
    public function authCancel(int $orderId)
    {
        $orderCredit = $this->orderCreditRepository->findByOrderId($orderId);

        $this->fRegiAdapter->authCancel($orderCredit->transaction_number);

        $orderCredit = $this->execInternalCancel($orderCredit->id);

        return true;
    }

    /**
     * 承認金額変更
     *
     * @param int $orderId
     * @param int $price
     *
     * @return \App\Models\OrderCredit
     */
    public function changeAuthPrice(int $orderId, int $price)
    {
        $orderCredit = $this->orderCreditRepository->findByOrderId($orderId);

        $authChangeResult = $this->fRegiAdapter->authChange($orderCredit->transaction_number, [
            'price' => $price,
        ]);

        $orderCredit = $this->execInternalCancel($orderCredit->id);

        $newOrderCredit = $this->createOrderCredit($orderId, $authChangeResult, [
            'payment_method' => $orderCredit->payment_method,
            'member_credit_card_id' => $orderCredit->member_credit_card_id,
        ]);

        return $newOrderCredit;
    }

    /**
     * @param int $orderCreditId
     *
     * @return \App\Models\OrderCredit
     */
    private function execInternalCancel(int $orderCreditId)
    {
        $orderCredit = $this->orderCreditRepository->update([
            'status' => \App\Enums\OrderCredit\Status::Canceled,
        ], $orderCreditId);

        $orderCredit->delete();

        return $orderCredit;
    }

    /**
     * クレジット決済 売り上げ処理
     *
     * @param int $orderId
     *
     * @return \App\Models\OrderCredit
     */
    public function sale(int $orderId)
    {
        $orderCredit = $this->orderCreditRepository->with('order')->findByOrderId($orderId);

        try {
            $this->fRegiAdapter->sale($orderCredit->authorization_number, $orderCredit->transaction_number);
        } catch (FRegiResponseException $e) {
            throw $e;
            // 期限切れの場合の再オーソリ処理。
            // 処理自体不要になったが、今後必要になる可能性があるのでコメントアウトしておく。

            // // 有効期限切れの場合最オーソリを試みる
            // if ($e->getErrorCode() !== \App\Enums\FRegi\ErrorCode::SaleAuthExpired) {
            //     throw $e;
            // }

            // $memberCreditCard = $orderCredit->memberCreditCard;

            // if (empty($memberCreditCard)) {
            //     throw $e;
            // }

            // $this->orderCreditRepository->update([
            //     'status' => \App\Enums\OrderCredit\Status::Expired,
            // ], $orderCredit->id);

            // $this->orderCreditRepository->delete($orderCredit->id);

            // $orderCredit = $this->auth($orderCredit->order, [
            //     'payment_method' => $orderCredit->payment_method,
            //     'member_credit_card_id' => $memberCreditCard->id,
            //     'use_saved_card_info' => true,
            // ]);

            // $this->fRegiAdapter->sale($orderCredit->authorization_number, $orderCredit->transaction_number);
        }

        $orderCredit = $this->orderCreditRepository->update([
            'status' => \App\Enums\OrderCredit\Status::Captured,
        ], $orderCredit->id);

        return $orderCredit;
    }

    /**
     * 売上取消
     *
     * @param int $orderId
     *
     * @return void
     */
    public function saleCancel(int $orderId)
    {
        $orderCredit = $this->orderCreditRepository->findByOrderId($orderId);

        $this->fRegiAdapter->saleCancel($orderCredit->transaction_number);

        $this->execInternalCancel($orderCredit->id);
    }

    /**
     * 売上金額の変更
     *
     * @param int $orderId
     * @param int $price
     *
     * @return \App\Models\OrderCredit
     */
    public function changeSalePrice(int $orderId, int $price)
    {
        $orderCredit = $this->orderCreditRepository->findByOrderId($orderId);

        $saleResult = $this->fRegiAdapter->saleChange($orderCredit->transaction_number, [
            'price' => $price,
        ]);

        $newOrderCredit = $this->orderCreditRepository->create(array_merge($orderCredit->toArray(), [
            'transaction_number' => $saleResult->transaction_number,
        ]));

        $this->execInternalCancel($orderCredit->id);

        return $newOrderCredit;
    }

    /**
     * 顧客情報削除
     *
     * @param int $id
     *
     * @return bool
     */
    public function deleteCustomerInfo(int $id)
    {
        $memberCreditCard = $this->memberCreditCardRepository->find($id);

        $this->fRegiAdapter->leaveCustomer($memberCreditCard->id);

        $this->memberCreditCardRepository->delete($id);

        return true;
    }
}

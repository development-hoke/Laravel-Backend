<?php

namespace App\Services\Admin;

use App\Domain\Adapters\Ymdy\MemberPurchaseInterface as MemberPurchaseAdapter;
use App\Domain\Adapters\Ymdy\Purchase as PurchaseAdapter;
use App\Domain\Exceptions\NpFailedAuthorizationException;
use App\Domain\Exceptions\NpFailedCancelForReregisteringException;
use App\Domain\Exceptions\NpPaymentException;
use App\Domain\Exceptions\NpPaymentReregisterResponseException;
use App\Domain\Exceptions\NpPaymentResponseException;
use App\Domain\Exceptions\NpPaymentUnsolvedFailedTransactionException;
use App\Domain\Exceptions\NpPaymentValidationException;
use App\Domain\Exceptions\PaymentException;
use App\Domain\Exceptions\PaymentRefundException;
use App\Domain\Exceptions\PaymentRefundPartiallyException;
use App\Domain\Exceptions\PaymentUpdateBillingAmountException;
use App\Domain\Exceptions\PaymentUpdateShippingAddressException;
use App\Domain\MemberInterface as MemberService;
use App\Exceptions\InvalidInputException;
use App\Http\Response;
use App\HttpCommunication\AmazonPay\Exceptions\HttpException as AmazonPayHttpException;
use App\HttpCommunication\Exceptions\FRegiResponseException;
use App\Repositories\OrderRepository;
use App\Utils\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Lang;
use Symfony\Component\HttpKernel\Exception\HttpException;

abstract class BaseOrderService extends Service
{
    const MAXIMAM_MEMBER_CONDITION_COUNT = 200;

    /**
     * @var MemberService
     */
    protected $memberService;

    /**
     * @var OrderRepository
     */
    protected $orderRepository;

    /**
     * @var MemberPurchaseAdapter
     */
    protected $memberPurchaseAdapter;

    /**
     * @var PurchaseAdapter
     */
    protected $purchaseAdapter;

    /**
     * @param MemberService $memberService
     */
    public function __construct(
        MemberService $memberService,
        PurchaseAdapter $purchaseAdapter,
        MemberPurchaseAdapter $memberPurchaseAdapter,
        OrderRepository $orderRepository
    ) {
        $this->orderRepository = $orderRepository;
        $this->memberService = $memberService;
        $this->purchaseAdapter = $purchaseAdapter;
        $this->memberPurchaseAdapter = $memberPurchaseAdapter;

        if (auth('admin_api')->check()) {
            $token = auth('admin_api')->user()->token;
            $this->memberPurchaseAdapter->setStaffToken($token);
            $this->memberService->setStaffToken($token);
        }
    }

    /**
     * 検索
     *
     * @param array $params
     *
     * @return array
     */
    protected function fetchMemberIds(array $memberSearchParams)
    {
        $memberSearchParams['per_page'] = self::MAXIMAM_MEMBER_CONDITION_COUNT + 1;
        $members = $this->memberService->search($memberSearchParams);

        if (empty($members)) {
            return [];
        }

        if (count($members) > self::MAXIMAM_MEMBER_CONDITION_COUNT) {
            throw new InvalidInputException(__('error.too_large_search_results', ['name' => __('resource.member')]));
        }

        return array_map(function ($member) {
            return $member['id'];
        }, $members);
    }

    /**
     * 会員検索パラメータを抽出
     *
     * @param array $params
     *
     * @return array
     */
    protected function extractMemberSearchParams(array $params)
    {
        $memberParamMap = [
            'member_name' => 'name',
            'member_email' => 'email',
            'member_phone_number' => 'tel',
        ];

        $memberSearchParams = Arr::reduce($memberParamMap, function ($memberSearchParams, $to, $from) use ($params) {
            if (isset($params[$from])) {
                $memberSearchParams[$to] = $params[$from];
            }

            return $memberSearchParams;
        }, []);

        return $memberSearchParams;
    }

    /**
     * 金額変更時の決済システム連携エラーの処理
     *
     * @param PaymentUpdateBillingAmountException $exception
     *
     * @return void
     */
    protected function handleUpdateBillingAmountError(PaymentUpdateBillingAmountException $exception)
    {
        $paymentType = $exception->getPaymentType();

        switch ((int) $paymentType) {
            case \App\Enums\Order\PaymentType::AmazonPay:
                $this->handleAmazonPayError($exception, 'update_billing_amount');
                break;

            case \App\Enums\Order\PaymentType::CreditCard:
                $this->handleCreditCardError($exception);
                break;

            case \App\Enums\Order\PaymentType::NP:
                $this->handleNpPaymentUpdateBillingAmountError($exception);
                break;
        }

        throw new HttpException(Response::HTTP_INTERNAL_SERVER_ERROR, __('validation.payment_change_auth_amount'));
    }

    /**
     * 返品時の決済システム連携エラーの処理
     *
     * @param PaymentRefundException $exception
     *
     * @return void
     */
    protected function handleRefundError(PaymentRefundException $exception)
    {
        $paymentType = $exception->getPaymentType();

        switch ((int) $paymentType) {
            case \App\Enums\Order\PaymentType::AmazonPay:
                $this->handleAmazonPayError($exception, 'refund');
                break;

            case \App\Enums\Order\PaymentType::CreditCard:
                $this->handleCreditCardError($exception);
                break;

            case \App\Enums\Order\PaymentType::NP:
                $this->handleNpPaymentError($exception);
                break;
        }

        throw new HttpException(Response::HTTP_INTERNAL_SERVER_ERROR, __('validation.payment_refund'));
    }

    /**
     * 一部返品時の決済システム連携エラーの処理
     *
     * @param PaymentRefundPartiallyException $exception
     *
     * @return void
     */
    protected function handleRefundPartiallyError(PaymentRefundPartiallyException $exception)
    {
        $paymentType = $exception->getPaymentType();

        switch ((int) $paymentType) {
            case \App\Enums\Order\PaymentType::AmazonPay:
                $this->handleAmazonPayError($exception, 'refund');
                break;

            case \App\Enums\Order\PaymentType::CreditCard:
                $this->handleCreditCardError($exception);
                break;

            case \App\Enums\Order\PaymentType::NP:
                $this->handelNpPaymentReturnPartiallyError($exception);
                break;
        }

        throw new HttpException(Response::HTTP_INTERNAL_SERVER_ERROR, __('validation.payment_refund_partially'));
    }

    /**
     * 決済システムの配送先連携エラーの処理
     *
     * @param PaymentUpdateShippingAddressException $exception
     *
     * @return void
     */
    protected function handleUpdateShippingAddressError(PaymentUpdateShippingAddressException $exception)
    {
        $paymentType = $exception->getPaymentType();

        switch ((int) $paymentType) {
            case \App\Enums\Order\PaymentType::NP:
                $this->handleNpPaymentError($exception);
                break;
        }

        throw new HttpException(Response::HTTP_INTERNAL_SERVER_ERROR, __('validation.payment_sync_update_destination'));
    }

    /**
     * @param PaymentException $exception
     * @param string $action
     *
     * @return void
     */
    private function handleAmazonPayError(PaymentException $exception, string $action)
    {
        $previous = $exception->getPrevious();

        if ($previous instanceof AmazonPayHttpException) {
            $messages = Lang::get('validation.amazon_pay.' . $action);
            $errorCode = $previous->getAmazonErrorCode();

            if (isset($messages[$errorCode])) {
                throw new InvalidInputException($messages[$errorCode]);
            }
        }

        return;
    }

    /**
     * @param PaymentException $exception
     * @param string $action
     *
     * @return void
     */
    private function handleCreditCardError(PaymentException $exception)
    {
        $previous = $exception->getPrevious();

        if ($previous instanceof FRegiResponseException) {
            $messages = Lang::get('validation.card.error');
            $errorCode = $previous->getErrorCode();

            if (isset($messages[$errorCode])) {
                throw new InvalidInputException($messages[$errorCode]);
            }
        }

        return;
    }

    private function handleNpPaymentUpdateBillingAmountError(PaymentException $exception)
    {
        $previous = $exception->getPrevious();

        if ($previous instanceof NpFailedAuthorizationException || $previous instanceof NpPaymentUnsolvedFailedTransactionException) {
            $this->fallbackNpFailedUpdateTransaction($previous);
        }

        $this->handleNpPaymentError($exception);
    }

    /**
     * 注文内容更新時に与信結果がNG/保留となった場合の処理
     *
     * @param NpFailedAuthorizationException|NpPaymentUnsolvedFailedTransactionException $exception
     *
     * @return void
     */
    private function fallbackNpFailedUpdateTransaction(NpPaymentException $exception)
    {
        DB::rollBack();

        $order = $exception->getOrder();
        $order = $this->orderRepository->update(['status' => \App\Enums\Order\Status::Pending], $order->id);

        /** @var \App\Domain\NpPaymentInterface */
        $npPaymentService = resolve(\App\Domain\NpPaymentInterface::class);
        $status = $exception instanceof NpPaymentUnsolvedFailedTransactionException ? \App\Enums\OrderNp\Status::AuthRejectedCancelFailed : null;
        $npPaymentService->importFailedTransactionStatus($exception->getTransaction(), $status);

        // FIXME: 会員ポイント/商品基幹と受注ステータス更新を連携する
        $ecBill = $this->purchaseAdapter->makeEcBill($order);
        $this->memberPurchaseAdapter->updateMemberPurchaseAndUpdateTax($order, $ecBill);
    }

    /**
     * NP後払い一部返品の時のエラー処理
     *
     * @param PaymentException $exception
     *
     * @return void
     */
    private function handelNpPaymentReturnPartiallyError(PaymentException $exception)
    {
        $previous = $exception->getPrevious();

        if ($previous instanceof NpPaymentReregisterResponseException) {
            DB::rollBack();
            $order = $exception->getOrder();
            /** @var \App\Domain\NpPaymentInterface */
            $npPaymentService = resolve(\App\Domain\NpPaymentInterface::class);
            $npPaymentService->updateOrderNpToCanceledButFailedReregister($order->id);

            $message = $this->extractNpPaymentErrorMessage($previous);

            $message = is_null($message) === false
                ? __('validation.np.admin.canceled_but_failed_reregister_with_reason', ['error' => $message])
                : __('validation.np.admin.canceled_but_failed_reregister');

            throw new InvalidInputException($message, null, $previous);
        }

        $this->handleNpPaymentError($exception);
    }

    /**
     * @param PaymentException $exception
     *
     * @return void
     */
    private function handleNpPaymentError(PaymentException $exception)
    {
        $previous = $exception->getPrevious();
        $messages = Lang::get('validation.np');

        if ($previous instanceof NpFailedCancelForReregisteringException) {
            $previous = $previous->getPrevious();
        }

        if ($previous instanceof NpFailedAuthorizationException || $previous instanceof NpPaymentUnsolvedFailedTransactionException) {
            $transaction = $previous->getTransaction();

            if ($transaction->authori_result === \App\Enums\Np\AuthoriResult::NG) {
                throw new InvalidInputException($messages['auth_ng'][$transaction->authori_ng]);
            }

            throw new InvalidInputException($messages['admin']['auth_pending']);
            // 保留コードに応じたエラーメッセージ。
            // 更新時に与信結果が保留になった場合、他の決済種別へ変更するフローになっているが、今後対応が必要になる可能性もあるので残しておく。
            // $holdCodes = (array) $transaction->authori_hold;
            // $requestParams = $previous->getRequestParams();

            // foreach ($holdCodes as $holdCode) {
            //     if (isset($messages['auth_pending'][$holdCode])) {
            //         $message = $messages['auth_pending'][$holdCode];

            //         switch ($holdCode) {
            //             case \App\Enums\Np\PendingReasonCode::InsufficientAddressInformation:
            //             case \App\Enums\Np\PendingReasonCode::ConfirmationAddress:
            //             case \App\Enums\Np\PendingReasonCode::InsufficientDeliveryAddressInformation:
            //             case \App\Enums\Np\PendingReasonCode::ConfirmationDeliveryAddress:
            //                 $message = str_replace(':value', $requestParams['customer']['address'] ?? [], $message);
            //         }

            //         throw new InvalidInputException($messages['auth_pending'][$holdCode]);
            //     }
            // }
        }

        if ($previous instanceof NpPaymentResponseException) {
            $message = $this->extractNpPaymentErrorMessage($previous);

            if (!is_null($message)) {
                throw new InvalidInputException($message);
            }
        }

        if ($previous instanceof NpPaymentValidationException) {
            throw new InvalidInputException($previous->getMessage(), null, $previous);
        }

        return;
    }

    /**
     * @param NpPaymentResponseException $exception
     *
     * @return string|null
     */
    private function extractNpPaymentErrorMessage(NpPaymentResponseException $exception)
    {
        $errors = $exception->getErrors();
        $messages = Lang::get('validation.np');

        foreach ($errors as $errorCode) {
            if (isset($messages['admin']['error'][$errorCode])) {
                return $messages['admin']['error'][$errorCode];
            }

            if (isset($messages['auth'][$errorCode])) {
                return $messages['auth'][$errorCode];
            }
        }

        return;
    }
}

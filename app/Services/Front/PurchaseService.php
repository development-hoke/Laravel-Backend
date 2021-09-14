<?php

namespace App\Services\Front;

use App\Domain\Adapters\FRegiAdapterInterface as FRegiAdapter;
use App\Domain\Adapters\Ymdy\MemberPurchase;
use App\Domain\Adapters\Ymdy\Purchase as PurchaseAdapter;
use App\Domain\AmazonPayInterface as AmazonPayService;
use App\Domain\CouponInterface as CouponService;
use App\Domain\CreditCardInterface as CreditCardService;
use App\Domain\NpPaymentInterface as NpPaymentService;
use App\Domain\OrderInterface as DomainOrderService;
use App\Entities\Ymdy\Member\EcBill;
use App\Enums\Order\PaymentType;
use App\Exceptions\FatalException;
use App\Exceptions\InvalidInputException;
use App\Http\Response;
use App\HttpCommunication\NP\PurchaseInterface as NPPurchaseInterface;
use App\HttpCommunication\SendGrid\SendGridServiceInterface;
use App\HttpCommunication\Shohin\ItemInterface;
use App\HttpCommunication\Ymdy\MemberInterface;
use App\Mail\Delivered;
use App\Models\Cart;
use App\Models\Order;
use App\Repositories\CartRepository;
use App\Repositories\EventRepository;
use App\Services\Service;
use App\Utils\Notification;
use App\Utils\OrderLog;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Session;
use Symfony\Component\HttpKernel\Exception\HttpException;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class PurchaseService extends Service implements PurchaseServiceInterface
{
    /** @var EventRepository */
    protected $eventRepository;

    /** @var CartRepository */
    protected $cartRepository;

    /** @var FRegiAdapter */
    protected $fRegiAdapter;

    /** @var NPPurchaseInterface */
    protected $npPurchaseHttp;

    /** @var CartService */
    protected $cartService;

    /** @var MemberInterface */
    protected $memberHttp;

    /** @var ItemInterface */
    protected $itemHttp;

    /** @var PointServiceInterface */
    protected $pointService;

    /** @var SendGridServiceInterface */
    protected $sendGridService;

    /** @var OrderService */
    protected $orderService;

    /** @var CouponService */
    protected $couponService;

    /** @var DomainOrderService */
    protected $domainOrderService;

    /** @var MemberPurchase */
    protected $memberPurchase;

    /** @var PurchaseAdapter */
    protected $purchaseAdapter;

    /** @var AmazonPayService */
    protected $amazonPayService;

    /** @var CreditCardService */
    protected $creditCardService;

    /** @var NpPaymentService */
    protected $npPaymentService;

    /**
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        EventRepository $eventRepository,
        CartRepository $cartRepository,
        FRegiAdapter $fRegiAdapter,
        NPPurchaseInterface $npPurchaseHttp,
        CartService $cartService,
        OrderService $orderService,
        MemberInterface $memberHttp,
        ItemInterface $itemHttp,
        PointServiceInterface $pointService,
        SendGridServiceInterface $sendGridService,
        CouponService $couponService,
        DomainOrderService $domainOrderService,
        MemberPurchase $memberPurchase,
        PurchaseAdapter $purchaseAdapter,
        AmazonPayService $amazonPayService,
        CreditCardService $creditCardService,
        NpPaymentService $npPaymentService
    ) {
        $this->eventRepository = $eventRepository;
        $this->cartRepository = $cartRepository;
        $this->fRegiAdapter = $fRegiAdapter;
        $this->npPurchaseHttp = $npPurchaseHttp;
        $this->cartService = $cartService;
        $this->orderService = $orderService;
        $this->memberHttp = $memberHttp;
        $this->itemHttp = $itemHttp;
        $this->pointService = $pointService;
        $this->sendGridService = $sendGridService;
        $this->couponService = $couponService;
        $this->domainOrderService = $domainOrderService;
        $this->memberPurchase = $memberPurchase;
        $this->purchaseAdapter = $purchaseAdapter;
        $this->amazonPayService = $amazonPayService;
        $this->creditCardService = $creditCardService;
        $this->npPaymentService = $npPaymentService;

        if (auth('api')->check()) {
            $user = auth('api')->user();

            $this->memberHttp->setMemberTokenHeader($user->token);
            $this->memberPurchase->setMemberToken($user->token);
            $this->couponService->setMemberToken($user->token);
            $this->domainOrderService->setMemberToken($user->token);
        }
    }

    /**
     * クレジット決済 承認処理
     *
     * @param Order $order
     * @param array $params
     * @param \App\Models\Cart $cart
     */
    public function auth(Order $order, array $params, \App\Models\Cart $cart)
    {
        try {
            OrderLog::purchased($order, __FUNCTION__, [
                'order' => $order,
                'card' => $params,
            ]);

            $this->creditCardService->auth($order, $params);

            // 予約注文はオーソリ期限切れの可能性を排除するためそのまま売上確定処理をする。
            if ((int) $order->order_type === \App\Enums\Order\OrderType::Reserve) {
                try {
                    $this->creditCardService->sale($order->id);
                } catch (\Exception $e) {
                    Notification::sendSlackError('予約注文の売上確定に失敗しました。', [
                        'member_id' => $order->member_id,
                        'cart_id' => $cart->id,
                    ]);

                    throw new HttpException(Response::HTTP_INTERNAL_SERVER_ERROR, __('validation.card.failed_reservation_sale'), $e);
                }
            }
        } catch (\Exception $e) {
            if ($e instanceof HttpException) {
                throw $e;
            }

            if ($e instanceof \App\Exceptions\InvalidArgumentValueException) {
                if ($e->getCode() === \App\Domain\CreditCardInterface::ERR_MEMBER_CREDIT_CARD_NOT_FOUND) {
                    throw new InvalidInputException(__('validation.card.not_found_saved_card_info'), null, $e);
                }
            }

            if ($e instanceof \App\HttpCommunication\Exceptions\FRegiResponseException) {
                OrderLog::purchased($order, __('error.fail_fregi_auth_for_log'), $e->getResponseBody());

                if (!$e->isClientError()) {
                    throw new FatalException(error_format('error.freg_failed_auth', [
                        'member_id' => $order->member_id,
                    ]), null, $e);
                }

                switch ($e->getErrorCode()) {
                    case \App\Enums\FRegi\ErrorCode::ExpiredToken:
                        throw new InvalidInputException(__('validation.card.expired_token'), null, $e);
                    case \App\Enums\FRegi\ErrorCode::ExceedUsageCountLimit:
                    case \App\Enums\FRegi\ErrorCode::ExceedUsageAmountLimit:
                        throw new InvalidInputException(__('validation.card.exceed_usage_limit'), null, $e);
                    default:
                        throw new InvalidInputException(__('validation.card.invalid_card'), null, $e);
                }
            }

            OrderLog::purchased($order, __('error.fail_fregi_auth_for_log'));

            throw new FatalException(error_format('error.freg_failed_auth', [
                'member_id' => $order->member_id,
            ]), null, $e);
        }
    }

    /**
     * NP後払い 承認処理
     *
     * @param Order $order
     * @param Cart $cart
     *
     * @return \App\Models\OrderNp
     */
    public function npTransaction(Order $order, Cart $cart)
    {
        try {
            OrderLog::purchased($order, __FUNCTION__, [
                'order' => $order->toArray(),
            ]);

            $orderNp = $this->npPaymentService->createTransaction($order);

            return $orderNp;
        } catch (\App\Domain\Exceptions\NpPaymentResponseException $e) {
            if ($e instanceof \App\Domain\Exceptions\NpFailedAuthorizationException) {
                // NOTE: 保留・NGに関してはログとしてDBに保存する。
                // DB保存するために先にロールバックする。
                DB::rollBack();

                $transaction = $e->getTransaction();

                $this->npPaymentService->createNpRejectedTransaction($cart->id, $order->member_id, $transaction);

                if ($transaction->authori_result === \App\Enums\Np\AuthoriResult::Pending) {
                    throw new InvalidInputException(__('validation.np.auth_pending'), null, $e);
                }

                $messages = Lang::get('validation.np.auth_ng');

                throw new InvalidInputException($messages[$transaction->authori_ng] ?? $messages[\App\Enums\Np\NGReasonCode::Other], null, $e);
            }

            $messages = Lang::get('validation.np.auth');

            foreach ($e->getErrors() as $errorCode) {
                if (isset($messages[$errorCode])) {
                    throw new InvalidInputException($messages[$errorCode]);
                }
            }

            $ex = $e->getPrevious();

            if ($ex instanceof \App\HttpCommunication\Exceptions\HttpException) {
                // 5xx
                if ($ex->getResponseStatusCode() >= 500) {
                    if ($ex->getResponseStatusCode() === Response::HTTP_SERVICE_UNAVAILABLE) {
                        throw new HttpException(Response::HTTP_SERVICE_UNAVAILABLE, __('validation.np.maintenance'));
                    }

                    throw new HttpException(Response::HTTP_INTERNAL_SERVER_ERROR, __('validation.np.system_error'));
                }

                // ステータスコード5xx系ではなく、対応するエラーコードが特定できない場合、
                // ECシステム側のパラメータ指定方法などに問題がある可能性があるため
                // システムエラーとして扱う。
                throw new FatalException(__('error.np_unsupported_error_code'), null, $e);
            }

            throw $e;
        }
    }

    /**
     * 注文処理
     *
     * @param Cart $cart
     * @param array $params
     *
     * @return array
     *
     * @throws FatalException
     */
    public function order(Cart $cart, array $params)
    {
        try {
            DB::beginTransaction();

            // 2重リクエストの防止
            $this->cartRepository->scopeQuery(function ($query) {
                return $query->lockForUpdate();
            })->find($cart->id);

            // DBへ保存
            $order = $this->storeToMember($cart, $params);

            // 外部連携用Entity生成
            $ecBill = $this->purchaseAdapter->makeEcBill($order);

            // 商品基幹販売情報登録
            // 商品基幹が会員ポイントと同じになるまでコメントアウト
            // $this->storeToShohin($order, $ecBill);

            // 会員ポイント販売情報登録
            try {
                if ($cart->is_guest) {
                    $this->memberPurchase->setMemberToken($params['member_token']);
                }

                $this->memberPurchase->createMemberPurchaseAndUpdateTax($order, $ecBill);
            } catch (\App\HttpCommunication\Exceptions\HttpException $e) {
                $this->orderService->updateErrorMemo($order, __('error.failed_to_purchase_member_system', ['message' => $e->getMessage()]));
            }

            // 注文完了メール
            \App\Jobs\NotifyOrdered::dispatch($order, $params);

            // // 予約在庫僅少メール
            // $this->sendLowInventroryMail($params);

            // カート削除
            $newCart = $this->transferCartItemsAndDelete($cart->id);

            DB::commit();

            return ['order' => $order, 'new_cart' => $newCart];
        } catch (Exception $e) {
            DB::rollBack();
            OrderLog::unPurchased(Session::getId(), $e->getMessage());

            if ($e instanceof \App\Exceptions\FailedAddingCouponException) {
                throw new InvalidInputException($e->getMessage(), null, $e);
            }

            if ($e instanceof \App\Domain\Exceptions\StockShortageException) {
                throw new InvalidInputException(__('error.not_exists_enouch_stock'), \App\Enums\Common\ErrorCode::StockShortage, $e);
            }

            throw $e;
        }
    }

    private function transferCartItemsAndDelete(int $cartId)
    {
        $newCart = $this->cartRepository->transferCartItems($cartId);

        $this->cartRepository->delete($cartId);

        return $newCart;
    }

    /**
     * 注文登録
     *
     * @param Cart $cart
     * @param array $params
     *
     * @return Order
     */
    private function storeToMember(Cart $cart, array $params)
    {
        $sessionId = Session::getId();
        OrderLog::unPurchased($sessionId, __FUNCTION__, [
            'cart' => $cart->toArray(),
            'params' => $params,
        ]);
        $response = $this->memberHttp->showMember(
            $cart->member_id,
            $params['is_guest'] ? $params['member_token'] : null
        )->getBody();
        $order = $this->orderService->createOrder($cart, $params);
        OrderLog::moveToPurchased($sessionId, $order);

        // order_detailsに登録
        $cart->member = $response['member'];
        $this->orderService->createOrderDetail($cart, $order);

        // クーポンを作成する。商品タイプと送料タイプ2種類のorder_discountsが作成される。
        // 商品タイプはorder_detailsと紐づくため、order_details作成後にクーポンを作成する。
        $this->couponService->addCoupons($cart->use_coupon_ids, $order);

        // 最終的な受注金額の計算を行う。
        $this->domainOrderService->updateTotalPrice($order);

        // order_addressに登録(請求先・配送先)
        // Amazon Payの場合、別の処理経路になるため、オーソリ処理と一緒に実行する。
        if ($order->payment_type !== PaymentType::AmazonPay) {
            if ($params['is_guest']) {
                $this->orderService->createGuestOrderAddresses($order, $params, $response['member']);
            } else {
                $this->orderService->createOrderAddresses($order, $params, $response['member']);
            }
        }

        // order_messages
        if (isset($params['message'])) {
            $message = $params['message'];
            $this->orderService->createOrderMessage($order, $message);
        }

        // 与信の取得処理
        switch ($order->payment_type) {
            case PaymentType::CreditCard:
                $this->auth($order, array_merge($params['card'], ['ip' => $params['ip']]), $cart);
                break;

            case PaymentType::NP:
                $this->npTransaction($order, $cart);
                break;

            case PaymentType::AmazonPay:
                $this->careteAmazonPayOrderAddressAndOrder($order, $params, $response['member']);
                break;

            default:
        }

        return $order;
    }

    private function careteAmazonPayOrderAddressAndOrder(\App\Models\Order $order, array $params, array $member)
    {
        $amazonPayOrder = $this->amazonPayService->order($params['amazon_order_reference_id'], $order->id, $params);

        $orderReferenceDetails = $amazonPayOrder->orderReferenceDetails;

        $this->orderService->createOrderAddressesByAmazonPay($order, $orderReferenceDetails, $member);
    }

    /**
     * 商品基幹へ販売情報登録
     *
     * @param Order $order
     * @param EcBill $ecBill
     *
     * @return array
     */
    public function storeToShohin(Order $order, \App\Entities\Ymdy\Member\EcBill $ecBill)
    {
        OrderLog::purchased($order, __FUNCTION__, [
            'order' => $order->toArray(),
        ]);

        return $this->itemHttp->purchase($ecBill->toArray())->getBody();
    }

    /**
     * 配送完了メール送信
     */
    public function sendDeliveredMail(array $params)
    {
        $data = [
            'email' => $params['order']->memberOrderAddress->email,
            'lname' => $params['order']->memberOrderAddress->lname,
            'fname' => $params['order']->memberOrderAddress->fname,
            'order' => $params['order'],
            'orderDetails' => $params['orderDetails'],
        ];
        $mail = new Delivered($data);

        $mail->to($data['email'], $data['lname'] . $data['fname']);

        $this->sendGridService->send($mail);
    }

    /**
     * カード情報取得
     *
     * @return \App\Models\MemberCreditCard|null
     */
    public function fetchCreditCardInfo()
    {
        $memberId = auth('api')->id();

        $memberCreditCard = $this->creditCardService->fetchCustomerInfo($memberId);

        return $memberCreditCard;
    }

    /**
     * カード情報削除
     *
     * @param int $id
     *
     * @return bool
     */
    public function deleteCreditCardInfo(int $id)
    {
        $this->creditCardService->deleteCustomerInfo($id);

        return true;
    }
}

<?php

namespace App\Services\Front;

use App\Domain\ItemOrderDiscountInterface as ItemOrderDiscountService;
use App\Domain\ItemPriceInterface as ItemPriceService;
use App\Domain\OrderInterface as DomainOrderService;
use App\Domain\Utils\OrderPrice;
use App\Enums\Order\PaymentType;
use App\Enums\Order\Request;
use App\Enums\Order\Status;
use App\Enums\OrderMessage\Type;
use App\HttpCommunication\Ymdy\MemberShippingAddressInterface;
use App\Models\Cart;
use App\Models\Order;
use App\Repositories\EventRepository;
use App\Repositories\ItemDetailIdentificationRepository;
use App\Repositories\ItemDetailRepository;
use App\Repositories\OrderAddressRepository;
use App\Repositories\OrderCreditRepository;
use App\Repositories\OrderDetailRepository;
use App\Repositories\OrderDetailUnitRepository;
use App\Repositories\OrderDiscountRepository;
use App\Repositories\OrderMessageRepository;
use App\Repositories\OrderNpRepository;
use App\Repositories\OrderRepository;
use App\Repositories\PrefRepository;
use App\Services\Service;
use Carbon\Carbon;

/**
 * @SuppressWarnings(PHPMD.ExcessiveParameterList)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class OrderService extends Service implements OrderServiceInterface
{
    /** @var ItemDetailRepository */
    protected $itemDetailRepository;

    /** @var ItemDetailIdentificationRepository */
    protected $identificationRepository;

    /** @var OrderRepository */
    protected $orderRepository;

    /** @var OrderAddressRepository */
    protected $orderAddressRepository;

    /** @var OrderCreditRepository */
    protected $orderCreditRepository;

    /** @var OrderNpRepository */
    protected $orderNpRepository;

    /** @var OrderMessageRepository */
    protected $orderMessageRepository;

    /** @var OrderDetailRepository */
    protected $orderDetailRepository;

    /** @var OrderDetailUnitRepository */
    protected $orderDetailUnitRepository;

    protected $eventRepository;

    /** @var MemberShippingAddressInterface */
    protected $memberShippingAddressHttp;

    /** @var ItemPriceService */
    protected $itemPriceService;

    /** @var OrderDiscountRepository */
    protected $orderDiscountRepository;

    /** @var DomainOrderService */
    protected $domainOrderService;

    /** @var ItemOrderDiscountService */
    protected $itemOrderDiscountService;

    /** @var PrefRepository */
    protected $prefRepository;

    /**
     * @param ItemDetailRepository $itemDetailRepository
     * @param ItemDetailIdentificationRepository $identificationRepository
     * @param OrderAddressRepository $orderAddressRepository
     * @param OrderRepository $orderRepository
     * @param OrderCreditRepository $orderCreditRepository
     * @param OrderNpRepository $orderNpRepository
     * @param OrderMessageRepository $orderMessageRepository
     * @param OrderDetailRepository $orderDetailRepository
     * @param OrderDetailUnitRepository $orderDetailUnitRepository
     * @param EventRepository $eventRepository
     * @param MemberShippingAddressInterface $memberShippingAddressHttp
     * @param ItemPriceService $itemPriceService
     * @param OrderDiscountRepository $orderDiscountRepository
     * @param DomainOrderService $domainOrderService
     * @param ItemOrderDiscountService $itemOrderDiscountService
     * @param PrefRepository $prefRepository
     */
    public function __construct(
        ItemDetailRepository $itemDetailRepository,
        ItemDetailIdentificationRepository $identificationRepository,
        OrderAddressRepository $orderAddressRepository,
        OrderRepository $orderRepository,
        OrderCreditRepository $orderCreditRepository,
        OrderNpRepository $orderNpRepository,
        OrderMessageRepository $orderMessageRepository,
        OrderDetailRepository $orderDetailRepository,
        OrderDetailUnitRepository $orderDetailUnitRepository,
        EventRepository $eventRepository,
        MemberShippingAddressInterface $memberShippingAddressHttp,
        ItemPriceService $itemPriceService,
        OrderDiscountRepository $orderDiscountRepository,
        DomainOrderService $domainOrderService,
        ItemOrderDiscountService $itemOrderDiscountService,
        PrefRepository $prefRepository
    ) {
        $this->itemDetailRepository = $itemDetailRepository;
        $this->identificationRepository = $identificationRepository;
        $this->orderRepository = $orderRepository;
        $this->orderAddressRepository = $orderAddressRepository;
        $this->orderCreditRepository = $orderCreditRepository;
        $this->orderNpRepository = $orderNpRepository;
        $this->orderMessageRepository = $orderMessageRepository;
        $this->orderDetailRepository = $orderDetailRepository;
        $this->orderDetailUnitRepository = $orderDetailUnitRepository;
        $this->eventRepository = $eventRepository;
        $this->memberShippingAddressHttp = $memberShippingAddressHttp;
        $this->itemPriceService = $itemPriceService;
        $this->orderDiscountRepository = $orderDiscountRepository;
        $this->domainOrderService = $domainOrderService;
        $this->itemOrderDiscountService = $itemOrderDiscountService;
        $this->prefRepository = $prefRepository;

        if (auth('api')->check()) {
            $user = auth('api')->user();
            $this->memberShippingAddressHttp->setMemberTokenHeader($user->token);
            $this->domainOrderService->setMemberToken($user->token);
        }
    }

    /**
     * 注文レコード作成
     *
     * @param Cart $cart
     * @param array $params
     *
     * @return mixed
     */
    public function createOrder(Cart $cart, array $params)
    {
        $orderCode = $this->orderRepository->generateCode();

        $attributes = [
            'member_id' => $cart->member_id,
            'code' => $orderCode,
            'order_date' => Carbon::now()->format('Y-m-d H:i:s'),
            'payment_type' => $params['payment_type'],
            'paid' => $this->getPaidStatus($cart, $params),
            'delivery_type' => $params['delivery_type'],
            'delivery_fee' => $params['original_postage'],
            'price' => $params['total_price'],
            'fee' => OrderPrice::getPaymentFee($params['payment_type']),
            'use_point' => $params['use_point'],
            'order_type' => $cart->order_type,
            'status' => $this->getOrderStatus($cart),
            'add_point' => $params['point'],
            'device_type' => \App\Utils\UserAgent::getDeviceType(),
            'is_guest' => $params['is_guest'],
        ];

        if ($params['delivery_has_time']) {
            $attributes['delivery_hope_date'] = isset($params['delivery_hope_date']) && $params['delivery_hope_date'] ? (new Carbon($params['delivery_hope_date']))->format('Y-m-d') : null;
            $attributes['delivery_hope_time'] = $params['delivery_hope_time'] ?? null;
        }

        return $this->orderRepository->create($attributes);
    }

    /**
     * 注文時のステータスを取得
     *
     * @param \App\Models\Cart $cart
     *
     * @return int
     */
    private static function getOrderStatus(\App\Models\Cart $cart)
    {
        switch ((int) $cart->order_type) {
            case \App\Enums\Order\OrderType::BackOrder:
            case \App\Enums\Order\OrderType::Reserve:
                return \App\Enums\Order\Status::Pending;

            default:
                return \App\Enums\Order\Status::Ordered;
        }
    }

    /**
     * 支払いステータスの取得
     *
     * @param \App\Models\Cart $cart
     * @param array $params
     *
     * @return bool
     */
    private static function getPaidStatus(\App\Models\Cart $cart, array $params)
    {
        switch ((int) $cart->order_type) {
            case \App\Enums\Order\OrderType::BackOrder:
            case \App\Enums\Order\OrderType::Reserve:
                return false;

            default:
                break;
        }

        switch ((int) $params['payment_type']) {
            case \App\Enums\Order\PaymentType::Cod:
                return false;

            default:
                return true;
        }
    }

    /**
     * 注文詳細レコード作成
     *
     * @param Cart $cart
     * @param Order $order
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function createOrderDetail(Cart $cart, Order $order)
    {
        $cart->load('cartItems.itemDetail.item');
        $cart = $this->itemPriceService->fillPriceBeforeOrderToCreateNewOrder($cart);

        $orderDetails = (new \App\Models\OrderDetail())->newCollection();

        foreach ($cart->getSecuredCartItems() as $cartItem) {
            $itemDetail = $cartItem->itemDetail;

            $orderDetail = $this->domainOrderService->createOrderDetail($order, $itemDetail);

            $this->domainOrderService->addUnitsByFront($orderDetail, $cartItem);

            $orderDetail->setRelation('itemDetail', $itemDetail);

            $orderDetails->add($orderDetail);
        }

        $order->setRelation('orderDetails', $orderDetails);

        $this->itemOrderDiscountService->createAndLoadItemOrderDiscounts($order);

        return $orderDetails;
    }

    /**
     * 請求先とお届け先を保存
     *
     * @param Order $order
     * @param array $params
     * @param array $member
     */
    public function createOrderAddresses(Order $order, array $params, array $member)
    {
        // お客様情報
        $this->orderAddressRepository->create(array_merge([
            'order_id' => $order->id,
            'type' => \App\Enums\OrderAddress\Type::Member,
            'email' => $member['email'],
        ], $params['member']));

        // お届け先
        $attributes = $this->createDeliveryAddressAttributes($order, $params, $member);
        $this->orderAddressRepository->create($attributes);

        // 請求情報
        if ($order->payment_type === PaymentType::CreditCard) {
            $this->orderAddressRepository->create(array_merge([
                'order_id' => $order->id,
                'type' => \App\Enums\OrderAddress\Type::Bill,
                'email' => $member['email'],
            ], $params['billing_address']));
        }
    }

    /**
     * ゲスト購入の請求先とお届け先を保存
     *
     * @param Order $order
     * @param array $params
     * @param array $member
     */
    public function createGuestOrderAddresses(Order $order, array $params, array $member)
    {
        // お客様情報
        $this->orderAddressRepository->create(array_merge([
            'order_id' => $order->id,
            'type' => \App\Enums\OrderAddress\Type::Member,
            'email' => $member['email'],
        ], $params['shipping_address']));

        // お届け先
        $this->orderAddressRepository->create(array_merge([
            'order_id' => $order->id,
            'type' => \App\Enums\OrderAddress\Type::Delivery,
            'email' => $member['email'],
        ], $params['shipping_address']));

        // 請求情報
        $this->orderAddressRepository->create(array_merge([
            'order_id' => $order->id,
            'type' => \App\Enums\OrderAddress\Type::Bill,
            'email' => $member['email'],
        ], $params['shipping_address']));
    }

    /**
     * @param Order $order
     * @param array $params
     * @param array $member
     *
     * @return array
     */
    private function createDeliveryAddressAttributes(Order $order, array $params, array $member)
    {
        $baseParams = [
            'order_id' => $order->id,
            'type' => \App\Enums\OrderAddress\Type::Delivery,
            'email' => $member['email'],
        ];

        switch ($order->payment_type) {
            case PaymentType::Cod:
            case PaymentType::NP:
                return array_merge($baseParams, $params['member']);

            default:
                break;
        }

        $response = $this->memberShippingAddressHttp->get($params['destination_id'])->getBody();

        return array_merge($baseParams, $response['shipping_address']);
    }

    /**
     * 請求先とお届け先をAmazonPayの情報を元に保存
     *
     * @param \App\Models\Order $order
     * @param \App\Entities\AmazonPay\OrderReferenceDetails $orderReferenceDetails
     *
     * @return void
     */
    public function createOrderAddressesByAmazonPay(
        Order $order,
        \App\Entities\AmazonPay\OrderReferenceDetails $orderReferenceDetails,
        array $member
    ) {
        $buyer = $orderReferenceDetails->buyer;
        $address = $orderReferenceDetails->destination->physical_destination;

        $attributes = [
            'order_id' => $order->id,
            'email' => $buyer->email,
            'fname' => '',
            'fkana' => '',
            'lkana' => '',
            'tel' => $member['tel'],
            'pref_id' => $address->pref_id,
            'zip' => $address->postal_code,
            'city' => $address->address_line1 ?? '',
            'town' => $address->address_line2 ?? '',
            'address' => $address->address_line3 ?? '',
        ];

        // お客様情報
        $this->orderAddressRepository->create(array_merge($attributes, [
            'type' => \App\Enums\OrderAddress\Type::Member,
            'lname' => $buyer->name,
        ]));

        // お届け先
        $this->orderAddressRepository->create(array_merge($attributes, [
            'type' => \App\Enums\OrderAddress\Type::Delivery,
            'lname' => $address->name,
        ]));
    }

    /**
     * 注文メッセージレコード作成
     *
     * @param Order $order
     * @param array $message
     */
    public function createOrderMessage(Order $order, array $message)
    {
        $this->orderMessageRepository->create([
            'order_id' => $order->id,
            'title' => Request::getDescription($message['type']),
            'body' => nl2br($message['content']),
            'type' => Type::User,
        ]);
    }

    /**
     * NP後払い決済情報保存
     *
     * @param Order $order
     * @param array $result
     */
    public function createOrderNp(Order $order, array $result)
    {
        $this->orderNpRepository->create([
            'order_id' => $order->id,
            'shop_transaction_id' => $result['results'][0]['shop_transaction_id'],
            'np_transaction_id' => $result['results'][0]['np_transaction_id'],
        ]);
    }

    /**
     * 決済エラー情報保存
     *
     * @param Order $order
     * @param $errorMemo
     */
    public function updateErrorMemo(Order $order, $errorMemo)
    {
        $order->error_memo = ($order->error_memo ? $order->error_memo."\n" : '') . $errorMemo;
        $order->save();
    }

    /**
     * 未発送の注文があるか
     *
     * @param $memberId
     *
     * @return bool
     */
    public function canWithdraw($memberId)
    {
        return $this->orderRepository->scopeQuery(function ($query) use ($memberId) {
            return $query->where('member_id', $memberId);
        })->findWhereIn('status', [
            Status::Ordered,
            Status::Arrived,
            Status::ReadyForDelivery,
            Status::Deliveryed,
            Status::Pending,
            Status::Changed,
        ])->isEmpty();
    }
}

<?php

namespace App\Services\Admin;

use App\Criteria\Order\AdminSearchCriteria;
use App\Domain\Adapters\Ymdy\MemberPurchaseInterface as MemberPurchaseAdapter;
use App\Domain\Adapters\Ymdy\Purchase as PurchaseAdapter;
use App\Domain\MemberInterface as MemberService;
use App\Domain\OrderPortionInterface as OrderPortion;
use App\Exceptions\FatalException;
use App\Repositories\OrderRepository;
use App\Utils\Arr;
use App\Utils\Csv\ExportCsvInterface as ExportCsvUtil;
use Illuminate\Database\Eloquent\Collection;

class OrderCsvService extends BaseOrderService implements OrderCsvServiceInterface
{
    const MEMBER_FETCHING_SPLIT_NUM = 50;

    /**
     * @var ExportCsvUtil
     */
    private $exportCsvUtil;

    /**
     * @var OrderPortion
     */
    private $orderPortion;

    /**
     * @param OrderRepository $orderRepository
     */
    public function __construct(
        OrderRepository $orderRepository,
        ExportCsvUtil $exportCsvUtil,
        MemberService $memberService,
        OrderPortion $orderPortion,
        PurchaseAdapter $purchaseAdapter,
        MemberPurchaseAdapter $memberPurchaseAdapter
    ) {
        parent::__construct($memberService, $purchaseAdapter, $memberPurchaseAdapter, $orderRepository);

        $this->orderRepository = $orderRepository;
        $this->exportCsvUtil = $exportCsvUtil;
        $this->orderPortion = $orderPortion;
    }

    /**
     * 受注詳細情報 (受注単位) CSVエクスポートを実行するコールバックを取得する
     *
     * @param array $params
     *
     * @return \Closure
     */
    public function exportOrderCsv(array $params)
    {
        $this->exportCsvUtil->setHeaders([
            'code' => __('file_format.csv.headers.order.code'),
            'order_date' => __('file_format.csv.headers.order.order_date'),
            'order_time' => __('file_format.csv.headers.order.order_time'),
            'amount' => __('file_format.csv.headers.order.amount'),
            'member_id' => __('file_format.csv.headers.order.member_id'),
            'member_name' => __('file_format.csv.headers.order.member_name'),
            'member_name_kana' => __('file_format.csv.headers.order.member_name_kana'),
            'member_email' => __('file_format.csv.headers.order.member_email'),
            'member_zip' => __('file_format.csv.headers.order.member_zip'),
            'member_address_1' => __('file_format.csv.headers.order.member_address_1'),
            'member_address_2' => __('file_format.csv.headers.order.member_address_2'),
            'member_tel' => __('file_format.csv.headers.order.member_tel'),
            'member_gender' => __('file_format.csv.headers.order.member_gender'),
            'recipient_name' => __('file_format.csv.headers.order.recipient_name'),
            'recipient_name_kana' => __('file_format.csv.headers.order.recipient_name_kana'),
            'recipient_email' => __('file_format.csv.headers.order.recipient_email'),
            'recipient_zip' => __('file_format.csv.headers.order.recipient_zip'),
            'recipient_address_1' => __('file_format.csv.headers.order.recipient_address_1'),
            'recipient_address_2' => __('file_format.csv.headers.order.recipient_address_2'),
            'recipient_tel' => __('file_format.csv.headers.order.recipient_tel'),
            'delivery_type' => __('file_format.csv.headers.order.delivery_type'),
            'user_request' => __('file_format.csv.headers.order.user_request'),
            'total_item_price' => __('file_format.csv.headers.order.total_item_price'),
            'delivery_fee' => __('file_format.csv.headers.order.delivery_fee'),
            'tax' => __('file_format.csv.headers.order.tax'),
            'fee' => __('file_format.csv.headers.order.fee'),
            'invoiced_price' => __('file_format.csv.headers.order.invoiced_price'),
            'device' => __('file_format.csv.headers.order.device'),
            'is_point_used' => __('file_format.csv.headers.order.is_point_used'),
            'use_point' => __('file_format.csv.headers.order.use_point'),
            'point_status' => __('file_format.csv.headers.order.point_status'),
            'add_point' => __('file_format.csv.headers.order.add_point'),
            'total_price' => __('file_format.csv.headers.order.total_price'),
            'delivery_hope_date' => __('file_format.csv.headers.order.delivery_hope_date'),
            'delivery_hope_time' => __('file_format.csv.headers.order.delivery_hope_time'),
            'deliveryed_date' => __('file_format.csv.headers.order.deliveryed_date'),
            'paid_date' => __('file_format.csv.headers.order.paid_date'),
        ]);

        $params = $this->mergetMemberIdToParams($params);

        $this->orderRepository->pushCriteria(new AdminSearchCriteria($params));

        $this->orderRepository->with([
            'orderDetails.orderDetailUnits',
            'orderDetails.displayedDiscount',
            'orderDetails.bundleSaleDiscount',
            'orderMessages',
            'deliveryOrderAddress.pref',
            'orderUsedCoupons',
            'deliveryFeeDiscount',
        ]);

        return $this->exportCsvUtil->getExporter(function ($exporter) {
            $memberDict = [];

            $this->orderRepository->chunk(config('repository.chunk.default'), function ($chunk) use ($exporter, $memberDict) {
                $memberDict = $this->loadMembers($chunk, $memberDict);

                foreach ($chunk as $row) {
                    $data = [
                        'code' => '',
                        'order_date' => '',
                        'order_time' => '',
                        'amount' => '',
                        'member_id' => '',
                        'member_name' => '',
                        'member_name_kana' => '',
                        'member_email' => '',
                        'member_zip' => '',
                        'member_address_1' => '',
                        'member_address_2' => '',
                        'member_tel' => '',
                        'member_gender' => '',
                        'recipient_name' => '',
                        'recipient_name_kana' => '',
                        'recipient_zip' => '',
                        'recipient_email' => '',
                        'recipient_address_1' => '',
                        'recipient_address_2' => '',
                        'recipient_tel' => '',
                        'delivery_type' => '',
                        'total_item_price' => '',
                        'delivery_fee' => '',
                        'tax' => '',
                        'fee' => '',
                        'invoiced_price' => '',
                        'device' => '',
                        'is_point_used' => '',
                        'use_point' => '',
                        'point_status' => '',
                        'add_point' => '',
                        'total_price' => '',
                        'delivery_hope_date' => '',
                        'delivery_hope_time' => '',
                        'deliveryed_date' => '',
                        'user_request' => '',
                        'paid_date' => '',
                    ];

                    if (!isset($memberDict[$row->member_id])) {
                        // throw new FatalException(error_format('error.resource_not_found', ['member_id' => $row->member_id]));
                        $data['member_name'] = 'member_idに紐づく会員が取得できませんでした';
                    } else {
                        $member = $memberDict[$row->member_id];

                        $data['code'] = $row->code;
                        $data['order_date'] = $row->order_date->format('Y/m/d');
                        $data['order_time'] = $row->order_date->format('H:i:s');
                        $data['amount'] = $row->orderDetails->sum('amount');
                        $data['member_id'] = $member['id'];
                        $data['member_name'] = $member['lname'] . $member['fname'];
                        $data['member_name_kana'] = $member['lkana'] . $member['fkana'];
                        $data['member_email'] = $member['email'];
                        $data['member_zip'] = $member['zip'];
                        $data['member_address_1'] = isset($member['pref']['name']) ? $member['pref']['name'] . $member['city'] : $member['city'];
                        $data['member_address_2'] = $member['town'] . $member['address'] . ($member['building'] ?? '');
                        $data['member_tel'] = $member['tel'];
                        $data['member_gender'] = \App\Enums\Member\Gender::getDescription($member['gender']);

                        $data['recipient_name'] = $row->deliveryOrderAddress->lname . $row->deliveryOrderAddress->fname;
                        $data['recipient_name_kana'] = $row->deliveryOrderAddress->lkana . $row->deliveryOrderAddress->fkana;
                        $data['recipient_zip'] = $row->deliveryOrderAddress->zip;
                        $data['recipient_email'] = $row->deliveryOrderAddress->email;
                        $data['recipient_address_1'] = $row->deliveryOrderAddress->pref->name . $row->deliveryOrderAddress->city;
                        $data['recipient_address_2'] = $row->deliveryOrderAddress->town . $row->deliveryOrderAddress->address . $row->deliveryOrderAddress->building;
                        $data['recipient_tel'] = $row->deliveryOrderAddress->tel;

                        $data['delivery_type'] = \App\Enums\Order\DeliveryType::getDescription($row->delivery_type);
                        $data['user_request'] = $this->extractRequestMessage($row->orderMessages);

                        $data['total_item_price'] = $row->orderDetails->sum('total_price_before_order');
                        $data['delivery_fee'] = $row->discounted_delivery_fee;
                        $data['tax'] = $row->tax;
                        $data['fee'] = $row->fee;
                        $data['invoiced_price'] = $row->price;
                        $data['device'] = \App\Enums\Common\DeviceType::getDescription($row->device_type);
                        $data['is_point_used'] = $row->use_point > 0 ? 'あり' : 'なし';
                        $data['use_point'] = $row->use_point;
                        $data['point_status'] = (int) $row->deliveryed ? '有効' : '無効';
                        $data['add_point'] = $row->add_point;
                        $data['total_price'] = $row->price + $row->use_point;
                        $data['delivery_hope_date'] = $row->delivery_hope_date ? $row->delivery_hope_date->format('m/d') : '';
                        $data['delivery_hope_time'] = \App\Enums\Order\DeliveryTime::getDescription($row->delivery_hope_time);
                        $data['deliveryed_date'] = $row->deliveryed_date ? $row->deliveryed_date->format('Y/m/d') : '';
                        $data['paid_date'] = $row->paid_date ? $row->paid_date->format('Y/m/d') : '';
                    }

                    $exporter($data);
                }
            });
        });
    }

    /**
     * 受注詳細情報 (明細単位) CSVエクスポートを実行するコールバックを取得する
     *
     * @param array $params
     *
     * @return \Closure
     */
    public function exportOrderDetailCsv(array $params)
    {
        $this->exportCsvUtil->setHeaders([
            'code' => __('file_format.csv.headers.order.code'),
            'order_date' => __('file_format.csv.headers.order.order_date'),
            'order_time' => __('file_format.csv.headers.order.order_time'),
            'brand_name' => __('file_format.csv.headers.order_detail.brand_name'),
            'item_name' => __('file_format.csv.headers.order_detail.item_name'),
            'product_number' => __('file_format.csv.headers.order_detail.product_number'),
            'maker_product_number' => __('file_format.csv.headers.order_detail.maker_product_number'),
            'color_and_size' => __('file_format.csv.headers.order_detail.color_and_size'),
            'jan_code' => __('file_format.csv.headers.order_detail.jan_code'),
            'amount' => __('file_format.csv.headers.order_detail.amount'),
            'price_before_order' => __('file_format.csv.headers.order_detail.price_before_order'),
            'member_id' => __('file_format.csv.headers.order.member_id'),
            'member_name' => __('file_format.csv.headers.order.member_name'),
            'member_name_kana' => __('file_format.csv.headers.order.member_name_kana'),
            'member_email' => __('file_format.csv.headers.order.member_email'),
            'member_zip' => __('file_format.csv.headers.order.member_zip'),
            'member_address_1' => __('file_format.csv.headers.order.member_address_1'),
            'member_address_2' => __('file_format.csv.headers.order.member_address_2'),
            'member_tel' => __('file_format.csv.headers.order.member_tel'),
            'member_gender' => __('file_format.csv.headers.order.member_gender'),
            'recipient_name' => __('file_format.csv.headers.order.recipient_name'),
            'recipient_name_kana' => __('file_format.csv.headers.order.recipient_name_kana'),
            'recipient_email' => __('file_format.csv.headers.order.recipient_email'),
            'recipient_zip' => __('file_format.csv.headers.order.recipient_zip'),
            'recipient_address_1' => __('file_format.csv.headers.order.recipient_address_1'),
            'recipient_address_2' => __('file_format.csv.headers.order.recipient_address_2'),
            'recipient_tel' => __('file_format.csv.headers.order.recipient_tel'),
            'delivery_type' => __('file_format.csv.headers.order.delivery_type'),
            'user_request' => __('file_format.csv.headers.order.user_request'),
            'total_item_price' => __('file_format.csv.headers.order.total_item_price'),
            'delivery_fee' => __('file_format.csv.headers.order.delivery_fee'),
            'tax' => __('file_format.csv.headers.order.tax'),
            'fee' => __('file_format.csv.headers.order.fee'),
            'invoiced_price' => __('file_format.csv.headers.order.invoiced_price'),
            'device' => __('file_format.csv.headers.order.device'),
            'is_point_used' => __('file_format.csv.headers.order.is_point_used'),
            'use_point' => __('file_format.csv.headers.order.use_point'),
            'point_status' => __('file_format.csv.headers.order.point_status'),
            'total_price' => __('file_format.csv.headers.order.total_price'),
            'delivery_hope_date' => __('file_format.csv.headers.order.delivery_hope_date'),
            'delivery_hope_time' => __('file_format.csv.headers.order.delivery_hope_time'),
            'deliveryed_date' => __('file_format.csv.headers.order.deliveryed_date'),
            'paid_date' => __('file_format.csv.headers.order.paid_date'),
        ]);

        $params = $this->mergetMemberIdToParams($params);

        $this->orderRepository->pushCriteria(new AdminSearchCriteria($params));

        $this->orderRepository->with([
            'orderDetails.orderDetailUnits.itemDetailIdentification.itemDetail.item.brand',
            'orderDetails.orderDetailUnits.itemDetailIdentification.itemDetail.size',
            'orderDetails.displayedDiscount',
            'orderDetails.bundleSaleDiscount',
            'orderMessages',
            'deliveryOrderAddress.pref',
            'orderUsedCoupons',
            'deliveryFeeDiscount',
        ]);

        return $this->exportCsvUtil->getExporter(function ($exporter) {
            $memberDict = [];

            $this->orderRepository->chunk(config('repository.chunk.default'), function ($chunk) use ($exporter, $memberDict) {
                $memberDict = $this->loadMembers($chunk, $memberDict);

                foreach ($chunk as $row) {
                    if (!isset($memberDict[$row->member_id])) {
                        throw new FatalException(error_format('error.resource_not_found', ['member_id' => $row->member_id]));
                    }

                    $porsionedPoints = $this->orderPortion->portionPoints($row, false);
                    $usedPoints = Arr::dict($porsionedPoints, 'order_detail_unit_id');

                    foreach ($row->orderDetails as $orderDetail) {
                        foreach ($orderDetail->orderDetailUnits as $unit) {
                            if ((int) $unit->amount === 0) {
                                continue;
                            }

                            $member = $memberDict[$row->member_id];
                            $usedPoint = $usedPoints[$unit->id]['price'] ?? 0;

                            $data = [];
                            $data['code'] = $row->code;
                            $data['order_date'] = $row->order_date->format('Y/m/d');
                            $data['order_time'] = $row->order_date->format('H:i:s');
                            $data['member_id'] = $member['id'];
                            $data['member_name'] = $member['lname'] . $member['fname'];
                            $data['member_name_kana'] = $member['lkana'] . $member['fkana'];
                            $data['member_email'] = $member['email'];
                            $data['member_zip'] = $member['zip'];
                            $data['member_address_1'] = $member['pref']['name'] . $member['city'];
                            $data['member_address_2'] = $member['town'] . $member['address'] . ($member['building'] ?? '');
                            $data['member_tel'] = $member['tel'];
                            $data['member_gender'] = \App\Enums\Member\Gender::getDescription($member['gender']);

                            $data['brand_name'] = optional($unit->itemDetailIdentification->itemDetail->item->brand)->name;
                            $data['item_name'] = $unit->itemDetailIdentification->itemDetail->item->display_name;
                            $data['product_number'] = $unit->itemDetailIdentification->itemDetail->item->product_number;
                            $data['maker_product_number'] = $unit->itemDetailIdentification->itemDetail->item->maker_product_number;
                            $data['color_and_size'] = sprintf(
                                '%s %s',
                                $unit->itemDetailIdentification->itemDetail->color_id,
                                $unit->itemDetailIdentification->itemDetail->size->name
                            );
                            $data['jan_code'] = $unit->itemDetailIdentification->jan_code;
                            $data['amount'] = $unit->amount;
                            $data['price_before_order'] = $orderDetail->price_before_order;

                            $data['recipient_name'] = $row->deliveryOrderAddress->lname . $row->deliveryOrderAddress->fname;
                            $data['recipient_name_kana'] = $row->deliveryOrderAddress->lkana . $row->deliveryOrderAddress->fkana;
                            $data['recipient_zip'] = $row->deliveryOrderAddress->zip;
                            $data['recipient_email'] = $row->deliveryOrderAddress->email;
                            $data['recipient_address_1'] = $row->deliveryOrderAddress->pref->name . $row->deliveryOrderAddress->city;
                            $data['recipient_address_2'] = $row->deliveryOrderAddress->town . $row->deliveryOrderAddress->address . $row->deliveryOrderAddress->building;
                            $data['recipient_tel'] = $row->deliveryOrderAddress->tel;

                            $data['delivery_type'] = \App\Enums\Order\DeliveryType::getDescription($row->delivery_type);
                            $data['user_request'] = $this->extractRequestMessage($row->orderMessages);

                            $data['total_item_price'] = $row->orderDetails->sum('total_price_before_order');
                            $data['delivery_fee'] = $row->discounted_delivery_fee;
                            $data['tax'] = $unit->tax;
                            $data['fee'] = $row->fee;
                            $data['invoiced_price'] = $row->price;
                            $data['device'] = \App\Enums\Common\DeviceType::getDescription($row->device_type);
                            $data['is_point_used'] = $usedPoint > 0 ? 'あり' : 'なし';
                            $data['use_point'] = $usedPoint;
                            $data['point_status'] = (int) $row->deliveryed ? '有効' : '無効';
                            $data['total_price'] = $row->price + $row->use_point;
                            $data['delivery_hope_date'] = $row->delivery_hope_date ? $row->delivery_hope_date->format('m/d') : '';
                            $data['delivery_hope_time'] = \App\Enums\Order\DeliveryTime::getDescription($row->delivery_hope_time);
                            $data['deliveryed_date'] = $row->deliveryed_date ? $row->deliveryed_date->format('Y/m/d') : '';
                            $data['paid_date'] = $row->paid_date ? $row->paid_date->format('Y/m/d') : '';

                            $exporter($data);
                        }
                    }
                }
            });
        });
    }

    /**
     * @param array $params
     *
     * @return array
     */
    private function mergetMemberIdToParams(array $params)
    {
        $memberSearchParams = $this->extractMemberSearchParams($params);

        if (!empty($memberSearchParams)) {
            $memberIds = $this->fetchMemberIds($memberSearchParams);

            if (empty($memberIds)) {
                return $this->exportCsvUtil->getExporter(function () {
                });
            }

            $params['member_id'] = $memberIds;
        }

        return $params;
    }

    /**
     * @param \Illuminate\Database\Eloquent\Collection $orders
     * @param array $memberDict
     *
     * @return array
     */
    private function loadMembers($orders, array $memberDict)
    {
        $memberIds = Arr::uniq($orders->pluck('member_id')->toArray());
        $memberIds = Arr::except($memberIds, array_keys($memberDict));

        if (empty($memberIds)) {
            return $memberDict;
        }

        foreach (collect($memberIds)->chunk(self::MEMBER_FETCHING_SPLIT_NUM)->toArray() as $chunkMemberIds) {
            $members = $this->memberService->search(['member_id' => $chunkMemberIds]);
            $memberDict = Arr::merge($memberDict, Arr::dict($members));
        }

        return $memberDict;
    }

    /**
     * @param Collection $orderMessages
     *
     * @return string
     */
    private function extractRequestMessage(Collection $orderMessages)
    {
        $orderMessage = $orderMessages->sortBy('id')->first();

        if (empty($orderMessage) || (int) $orderMessage->type !== \App\Enums\OrderMessage\Type::User) {
            return '';
        }

        return $orderMessage->title . ' ' . $orderMessage->body;
    }
}

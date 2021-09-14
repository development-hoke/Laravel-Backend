<?php

namespace App\Repositories;

use App\Entities\Ymdy\Member\EcBill;
use App\Enums\Order\Status;
use App\Models\Order;
use App\Repositories\Traits\QueryBuilderMethodTrait;
use App\Utils\Arr;
use App\Utils\Cache;
use Illuminate\Support\Carbon;

/**
 * Class OrderRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 *
 * @SuppressWarnings(PHPMD.CyclomaticComplexity)
 */
class OrderRepositoryEloquent extends BaseRepositoryEloquent implements OrderRepository
{
    use QueryBuilderMethodTrait;

    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return Order::class;
    }

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
    }

    /**
     * 配送情報の更新処理
     *
     * @param int $orderId
     * @param array $params
     *
     * @return \App\Models\Order
     */
    public function changeToDelivered(int $orderId, array $params)
    {
        $order = $this->update([
            'delivery_number' => $params['delivery_number'],
            'delivery_company' => $params['delivery_company'],
            'deliveryed_date' => $params['delivery_date'],
            'status' => Status::Deliveryed,
            'deliveryed' => true,
            'inspected' => true,
        ], $orderId);

        return $order;
    }

    /**
     * orders.codeの値を生成する
     *
     * @return string
     */
    public static function generateCode()
    {
        $shopNo = config('constants.order.code.shop_no');
        $posNo = config('constants.order.code.pos_no');

        $date = Carbon::today()->format('Ymd');
        $key = sprintf(Cache::KEY_ORDER_CODE, $date);
        $number = Cache::increment($key);

        return sprintf('%s-%s-%s-%s', $date, $shopNo, $posNo, sprintf('%05d', $number));
    }

    /**
     * 消費税の保存
     *
     * @param Order $order
     * @param EcBill $ecBill
     *
     * @return OrderModel
     */
    public function syncOrderTax(Order $order, EcBill $ecBill)
    {
        $detailDict = Arr::dict($ecBill->details, 'ec_id');

        $order->orderDetails->each(function ($orderDetail) use ($detailDict) {
            $orderDetail->orderDetailUnits->each(function ($unit) use ($detailDict) {
                $detail = $detailDict[$unit->id];
                $unit->tax = $detail->tax;
                $unit->save();
            });
        });

        $order->tax = $ecBill->total_tax;
        $order->save();

        return $order;
    }
}

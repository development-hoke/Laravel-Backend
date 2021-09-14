<?php

namespace App\Repositories;

use App\Entities\Ymdy\Member\EcBill;
use App\Models\Order;
use Prettus\Repository\Contracts\RepositoryInterface;

/**
 * Interface OrderRepository.
 *
 * @package namespace App\Repositories;
 */
interface OrderRepository extends RepositoryInterface
{
    /**
     * orders.codeの値を生成する
     *
     * @return string
     */
    public static function generateCode();

    /**
     * 配送情報の更新処理
     *
     * @param int $orderId
     * @param array $params
     *
     * @return \App\Models\Order
     */
    public function changeToDelivered(int $orderId, array $params);

    /**
     * 消費税の保存
     *
     * @param Order $order
     * @param EcBill $ecBill
     *
     * @return OrderModel
     */
    public function syncOrderTax(Order $order, EcBill $ecBill);

    /**
     * 指定した条件で最初の1件を取得する。なければModelNotFoundExceptionを投げる。
     *
     * @param array $where
     *
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function findOrFail(array $where);
}

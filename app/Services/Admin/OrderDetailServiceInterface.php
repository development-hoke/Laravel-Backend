<?php

namespace App\Services\Admin;

interface OrderDetailServiceInterface
{
    /**
     * @param int $orderId
     *
     * @return \App\Models\OrderDetail
     */
    public function findByOrderId(int $orderId);

    /**
     * @param int $id
     *
     * @return \App\Models\OrderDetail
     */
    public function findOne(int $id);

    /**
     * @param array $attributes
     * @param int $orderId
     *
     * @return \App\Models\OrderDetail
     */
    public function add(array $attributes, int $orderId);

    /**
     * @param int $orderId
     * @param array $params
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function cancel(int $orderId, array $params);

    /**
     * 商品返品
     *
     * @param int $orderId
     * @param array $params
     *
     * @return \App\Models\OrderDetail
     */
    public function return(int $orderId, array $params);
}

<?php

namespace App\Repositories;

use App\Models\DeliverySetting;

/**
 * Class DeliverySettingRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class DeliverySettingRepositoryEloquent extends BaseRepositoryEloquent implements DeliverySettingRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return DeliverySetting::class;
    }

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
    }

    /**
     * @return int
     */
    public static function getDefaultDeliveryFee()
    {
        return config('constants.delivery_fee.default_price');
    }

    /**
     * 配送料を計算
     *
     * @param int $totalPrice
     *
     * @return int
     */
    public function calculateDeliveryFee(int $totalPrice)
    {
        $deliverySetting = $this->model->orderBy('id', 'asc')->first();
        if (empty($deliverySetting)) {
            throw new \App\Exceptions\FatalException('Delivery setting is not set.');
        }

        return $totalPrice < $deliverySetting->delivery_condition
            ? config('constants.delivery_fee.default_price')
            : $deliverySetting->delivery_price;
    }
}

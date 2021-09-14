<?php

namespace App\Repositories;

use App\Models\OrderAddress;
use Illuminate\Database\Eloquent\ModelNotFoundException;

/**
 * Class OrderAddressRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class OrderAddressRepositoryEloquent extends BaseRepositoryEloquent implements OrderAddressRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return OrderAddress::class;
    }

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
    }

    /**
     * @param int $orderId
     *
     * @return OrderAddress
     */
    public function findDeliveryAddress($orderId)
    {
        $model = $this->findWhere([
            'order_id' => $orderId,
            'type' => \App\Enums\OrderAddress\Type::Delivery,
        ])->first();

        if (empty($model)) {
            throw new ModelNotFoundException(error_format('model_not_found', [
                'order_id' => $orderId,
                'type' => \App\Enums\OrderAddress\Type::Delivery,
            ]));
        }

        return $model;
    }

    /**
     * @param int $orderId
     *
     * @return OrderAddress
     */
    public function findBillingAddress($orderId)
    {
        $model = $this->findWhere([
            'order_id' => $orderId,
            'type' => \App\Enums\OrderAddress\Type::Bill,
        ])->first();

        if (empty($model)) {
            throw new ModelNotFoundException(error_format('model_not_found', [
                'order_id' => $orderId,
                'type' => \App\Enums\OrderAddress\Type::Bill,
            ]));
        }

        return $model;
    }
}

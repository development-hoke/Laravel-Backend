<?php

namespace App\Repositories;

use App\Exceptions\InvalidArgumentValueException;
use App\Models\OrderDetailUnit;
use Illuminate\Database\Eloquent\Collection;

/**
 * Class OrderDetailUnitRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class OrderDetailUnitRepositoryEloquent extends BaseRepositoryEloquent implements OrderDetailUnitRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return OrderDetailUnit::class;
    }

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
    }

    /**
     * 取り消し可能な注文データを取得する。
     * JANが新しいものから取得する。（注文時とは逆の順序）
     *
     * @param int $orderDetailId
     * @param int $cancelAmount
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function findCancelableUnits($orderDetailId, $cancelAmount)
    {
        $models = $this->scopeQuery(function ($query) {
            return $query
                ->select(['*'])
                ->join('item_detail_identifications', 'order_detail_units.id', '=', 'item_detail_identifications.id')
                ->where('order_detail_units.amount', '>', 0)
                ->orderBy('item_detail_identifications.jan_code', 'desc');
        })->findWhere(['order_detail_id' => $orderDetailId]);

        $units = [];
        $amount = 0;

        foreach ($models as $model) {
            $units[] = $model;
            $amount += $model->amount;

            if ($amount >= $cancelAmount) {
                return Collection::make($units);
            }
        }

        throw new InvalidArgumentValueException('error.not_exists_number_of_units');
    }
}

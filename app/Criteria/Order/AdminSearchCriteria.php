<?php

namespace App\Criteria\Order;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Prettus\Repository\Contracts\CriteriaInterface;
use Prettus\Repository\Contracts\RepositoryInterface;

/**
 * Class AdminSearchCriteria.
 *
 * @package namespace App\Criteria\Order;
 * @SuppressWarnings(PHPMD.CyclomaticComplexity)
 * @SuppressWarnings(PHPMD.NPathComplexity)
 */
class AdminSearchCriteria implements CriteriaInterface
{
    /**
     * @var array
     */
    protected $params;

    public function __construct(array $params)
    {
        $this->params = $params;
    }

    /**
     * Apply criteria in query repository
     *
     * @param Builder|Model $model
     * @param RepositoryInterface $repository
     *
     * @return mixed
     */
    public function apply($model, RepositoryInterface $repository)
    {
        $params = $this->params;

        $targetColumns = ['member_id', 'code', 'delivery_number', 'status', 'payment_type', 'delivery_type', 'order_type', 'paid', 'deliveryed', 'inspected'];

        foreach ($targetColumns as $column) {
            if (isset($params[$column])) {
                if (is_array($params[$column])) {
                    $model = $model->whereIn($column, $params[$column]);
                } else {
                    $model = $model->where($column, $params[$column]);
                }
            }
        }

        if (isset($params['order_date_from'])) {
            $model = $model->whereDate('order_date', '>=', Carbon::parse($params['order_date_from']));
        }

        if (isset($params['order_date_to'])) {
            $model = $model->whereDate('order_date', '<=', Carbon::parse($params['order_date_to']));
        }

        if (isset($params['product_number'])) {
            $model = $model->whereItemProductNumber($params['product_number']);
        }

        if (isset($params['jan_code'])) {
            $model = $model->whereItemJanCode($params['jan_code']);
        }

        if ($this->hasMemberParams($params)) {
            $model = $this->applyMemberAddressConditions($model, $params);
        }

        return $model;
    }

    /**
     * @param mixed $model
     * @param array $params
     *
     * @return mixed
     */
    private function applyMemberAddressConditions($model, array $params)
    {
        $subQuery = \App\Models\OrderAddress::query()
            ->select('order_addresses.order_id')
            ->where('order_addresses.type', \App\Enums\OrderAddress\Type::Member);

        if (isset($params['member_name'])) {
            $keywords = mb_convert_kana($params['member_name'], 'KVs');

            $subQuery = $subQuery->where(function ($query) use ($keywords) {
                $query = $query->orWhere(DB::raw('CONCAT(order_addresses.lname, " ", order_addresses.fname)'), 'like', "%{$keywords}%");
                $query = $query->orWhere(DB::raw('CONCAT(order_addresses.lkana, " ", order_addresses.fkana)'), 'like', "%{$keywords}%");

                return $query;
            });
        }

        if (isset($params['member_phone_number'])) {
            $subQuery = $subQuery->where('order_addresses.tel', 'like', "%{$params['member_phone_number']}%");
        }

        if (isset($params['member_email'])) {
            $subQuery = $subQuery->where('order_addresses.email', 'like', "%{$params['member_email']}%");
        }

        return $model->whereIn('orders.id', $subQuery);
    }

    /**
     * @param array $params
     *
     * @return bool
     */
    private function hasMemberParams(array $params)
    {
        foreach (['member_name', 'member_phone_number', 'member_email'] as $name) {
            if (isset($params[$name])) {
                return true;
            }
        }

        return false;
    }
}

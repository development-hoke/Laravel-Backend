<?php

namespace App\Repositories;

use App\Models\MemberCreditCard;

/**
 * Class MemberCreditCardRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class MemberCreditCardRepositoryEloquent extends BaseRepositoryEloquent implements MemberCreditCardRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return MemberCreditCard::class;
    }

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
    }

    /**
     * @param int $memberId
     *
     * @return \App\Models\MemberCreditCard
     */
    public function findDefaultInfo(int $memberId)
    {
        $memberCreditCard = $this->scopeQuery(function ($query) {
            return $query->orderBy('priority');
        })->findWhere([
            'member_id' => $memberId,
        ])->first();

        return $memberCreditCard;
    }

    /**
     * 予約注文との紐付けの有無を確認する
     *
     * @param int $id
     *
     * @return bool
     */
    public function hasReservationOrder(int $id)
    {
        $model = $this->with(['orderCredits.order'])->find($id);

        $orderCredit = $model->orderCredits->first(function ($orderCredit) {
            return (int) $orderCredit->order->order_type === \App\Enums\Order\OrderType::Reserve;
        });

        return !empty($orderCredit);
    }
}

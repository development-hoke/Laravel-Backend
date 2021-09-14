<?php

namespace App\Repositories;

use Prettus\Repository\Contracts\RepositoryInterface;

/**
 * Interface MemberCreditCardRepository.
 *
 * @package namespace App\Repositories;
 */
interface MemberCreditCardRepository extends RepositoryInterface
{
    /**
     * @param int $memberId
     *
     * @return \App\Models\MemberCreditCard
     */
    public function findDefaultInfo(int $memberId);

    /**
     * 予約注文との紐付けの有無を確認する
     *
     * @param int $id
     *
     * @return bool
     */
    public function hasReservationOrder(int $id);
}

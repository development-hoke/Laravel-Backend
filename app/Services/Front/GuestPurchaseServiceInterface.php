<?php

namespace App\Services\Front;

interface GuestPurchaseServiceInterface
{
    /**
     * @param int $cartId
     * @param array $params
     *
     * @return array
     */
    public function emailAuth(int $cartId, array $params);

    /**
     * @param int $cartId
     * @param array $params
     *
     * @return \App\Models\Cart
     */
    public function verify(int $cartId, array $params);

    /**
     * @param int $memberId
     * @param string $memberToken
     *
     * @return array
     */
    public function fetchMemberDetail(int $memberId, string $memberToken);
}

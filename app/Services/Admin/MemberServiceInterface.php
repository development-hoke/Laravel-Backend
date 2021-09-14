<?php

namespace App\Services\Admin;

interface MemberServiceInterface
{
    /**
     * 会員検索
     *
     * @param array $params
     * @param array $applyingPertialParams (default: ['tel', 'name', 'email'])
     *
     * @return array
     */
    public function search(array $params, $applyingPertialParams = ['tel', 'name', 'email']);

    /**
     * 会員詳細
     *
     * @param int $memberId
     *
     * @return array
     */
    public function fetchOne($memberId);

    /**
     * 利用可能クーポン取得
     *
     * @param string $memberId
     * @param array $query (default: [])
     *
     * @return \App\Entities\Collection
     */
    public function fetchAvailableMemberCoupons($memberId, array $query = []);
}

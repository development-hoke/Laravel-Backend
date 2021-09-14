<?php

namespace App\Domain;

use App\Domain\Contracts\AssignableCrediencalToken;

interface MemberInterface extends AssignableCrediencalToken
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
     * 複数会員IDを指定してをまとめて取得する（外部サーバー負荷対策）
     *
     * @param array|Collection $memberIds
     * @param array $params
     * @param array $applyingPertialParams (default: ['tel', 'name', 'email'])
     *
     * @return array
     */
    public function fetchBatchMembers($memberIds, array $conditions = [], array $applyingPertialParams = ['tel', 'name', 'email']);

    /**
     * 会員詳細
     *
     * @param string $memberId
     *
     * @return array
     */
    public function fetchOne($memberId);
}

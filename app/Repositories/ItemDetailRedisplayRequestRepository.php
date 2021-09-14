<?php

namespace App\Repositories;

use Prettus\Repository\Contracts\RepositoryInterface;

/**
 * Interface ItemDetailRedisplayRequestRepository.
 *
 * @package namespace App\Repositories;
 */
interface ItemDetailRedisplayRequestRepository extends RepositoryInterface
{
    /**
     * user_tokenとmember_idの状態によって取得条件を分岐する
     *
     * @param array $conditions
     *
     * @return mixed
     */
    public function findRedisplayRequests(array $conditions);

    /**
     * member_idがなかったら保存する
     *
     * @param Collection $redispalyRequests
     * @param int $memberId
     *
     * @return ItemDetailRedisplayRequest
     */
    public function saveMemberId($redispalyRequests, $memberId);
}

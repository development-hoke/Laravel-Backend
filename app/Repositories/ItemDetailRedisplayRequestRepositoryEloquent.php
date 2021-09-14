<?php

namespace App\Repositories;

use App\Models\ItemDetailRedisplayRequest;
use Illuminate\Database\Query\JoinClause;
use Prettus\Repository\Eloquent\BaseRepository;

/**
 * Class ItemDetailRedisplayRequestRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 *
 * @SuppressWarnings(PHPMD.LongClassName)
 */
class ItemDetailRedisplayRequestRepositoryEloquent extends BaseRepository implements ItemDetailRedisplayRequestRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return ItemDetailRedisplayRequest::class;
    }

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
    }

    /**
     * user_tokenとmember_idの状態によって取得条件を分岐する
     *
     * @param array $conditions
     *
     * @return mixed
     */
    public function findRedisplayRequests(array $conditions)
    {
        $query = $this->model->where('is_notified', false);

        if (isset($conditions['member_id'])) {
            $query = $query->where('item_detail_redisplay_requests.member_id', $conditions['member_id']);
        } else {
            $query = $query->where('item_detail_redisplay_requests.user_token', $conditions['user_token']);
        }

        $query = $query->join('item_details', function (JoinClause $join) use ($conditions) {
            return $join->on('item_detail_redisplay_requests.item_detail_id', '=', 'item_details.id')
                ->where('item_details.item_id', $conditions['item_id']);
        })
        ->select('item_detail_redisplay_requests.*');

        $models = $query->get();

        $this->resetModel();

        return $models;
    }

    /**
     * member_idがなかったら保存する
     *
     * @param Collection $redispalyRequests
     * @param int $memberId
     *
     * @return ItemDetailRedisplayRequest
     */
    public function saveMemberId($redispalyRequests, $memberId)
    {
        foreach ($redispalyRequests as &$row) {
            if (empty($row->member_id)) {
                $row->member_id = $memberId;
                $row->save();
            }
        }

        return $redispalyRequests;
    }
}

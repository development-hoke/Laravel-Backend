<?php

namespace App\Criteria\Item;

use Illuminate\Database\Eloquent\Builder;
use Prettus\Repository\Contracts\CriteriaInterface;
use Prettus\Repository\Contracts\RepositoryInterface;

/**
 * Class FrontMypageFavoriteCriteria.
 *
 * @package namespace App\Criteria\Item;
 */
class FrontMypageFavoriteCriteria implements CriteriaInterface
{
    const KEY = 'is_mypage_favorite';

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
        if (!isset($params[self::KEY])) {
            return $model;
        }

        $model = $model->favorites($params['member_id']);

        return $model;
    }
}

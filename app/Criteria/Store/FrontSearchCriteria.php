<?php

namespace App\Criteria\Store;

use Prettus\Repository\Contracts\CriteriaInterface;
use Prettus\Repository\Contracts\RepositoryInterface;

/**
 * Class FrontSearchCriteria.
 *
 * @package namespace App\Criteria\Store;
 */
class FrontSearchCriteria implements CriteriaInterface
{
    /**
     * @var array
     */
    private $params;

    /**
     * @param array $params
     */
    public function __construct(array $params)
    {
        $this->params = $params;
    }

    /**
     * Apply criteria in query repository
     *
     * @param mixed $model
     * @param RepositoryInterface $repository
     *
     * @return mixed
     */
    public function apply($model, RepositoryInterface $repository)
    {
        $params = $this->params;

        if (isset($params['q'])) {
            $keywords = explode(' ', mb_convert_kana($params['q'], 'KVs'));

            $model = $model->where(function ($query) use ($keywords) {
                foreach ($keywords as $word) {
                    $query->where(function ($query) use ($word) {
                        $condition = '%' . $word . '%';

                        return $query->where('stores.name', 'like', $condition)
                            ->orWhere('stores.address1', 'like', $condition)
                            ->orWhere('stores.address2', 'like', $condition);
                    });
                }
            });
        }

        $model = $this->applyItemDetailConditions($model, $params);

        return $model;
    }

    /**
     * @param mixed $model
     * @param array $params
     *
     * @return mixed
     */
    private function applyItemDetailConditions($model, array $params)
    {
        $conditions = [];

        if (isset($params['item_id'])) {
            $conditions[] = ['item_details.item_id', $params['item_id']];
        }

        if (isset($params['has_stock'])) {
            $conditions[] = ['item_detail_stores.stock', $params['has_stock'] ? '>' : '=', 0];
        }

        if (!empty($conditions)) {
            $model = $model->whereItemDetail($conditions);
        }

        return $model;
    }
}

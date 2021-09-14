<?php

namespace App\Criteria\Help;

use Illuminate\Http\Request;
use Prettus\Repository\Contracts\CriteriaInterface;
use Prettus\Repository\Contracts\RepositoryInterface;

/**
 * Class FrontHelpCriteria.
 *
 * @package namespace App\Criteria\Help;
 */
class FrontHelpCriteria implements CriteriaInterface
{
    /**
     * @var \Illuminate\Http\Request
     */
    protected $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
     * Apply criteria in query repository
     *
     * @param string $model
     * @param RepositoryInterface $repository
     *
     * @return mixed
     */
    public function apply($model, RepositoryInterface $repository)
    {
        $columns = ['is_faq'];
        $useLike = array_flip([]);

        $params = $this->request->all();

        foreach ($columns as $column) {
            if (!empty($params[$column])) {
                if (is_array($params[$column])) {
                    $model = $model->whereIn($column, $params[$column]);
                } elseif (!empty($useLike[$column])) {
                    $model = $model->where($column, 'like', "%{$params[$column]}%");
                } else {
                    $model = $model->where($column, $params[$column]);
                }
            }
        }

        if (isset($params['q'])) {
            $q = $params['q'];
            $model = $model->where(function ($subquery) use ($q) {
                $subquery->where('title', 'LIKE', "%{$q}%")
                    ->orWhereRaw('regexp_replace(body, "<[^>]*>|&nbsp;", "") LIKE ?', "%{$q}%");
            });
        }

        return $model->published();
    }
}

<?php

namespace App\Repositories\Traits;

use App\Database\Eloquent\CustomPaginationBuilder;
use App\Exceptions\FatalException;
use Illuminate\Database\Eloquent\Builder;

/**
 * paginateWithDistinctの提供。
 * デフォルトでdistinctを使ったpaginateは件数がおかしくなるので別のメソッドを定義。
 */
trait PaginateWithDistinctTrait
{
    /**
     * @param string $countColumn
     * @param int $limit
     * @param array $columns
     *
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function paginateWithDistinct(string $countColumn, $limit = null, $columns = ['*'])
    {
        $model = $this->model instanceof Builder ? $this->model : $this->model->getQuery();

        if (!($model instanceof CustomPaginationBuilder)) {
            throw new FatalException(__('error.invalid_instance', ['class' => CustomPaginationBuilder::class]));
        }

        $this->applyCriteria();
        $this->applyScope();

        $limit = is_null($limit) ? config('repository.pagination.limit', 15) : $limit;

        if ($model->toBase()->distinct) {
            $results = $model->paginateWithDistinct($countColumn, $limit, $columns);
        } else {
            $results = $model->paginate($limit, $columns);
        }

        $results->appends(app('request')->query());

        $this->resetModel();

        return $this->parserResult($results);
    }
}

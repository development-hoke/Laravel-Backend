<?php

namespace App\Repositories\Traits;

use Closure;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;

/**
 * QueryBuilderで提供されている一般的なメソッドを定義する。
 */
trait QueryBuilderMethodTrait
{
    /**
     * チャンクを実行する
     *
     * @param int $num
     * @param Closure $closure
     *
     * @return void
     */
    public function chunk(int $num, Closure $closure)
    {
        $this->applyCriteria();
        $this->applyScope();

        $query = $this->model instanceof Model ? $this->model->getQuery() : $this->model;

        if (empty($query->getQuery()->orders)) {
            $this->model = $this->model->orderBy('id', 'asc');
        }

        $this->model->chunk($num, $closure);

        $this->resetModel();
    }

    /**
     * whereIn句で対象を指定して削除する
     *
     * @param array $where
     *
     * @return int
     */
    public function deleteWhereIn($field, array $values)
    {
        $this->applyScope();

        $model = $this->model->whereIn($field, $values);

        $deleted = $model->delete();

        $this->resetModel();

        return $deleted;
    }

    /**
     * 指定した条件で最初の1件を取得する。なければModelNotFoundExceptionを投げる。
     *
     * @param array $where
     *
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function findOrFail(array $where)
    {
        $model = $this->findWhere($where)->first();

        if (empty($model)) {
            throw (
                new ModelNotFoundException(error_format('error.model_not_found', $where))
            )->setModel($this->model());
        }

        return $model;
    }
}

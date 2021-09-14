<?php

namespace App\Database\Eloquent;

use Illuminate\Database\Eloquent\Builder as BaseBuilder;
use Illuminate\Pagination\Paginator;

class CustomPaginationBuilder extends BaseBuilder
{
    /**
     * @param string|array $countColumns
     * @param int $perPage
     * @param array $columns
     * @param string $pageName
     * @param int $page
     *
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function paginateWithDistinct($countColumns, $perPage = null, $columns = ['*'], $pageName = 'page', $page = null)
    {
        $page = $page ?: Paginator::resolveCurrentPage($pageName);

        $perPage = $perPage ?: $this->model->getPerPage();

        $results = ($total = $this->toBase()->count($countColumns))
            ? $this->forPage($page, $perPage)->get($columns)
            : $this->model->newCollection();

        return $this->paginator($results, $total, $perPage, $page, [
            'path' => Paginator::resolveCurrentPath(),
            'pageName' => $pageName,
        ]);
    }

    /**
     * @param string|array $groupingColumns `group by` に指定するカラムを指定する
     * @param int $perPage
     * @param array $columns
     * @param string $pageName
     * @param int $page
     *
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function paginateWithGroupBy($groupingColumns, $perPage = null, $columns = ['*'], $pageName = 'page', $page = null)
    {
        $page = $page ?: Paginator::resolveCurrentPage($pageName);

        $perPage = $perPage ?: $this->model->getPerPage();

        $results = ($total = $this->getCountForGroupByPagination($this->toBase(), $groupingColumns))
            ? $this->forPage($page, $perPage)->get($columns)
            : $this->model->newCollection();

        return $this->paginator($results, $total, $perPage, $page, [
            'path' => Paginator::resolveCurrentPath(),
            'pageName' => $pageName,
        ]);
    }

    /**
     * paginateWithGroupByのためのカウント取得メソッド
     *
     * @param array $columns
     *
     * @return array
     */
    protected function getCountForGroupByPagination($query, $columns)
    {
        $without = $query->unions ? ['orders', 'limit', 'offset', 'groups'] : ['columns', 'orders', 'limit', 'offset', 'groups'];

        $results = $query->cloneWithout($without)
            ->cloneWithoutBindings($query->unions ? ['order'] : ['select', 'order'])
            ->distinct()
            ->count($this->withoutSelectAliases((array) $columns));

        return $results;
    }

    /**
     * エイリアスの除去
     *
     * @param array $columns
     *
     * @return array
     */
    protected function withoutSelectAliases(array $columns)
    {
        return \App\Database\Utils\Query::removeSelectAliases($columns);
    }
}

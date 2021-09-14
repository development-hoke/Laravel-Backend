<?php

namespace App\Repositories;

use Prettus\Repository\Contracts\RepositoryInterface;

/**
 * Interface EventItemRepository.
 *
 * @package namespace App\Repositories;
 */
interface EventItemRepository extends RepositoryInterface
{
    /**
     * @param array $attributes
     * @param $id
     * @param array $where
     *
     * @return mixed
     */
    public function updateWithCondition(array $attributes, $id, array $where);

    /**
     * 条件に一致したレコードから、新しいレコードを作成する
     *
     * @param array $where
     * @param array $replaceColumns 新しく置き換えるカラムの値
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function copyBatch(array $where, array $replaceColumns = []);
}

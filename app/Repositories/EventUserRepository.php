<?php

namespace App\Repositories;

use Prettus\Repository\Contracts\RepositoryInterface;

/**
 * Interface EventUserRepository.
 *
 * @package namespace App\Repositories;
 */
interface EventUserRepository extends RepositoryInterface
{
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

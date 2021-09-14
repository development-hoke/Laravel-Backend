<?php

namespace App\Repositories;

use Prettus\Repository\Contracts\RepositoryInterface;

/**
 * Interface PlanRepository.
 *
 * @package namespace App\Repositories;
 */
interface PlanRepository extends RepositoryInterface
{
    /**
     * 既存のレコードから、新しいレコードを作成する
     *
     * @param $id
     * @param array $where
     * @param array $replaceColumns 新しく置き換えるカラムの値
     *
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function copy($id, array $where = [], array $replaceColumns = []);
}

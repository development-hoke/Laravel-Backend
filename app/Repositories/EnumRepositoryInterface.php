<?php

namespace App\Repositories;

interface EnumRepositoryInterface
{
    /**
     * enumマスタの全値を取得する
     *
     * @return array
     */
    public function all(): array;
}

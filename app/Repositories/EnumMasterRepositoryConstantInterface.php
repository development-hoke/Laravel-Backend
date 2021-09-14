<?php

namespace App\Repositories;

interface EnumMasterRepositoryConstantInterface
{
    /**
     * クラス名（ネームスペース付き）からenumsのパスを取得する
     *
     * @param string $class
     *
     * @return string
     */
    public function resolveNamespase(string $class): string;

    /**
     * enumsの値を作成しセットする
     *
     * @return void
     */
    public function buildEnumMaster(): void;

    /**
     * enumマスタの全値を取得する
     *
     * @return array
     */
    public function all(): array;
}

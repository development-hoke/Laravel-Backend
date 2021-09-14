<?php

namespace App\Repositories;

use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Str;

abstract class EnumMasterRepositoryConstant extends BaseRepositoryConstant implements EnumMasterRepositoryConstantInterface
{
    /**
     * 指定 Enum
     * クラス名（ネームスペース付き）で指定
     *
     * @var array
     */
    protected $includes = [];

    /**
     * 除外 Enum
     * クラス名（ネームスペース付き）で指定
     *
     * @var array
     */
    protected $exculudes = [];

    /**
     * @var array
     */
    protected $enums;

    /**
     * クラス名（ネームスペース付き）からオブジェクトのキーを取得する
     *
     * @param string $class
     *
     * @return string
     */
    public function resolveNamespase(string $class): string
    {
        return Str::snake(str_replace(['App\\Enums\\', '\\'], ['', '_'], $class));
    }

    /**
     * enumsの値を作成しセットする
     *
     * @return void
     */
    public function buildEnumMaster(): void
    {
        $enums = [];
        foreach (Lang::get('enums') as $class => $labels) {
            // 指定enumに含まれていない場合
            if (!empty($this->includes) && !in_array($class, $this->includes)) {
                continue;
            }
            // 除外enumに含まれている場合
            if (!empty($this->exculudes) && in_array($class, $this->exculudes)) {
                continue;
            }
            $namespace = $this->resolveNamespase($class);
            $enums[$namespace] = $class::createEnumObject();
        }

        $this->enums = $enums;
    }

    /**
     * enumマスタの全値を取得する
     *
     * @return array
     */
    public function all(): array
    {
        return $this->enums;
    }
}

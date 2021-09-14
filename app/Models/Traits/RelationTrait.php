<?php

namespace App\Models\Traits;

use Illuminate\Database\Eloquent\Collection;

/**
 * リレーション関連のメソッド
 */
trait RelationTrait
{
    /**
     * ネストされたリレーションがロード済みか「.」でつなげてチェックできるようにする
     *
     * @param string $key
     *
     * @return bool
     */
    public function relationDeeplyLoaded(string $key)
    {
        if (strpos($key, '.') === false) {
            return $this->relationLoaded($key);
        }

        $model = $this;

        foreach (explode('.', $key) as $relation) {
            if (is_null($model) || !$model->relationLoaded($relation)) {
                return false;
            }

            $model = $model->{$relation} instanceof Collection
                ? $model->{$relation}->first()
                : $model->{$relation};
        }

        return true;
    }
}

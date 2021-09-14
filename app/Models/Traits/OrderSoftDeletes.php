<?php

namespace App\Models\Traits;

trait OrderSoftDeletes
{
    /**
     * スタッフID保存のため論理削除をアップデートで実行する
     *
     * @param int|null $staffId
     *
     * @return static
     */
    public function softDeleteBy($staffId = null)
    {
        $this->{$this->getDeletedAtColumn()} = $this->freshTimestamp();
        $this->update_staff_id = $staffId;
        $this->save();

        return $this;
    }
}

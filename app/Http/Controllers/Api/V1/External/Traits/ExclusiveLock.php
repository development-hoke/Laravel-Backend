<?php

namespace App\Http\Controllers\Api\V1\External\Traits;

use App\Utils\Cache;

trait ExclusiveLock
{
    /**
     * @param string|int $suffix
     *
     * @return string
     */
    abstract protected function getExclusiveLockKey($suffix = null): string;

    /**
     * 2重実行を防止するため、専有ロックをかける
     *
     * @param string|int $suffix
     *
     * @return bool
     */
    protected function lock($suffix = null)
    {
        $key = $this->getExclusiveLockKey($suffix);

        return Cache::put($key, 1);
    }

    /**
     * ロックの確認
     *
     * @param string|int $suffix
     *
     * @return bool
     */
    protected function locked($suffix = null)
    {
        $key = $this->getExclusiveLockKey($suffix);

        return Cache::has($key);
    }

    /**
     * ロックの解除
     *
     * @param string|int $suffix
     *
     * @return bool
     */
    protected function unlock($suffix = null)
    {
        $key = $this->getExclusiveLockKey($suffix);

        return Cache::forget($key);
    }
}

<?php

namespace App\Models\Traits;

use Illuminate\Support\Arr;

trait Logging
{
    /**
     * @param string $logClassName (Default: null)
     * @param string $foreignKey (Default: null)
     * @param array $excludes (Default: ['id', 'updated_at', 'created_at'])
     *
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function createLog(string $logClassName = null, string $foreignKey = null, array $excludes = ['id', 'updated_at', 'created_at'])
    {
        $foreignKey = $foreignKey ?? $this->getForeignKey();
        $logClassName = $logClassName ?? $this->getLogClassName();

        $attributes = Arr::except($this->toArray(), $excludes);

        $log = $this->newRelatedInstance($logClassName);

        $log->fill($attributes);
        $log->{$foreignKey} = $this->getKey();
        $log->save();

        return $log;
    }

    /**
     * ログモデルのクラス名を取得
     *
     * @return string
     */
    public function getLogClassName()
    {
        return get_class($this) . 'Log';
    }

    /**
     * @param string $className (Default: null)
     *
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function getLatestLog($className = null)
    {
        $className = $className ?? $this->getLogClassName();

        return $this->hasMany($className)->orderBy('id', 'desc')->first();
    }
}

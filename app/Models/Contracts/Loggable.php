<?php

namespace App\Models\Contracts;

interface Loggable
{
    /**
     * @param string $logClassName (Default: null)
     * @param string $foreignKey (Default: null)
     * @param array $excludes (Default: ['id', 'updated_at', 'created_at'])
     *
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function createLog(string $logClassName = null, string $foreignKey = null, array $excludes = ['id', 'updated_at', 'created_at']);

    /**
     * @param string $className (Default: null)
     *
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function getLatestLog($className = null);
}

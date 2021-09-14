<?php

namespace App\Services\Admin;

interface ItemDetailServiceInterface
{
    /**
     * CSVエクスポートを実行するコールバックを取得する
     *
     * @param array $params
     *
     * @return \Closure
     */
    public function exportStockCsv(array $params);
}

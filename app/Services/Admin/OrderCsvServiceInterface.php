<?php

namespace App\Services\Admin;

interface OrderCsvServiceInterface
{
    /**
     * 受注詳細情報 (受注単位) CSVエクスポートを実行するコールバックを取得する
     *
     * @param array $params
     *
     * @return \Closure
     */
    public function exportOrderCsv(array $params);

    /**
     * 受注詳細情報 (明細単位) CSVエクスポートを実行するコールバックを取得する
     *
     * @param array $params
     *
     * @return \Closure
     */
    public function exportOrderDetailCsv(array $params);
}

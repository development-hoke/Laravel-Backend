<?php

namespace App\Services\Admin;

interface PastItemServiceInterface
{
    /**
     * @param array $params
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function search(array $params);

    /**
     * CSVの保存を実行する。
     *
     * @param array $params
     *
     * @return \App\Models\ItemBulkUpload
     */
    public function storeItemCsv(array $params);
}

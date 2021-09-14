<?php

namespace App\Services\Admin;

interface SalesTypeServiceInterface
{
    /**
     * sales_types, item_sales_typesをまとめて削除
     *
     * @param int $id
     *
     * @return void
     */
    public function delete($id);

    /**
     * CSVエクスポート
     *
     * @return \Closure
     */
    public function getCsvExporter();
}

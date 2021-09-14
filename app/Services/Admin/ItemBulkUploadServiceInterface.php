<?php

namespace App\Services\Admin;

interface ItemBulkUploadServiceInterface
{
    /**
     * CSVの保存を実行する。
     *
     * @param array $params
     *
     * @return \App\Models\ItemBulkUpload
     */
    public function storeItemCsv(array $params);

    /**
     * エラーCSVを出力するコールバックを取得する
     *
     * @param int $id
     *
     * @return array
     */
    public function getErrorCsvExporter(int $id);

    /**
     * 商品情報一括登録CSVのサンプルを取得
     *
     * @return \Closure
     */
    public function getItemCsvFormatExporter();

    /**
     * 商品画像一括登録CSVのサンプルを取得
     *
     * @return \Closure
     */
    public function getItemImageCsvFormatExporter();

    /**
     * @param \Illuminate\Http\UploadedFile $zip
     * @param array $params
     *
     * @return \App\Models\ItemBulkUpload
     */
    public function importItemImages(\Illuminate\Http\UploadedFile $zip, array $params);
}

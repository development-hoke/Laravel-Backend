<?php

namespace App\Utils\Csv;

use Illuminate\Validation\ValidationException;

interface ImportCsvInterface
{
    /**
     * @param array $headers
     *
     * @return void
     */
    public function setHeaders(array $headers);

    /**
     * @param string $enclosure
     *
     * @return void
     */
    public function setEnclosure(string $enclosure);

    /**
     * @param array $rules
     *
     * @return void
     */
    public function setValidationRules(array $rules);

    /**
     * @param array $messages
     *
     * @return void
     */
    public function setValidationMessages(array $messages);

    /**
     * @param array $attributes
     *
     * @return void
     */
    public function setValidationAttributes(array $attributes);

    /**
     * CSVインポートの共通処理。
     * 実際の保存処理はクロージャで行う。
     * エラーメッセージの配列と保存済みのデータの配列を返す。
     *
     * @param string $csv
     * @param \Closure $storeRow
     * @param bool $withHeader
     *
     * @return array
     */
    public function import(string $csv, \Closure $storeRow, bool $withHeader = true);

    /**
     * CSVからパラメータを抽出する
     *
     * @param string $csv
     * @param bool $withHeader
     *
     * @return array
     */
    public function extract(string $csv, bool $withHeader = true);

    /**
     * @param \Exception|string $e
     * @param int $csvLine
     *
     * @return string
     */
    public function formatErrorReport($e, int $csvLine);

    /**
     * @param array $row
     *
     * @throws ValidationException
     *
     * @return void
     */
    public function validate(array $row);
}

<?php

namespace App\Utils\Csv;

use App\Utils\FileUploadUtil;
use Closure;
use Exception;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class ImportCsv implements ImportCsvInterface
{
    const DEFAULT_CHUNK_SPLIT_NUM = 50;

    /**
     * @var array
     */
    protected $headers = [];

    /**
     * @var string
     */
    protected $enclosure = '"';

    /**
     * @var array
     */
    protected $validationRules = [];

    /**
     * @var array
     */
    protected $validationMessages = [];

    /**
     * @var array
     */
    protected $validationAttributes = [];

    /**
     * @var array
     */
    protected $acceptableContentType = ['*'];

    /**
     * @param array $headers
     *
     * @return void
     */
    public function setHeaders(array $headers)
    {
        $this->headers = $headers;
    }

    /**
     * @param string $enclosure
     *
     * @return void
     */
    public function setEnclosure(string $enclosure)
    {
        $this->enclosure = $enclosure;
    }

    /**
     * @param array $rules
     *
     * @return void
     */
    public function setValidationRules(array $rules)
    {
        $this->validationRules = $rules;
    }

    /**
     * @param array $messages
     *
     * @return void
     */
    public function setValidationMessages(array $messages)
    {
        $this->validationMessages = $messages;
    }

    /**
     * @param array $attributes
     *
     * @return void
     */
    public function setValidationAttributes(array $attributes)
    {
        $this->validationAttributes = $attributes;
    }

    /**
     * CSVインポートの共通処理。
     * 実際の保存処理はクロージャで行う。
     * エラーメッセージの配列と保存済みのデータの配列を返す。
     *
     * @param string $csv
     * @param Closure $storeRow
     * @param bool $withHeader
     *
     * @return array
     */
    public function import(string $csv, Closure $storeRow, bool $withHeader = true)
    {
        $csv = $this->preprocess($csv);

        if ($withHeader) {
            array_shift($csv);
        }

        $failed = [];
        $succeeded = [];

        foreach ($csv as $index => $row) {
            try {
                if (empty($row)) {
                    continue;
                }

                $row = $this->preprocessRow($row);
                $succeeded[] = $storeRow($row);
            } catch (Exception $e) {
                $failed[] = $this->formatErrorReport($e, $index + 1);
            }
        }

        return ['failed' => $failed, 'succeeded' => $succeeded];
    }

    /**
     * CSVからパラメータを抽出する
     *
     * @param string $csv
     * @param bool $withHeader
     *
     * @return array
     */
    public function extract(string $csv, bool $withHeader = true)
    {
        $csv = $this->preprocess($csv);

        if ($withHeader) {
            array_shift($csv);
        }

        $params = [];

        foreach ($csv as $row) {
            if (empty($row)) {
                continue;
            }

            $row = $this->preprocessRow($row);

            $params[] = $row;
        }

        return $params;
    }

    /**
     * @param mixed $csv
     *
     * @return array
     */
    protected function preprocess($csv)
    {
        if (FileUploadUtil::isBase64Encoded($csv)) {
            list($csv, $contentType) = FileUploadUtil::extractContentBase64($csv);
            FileUploadUtil::validateContentType($contentType, $this->acceptableContentType);
        }

        $csv = mb_convert_encoding($csv, 'UTF-8', 'auto');

        if (strpos($csv, "\r\n") !== false) {
            $csv = explode("\r\n", $csv);
        } else {
            $csv = explode("\n", $csv);
        }

        return $csv;
    }

    /**
     * @param string $row
     *
     * @return array
     */
    protected function preprocessRow(string $row)
    {
        $row = str_getcsv($row, ',', $this->enclosure);

        $translated = [];

        foreach ($this->headers as $index => $column) {
            $value = $row[$index];
            $value = trim($value);
            $value = $value === '' ? null : $value;
            $translated[$column] = $value;
        }

        return $translated;
    }

    /**
     * @param Exception|string $e
     * @param int $csvLine
     *
     * @return string
     */
    public function formatErrorReport($e, int $csvLine)
    {
        $message = '';

        if (is_string($e)) {
            $message = $e;
        } elseif ($e instanceof ValidationException) {
            $errors = $e->errors();
            $message = current($errors)[0];
        } else {
            $message = $e->getMessage();
        }

        return sprintf('%d行目: %s', $csvLine, $message);
    }

    /**
     * @param array $row
     *
     * @throws ValidationException
     *
     * @return void
     */
    public function validate(array $row)
    {
        $validator = Validator::make(
            $row,
            $this->validationRules,
            $this->validationMessages,
            $this->validationAttributes
        );

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }
    }
}

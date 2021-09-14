<?php

namespace App\Utils\Csv;

class ExportCsv implements ExportCsvInterface
{
    /**
     * @var array
     */
    protected $headers = [];

    /**
     * @var string
     */
    protected $outputEncoding = 'SJIS';

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
     * @param string $encodeing
     *
     * @return void
     */
    public function setOutputEncoding(string $encodeing)
    {
        $this->outputEncoding = $encodeing;
    }

    /**
     * @param \Closure $provider
     *
     * @return \Closure
     */
    public function getExporter(\Closure $provider)
    {
        return function () use ($provider) {
            $file = fopen('php://output', 'w');

            fputcsv($file, array_map(function ($value) {
                return mb_convert_encoding($value, $this->outputEncoding);
            }, $this->headers));

            $exporter = function ($row) use ($file) {
                $data = [];

                foreach ($this->headers as $key => $name) {
                    $value = $this->extractValue($row, $key);

                    $data[] = mb_convert_encoding($value, $this->outputEncoding);
                }

                fputcsv($file, $data);
            };

            $provider($exporter);

            fclose($file);
        };
    }

    /**
     * @param object|array $row
     * @param mixed $key
     *
     * @return mixed
     */
    private function extractValue($row, $key)
    {
        if (is_array($row)) {
            return $row[$key] ?? '';
        }

        return $row->{$key} ?? '';
    }
}

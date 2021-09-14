<?php

namespace App\Utils\Csv;

interface ExportCsvInterface
{
    /**
     * @param array $headers
     *
     * @return void
     */
    public function setHeaders(array $headers);

    /**
     * @param string $encodeing
     *
     * @return void
     */
    public function setOutputEncoding(string $encodeing);

    /**
     * @param \Closure $provider
     *
     * @return \Closure
     */
    public function getExporter(\Closure $provider);
}

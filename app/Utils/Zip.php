<?php

namespace App\Utils;

use App\Exceptions\Util\FailedZipProcessException;
use ZipArchive;

class Zip implements ZipInterface
{
    /**
     * @var ZipArchive
     */
    private $client;

    /**
     * @param mixed $client
     */
    public function __construct($client = null)
    {
        $this->client = $client ?? new ZipArchive();
    }

    /**
     * ZIPの展開
     *
     * @param string $source
     * @param string $destination
     * @param mixed $entries
     *
     * @return bool
     */
    public function extract(string $source, string $destination, $entries = null)
    {
        if (!$this->client->open($source)) {
            throw new FailedZipProcessException(__('error.failed_to_open_zip', ['path' => $source]));
        }

        if (!$this->client->extractTo($destination, $entries)) {
            throw new FailedZipProcessException(__('error.failed_to_extract_zip', [
                'source' => $source,
                'destination' => $destination,
            ]));
        }

        return true;
    }
}

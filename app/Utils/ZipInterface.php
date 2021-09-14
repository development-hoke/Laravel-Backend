<?php

namespace App\Utils;

interface ZipInterface
{
    /**
     * ZIPの展開
     *
     * @param string $source
     * @param string $destination
     * @param mixed $entries
     *
     * @return bool
     *
     * @throws \App\Exceptions\Util\FailedZipProcessException
     */
    public function extract(string $source, string $destination, $entries = null);
}

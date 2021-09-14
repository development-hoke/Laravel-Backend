<?php

namespace App\Utils;

class FileDownloadUtil
{
    public static function getExportFileHeaders(string $fileName, string $contentType)
    {
        $headers = [
            'Content-type' => $contentType,
            'Content-Disposition' => sprintf('attachment; filename="%s"; filename*=UTF-8', urlencode($fileName)),
            'Access-Control-Expose-Headers' => 'Content-Disposition',
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        return $headers;
    }
}

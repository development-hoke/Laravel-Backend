<?php

namespace App\Utils;

use App\Exceptions\FatalException;
use App\Exceptions\FileUploadException;

class FileUploadUtil
{
    private static $ct2ext = [
        FileUtil::MIME_TYPE_CSV => FileUtil::EXT_CSV,
        FileUtil::MIME_TYPE_JPG => FileUtil::EXT_JPG,
        FileUtil::MIME_TYPE_PNG => FileUtil::EXT_PNG,
        FileUtil::MIME_TYPE_GIF => FileUtil::EXT_GIF,
    ];

    /**
     * @param mixed $data
     *
     * @return bool
     */
    public static function isBase64Encoded($data)
    {
        return (bool) preg_match('/data:(.+);base64/', (string) $data);
    }

    /**
     * Base64からデータをContentTypeとデコード済みの値を抽出する
     *
     * @param string $data
     *
     * @return array
     */
    public static function extractContentBase64(string $data)
    {
        list($header, $content) = explode(',', $data);

        if (!preg_match('/data:(.+);base64/', $header, $matchs)) {
            throw new FileUploadException(error_format('error.invalid_upload_file'));
        }

        $contentType = $matchs[1];

        return [base64_decode($content), $contentType];
    }

    /**
     * @param string $contentType
     * @param array $acceptable
     *
     * @return bool
     *
     * @throws FileUploadException
     */
    public static function validateContentType(string $contentType, array $acceptable = ['*'])
    {
        if (current($acceptable) !== '*' && !in_array(strtolower($contentType), $acceptable)) {
            throw new FileUploadException(error_format('error.invalid_content_type'));
        }

        return true;
    }

    /**
     * @param string $contentType
     *
     * @return string
     *
     * @throws FatalException
     */
    public static function contentType2Ext(string $contentType)
    {
        if (!isset(static::$ct2ext[$contentType])) {
            throw new FatalException(error_format('error.invalid_arguments'));
        }

        return static::$ct2ext[$contentType];
    }

    /**
     * @param string $disk
     * @param string $dir
     * @param string $name
     * @param string $ext
     * @param bool|null $ignoreOverwrite
     *
     * @return string
     */
    public static function decideFilePath(string $disk, string $dir, string $name, string $ext, ?bool $ignoreOverwrite = false)
    {
        $filePath = static::makeFilePath($dir, $name, $ext);

        if ($ignoreOverwrite) {
            return $filePath;
        }

        $incrementor = 0;

        $disk = \App\Utils\FileUtil::getDisk($disk);

        while ($disk->exists($filePath)) {
            $filePath = static::makeFilePath($dir, $name, $ext, ++$incrementor);
        }

        return $filePath;
    }

    /**
     * @param string $disk
     * @param string $dir
     * @param string $name
     * @param string $ext
     * @param int|null $suffix
     *
     * @return string
     */
    public static function makeFilePath(string $dir, string $name, string $ext, ?int $suffix = null)
    {
        $name = ltrim($name, '/');
        $dir = rtrim($dir, '/');

        if (!isset($suffix)) {
            return $dir . '/' . $name . '.' . $ext;
        }

        return $dir . '/' . $name . '_' . $suffix . '.' . $ext;
    }

    /**
     * 新しいファイルパスを生成する
     *
     * @param string $disk
     * @param string $dir
     * @param string $fileName
     * @param string|null $contentType
     * @param bool|null $ignoreOverwrite
     *
     * @return string
     */
    public static function generateNewFilePath(string $disk, string $dir, string $fileName, ?string $contentType = null, ?bool $ignoreOverwrite = false)
    {
        $ext = isset($contentType) ? static::contentType2Ext($contentType) : null;
        $info = pathinfo($fileName);
        $filePath = static::decideFilePath($disk, $dir, $info['filename'], $ext ?? $info['extension'], $ignoreOverwrite);

        return $filePath;
    }

    /**
     * 新しい画像パスを生成する
     *
     * @param string $dir
     * @param string $fileName
     * @param string|null $contentType
     * @param string|null $disk
     * @param bool|null $ignoreOverwrite
     *
     * @return string
     */
    public static function generateNewImageFilePath(string $dir, string $fileName, ?string $contentType = null, ?string $disk = null, ?bool $ignoreOverwrite = false)
    {
        if (!isset($disk)) {
            $disk = config('filesystems.image');
        }

        return static::generateNewFilePath($disk, $dir, $fileName, $contentType, $ignoreOverwrite);
    }
}

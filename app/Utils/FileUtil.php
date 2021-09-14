<?php

namespace App\Utils;

use Illuminate\Support\Facades\Storage;

class FileUtil
{
    const MIME_TYPE_CSV = 'text/csv';
    const MIME_TYPE_JPG = 'image/jpeg';
    const MIME_TYPE_PNG = 'image/png';
    const MIME_TYPE_GIF = 'image/gif';

    const EXT_CSV = 'csv';
    const EXT_JPG = 'jpg';
    const EXT_PNG = 'png';
    const EXT_GIF = 'gif';

    public static function getIgnoreZipContentNames()
    {
        return [
            '__MACOSX',
        ];
    }

    /**
     * @param string $disk
     *
     * @return \Illuminate\Contracts\Filesystem\Filesystem
     */
    public static function getDisk(string $disk)
    {
        return Storage::disk($disk);
    }

    /**
     * @param string|null $disk
     *
     * @return \Illuminate\Contracts\Filesystem\Filesystem
     */
    public static function getPublicImageDisk(?string $disk = null)
    {
        return static::getDisk($disk ?? config('filesystems.image'));
    }

    /**
     * 公開用画像をドライブに置く
     *
     * @param string $filePath
     * @param string $content
     *
     * @return string 新しい画像URL
     */
    public static function putPublicImage($filePath, $content)
    {
        $disk = static::getPublicImageDisk();

        $disk->put($filePath, $content);

        return $disk->url($filePath);
    }

    /**
     * 公開用画像をドライブから削除する
     *
     * @param string $filePath
     *
     * @return void
     */
    public static function deletePublicImage($filePath)
    {
        $filePath = static::nomarizePublicImagePath($filePath);

        $disk = static::getPublicImageDisk();

        $disk->delete($filePath);
    }

    /**
     * 公開用画像をコピーする
     *
     * @param string $filePath
     *
     * @return void
     */
    public static function copyPublicImage(string $srcPath, string $destPath)
    {
        $srcPath = static::nomarizePublicImagePath($srcPath);
        $destPath = static::nomarizePublicImagePath($destPath);

        $disk = static::getPublicImageDisk();

        if ($disk->exists($destPath)) {
            $disk->delete($destPath);
        }

        $disk->copy($srcPath, $destPath);
    }

    /**
     * @param \Illuminate\Contracts\Filesystem\Filesystem $disk
     * @param string $path
     *
     * @return string
     */
    private static function nomarizePublicImagePath(string $path)
    {
        $disk = static::getPublicImageDisk();

        $baseUrl = rtrim($disk->url(''), '/');

        if (strpos($path, $baseUrl) === 0) {
            $path = str_replace($baseUrl, '', $path);
        }

        if (strpos($path, 'http') === 0) {
            $path = \App\Utils\Url::extractPath($path);
        }

        return $path;
    }

    /**
     * @param string|null $disk
     *
     * @return string
     */
    public static function generateTempDir(?string $disk = 'local')
    {
        $disk = static::getDisk($disk);

        $uuid = \Webpatser\Uuid\Uuid::generate(4);

        return sprintf('%s/%s', config('filesystems.dirs.local.tmp'), $uuid);
    }
}

<?php

namespace App\Domain\Utils;

use App\Utils\FileUtil;
use App\Utils\Image;

class ItemImage
{
    /**
     * @return int
     */
    public static function getNormalImageType()
    {
        return \App\Enums\ItemImage\Type::Normal;
    }

    /**
     * @return array
     */
    public static function getBackendImageTypes()
    {
        return [
            \App\Enums\ItemImage\Type::DeploymentPhotoFront,
            \App\Enums\ItemImage\Type::DeploymentPhotoBack,
        ];
    }

    /**
     * @param string $path
     * @param mixed $content
     *
     * @return array
     */
    public static function storeFile($path, $content)
    {
        $urls = static::getResizedUrls($path);
        $urls[\App\Enums\ItemImage\Size::Original] = $path;
        $configs = config('image.resize.item');

        $storedUrls = [];

        foreach ($urls as $type => $targetUrl) {
            $data = $content;

            if ($type !== \App\Enums\ItemImage\Size::Original) {
                $config = $configs[$type];
                $data = Image::make($content)->resize($config['w'], $config['h'])->encode();
            }

            $storedUrls[$type] = FileUtil::putPublicImage($targetUrl, $data);
        }

        return $storedUrls;
    }

    /**
     * @param string $url
     *
     * @return void
     */
    public static function deleteFile($url)
    {
        foreach (array_merge([$url], static::getResizedUrls($url)) as $target) {
            FileUtil::deletePublicImage($target);
        }
    }

    /**
     * @param string $url
     *
     * @return array
     */
    public static function getResizedUrls($url)
    {
        return [
            \App\Enums\ItemImage\Size::S => static::toS($url),
            \App\Enums\ItemImage\Size::M => static::toM($url),
            \App\Enums\ItemImage\Size::L => static::toL($url),
            \App\Enums\ItemImage\Size::XL => static::toXL($url),
        ];
    }

    /**
     * @param string $url
     *
     * @return string
     */
    public static function toS($url)
    {
        return static::buildUrl($url, \App\Enums\ItemImage\Size::S);
    }

    /**
     * @param string $url
     *
     * @return string
     */
    public static function toM($url)
    {
        return static::buildUrl($url, \App\Enums\ItemImage\Size::M);
    }

    /**
     * @param string $url
     *
     * @return string
     */
    public static function toL($url)
    {
        return static::buildUrl($url, \App\Enums\ItemImage\Size::L);
    }

    /**
     * @param string $url
     *
     * @return string
     */
    public static function toXL($url)
    {
        return static::buildUrl($url, \App\Enums\ItemImage\Size::XL);
    }

    /**
     * @param string $url
     * @param string|null $suffix
     *
     * @return string
     */
    public static function buildUrl(string $url, ?string $suffix = null, $withoutHost = false)
    {
        $info = static::parseUrl($url);

        $url = ($withoutHost ? '' : $info['host'])
            . '/' . trim($info['dirname'], '/')
            . '/' . $info['filename'];

        if (!isset($suffix)) {
            return $url . '.' . $info['extension'];
        }

        return $url . '_' . $suffix . '.' . $info['extension'];
    }

    /**
     * @param string $url
     *
     * @return array
     */
    public static function parseUrl(string $url)
    {
        $urlInfo = parse_url($url);

        $pathInfo = pathinfo($urlInfo['path']);

        $host = trim($urlInfo['host'] ?? '', '/');

        if ($host !== '' && isset($urlInfo['scheme'])) {
            $host = $urlInfo['scheme'] . '://' . $host;
        }

        $pathInfo['host'] = $host;

        return $pathInfo;
    }

    /**
     * @param \App\Models\Item $item
     *
     * @return string
     */
    public static function generateNewFileDir(\App\Models\Item $item)
    {
        $baseDir = sprintf('%s/%s', config('filesystems.dirs.image.item'), $item->product_number);

        $sequence = count(FileUtil::getPublicImageDisk()->directories($baseDir)) + 1;

        return $baseDir . '/' . $sequence;
    }

    /**
     * @param \App\Models\Item $item
     * @param string $fileName
     * @param string|null $contentType
     *
     * @return string
     */
    public static function generateNewFilePath(\App\Models\Item $item, string $fileName, ?string $contentType = null)
    {
        $dir = static::generateNewFileDir($item);

        $filePath = \App\Utils\FileUploadUtil::generateNewImageFilePath($dir, $fileName, $contentType);

        return $filePath;
    }

    /**
     * @param \App\Models\Item $item
     * @param string $ext
     *
     * @return string
     */
    public static function generateThumbnailPath(\App\Models\Item $item, string $ext)
    {
        $dir = sprintf('%s/%s', config('filesystems.dirs.image.item'), $item->product_number);

        $filePath = \App\Utils\FileUploadUtil::generateNewImageFilePath($dir, 'thumb.' . $ext, null, null, true);

        return $filePath;
    }

    /**
     * @return array
     */
    public static function getAcceptableItemImageContentType()
    {
        return [
            \App\Utils\FileUtil::MIME_TYPE_JPG,
            \App\Utils\FileUtil::MIME_TYPE_PNG,
        ];
    }
}

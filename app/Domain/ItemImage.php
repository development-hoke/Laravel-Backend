<?php

namespace App\Domain;

use App\Domain\Utils\ItemImage as ItemImageUtil;
use App\Exceptions\FatalException;
use App\Exceptions\FileUploadException;
use App\Repositories\ItemImageRepository;
use App\Repositories\ItemRepository;
use App\Utils\FileUploadUtil;
use Carbon\Carbon;

class ItemImage implements ItemImageInterface
{
    /**
     * @var ItemImageRepository
     */
    private $itemImageRepository;

    /**
     * @var ItemRepository
     */
    private $itemRepository;

    /**
     * @param ItemImageRepository $itemImageRepository
     */
    public function __construct(ItemImageRepository $itemImageRepository, ItemRepository $itemRepository)
    {
        $this->itemImageRepository = $itemImageRepository;
        $this->itemRepository = $itemRepository;
    }

    /**
     * 画像データの入れ替え
     *
     * @param array $params
     * @param \App\Models\Item $item
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function deleteAndInsertItemImages(array $params, \App\Models\Item $item)
    {
        $storedImages = $this->putNewFiles($params['item_images'], ItemImageUtil::generateNewFileDir($item));

        $newUrls = collect($storedImages)->map(function ($image) {
            return $image['url'];
        });

        try {
            $oldUrls = $this->itemImageRepository->findWhere(['item_id' => $item->id])->map(function ($image) {
                return $image->url;
            });

            $itemImages = $this->itemImageRepository->deleteAndInsertBatch($storedImages, 'item_id', $item->id);

            foreach ($oldUrls as $url) {
                if (!$newUrls->contains($url)) {
                    ItemImageUtil::deleteFile($url);
                }
            }

            $this->createThumbnail($item->id);

            return $itemImages;
        } catch (\Exception $e) {
            foreach ($storedImages as $image) {
                if ($image['is_new']) {
                    ItemImageUtil::deleteFile($image['url']);
                }
            }

            throw $e;
        }
    }

    /**
     * @param array $itemImgaes
     * @param \App\Models\Item $item
     * @param string $previewKey
     *
     * @return array
     */
    public function putNewPreviewFiles(array $itemImgaes, \App\Models\Item $item, string $previewKey)
    {
        $datetime = Carbon::now();
        $dir = sprintf('%s/%s/%s/%s', config('filesystems.dirs.image.item_preview'), $datetime->timestamp, $previewKey, $item->product_number);

        return $this->putNewFiles($itemImgaes, $dir, $previewKey);
    }

    /**
     * @param array $itemImgaes
     * @param \App\Models\Item $item
     *
     * @return array
     */
    private function putNewFiles(array $itemImgaes, string $dir)
    {
        $storedImages = [];

        foreach ($itemImgaes as $image) {
            if (!$image['is_new']) {
                $storedImages[] = $image;
                continue;
            }

            [$content, $contentType] = FileUploadUtil::extractContentBase64($image['raw_image']);

            if (!in_array($contentType, ItemImageUtil::getAcceptableItemImageContentType())) {
                throw new FileUploadException(error_format('error.invalid_content_type'));
            }

            $filePath = \App\Utils\FileUploadUtil::generateNewImageFilePath($dir, $image['file_name'], $contentType);

            $image['url'] = ItemImageUtil::storeFile($filePath, $content)[
                \App\Enums\ItemImage\Size::Original
            ];

            $storedImages[] = $image;
        }

        return $storedImages;
    }

    /**
     * @param \App\Models\Item $item
     * @param mixed $content
     * @param array $params
     *
     * @return \App\Models\ItemImage
     */
    public function create(\App\Models\Item $item, $content, array $params)
    {
        $filePath = ItemImageUtil::generateNewFilePath($item, $params['file_name']);

        $url = ItemImageUtil::storeFile($filePath, $content)[
            \App\Enums\ItemImage\Size::Original
        ];

        try {
            $attributes = [
                'item_id' => $item->id,
                'url' => $url,
                'file_name' => $params['file_name'],
                'caption' => $params['caption'],
                'color_id' => $params['color_id'],
            ];

            if (isset($params['sort'])) {
                $attributes['sort'] = $params['sort'];
                $this->itemImageRepository->resort($item->id, $params['sort']);
            }

            $itemImage = $this->itemImageRepository->create($attributes);

            $this->createThumbnail($item->id);
        } catch (\Exception $e) {
            ItemImageUtil::deleteFile($url);

            throw $e;
        }

        return $itemImage;
    }

    /**
     * 事部品番のみで一意に取得できるサムネイルを作成
     *
     * @param int $itemId
     *
     * @return void
     */
    private function createThumbnail(int $itemId)
    {
        $item = $this->itemRepository->with(['itemImages'])->find($itemId);

        $itemImage = $item->itemImages->first();

        if (empty($itemImage)) {
            throw new FatalException();
        }

        $info = pathinfo($itemImage->url_m);

        $thumbPath = ItemImageUtil::generateThumbnailPath($item, $info['extension']);

        \App\Utils\FileUtil::copyPublicImage($itemImage->url_m, $thumbPath);
    }
}

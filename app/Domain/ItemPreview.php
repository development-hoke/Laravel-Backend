<?php

namespace App\Domain;

use App\Domain\ItemImageInterface as ItemImageService;
use App\Models\Collections\Collection;
use App\Repositories\ItemRepository;
use App\Repositories\OnlineCategoryRepository;
use App\Repositories\OnlineTagRepository;
use App\Repositories\SalesTypeRepository;
use App\Services\Front\ItemServiceInterface as FrontItemService;
use App\Utils\Arr;
use App\Utils\Cache;
use App\Utils\FileUtil;
use Carbon\Carbon;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ItemPreview implements ItemPreviewInterface
{
    /**
     * キャッシュの保存時間（24時間）
     */
    const DATA_EXPIRATION_SEC = 86400;

    /**
     * @var FrontItemService
     */
    private $frontItemService;

    /**
     * @var SalesTypeRepository
     */
    private $salesTypeRepository;

    /**
     * @var ItemRepository
     */
    private $itemRepository;

    /**
     * @var OnlineCategoryRepository
     */
    private $onlineCategoryRepository;

    /**
     * @var OnlineTagRepository
     */
    private $onlineTagRepository;

    /**
     * @var ItemImageService
     */
    private $itemImageService;

    /**
     * @param FrontItemService $frontItemService
     * @param SalesTypeRepository $salesTypeRepository
     * @param ItemRepository $itemRepository
     * @param OnlineCategoryRepository $onlineCategoryRepository
     * @param OnlineTagRepository $onlineTagRepository
     * @param ItemImageService $itemImageService
     */
    public function __construct(
        FrontItemService $frontItemService,
        SalesTypeRepository $salesTypeRepository,
        ItemRepository $itemRepository,
        OnlineCategoryRepository $onlineCategoryRepository,
        OnlineTagRepository $onlineTagRepository,
        ItemImageService $itemImageService
    ) {
        $this->frontItemService = $frontItemService;
        $this->salesTypeRepository = $salesTypeRepository;
        $this->itemRepository = $itemRepository;
        $this->onlineCategoryRepository = $onlineCategoryRepository;
        $this->onlineTagRepository = $onlineTagRepository;
        $this->itemImageService = $itemImageService;
    }

    /**
     * プレビューデータの保存
     *
     * @param int $id
     * @param array $params
     *
     * @return array cache info
     */
    public function store(int $id, array $params)
    {
        $previewKey = (string) \Webpatser\Uuid\Uuid::generate(4);

        $item = $this->itemRepository->find($id);
        $item = $this->frontItemService->fetchDetail($item->product_number, null, true);
        $item->fill($params);

        $itemDetailParams = Arr::dict($params['item_details'], 'id');

        foreach ($item->itemDetails as $itemDetail) {
            if (isset($itemDetailParams[$itemDetail->id])) {
                $itemDetail->fill($itemDetailParams[$itemDetail->id]);
            }
        }

        if (isset($params['sales_types'])) {
            $salesTypeParams = collect($params['sales_types'])->sortBy('sort');
            $salesTypes = $this->salesTypeRepository->findWhereIn('id', $salesTypeParams->pluck('id')->toArray());
            $item->setRelation('salesTypes', $salesTypes);
        }

        if (isset($params['item_sub_brands'])) {
            $subBrands = Arr::map($params['item_sub_brands'], function ($subStoreBrand) use ($item) {
                return new \App\Models\ItemSubBrand(['sub_store_brand' => $subStoreBrand, 'item_id' => $item->id]);
            });
            $item->setRelation('itemSubBrands', Collection::make($subBrands));
        }

        if (isset($params['online_category_id'])) {
            $onlineCategories = $this->onlineCategoryRepository->with('ancestors')->findWhereIn('id', $params['online_category_id']);
            $item->setRelation('onlineCategories', $onlineCategories);
        }

        if (isset($params['online_tag_id'])) {
            $onlineTags = $this->onlineTagRepository->findWhereIn('id', $params['online_tag_id']);
            $item->setRelation('onlineTags', $onlineTags);
        }

        if (isset($params['items_used_same_styling_used_item_id'])) {
            $itemsUsedSameStylings = $this->itemRepository->findWhereIn('id', $params['items_used_same_styling_used_item_id']);
            $item->setRelation('itemsUsedSameStylings', $itemsUsedSameStylings);
        } else {
            $item->load('itemsUsedSameStylings');
        }

        $item->itemsUsedSameStylings->load(['itemImages', 'salesTypes', 'brand']);
        $this->frontItemService->fillAdditionalItemRecommendationsAttributes($item->itemsUsedSameStylings);

        if (isset($params['recommend_item_id'])) {
            $recommendItems = $this->itemRepository->findWhereIn('id', $params['recommend_item_id']);
            $item->setRelation('recommendItems', $recommendItems);
        } else {
            $item->load('recommendItems');
        }

        $item->recommendItems->load(['itemImages', 'salesTypes', 'brand']);
        $this->frontItemService->fillAdditionalItemRecommendationsAttributes($item->recommendItems);

        if (isset($params['item_images'])) {
            $itemImages = $this->itemImageService->putNewPreviewFiles($params['item_images'], $item, $previewKey);

            $item->setRelation('itemImages', Collection::make(Arr::map($itemImages, function ($attributes) {
                return new \App\Models\ItemImage($attributes);
            })));
        }

        Cache::put(sprintf(Cache::KEY_ADMIN_ITEM_DETAIL_PREVIW, $previewKey), $item->toArray(), self::DATA_EXPIRATION_SEC);

        return ['key' => $previewKey, 'expires' => self::DATA_EXPIRATION_SEC];
    }

    /**
     * @param string $key
     *
     * @return array
     */
    public function fetch(string $key)
    {
        $data = Cache::get(sprintf(Cache::KEY_ADMIN_ITEM_DETAIL_PREVIW, $key));

        if (!$data) {
            throw new NotFoundHttpException();
        }

        return $data;
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function deleteOldItemImageDirectories()
    {
        $limit = Carbon::now()->timestamp - self::DATA_EXPIRATION_SEC;

        $baseDir = config('filesystems.dirs.image.item_preview');

        $disk = FileUtil::getPublicImageDisk();

        $dirs = collect($disk->directories($baseDir));

        $dirs = $dirs->filter(function ($dir) use ($limit, $baseDir) {
            return ((float) trim(str_replace($baseDir, '', $dir), '/')) < $limit;
        });

        foreach ($dirs as $dir) {
            $disk->deleteDirectory($dir);
        }

        return $dirs;
    }
}

<?php

namespace App\Services\Admin;

use App\Domain\Utils\ItemPrice;
use App\Exceptions\FileUploadException;
use App\Models\TopContent;
use App\Repositories\ItemRepository;
use App\Repositories\PlanRepository;
use App\Repositories\TopContentAdminRepository;
use App\Utils\Arr;
use App\Utils\FileUploadUtil;
use App\Utils\FileUtil;
use Exception;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpKernel\Exception\HttpException;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * @SuppressWarnings(PHPMD.ExcessiveClassComplexity)
 */
class TopContentService extends Service implements TopContentServiceInterface
{
    /**
     * @var array
     */
    private $acceptableImageContentType = [
        'image/jpeg',
        'image/png',
        'image/gif',
    ];

    /**
     * @var TopContentAdminRepository
     */
    private $topContentRepository;

    /**
     * @var ItemRepository
     */
    private $itemRepository;

    /**
     * @var PlanRepository
     */
    private $planRepository;

    /**
     * @param TopContentAdminRepository $topContentRepository
     */
    public function __construct(
        TopContentAdminRepository $topContentRepository,
        ItemRepository $itemRepository,
        PlanRepository $planRepository
    ) {
        $this->topContentRepository = $topContentRepository;
        $this->itemRepository = $itemRepository;
        $this->planRepository = $planRepository;
    }

    /**
     * ストアブランドからtop_contentを取得
     *
     * @param int|null $storeBrand
     *
     * @return TopContent
     */
    public function fetchOneByStoreBrand(int $storeBrand = null)
    {
        $topContent = $this->topContentRepository->scopeQuery(function ($query) use ($storeBrand) {
            return $storeBrand === null
                ? $query->whereNull('top_contents.store_brand')
                : $query->where('top_contents.store_brand', $storeBrand);
        })->all()->first();

        if (empty($topContent)) {
            throw new HttpException(Response::HTTP_NOT_FOUND, error_format('error.model_not_found', ['store_brand' => $storeBrand]));
        }

        $this->loadRelatedData($topContent);

        return $topContent;
    }

    /**
     * 新着商品からデータを削除
     *
     * @param int $id
     * @param int $itemId
     *
     * @return TopContent
     */
    public function deleteNewItem(int $id, int $itemId)
    {
        try {
            DB::beginTransaction();

            $topContent = $this->topContentRepository->find($id);

            $newItems = $topContent->new_items;

            $newItems = array_values(array_filter($newItems, function ($item) use ($itemId) {
                return (int) $item['item_id'] !== (int) $itemId;
            }));

            $topContent->new_items = $newItems;

            $topContent->save();

            DB::commit();

            $this->loadRelatedData($topContent);

            return $topContent;
        } catch (Exception $e) {
            DB::rollBack();
        }
    }

    /**
     * 新着商品の更新
     *
     * @param int $id
     * @param int $itemId
     * @param array $attributes
     * @param array $except
     *
     * @return TopContent
     */
    public function updateNewItem(int $id, int $itemId, array $attributes, array $except = [])
    {
        try {
            DB::beginTransaction();

            $topContent = $this->topContentRepository->find($id);

            $newItems = $topContent->new_items;

            $index = Arr::findKey($newItems, function ($item) use ($itemId) {
                return (int) $item['item_id'] === (int) $itemId;
            });

            if ($index === false) {
                throw new HttpException(Response::HTTP_NOT_FOUND, error_format('error.model_not_found', ['item_id' => $itemId]));
            }

            $targetItem = $newItems[$index];

            $newItems[$index] = array_merge($targetItem, Arr::except($attributes, $except));

            $newItems = $this->adjustItemSort($newItems, $targetItem['item_id'], $targetItem['sort'], $attributes['sort']);

            $topContent->new_items = $newItems;

            $topContent->save();

            DB::commit();

            $this->loadRelatedData($topContent);

            return $topContent;
        } catch (Exception $e) {
            DB::rollBack();
        }
    }

    /**
     * おすすめ商品からデータを削除
     *
     * @param int $id
     * @param int $itemId
     *
     * @return TopContent
     */
    public function deletePickup(int $id, int $itemId)
    {
        try {
            DB::beginTransaction();

            $topContent = $this->topContentRepository->find($id);

            $pickups = $topContent->pickups;

            $pickups = array_values(array_filter($pickups, function ($item) use ($itemId) {
                return (int) $item['item_id'] !== (int) $itemId;
            }));

            $topContent->pickups = $pickups;

            $topContent->save();

            DB::commit();

            $this->loadRelatedData($topContent);

            return $topContent;
        } catch (Exception $e) {
            DB::rollBack();
        }
    }

    /**
     * おすすめ商品の更新
     *
     * @param int $id
     * @param int $itemId
     * @param array $attributes
     * @param array $except
     *
     * @return TopContent
     */
    public function updatePickup(int $id, int $itemId, array $attributes, array $except = [])
    {
        try {
            DB::beginTransaction();

            $topContent = $this->topContentRepository->find($id);

            $pickups = $topContent->pickups;

            $index = Arr::findKey($pickups, function ($item) use ($itemId) {
                return (int) $item['item_id'] === (int) $itemId;
            });

            if ($index === false) {
                throw new HttpException(Response::HTTP_NOT_FOUND, error_format('error.model_not_found', ['item_id' => $itemId]));
            }

            $targetItem = $pickups[$index];

            $pickups[$index] = array_merge($targetItem, Arr::except($attributes, $except));

            $pickups = $this->adjustItemSort($pickups, $targetItem['item_id'], $targetItem['sort'], $attributes['sort']);

            $topContent->pickups = $pickups;

            $topContent->save();

            DB::commit();

            $this->loadRelatedData($topContent);

            return $topContent;
        } catch (Exception $e) {
            DB::rollBack();
        }
    }

    /**
     * ソートの調整
     *
     * @param array $items
     * @param int $oldSort
     * @param int $newSort
     *
     * @return array
     */
    private function adjustItemSort(array $items, int $targetItemId, $oldSort, $newSort)
    {
        $upward = $oldSort < $newSort;

        foreach ($items as &$item) {
            if ((int) $targetItemId === (int) $item['item_id']) {
                continue;
            }

            if ($upward) {
                $item['sort'] += $item['sort'] > $newSort ? 1 : -1;
            } elseif ($item['sort'] >= $newSort) {
                ++$item['sort'];
            }
        }

        usort($items, function ($item1, $item2) {
            return $item1['sort'] - $item2['sort'];
        });

        foreach ($items as $index => &$item) {
            $item['sort'] = $index + 1;
        }

        return $items;
    }

    /**
     * 特集、NEWSのソート調整
     *
     * @param array $items
     * @param int $oldSort
     * @param int $newSort
     *
     * @return array
     */
    private function adjustPlanSort(array $items, int $targetItemId, $oldSort, $newSort)
    {
        $upward = $oldSort < $newSort;

        foreach ($items as &$item) {
            if ((int) $targetItemId === (int) $item['plan_id']) {
                continue;
            }

            if ($upward) {
                $item['sort'] += $item['sort'] > $newSort ? 1 : -1;
            } elseif ($item['sort'] >= $newSort) {
                ++$item['sort'];
            }
        }

        usort($items, function ($item1, $item2) {
            return $item1['sort'] - $item2['sort'];
        });

        foreach ($items as $index => &$item) {
            $item['sort'] = $index + 1;
        }

        return $items;
    }

    /**
     * jsonデータに関連するデータを読み込む
     *
     * @param TopContent $topContent
     *
     * @return void
     */
    private function loadRelatedData(TopContent $topContent)
    {
        $this->loadNewItems($topContent);
        $this->loadPickups($topContent);
    }

    /**
     * new_itemsのデータを読み込む
     *
     * @param TopContent $topContent
     *
     * @return void
     */
    private function loadNewItems(TopContent $topContent)
    {
        $newItems = $topContent->new_items ?? [];

        if (count($newItems) === 0) {
            return;
        }

        $itemIds = array_column($newItems, 'item_id');

        $items = $this->itemRepository->scopeQuery(function ($query) use ($itemIds) {
            return $query->whereIn('items.id', $itemIds);
        })->with(['itemDetails', 'itemImages'])->all();

        foreach ($items as $key => $item) {
            $firstImage = $item->itemImages->sortBy('sort')->first();
            if ($firstImage) {
                $item['itemImages'][0]['url_m'] = $firstImage->url_m;
            }
        }

        $itemDict = Arr::reduce($items, function ($dict, $item) {
            $item->discounted_price = ItemPrice::calcDiscountedPrice($item);
            $item->ec_stock = $item->itemDetails->sum('ec_stock');
            $dict[$item->id] = $item;

            return $dict;
        }, []);

        $newItems = array_map(function ($item) use ($itemDict) {
            return array_merge($item, ['item' => $itemDict[$item['item_id']] ?? null]);
        }, $newItems);

        $topContent->new_items = $newItems;
    }

    /**
     * pickupsのデータを読み込む
     *
     * @param TopContent $topContent
     *
     * @return void
     */
    private function loadPickups(TopContent $topContent)
    {
        $pickups = $topContent->pickups ?? [];

        if (count($pickups) === 0) {
            return;
        }

        $itemIds = array_column($pickups, 'item_id');

        $items = $this->itemRepository->scopeQuery(function ($query) use ($itemIds) {
            return $query->whereIn('items.id', $itemIds);
        })->with(['itemDetails', 'itemImages'])->all();

        foreach ($items as $key => $item) {
            $firstImage = $item->itemImages->sortBy('sort')->first();
            if ($firstImage) {
                $item['itemImages'][0]['url_m'] = $firstImage->url_m;
            }
        }

        $itemDict = Arr::reduce($items, function ($dict, $item) {
            $item->discounted_price = ItemPrice::calcDiscountedPrice($item);
            $item->ec_stock = $item->itemDetails->sum('ec_stock');
            $dict[$item->id] = $item;

            return $dict;
        }, []);

        $pickups = array_map(function ($item) use ($itemDict) {
            return array_merge($item, ['item' => $itemDict[$item['item_id']] ?? null]);
        }, $pickups);

        $topContent->pickups = $pickups;
    }

    /**
     * Update
     *
     * @param array $request
     * @param int $itemId
     *
     * @return \App\Models\TopContent
     */
    public function addMainVisual(array $params, int $id)
    {
        try {
            DB::beginTransaction();

            $model = $this->topContentRepository->find($id);
            $mainVisuals = $model->main_visuals;

            $lastSort = count($mainVisuals) === 0 ? 1 : max(array_column($mainVisuals, 'sort')) + 1;

            $pcPath = $this->putNewItemImages($params['pc_path'], $id, 'pc');
            $spPath = $this->putNewItemImages($params['sp_path'], $id, 'sp');

            $saveArray = [
                'pc_path' => $pcPath,
                'sp_path' => $spPath,
                'status' => (bool) $params['status'],
                'url' => $params['url'],
                'sort' => $lastSort,
            ];
            array_push($mainVisuals, $saveArray);

            $model->main_visuals = $mainVisuals;
            $model->save();

            DB::commit();

            $this->loadRelatedData($model);

            return $model;
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function updateMainVisual(array $param, $id)
    {
        $model = $this->topContentRepository->updateWithAdjustmentSort($param, $id);

        $this->loadRelatedData($model);

        return $model;
    }

    /**
     * メインビジュアルのステータス更新
     *
     * @param int $id
     * @param int $itemId
     * @param array $attributes
     * @param array $except
     *
     * @return TopContent
     */
    public function updateStatusMainVisual(int $id, int $sort, array $attributes, array $except = [])
    {
        try {
            DB::beginTransaction();

            $topContent = $this->topContentRepository->find($id);

            $mainVisuals = $topContent->main_visuals;

            $index = Arr::findKey($mainVisuals, function ($element) use ($sort) {
                return (int) $element['sort'] === (int) $sort;
            });

            if ($index === false) {
                throw new HttpException(Response::HTTP_NOT_FOUND, error_format('error.model_not_found', ['sort' => $sort]));
            }

            $mainVisuals[$index]['status'] = $attributes['status'];

            $topContent->main_visuals = $mainVisuals;

            $topContent->save();

            DB::commit();

            $this->loadRelatedData($topContent);

            return $topContent;
        } catch (Exception $e) {
            DB::rollBack();
        }
    }

    /**
     * メインビジュアル削除
     *
     * @param int $id
     * @param array $sort
     *
     * @return void
     */
    public function deleteMainVisual(int $id, int $sort)
    {
        $removingImages = [];

        $mainViduals = $this->topContentRepository->find($id)->main_visuals;
        $index = Arr::findKey($mainViduals, function ($element) use ($sort) {
            return (int) $element['sort'] === (int) $sort;
        });
        $target = $mainViduals[$index];
        $removingImages[] = $target['pc_path'];
        $removingImages[] = $target['sp_path'];

        $this->topContentRepository->deleteWithAdjustmentSort($id, $sort);

        if (empty($removingImages)) {
            return;
        }

        foreach ($removingImages as $imageUrl) {
            FileUtil::deletePublicImage($imageUrl);
        }
    }

    /**
     * new_itemsを追加する
     *
     * @param array $params
     * @param int $id
     *
     * @return \App\Models\TopContent
     */
    public function addNewItems(array $params, int $id)
    {
        try {
            DB::beginTransaction();

            $model = $this->topContentRepository->find($id);
            $newItems = $model->new_items;

            $lastSort = count($newItems) === 0 ? 1 : max(array_column($newItems, 'sort'));

            foreach ($params['item_id'] as $itemId) {
                $newItems[] = [
                    'item_id' => $itemId,
                    'sort' => ++$lastSort,
                ];
            }

            $model->new_items = $newItems;
            $model->save();

            DB::commit();

            $this->loadRelatedData($model);

            return $model;
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * おすすめ商品を追加する
     *
     * @param array $params
     * @param int $id
     *
     * @return \App\Models\TopContent
     */
    public function addPickups(array $params, int $id)
    {
        try {
            DB::beginTransaction();

            $model = $this->topContentRepository->find($id);
            $pickups = $model->pickups;

            $lastSort = count($pickups) === 0 ? 1 : max(array_column($pickups, 'sort'));

            foreach ($params['item_id'] as $itemId) {
                $pickups[] = [
                    'item_id' => $itemId,
                    'sort' => ++$lastSort,
                ];
            }

            $model->pickups = $pickups;
            $model->save();

            DB::commit();

            $this->loadRelatedData($model);

            return $model;
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * 特集の背景色を更新する
     *
     * @param array $params
     * @param int $id
     *
     * @return \App\Models\TopContent
     */
    public function updateBackgroundColor(int $id, array $params)
    {
        try {
            DB::beginTransaction();

            $model = $this->topContentRepository->find($id);

            $model->background_color = $params['background_color'];

            $model->save();

            DB::commit();

            $this->loadRelatedData($model);

            return $model;
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * 特集を更新する
     *
     * @param array $params
     * @param int $id
     *
     * @return \App\Models\TopContent
     */
    public function updateFeatures(int $id, array $params)
    {
        try {
            DB::beginTransaction();

            $model = $this->topContentRepository->find($id);

            $features = [];
            $sort = 0;
            if (!empty($params['plan_id'])) {
                foreach ($params['plan_id'] as $planId) {
                    array_push($features, [
                        'plan_id' => $planId,
                        'sort' => ++$sort,
                    ]);
                }
            }
            $model->features = $features;

            $model->save();

            DB::commit();

            $this->loadRelatedData($model);

            return $model;
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * 特集のソート更新
     *
     * @param int $id
     * @param int $itemId
     * @param array $attributes
     * @param array $except
     *
     * @return TopContent
     */
    public function updateSortFeatures(int $id, int $planId, array $attributes, array $except = [])
    {
        try {
            DB::beginTransaction();

            $topContent = $this->topContentRepository->find($id);

            $features = $topContent->features;

            $index = Arr::findKey($features, function ($plan) use ($planId) {
                return (int) $plan['plan_id'] === (int) $planId;
            });

            if ($index === false) {
                throw new HttpException(Response::HTTP_NOT_FOUND, error_format('error.model_not_found', ['plan_id' => $planId]));
            }

            $targetItem = $features[$index];

            $features[$index] = array_merge($targetItem, Arr::except($attributes, $except));

            $features = $this->adjustPlanSort($features, $targetItem['plan_id'], $targetItem['sort'], $attributes['sort']);

            $topContent->features = $features;

            $topContent->save();

            DB::commit();

            $this->loadRelatedData($topContent);

            return $topContent;
        } catch (Exception $e) {
            DB::rollBack();
        }
    }

    /**
     * NEWSを更新する
     *
     * @param array $params
     * @param int $id
     *
     * @return \App\Models\TopContent
     */
    public function updateNews(int $id, array $params)
    {
        try {
            DB::beginTransaction();

            $model = $this->topContentRepository->find($id);

            $news = [];
            $sort = 0;
            if (!empty($params['plan_id'])) {
                foreach ($params['plan_id'] as $planId) {
                    array_push($news, [
                        'plan_id' => $planId,
                        'sort' => ++$sort,
                    ]);
                }
            }

            $model->news = $news;

            $model->save();

            DB::commit();

            $this->loadRelatedData($model);

            return $model;
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * NEWSのソート更新
     *
     * @param int $id
     * @param int $itemId
     * @param array $attributes
     * @param array $except
     *
     * @return TopContent
     */
    public function updateSortNews(int $id, int $planId, array $attributes, array $except = [])
    {
        try {
            DB::beginTransaction();

            $topContent = $this->topContentRepository->find($id);

            $news = $topContent->news;

            $index = Arr::findKey($news, function ($plan) use ($planId) {
                return (int) $plan['plan_id'] === (int) $planId;
            });

            if ($index === false) {
                throw new HttpException(Response::HTTP_NOT_FOUND, error_format('error.model_not_found', ['plan_id' => $planId]));
            }

            $targetItem = $news[$index];

            $news[$index] = array_merge($targetItem, Arr::except($attributes, $except));

            $news = $this->adjustPlanSort($news, $targetItem['plan_id'], $targetItem['sort'], $attributes['sort']);

            $topContent->news = $news;

            $topContent->save();

            DB::commit();

            $this->loadRelatedData($topContent);

            return $topContent;
        } catch (Exception $e) {
            DB::rollBack();
        }
    }

    /**
     * @param array $itemImgaes
     * @param int $itemId
     *
     * @return array
     */
    private function putNewItemImages(array $image, int $id, string $type)
    {
        list($content, $contentType) = FileUploadUtil::extractContentBase64($image['raw_image']);

        if (!in_array($contentType, $this->acceptableImageContentType)) {
            throw new FileUploadException(error_format('error.invalid_content_type'));
        }

        $filePath = FileUploadUtil::generateNewImageFilePath(
            sprintf('%s/%s/%s/%s', config('filesystems.dirs.image.top_content_main_visual'), $type, $id, date('YmdHis')),
            $image['file_name'],
            $contentType
        );

        $url = FileUtil::putPublicImage($filePath, $content);

        return $url;
    }

    /**
     * @return $deleteCount
     */
    public function deleteExpiredFeatures()
    {
        $deleteCount = 0;
        $topContents = $this->topContentRepository->all();
        foreach ($topContents as $topContent) {
            $hasExpired = false;
            $planIds = array_column($topContent->features, 'plan_id');
            foreach ($planIds as $planId) {
                if (count($this->planRepository->where('plans.id', $planId)->active()->get()) === 0) {
                    ++$deleteCount;
                    $hasExpired = true;
                    $planIds = array_diff($planIds, [$planId]);
                }
            }
            if ($hasExpired) {
                $this->updateFeatures($topContent->id, ['plan_id' => $planIds]);
            }
        }

        return $deleteCount;
    }

    /**
     * @return $deleteCount
     */
    public function deleteExpiredNews()
    {
        $deleteCount = 0;
        $topContents = $this->topContentRepository->all();
        foreach ($topContents as $topContent) {
            $hasExpired = false;
            $planIds = array_column($topContent->news, 'plan_id');
            foreach ($planIds as $planId) {
                if (count($this->planRepository->where('plans.id', $planId)->active()->get()) === 0) {
                    ++$deleteCount;
                    $hasExpired = true;
                    $planIds = array_diff($planIds, [$planId]);
                }
            }
            if ($hasExpired) {
                $this->updateNews($topContent->id, ['plan_id' => $planIds]);
            }
        }

        return $deleteCount;
    }
}

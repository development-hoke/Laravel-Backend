<?php

namespace App\Services\Admin;

use App\Exceptions\FileUploadException;
use App\Repositories\PlanItemRepository;
use App\Repositories\PlanRepository;
use App\Utils\Csv\ExportCsvInterface;
use App\Utils\Csv\ImportCsvInterface;
use App\Utils\FileUploadUtil;
use App\Utils\FileUtil;
use Exception;
use Illuminate\Http\Response;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpKernel\Exception\HttpException;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class PlanService extends Service implements PlanServiceInterface
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
     * @var \App\Repositories\PlanRepository
     */
    private $planRepository;

    /**
     * @var \App\Repositories\PlanItemRepository
     */
    private $planItemRepository;

    /**
     * @var \App\Utils\Csv\ImportCsvInterface
     */
    private $importCsvUtil;

    /**
     * @var \App\Utils\Csv\ExportCsvInterface
     */
    private $exportCsvUtil;

    /**
     * @param PlanRepository $planRepository
     * @param \App\Utils\Csv\ImportCsvInterface $importCsvUtil
     * @param \App\Utils\Csv\ExportCsvInterface $exportCsvUtil
     */
    public function __construct(
        PlanRepository $planRepository,
        PlanItemRepository $planItemRepository,
        ImportCsvInterface $importCsvUtil,
        ExportCsvInterface $exportCsvUtil
    ) {
        $this->planRepository = $planRepository;
        $this->planItemRepository = $planItemRepository;
        $this->importCsvUtil = $importCsvUtil;
        $this->exportCsvUtil = $exportCsvUtil;
    }

    /**
     * Update
     *
     * @param array $params
     *
     * @return \App\Models\Plan
     */
    public function create(array $params)
    {
        try {
            DB::beginTransaction();

            $attributes = Arr::except($params, ['thumbnail']);

            $attributes['thumbnail'] = $this->putNewThumbnail($params['thumbnail']);

            $plan = $this->planRepository->create($attributes);

            DB::commit();

            return $plan;
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Update
     *
     * @param array $request
     * @param int $planId
     *
     * @return \App\Models\Plan
     */
    public function update(array $params, int $planId)
    {
        try {
            DB::beginTransaction();

            $plan = $this->planRepository->find($planId);

            if ($plan->store_brand !== $params['store_brand']) {
                $this->planItemRepository->where('plan_id', $planId)->delete();
            }

            $attributes = Arr::except($params, ['thumbnail']);

            if (!empty($params['thumbnail'])) {
                $oldThumbnailUrl = $this->planRepository->find($planId)->thumbnail;

                $attributes['thumbnail'] = $this->putNewThumbnail($params['thumbnail']);
            }
            $plan = $this->planRepository->update($attributes, $planId);

            if (!empty($params['thumbnail'])) {
                FileUtil::deletePublicImage($oldThumbnailUrl);
            }

            DB::commit();

            return $plan;
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Delete
     *
     * @param int $planId
     *
     * @return \App\Models\Plan
     */
    public function delete(int $planId)
    {
        try {
            DB::beginTransaction();

            $plan = $this->planRepository->find($planId);

            FileUtil::deletePublicImage($plan->thumbnail);

            $this->planRepository->delete($planId);

            DB::commit();

            return;
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * @param int $planId
     *
     * @return array
     */
    public function copy(int $planId)
    {
        try {
            DB::beginTransaction();

            $originalPlan = $this->planRepository->find($planId);

            $count = $this->planRepository->withTrashed()->count();

            $plan = $this->planRepository->copy(
                $planId,
                [],
                [
                    'status' => \App\Enums\Common\Status::Unpublished,
                    'slug' => $originalPlan->slug . '_' . ($count + 1),
                ]
            );

            DB::commit();

            return $plan;
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * @param array $itemImgaes
     * @param int $itemId
     *
     * @return string
     */
    private function putNewThumbnail(array $thumbnail)
    {
        list($content, $contentType) = FileUploadUtil::extractContentBase64($thumbnail['raw_image']);

        if (!in_array($contentType, $this->acceptableImageContentType)) {
            throw new FileUploadException(error_format('error.invalid_content_type'));
        }

        $filePath = FileUploadUtil::generateNewImageFilePath(
            sprintf('%s/%s/', config('filesystems.dirs.image.plan_thumb'), date('YmdHis')),
            $thumbnail['file_name'],
            $contentType
        );

        $url = FileUtil::putPublicImage($filePath, $content);

        return $url;
    }

    /**
     * 一覧商品からデータを削除
     *
     * @param int $id
     * @param int $itemId
     *
     * @return TopContent
     */
    public function deleteItem(int $id, int $itemId)
    {
        try {
            DB::beginTransaction();

            $planItem = $this->planItemRepository->where('plan_id', $id)->where('item_id', $itemId)->first();

            $planItem->delete();

            $plan = $this->planRepository->find($id);

            DB::commit();

            return $plan;
        } catch (Exception $e) {
            DB::rollBack();
        }
    }

    /**
     * 商品一覧表示設定変更
     *
     * @param array $request
     * @param int $planId
     *
     * @return \App\Models\Plan
     */
    public function updateItemSetting(array $params, int $planId)
    {
        try {
            DB::beginTransaction();

            $this->planRepository->update($params, $planId);

            $plan = $this->planRepository->find($planId);

            DB::commit();

            return $plan;
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * 商品を追加する
     *
     * @param array $params
     * @param int $planId
     *
     * @return \App\Models\Plan
     */
    public function addNewItems(array $params, int $planId)
    {
        try {
            DB::beginTransaction();

            foreach ($params['item_id'] as $itemId) {
                $columns = [
                    'item_id' => $itemId,
                    'plan_id' => $planId,
                ];
                $this->planItemRepository->create($columns);
            }

            $plan = $this->planRepository->find($planId);

            DB::commit();

            return $plan;
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * 指定されたストアブランドの企画管理を取得
     *
     * @param int|null $storeBrand
     *
     * @return \App\Models\Plan
     */
    public function fetchByStoreBrand(int $storeBrand = null)
    {
        $plans = $this->planRepository->scopeQuery(function ($query) use ($storeBrand) {
            return $storeBrand === null
               ? $query
               : $query->where('plans.store_brand', $storeBrand);
        })->where('status', true)->active()->get();

        if (empty($plans)) {
            throw new HttpException(Response::HTTP_NOT_FOUND, error_format('error.model_not_found', ['store_brand' => $storeBrand]));
        }

        return $plans;
    }
}

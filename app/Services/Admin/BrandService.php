<?php

namespace App\Services\Admin;

use App\Models\Brand;
use App\Repositories\BrandRepository;
use Exception;
use Illuminate\Support\Facades\DB;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * @SuppressWarnings(PHPMD.ExcessiveClassComplexity)
 */
class BrandService extends Service implements BrandServiceInterface
{
    /**
     * @var BrandRepository
     */
    private $brandRepository;

    /**
     * @param BrandRepository $brandRepository
     */
    public function __construct(
        BrandRepository $brandRepository
    ) {
        $this->brandRepository = $brandRepository;
    }

    /**
     * 新着商品の更新
     *
     * @param int $id
     * @param int $itemId
     * @param array $attributes
     * @param array $except
     *
     * @return Brand
     */
    public function updateSort(int $id, array $params)
    {
        try {
            DB::beginTransaction();

            $brands = $this->brandRepository->orderBy('sort', 'ASC')->get();

            $brands = $this->resetBrandSort($brands);

            $target = $this->brandRepository->find($id);
            $oldIndex = $target->sort;
            $newIndex = $params['sort'];

            $upward = $oldIndex < $newIndex;

            foreach ($brands as $key => $brand) {
                if ($upward) {
                    if ($brand->sort > $newIndex) {
                        continue;
                    }
                    if ($brand->sort < $oldIndex) {
                        continue;
                    }

                    if ($brand->id === $id) {
                        $brand->sort = $newIndex;
                    } else {
                        --$brand->sort;
                    }
                } else {
                    if ($brand->sort > $oldIndex) {
                        continue;
                    }
                    if ($brand->sort < $newIndex) {
                        continue;
                    }

                    if ($brand->id === $id) {
                        $brand->sort = $newIndex;
                    } else {
                        ++$brand->sort;
                    }
                }
                $brand->save();
            }

            DB::commit();

            return $brands;
        } catch (Exception $e) {
            DB::rollBack();
        }
    }

    /**
     * @param collection $brands
     *
     * @return collection
     */
    private function resetBrandSort($brands)
    {
        foreach ($brands as $key => $brand) {
            $brand->sort = $key + 1;
        }

        return $brands;
    }
}

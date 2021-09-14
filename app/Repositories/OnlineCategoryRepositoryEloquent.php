<?php

namespace App\Repositories;

use App\Enums\OnlineCategory\EndValue as OnlineCategoryEndValue;
use App\Exceptions\InvalidNestedSetAttributesException;
use App\Models\OnlineCategory;
use App\Repositories\Traits\HavingSortTrait;
use Exception;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

/**
 * Class OnlineCategoryRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 *
 * @SuppressWarnings(PHPMD.CyclomaticComplexity)
 * @SuppressWarnings(PHPMD.NPathComplexity)
 */
class OnlineCategoryRepositoryEloquent extends BaseRepositoryEloquent implements OnlineCategoryRepository
{
    use HavingSortTrait;

    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return OnlineCategory::class;
    }

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
    }

    /**
     * @param array $attributes
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function create(array $attributes)
    {
        try {
            DB::beginTransaction();

            $this->model->lockForUpdate()->get();
            $this->resetModel();

            if (!isset($attributes['sort'])) {
                $attributes['sort'] = $this->model->max('sort') + 1;
                $this->resetModel();
            }

            $model = $this->model->newInstance();
            $this->resetModel();

            $attributes = $this->mergeNewRootIdAndLevelToAttributes($attributes['id'], $attributes);

            $model->id = $attributes['id'];
            $model->fill(Arr::except($attributes, ['id']));
            $model->save();

            $this->validateTreeStructure($model);

            $this->resetSort($model);

            $model = $this->model->get();
            $this->resetModel();

            DB::commit();

            return $model;
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * @param array $attributes
     * @param mix $id
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function update(array $attributes, $id)
    {
        try {
            DB::beginTransaction();

            $this->model->lockForUpdate()->get();
            $this->resetModel();

            $model = $this->model->findOrFail($id);
            $this->resetModel();

            $moving = $this->isSpecifiedParentId($attributes)
                && (int) $model->parent_id !== (int) $attributes['parent_id'];

            $moving && $attributes = $this->mergeNewRootIdAndLevelToAttributes($model->id, $attributes);

            $model->fill($attributes);
            $model->save();

            $moving && $this->updateDescendants($model);

            $this->validateTreeStructure($model);

            if (isset($attributes['sort'])) {
                $this->resetSort($model);
            }

            $model = $this->model->get();
            $this->resetModel();

            DB::commit();

            return $model;
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * @param $id
     *
     * @return bool|null
     */
    public function delete($id)
    {
        try {
            DB::beginTransaction();

            $model = $this->find($id);
            $this->resetModel();

            foreach ($model->descendants()->orderBy('level', 'desc')->get() as $descendant) {
                $descendant->delete();
            }

            $deleted = $model->delete();

            DB::commit();

            return $deleted;
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * @param array $attributes
     *
     * @return bool
     */
    private function isSpecifiedParentId(array $attributes)
    {
        return array_key_exists('parent_id', $attributes);
    }

    /**
     * @param int $id
     * @param array $attributes
     *
     * @return void
     */
    private function mergeNewRootIdAndLevelToAttributes(int $id, array $attributes)
    {
        if (!isset($attributes['parent_id'])) {
            return array_merge($attributes, [
                'parent_id' => null,
                'root_id' => $id,
                'level' => OnlineCategoryEndValue::LowestLevel,
            ]);
        }

        $parent = $this->model->findOrFail($attributes['parent_id']);
        $this->resetModel();

        return array_merge($attributes, [
            'root_id' => $parent->root_id,
            'level' => $parent->level + 1,
        ]);
    }

    /**
     * @param \Illuminate\Database\Eloquent\Model|\Illuminate\Database\Eloquent\Builder $model
     *
     * @return void
     */
    private function updateDescendants($model)
    {
        foreach ($model->descendants()->orderBy('level', 'asc')->get() as $index => $descendant) {
            $descendant->level = $model->level + $index + 1;
            $descendant->root_id = $model->root_id;
            $descendant->save();
        }
    }

    /**
     * level, root_idなどの整合性を検証する。
     *
     * @param \Illuminate\Database\Eloquent\Model|\Illuminate\Database\Eloquent\Builder $model
     *
     * @return bool
     *
     * @throws InvalidNestedSetAttributesException
     */
    private function validateTreeStructure($model)
    {
        if ($model->level < OnlineCategoryEndValue::LowestLevel || $model->level > OnlineCategoryEndValue::HighestLevel) {
            throw new InvalidNestedSetAttributesException(__('validation.between.numeric', [
                'attribute' => __('validation.attributes.online_category'),
                'min' => OnlineCategoryEndValue::LowestLevel,
                'max' => OnlineCategoryEndValue::HighestLevel,
            ]));
        }

        foreach ($model->descendants()->get() as $descendant) {
            if ($descendant->level > OnlineCategoryEndValue::HighestLevel) {
                throw new InvalidNestedSetAttributesException(error_format('error.exceeded_maximum_descendant_level', [
                    'expected_value' => $descendant->level,
                ]));
            }
            if ($model->level >= $descendant->level) {
                throw new InvalidNestedSetAttributesException(error_format('error.inconsistent_level_with_descendants', [
                    'expected_value' => 'less than '.$descendant->level,
                    'actual_value' => $model->level,
                ]));
            }
        }

        $ancestors = $model->ancestors()->orderBy('level', 'desc')->get();

        if (count($ancestors) === 0) {
            if ($model->level !== OnlineCategoryEndValue::LowestLevel) {
                throw new InvalidNestedSetAttributesException(error_format('error.inconsistent_level_as_root', [
                    'expected_value' => OnlineCategoryEndValue::LowestLevel,
                    'actual_value' => $model->level,
                ]));
            }
            if ((int) $model->root_id !== (int) $model->id) {
                throw new InvalidNestedSetAttributesException(error_format('error.inconsistent_root_id_as_root', [
                    'expected_value' => $model->root_id,
                    'actual_value' => $model->id,
                ]));
            }
        }

        foreach ($ancestors as $index => $ancestor) {
            if (($model->level - $index - 1) !== (int) $ancestor->level) {
                throw new InvalidNestedSetAttributesException(error_format('error.inconsistent_level_with_ancestors', [
                    'expected_value' => $ancestor->level,
                    'actual_value' => $model->level - $index - 1,
                ]));
            }
            if ((int) $ancestor->level === OnlineCategoryEndValue::LowestLevel &&
                (int) $ancestor->id !== (int) $model->root_id
            ) {
                throw new InvalidNestedSetAttributesException(error_format('error.inconsistent_root_id', [
                    'expected_value' => $ancestor->id,
                    'actual_value' => $model->root_id,
                ]));
            }
        }

        return true;
    }
}

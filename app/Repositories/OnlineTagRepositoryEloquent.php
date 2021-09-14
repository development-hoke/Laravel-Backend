<?php

namespace App\Repositories;

use App\Exceptions\InvalidInputException;
use App\Models\OnlineTag;
use App\Repositories\Traits\HavingSortTrait;
use Exception;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

/**
 * Class OnlineTagRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class OnlineTagRepositoryEloquent extends BaseRepositoryEloquent implements OnlineTagRepository
{
    use HavingSortTrait;

    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return OnlineTag::class;
    }

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
    }

    /**
     * Save a new entity in repository
     *
     * @throws ValidatorException
     *
     * @param array $attributes
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function create(array $attributes)
    {
        try {
            DB::beginTransaction();

            if (!isset($attributes['sort'])) {
                $attributes['sort'] = $this->model->max('sort') + 1;
                $this->resetModel();
            }

            $this->model->lockForUpdate()->get();
            $this->resetModel();

            $model = $this->model->newInstance(Arr::except($attributes, ['id']));
            $model->id = $attributes['id'];

            $this->validateHierarchy($model);

            $model->save();
            $this->resetModel();

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
     * Update a entity in repository by id
     *
     * @throws ValidatorException
     *
     * @param array $attributes
     * @param $id
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function update(array $attributes, $id)
    {
        try {
            DB::beginTransaction();

            $this->applyScope();

            $this->model->lockForUpdate()->get();
            $this->resetModel();

            $model = $this->model->findOrFail($id);
            $this->resetModel();
            $model->fill(Arr::except($attributes, ['id']));

            $this->validateHierarchy($model);

            $model->save();

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
     * Delete a entity in repository by id
     *
     * @param $id
     *
     * @return int
     */
    public function delete($id)
    {
        try {
            DB::beginTransaction();

            $this->applyScope();

            $model = $this->find($id);

            foreach ($model->children()->get() as $child) {
                $child->delete();
            }

            $this->resetModel();

            $deleted = $model->delete();

            DB::commit();

            return $deleted;
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * @param Illuminate\Database\Eloquent\Model $model
     *
     * @return bool
     */
    private function validateHierarchy($model)
    {
        if ($model->id === $model->parent_id) {
            throw new InvalidInputException(error_format(
                'error.parent_id_must_not_be_same_value_with_id',
                ['parent_id' => $model->parent_id, 'id' => $model->id]
            ));
        }

        if (empty($model->parent_id)) {
            return true;
        }

        if (!empty($model->parent->parent_id)) {
            throw new InvalidInputException(error_format(
                'error.only_secound_levels_can_be_used',
                ['parent_id' => $model->parent_id, 'id' => $model->id],
                ['table' => $model->getTable()]
            ));
        }

        return true;
    }
}

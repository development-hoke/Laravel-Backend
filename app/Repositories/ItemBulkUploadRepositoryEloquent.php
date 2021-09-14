<?php

namespace App\Repositories;

use App\Models\ItemBulkUpload;
use Prettus\Repository\Eloquent\BaseRepository;

/**
 * Class ItemBulkUploadRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class ItemBulkUploadRepositoryEloquent extends BaseRepository implements ItemBulkUploadRepository
{
    const MAXIMUM_ITEM_BULK_UPLOAD_COUNT = 50;

    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return ItemBulkUpload::class;
    }

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
    }

    /**
     * 余分な古いデータを削除する
     *
     * @return void
     */
    public function clearOldRows()
    {
        $models = $this->model->orderBy('id', 'asc')->get();

        $this->resetModel();

        if (($oldRowCount = count($models) - self::MAXIMUM_ITEM_BULK_UPLOAD_COUNT) > 0) {
            $this->model->orderBy('id', 'asc')->limit($oldRowCount)->delete();
        }
    }
}

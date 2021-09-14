<?php

namespace App\Models\Traits;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

/**
 * Bulk Insertの提供
 */
trait BulkInsertTrait
{
    /**
     * @param Collection|Model[] $models
     *
     * @return bool
     */
    public static function bulkInsert(Collection $models): bool
    {
        if ($models->isEmpty()) {
            return true;
        }

        // before save
        foreach ($models as $model) {
            if ($model->usesTimestamps()) {
                $time = $model->freshTimestamp();

                if (static::UPDATED_AT !== null && !$model->isDirty(static::UPDATED_AT)) {
                    $model->setUpdatedAt($time);
                }

                if (static::CREATED_AT !== null && !$model->isDirty(static::CREATED_AT)) {
                    $model->setCreatedAt($time);
                }
            }
        }

        // perform insert
        $attributesArray = static::convertModelsToArray($models);
        $saved = (new static())->newQueryWithoutScopes()->insert($attributesArray);
        if (!$saved) {
            return false;
        }

        return true;
    }

    /**
     * create array of model attributes
     *
     * @param Collection|Model[] $models
     *
     * @return array
     */
    private static function convertModelsToArray(Collection $models): array
    {
        $columns = array_diff(\Schema::getColumnListing($models->first()->getTable()), ['id']);

        return $models->map(function ($model) use ($columns) {
            $attributes = $model->getAttributes();
            foreach ($columns as $column) {
                if (!array_key_exists($column, $attributes)) {
                    $attributes[$column] = null;
                }
            }

            return $attributes;
        })
        ->toArray();
    }
}

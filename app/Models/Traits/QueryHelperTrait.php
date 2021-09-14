<?php

namespace App\Models\Traits;

use Illuminate\Database\Eloquent\Builder;

trait QueryHelperTrait
{
    /**
     * 任意の条件を追加する
     *
     * $conditionsの定義
     * - タイプ1: ['field', 'operator', 'value'] e.g. where field >= 3
     * - タイプ2: ['field', 'is null|is not null'] e.g. where field is null
     * - タイプ3: ['field', 'value'] e.g. where field = 'value'
     *
     * @param Builder $query
     * @param array $conditions
     *
     * @return Builder
     */
    public function applyConditions($query, array $conditions)
    {
        foreach ($conditions as $where) {
            if (count($where) === 2) {
                [$field, $value] = $where;

                if (is_array($value)) {
                    $query = $query->whereIn($field, $value);
                } elseif (($expression = strtolower((string) $value)) === 'is null') {
                    $query = $query->whereNull($field);
                } elseif ($expression === 'is not null') {
                    $query = $query->whereNotNull($field);
                } else {
                    $query = $query->where($field, $value);
                }
            } else {
                [$field, $operator, $value] = $where;

                $query = $query->where($field, $operator, $value);
            }
        }

        return $query;
    }
}

<?php

namespace App\Database\Utils;

use Illuminate\Database\Query\Expression;
use Illuminate\Support\Facades\DB;

class Query
{
    /**
     * エイリアスの除去
     *
     * @param array $columns
     *
     * @return array
     */
    public static function removeSelectAliases(array $columns)
    {
        return array_map(function ($column) {
            $raw = $column instanceof Expression;

            if ($raw) {
                $column = $column->getValue();
            }

            $column = is_string($column) && ($aliasPosition = stripos($column, ' as ')) !== false
                ? substr($column, 0, $aliasPosition)
                : $column;

            return $raw ? DB::raw($column) : $column;
        }, $columns);
    }

    /**
     * エイリアスがあった場合、エイリアスのみを取り出す
     *
     * @param array $columns
     *
     * @return array
     */
    public static function extractSelectAliasesIfHave(array $columns)
    {
        return array_map(function ($column) {
            $raw = $column instanceof Expression;

            if ($raw) {
                $column = $column->getValue();
            }

            $column = is_string($column) && ($aliasPosition = stripos($column, ' as ')) !== false
                ? substr($column, $aliasPosition + 4)
                : $column;

            $column = trim($column);

            return $raw ? DB::raw($column) : ($column);
        }, $columns);
    }

    /**
     * joinしているか確かめる
     *
     * @param string $table
     *
     * @return bool
     */
    public static function joined($table, $query = null)
    {
        $joins = $query->getQuery()->joins;

        if ($joins == null) {
            return false;
        }

        foreach ($joins as $join) {
            if ($join->table == $table) {
                return true;
            }
        }

        return false;
    }
}

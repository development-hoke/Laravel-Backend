<?php

namespace App\Validation\Rules;

use Closure;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

/**
 * ユニークかどうかを検証する。
 * （LaravelのRule::uniqueではjoinが使えなかったので定義）
 */
class Unique implements Rule
{
    /**
     * @var \Illuminate\Database\Query\Builder|\Illuminate\Database\Eloquent\Builder
     */
    private $query;

    /**
     * @var array
     */
    private $conditions = [];

    /**
     * @param string $table
     *
     * @return Unique
     */
    public static function newInstance(string $table)
    {
        return new static($table);
    }

    /**
     * @param string $table
     *
     * @return void
     */
    public function __construct(string $table)
    {
        if (class_exists($table) && is_subclass_of($table, Model::class)) {
            $this->query = $table::query();
        } else {
            $this->query = DB::table($table);
        }
    }

    /**
     * @param Closure $closure
     *
     * @return Unique
     */
    public function where(Closure $closure)
    {
        $this->conditions[] = $closure;

        return $this;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param string $attribute
     * @param mixed $value
     *
     * @return bool
     */
    public function passes($attribute, $value)
    {
        $query = $this->query;

        foreach ($this->conditions as $condition) {
            $query = $condition($query, $value);
        }

        $row = $query->first();

        return empty($row);
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return __('validation.unique');
    }
}

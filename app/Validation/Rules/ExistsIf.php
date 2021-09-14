<?php

namespace App\Validation\Rules;

use Closure;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

/**
 * 特定の条件下で存在の検証をする
 */
class ExistsIf implements Rule
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
     * @var Closure
     */
    private $shouldValidate;

    /**
     * @param string $table
     * @param Closure $shouldValidate 存在の検証をする条件をクロージャで指定する
     *
     * @return ExistsIf
     */
    public static function newInstance(string $table, Closure $shouldValidate)
    {
        return new static($table, $shouldValidate);
    }

    /**
     * @param string $table
     * @param Closure $shouldValidate 存在の検証をする条件をクロージャで指定する
     *
     * @return void
     */
    public function __construct(string $table, Closure $shouldValidate)
    {
        if (class_exists($table) && is_subclass_of($table, Model::class)) {
            $this->query = $table::query();
        } else {
            $this->query = DB::table($table);
        }

        $this->shouldValidate = $shouldValidate;
    }

    /**
     * @param Closure $closure
     *
     * @return ExistsIf
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
        $shouldValidate = $this->shouldValidate;

        if (!$shouldValidate()) {
            return true;
        }

        $query = $this->query;

        foreach ($this->conditions as $condition) {
            $query = $condition($query, $value);
        }

        $row = $query->first();

        return !empty($row);
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return __('validation.exists');
    }
}

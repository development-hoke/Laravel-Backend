<?php

namespace App\Validation\Rules;

use App\Exceptions\FatalException;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\DB;

abstract class ComparedWithTable implements Rule
{
    protected $comparedValue;

    protected $fieldName;

    /**
     * @param string $table
     * @param string $field
     * @param mix $id
     * @param string $fieldName
     */
    public function __construct(string $table, string $field, $condition, string $fieldName = null)
    {
        $query = DB::table($table);

        if (is_array($condition)) {
            $query = $query->where($condition);
        } else {
            $query = $query->where('id', $condition);
        }

        $comparedRow = $query->first();

        if (!isset($comparedRow->{$field})) {
            throw new FatalException(error_format('error.resource_not_found', $condition));
        }

        $this->comparedValue = $comparedRow->{$field};

        $this->fieldName = $fieldName ?: $field;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param string $attribute
     * @param mixed $value
     *
     * @return bool
     */
    abstract public function passes($attribute, $value);

    /**
     * Get the validation error message.
     *
     * @return string
     */
    abstract public function message();
}

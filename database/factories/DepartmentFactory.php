<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\Department;
use Faker\Generator as Faker;

$factory->define(Department::class, function (Faker $faker) {
    $departmentGroup = $faker->randomElement(\App\Models\DepartmentGroup::all());

    return [
        'name' => $faker->word,
        'code' => $faker->lexify('??????????'),
        'short_name' => $faker->word,
        'sign' => $faker->lexify('??????????'),
        'department_group_id' => $departmentGroup->id,
    ];
});

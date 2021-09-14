<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\ItemImage;
use Faker\Generator as Faker;

$factory->define(ItemImage::class, function (Faker $faker) {
    return [
        'item_id' => $faker->randomElement(\App\Models\Item::all()->pluck('id')),
        'type' => $faker->randomElement(\App\Enums\ItemImage\Type::getValues()),
        'url' => 'https://d2j51srls0nofr.cloudfront.net/items/01131234/1/001202033-m-02-dl.jpg',
        'file_name' => $faker->word,
        'caption' => $faker->word,
        'color_id' => $faker->optional(0.9)->randomElement(\App\Models\Color::all()->pluck('id')),
        'sort' => $faker->numberBetween(0, 100),
    ];
});

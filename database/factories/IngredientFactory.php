<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Ingredient;
use Faker\Generator as Faker;
use Illuminate\Support\Facades\Config;

$factory->define(Ingredient::class, function (Faker $faker) {
    return [
        'name' => $faker->name,
        'description' => $faker->sentence(
            $nbWords = 10,
            $variableNbWords = true
        ),
        'in_stock' => $faker->boolean($chanceOfGettingTrue = 50),
        'stock_qty' => $faker->numberBetween($min = 0, $max = 500),
        'measure' => $faker->randomElement(
            $array = Config::get('constants.ingredient_measure')
        ),
        'supplier_id' => $faker->numberBetween($min = 1, $max = 5),
        'created_at' => now(),
        'updated_at' => now(),
    ];
});

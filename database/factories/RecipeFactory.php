<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Recipe;
use Faker\Generator as Faker;

$factory->define(Recipe::class, function (Faker $faker) {
    return [
        'name' => $faker->unique()->name,
        'description' => $faker->sentence(
            $nbWords = 10,
            $variableNbWords = true
        ),
    ];
});

<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\RecipeIngredient;
use Faker\Generator as Faker;

$factory->define(RecipeIngredient::class, function (Faker $faker) {
    return [
        'recipe_id' => 1, // override these values from the test case
        'ingredient_id' => 1, // override these values from the test case
    ];
});

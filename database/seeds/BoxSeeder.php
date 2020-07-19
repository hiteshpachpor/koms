<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Config;
use App\UserAddress;
use App\Recipe;

class BoxSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Create a Faker instance
        $faker = \Faker\Factory::create();

        // Generate an array of integers from 1 to 20
        // This array will be used while adding recipes to a box
        $allRecipeIds = range(1, 20);

        // Seed 10 boxes for 10 users
        // Use `UserAddress` model to get combos of user & address ids
        for ($i = 1; $i <= 10; $i++) {
            $userAddress = UserAddress::find($i);

            // Seed the box
            $boxId = DB::table('box_order')->insertGetId([
                'user_id' => $userAddress->user_id,
                'user_address_id' => $userAddress->id,
                'delivery_date' => $faker->dateTimeBetween(
                    $startDate = '-7 days',
                    $endDate = '+7 days',
                    $timezone = null
                ),
                'delivery_slot' => $faker->randomElement(
                    $array = Config::get('constants.box_order_delivery_slot')
                ),
                'delivery_notes' => $faker->sentence(
                    $nbWords = $faker->numberBetween($min = 0, $max = 8),
                    $variableNbWords = true
                ),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Choose anywhere between 1 & 4 recipes to be added to this box
            $recipeIds = $faker->randomElements(
                $array = $allRecipeIds,
                $count = $faker->numberBetween($min = 1, $max = 4)
            );

            // Seed recipes for this box
            foreach ($recipeIds as $recipeId) {
                $recipe = Recipe::with('ingredientList.ingredient')->find(
                    $recipeId
                );
                $ingredients = $recipe->ingredientList;

                foreach ($ingredients as $ingredient) {
                    $record = [
                        'box_order_id' => $boxId,
                        'recipe_id' => $recipeId,
                        'recipe_name' => $recipe->name,
                        'ingredient_id' => $ingredient->ingredient->id,
                        'ingredient_name' => $ingredient->ingredient->name,
                        'ingredient_measure' =>
                            $ingredient->ingredient->measure,
                        'ingredient_amount' => $ingredient->amount,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];

                    DB::table('box_order_recipe')->insert($record);
                }
            }
        }
    }
}

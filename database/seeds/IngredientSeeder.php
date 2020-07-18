<?php

use Illuminate\Database\Seeder;

class IngredientSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Use the Faker generator for Food and Beverage names
        $faker = \Faker\Factory::create();
        $faker->addProvider(
            new \FakerRestaurant\Provider\en_US\Restaurant($faker)
        );

        // Create a variety of ingredients
        $ingredientNames = [];

        for ($i = 0; $i < 10; $i++) {
            $ingredientNames[] = $faker->vegetableName();
            $ingredientNames[] = $faker->fruitName();
            $ingredientNames[] = $faker->meatName();
            $ingredientNames[] = $faker->sauceName();
        }

        // Seed
        foreach ($ingredientNames as $ingredientName) {
            DB::table('ingredient')->insert([
                'name' => $ingredientName,
                'description' => $faker->sentence(
                    $nbWords = 10,
                    $variableNbWords = true
                ),
                'in_stock' => $faker->boolean($chanceOfGettingTrue = 90),
                'stock_qty' => $faker->numberBetween($min = 0, $max = 500),
                'measure' => $faker->randomElement(
                    $array = ['g', 'kg', 'pieces']
                ),
                'supplier_id' => $faker->numberBetween($min = 1, $max = 5),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}

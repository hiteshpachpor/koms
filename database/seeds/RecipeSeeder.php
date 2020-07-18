<?php

use Illuminate\Database\Seeder;

class RecipeSeeder extends Seeder
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

        // Some food names
        $foodNames = [
            'Teriyaki Salmon with Toasted Sesame Seeds and Rice​​',
            'Smoky Chicken Quesadillas with Salsa and Smashed Avo​​',
            'Colourful Beef and Noodle Stir Fry​​',
            'Leek and Potato Soup with Cheese Croutons​​',
            'Salmon and Feta Salad with Tahini Dressing​​',
            'Cauliflower \'Pilaf\' with Spicy Tandoori Prawns and Fresh Salad​​',
            'Chicken Saltimbocca with Butter Bean Mash and Green Beans​​',
            'Pesto Pulled Chicken Salad with Mozzarella​​',
            'Mexican Chicken and Sweetcorn Meatball Bake with Pinto Beans​​',
            'Sirloin Steak with Anchovy Butter and Greens​​',
            'Thai Beef and Mango Salad​​',
            'Green Pea and Kale Soup with Chilli and Feta​​',
            'Chicken Teriyaki Donburi Bowl with Pak Choi​​',
            'Beef Bacon and Onion Spaghetti Carbonara with Side Salad​​',
            'Cheat\'s Chicken Kiev with Mash and Green Beans​​',
            'Thai Chicken Red Curry with Steamed Rice',
            'Pan Grilled Fillet of Fish, Caper Butter Sauce',
            'Parmesan, Creamy Mushroom Pasta',
            'Cantonese Chicken Chilli',
            'Classic Pepperoni Pizza',
        ];

        // Seed data
        foreach ($foodNames as $foodName) {
            // Seed the recipe
            $recipeId = DB::table('recipe')->insertGetId([
                'name' => $foodName,
                'description' => $faker->sentence(
                    $nbWords = 30,
                    $variableNbWords = true
                ),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Associate some ingredients to this recipe
            for ($j = 0; $j < $faker->numberBetween($min = 4, $max = 8); $j++) {
                DB::table('recipe_ingredient')->insert([
                    'recipe_id' => $recipeId,
                    'ingredient_id' => $faker->numberBetween(
                        $min = 1,
                        $max = 40
                    ),
                    'amount' => $faker->numberBetween($min = 1, $max = 5),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }
}

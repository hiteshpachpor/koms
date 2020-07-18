<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        /**
         * This script will seed:
         * Users: 10
         * User addresses: 10
         * Ingredients: 40
         * Suppliers: 5
         * Recipes: 20
         * Boxes: 10
         */
        $this->call([
            UserSeeder::class,
            UserAddressSeeder::class,
            SupplierSeeder::class,
            IngredientSeeder::class,
            RecipeSeeder::class,
            BoxSeeder::class,
        ]);
    }
}

<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Ingredient;
use App\Recipe;
use App\RecipeIngredient;
use App\BoxOrder;
use App\BoxOrderRecipe;

class InventoryTest extends TestCase
{
    use RefreshDatabase;

    /**
     * API should return the list of ingredients to be purchased
     * to fulfill all orders for the 7 days from the given date
     *
     * @return void
     */
    public function testPurchaseOrderApi()
    {
        // We'll take today's date.
        // Any date will work here, since all box delivery_dates
        // are set relative to this date.
        $date = new \DateTimeImmutable();

        // Create a Faker instance
        $faker = \Faker\Factory::create();

        // Create a few ingredients
        $ingredients = [];

        $ingredients[] = factory(Ingredient::class)->create([
            'name' => 'Potato',
            'in_stock' => true,
            'measure' => 'g',
        ]);

        $ingredients[] = factory(Ingredient::class)->create([
            'name' => 'Garlic',
            'in_stock' => true,
            'measure' => 'pieces',
        ]);

        $ingredients[] = factory(Ingredient::class)->create([
            'name' => 'Onion',
            'in_stock' => true,
            'measure' => 'g',
        ]);

        $ingredients[] = factory(Ingredient::class)->create([
            'name' => 'Tomato',
            'in_stock' => true,
            'measure' => 'g',
        ]);

        $ingredients[] = factory(Ingredient::class)->create([
            'name' => 'Cheese',
            'in_stock' => true,
            'measure' => 'pieces',
        ]);

        $ingredients[] = factory(Ingredient::class)->create([
            'name' => 'Butter',
            'in_stock' => true,
            'measure' => 'g',
        ]);

        $ingredients[] = factory(Ingredient::class)->create([
            'name' => 'Thyme',
            'in_stock' => true,
            'measure' => 'g',
        ]);

        $ingredients[] = factory(Ingredient::class)->create([
            'name' => 'Celery',
            'in_stock' => true,
            'measure' => 'g',
        ]);

        $ingredients[] = factory(Ingredient::class)->create([
            'name' => 'Chicken',
            'in_stock' => true,
            'measure' => 'kg',
        ]);

        $ingredients[] = factory(Ingredient::class)->create([
            'name' => 'Shrimp',
            'in_stock' => true,
            'measure' => 'pieces',
        ]);

        // Create a few recipes
        $recipes = [];

        $recipes[] = factory(Recipe::class)->create();
        $recipes[] = factory(Recipe::class)->create();
        $recipes[] = factory(Recipe::class)->create();
        $recipes[] = factory(Recipe::class)->create();
        $recipes[] = factory(Recipe::class)->create();
        $recipes[] = factory(Recipe::class)->create();
        $recipes[] = factory(Recipe::class)->create();
        $recipes[] = factory(Recipe::class)->create();
        $recipes[] = factory(Recipe::class)->create();
        $recipes[] = factory(Recipe::class)->create();

        /**
         * Associate ingredients to recipes (rather randomly)
         * Recipe: [Ingredients]
         */
        $recipeIngredientsMapping = [
            0 => [1, 2, 4, 8],
            1 => [1, 2, 3, 4],
            2 => [2, 3, 4],
            3 => [4, 5, 6, 7],
            4 => [5, 6, 7, 8],
            5 => [6, 7, 8, 9],
            6 => [8, 9, 0],
            7 => [1, 3, 5],
            8 => [2, 4, 6, 8],
            9 => [0, 3, 6, 9],
        ];

        // Associate ingredients to recipes
        $recipeIngredients = [];

        foreach ($recipeIngredientsMapping as $recipeId => $ingredientIds) {
            foreach ($ingredientIds as $ingredientId) {
                $recipe = $recipes[$recipeId];
                $ingredient = $ingredients[$ingredientId];

                $recipeIngredients[$recipeId . '.' . $ingredientId] = factory(
                    RecipeIngredient::class
                )->create([
                    'recipe_id' => $recipe->id,
                    'ingredient_id' => $ingredient->id,
                    'amount' => 2,
                ]);
            }
        }

        // Create a few boxes
        $boxRecipes = [
            0 => [
                'delivery_date' => $date
                    ->add(\DateInterval::createFromDateString('1 days'))
                    ->format('Y-m-d'),
                'recipes' => [0, 1, 2, 3],
            ],
            1 => [
                'delivery_date' => $date
                    ->add(\DateInterval::createFromDateString('2 days'))
                    ->format('Y-m-d'),
                'recipes' => [1, 2, 3, 4],
            ],
            2 => [
                'delivery_date' => $date
                    ->add(\DateInterval::createFromDateString('3 days'))
                    ->format('Y-m-d'),
                'recipes' => [2, 3, 4],
            ],
            3 => [
                'delivery_date' => $date
                    ->add(\DateInterval::createFromDateString('4 days'))
                    ->format('Y-m-d'),
                'recipes' => [4, 5, 6, 7],
            ],
            4 => [
                'delivery_date' => $date
                    ->add(\DateInterval::createFromDateString('5 days'))
                    ->format('Y-m-d'),
                'recipes' => [5, 6, 7, 8],
            ],
            5 => [
                'delivery_date' => $date
                    ->add(\DateInterval::createFromDateString('6 days'))
                    ->format('Y-m-d'),
                'recipes' => [6, 7, 8, 9],
            ],
            6 => [
                'delivery_date' => $date
                    ->add(\DateInterval::createFromDateString('10 days'))
                    ->format('Y-m-d'),
                'recipes' => [8, 9, 0],
            ],
            7 => [
                'delivery_date' => $date
                    ->sub(\DateInterval::createFromDateString('1 days'))
                    ->format('Y-m-d'),
                'recipes' => [1, 3, 5],
            ],
            8 => [
                'delivery_date' => $date
                    ->sub(\DateInterval::createFromDateString('2 days'))
                    ->format('Y-m-d'),
                'recipes' => [2, 4, 6, 8],
            ],
            9 => [
                'delivery_date' => $date
                    ->sub(\DateInterval::createFromDateString('3 days'))
                    ->format('Y-m-d'),
                'recipes' => [0, 3, 6, 9],
            ],
        ];

        // Associate recipes to boxes
        foreach ($boxRecipes as $boxId => $data) {
            $box = factory(BoxOrder::class)->create([
                'delivery_date' => $data['delivery_date'],
            ]);

            foreach ($data['recipes'] as $recipeId) {
                $recipe = $recipes[$recipeId];
                $recipeIngredientIds = $recipeIngredientsMapping[$recipeId];

                foreach ($recipeIngredientIds as $ingredientId) {
                    $ingredient = $ingredients[$ingredientId];

                    factory(BoxOrderRecipe::class)->create([
                        'box_order_id' => $box->id,
                        'recipe_id' => $recipe->id,
                        'recipe_name' => $recipe->name,
                        'ingredient_id' => $ingredient->id,
                        'ingredient_name' => $ingredient->name,
                        'ingredient_measure' => $ingredient->measure,
                        'ingredient_amount' =>
                            $recipeIngredients[$recipeId . '.' . $ingredientId]
                                ->amount,
                    ]);
                }
            }
        }

        // Make api call to list all ingredients
        $response = $this->get("/api/purchase-order/{$date->format('Y-m-d')}");
        $response->assertStatus(200);

        $responseJson = $response->json();

        /**
         * How to verify these numbers:
         * 1. Out of the 10 boxes created, 1 to 6 are in the date range
         *    7 to 10 boxes are outside the date range.
         * 2. For every ingredient, count the number of rows where box_order_id
         *    is between 1 & 6.
         * 3. Multiply this number by 2 since every ingredient's amount in
         *    every recipe is hardcoded to 2.
         * 4. Validate these numbers in the response.
         */
        $response->assertJson([
            [
                'id' => '1',
                'ingredient' => 'Potato',
                'total_amount' => '8',
                'measure' => 'g',
            ],
            [
                'id' => '2',
                'ingredient' => 'Garlic',
                'total_amount' => '12',
                'measure' => 'pieces',
            ],
            [
                'id' => '3',
                'ingredient' => 'Onion',
                'total_amount' => '16',
                'measure' => 'g',
            ],
            [
                'id' => '4',
                'ingredient' => 'Tomato',
                'total_amount' => '18',
                'measure' => 'g',
            ],
            [
                'id' => '5',
                'ingredient' => 'Cheese',
                'total_amount' => '22',
                'measure' => 'pieces',
            ],
            [
                'id' => '6',
                'ingredient' => 'Butter',
                'total_amount' => '18',
                'measure' => 'g',
            ],
            [
                'id' => '7',
                'ingredient' => 'Thyme',
                'total_amount' => '22',
                'measure' => 'g',
            ],
            [
                'id' => '8',
                'ingredient' => 'Celery',
                'total_amount' => '16',
                'measure' => 'g',
            ],
            [
                'id' => '9',
                'ingredient' => 'Chicken',
                'total_amount' => '22',
                'measure' => 'kg',
            ],
            [
                'id' => '10',
                'ingredient' => 'Shrimp',
                'total_amount' => '12',
                'measure' => 'pieces',
            ],
        ]);
    }
}

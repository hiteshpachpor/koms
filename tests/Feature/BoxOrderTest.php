<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Config;
use Tests\TestCase;
use App\User;
use App\UserAddress;
use App\Recipe;
use App\RecipeIngredient;
use App\Ingredient;
use App\BoxOrder;

class BoxOrderTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test API to create a box for a user
     *
     * @return void
     */
    public function testBoxOrderCreation()
    {
        // Create a Faker instance
        $faker = \Faker\Factory::create();

        // Create a few recipes
        $recipes = factory(Recipe::class, 4)->create();

        // Keep a record of all recipe ingredient pairs
        $recipeIngredients = [];

        foreach ($recipes as $recipe) {
            // Create a few ingredients
            $ingredients = factory(
                Ingredient::class,
                $faker->numberBetween($min = 1, $max = 5)
            )->create([
                'in_stock' => true,
                'stock_qty' => 100,
            ]);

            // Associate these ingredients to the recipe
            foreach ($ingredients as $ingredient) {
                $recipeIngredients[] = factory(RecipeIngredient::class)->create(
                    [
                        'recipe_id' => $recipe->id,
                        'ingredient_id' => $ingredient->id,
                        'amount' => $faker->numberBetween($min = 1, $max = 5),
                    ]
                );
            }
        }

        // Get all recipe ids
        $recipeIds = [];

        foreach ($recipes as $recipe) {
            $recipeIds[] = $recipe->id;
        }

        // Create a user and an address for the user
        $user = factory(User::class)->create();
        $userAddress = factory(UserAddress::class)->create([
            'user_id' => $user->id,
        ]);

        // Prepare the box payload for the api
        $box = [
            'user_id' => $user->id,
            'user_address_id' => $userAddress->id,
            'delivery_date' => $faker
                ->dateTimeBetween(
                    $startDate = '+3 days',
                    $endDate = '+14 days',
                    $timezone = null
                )
                ->format('Y-m-d'),
            'delivery_slot' => 'Morning',
            'delivery_notes' => $faker->sentence(
                $nbWords = 5,
                $variableNbWords = true
            ),
            'recipes' => $recipeIds,
        ];

        // Make api call to create a box
        $response = $this->postJson('/api/box/create', $box);
        $response->assertStatus(200);

        $responseJson = $response->json();

        $this->assertEquals(
            count($responseJson['data']['recipes']),
            count($recipeIds)
        );

        $this->assertDatabaseHas('box_order', [
            'id' => $response['data']['id'],
        ]);

        // Recipes should be associated to the box order
        foreach ($recipeIngredients as $recipeIngredient) {
            $this->assertDatabaseHas('box_order_recipe', [
                'box_order_id' => $response['data']['id'],
                'recipe_id' => $recipeIngredient->recipe_id,
                'ingredient_id' => $recipeIngredient->ingredient_id,
                'ingredient_amount' => $recipeIngredient->amount,
            ]);
        }
    }

    /**
     * API should throw a 422 Unprocessable Entity error
     * if required input is missing
     *
     * @return void
     */
    public function testCannotCreateBoxOrderIfMissingInput()
    {
        $box = [
            'delivery_notes' => 'Ring the bell.',
        ];

        $response = $this->postJson('/api/box/create', $box);
        $response->assertStatus(
            \Illuminate\Http\Response::HTTP_UNPROCESSABLE_ENTITY
        );
        $response->assertJson([
            'message' => 'The given data was invalid.',
            'errors' => [
                'user_id' => ['The user id field is required.'],
                'user_address_id' => ['The user address id field is required.'],
                'delivery_date' => ['The delivery date field is required.'],
                'recipes' => ['The recipes field is required.'],
            ],
        ]);
    }

    /**
     * API should throw a 422 Unprocessable Entity error
     * if any recipe's ingredient is out of stock
     *
     * @return void
     */
    public function testCannotCreateBoxOrderIfIngredientOutOfStock()
    {
        // Create a Faker instance
        $faker = \Faker\Factory::create();

        // Create a few ingredients
        $ingredient1 = factory(Ingredient::class)->create([
            'in_stock' => true,
        ]);

        $ingredient2 = factory(Ingredient::class)->create([
            'in_stock' => true,
        ]);

        $ingredient3 = factory(Ingredient::class)->create([
            'in_stock' => true,
        ]);

        $ingredient4 = factory(Ingredient::class)->create([
            'name' => 'Tomato',
            'in_stock' => false,
        ]);

        // Create a few recipes
        $recipe1 = factory(Recipe::class)->create();
        $recipe2 = factory(Recipe::class)->create();
        $recipe3 = factory(Recipe::class)->create([
            'name' => 'Red Sauce Pasta',
        ]);

        // Associate ingredients to recipes (rather randomly)
        factory(RecipeIngredient::class)->create([
            'recipe_id' => $recipe1->id,
            'ingredient_id' => $ingredient1->id,
            'amount' => $faker->numberBetween($min = 1, $max = 5),
        ]);

        factory(RecipeIngredient::class)->create([
            'recipe_id' => $recipe1->id,
            'ingredient_id' => $ingredient2->id,
            'amount' => $faker->numberBetween($min = 1, $max = 5),
        ]);

        factory(RecipeIngredient::class)->create([
            'recipe_id' => $recipe1->id,
            'ingredient_id' => $ingredient3->id,
            'amount' => $faker->numberBetween($min = 1, $max = 5),
        ]);

        factory(RecipeIngredient::class)->create([
            'recipe_id' => $recipe2->id,
            'ingredient_id' => $ingredient2->id,
            'amount' => $faker->numberBetween($min = 1, $max = 5),
        ]);

        factory(RecipeIngredient::class)->create([
            'recipe_id' => $recipe2->id,
            'ingredient_id' => $ingredient3->id,
            'amount' => $faker->numberBetween($min = 1, $max = 5),
        ]);

        factory(RecipeIngredient::class)->create([
            'recipe_id' => $recipe3->id,
            'ingredient_id' => $ingredient1->id,
            'amount' => $faker->numberBetween($min = 1, $max = 5),
        ]);

        factory(RecipeIngredient::class)->create([
            'recipe_id' => $recipe3->id,
            'ingredient_id' => $ingredient4->id,
            'amount' => $faker->numberBetween($min = 1, $max = 5),
        ]);

        // Create a user and an address for the user
        $user = factory(User::class)->create();
        $userAddress = factory(UserAddress::class)->create([
            'user_id' => $user->id,
        ]);

        // Prepare the box payload for the api
        $box = [
            'user_id' => $user->id,
            'user_address_id' => $userAddress->id,
            'delivery_date' => $faker
                ->dateTimeBetween(
                    $startDate = '+3 days',
                    $endDate = '+14 days',
                    $timezone = null
                )
                ->format('Y-m-d'),
            'delivery_slot' => 'Morning',
            'delivery_notes' => $faker->sentence(
                $nbWords = 5,
                $variableNbWords = true
            ),
            'recipes' => [$recipe1->id, $recipe2->id, $recipe3->id],
        ];

        $response = $this->postJson('/api/box/create', $box);
        $response->assertStatus(
            \Illuminate\Http\Response::HTTP_UNPROCESSABLE_ENTITY
        );
        $response->assertJson([
            'message' =>
                'Sorry, the recipe \'Red Sauce Pasta\' has an ingredient \'Tomato\' which currently out of stock. Please choose a different recipe.',
        ]);
    }

    /**
     * API should throw a 422 Unprocessable Entity error
     * if the delivery_date is in an invalid format
     *
     * @return void
     */
    public function testCannotCreateBoxOrderIfDateInvalid()
    {
        // Create a Faker instance
        $faker = \Faker\Factory::create();

        // Create a few recipes
        $recipes = factory(Recipe::class, 4)->create();

        foreach ($recipes as $recipe) {
            // Create a few ingredients
            $ingredients = factory(
                Ingredient::class,
                $faker->numberBetween($min = 1, $max = 5)
            )->create();

            // Associate these ingredients to the recipe
            foreach ($ingredients as $ingredient) {
                factory(RecipeIngredient::class)->create([
                    'recipe_id' => $recipe->id,
                    'ingredient_id' => $ingredient->id,
                    'amount' => $faker->numberBetween($min = 1, $max = 5),
                ]);
            }
        }

        // Get all recipe ids
        $recipeIds = [];

        foreach ($recipes as $recipe) {
            $recipeIds[] = $recipe->id;
        }

        // Create a user and an address for the user
        $user = factory(User::class)->create();
        $userAddress = factory(UserAddress::class)->create([
            'user_id' => $user->id,
        ]);

        // Prepare the box payload for the api
        $box = [
            'user_id' => $user->id,
            'user_address_id' => $userAddress->id,
            'delivery_date' => '12-12-2021', // correct format is 'Y-m-d'
            'delivery_slot' => 'Morning',
            'delivery_notes' => $faker->sentence(
                $nbWords = 5,
                $variableNbWords = true
            ),
            'recipes' => $recipeIds,
        ];

        $response = $this->postJson('/api/box/create', $box);
        $response->assertStatus(
            \Illuminate\Http\Response::HTTP_UNPROCESSABLE_ENTITY
        );
        $response->assertJson([
            'message' => 'The given data was invalid.',
            'errors' => [
                'delivery_date' => [
                    'The delivery date does not match the format Y-m-d.',
                ],
            ],
        ]);
    }

    /**
     * API should throw a 422 Unprocessable Entity error
     * if the delivery date & slot is not serviceable
     *
     * @return void
     */
    public function testCannotCreateBoxOrderIfDateNotServiceable()
    {
        // Create a Faker instance
        $faker = \Faker\Factory::create();

        // Create a few recipes
        $recipes = factory(Recipe::class, 4)->create();

        foreach ($recipes as $recipe) {
            // Create a few ingredients
            $ingredients = factory(
                Ingredient::class,
                $faker->numberBetween($min = 1, $max = 5)
            )->create();

            // Associate these ingredients to the recipe
            foreach ($ingredients as $ingredient) {
                factory(RecipeIngredient::class)->create([
                    'recipe_id' => $recipe->id,
                    'ingredient_id' => $ingredient->id,
                    'amount' => $faker->numberBetween($min = 1, $max = 5),
                ]);
            }
        }

        // Get all recipe ids
        $recipeIds = [];

        foreach ($recipes as $recipe) {
            $recipeIds[] = $recipe->id;
        }

        // Create a user and an address for the user
        $user = factory(User::class)->create();
        $userAddress = factory(UserAddress::class)->create([
            'user_id' => $user->id,
        ]);

        // Prepare the box payload for the api
        $box = [
            'user_id' => $user->id,
            'user_address_id' => $userAddress->id,
            'delivery_date' => $faker
                ->dateTimeBetween(
                    $startDate = 'now',
                    $endDate = '+1 days',
                    $timezone = null
                )
                ->format('Y-m-d'),
            'delivery_slot' => 'Evening',
            'delivery_notes' => $faker->sentence(
                $nbWords = 5,
                $variableNbWords = true
            ),
            'recipes' => $recipeIds,
        ];

        $response = $this->postJson('/api/box/create', $box);
        $response->assertStatus(
            \Illuminate\Http\Response::HTTP_UNPROCESSABLE_ENTITY
        );
        $response->assertJson([
            'message' =>
                'Sorry, this slot is not available. Please choose a different slot.',
        ]);
    }

    /**
     * API should throw a 422 Unprocessable Entity error
     * if the delivery_slot is invalid
     *
     * @return void
     */
    public function testCannotCreateBoxOrderIfDeliverySlotInvalid()
    {
        // Create a Faker instance
        $faker = \Faker\Factory::create();

        // Create a few recipes
        $recipes = factory(Recipe::class, 4)->create();

        foreach ($recipes as $recipe) {
            // Create a few ingredients
            $ingredients = factory(
                Ingredient::class,
                $faker->numberBetween($min = 1, $max = 5)
            )->create();

            // Associate these ingredients to the recipe
            foreach ($ingredients as $ingredient) {
                factory(RecipeIngredient::class)->create([
                    'recipe_id' => $recipe->id,
                    'ingredient_id' => $ingredient->id,
                    'amount' => $faker->numberBetween($min = 1, $max = 5),
                ]);
            }
        }

        // Get all recipe ids
        $recipeIds = [];

        foreach ($recipes as $recipe) {
            $recipeIds[] = $recipe->id;
        }

        // Create a user and an address for the user
        $user = factory(User::class)->create();
        $userAddress = factory(UserAddress::class)->create([
            'user_id' => $user->id,
        ]);

        // Prepare the box payload for the api
        $box = [
            'user_id' => $user->id,
            'user_address_id' => $userAddress->id,
            'delivery_date' => $faker
                ->dateTimeBetween(
                    $startDate = '+3 days',
                    $endDate = '+14 days',
                    $timezone = null
                )
                ->format('Y-m-d'),
            'delivery_slot' => 'Night',
            'delivery_notes' => $faker->sentence(
                $nbWords = 5,
                $variableNbWords = true
            ),
            'recipes' => $recipeIds,
        ];

        $response = $this->postJson('/api/box/create', $box);
        $response->assertStatus(
            \Illuminate\Http\Response::HTTP_UNPROCESSABLE_ENTITY
        );
        $response->assertJson([
            'message' =>
                'Delivery slot is incorrect. Permissible values are Morning, Afternoon, Evening.',
        ]);
    }

    /**
     * API should throw a 422 Unprocessable Entity error
     * if any recipe id is invalid
     *
     * @return void
     */
    public function testCannotCreateBoxOrderIfRecipeIdInvalid()
    {
        // Create a Faker instance
        $faker = \Faker\Factory::create();

        $maxRecipesInABox = Config::get('constants.max_recipes_in_a_box');

        // Create a few recipes
        $recipes = factory(Recipe::class, $maxRecipesInABox - 1)->create();

        foreach ($recipes as $recipe) {
            // Create a few ingredients
            $ingredients = factory(
                Ingredient::class,
                $faker->numberBetween($min = 1, $max = 5)
            )->create([
                'in_stock' => true,
                'stock_qty' => 100,
            ]);

            // Associate these ingredients to the recipe
            foreach ($ingredients as $ingredient) {
                factory(RecipeIngredient::class)->create([
                    'recipe_id' => $recipe->id,
                    'ingredient_id' => $ingredient->id,
                    'amount' => $faker->numberBetween($min = 1, $max = 5),
                ]);
            }
        }

        // Get all recipe ids
        $recipeIds = [];

        foreach ($recipes as $recipe) {
            $recipeIds[] = $recipe->id;
        }

        // Add a non-existing recipe id to the list
        $recipeIds[] = 9999;

        // Create a user and an address for the user
        $user = factory(User::class)->create();
        $userAddress = factory(UserAddress::class)->create([
            'user_id' => $user->id,
        ]);

        // Prepare the box payload for the api
        $box = [
            'user_id' => $user->id,
            'user_address_id' => $userAddress->id,
            'delivery_date' => $faker
                ->dateTimeBetween(
                    $startDate = '+3 days',
                    $endDate = '+14 days',
                    $timezone = null
                )
                ->format('Y-m-d'),
            'delivery_slot' => 'Morning',
            'delivery_notes' => $faker->sentence(
                $nbWords = 5,
                $variableNbWords = true
            ),
            'recipes' => $recipeIds,
        ];

        $response = $this->postJson('/api/box/create', $box);
        $response->assertStatus(
            \Illuminate\Http\Response::HTTP_UNPROCESSABLE_ENTITY
        );
        $response->assertJson([
            'message' => 'One or more recipe ids are invalid.',
        ]);
    }

    /**
     * API should throw a 422 Unprocessable Entity error
     * if more than 4 recipes are being added to a box
     *
     * @return void
     */
    public function testCannotCreateBoxOrderIfMoreThanAllowedRecipes()
    {
        // Create a Faker instance
        $faker = \Faker\Factory::create();

        $maxRecipesInABox = Config::get('constants.max_recipes_in_a_box');

        // Create a few recipes
        $recipes = factory(Recipe::class, $maxRecipesInABox + 1)->create();

        foreach ($recipes as $recipe) {
            // Create a few ingredients
            $ingredients = factory(
                Ingredient::class,
                $faker->numberBetween($min = 1, $max = 5)
            )->create([
                'in_stock' => true,
                'stock_qty' => 100,
            ]);

            // Associate these ingredients to the recipe
            foreach ($ingredients as $ingredient) {
                factory(RecipeIngredient::class)->create([
                    'recipe_id' => $recipe->id,
                    'ingredient_id' => $ingredient->id,
                    'amount' => $faker->numberBetween($min = 1, $max = 5),
                ]);
            }
        }

        // Get all recipe ids
        $recipeIds = [];

        foreach ($recipes as $recipe) {
            $recipeIds[] = $recipe->id;
        }

        // Create a user and an address for the user
        $user = factory(User::class)->create();
        $userAddress = factory(UserAddress::class)->create([
            'user_id' => $user->id,
        ]);

        // Prepare the box payload for the api
        $box = [
            'user_id' => $user->id,
            'user_address_id' => $userAddress->id,
            'delivery_date' => $faker
                ->dateTimeBetween(
                    $startDate = '+3 days',
                    $endDate = '+14 days',
                    $timezone = null
                )
                ->format('Y-m-d'),
            'delivery_slot' => 'Morning',
            'delivery_notes' => $faker->sentence(
                $nbWords = 5,
                $variableNbWords = true
            ),
            'recipes' => $recipeIds,
        ];

        $response = $this->postJson('/api/box/create', $box);
        $response->assertStatus(
            \Illuminate\Http\Response::HTTP_UNPROCESSABLE_ENTITY
        );
        $response->assertJson([
            'message' => 'The given data was invalid.',
            'errors' => [
                'recipes' => [
                    "The recipes must have between 1 and {$maxRecipesInABox} items.",
                ],
            ],
        ]);
    }
}

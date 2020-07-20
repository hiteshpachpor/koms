<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
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
    }
}

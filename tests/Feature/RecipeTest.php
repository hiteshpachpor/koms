<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Ingredient;
use App\Recipe;
use App\RecipeIngredient;

class RecipeTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test API to create a recipe
     *
     * @return void
     */
    public function testRecipeCreation()
    {
        // Create a Faker instance
        $faker = \Faker\Factory::create();

        // Create a few ingredients
        $ingredients = factory(Ingredient::class, 5)->create();
        $ingredientsInRecipe = [];

        // Prepare ingredients for payload
        foreach ($ingredients as $ingredient) {
            $ingredientsInRecipe[] = [
                'id' => $ingredient->id,
                'amount' => $faker->numberBetween($min = 1, $max = 5),
            ];
        }

        // Prepare recipe payload
        $recipe = [
            'name' => $faker->unique()->name,
            'description' => $faker->sentence(
                $nbWords = 10,
                $variableNbWords = true
            ),
            'ingredients' => $ingredientsInRecipe,
        ];

        // Make api call to create a recipe
        $response = $this->postJson('/api/recipes', $recipe);
        $response->assertStatus(200);

        $responseJson = $response->json();

        $this->assertEquals($responseJson['name'], $recipe['name']);

        $this->assertDatabaseHas('recipe', [
            'name' => $recipe['name'],
        ]);
    }

    /**
     * Test API to list all recipes
     *
     * @return void
     */
    public function testRecipeListing()
    {
        // Create a Faker instance
        $faker = \Faker\Factory::create();

        // Create a few recipes
        $recipes = factory(Recipe::class, 5)->create();

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

        // Make api call to list all recipes
        $response = $this->get('/api/recipes');
        $response->assertStatus(200);

        $responseJson = $response->json();

        // All recipes should be returned
        $this->assertEquals(count($responseJson['data']), 5);
        $this->assertEquals($responseJson['total'], 5);
    }
}

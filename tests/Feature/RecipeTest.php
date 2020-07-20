<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Supplier;
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

        // Create a supplier
        $supplier = factory(Supplier::class)->create();

        // Create a few ingredient using factory
        $ingredients = factory(Ingredient::class, 5)->create([
            'supplier_id' => $supplier->id,
        ]);

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

    /**
     * API should throw a 422 Unprocessable Entity error
     * if recipe name is missing
     *
     * @return void
     */
    public function testCannotCreateRecipeIfMissingInput()
    {
        // Missing inputs - name
        // A recipe can be created without mapping any ingredients to it
        $recipe = [
            'description' => 'Lorem ipsum.',
        ];

        $response = $this->postJson('/api/recipes', $recipe);
        $response->assertStatus(
            \Illuminate\Http\Response::HTTP_UNPROCESSABLE_ENTITY
        );
        $response->assertJson([
            'message' => 'The given data was invalid.',
            'errors' => [
                'name' => ['The name field is required.'],
            ],
        ]);
    }

    /**
     * API should throw a 422 Unprocessable Entity error
     * if any ingredient id is invalid
     *
     * @return void
     */
    public function testCannotCreateRecipeIfIngredientIdInvalid()
    {
        // Create a Faker instance
        $faker = \Faker\Factory::create();

        // Create a supplier
        $supplier = factory(Supplier::class)->create();

        // Create a few ingredient using factory
        $ingredients = factory(Ingredient::class, 5)->create([
            'supplier_id' => $supplier->id,
        ]);

        // Prepare ingredients for payload
        foreach ($ingredients as $ingredient) {
            $ingredientsInRecipe[] = [
                'id' => $ingredient->id,
                'amount' => $faker->numberBetween($min = 1, $max = 5),
            ];
        }

        // Add a random ingredient to the list too
        $ingredientsInRecipe[] = [
            'id' => 9999,
            'amount' => 10,
        ];

        // Prepare recipe payload
        $recipe = [
            'name' => $faker->unique()->name,
            'description' => $faker->sentence(
                $nbWords = 10,
                $variableNbWords = true
            ),
            'ingredients' => $ingredientsInRecipe,
        ];

        $response = $this->postJson('/api/recipes', $recipe);
        $response->assertStatus(
            \Illuminate\Http\Response::HTTP_UNPROCESSABLE_ENTITY
        );
        $response->assertJson([
            'message' => 'Invalid ingredient amount or id 9999.',
        ]);
    }

    /**
     * API should throw a 422 Unprocessable Entity error
     * if any ingredient's amount is not valid
     *
     * @return void
     */
    public function testCannotCreateRecipeIfIngredientAmountInvalid()
    {
        // Create a Faker instance
        $faker = \Faker\Factory::create();

        // Create a supplier
        $supplier = factory(Supplier::class)->create();

        // Create a few ingredient using factory
        $ingredients = factory(Ingredient::class, 5)->create([
            'supplier_id' => $supplier->id,
        ]);

        // Prepare ingredients for payload
        foreach ($ingredients as $ingredient) {
            $ingredientsInRecipe[] = [
                'id' => $ingredient->id,
                'amount' => $faker->numberBetween($min = 1, $max = 5),
            ];
        }

        // Make first ingredient's amount a string
        $ingredientsInRecipe[0]['amount'] = 'five';

        // Prepare recipe payload
        $recipe = [
            'name' => $faker->unique()->name,
            'description' => $faker->sentence(
                $nbWords = 10,
                $variableNbWords = true
            ),
            'ingredients' => $ingredientsInRecipe,
        ];

        $response = $this->postJson('/api/recipes', $recipe);
        $response->assertStatus(
            \Illuminate\Http\Response::HTTP_UNPROCESSABLE_ENTITY
        );
        $response->assertJson([
            'message' => 'Invalid ingredient amount or id 1.',
        ]);
    }

    /**
     * API should throw a 422 Unprocessable Entity error
     * if the name is already taken.
     *
     * @return void
     */
    public function testCannotCreateRecipeIfNameConflict()
    {
        // Create a recipe
        $firstRecipe = factory(Recipe::class)->create();

        // Create a Faker instance
        $faker = \Faker\Factory::create();

        // Create a supplier
        $supplier = factory(Supplier::class)->create();

        // Create a few ingredient using factory
        $ingredients = factory(Ingredient::class, 5)->create([
            'supplier_id' => $supplier->id,
        ]);

        // Prepare ingredients for payload
        foreach ($ingredients as $ingredient) {
            $ingredientsInRecipe[] = [
                'id' => $ingredient->id,
                'amount' => $faker->numberBetween($min = 1, $max = 5),
            ];
        }

        // Prepare recipe payload
        $secondRecipe = [
            'name' => $firstRecipe->name,
            'description' => $faker->sentence(
                $nbWords = 10,
                $variableNbWords = true
            ),
            'ingredients' => $ingredientsInRecipe,
        ];

        $response = $this->postJson('/api/recipes', $secondRecipe);
        $response->assertStatus(
            \Illuminate\Http\Response::HTTP_UNPROCESSABLE_ENTITY
        );
        $response->assertJson([
            'message' => 'The given data was invalid.',
            'errors' => [
                'name' => ['The name has already been taken.'],
            ],
        ]);
    }
}

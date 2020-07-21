<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Config;
use Tests\TestCase;
use App\Ingredient;
use App\Supplier;

class IngredientTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test API to create an ingredient
     *
     * @return void
     */
    public function testIngredientCreation()
    {
        $supplier = factory(Supplier::class)->create();
        $ingredient = factory(Ingredient::class)->make([
            'supplier_id' => $supplier->id,
        ]);

        $response = $this->postJson('/api/ingredients', $ingredient->toArray());
        $response->assertStatus(201);

        $responseJson = $response->json();

        $this->assertTrue(
            in_array(
                $responseJson['measure'],
                Config::get('constants.ingredient_measure')
            )
        );

        $this->assertDatabaseHas('ingredient', [
            'name' => $ingredient['name'],
        ]);

        // To debug:
        // fwrite(STDERR, print_r($responseJson, true));
    }

    /**
     * Test API to list ingredients in paginated format
     *
     * @return void
     */
    public function testIngredientListing()
    {
        $ingredientCount = 25;
        $ingredientsPerPage = 15;
        $totalPagesExpected = (int) ceil(
            $ingredientCount / $ingredientsPerPage
        );
        $currentPage = 1;

        // Create a few ingredients
        $ingredients = factory(Ingredient::class, $ingredientCount)->create();

        // Make api call to list all ingredients
        $response = $this->get('/api/ingredients');
        $response->assertStatus(200);

        $responseJson = $response->json();

        // 15 ingredients should be returned as per pagination
        $this->assertEquals(count($responseJson['data']), $ingredientsPerPage);
        $this->assertEquals($responseJson['total'], $ingredientCount);

        $response->assertJson([
            'current_page' => $currentPage,
            'data' => array_slice(
                $ingredients->toArray(),
                0,
                $ingredientsPerPage
            ),
            'first_page_url' => "http://localhost/api/ingredients?page={$currentPage}",
            'from' => 1,
            'last_page' => $totalPagesExpected,
            'last_page_url' => "http://localhost/api/ingredients?page={$totalPagesExpected}",
            'next_page_url' => "http://localhost/api/ingredients?page={$totalPagesExpected}",
            'path' => 'http://localhost/api/ingredients',
            'per_page' => $ingredientsPerPage,
            'prev_page_url' => null,
            'to' => $ingredientsPerPage,
            'total' => $ingredientCount,
        ]);

        // To debug:
        // fwrite(STDERR, print_r("...", true));
    }

    /**
     * API should throw a 422 Unprocessable Entity error
     * if a required input is missing
     *
     * @return void
     */
    public function testCannotCreateIngredientIfMissingInput()
    {
        // Missing inputs - name, in_stock, stock_qty
        $supplier = factory(Supplier::class)->create();
        $ingredient = [
            'description' => 'Lorem ipsum.',
            'measure' => 'g',
            'supplier_id' => $supplier->id,
        ];

        $response = $this->postJson('/api/ingredients', $ingredient);
        $response->assertStatus(
            \Illuminate\Http\Response::HTTP_UNPROCESSABLE_ENTITY
        );
        $response->assertJson([
            'message' => 'The given data was invalid.',
            'errors' => [
                'name' => ['The name field is required.'],
                'in_stock' => ['The in stock field is required.'],
                'stock_qty' => ['The stock qty field is required.'],
            ],
        ]);
    }

    /**
     * API should throw a 422 Unprocessable Entity error
     * if the measure string is invalid (not in the enum)
     *
     * @return void
     */
    public function testCannotCreateIngredientIfMeasureInvalid()
    {
        // gms is not in the measure enum list
        $supplier = factory(Supplier::class)->create();
        $ingredient = [
            'name' => 'Chili flakes',
            'description' => 'Lorem ipsum.',
            'in_stock' => true,
            'stock_qty' => 50,
            'measure' => 'gms',
            'supplier_id' => $supplier->id,
        ];

        $response = $this->postJson('/api/ingredients', $ingredient);
        $response->assertStatus(
            \Illuminate\Http\Response::HTTP_UNPROCESSABLE_ENTITY
        );
        $response->assertJson([
            'message' =>
                'Measure is invalid. Permissible values are g, kg, ml, l, pieces.',
        ]);
    }

    /**
     * API should throw a 422 Unprocessable Entity error
     * if the name is already taken.
     *
     * @return void
     */
    public function testCannotCreateIngredientIfNameConflict()
    {
        $supplier = factory(Supplier::class)->create();

        // Create an ingredient using factory
        $firstIngredient = factory(Ingredient::class)->create([
            'supplier_id' => $supplier->id,
        ]);

        // Prepare payload for API
        $secondIngredient = [
            'name' => $firstIngredient->name, // same name as the first one
            'description' => 'Lorem ipsum.',
            'in_stock' => true,
            'stock_qty' => 50,
            'measure' => 'gms',
            'supplier_id' => $supplier->id,
        ];

        $response = $this->postJson('/api/ingredients', $secondIngredient);
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

<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Config;
use Tests\TestCase;
use App\Ingredient;

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
        $ingredient = factory(Ingredient::class)->make();

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
        // fwrite(STDERR, print_r("...", true));
    }

    /**
     * Test API to list all ingredients
     *
     * @return void
     */
    public function testIngredientListing()
    {
        // Create a few ingredients
        $ingredients = factory(Ingredient::class, 5)->create();

        // Make api call to list all ingredients
        $response = $this->get('/api/ingredients');
        $response->assertStatus(200);

        $responseJson = $response->json();

        // All ingredients should be returned
        $this->assertEquals(count($responseJson['data']), 5);
        $this->assertEquals($responseJson['total'], 5);

        // To debug:
        // fwrite(STDERR, print_r("...", true));
    }
}

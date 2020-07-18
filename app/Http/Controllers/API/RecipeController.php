<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Recipe;
use App\RecipeIngredient;
use Illuminate\Http\Request;

class RecipeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return Recipe::with('ingredientList.ingredient')->paginate();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $fields = ["name", "description"];

        $recipe = new Recipe();

        // Only assign valid fields to the model
        foreach ($fields as $field) {
            if ($request->has($field)) {
                $recipe->{$field} = $request->get($field);
            }
        }

        // Save the recipe
        $recipe->save();

        // Save the recipe's ingredients
        if ($request->has('ingredients')) {
            foreach ($request->get('ingredients') as $ingredient) {
                $recipeIngredient = new RecipeIngredient();
                $recipeIngredient->ingredient_id = $ingredient['id'];
                $recipeIngredient->amount = $ingredient['amount'];
                $recipe->ingredientList()->save($recipeIngredient);
            }
        }

        return Recipe::with('ingredientList.ingredient')->find($recipe->id);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Recipe  $recipe
     * @return \Illuminate\Http\Response
     */
    public function show(Recipe $recipe)
    {
        return Recipe::with('ingredientList.ingredient')->find($recipe->id);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Recipe  $recipe
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Recipe $recipe)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Recipe  $recipe
     * @return \Illuminate\Http\Response
     */
    public function destroy(Recipe $recipe)
    {
        $id = $recipe->id;
        $recipe->delete();
        return ['id' => $id];
    }
}

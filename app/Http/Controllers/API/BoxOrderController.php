<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\BoxOrder;
use App\BoxOrderRecipe;
use App\Recipe;
use App\Http\Resources\BoxOrder as BoxOrderResource;

class BoxOrderController extends Controller
{
    /**
     * Create a box for the user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $fields = [
            'user_id',
            'user_address_id',
            'delivery_date',
            'delivery_slot',
            'delivery_notes',
        ];

        $boxOrder = new BoxOrder();

        // Only assign valid fields to the model
        foreach ($fields as $field) {
            if ($request->has($field)) {
                $boxOrder->{$field} = $request->get($field);
            }
        }

        // Save the box
        $boxOrder->save();

        // Save the box's recipes
        if ($request->has('recipes')) {
            foreach ($request->get('recipes') as $recipeId) {
                $recipe = Recipe::with('ingredientList.ingredient')->find(
                    $recipeId
                );
                $ingredients = $recipe->ingredientList;

                foreach ($ingredients as $ingredient) {
                    $boxOrderRecipe = new BoxOrderRecipe([
                        'recipe_id' => $recipeId,
                        'recipe_name' => $recipe->name,
                        'ingredient_id' => $ingredient->ingredient->id,
                        'ingredient_name' => $ingredient->ingredient->name,
                        'ingredient_measure' =>
                            $ingredient->ingredient->measure,
                        'ingredient_amount' => $ingredient->amount,
                    ]);
                    $boxOrder->recipes()->save($boxOrderRecipe);
                }
            }
        }

        return $this->show($boxOrder->id);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $boxOrder = BoxOrder::with(['user', 'userAddress', 'recipes'])->find(
            $id
        );

        // Throw a 404 if the box_order is not found
        if (!isset($boxOrder->id)) {
            abort(404, "No box was found with id ${id}.");
        }

        return new BoxOrderResource($boxOrder);
    }
}

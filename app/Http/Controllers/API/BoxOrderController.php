<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\BoxOrder;
use App\BoxOrderRecipe;

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
                $boxOrderRecipe = new BoxOrderRecipe([
                    'recipe_id' => $recipeId,
                ]);
                $boxOrder->recipes()->save($boxOrderRecipe);
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
        return BoxOrder::with([
            'user',
            'userAddress',
            'recipes.recipe.ingredientList.ingredient',
        ])->find($id);
    }
}

<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Recipe;
use App\BoxOrder;
use App\Http\Resources\BoxOrder as BoxOrderResource;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

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
        $maxRecipesInABox = Config::get('constants.max_recipes_in_a_box');

        // Validate general fields
        $request->validate([
            'user_id' => ['required', 'exists:\App\User,id'],
            'user_address_id' => ['required', 'exists:\App\UserAddress,id'],
            'delivery_date' => ['required', 'date_format:Y-m-d'],
            'recipes' => ['required', 'array', "between:1,{$maxRecipesInABox}"],
        ]);

        // Validate delivery_slot field
        $availableDeliverySlots = Config::get(
            'constants.box_order_delivery_slot'
        );
        $validator = Validator::make($request->all(), [
            'delivery_slot' => ['required', Rule::in($availableDeliverySlots)],
        ]);

        if ($validator->fails()) {
            $deliverySlotsString = implode(', ', $availableDeliverySlots);
            abort(
                \Illuminate\Http\Response::HTTP_UNPROCESSABLE_ENTITY,
                "Delivery slot is incorrect. Permissible values are ${deliverySlotsString}."
            );
        }

        // Validate delivery_date & delivery_slot
        $deliveryDateSlotIsValid = BoxOrder::isDeliveryDateSlotValid(
            $request->delivery_date,
            $request->delivery_slot
        );

        if (!$deliveryDateSlotIsValid) {
            abort(
                \Illuminate\Http\Response::HTTP_UNPROCESSABLE_ENTITY,
                "Sorry, this slot is not available. Please choose a different slot."
            );
        }

        // Validate all the recipe ids
        $validator = Validator::make($request->all(), [
            'recipes.*' => ['required', 'exists:\App\Recipe,id'],
        ]);

        if ($validator->fails()) {
            abort(
                \Illuminate\Http\Response::HTTP_UNPROCESSABLE_ENTITY,
                "One or more recipe ids are invalid."
            );
        }

        // Check if all the ingredients are in stock
        foreach ($request->get('recipes') as $recipeId) {
            $recipe = Recipe::with('ingredientList.ingredient')->find(
                $recipeId
            );
            $ingredients = $recipe->ingredientList;

            foreach ($ingredients as $ingredient) {
                // If the ingredient is out of stock,
                // this recipe cannot be added to the box.
                if (
                    !$ingredient->ingredient->in_stock ||
                    $ingredient->ingredient->stock_qty == 0
                ) {
                    abort(
                        \Illuminate\Http\Response::HTTP_UNPROCESSABLE_ENTITY,
                        "Sorry, the recipe '{$recipe->name}' has an ingredient '{$ingredient->ingredient->name}' which currently out of stock. Please choose a different recipe."
                    );
                }
            }
        }

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
        foreach ($request->get('recipes') as $recipeId) {
            $boxOrder->addRecipe($recipeId);
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

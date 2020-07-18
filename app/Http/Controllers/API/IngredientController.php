<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Ingredient;
use Illuminate\Http\Request;

class IngredientController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return Ingredient::paginate();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $ingredient = Ingredient::create($request->all());
        return $ingredient;
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Ingredient  $ingredient
     * @return \Illuminate\Http\Response
     */
    public function show(Ingredient $ingredient)
    {
        return $ingredient;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Ingredient  $ingredient
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Ingredient $ingredient)
    {
        $fields = [
            "name",
            "description",
            "in_stock",
            "stock_qty",
            "measure",
            "supplier_id",
        ];

        // Only assign valid fields to the model
        foreach ($fields as $field) {
            if ($request->has($field)) {
                $ingredient->{$field} = $request->get($field);
            }
        }

        $ingredient->save();
        return $ingredient;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Ingredient  $ingredient
     * @return \Illuminate\Http\Response
     */
    public function destroy(Ingredient $ingredient)
    {
        $id = $ingredient->id;
        $ingredient->delete();
        return ['id' => $id];
    }
}

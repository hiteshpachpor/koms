<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class BoxOrder extends JsonResource
{
    /**
     * Group all the ingredients into their recipes
     */
    public function getRecipes()
    {
        $recipes = [];

        if (!isset($this->recipes)) {
            return $recipes;
        }

        foreach ($this->recipes as $boxOrderRecipe) {
            $ingredient = [
                'id' => $boxOrderRecipe->ingredient_id,
                'name' => $boxOrderRecipe->ingredient_name,
                'measure' => $boxOrderRecipe->ingredient_measure,
                'amount' => $boxOrderRecipe->ingredient_amount,
            ];

            if (array_key_exists($boxOrderRecipe->recipe_id, $recipes)) {
                $recipes[$boxOrderRecipe->recipe_id][
                    'ingredients'
                ][] = $ingredient;
            } else {
                $recipes[$boxOrderRecipe->recipe_id] = [
                    'id' => $boxOrderRecipe->recipe_id,
                    'name' => $boxOrderRecipe->recipe_name,
                    'ingredients' => [$ingredient],
                ];
            }
        }

        return $recipes;
    }

    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        // Return if object is not set
        if (!isset($this->id)) {
            return [];
        }

        // Group all the ingredients into their recipes
        $recipes = $this->getRecipes();

        // Add it to the response
        $response['recipes'] = array_values($recipes);

        return [
            'id' => $this->id,
            'delivery_date' => $this->delivery_date,
            'delivery_slot' => $this->delivery_slot,
            'delivery_notes' => $this->delivery_notes,
            'user' => $this->user,
            'user_address' => $this->userAddress,
            'recipes' => $recipes,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}

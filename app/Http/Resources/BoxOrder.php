<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class BoxOrder extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $recipes = [];

        // Group all the ingredients into their recipes
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

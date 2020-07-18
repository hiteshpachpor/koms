<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RecipeIngredient extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'recipe_ingredient';

    /**
     * Get the ingredient info from the recipe ingredient mapping.
     */
    public function ingredient()
    {
        // recipe_ingredient maps to one ingredient, and ingredient.id = recipe_ingredient.ingredient_id
        return $this->hasOne('App\Ingredient', 'id', 'recipe_id');
    }
}

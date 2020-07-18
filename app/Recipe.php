<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Recipe extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'recipe';

    /**
     * Get the ingredient list associated with the recipe.
     */
    public function ingredientList()
    {
        // recipe has many ingredients, and recipe_ingredient.recipe_id = recipe.id
        return $this->hasMany('App\RecipeIngredient', 'recipe_id');
    }
}

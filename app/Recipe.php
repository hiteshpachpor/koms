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
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name', 'description'];

    /**
     * Get the ingredients associated with the recipe (nested).
     */
    public function ingredientList()
    {
        // recipe has many ingredients, and recipe_ingredient.recipe_id = recipe.id
        return $this->hasMany('App\RecipeIngredient', 'recipe_id');
    }

    /**
     * Get the ingredients associated with the recipe (direct).
     * 1. A recipe can have many ingredients.
     * 2. A one-to-many mapping between recipes and ingredients
     *    is defined in the recipe_ingredient table.
     * 3. Through this relationship, we can fetch all the ingredients
     *    that are associated with the recipe.
     */
    public function ingredients()
    {
        /**
         * The following statement in simple English:
         * A recipe has many ingredients (1st arg)
         * through the recipe_ingredient table (2nd arg),
         * and recipe_ingredient.recipe_id (3rd arg) = recipe.id (5th arg),
         * and recipe_ingredient.ingredient_id (6th arg) = ingredient.id (4th arg)
         */
        return $this->hasManyThrough(
            'App\Ingredient',
            'App\RecipeIngredient',
            'recipe_id',
            'id',
            'id',
            'ingredient_id'
        );
    }
}

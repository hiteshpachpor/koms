<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BoxOrderRecipe extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'box_order_recipe';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'box_order_id',
        'recipe_id',
        'recipe_name',
        'ingredient_id',
        'ingredient_name',
        'ingredient_measure',
        'ingredient_amount',
    ];

    /**
     * Get the recipe associated with the box.
     */
    public function recipe()
    {
        return $this->hasOne('App\Recipe', 'id', 'recipe_id');
    }
}

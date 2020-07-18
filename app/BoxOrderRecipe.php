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
     * Get the recipe associated with the box.
     */
    public function recipe()
    {
        return $this->hasOne('App\Recipe', 'id', 'recipe_id');
    }
}

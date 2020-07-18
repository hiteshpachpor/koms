<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Ingredient extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'ingredient';

    /**
     * Get the supplier associated with the ingredient.
     */
    public function supplier()
    {
        // ingredient has one supplier, and supplier.id = ingredient.supplier_id
        return $this->hasOne('App\Supplier', 'id', 'supplier_id');
    }
}

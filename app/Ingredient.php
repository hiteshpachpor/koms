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
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'description',
        'in_stock',
        'stock_qty',
        'measure',
        'supplier_id',
    ];

    /**
     * Get the supplier associated with the ingredient.
     */
    public function supplier()
    {
        // ingredient has one supplier, and supplier.id = ingredient.supplier_id
        return $this->hasOne('App\Supplier', 'id', 'supplier_id');
    }
}

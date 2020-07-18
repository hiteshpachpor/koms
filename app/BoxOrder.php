<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BoxOrder extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'box_order';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'user_address_id',
        'delivery_date',
        'delivery_slot',
        'delivery_notes',
    ];

    /**
     * Get the user who ordered this box.
     */
    public function user()
    {
        return $this->hasOne('App\User', 'id', 'user_id');
    }

    /**
     * Get the address of the user who ordered this box.
     */
    public function userAddress()
    {
        return $this->hasOne('App\UserAddress', 'id', 'user_address_id');
    }

    /**
     * Get the boxes associated with this order.
     */
    public function recipes()
    {
        return $this->hasMany('App\BoxOrderRecipe', 'box_order_id', 'id');
    }
}

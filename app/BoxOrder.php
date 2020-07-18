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
    public function phone()
    {
        return $this->hasMany('App\BoxOrderRecipe', 'box_order_id', 'id');
    }
}

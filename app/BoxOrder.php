<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Config;
use App\Recipe;
use App\BoxOrderRecipe;

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

    /**
     * Returns true if the requested delivery date & slot is valid.
     * Any delivery date in the past or 48 hours from now is not valid.
     *
     * @param String $date
     * @param String $slot
     */
    public static function isDeliveryDateSlotValid($date = null, $slot = null)
    {
        $availableDeliverySlots = Config::get(
            'constants.box_order_delivery_slot'
        );

        if (!$date) {
            $date = new \DateTime();
            $date = $date->format('Y-m-d');
        }

        if (!$slot) {
            $slot = $availableDeliverySlots[0];
        }

        $time;
        switch ($slot) {
            case $availableDeliverySlots[0]: // Morning
                $time = '06:00:00';
                break;
            case $availableDeliverySlots[1]: // Afternoon
                $time = '12:00:00';
                break;
            case $availableDeliverySlots[2]: // Evening
            default:
                $time = '18:00:00';
                break;
        }

        $dateTime = new \DateTime(implode(' ', [$date, $time]));
        $cannotOrderBefore = new \DateTime('+2 days');

        return $dateTime >= $cannotOrderBefore;
    }

    /**
     * Add a recipe to the box
     *
     * @param int $recipeId
     * @return this
     */
    public function addRecipe($recipeId)
    {
        $recipe = Recipe::with('ingredientList.ingredient')->find($recipeId);
        $ingredients = $recipe->ingredientList;

        foreach ($ingredients as $ingredient) {
            $boxOrderRecipe = new BoxOrderRecipe([
                'recipe_id' => $recipeId,
                'recipe_name' => $recipe->name,
                'ingredient_id' => $ingredient->ingredient->id,
                'ingredient_name' => $ingredient->ingredient->name,
                'ingredient_measure' => $ingredient->ingredient->measure,
                'ingredient_amount' => $ingredient->amount,
            ]);

            $this->recipes()->save($boxOrderRecipe);
        }

        return $this;
    }
}

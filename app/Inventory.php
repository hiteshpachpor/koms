<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\BoxOrder;
use Illuminate\Support\Facades\DB;

class Inventory extends Model
{
    /**
     * Returns the list of ingredients to be purchased
     * to fulfil box orders for the specified date range
     *
     * @param String|null $from
     * @param String|null $to
     */
    public static function purchaseOrder($from = null, $to = null)
    {
        if ($from) {
            $from = new \DateTimeImmutable($from);
        } else {
            $from = new \DateTimeImmutable('NOW');
        }

        if ($to) {
            $to = new \DateTimeImmutable($to);
        } else {
            $to = $from->add(new \DateInterval("P7D"));
        }

        $data = DB::table('box_order')
            ->join(
                'box_order_recipe',
                'box_order.id',
                '=',
                'box_order_recipe.box_order_id'
            )
            ->join(
                'recipe_ingredient',
                'box_order_recipe.recipe_id',
                '=',
                'recipe_ingredient.recipe_id'
            )
            ->join(
                'ingredient',
                'recipe_ingredient.ingredient_id',
                '=',
                'ingredient.id'
            )
            ->where('delivery_date', '>=', $from->format('Y-m-d'))
            ->where('delivery_date', '<=', $to->format('Y-m-d'))
            ->select(
                'ingredient.name',
                'ingredient.id',
                DB::raw(
                    'CAST(SUM(recipe_ingredient.amount) AS UNSIGNED) AS total_amount'
                )
            )
            ->groupBy('ingredient.id')
            ->get();

        return $data;
    }
}

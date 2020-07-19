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
            ->where('box_order.delivery_date', '>=', $from->format('Y-m-d'))
            ->where('box_order.delivery_date', '<=', $to->format('Y-m-d'))
            ->select(
                'box_order_recipe.ingredient_id AS id',
                DB::raw(
                    'ANY_VALUE(box_order_recipe.ingredient_name) AS ingredient'
                ),
                DB::raw(
                    'CAST(SUM(box_order_recipe.ingredient_amount) AS UNSIGNED) AS total_amount'
                ),
                DB::raw(
                    'ANY_VALUE(box_order_recipe.ingredient_measure) AS measure'
                )
            )
            ->groupBy('box_order_recipe.ingredient_id')
            ->get();

        return $data;
    }
}

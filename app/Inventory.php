<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\BoxOrder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;

class Inventory extends Model
{
    /**
     * MySQL 5.7.5 and up implements detection of functional dependence.
     * If the ONLY_FULL_GROUP_BY SQL mode is enabled (which it is by default),
     * MySQL rejects queries for which the select list, HAVING condition, or
     * ORDER BY list refer to nonaggregated columns that are neither named in the
     * GROUP BY clause nor are functionally dependent on them.
     *
     * MySQL recommends using ANY_VALUE() to get around this.
     * However, this is not supported by sqlite which is the test database.
     *
     * This method helps overcome this problem by identifying the database type
     * and returns a compatible string for the current database.
     *
     * @param String $database
     * @param String $field
     * @param String $alias
     * @return String
     */
    public static function databaseSpecificFieldCast($database, $field, $alias)
    {
        switch ($database) {
            case 'sqlite':
                return "{$field} AS {$alias}";
                break;

            case 'mysql':
            default:
                return "ANY_VALUE({$field}) AS {$alias}";
                break;
        }
    }

    /**
     * Returns the list of ingredients to be purchased
     * to fulfil box orders for the specified date range
     *
     * @param integer|null $supplierId
     * @param String|null $from
     * @param String|null $to
     */
    public static function purchaseOrder(
        $supplierId = null,
        $from = null,
        $to = null
    ) {
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

        $data = DB::table('box_order')->join(
            'box_order_recipe',
            'box_order.id',
            '=',
            'box_order_recipe.box_order_id'
        );

        if ($supplierId) {
            $data->join(
                'ingredient',
                'box_order_recipe.ingredient_id',
                '=',
                'ingredient.id'
            );
        }

        $data
            ->where('box_order.delivery_date', '>=', $from->format('Y-m-d'))
            ->where('box_order.delivery_date', '<=', $to->format('Y-m-d'));

        if ($supplierId) {
            $data->where('ingredient.supplier_id', $supplierId);
        }

        $database = Config('database.default');

        $data
            ->select(
                'box_order_recipe.ingredient_id AS id',
                DB::raw(
                    self::databaseSpecificFieldCast(
                        $database,
                        'box_order_recipe.ingredient_name',
                        'ingredient'
                    )
                ),
                DB::raw(
                    'CAST(SUM(box_order_recipe.ingredient_amount) AS UNSIGNED) AS total_amount'
                ),
                DB::raw(
                    self::databaseSpecificFieldCast(
                        $database,
                        'box_order_recipe.ingredient_measure',
                        'measure'
                    )
                )
            )
            ->groupBy('box_order_recipe.ingredient_id');

        return $data->get();
    }
}

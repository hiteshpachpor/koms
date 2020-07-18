<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Inventory;

class InventoryController extends Controller
{
    /**
     * Returns the list of ingredients to be purchased
     * to fulfil box orders for 7 days from the given `order_date`
     *
     * @param  String|null  $orderDate
     * @return \Illuminate\Http\Response
     */
    public function purchaseOrder($orderDate = null)
    {
        return Inventory::purchaseOrder($orderDate);
    }
}

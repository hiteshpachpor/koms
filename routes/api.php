<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::apiResources([
    'suppliers' => 'API\SupplierController',
    'ingredients' => 'API\IngredientController',
    'recipes' => 'API\RecipeController',
]);

Route::post('box/create', 'API\BoxOrderController@create');
Route::get('box/{id}', 'API\BoxOrderController@show');

Route::get(
    'purchase-order/{order_date?}',
    'API\InventoryController@purchaseOrder'
);

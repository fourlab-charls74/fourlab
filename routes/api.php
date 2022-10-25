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

// 상품관리
Route::get('prd01', 'head\product\prd01Controller@index');
Route::get('prd01/search', 'head\product\prd01Controller@search');
Route::get('prd01/{no}', 'head\product\prd01Controller@show');
Route::get('prd01/{no}/get', 'head\product\prd01Controller@get');
Route::get('prd01/{no}/in-qty', 'head\product\prd01Controller@show_in_qty');
Route::get('prd01/{no}/options', 'head\product\prd01Controller@options');
Route::get('prd01/{no}/goods-class', 'head\product\prd01Controller@goods_class');
Route::get('prd01/{no}/get-option-name', 'head\product\prd01Controller@get_option_name');
Route::get('prd01/{no}/get-option-stock', 'head\product\prd01Controller@get_option_stock');

Route::get('stk01/search', 'head\stock\stk01Controller@search');
Route::get('stk20/search', 'head\stock\stk20Controller@search');


Route::prefix("cafe24")->namespace('api\cafe24')->group(function () {

    Route::get('getToken', 'authController@getToken');

    Route::get('product/getItemList', 'productController@getItemList');
    Route::get('product/getBrandList', 'productController@getBrandList');
    Route::get('product/getCategoryList/{cat_type?}', 'productController@getCategoryList');
    Route::get('product/getItemCategoryList', 'productController@getItemCategoryList');
    Route::get('product/getCarrierList', 'productController@getCarrierList');
    Route::get('product/getOfficialList', 'productController@getOfficialList');

    Route::get('product/getProduct/{goods_no}', 'productController@getProduct');
    Route::any('product/register', 'productController@register');
    Route::any('product/modify/{goods_no}', 'productController@modify');

    Route::get('order/getOrderList', 'orderController@getOrderList');
    Route::get('order/getClaimList', 'orderController@getClaimList');
    Route::any('order/orderSetConfirmreceiving', 'orderController@orderSetComfirmreceiving');
    Route::any('order/orderSetShippinggeneral', 'orderController@orderSetShippinggeneral');
});

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

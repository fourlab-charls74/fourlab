<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
// 라우터 적용

Route::group(['middleware' => 'store','as' => 'store.', 'namespace' => 'store'], function() {

    //Route::Redirect("/","//dashboard");
    Route::get('/', 'IndexController@index');
    Route::get('/login', 'LoginController@index');
    //Route::get('/login', 'LoginController@index')->name('login');
    Route::post('/login', 'LoginController@login');
    Route::get('/logout', 'LoginController@logout');

    //코드관리
    Route::prefix("standard")->namespace('standard')->group(function () {
        //매장
        Route::get('std02', 'std02Controller@index');
        Route::get('std02/search', 'std02Controller@search');
        Route::get('std02/show/{com_id?}', 'std02Controller@show');
        Route::get('std02/getcate1/{com_id?}', 'std02Controller@getdisplaycategory');
        Route::get('std02/getcate2/{com_id?}', 'std02Controller@getitemcategory');
        Route::get('std02/addcate/{com_id?}', 'std02Controller@addcategory');
        Route::get('std02/delcate/{com_id?}', 'std02Controller@DelCategory');

        // 창고관리
        Route::get('std03', 'std03Controller@index');
        Route::get('std03/search', 'std03Controller@search');
        Route::get('std03/show/{storage_cd?}', 'std03Controller@show');
        Route::get('std03/dupcheck/{storage_cd?}', 'std03Controller@dupcheck_storage');

        Route::post('std03/add', 'std03Controller@add_storage');
        Route::put('std03/update', 'std03Controller@update_storage');
        Route::delete('std03/delete/{storage_cd?}', 'std03Controller@delete_storage');

        // 동종업계
        Route::get('std04', 'std04Controller@index');
        Route::get('std04/search', 'std04Controller@search');
        Route::get('std04/search-competitor/{store_cd?}', 'std04Controller@search_competitor');

        Route::put('std04/update-competitor', 'std04Controller@update_competitor');

        // 판매유형관리
        Route::get('std05', 'std05Controller@index');
        Route::get('std05/search', 'std05Controller@search');

        // 매장 영업담당자
        Route::get('std06', 'std06Controller@index');
        Route::get('std06/search', 'std06Controller@search');

        // 수선관리
        Route::get('std11', 'std11Controller@index');
        Route::get('std11/search', 'std11Controller@search');

        Route::get('std11/create', 'std11Controller@createIndex');
        Route::post('std11/create', 'std11Controller@create');

        Route::get('std11/detail/{idx?}', 'std11Controller@detail');
        Route::post('std11/edit', 'std11Controller@edit');
        Route::post('std11/remove', 'std11Controller@remove');

        //코드
        Route::get('std51', 'std51Controller@index');
        Route::post('std51', 'std51Controller@store');
        Route::get('std51/search', 'std51Controller@search');
        Route::get('std51/create', 'std51Controller@create');
        Route::get('std51/{code?}', 'std51Controller@show');
        Route::put('std51/{code?}', 'std51Controller@update');
        Route::delete('std51/{code}', 'std51Controller@delete');

        Route::get('std51/{code?}/search', 'std51Controller@data_search');
        Route::post('std51/{code?}/save', 'std51Controller@data_add');
        Route::post('std51/{code?}/del', 'std51Controller@data_del');
        Route::post('std51/{code?}/seq', 'std51Controller@data_seq');

    });

    // 상품관리
    Route::prefix("product")->namespace('product')->group(function () {
        Route::get('prd02','prd02Controller@index');
        Route::get('prd02/search','prd02Controller@search');
    });


    //매장관리
    Route::prefix("sale")->namespace('sale')->group(function () {
        Route::get('sal01','sal01Controller@index');
        Route::get('sal01/search','sal01Controller@search');
        Route::get('sal01/show','sal01Controller@show');
        Route::post('sal01/show','sal01Controller@update');
        Route::post('sal01/upload',	'sal01Controller@upload');

        Route::get('sal02','sal02Controller@index');
        Route::get('sal03','sal03Controller@index');
        Route::get('sal04','sal04Controller@index');
        Route::get('sal05','sal05Controller@index');
        Route::get('sal06','sal06Controller@index');
        Route::get('sal07','sal07Controller@index');

        Route::get('sal11','sal11Controller@index');
        Route::get('sal12','sal12Controller@index');
        Route::get('sal13','sal13Controller@index');
    });

    //매장관리
    Route::prefix("stock")->namespace('stock')->group(function () {
        Route::get('stk01','stk01Controller@index');
    });

});

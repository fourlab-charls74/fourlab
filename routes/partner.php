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

Route::group(['middleware' => 'partner', 'as' => 'partner.', 'namespace' => 'partner'], function() {

    //Route::get('/', 'IndexController@index');
    Route::Redirect("/","/partner/dashboard");

    Route::get('/login', 'LoginController@index')->name('login');
    Route::post('/login', 'LoginController@login');
    Route::get('/logout', 'LoginController@logout');

    Route::get('/menu', 'UserController@menu');

    Route::get('/dashboard', 'dsh01Controller@index');
    Route::get('/dashboard/search', 'dsh01Controller@search');

    Route::prefix("user")->group(function () {
        Route::get('/', 'UserController@index');
        Route::put('/', 'UserController@update');
        Route::get('/category/{type}', 'UserController@category');
    });

    Route::prefix("auto-complete")->group(function () {
        Route::get('/template', 'AutoCompleteController@template');
        Route::get('/style-no', 'AutoCompleteController@style_no');
        Route::get('/brand', 'AutoCompleteController@brand');
        Route::get('/goods-nm', 'AutoCompleteController@goods_nm');
        Route::get('/category', 'AutoCompleteController@category');
    });

    Route::prefix("promotion")->namespace('promotion')->group(function () {

        Route::get('prm02', 'prm02Controller@index');
        Route::post('prm02', 'prm02Controller@store');
        Route::get('prm02/search', 'prm02Controller@search');
        Route::get('prm02/create', 'prm02Controller@create');
        Route::get('prm02/{no}', 'prm02Controller@show');
        Route::put('prm02/{no}', 'prm02Controller@update');
        Route::delete('prm02/{no}', 'prm02Controller@destroy');
    });

    Route::prefix("support")->namespace('support')->group(function () {
        Route::get('spt01', 'spt01Controller@index');
        Route::get('spt01/search', 'spt01Controller@search');
        Route::get('spt01/{no}', 'spt01Controller@show');

        // Q&A
        Route::get('spt02', 'spt02Controller@index');
        Route::get('spt02/search', 'spt02Controller@search');
        Route::get('spt02/create', 'spt02Controller@create');
        Route::post('spt02/store', 'spt02Controller@store');
        Route::get('spt02/{idx}', 'spt02Controller@show');
        Route::prefix("spt02/show")->group(function () {
            Route::post('save_reply', 'spt02Controller@saveReply');
            Route::delete('remove_qna', 'spt02Controller@removeQna');
        });
    });

    Route::prefix("order")->namespace('order')->group(function () {

        // 주문내역
        Route::get('ord01', 'ord01Controller@index');
        Route::get('ord01/search', 'ord01Controller@search');
        Route::get('ord01/{ord_no}', 'ord01Controller@show');
        Route::get('ord01/{ord_no}/{ord_opt_no}', 'ord01Controller@show');
        Route::post('ord01/claim/comments', 'ord01Controller@claim_comments_store');
        Route::get('ord01/dlv/{ord_no}/{ord_opt_no}', 'ord01Controller@dlv');
        Route::put('ord01/dlv-info-save/{ord_no}', 'ord01Controller@dlv_info_save');

        //배송출고요청
        Route::get('ord21', 'ord21Controller@index');
        Route::get('ord21/search', 'ord21Controller@search');
        Route::put('ord21/update/state', 'ord21Controller@update_state');
        Route::put('ord21/update/kind', 'ord21Controller@update_kind');

        //배송출고처리
        Route::prefix("ord22")->group(function () {
            $cont = 'ord22Controller';
            Route::get('/', $cont . '@index');
            Route::get('show', $cont . '@show');
            Route::get('search', $cont . '@search');
            Route::get('dlv-import', $cont . '@dlv_import');
            Route::get('dlv-import/search', $cont . '@dlv_import_search');
            Route::get('download/delivery_list', $cont . '@download_delivery_list');
            Route::get('download/baesong_list', $cont . '@download_baesong_list');

            Route::put('kind', $cont . '@update_kind');
            Route::put('state', $cont . '@update_state');
            Route::put('out-complete', $cont . '@out_complete');
            Route::put('dlv-import/upload', $cont . '@dlv_import_upload');
        });

        // 일별 주문 통계 검색
        Route::get('ord71', 'ord71Controller@index');
        Route::get('ord71/search', 'ord71Controller@search');

        // 월별 주문 통계 검색
        Route::get('ord72', 'ord72Controller@index');
        Route::get('ord72/search', 'ord72Controller@search');

        //뱅크다
        //Route::get('ord14', 'ord14Controller@index');
        //Route::get('ord14/search', 'ord14Controller@search');
        //Route::get('ord14/{code?}', 'ord14Controller@show');

    });

    Route::prefix("product")->namespace('product')->group(function () {

        // 상품관리
        Route::get('prd01', 'prd01Controller@index');
        Route::get('prd01/choice', 'prd01Controller@index_choice');
        Route::get('prd01/search', 'prd01Controller@search');
        Route::get('prd01/create', 'prd01Controller@create');

        Route::match(['get', 'post'], 'prd01/edit', 'prd01Controller@edit_index');
        Route::post('prd01/edit/search', 'prd01Controller@edit_search');
        Route::post('prd01/edit/save', 'prd01Controller@edit_save');

        Route::get('prd01/{no}', 'prd01Controller@show');
        Route::get('prd01/{no}/get', 'prd01Controller@get');
        Route::get('prd01/{no}/get-addinfo', 'prd01Controller@get_addinfo');
        Route::get('prd01/{no}/in-qty', 'prd01Controller@show_in_qty');
        Route::get('prd01/{no}/options', 'prd01Controller@options');
        Route::get('prd01/{no}/goods-class', 'prd01Controller@goods_class');
        Route::get('prd01/{no}/get-option-stock', 'prd01Controller@get_option_stock');
        Route::get("prd01/{no}/get-similar-goods", "prd01Controller@get_similar_goods");
        Route::get("prd01/{no}/goods-cont", "prd01Controller@index_cont");
        Route::get("prd01/{no}/search/sale-place-cont", "prd01Controller@search_sale_place_cont");

        Route::post('prd01', 'prd01Controller@create_goods');

        Route::put('prd01', 'prd01Controller@update');
        Route::put('prd01/{no}/in-qty', 'prd01Controller@update_in_qty');
        Route::put('prd01/update/state', 'prd01Controller@update_state');
        Route::put('prd01/update/qty', 'prd01Controller@update_qty');
        Route::put('prd01/goods-class-update', 'prd01Controller@goods_class_update');
        Route::put('prd01/goods-class-delete', 'prd01Controller@goods_class_delete');
        Route::put('prd01/{no}/save/sale-place-cont', 'prd01Controller@save_sale_place_cont');

        Route::put('prd01/goods-class-opt-update', 'prd01Controller@goods_class_opt_update');
        Route::put('prd01/goods-class-update', 'prd01Controller@goods_class_update');
        Route::put('prd01/goods-class-delete', 'prd01Controller@goods_class_delete');
        Route::put('prd01/{no}/save/sale-place-cont', 'prd01Controller@save_sale_place_cont');

        Route::post("prd01/{no}/planing-delete", 'prd01Controller@delete_planing');
        Route::post("prd01/{no}/coupon-delete", 'prd01Controller@delete_coupon');

        Route::post("prd01/update", 'prd01Controller@update_selected');

        // 옵션 관리
        Route::get('prd01/{no}/get-option-name', 'prd01Controller@get_option_name');
        Route::post('prd01/get-option-stock', 'prd01Controller@get_option_stock');
        Route::post("prd01/{no}/option-kind-add", "prd01Controller@add_option_kind");
        Route::post("prd01/{no}/option-kind-del", "prd01Controller@del_option_kind");

        Route::get("prd01/{no}/get-basic-options", "prd01Controller@getBasicOptions");
        Route::get("prd01/{no}/get-basic-opts-matrix", "prd01Controller@getBasicOptsMatrix");
        Route::post("prd01/{no}/save-basic-options", "prd01Controller@saveBasicOptions");
        Route::post("prd01/{no}/delete-basic-options", "prd01Controller@deleteBasicOptions");

        Route::post("prd01/get-extra-options", "prd01Controller@getExtraOptions");
        Route::post("prd01/{no}/update-basic-opts-data", "prd01Controller@updateBasicOptsData");
        Route::post("prd01/update-extra-opts-data", "prd01Controller@updateExtraOptsData");
        Route::get('prd01/{no}/stock', 'prd01Controller@stock');
        Route::post('prd01/stock-in', 'prd01Controller@stockIn');

        Route::get("prd01/{no}/get-similar-goods", "prd01Controller@get_similar_goods");
        Route::post("prd01/{no}/similar-goods-add", "prd01Controller@save_similar_goods");
        Route::delete("prd01/{no}/similar-goods-del", "prd01Controller@delete_similar_goods");

		// 관련 상품
		Route::post('prd01/add-related-goods', 'prd01Controller@addRelatedGoods');
		Route::post('prd01/del-related-good', 'prd01Controller@delRelatedGood');
		
        // 이미지 관리
        Route::post('prd02/{idx}/upload', 'prd02Controller@upload');
        Route::get('prd02/{no}/image', 'prd02Controller@index');
        Route::get("prd02/slider", "prd02Controller@index_slider");

        Route::get('prd03', 'prd03Controller@index');
        Route::get('prd03/search', 'prd03Controller@search');
        Route::post('prd03/save', 'prd03Controller@save');
        Route::get('prd03/{no}/get', 'prd03Controller@get');


        Route::get('prd05', 'prd05Controller@index');
        Route::get('prd05/search', 'prd05Controller@search');
        Route::get('prd05/column_search', 'prd05Controller@column_search');

        Route::get('prd05/delete', 'prd05Controller@delete');
        Route::get('prd05/update', 'prd05Controller@update');
        Route::post('prd05/load_excel', 'prd05Controller@load_excel');
        Route::get('prd05/show_excel', 'prd05Controller@show_excel');
        Route::get('prd05/down_excel', 'prd05Controller@down_excel');

        Route::get('prd06', 'prd06Controller@index');
        Route::post('prd06', 'prd06Controller@store');
        Route::post('prd06/bundle', 'prd06Controller@store_bundle');

        Route::get('prd07', 'prd07Controller@index');
        Route::get('prd07/search', 'prd07Controller@search');
        Route::put('prd07/update', 'prd07Controller@update');

        // 상품이미지 일괄등록
        Route::get('prd08', 'prd08Controller@index');
        Route::get('prd08/goods-info/goods-no', 'prd08Controller@get_goods_info_by_goodsno');
        Route::get('prd08/goods-info/style-no', 'prd08Controller@get_goods_info_by_styleno');
        Route::put('prd08/upload', 'prd08Controller@upload_images');

        Route::get('prd09', 'prd09Controller@index');

        Route::get('prd10', 'prd10Controller@index');
        Route::get('prd10/search', 'prd10Controller@search');
        Route::get('prd10/prd_search', 'prd10Controller@prd_search');

    });

    Route::prefix("stock")->namespace('stock')->group(function () {
        // 재고관리
        Route::get('stk01', 'stk01Controller@index');
        Route::get('stk01/search', 'stk01Controller@search');
        Route::get('stk01/{goods_no}/{goods_opt?}', 'stk01Controller@show');
        Route::get('stk01/option/search/{goods_no}', 'stk01Controller@show_search');
        Route::post('stk01', 'stk01Controller@update');
    });

    Route::prefix("cs")->namespace('cs')->group(function () {

        //클레임 내역
        Route::get('cs01', 'cs01Controller@index');
        Route::get('cs01/search', 'cs01Controller@search');
        Route::get('cs01/popup', 'cs01Controller@popup');

        //상품 Q/A
        Route::get('cs02', 'cs02Controller@index');
        Route::get('cs02/search', 'cs02Controller@search');
        Route::post('cs02/{no}', 'cs02Controller@store');
        Route::put('cs02/{no}', 'cs02Controller@update');
        Route::delete('cs02/{no}', 'cs02Controller@destroy');

        Route::get('cs02/show/template', 'cs02Controller@template');
        Route::get('cs02/show/template/{no}', 'cs02Controller@template_msg');
        Route::get('cs02/show/{no}', 'cs02Controller@show');
        Route::post('cs02/show/check', 'cs02Controller@check');

        //일별 클레임 통계
        Route::get('cs71', 'cs71Controller@index');
        Route::get('cs71/search', 'cs71Controller@search');
        Route::get('cs71/popup', 'cs71Controller@popup');

        //월별 클레임 통계
        Route::get('cs72', 'cs72Controller@index');
        Route::get('cs72/search', 'cs72Controller@search');
        
    });

    Route::prefix("sales")->namespace('sales')->group(function () {
        //일별 매출 통계
        Route::get('sal02', 'sal02Controller@index');
        Route::get('sal02/search', 'sal02Controller@search');
        Route::get('sal02/popup', 'sal02Controller@popup');

        //월별 매출 통계
        Route::get('sal03', 'sal03Controller@index');
        Route::get('sal03/search', 'sal03Controller@search');

        //상품별 매출 통계
        Route::get('sal04', 'sal04Controller@index');
        Route::get('sal04/search', 'sal04Controller@search');
    });

    Route::prefix("api")->namespace('api')->group(function () {
        //스타일 넘버 자동 완성 리스트
        Route::get('autocomplete/get_style_no/{search_str}', 'autocomplete@get_style_no');

        //상품이름 검색 자동완성 리스트
        Route::get('autocomplete/get_goods_nm/{search_str}', 'autocomplete@get_goods_nm');

        //brand 리스트
        Route::get('brand/get_brand_nm', 'brand@get_brand_nm');
        Route::get('brand/getlist', 'brand@getlist');

        //업체 검색
        Route::get('company/getlist', 'company@getlist');

        //category 리스트
        Route::get('category/get_category_list/{cat_type}', 'category@get_category_list');
        Route::get('category/getlist/{cat_type}', 'category@getlist');

        Route::get('category/get_category_by_goods_no/{cat_type}/{goods_no}/{goods_sub}', 'category@get_category_by_goods_no');

        //claim 등록
        Route::post('claim/insert', 'claim@store');

        Route::get('goods', 'goods@search');
        Route::get('goods/show', 'goods@show');
        Route::get('goods/show/file/search', 'goods@file_search');

    });

    Route::prefix("settle")->namespace('settle')->group(function () {
        //정산내역
        Route::get('stl01', 'stl01Controller@index');
        Route::get('stl01/search', 'stl01Controller@search');
        Route::get('stl01/{idx}', 'stl01Controller@detail');
        Route::get('stl01/detail_search/{idx}', 'stl01Controller@detail_search');

        //마감내역
        //Route::get('stl02', 'stl02Controller@index');
        //Route::get('stl02/search', 'stl02Controller@search');
    });

});

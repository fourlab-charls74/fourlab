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
    Route::get('/main', 'IndexController@main');
    Route::get('/main_alarm', 'IndexController@main_alarm');
    // Route::get('/search', 'IndexController@search');
    Route::get('/login', 'LoginController@index');
    //Route::get('/login', 'LoginController@index')->name('login');
    Route::post('/login', 'LoginController@login');
    Route::get('/logout', 'LoginController@logout');
    Route::get('/user/log', 'UserController@log');
    Route::get('/user/log_search', 'UserController@searchlog');

    Route::prefix("auto-complete")->group(function () {

        // 매장명 조회 (자동완성)
        Route::get('/store', 'AutoCompleteController@store');
       
        // 창고명 조회 (자동완성)
        Route::get('/storage', 'AutoCompleteController@storage');

    });

    Route::prefix("api")->namespace('api')->group(function () {

        // 상품검색
        Route::get('goods', 'goods@search');
        Route::get('goods/show', 'goods@show');
        Route::get('goods/show/file/search', 'goods@file_search');

        // 고객명 조회
        Route::get('members', 'MemberController@show');
        Route::get('members/search', 'MemberController@search');

        // 관리자 (담당자MD) 조회
        Route::get('mds/search', 'AdminController@search');

        //category 리스트
        Route::get('category/{cat_type}', 'category@index');
        Route::get('category/get_category_list/{cat_type}', 'category@get_category_list');
        Route::get('category/getlist/{cat_type}', 'category@getlist');
        Route::get('category/get_category_by_goods_no/{cat_type}/{goods_no}/{goods_sub}', 'category@get_category_by_goods_no');

        // 매장명 조회
        Route::get('stores', 'StoreController@show');
        Route::get('stores/search', 'StoreController@search');
        Route::get('stores/search-storechannel', 'StoreController@search_storeChannel');
        Route::get('stores/search-storechannelkind', 'StoreController@search_storeChannelKind');
        Route::post('stores/search-storenm', 'StoreController@search_storenm');
        Route::post('stores/search-storenm-from-type', 'StoreController@search_storenm_from_type');
        Route::get('stores/search-store-info/{store_cd?}', 'StoreController@search_store_info');
        
        // 창고명 조회
        Route::get('storage', 'StoreController@storage_show');
        Route::get('storage/search', 'StoreController@storage_search');
        Route::post('storage/search-storagenm', 'StoreController@search_storagenm');
        Route::post('storage/search-storagenm-from-type', 'StoreController@search_storagenm_from_type');
        Route::get('storage/search-storage-info/{storage_cd?}', 'StoreController@search_storage_info');

        // 상품코드 조회
        Route::get('prdcd/conds', 'goods@search_product_conditions');
        Route::get('prdcd/search', 'goods@search_prdcd');
        Route::get('prdcd/conds_code', 'goods@search_product_conditions_code');
        Route::get('prdcd/search_code', 'goods@search_prdcd_code');
        
        // 원부자재코드 조회
        Route::get('prdcd/conds_sub', 'goods@search_product_sub_conditions');
        Route::get('prdcd/search_sub', 'goods@search_prdcd_sub');

        // 코드일련 조회
        Route::get('product/color', 'goods@search_color'); // 코드일련으로 해당 상품의 컬러옵션 리스트 조회
        Route::get('prdcd/search_p', 'goods@search_prdcd_p');

        // sms
        Route::get('sms/{type}', 'SmsController@index');
        Route::get('sms/search/member', 'SmsController@search_member');

        //판매유형검색
        Route::get('sale/search_sell_type', 'goods@search_sell_type');
        
        //행사코드검색
        Route::get('sale/search_prcode', 'goods@search_prcode');
    });

    // 포스
    Route::prefix("pos")->namespace('pos')->group(function () {
        Route::get('', 'PosController@index');
        Route::get('search/{cmd?}', 'PosController@search_command');
        Route::post('save', 'PosController@save');
        Route::post('add-member', 'PosController@add_member');
        Route::delete('remove-waiting', 'PosController@remove_waiting');
    });

    //코드관리
    Route::prefix("standard")->namespace('standard')->group(function () {
        //매장
        Route::get('std02', 'std02Controller@index');
        Route::get('std02/search', 'std02Controller@search');
        Route::get('std02/show/{store_cd?}', 'std02Controller@show');
        Route::post('std02/show/chg-store-channel/{store_cd?}', 'std02Controller@change_store_channel');
        Route::post('std02/show/chg-store-channel_kind/{store_cd?}', 'std02Controller@change_store_channel_kind');
        Route::get('std02/charge/{store_cd?}', 'std02Controller@charge');
        Route::get('std02/charge_search', 'std02Controller@charge_search');
        Route::get('std02/check-code/{storage_cd?}', 'std02Controller@check_code');
        Route::post('std02/update', 'std02Controller@update_store');
        Route::post('std02/del_img', 'std02Controller@del_img');
        Route::post('std02/create-store-cd', 'std02Controller@create_store_cd');


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
        Route::get('std05/show/{sale_type_cd?}', 'std05Controller@show');
        Route::get('std05/search-store/{sale_type_cd?}', 'std05Controller@search_store');
        Route::get('std05/search-brand/{sale_type_cd?}', 'std05Controller@search_brand');
        Route::get('std05/check-code/{sale_type_cd?}', 'std05Controller@check_code');

        Route::post('std05/add', 'std05Controller@add_sale_type');
        Route::put('std05/update', 'std05Controller@update_sale_type');

        // 매장 영업담당자
        Route::get('std06', 'std06Controller@index');
        Route::get('std06/search', 'std06Controller@search');

        // 매장마진관리
        Route::get('std07', 'std07Controller@index');
        Route::get('std07/search', 'std07Controller@search');
        Route::get('std07/search-store-fee/{store_cd?}', 'std07Controller@search_store_fee');
        Route::get('std07/show/{store_cd?}/{pr_code_cd?}', 'std07Controller@show');
        Route::get('std07/search-store-fee-history', 'std07Controller@search_store_fee_history');
        Route::put('std07/update-store-fee', 'std07Controller@update_store_fee');
        Route::delete('std07/remove-store-fee/{fee_idx?}', 'std07Controller@remove_store_fee');

        // 매장등급관리
        Route::get('std08', 'std08Controller@index');
        Route::get('std08/search', 'std08Controller@search');
        Route::post('std08/save', 'std08Controller@save');
        Route::post('std08/remove', 'std08Controller@remove');
        Route::get('std08/choice', 'std08Controller@choice_index');

        //판매채널관리
        Route::get('std09', 'std09Controller@index');
        Route::get('std09/show/{code?}/{type?}/{idx?}', 'std09Controller@show');
        Route::get('std09/search', 'std09Controller@search');
        Route::post('std09/save', 'std09Controller@save');
        Route::post('std09/edit', 'std09Controller@edit');
        Route::get('std09/search-store-type/{store_channel_cd?}', 'std09Controller@search_store_type');
        Route::post('std09/delete', 'std09Controller@delete');
        Route::post('std09/delete-channel', 'std09Controller@delete_channel');
        Route::get('std09/check-code/{code?}/{add_type?}', 'std09Controller@check_code');

        //사이즈관리
        Route::get('std10', 'std10Controller@index');
        Route::post('std10/save', 'std10Controller@save');
        Route::get('std10/search', 'std10Controller@search');
        Route::get('std10/create', 'std10Controller@create');
        Route::get('std10/{code?}', 'std10Controller@show');
        Route::put('std10/{code?}', 'std10Controller@update');
        Route::delete('std10/{code}', 'std10Controller@delete');

        Route::get('std10/{code?}/search', 'std10Controller@size_search');
        Route::post('std10/{code?}/save', 'std10Controller@size_add');
        Route::post('std10/{code?}/mod', 'std10Controller@size_mod');
        Route::post('std10/{code?}/del', 'std10Controller@size_del');
        Route::post('std10/{code?}/seq', 'std10Controller@size_seq');
        Route::post('std10/{code?}/change-yn', 'std10Controller@change_yn');

        // 수선관리
        Route::get('std11', 'std11Controller@index');
        Route::get('std11/search', 'std11Controller@search');
        Route::post('std11/batch-edit', 'std11Controller@batchEdit');
        Route::post('std11/repair-info-save', 'std11Controller@save');
        Route::post('std11/change_state', 'std11Controller@change_state');
        Route::post('std11/change_state2', 'std11Controller@change_state2');
        Route::get('std11/create', 'std11Controller@showCreate');
        Route::post('std11/create', 'std11Controller@create');
        Route::get('std11/view/{idx?}', 'std11Controller@view');
        Route::post('std11/delete', 'std11Controller@delete');

        Route::get('std11/detail/{idx?}', 'std11Controller@showDetail');
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
        Route::post('std51/{code?}/mod', 'std51Controller@data_mod');
        Route::post('std51/{code?}/del', 'std51Controller@data_del');
        Route::post('std51/{code?}/seq', 'std51Controller@data_seq');
        Route::post('std51/{code?}/change-yn', 'std51Controller@change_yn');

    });

    // 상품관리
    Route::prefix("product")->namespace('product')->group(function () {
        Route::get('prd01', 'prd01Controller@index');
        Route::get('prd01/choice', 'prd01Controller@index_choice');
        Route::get('prd01/search', 'prd01Controller@search');
        Route::get('prd01/create', 'prd01Controller@create');
        Route::post('prd01/cleanup-trash', 'prd01Controller@cleanup_trash');

        // 상품관리 - 일괄수정
        Route::match(['get', 'post'], 'prd01/edit', 'prd01Controller@edit_index');
        Route::post('prd01/edit/search', 'prd01Controller@edit_search');
        Route::post('prd01/edit/save', 'prd01Controller@edit_save');

        Route::get('prd01/{no}', 'prd01Controller@show');
        Route::get('prd01/{no}/get', 'prd01Controller@get');
        Route::get('prd01/{no}/get-addinfo', 'prd01Controller@get_addinfo');
        Route::get('prd01/{no}/in-qty', 'prd01Controller@show_in_qty');
        Route::get('prd01/{no}/options', 'prd01Controller@options');
        Route::get('prd01/{no}/goods-class', 'prd01Controller@goods_class');
        Route::get("prd01/{no}/get-similar-goods", "prd01Controller@get_similar_goods");
        Route::post("prd01/{no}/similar-goods-add", "prd01Controller@save_similar_goods");
        Route::delete("prd01/{no}/similar-goods-del", "prd01Controller@delete_similar_goods");

        Route::get("prd01/{no}/goods-cont", "prd01Controller@index_cont");
        Route::get("prd01/{no}/search/sale-place-cont", "prd01Controller@search_sale_place_cont");

        Route::post('prd01', 'prd01Controller@create_goods');

        Route::put('prd01', 'prd01Controller@update');
        Route::put('prd01/{no}/in-qty', 'prd01Controller@update_in_qty');
        Route::put('prd01/update/state', 'prd01Controller@update_state');
        Route::put('prd01/update/qty', 'prd01Controller@update_qty');

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

        // 관련 상품
        Route::post('prd01/add-related-goods', 'prd01Controller@addRelatedGoods');
        Route::post('prd01/del-related-good', 'prd01Controller@delRelatedGood');

        Route::get('prd02','prd02Controller@index');
        Route::get('prd02/search','prd02Controller@search');
        Route::get('prd02/create', 'prd02Controller@create');

        Route::get('prd02/create', 'prd02Controller@create');
        Route::get('prd02/prd-search', 'prd02Controller@prd_search');
        Route::get('prd02/prd-search-code', 'prd02Controller@prd_search_code');
        Route::put('prd02/add-product-code', 'prd02Controller@add_product_code');
        Route::put('prd02/add-product-product', 'prd02Controller@add_product_product');

        Route::get('prd02/edit-goods-no/{product_code}/{goods_no}/', 'prd02Controller@edit_goods_no');
        Route::get('prd02/edit-goods-no/{product_code}', 'prd02Controller@match_goods_no');
        Route::get('prd02/prd-edit-search', 'prd02Controller@prd_edit_search');
        Route::get('prd02/prd-edit-match-search', 'prd02Controller@prd_edit_match_search');
        Route::put('prd02/del-product-code', 'prd02Controller@del_product_code');
        Route::put('prd02/edit-match-product-code', 'prd02Controller@edit_match_product_code');

        Route::get('prd02/batch', 'prd02Controller@batch_show');
        Route::post('prd02/batch-import', 'prd02Controller@import_excel');
        Route::get('prd02/batch-create', 'prd02Controller@batch_create');
        Route::post('prd02/upload', 'prd02Controller@upload');
        Route::put('prd02/show', 'prd02Controller@update');

        Route::get('prd02/product_upload', 'prd02Controller@product_upload');
        Route::post('prd02/change-size', 'prd02Controller@change_size');
        Route::post('prd02/get-seq', 'prd02Controller@getSeq');
        Route::post('prd02/save_product', 'prd02Controller@save_product');
        Route::post('prd02/update_product', 'prd02Controller@update_product');
        Route::post('prd02/del-img', 'prd02Controller@delImg');
        Route::post('prd02/sel_seq', 'prd02Controller@selSeq');
        Route::post('prd02/change_seq', 'prd02Controller@changeSeq');
        Route::post('prd02/batch-getproducts','prd02Controller@get_products'); 
        Route::post('prd02/batch-products','prd02Controller@batch_products'); 

        Route::get('prd02/create_barcode', 'prd02Controller@create_barcode');
        Route::post('prd02/dup-style-no', 'prd02Controller@dup_style_no');

        // 이미지 관리
        Route::post('prd02/{idx}/upload', 'prd02Controller@upload');
        Route::get('prd02/{no}/image', 'prd02Controller@index');
        Route::get("prd02/slider", "prd02Controller@index_slider");

        // 원부자재 상품 관리
        Route::get('prd03','prd03Controller@index');
        Route::get('prd03/search','prd03Controller@search');

        Route::get('prd03/create', 'prd03Controller@showCreate');
        Route::post('prd03/get-seq', 'prd03Controller@getSeq');
        Route::post('prd03/change-gender', 'prd03Controller@change_gender');
        Route::post('prd03/create', 'prd03Controller@create');

        Route::get('prd03/{type}/{product_code}','prd03Controller@showAndEdit');
        Route::post('prd03/edit','prd03Controller@edit');

        Route::get('prd03/delete/{product_code}','prd03Controller@delete');
        Route::post('prd03/del-img', 'prd03Controller@delImg');

        // 상품재고관리
        Route::get('prd04','prd04Controller@index');
        Route::get('prd04/search','prd04Controller@search');
        Route::get('prd04/stock','prd04Controller@show_stock');
        Route::get('prd04/stock/search','prd04Controller@search_stock');

        Route::get('prd04/batch', 'prd04Controller@batch');
        Route::post('prd04/upload',	'prd04Controller@upload');
        Route::put('prd04/batch', 'prd04Controller@update');

        Route::get('prd04/batch_wonga', 'prd04Controller@batch_wonga');
        Route::post('prd04/upload_wonga', 'prd04Controller@upload_wonga');
        Route::put('prd04/batch_wonga', 'prd04Controller@update_wonga');

        Route::get('prd04/batch_store', 'prd04Controller@batch_store');
        Route::post('prd04/upload_store', 'prd04Controller@upload_store');
        Route::put('prd04/batch_store', 'prd04Controller@update_store');

        // 상품가격 관리
        Route::get('prd05','prd05Controller@index');
        Route::get('prd05/search','prd05Controller@search');
        Route::get('prd05/show-search','prd05Controller@show_search');
        Route::get('prd05/view-search','prd05Controller@view_search');
        Route::get('prd05/show/{code?}','prd05Controller@show');
        Route::get('prd05/view/{code?}','prd05Controller@view');
        Route::put('prd05/change-price', 'prd05Controller@change_price');
        Route::put('prd05/change-price-now', 'prd05Controller@change_price_now');
        Route::put('prd05/update-price', 'prd05Controller@update_price');
        Route::put('prd05/update-price-now', 'prd05Controller@update_price_now');
        Route::put('prd05/del-product-price', 'prd05Controller@del_product_price');
        Route::put('prd05/del-product', 'prd05Controller@del_product');

        // 온라인 재고 매핑
        Route::get('prd06', 'prd06Controller@index');
        Route::get('prd06/search', 'prd06Controller@search');
        Route::get('prd06/create', 'prd06Controller@create');
        Route::get('prd06/search_store', 'prd06Controller@search_store');
        Route::get('prd06/search_prd', 'prd06Controller@search_product');
        Route::post('prd06/save', 'prd06Controller@save');
        Route::put('prd06/prd_update', 'prd06Controller@prd_update');
        Route::put('prd06/prd_delete', 'prd06Controller@prd_delete');
        Route::get('prd06/prd_add', 'prd06Controller@add_show');
        Route::post('prd06/prd_add', 'prd06Controller@add_save');

        // 상품관리 - 일괄등록
        Route::get('prd07', 'prd07Controller@index');
        Route::post('prd07/enroll', 'prd07Controller@enroll');
        Route::post('prd07/enroll2', 'prd07Controller@enroll2');
        Route::get('prd07/batch', 'prd07Controller@batch_show');
        Route::post('prd07/batch-import', 'prd07Controller@import_excel');
        Route::post('prd07/batch-getproducts','prd07Controller@get_products');
        Route::post('prd07/batch-products','prd07Controller@batch_products');

        // 이미지 관리
        Route::post('prd08/{idx}/upload', 'prd08Controller@upload');
        Route::get('prd08/{no}/image', 'prd08Controller@index');

        // 상품이미지 일괄등록
        Route::get('prd23', 'prd23Controller@index');
        Route::get('prd23/goods-info/goods-no', 'prd23Controller@get_goods_info_by_goodsno');
        Route::get('prd23/goods-info/style-no', 'prd23Controller@get_goods_info_by_styleno');
        Route::put('prd23/upload', 'prd23Controller@upload_images');
    });

    // 생산입고관리
    Route::prefix("cs")->namespace('cs')->group(function () {

        // 입고
        Route::get('cs01','cs01Controller@index');
        Route::get('cs01/choice','cs01Controller@choice_index'); // 송장번호 선택 팝업 index
        Route::get('cs01/search','cs01Controller@search');
        Route::get('cs01/show','cs01Controller@show');
        Route::post('cs01/comm', 'cs01Controller@command');
        
        // 상품반품
        Route::get('cs02', 'cs02Controller@index');
        Route::get('cs02/search', 'cs02Controller@search');
        Route::get('cs02/show/{sgr_cd?}','cs02Controller@show');
        Route::put('cs02/update-return-state','cs02Controller@update_return_state'); // 상품반품이동 상태변경
        Route::delete('cs02/del-return','cs02Controller@del_return'); // 상품반품이동 삭제
        Route::get('cs02/search-return-products','cs02Controller@search_return_products'); // 기존에 반품등록된 상품목록 조회
        Route::put('cs02/add-storage-return','cs02Controller@add_storage_return'); // 상품반품이동정보 등록
        Route::put('cs02/update-storage-return','cs02Controller@update_storage_return'); // 상품반품이동정보 수정
        Route::get('cs02/batch','cs02Controller@batch_show'); // 상품반품이동 일괄등록
        Route::post('cs02/batch-import','cs02Controller@import_excel'); // 상품반품이동 엑셀파일 적용
        Route::post('cs02/batch-getgoods','cs02Controller@get_goods'); // 일괄적용 시 상품정보 조회

        // 원부자재입고/반품
        Route::get('cs03', 'cs03Controller@index');
        Route::get('cs03/search', 'cs03Controller@search');
        Route::put('cs03/update', 'cs03Controller@changeState');
        Route::delete('cs03/delete', 'cs03Controller@delete');
        Route::prefix('cs03/buy')->group(function () {
            Route::get('/', 'cs03Controller@showBuy');
            Route::get('/search', 'cs03Controller@searchBuy');
            Route::get('/get-invoice-no/{com_id}', 'cs03Controller@getInvoiceNo');
            Route::post('/add', 'cs03Controller@addBuy');
            Route::post('/save', 'cs03Controller@save');
            Route::post('/changeInput', 'cs03Controller@changeInput');
        });

        // 창고간상품이동
        Route::get('cs04', 'cs04Controller@index');
        Route::get('cs04/search', 'cs04Controller@search');
        Route::get('cs04/show/{sgr_cd?}','cs04Controller@show');
        Route::put('cs04/update-return-state','cs04Controller@update_return_state');
        Route::delete('cs04/del-return','cs04Controller@del_return');
        Route::get('cs04/search-return-products','cs04Controller@search_return_products');
        Route::put('cs04/add-storage-return','cs04Controller@add_storage_return');
        Route::put('cs04/update-storage-return','cs04Controller@update_storage_return');
        Route::get('cs04/batch','cs04Controller@batch_show');
        Route::post('cs04/batch-import','cs04Controller@import_excel');
        Route::post('cs04/batch-getgoods','cs04Controller@get_goods');
        
    });

    //게시판
    Route::prefix("community")->namespace('community')->group(function () {
        Route::get('comm01/{notice_id}','comm01Controller@index');
        Route::get('comm01/{notice_id}/search', 'comm01Controller@search');
        Route::get('comm01/{notice_id}/create', 'comm01Controller@create');
        Route::get('comm01/show/{notice_id}/{no}', 'comm01Controller@show');
        Route::get('comm01/file/download/{path}', 'comm01Controller@download_file');
        Route::delete('comm01/file/delete/{no}/{path}', 'comm01Controller@delete_file');
        Route::post('comm01/store', 'comm01Controller@store');
        Route::post('comm01/edit/{no}', 'comm01Controller@update');
        Route::post('comm01/del_store', 'comm01Controller@del_store');
    });

    //매장관리
    Route::prefix("stock")->namespace('stock')->group(function () {

        // 생산입고관리
        Route::get('cs01','cs01Controller@index');

        // 매장재고
        Route::get('stk01','stk01Controller@index');
        Route::get('stk01/search','stk01Controller@search');
        Route::get('stk01/{prd_cd?}','stk01Controller@show');
        Route::get('stk01/search-stock/{cmd?}','stk01Controller@search_command');

        Route::get('stk02','stk02Controller@index');

        // 매장주문
        Route::get('stk03', 'stk03Controller@index');
        Route::get('stk03/search', 'stk03Controller@search');
        Route::delete('stk03', 'stk03Controller@del_order'); // 출고 전 주문삭제
        Route::get('stk03/create', 'stk03Controller@create');
        Route::post('stk03/save', 'stk03Controller@save');
        Route::get('stk03/batch-create', 'stk03Controller@batch_create');
        Route::post('stk03/batch-import', 'stk03Controller@batch_import');
        Route::put('stk03/batch-add', 'stk03Controller@batch_add');
        Route::get('stk03/order/{ord_no}/{ord_opt_no?}', 'stk03Controller@show'); // 매장주문 상세
        Route::post('stk03/order/store_refund', 'stk03Controller@store_refund_save'); // 매장환불처리

        Route::get('stk11','stk11Controller@index');

        // 출고리스트
        Route::get('stk10','stk10Controller@index');
        Route::get('stk10/search','stk10Controller@search');
		Route::get('stk10/download','stk10Controller@download'); // 명세서출력
        Route::post('stk10/receipt','stk10Controller@receipt'); // 접수
        Route::post('stk10/release','stk10Controller@release'); // 출고
        Route::post('stk10/receive','stk10Controller@receive'); // 매장입고
        Route::post('stk10/reject','stk10Controller@reject'); // 거부

        // 초도출고
        Route::get('stk12','stk12Controller@index');
        Route::get('stk12/search','stk12Controller@search');
        Route::get('stk12/batch','stk12Controller@batch_show'); // 엑셀 업로드
        Route::post('stk12/request-release', 'stk12Controller@request_release');
        Route::post('stk12/request-release-excel', 'stk12Controller@request_release_excel');
        Route::put('stk12/add-storage-return','stk12Controller@add_storage_return');
        Route::put('stk12/update-storage-return','stk12Controller@update_storage_return'); 
        Route::post('stk12/batch-import','stk12Controller@import_excel'); 
        Route::post('stk12/batch-getgoods','stk12Controller@get_goods'); 
        

        // 판매분출고
        Route::get('stk13','stk13Controller@index');
        Route::get('stk13/search','stk13Controller@search');
        Route::post('stk13/request-release', 'stk13Controller@request_release');

        // 요청분출고
        Route::get('stk14','stk14Controller@index');
        Route::get('stk14/search','stk14Controller@search');
        Route::post('stk14/request-release', 'stk14Controller@request_release');

        // 일반출고
        Route::get('stk15','stk15Controller@index');
        Route::get('stk15/search','stk15Controller@search');
        Route::post('stk15/request-release', 'stk15Controller@request_release');
        Route::post('stk15/chg-store-type', 'stk15Controller@chg_store_type');

        // 원부자재 출고
        Route::get('stk16','stk16Controller@index');
        Route::get('stk16/search','stk16Controller@search');
        Route::post('stk16/receipt','stk16Controller@receipt'); // 접수
        Route::post('stk16/release','stk16Controller@release'); // 출고
        Route::post('stk16/receive','stk16Controller@receive'); // 매장입고
        Route::post('stk16/reject','stk16Controller@reject'); // 거부

        // 원부자재 - 요청분출고
        Route::get('stk17','stk17Controller@index');
        Route::get('stk17/search','stk17Controller@search');
        Route::post('stk17/request-release', 'stk17Controller@request_release');

        // 원부자재 - 일반출고
        Route::get('stk18','stk18Controller@index');
        Route::get('stk18/search','stk18Controller@search');
        Route::post('stk18/request-release', 'stk18Controller@request_release');
        Route::get('stk18/chg-store-type', 'stk18Controller@change_store_type');

        // 매장RT
        Route::get('stk20','stk20Controller@index');
        Route::get('stk20/search','stk20Controller@search');
        Route::get('stk20/download','stk20Controller@download'); // 전표출력
        Route::post('stk20/receipt','stk20Controller@receipt'); // 접수
        Route::post('stk20/release','stk20Controller@release'); // 출고
        Route::post('stk20/receive','stk20Controller@receive'); // 매장입고
        Route::post('stk20/reject','stk20Controller@reject'); // 거부
        Route::delete('stk20','stk20Controller@remove'); // 삭제

        // 요청RT
        Route::get('stk21','stk21Controller@index');
        Route::get('stk21/search-goods','stk21Controller@search_goods');
        Route::get('stk21/search-stock','stk21Controller@search_stock');
        Route::post('stk21/request-rt','stk21Controller@request_rt');

        // 일반RT
        Route::get('stk22','stk22Controller@index');
        Route::get('stk22/search-goods','stk22Controller@search_goods');
        Route::get('stk22/search-stock','stk22Controller@search_stock');
        Route::post('stk22/request-rt','stk22Controller@request_rt');


        // 매장별할인율적용조회
        Route::get('stk25','stk25Controller@index');
        Route::get('stk25/search','stk25Controller@search');
        
        // 실사
        Route::get('stk26','stk26Controller@index');
        Route::get('stk26/search','stk26Controller@search');
        Route::get('stk26/show/{sc_cd?}','stk26Controller@show');
        Route::get('stk26/search-check-products','stk26Controller@search_check_products');
        Route::put('stk26/save', 'stk26Controller@save');
        Route::put('stk26/update', 'stk26Controller@update');
        Route::post('stk26/save-loss', 'stk26Controller@save_loss');
        Route::delete('stk26','stk26Controller@delete');
        Route::get('stk26/batch', 'stk26Controller@show_batch');
        Route::get('stk26/barcode-batch', 'stk26Controller@barcode_batch');
        Route::post('stk26/batch-import', 'stk26Controller@import_excel');
        Route::post('stk26/batch-getgoods', 'stk26Controller@get_goods');
        Route::post('stk26/batch-import2', 'stk26Controller@import_excel2');
        Route::post('stk26/batch-getgoods2', 'stk26Controller@get_goods2');
        
        // 창고반품
        Route::get('stk30','stk30Controller@index');
        Route::get('stk30/search','stk30Controller@search');
		Route::get('stk30/download','stk30Controller@download'); // 명세서출력
        Route::get('stk30/show/{sr_cd?}','stk30Controller@show');
        Route::get('stk30/view/{sr_cd?}','stk30Controller@view');
        Route::get('stk30/search-return-products','stk30Controller@search_return_products'); // 기존에 반품등록된 상품목록 조회
        Route::put('stk30/add-store-return','stk30Controller@add_store_return'); // 창고반품등록
        Route::put('stk30/update-store-return','stk30Controller@update_store_return'); // 창고반품수정
        Route::put('stk30/update-return-state','stk30Controller@update_return_state'); // 창고반품 상태변경
        Route::put('stk30/update-state','stk30Controller@update_state');
        Route::delete('stk30/del-return','stk30Controller@del_return'); // 창고반품 삭제
        Route::get('stk30/batch', 'stk30Controller@show_batch');
        Route::post('stk30/batch-import', 'stk30Controller@import_excel');
        Route::post('stk30/batch-getgoods', 'stk30Controller@get_goods');
        Route::put('stk30/save', 'stk30Controller@save');

        // 매장 및 vmd 공지사항
        Route::get('stk31/{notice_id}','stk31Controller@index');
        Route::get('stk31/{notice_id}/search', 'stk31Controller@search');
        Route::get('stk31/{notice_id}/create', 'stk31Controller@create');
        Route::get('stk31/show/{notice_id}/{no}', 'stk31Controller@show');
        Route::get('stk31/file/download/{path}', 'stk31Controller@download_file');
        Route::delete('stk31/file/delete/{path}', 'stk31Controller@delete_file');
        Route::post('stk31/store', 'stk31Controller@store');
        Route::put('stk31/edit/{no}', 'stk31Controller@update');
        Route::post('stk31/del_store', 'stk31Controller@del_store');

        //알림
        Route::get('stk32','stk32Controller@index');
        Route::get('stk32/search', 'stk32Controller@search');
        Route::get('stk32/search-store', 'stk32Controller@search_store');
        Route::get('stk32/search-groupstore', 'stk32Controller@search_groupStore');
        Route::get('stk32/search_group', 'stk32Controller@search_group');
        Route::get('stk32/search_group_show', 'stk32Controller@search_group_show');
        Route::get('stk32/search_group2{group_cd?}', 'stk32Controller@search_group2');
        Route::get('stk32/create', 'stk32Controller@create');
        Route::get('stk32/sendMsg', 'stk32Controller@sendMsg');
        Route::get('stk32/showContent', 'stk32Controller@showContent');
        Route::get('stk32/show/{no?}', 'stk32Controller@show');
        Route::get('stk32/msg{no?}', 'stk32Controller@msg');
        Route::post('stk32/store', 'stk32Controller@store');
        Route::put('stk32/msg_read', 'stk32Controller@msg_read');
        Route::post('stk32/msg_del', 'stk32Controller@msg_del');
        Route::get('stk32/popup_chk', 'stk32Controller@popup_chk');
        Route::get('stk32/group', 'stk32Controller@group');
        Route::get('stk32/group_show', 'stk32Controller@group_show');
        Route::post('stk32/add_group', 'stk32Controller@add_group');
        Route::get('stk32/addGroup', 'stk32Controller@addGroup');
        Route::get('stk32/addGroup', 'stk32Controller@addGroup_show');
        Route::post('stk32/update', 'stk32Controller@update');
        Route::post('stk32/del_group', 'stk32Controller@del_group');
        Route::post('stk32/del_store', 'stk32Controller@del_store');

        //일별동종업계매출관리
        Route::get('stk33','stk33Controller@index');
        Route::get('stk33/search', 'stk33Controller@search');
        Route::get('stk33/create', 'stk33Controller@create');
        Route::get('stk33/com_search', 'stk33Controller@com_search');
        Route::post('stk33/save_amt', 'stk33Controller@save_amt');

        //월별동종업계매출관리
        Route::get('stk34','stk34Controller@index');
        Route::get('stk34/search', 'stk34Controller@search');
        Route::get('stk34/create', 'stk34Controller@create');
        Route::get('stk34/com_search', 'stk34Controller@com_search');
        Route::post('stk34/save_amt', 'stk34Controller@save_amt');

    });

    // 주문/배송관리
    Route::prefix("order")->namespace('order')->group(function () {

        // 매장주문
        Route::get('ord01', 'ord01Controller@index');
        Route::get('ord01/search', 'ord01Controller@search');
        Route::delete('ord01', 'ord01Controller@del_order'); // 출고 전 주문삭제
        Route::get('ord01/create', 'ord01Controller@create');
        Route::get('ord01/view', 'ord01Controller@view');
        Route::post('ord01/save', 'ord01Controller@save');
        Route::get('ord01/batch-create', 'ord01Controller@batch_create');
        Route::post('ord01/batch-import', 'ord01Controller@batch_import');
        Route::put('ord01/batch-add', 'ord01Controller@batch_add');
        Route::get('ord01/order/{ord_no}/{ord_opt_no?}', 'ord01Controller@show'); // 매장주문 상세
        Route::post('ord01/order/store_refund', 'ord01Controller@store_refund_save'); // 매장환불처리
        Route::get('ord01/refund/{ord_no}/{ord_opt_no?}', 'ord01Controller@refund');
        Route::post('ord01/complete-reservation', 'ord01Controller@complete_reservation'); // 예약판매상품 지급처리

        // 온라인 주문접수
        Route::get('ord02','ord02Controller@index');
        Route::get('ord02/search','ord02Controller@search');
        Route::post('ord02/receipt','ord02Controller@receipt');
        Route::post('ord02/update/ord-kind','ord02Controller@update_ord_kind');
        
        // 온라인 배송처리
        Route::get('ord03','ord03Controller@index');
        Route::get('ord03/search','ord03Controller@search');
        Route::post('ord03/complete','ord03Controller@complete');
        Route::post('ord03/update/ord-kind','ord03Controller@update_ord_kind');
        Route::get('ord03/show/{cmd}', 'ord03Controller@show_popup');
        Route::get('ord03/download/{cmd}', 'ord03Controller@download');
        Route::post('ord03/batch-import', 'ord03Controller@batch_import');
        Route::post('ord03/search-orders', 'ord03Controller@batch_search_orders');
    });

    // 고객관리
    Route::prefix("member")->namespace('member')->group(function () {
        Route::get('mem01','mem01Controller@index');
        Route::get('mem01/search', 'mem01Controller@search');
        Route::get('mem01/batch', 'mem01Controller@batch');
        Route::post('mem01/upload',	'mem01Controller@upload');
        Route::put('mem01/batch', 'mem01Controller@update');
    });

    // 영업관리
    Route::prefix("sale")->namespace('sale')->group(function () {
        Route::get('sal01','sal01Controller@index');
        Route::get('sal01/search','sal01Controller@search');
        Route::get('sal01/show','sal01Controller@show');
        Route::post('sal01/update','sal01Controller@update');
        Route::get('sal01/update2','sal01Controller@update');
        Route::post('sal01/upload',	'sal01Controller@upload');

        Route::get('sal02','sal02Controller@index');
        Route::get('sal02/search','sal02Controller@search');

        Route::get('sal03','sal03Controller@index');
        Route::get('sal03/search','sal03Controller@search');

        Route::get('sal04','sal04Controller@index');
        Route::get('sal05','sal05Controller@index');
        Route::get('sal06','sal06Controller@index');
        Route::get('sal06/search','sal06Controller@search');

        Route::get('sal07','sal07Controller@index');
        Route::get('sal07/search','sal07Controller@search');
        
        Route::get('sal08', 'sal08Controller@index'); // 매장브랜드별매출분석
        Route::get('sal08/search','sal08Controller@search');

        Route::get('sal11','sal11Controller@index');
        Route::get('sal12','sal12Controller@index');
        Route::get('sal13','sal13Controller@index');
        Route::get('sal17','sal17Controller@index');
        Route::get('sal17/search','sal17Controller@search');
        Route::post('sal17/update','sal17Controller@update');

        // 월별할인유형적용관리
        Route::get('sal18', 'sal18Controller@index');
        Route::get('sal18/search', 'sal18Controller@search');
        Route::get('sal18/search-store', 'sal18Controller@search_store');
        Route::post('sal18/save', 'sal18Controller@save');

        // 매장LOSS등록
        Route::get('sal20', 'sal20Controller@index');
        Route::get('sal20/search', 'sal20Controller@search');
        Route::post('sal20/save-loss', 'sal20Controller@save_loss');
        Route::get('sal20/show/{sc_cd?}', 'sal20Controller@show');
        Route::get('sal20/search-check-products','sal20Controller@search_check_products');

        // 매장수불집계표
        Route::get('sal21', 'sal21Controller@index');
        Route::get('sal21/search', 'sal21Controller@search');
      
        // 창고수불집계표
        Route::get('sal22', 'sal22Controller@index');
        Route::get('sal22/search', 'sal22Controller@search');

        // 본사수불집계표
        Route::get('sal23', 'sal23Controller@index');
        Route::get('sal23/search', 'sal23Controller@search');
      
        // 일별 매출 통계
        Route::get('sal24', 'sal24Controller@index');
        Route::get('sal24/search', 'sal24Controller@search');

        //월별 매출 통계
        Route::get('sal25', 'sal25Controller@index');
        Route::get('sal25/search', 'sal25Controller@search');

        //판매처별 매출 통계
        Route::get('sal26', 'sal26Controller@index');
        Route::get('sal26/search', 'sal26Controller@search');

        //품번별종합분석현황
        Route::get('sal27', 'sal27Controller@index');
        Route::get('sal27/search', 'sal27Controller@search');
    });

    Route::prefix("account")->namespace('account')->group(function () {

        // 마감
        Route::get('acc03', 'acc03Controller@index');
        Route::get('acc03/search', 'acc03Controller@search');
        Route::get('acc03/show', 'acc03Controller@show');
        Route::get('acc03/show_search', 'acc03Controller@show_search');
        Route::put('acc03/show_update', 'acc03Controller@show_update');
        Route::delete('acc03/show_delete', 'acc03Controller@show_delete');
        Route::post('acc03/show_close', 'acc03Controller@show_close');

        // 매장별매출현황
        Route::get('acc04', 'acc04Controller@index');
        Route::get('acc04/search', 'acc04Controller@search');

        // 기타재반자료
        Route::get('acc05', 'acc05Controller@index');
        Route::get('acc05/search', 'acc05Controller@search');
        Route::get('acc05/show', 'acc05Controller@show');
        Route::get('acc05/show-search', 'acc05Controller@show_search');
        Route::post('acc05/save', 'acc05Controller@save');
        Route::post('acc05/batch-import', 'acc05Controller@import_excel');

        // 매장중간관리자 - 정산
        Route::get('acc06', 'acc06Controller@index');
        Route::get('acc06/search', 'acc06Controller@search');
        Route::get('acc06/show/{store_cd}/{sdate}', 'acc06Controller@show');
        Route::get('acc06/show-search', 'acc06Controller@show_search');
        Route::put('acc06/closed', 'acc06Controller@closed');
        Route::get('acc06/show-online', 'acc06Controller@show_online');
        Route::get('acc06/show-online/search', 'acc06Controller@search_online');

        // 매장중간관리자 - 마감
        Route::get('acc07', 'acc07Controller@index');
        Route::get('acc07/search', 'acc07Controller@search');
        Route::get('acc07/show/{idx}', 'acc07Controller@show');
        Route::get('acc07/show-search/{cmd}', 'acc07Controller@search_command');
        Route::delete('acc07/{idx}', 'acc07Controller@remove');
        Route::post('acc07/complete', 'acc07Controller@complete_closed');
        Route::put('acc07/update', 'acc07Controller@update_closed');

    });

    Route::prefix("system")->namespace('system')->group(function () {
        //사용자관리
        Route::get('sys01', 'sys01Controller@index');
        Route::post('sys01', 'sys01Controller@store');
        Route::get('sys01/search', 'sys01Controller@search');
        Route::get('sys01/create', 'sys01Controller@create');
        Route::get('sys01/{code?}', 'sys01Controller@show');
        Route::put('sys01/{code?}', 'sys01Controller@update');
        Route::delete('sys01/{code}', 'sys01Controller@delete');

        Route::get('sys01/{code?}/search', 'sys01Controller@group_search');

        //메뉴관리
        Route::get('sys02', 'sys02Controller@index');
        Route::post('sys02', 'sys02Controller@store');
        Route::get('sys02/search', 'sys02Controller@search');
        Route::get('sys02/create', 'sys02Controller@create');
        Route::get('sys02/{code?}', 'sys02Controller@show');
        Route::put('sys02/{code?}', 'sys02Controller@update');
        Route::delete('sys02/{code}', 'sys02Controller@delete');

        Route::get('sys02/{code?}/search', 'sys02Controller@role_search');
        Route::get('sys02/{code?}/search-seq', 'sys02Controller@search_seq');
        Route::post('sys02/{code?}/change-seq', 'sys02Controller@change_seq');

        //그룹관리
        Route::get('sys03', 'sys03Controller@index');
        Route::post('sys03', 'sys03Controller@store');
        Route::get('sys03/search', 'sys03Controller@search');
        Route::get('sys03/create', 'sys03Controller@create');
        Route::get('sys03/{code?}', 'sys03Controller@show');
        Route::put('sys03/{code?}', 'sys03Controller@update');
        Route::delete('sys03/{code}', 'sys03Controller@delete');

        Route::get('sys03/{code?}/search', 'sys03Controller@user_search');

        //로그
        Route::get('sys04', 'sys04Controller@index');
        Route::get('sys04/search', 'sys04Controller@search');

        

    });

});

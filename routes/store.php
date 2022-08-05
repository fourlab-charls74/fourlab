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

    Route::prefix("auto-complete")->group(function () {

        // 매장명 조회 (자동완성)
        Route::get('/store', 'AutoCompleteController@store');

    });

    Route::prefix("api")->namespace('api')->group(function () {

        // 상품검색
        Route::get('goods', 'goods@search');
        Route::get('goods/show', 'goods@show');
        Route::get('goods/show/file/search', 'goods@file_search');

        // 고객명 조회
        Route::get('members', 'MemberController@show');
        Route::get('members/search', 'MemberController@search');

        // 매장명 조회
        Route::get('stores', 'StoreController@show');
        Route::get('stores/search', 'StoreController@search');
        Route::post('stores/search-storenm', 'StoreController@search_storenm');
        Route::post('stores/search-storenm-from-type', 'StoreController@search_storenm_from_type');

    });

    // 포스
    Route::prefix("pos")->namespace('pos')->group(function () {
        Route::get('', 'PosController@index');
    });

    //코드관리
    Route::prefix("standard")->namespace('standard')->group(function () {
        //매장
        Route::get('std02', 'std02Controller@index');
        Route::get('std02/search', 'std02Controller@search');
        Route::get('std02/show/{store_cd?}', 'std02Controller@show');
        Route::get('std02/check-code/{storage_cd?}', 'std02Controller@check_code');

        Route::post('std02/update', 'std02Controller@update_store');

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

        // 수선관리
        Route::get('std11', 'std11Controller@index');
        Route::get('std11/search', 'std11Controller@search');

        Route::get('std11/create', 'std11Controller@showCreate');
        Route::post('std11/create', 'std11Controller@create');

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
        Route::post('std51/{code?}/del', 'std51Controller@data_del');
        Route::post('std51/{code?}/seq', 'std51Controller@data_seq');

    });

    // 상품관리
    Route::prefix("product")->namespace('product')->group(function () {
        Route::get('prd02','prd02Controller@index');
        Route::get('prd02/search','prd02Controller@search');
        Route::get('prd02/create', 'prd02Controller@create');

        Route::get('prd02/create', 'prd02Controller@create');
        Route::get('prd02/prd-search', 'prd02Controller@prd_search');
        Route::put('prd02/add-product-code', 'prd02Controller@add_product_code');
    });

    // 생산입고관리
    Route::prefix("cs")->namespace('cs')->group(function () {

        // 입고
        Route::get('cs01','cs01Controller@index');
        Route::get('cs01/search','cs01Controller@search');
        Route::get('cs01/show','cs01Controller@show');
        Route::post('cs01/comm', 'cs01Controller@command');
        
        // 상품반품
        Route::get('cs02', 'cs02Controller@index');
        Route::get('cs02/show','cs02Controller@show');

    });

    //매장관리
    Route::prefix("stock")->namespace('stock')->group(function () {

        // 생산입고관리
        Route::get('cs01','cs01Controller@index');

        // 매장재고
        Route::get('stk01','stk01Controller@index');
        Route::get('stk01/search','stk01Controller@search');

        Route::get('stk02','stk02Controller@index');
        Route::get('stk11','stk11Controller@index');

        // 출고리스트
        Route::get('stk10','stk10Controller@index');
        Route::get('stk10/search','stk10Controller@search');
        Route::post('stk10/receipt','stk10Controller@receipt'); // 접수
        Route::post('stk10/release','stk10Controller@release'); // 출고
        Route::post('stk10/receive','stk10Controller@receive'); // 매장입고
        Route::post('stk10/reject','stk10Controller@reject'); // 거부

        // 초도출고
        Route::get('stk12','stk12Controller@index');
        Route::get('stk12/search','stk12Controller@search');
        Route::post('stk12/request-release', 'stk12Controller@request_release');

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

        // 매장RT
        Route::get('stk20','stk20Controller@index');
        Route::get('stk20/search','stk20Controller@search');
        Route::post('stk20/receipt','stk20Controller@receipt'); // 접수
        Route::post('stk20/release','stk20Controller@release'); // 출고
        Route::post('stk20/receive','stk20Controller@receive'); // 매장입고
        Route::post('stk20/reject','stk20Controller@reject'); // 거부
        Route::delete('stk20','stk20Controller@remove'); // 삭제

        // RT요청
        Route::get('stk21','stk21Controller@index');
        Route::get('stk21/search-goods','stk21Controller@search_goods');
        Route::get('stk21/search-stock','stk21Controller@search_stock');
        Route::post('stk21/request-rt','stk21Controller@request_rt');

        // 일반RT
        Route::get('stk22','stk22Controller@index');
        Route::get('stk22/search-goods','stk22Controller@search_goods');
        Route::get('stk22/search-stock','stk22Controller@search_stock');
        Route::post('stk22/request-rt','stk22Controller@request_rt');
        
        // 창고반품
        Route::get('stk30','stk30Controller@index');
        Route::get('stk30/search','stk30Controller@search');
        Route::get('stk30/show/{sr_cd?}','stk30Controller@show');
        Route::post('stk30/search-store-qty','stk30Controller@search_store_qty'); //  상품추가 시 매장수량 조회
        Route::put('stk30/add-storage-return','stk30Controller@add_storage_return'); // 창고반품등록

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

        Route::get('sal11','sal11Controller@index');
        Route::get('sal12','sal12Controller@index');
        Route::get('sal13','sal13Controller@index');
        Route::get('sal17','sal17Controller@index');
        Route::get('sal17/search','sal17Controller@search');
        Route::post('sal17/update','sal17Controller@update');
    });

    Route::prefix("account")->namespace('account')->group(function () {

        // 정산내역
        Route::get('acc01', 'acc01Controller@index');
        Route::get('acc01/search', 'acc01Controller@search');

        // 정산관리
        Route::get('acc02', 'acc02Controller@index');
        Route::get('acc02/search', 'acc02Controller@search');
        Route::get('acc02/show/{com_id}/{sdate}/{edate}', 'acc02Controller@show');
        Route::get('acc02/show-search', 'acc02Controller@show_search');
        Route::put('acc02/show', 'acc02Controller@closed');

        // 마감
        Route::get('acc03', 'acc03Controller@index');
        Route::get('acc03/search', 'acc03Controller@search');
        Route::get('acc03/show', 'acc03Controller@show');
        Route::get('acc03/show_search', 'acc03Controller@show_search');
        Route::put('acc03/show_update', 'acc03Controller@show_update');
        Route::delete('acc03/show_delete', 'acc03Controller@show_delete');
        Route::post('acc03/show_close', 'acc03Controller@show_close');

        // 매장판매수수료
        Route::get('acc04', 'acc04Controller@index');

        // 기타재반자료
        Route::get('acc05', 'acc05Controller@index');
        Route::get('acc05/search', 'acc05Controller@search');
        Route::post('acc05/save', 'acc05Controller@save');

        // 매장중간관리자정산
        Route::get('acc06', 'acc06Controller@index');
        Route::get('acc06/search', 'acc06Controller@search');

    });

});

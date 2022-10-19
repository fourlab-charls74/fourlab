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
        Route::get('store-goods/show/{store_cd?}', 'goods@store_show'); // 매장별 상품검색 화면 show
        Route::get('store-goods', 'goods@store_search'); // 매장별 상품검색
        Route::get('storage-goods/show/{storage_cd?}', 'goods@storage_show'); // 창고별 상품검색 화면 show
        Route::get('storage-goods', 'goods@storage_search'); // 창고별 상품검색

        // 고객명 조회
        Route::get('members', 'MemberController@show');
        Route::get('members/search', 'MemberController@search');

        // 관리자 (담당자MD) 조회
        Route::get('mds/search', 'AdminController@search');

        // 매장명 조회
        Route::get('stores', 'StoreController@show');
        Route::get('stores/search', 'StoreController@search');
        Route::get('stores/search-storetype', 'StoreController@search_storetype');
        Route::post('stores/search-storenm', 'StoreController@search_storenm');
        Route::post('stores/search-storenm-from-type', 'StoreController@search_storenm_from_type');
        Route::get('stores/search-store-info/{store_cd?}', 'StoreController@search_store_info');

        // 상품코드 조회
        Route::get('prdcd/conds', 'goods@search_product_conditions');
        Route::get('prdcd/search', 'goods@search_prdcd');

    });

    // 포스
    Route::prefix("pos")->namespace('pos')->group(function () {
        Route::get('', 'PosController@index');
        Route::get('search/{cmd?}', 'PosController@search_command');
        Route::post('save', 'PosController@save');
        Route::post('add-member', 'PosController@add_member');
    });

    //코드관리
    Route::prefix("standard")->namespace('standard')->group(function () {
        //매장
        Route::get('std02', 'std02Controller@index');
        Route::get('std02/search', 'std02Controller@search');
        Route::get('std02/show/{store_cd?}', 'std02Controller@show');
        Route::get('std02/check-code/{storage_cd?}', 'std02Controller@check_code');

        Route::post('std02/update', 'std02Controller@update_store');
        Route::post('std02/del_img', 'std02Controller@del_img');


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
        Route::get('std08/choice', 'std08Controller@choice_index');

        // 수선관리
        Route::get('std11', 'std11Controller@index');
        Route::get('std11/search', 'std11Controller@search');
        Route::post('std11/batch-edit', 'std11Controller@batchEdit');

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
        Route::post('std51/{code?}/mod', 'std51Controller@data_mod');
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

        Route::get('prd02/edit-goods-no/{product_code}/{goods_no}/', 'prd02Controller@edit_goods_no');
        Route::get('prd02/prd-edit-search', 'prd02Controller@prd_edit_search');
        Route::put('prd02/del-product-code', 'prd02Controller@del_product_code');

        Route::get('prd02/batch-create', 'prd02Controller@batch_create');
        Route::post('prd02/upload', 'prd02Controller@upload');
        Route::put('prd02/show', 'prd02Controller@update');

        Route::get('prd02/product_upload', 'prd02Controller@product_upload');
        Route::post('prd02/get-seq', 'prd02Controller@getSeq');
        Route::post('prd02/save_product', 'prd02Controller@save_product');
        Route::post('prd02/del-img', 'prd02Controller@delImg');

        // 원부자재 상품 관리
        Route::get('prd03','prd03Controller@index');
        Route::get('prd03/search','prd03Controller@search');

        Route::get('prd03/create', 'prd03Controller@showCreate');
        Route::post('prd03/get-seq', 'prd03Controller@getSeq');
        Route::post('prd03/create', 'prd03Controller@create');

        Route::get('prd03/edit/{product_code}','prd03Controller@showEdit');
        Route::post('prd03/edit','prd03Controller@edit');

        Route::get('prd03/delete/{product_code}','prd03Controller@delete');
        Route::post('prd03/del-img', 'prd03Controller@delImg');
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
        });
        
    });

    //매장관리
    Route::prefix("stock")->namespace('stock')->group(function () {

        // 생산입고관리
        Route::get('cs01','cs01Controller@index');

        // 매장재고
        Route::get('stk01','stk01Controller@index');
        Route::get('stk01/search','stk01Controller@search');

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
        
        // 창고반품
        Route::get('stk30','stk30Controller@index');
        Route::get('stk30/search','stk30Controller@search');
        Route::get('stk30/show/{sr_cd?}','stk30Controller@show');
        Route::get('stk30/search-return-products','stk30Controller@search_return_products'); // 기존에 반품등록된 상품목록 조회
        Route::put('stk30/add-store-return','stk30Controller@add_store_return'); // 창고반품등록
        Route::put('stk30/update-store-return','stk30Controller@update_store_return'); // 창고반품수정
        Route::put('stk30/update-return-state','stk30Controller@update_return_state'); // 창고반품 상태변경
        Route::delete('stk30/del-return','stk30Controller@del_return'); // 창고반품 삭제

        // 매장 공지사항
        Route::get('stk31','stk31Controller@index');
        Route::get('stk31/search', 'stk31Controller@search');
        Route::get('stk31/create', 'stk31Controller@create');
        Route::get('stk31/{no}', 'stk31Controller@show');
        Route::put('stk31/store', 'stk31Controller@store');
        Route::put('stk31/edit/{no}', 'stk31Controller@update');
        Route::post('stk31/del_store', 'stk31Controller@del_store');

        //알림
        Route::get('stk32','stk32Controller@index');
        Route::get('stk32/search', 'stk32Controller@search');
        Route::get('stk32/search-receiver', 'stk32Controller@search_receiver');
        Route::get('stk32/search_group', 'stk32Controller@search_group');
        Route::get('stk32/search_group2{group_cd?}', 'stk32Controller@search_group2');
        Route::get('stk32/create', 'stk32Controller@create');
        Route::get('stk32/sendMsg', 'stk32Controller@sendMsg');
        Route::get('stk32/showContent', 'stk32Controller@showContent');
        Route::get('stk32/show/{no?}', 'stk32Controller@show');
        Route::get('stk32/msg{no?}', 'stk32Controller@msg');
        Route::post('stk32/store', 'stk32Controller@store');
        Route::put('stk32/msg_read', 'stk32Controller@msg_read');
        Route::post('stk32/msg_del', 'stk32Controller@msg_del');
        Route::get('stk32/group', 'stk32Controller@group');
        Route::post('stk32/add_group', 'stk32Controller@add_group');
        Route::post('stk32/mod_group', 'stk32Controller@mod_group');
        Route::post('stk32/del_group', 'stk32Controller@del_group');

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
        Route::post('sal20/loss', 'sal20Controller@save_loss');

        // 매장수불집계표
        Route::get('sal21', 'sal21Controller@index');
        Route::get('sal21/search', 'sal21Controller@search');
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

        // 매장판매수수료
        Route::get('acc04', 'acc04Controller@index');

        // 기타재반자료
        Route::get('acc05', 'acc05Controller@index');
        Route::get('acc05/search', 'acc05Controller@search');
        Route::post('acc05/save', 'acc05Controller@save');

        // 매장중간관리자 - 정산
        Route::get('acc06', 'acc06Controller@index');
        Route::get('acc06/search', 'acc06Controller@search');
        Route::get('acc06/show/{store_cd}/{sdate}', 'acc06Controller@show');
        Route::get('acc06/show-search', 'acc06Controller@show_search');
        Route::put('acc06/show', 'acc06Controller@closed');

        // 매장중간관리자 - 마감
        Route::get('acc07', 'acc07Controller@index');
        Route::get('acc07/search', 'acc07Controller@search');
        Route::get('acc07/show', 'acc07Controller@show');
        Route::get('acc07/show_search', 'acc07Controller@show_search');
        Route::put('acc07/show_update', 'acc07Controller@show_update');
        Route::delete('acc07/show_delete', 'acc07Controller@show_delete');
        Route::post('acc07/show_close', 'acc07Controller@show_close');

    });

});

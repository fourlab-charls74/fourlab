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

Route::group(['middleware' => 'shop','as' => 'shop.', 'namespace' => 'shop'], function() {

    //Route::Redirect("/","//dashboard");
    Route::get('/', 'IndexController@index');
    Route::get('/login', 'LoginController@index');
    Route::get('/main', 'IndexController@main');
    Route::get('/main_alarm', 'IndexController@main_alarm');
    //Route::get('/login', 'LoginController@index')->name('login');
    Route::post('/login', 'LoginController@login');
    Route::get('/logout', 'LoginController@logout');
    Route::get('/user', 'UserController@index');
    Route::post('/user/store', 'UserController@store');
    Route::get('/user/log', 'UserController@log');
    Route::get('/user/log_search', 'UserController@searchlog');

    Route::prefix("auto-complete")->group(function () {

        // 매장명 조회 (자동완성)
        Route::get('/store', 'AutoCompleteController@store');
        Route::get('/brand', 'AutoCompleteController@brand');
        Route::get('/style-no', 'AutoCompleteController@style_no');
        Route::get('/goods-nm', 'AutoCompleteController@goods_nm');
        Route::get('/goods-nm-eng', 'AutoCompleteController@goods_nm_eng');
    });

    Route::prefix("api")->namespace('api')->group(function () {

        // 상품검색
        Route::get('goods', 'goods@search');
        Route::get('goods-search', 'goods@head_search');
        Route::get('goods/show', 'goods@show');
        Route::get('goods/show/file/search', 'goods@file_search');

        //brand 리스트
        Route::get('brand/get_brand_nm', 'brand@get_brand_nm');
        Route::get('brand/getlist', 'brand@getlist');

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
        Route::get('prdcd/conds_code', 'goods@search_product_conditions_code');
        Route::get('prdcd/search_code', 'goods@search_prdcd_code');
        
        // 원부자재코드 조회
        Route::get('prdcd/conds_sub', 'goods@search_product_sub_conditions');
        Route::get('prdcd/search_sub', 'goods@search_prdcd_sub');

        // 코드일련 조회
        Route::get('product/color', 'goods@search_color'); // 코드일련으로 해당 상품의 컬러옵션 리스트 조회
        Route::get('prdcd/search_p', 'goods@search_prdcd_p');

        // sms
        // Route::get('sms/{type}', 'SmsController@index');
        
        Route::get('sms/search/member', 'SmsController@search_member');
        Route::get('sms/search', 'SmsController@search');
        Route::get('sms/{type}', 'SmsController@index');
        Route::put('sms/send', 'SmsController@sendMsg');


        //템플릿 선택
        Route::get('template', 'template@index');
        Route::get('template/search', 'template@search');
        Route::get('template/detail/{no}', 'template@detail');

        //판매유형검색
        Route::get('sale/search_sell_type', 'goods@search_sell_type');
        
        //행사코드검색
        Route::get('sale/search_prcode', 'goods@search_prcode');

        //주문선택
        Route::get('order', 'OrderController@index');
        Route::get('order/search', 'OrderController@search');
    });

    // 포스
    Route::prefix("pos")->namespace('pos')->group(function () {
        Route::get('', 'PosController@index');
        Route::get('search/{cmd?}', 'PosController@search_command');
        Route::get('check-phone', 'PosController@check_phone');
        Route::post('save', 'PosController@save');
        Route::post('complete-reservation', 'PosController@complete_reservation');
        Route::post('add-member', 'PosController@add_member');
        Route::post('add-coupon', 'PosController@add_coupon');
        Route::delete('remove-waiting', 'PosController@remove_waiting');
    });

    //코드관리
    Route::prefix("standard")->namespace('standard')->group(function () {
        //매장
        Route::get('std02', 'std02Controller@index');
        Route::get('std02/search', 'std02Controller@search');
        Route::get('std02/show/{store_cd?}', 'std02Controller@show');
        Route::get('std02/check-code/{storage_cd?}', 'std02Controller@check_code');
        Route::get('std02/charge/{store_cd?}', 'std02Controller@charge');
        Route::get('std02/charge_search', 'std02Controller@charge_search');
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

    });

    // 상품관리
    Route::prefix("product")->namespace('product')->group(function () {

        
        Route::get('prd01/{no}', 'prd01Controller@show');
        Route::get('prd01/goods_img/{no}', 'prd01Controller@show_img');
        Route::get('prd01/{no}/get', 'prd01Controller@get');
       


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
        Route::post('prd02/change-gender', 'prd02Controller@change_gender');
        Route::post('prd02/get-seq', 'prd02Controller@getSeq');
        Route::post('prd02/save_product', 'prd02Controller@save_product');
        Route::post('prd02/del-img', 'prd02Controller@delImg');
        Route::post('prd02/sel_seq', 'prd02Controller@selSeq');
        Route::post('prd02/change_seq', 'prd02Controller@changeSeq');
        Route::post('prd02/batch-getproducts','prd02Controller@get_products'); 
        Route::post('prd02/batch-products','prd02Controller@batch_products'); 

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
            Route::post('/changeInput', 'cs03Controller@changeInput');
        });
        
    });

    //매장관리
    Route::prefix("community")->namespace('community')->group(function () {

        // 매장 공지사항
        Route::get('comm01/{notice_id}','comm01Controller@index');
        Route::get('comm01/{notice_id}/search', 'comm01Controller@search');
        // Route::get('comm01/create', 'comm01Controller@create');
        Route::get('comm01/{notice_id}/{no}', 'comm01Controller@show');
        Route::get('comm01/popup_chk', 'comm01Controller@popup_chk');
        Route::get('comm01/popup_notice/{no}', 'comm01Controller@show_notice');
        Route::put('comm01/notice_read', 'comm01Controller@notice_read');
        // Route::put('comm01/store', 'comm01Controller@store');
        // Route::put('comm01/edit/{no}', 'comm01Controller@update');
        // Route::post('comm01/del_store', 'comm01Controller@del_store');

        //알림
        Route::get('comm02','comm02Controller@index');
        Route::get('comm02/search', 'comm02Controller@search');
        Route::get('comm02/search-store', 'comm02Controller@search_store');
        Route::get('comm02/search-groupstore', 'comm02Controller@search_groupStore');
        Route::get('comm02/search_group', 'comm02Controller@search_group');
        Route::get('comm02/search_group_show', 'comm02Controller@search_group_show');
        Route::get('comm02/search_group2{group_cd?}', 'comm02Controller@search_group2');
        Route::get('comm02/create', 'comm02Controller@create');
        Route::get('comm02/sendMsg', 'comm02Controller@sendMsg');
        Route::get('comm02/showContent', 'comm02Controller@showContent');
        Route::get('comm02/show/{no?}', 'comm02Controller@show');
        Route::get('comm02/msg{no?}', 'comm02Controller@msg');
        Route::post('comm02/store', 'comm02Controller@store');
        Route::put('comm02/msg_read', 'comm02Controller@msg_read');
        Route::post('comm02/msg_del', 'comm02Controller@msg_del');
        Route::get('comm02/popup_chk', 'comm02Controller@popup_chk');
        Route::get('comm02/group', 'comm02Controller@group');
        Route::get('comm02/group_show', 'comm02Controller@group_show');
        Route::post('comm02/add_group', 'comm02Controller@add_group');
        Route::get('comm02/addGroup', 'comm02Controller@addGroup');
        Route::get('comm02/addGroup', 'comm02Controller@addGroup_show');
        Route::post('comm02/update', 'comm02Controller@update');
        Route::post('comm02/del_group', 'comm02Controller@del_group');
        Route::post('comm02/del_store', 'comm02Controller@del_store');
		Route::get('comm02/search-hq-user-id', 'comm02Controller@search_hq_user_id');
		Route::get('comm02/reply-msg/{msg_cd?}', 'comm02Controller@reply_msg');
		Route::put('comm02/update-check-yn', 'comm02Controller@update_check_yn');
	
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
		Route::post('stk10/download-multi','stk10Controller@downloadMulti'); // 명세서일괄출력 (excel)
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
        Route::post('stk16/receive','stk16Controller@receive'); // 매장입고

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
        Route::put('stk26/update', 'stk26Controller@update');
        
         // 창고반품
		Route::get('stk30','stk30Controller@index');
        Route::get('stk30/search','stk30Controller@search');
		Route::get('stk30/download','stk30Controller@download'); // 명세서출력
        Route::get('stk30/show/{sr_cd?}','stk30Controller@show');
        Route::get('stk30/search-return-products','stk30Controller@search_return_products'); // 기존에 반품등록된 상품목록 조회
        Route::put('stk30/update','stk30Controller@update'); // 반품수정
 
        // 매장 공지사항
        Route::get('comm01','comm01Controller@index');
        Route::get('comm01/search', 'comm01Controller@search');
        // Route::get('comm01/create', 'comm01Controller@create');
        Route::get('comm01/notice/{no}', 'comm01Controller@show');
        Route::get('comm01/popup_chk', 'comm01Controller@popup_chk');
        Route::get('comm01/popup_notice/{no}', 'comm01Controller@show_notice');
        Route::put('comm01/notice_read', 'comm01Controller@notice_read');
        // Route::put('comm01/store', 'comm01Controller@store');
        // Route::put('comm01/edit/{no}', 'comm01Controller@update');
        // Route::post('comm01/del_store', 'comm01Controller@del_store');

        //알림
        Route::get('comm02','comm02Controller@index');
        Route::get('comm02/search', 'comm02Controller@search');
        Route::get('comm02/search-store', 'comm02Controller@search_store');
        Route::get('comm02/search-groupstore', 'comm02Controller@search_groupStore');
        Route::get('comm02/search_group', 'comm02Controller@search_group');
        Route::get('comm02/search_group_show', 'comm02Controller@search_group_show');
        Route::get('comm02/search_group2{group_cd?}', 'comm02Controller@search_group2');
        Route::get('comm02/create', 'comm02Controller@create');
        Route::get('comm02/sendMsg', 'comm02Controller@sendMsg');
        Route::get('comm02/showContent', 'comm02Controller@showContent');
        Route::get('comm02/show/{no?}', 'comm02Controller@show');
        Route::get('comm02/msg{no?}', 'comm02Controller@msg');
        Route::post('comm02/store', 'comm02Controller@store');
        Route::put('comm02/msg_read', 'comm02Controller@msg_read');
        Route::post('comm02/msg_del', 'comm02Controller@msg_del');
        Route::get('comm02/popup_chk', 'comm02Controller@popup_chk');
        Route::get('comm02/group', 'comm02Controller@group');
        Route::get('comm02/group_show', 'comm02Controller@group_show');
        Route::post('comm02/add_group', 'comm02Controller@add_group');
        Route::get('comm02/addGroup', 'comm02Controller@addGroup');
        Route::get('comm02/addGroup', 'comm02Controller@addGroup_show');
        Route::post('comm02/update', 'comm02Controller@update');
        Route::post('comm02/del_group', 'comm02Controller@del_group');
        Route::post('comm02/del_store', 'comm02Controller@del_store');

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
        Route::get('ord01/search2/{cmd}', 'ord01Controller@search2');
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

        Route::get('ord01/receipt/{ord_no}', 'ord01Controller@receipt');
        Route::get('ord01/dlv/{ord_no}/{ord_opt_no}', 'ord01Controller@dlv');
        Route::get('ord01/order-list/{ord_no}', 'ord01Controller@order_list');
        Route::get('ord01/order-goods/{ord_no}/{ord_opt_no}', 'ord01Controller@order_goods');
        Route::get('ord01/get/{ord_no}/{ord_opt_no?}', 'ord01Controller@get');
        Route::get('ord01/{ord_no}/{ord_opt_no}/cash', 'ord01Controller@show_cash');
        Route::get('ord01/{ord_no}/cash/list', 'ord01Controller@search_cash_receipt_list');
        Route::get('ord01/{ord_no}/{ord_opt_no}/tax', 'ord01Controller@show_tax');
        Route::get('ord01/{ord_no}/tax/list', 'ord01Controller@search_tax_receipt_list');

        Route::put('ord01/dlv-info-save/{ord_no}', 'ord01Controller@dlv_info_save');
        Route::put('ord01/claim-save', 'ord01Controller@claim_save');
        Route::put('ord01/order-save', 'ord01Controller@order_save');
        Route::put('ord01/update/order-state', 'ord01Controller@update_order_state');
        Route::put('ord01/dlv-comment', 'ord01Controller@dlv_comment');
        Route::put('ord01/order-memo', 'ord01Controller@order_memo');
        Route::put('ord01/cancel-order', 'ord01Controller@cancel_orders');
        Route::put('ord01/confirm-orders', 'ord01Controller@confirm_orders');
        Route::put('ord01/claim-message-save', 'ord01Controller@claim_message_save');
        Route::put('ord01/order-goods/{ord_no}/{ord_opt_no}', 'ord01Controller@order_goods_save');
        Route::put('ord01/{ord_no}/{ord_opt_no}/cash-receipt', 'ord01Controller@set_cash_receipt');
        Route::put('ord01/{ord_no}/{ord_opt_no}/tax-receipt', 'ord01Controller@set_tax_receipt');

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
        Route::get('mem01', 'mem01Controller@index');
        Route::get('mem01/search', 'mem01Controller@search');
        Route::get('mem01/show/{type}/{id?}', 'mem01Controller@show');
        Route::get('mem01/batch', 'mem01Controller@batch');
        Route::get('mem01/check-id/{id}', 'mem01Controller@check_id');
        Route::post('mem01/upload',	'mem01Controller@upload');
        Route::put('mem01/batch', 'mem01Controller@update');
        Route::get('mem01/{user_id}/get', 'mem01Controller@get');

        Route::get('mem01/download', 'mem01Controller@download');
        Route::get('mem01/download/show', 'mem01Controller@download_show');
        Route::get('mem01/show/search/{type}/{id}', 'mem01Controller@show_search');

        Route::post('mem01/user', 'mem01Controller@add_user');
        Route::post('mem01/user/group/{id}', 'mem01Controller@add_group');

        Route::put('mem01/pw/{id}', 'mem01Controller@change_pw');
        Route::put('mem01/user/{id}', 'mem01Controller@edit_user');
        Route::put('mem01/active-user/{id}', 'mem01Controller@active_user');

        Route::delete('mem01/user/{id}', 'mem01Controller@delete_user');
        Route::delete('mem01/user/group/{id}', 'mem01Controller@del_group');

		Route::post('mem01/chg-store-channel/{store_cd?}', 'mem01Controller@change_store_channel');
		Route::post('mem01/chg-store-channel_kind/{store_cd?}', 'mem01Controller@change_store_channel_kind');

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
        Route::post('sal20/loss', 'sal20Controller@save_loss');

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

    });

});

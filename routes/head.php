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

//Route::get('standard/std01', 'head\standard\std01Controller@index');

// test
Route::group(['middleware' => 'head', 'as' => 'head.', 'namespace' => 'head'], function () {



    //Route::get('/', 'IndexController@index');
    Route::Redirect("/","/head/dashboard");

    Route::get('/login', 'LoginController@index')->name('login');
    Route::post('/login', 'LoginController@login');
    Route::get('/logout', 'LoginController@logout');

    Route::get('/user', 'UserController@index');
    Route::post('/user/store', 'UserController@store');
    Route::get('/user/log', 'UserController@log');
    Route::get('/user/log_search', 'UserController@searchlog');
    Route::get('/user/menu', 'UserController@menu');

    Route::get('/dashboard', 'dsh01Controller@index');
    Route::get('/dashboard/search', 'dsh01Controller@search');


    Route::get('/token', function(){
        return csrf_token();
    });

    //Route::get('/user/sys04_search', 'UserController@sys04search');
    //Route::get('/user/sys04_write', 'UserController@sys04Write');
    //Route::put('/user/sys04_update', 'UserController@sys04update');
    // Route::get('/system/search', 'sys04Controller@search');

    Route::prefix("auto-complete")->group(function () {
        Route::get('/template', 'AutoCompleteController@template');
        Route::get('/template-q', 'AutoCompleteController@template_q');
        Route::get('/style-no', 'AutoCompleteController@style_no');
        Route::get('/brand', 'AutoCompleteController@brand');
        Route::get('/company', 'AutoCompleteController@company');
        Route::get('/goods-nm', 'AutoCompleteController@goods_nm');
        Route::get('/category', 'AutoCompleteController@category');

        Route::get('/ad_type', 'AutoCompleteController@ad_type');
        Route::get('/style-no2', 'AutoCompleteController@style_no2');
    });

    Route::prefix("api")->namespace('api')->group(function () {
        //스타일 넘버 자동 완성 리스트
        Route::get('autocomplete/get_style_no/{search_str}', 'autocomplete@get_style_no');

        //상품이름 검색 자동완성 리스트
        Route::get('autocomplete/get_goods_nm/{search_str}', 'autocomplete@get_goods_nm');

        //brand 리스트
        Route::get('brand/get_brand_nm', 'brand@get_brand_nm');
        Route::get('brand/getlist', 'brand@getlist');

        //category 리스트
        Route::get('category/{cat_type}', 'category@index');
        Route::get('category/get_category_list/{cat_type}', 'category@get_category_list');
        Route::get('category/getlist/{cat_type}', 'category@getlist');

        Route::get('category/get_category_by_goods_no/{cat_type}/{goods_no}/{goods_sub}', 'category@get_category_by_goods_no');

        //claim 등록
        Route::post('claim/insert', 'claim@store');

        //상품검색
        Route::get('goods', 'goods@search');
        Route::get('goods/show', 'goods@show');
        Route::get('goods/show/file/search', 'goods@file_search');

        //sms
        Route::get('sms/search', 'SmsController@search');
        Route::get('sms/{type}', 'SmsController@index');
        Route::put('sms/send', 'SmsController@sendMsg');

        //템플릿 선택
        Route::get('template', 'template@index');
        Route::get('template/search', 'template@search');
        Route::get('template/detail/{no}', 'template@detail');

        //주문 선택
        Route::get('order', 'OrderController@index');
        Route::get('order/search', 'OrderController@search');

        //업체 검색
        Route::get('company/getlist', 'company@getlist');

        //Point
        Route::get('point', 'PointController@index');
        Route::get('point/search', 'PointController@search');

        Route::put('point', 'PointController@apply');

        // 단어 선택 - 머리말, 꼬리말
        Route::get('head_tail', 'WordsController@showAddHeadTail');
        Route::get('head_tail/search', 'WordsController@searchAddHeadTail');

        // 단어 선택 - 색상
        Route::get('colors', 'WordsController@showColors');
        Route::get('colors/search', 'WordsController@searchColors');

        //사방넷
        Route::prefix("sabangnet")->namespace('sabangnet')->group(function () {
            //상품등록수정 연동
            Route::get('goods_xml/good_view', 'goods_xmlController@good_view');
            //상품재고 연동
            Route::get('goods_xml/summary_view', 'goods_xmlController@summary_view');
            //주문 수집
            Route::get('order_xml/get_order', 'order_xmlController@get_order');
            //송장 연동
            Route::get('delivery_xml/dlv_view', 'delivery_xmlController@dlv_view');

            Route::get('test', 'testController@index');
        });
    });

    //[기준정보]
    Route::prefix("standard")->namespace('standard')->group(function () {
        //품목
        Route::get('std01', 'std01Controller@index');
        Route::get('std01/search', 'std01Controller@search');
        Route::post('std01/GetOpt', 'std01Controller@GetOpt');
        Route::post('std01/Command', 'std01Controller@Command');
        Route::post('std01/CheckOpt', 'std01Controller@CheckOpt');

        Route::post('std01', 'std01Controller@store');
        Route::get('std01/create', 'std01Controller@create');
        Route::get('std01/show', 'std01Controller@show');
        Route::get('std01/get', 'std01Controller@get');
        Route::put('std01/{opt_kind_cd}', 'std01Controller@update');
        Route::delete('std01/{opt_kind_cd}', 'std01Controller@delete');

        //업체
        Route::get('std02', 'std02Controller@index');
        Route::get('std02/search', 'std02Controller@search');
        Route::get('std02/show/{com_id?}', 'std02Controller@show');
        Route::get('std02/getcate1/{com_id?}', 'std02Controller@getdisplaycategory');
        Route::get('std02/getcate2/{com_id?}', 'std02Controller@getitemcategory');
        Route::get('std02/addcate/{com_id?}', 'std02Controller@addcategory');
        Route::get('std02/delcate/{com_id?}', 'std02Controller@DelCategory');


        Route::put('std02/checkid/{com_id?}', 'std02Controller@checkcomid');
        Route::put('std02/comm', 'std02Controller@Command');


        //브랜드
        Route::get('std03', 'std03Controller@index');
        Route::get('std03/search', 'std03Controller@search');
        Route::post('std03/CheckBrand', 'std03Controller@CheckBrand');
        Route::post('std03/Command', 'std03Controller@Command');
        Route::get('std03/GetBrand/{brand}', 'std03Controller@GetBrand');
        Route::post('std03/GetBrandSummary', 'std03Controller@GetBrandSummary');
        Route::post('std03/GetBrandList', 'std03Controller@GetBrandList');


        //카테고리
        Route::get('std04', 'std04Controller@index');
        Route::get('std04/search', 'std04Controller@search');
        Route::get('std04/detail', 'std04Controller@detail');
        Route::get('std04/GetMemGroup', 'std04Controller@GetMemberGroup');


        Route::put('std04/detail/{catCd?}', 'std04Controller@Command');


        //FAQ
        Route::get('std05', 'std05Controller@index');
        Route::get('std05/search', 'std05Controller@search');
        Route::get('std05/show/{idx?}', 'std05Controller@show');

        Route::put('std05/show/{idx?}', 'std05Controller@store');

        Route::delete('std05/show/{idx?}', 'std05Controller@delete');

        //광고
        Route::get('std10', 'std10Controller@index');
        Route::get('std10/search', 'std10Controller@search');
        Route::get('std10/show/{code?}', 'std10Controller@show');

        Route::put('std10/show/{code?}', 'std10Controller@store');

        Route::delete('std10/show/{code}', 'std10Controller@delete');

        //광고 할인
        Route::prefix("std11")->group(function () {
            $cont = 'std11Controller';

            Route::get('/', $cont . '@index');
            Route::get('show/dc/{no?}', $cont . '@show');
            Route::put('show/dc/{no?}', $cont . '@DCStore');

            Route::prefix("search")->group(function () use ($cont) {
                Route::get('/', $cont . '@search');
                Route::get('dc-brand', $cont . '@searchDCBrand');
                Route::get('dc-goods', $cont . '@searchDCGoods');
                Route::get('dc-ex-goods', $cont . '@searchDCExGoods');
            });

            Route::prefix("dc")->group(function () use ($cont) {
                //할인 추가
                Route::get('brand/{no}', $cont . '@showDCBrand');
                Route::get('goods/{no}', $cont . '@showDCGoods');

                //할인 추가
                Route::post('brand/{no}', $cont . '@addDCBrand');
                Route::post('goods/{no}', $cont . '@addDCGoods');
                Route::post('ex-goods/{no}', $cont . '@addDCExGoods');

                //할인 수정
                Route::put('brand/{no}', $cont . '@updateDCBrand');
                Route::put('goods/{no}', $cont . '@updateDCGoods');
                Route::put('ex-goods/{no}', $cont . '@updateDCExGoods');

                //할인 삭제
                Route::delete('brand/{no}', $cont . '@deleteDCBrand');
                Route::delete('goods/{no}', $cont . '@deleteDCGoods');
                Route::delete('ex-goods/{no}', $cont . '@deleteDCExGoods');
            });
        });

        //템플릿
        Route::get('std20', 'std20Controller@index');
        Route::get('std20/search', 'std20Controller@search');
        Route::post('std20/GetInfo', 'std20Controller@GetInfomation');
        Route::post('std20/Command', 'std20Controller@Command');

        //코드
        Route::get('std51', 'std51Controller@index');
        Route::get('std51/search', 'std51Controller@search');
        Route::get('std51/create', 'std51Controller@create');
        Route::post('std51', 'std51Controller@insert');
        Route::get('std51/{code?}', 'std51Controller@show');
        Route::post('std51/{code?}', 'std51Controller@update');
        Route::delete('std51/{code}', 'std51Controller@delete');

        // Route::post('std51', 'std51Controller@store');
        // Route::get('std51/{code?}/search', 'std51Controller@data_search');
        // Route::post('std51/{code?}/save', 'std51Controller@data_add');
        // Route::post('std51/{code?}/del', 'std51Controller@data_del');
        // Route::post('std51/{code?}/seq', 'std51Controller@data_seq');

        // 테스트
        Route::get('std52', 'std52Controller@index');
    });

    Route::prefix("product")->namespace('product')->group(function () {
        // 상품관리
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
        Route::get('prd01/{no}/get-option-name', 'prd01Controller@get_option_name');
        Route::get('prd01/{no}/get-option-stock', 'prd01Controller@get_option_stock');
        Route::get("prd01/{no}/get-option", "prd01Controller@get_option");
        Route::get("prd01/{no}/get-similar-goods", "prd01Controller@get_similar_goods");
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

        Route::post("prd01/{no}/option-kind-add", "prd01Controller@add_option_kind");
        Route::post("prd01/{no}/option-kind-del", "prd01Controller@del_option_kind");
        Route::post("prd01/{no}/option-save", "prd01Controller@save_option");
        Route::post("prd01/{no}/similar-goods-add", "prd01Controller@save_similar_goods");

        Route::delete("prd01/{no}/similar-goods-del", "prd01Controller@delete_similar_goods");

        Route::post('prd01/add-related-goods', 'prd01Controller@addRelatedGoods');
        Route::post('prd01/del-related-good', 'prd01Controller@delRelatedGood');

        // 이미지 관리
        Route::post('prd02/{idx}/upload', 'prd02Controller@upload');
        Route::get('prd02/{no}/image', 'prd02Controller@index');
        Route::get("prd02/slider", "prd02Controller@index_slider");

        // 상품도매
        Route::get('prd03', 'prd03Controller@index');
        Route::get('prd03/search', 'prd03Controller@search');
        Route::get('prd03/wonga', 'prd03Controller@wonga_index');
        Route::get('prd03/wonga_search', 'prd03Controller@wonga_search');

        Route::post('prd03/price-save', 'prd03Controller@save_price');

        // 세일 관리
        Route::get('prd04', 'prd04Controller@index');
        Route::get('prd04/search', 'prd04Controller@search');
        Route::post('prd04/time-sale-off', 'prd04Controller@timeSaleOff');

        Route::get('prd05', 'prd05Controller@index');
        Route::get('prd05/search', 'prd05Controller@search');
        Route::get('prd05/column_search', 'prd05Controller@column_search');

        Route::get('prd05/delete', 'prd05Controller@delete');
        Route::get('prd05/update', 'prd05Controller@update');
        Route::post('prd05/load_excel', 'prd05Controller@load_excel');
        Route::get('prd05/show_excel', 'prd05Controller@show_excel');
        Route::get('prd05/down_excel', 'prd05Controller@down_excel');

        // 원사이즈 및 클리어런스
        Route::get('prd06', 'prd06Controller@index');
        Route::get('prd06/search', 'prd06Controller@search');
        Route::post('prd06/sale-on', 'prd06Controller@saleOn');
        Route::post('prd06/sale-off', 'prd06Controller@saleOff');

        // 상품관리 - 일괄등록
        Route::get('prd07', 'prd07Controller@index');
        Route::post('prd07/enroll', 'prd07Controller@enroll');

        Route::get('prd08', 'prd08Controller@index');
        Route::get('prd09', 'prd09Controller@index');

        Route::get('prd10', 'prd10Controller@index');
        Route::get('prd10/search', 'prd10Controller@search');

        Route::get('prd10/{code?}/search', 'prd10Controller@goods_search');
        Route::post('prd10/{code?}/save', 'prd10Controller@goods_add');
        Route::post('prd10/{code?}/del', 'prd10Controller@goods_del');
        Route::post('prd10/{code?}/seq', 'prd10Controller@goods_seq');
        Route::post('prd10/{code?}/disp', 'prd10Controller@goods_disp');

        // 섹션관리
        Route::get('prd11', 'prd11Controller@index');
        Route::post('prd11', 'prd11Controller@store');
        Route::get('prd11/search', 'prd11Controller@search');
        Route::get('prd11/create', 'prd11Controller@create');
        Route::get('prd11/{code?}', 'prd11Controller@show');
        Route::put('prd11/{code?}', 'prd11Controller@update');
        Route::delete('prd11/{code}', 'prd11Controller@delete');

        Route::get('prd11/{code?}/search', 'prd11Controller@goods_search');
        Route::post('prd11/{code?}/save', 'prd11Controller@goods_add');
        Route::post('prd11/{code?}/del', 'prd11Controller@goods_del');
        Route::post('prd11/{code?}/seq', 'prd11Controller@goods_seq');

        // 기획전관리
        Route::get('prd12', 'prd12Controller@index');
        Route::post('prd12', 'prd12Controller@store');
        Route::get('prd12/search', 'prd12Controller@search');
        Route::get('prd12/create', 'prd12Controller@create');
        Route::get('prd12/{code?}', 'prd12Controller@show');
        Route::put('prd12/{code?}', 'prd12Controller@update');
        Route::delete('prd12/{code}', 'prd12Controller@delete');

        Route::get('prd12/{code?}/search', 'prd12Controller@goods_search');
        Route::post('prd12/{code?}/save', 'prd12Controller@goods_add');
        Route::post('prd12/{code?}/del', 'prd12Controller@goods_del');
        Route::post('prd12/{code?}/seq', 'prd12Controller@goods_seq');

        Route::get('prd12/{code?}/search_folder', 'prd12Controller@folder_search');
        Route::post('prd12/{code?}/save_folder', 'prd12Controller@folder_save');
        Route::post('prd12/{code?}/del_folder', 'prd12Controller@folder_del');
        Route::post('prd12/{code?}/seq_folder', 'prd12Controller@folder_seq');

        Route::post('prd12/{code?}/save_category', 'prd12Controller@category_save');
        Route::get('prd12/{code?}/search_category', 'prd12Controller@category_search');

        // 제휴 - 네이버 지식쇼핑
        Route::get('prd20', 'prd20Controller@index');
        Route::post('prd20', 'prd20Controller@store');
        Route::get('prd20/search', 'prd20Controller@search');
        Route::get('prd20/{code?}', 'prd20Controller@show');
        Route::post('prd20/{code?}', 'prd20Controller@store');

        // 제휴 -크리테오
        Route::get('prd22', 'prd22Controller@index');
        Route::post('prd22', 'prd22Controller@store');
        Route::get('prd22/search', 'prd22Controller@search');
        Route::get('prd22/{code?}', 'prd22Controller@show');
        Route::post('prd22/{code?}', 'prd22Controller@store');

        //사방넷
        //사방넷 - 상품연동
        Route::get('prd30', 'prd30Controller@index');
        Route::get('prd30/search', 'prd30Controller@search');
        Route::put('prd30/stock', 'prd30Controller@stockshop');
        Route::put('prd30/add', 'prd30Controller@addshop_goods');
        Route::get('prd30/pattern', 'prd30Controller@pattern');
        Route::put('prd30/pattern', 'prd30Controller@pattern_save');
        Route::put('prd30/delete', 'prd30Controller@deleteshop_goods');

        //사방넷 - 주문연동
        Route::get('prd31', 'prd31Controller@index');
        Route::get('prd31/search', 'prd31Controller@search');
        Route::get('prd31/option/{goods_no?}', 'prd31Controller@skuoption');
        Route::put('prd31/option', 'prd31Controller@goodsopt_add');
        Route::get('prd31/option_search', 'prd31Controller@skuoption_search');
        Route::put('prd31/get-order', 'prd31Controller@get_order');
        Route::put('prd31/add-order', 'prd31Controller@add_order');
        Route::put('prd31/del-order', 'prd31Controller@del_order');
        Route::put('prd31/dlv-order', 'prd31Controller@dlv_order');
    });

    //[재고]
    Route::prefix("stock")->namespace('stock')->group(function () {

        // 재고관리
        Route::get('stk01', 'stk01Controller@index');
        Route::get('stk01/search', 'stk01Controller@search');
        Route::post('stk01', 'stk01Controller@update');
        Route::get('stk01/option/search/{goods_no}', 'stk01Controller@show_search');
        Route::get('stk01/{goods_no?}', 'stk01Controller@show');

        // 입출고내역
        Route::get('stk05', 'stk05Controller@index');
        Route::get('stk05/search', 'stk05Controller@search');

        // 재고입고알림
        Route::get('stk06', 'stk06Controller@index');
        Route::get('stk06/search', 'stk06Controller@search');
        Route::get('stk06/restock', 'stk06Controller@showRestockingRequest');
        Route::get('stk06/search_restock', 'stk06Controller@searchRestockingRequest');
        Route::put('stk06/update_restock', 'stk06Controller@sendSMS');
        Route::delete('stk06/delete', 'stk06Controller@deleteRestockingRequest');

        // 발주
        Route::get('stk10', 'stk10Controller@index');
        Route::get('stk10/search', 'stk10Controller@search');
        Route::put('stk10/update', 'stk10Controller@changeState');
        Route::delete('stk10/delete', 'stk10Controller@delete');
        Route::prefix('stk10/buy')->group(function () {
            Route::get('/', 'stk10Controller@showBuy');
            Route::get('/search', 'stk10Controller@searchBuy');
            Route::post('/add', 'stk10Controller@addBuy');
        });

        // 입고
        Route::get('stk11', 'stk11Controller@index');
        Route::get('stk11/search', 'stk11Controller@search');
        Route::get('stk11/show/{cmd?}/{stock_no?}', 'stk11Controller@show');
        Route::post('stk11/comm', 'stk11Controller@command');

        // 상품 판매율
        Route::get('stk20', 'stk20Controller@index');
        Route::get('stk20/search', 'stk20Controller@search');

        // XMD 상품 매칭
        Route::get('stk30', 'stk30Controller@index');
        Route::put('stk30', 'stk30Controller@select_update');
        Route::get('stk30/search', 'stk30Controller@search');
        Route::get('stk30/show', 'stk30Controller@show');
        Route::put('stk30/show', 'stk30Controller@update');
        Route::post('stk30/upload', 'stk30Controller@upload');

        // XMD 상품 재고예외 관리
        Route::get('stk31', 'stk31Controller@index');
        Route::put('stk31', 'stk31Controller@update');
        Route::get('stk31/search', 'stk31Controller@search');
        Route::get('stk31/show', 'stk31Controller@show');
        Route::post('stk31/show', 'stk31Controller@save');
        Route::put('stk31/delete', 'stk31Controller@delete');

        // XMD 재고파일 관리
        Route::get('stk32', 'stk32Controller@index');
        Route::get('stk32/search', 'stk32Controller@search');
        Route::put('stk32/delete', 'stk32Controller@delete');
        Route::get('stk32/show', 'stk32Controller@show');
        Route::put('stk32/show', 'stk32Controller@update');
        Route::post('stk32/upload', 'stk32Controller@upload');

        // XMD 재고 등록
        Route::get('stk33', 'stk33Controller@index');
        Route::get('stk33/search', 'stk33Controller@search');
        Route::get('stk33/show/{idx?}', 'stk33Controller@show');
        Route::put('stk33/show', 'stk33Controller@update');
        Route::get('stk33/detail_search', 'stk33Controller@detail_search');
        Route::get('stk33/insert', 'stk33Controller@show_insert');
        Route::post('stk33/upload', 'stk33Controller@upload');

        // XMD 재고등록 오류관리
        Route::get('stk34', 'stk34Controller@index');
        Route::get('stk34/search', 'stk34Controller@search');

        // XMD 재고 모니터링
        Route::get('stk35', 'stk35Controller@index');
        Route::get('stk35/search', 'stk35Controller@search');
        Route::get('stk35/show', 'stk35Controller@show');
        Route::put('stk35/show', 'stk35Controller@update');
        Route::put('stk35/delete', 'stk35Controller@delete');
        Route::post('stk35/upload', 'stk35Controller@upload');
    });

    //[배송 / 주문]
    Route::prefix("order")->namespace('order')->group(function () {
        //주문내역
        Route::prefix("ord01")->group(function () {
            $cont = 'ord01Controller';

            Route::get('/', $cont . '@index');
            Route::get('receipt/{ord_no}', $cont . '@receipt');
            Route::get('dlv/{ord_no}/{ord_opt_no}', $cont . '@dlv');
            Route::get('refund/{ord_no}/{ord_opt_no?}', $cont . '@refund');
            Route::get('search/{cmd}', $cont . '@search');
            Route::get('order-list/{ord_no}', $cont . '@order_list');
            Route::get('order-goods/{ord_no}/{ord_opt_no}', $cont . '@order_goods');
            Route::get('{ord_no}/{ord_opt_no?}', $cont . '@show');
            Route::get('get/{ord_no}/{ord_opt_no?}', $cont . '@get');
            Route::get('{ord_no}/{ord_opt_no}/cash', $cont . '@show_cash');
            Route::get('{ord_no}/cash/list', $cont . '@search_cash_receipt_list');
            Route::get('{ord_no}/{ord_opt_no}/tax', $cont . '@show_tax');
            Route::get('{ord_no}/tax/list', $cont . '@search_tax_receipt_list');

            Route::put('dlv-info-save/{ord_no}', $cont . '@dlv_info_save');
            Route::put('refund-save/{ord_opt_no}', $cont . '@refund_save');
            Route::put('claim-save', $cont . '@claim_save');
            Route::put('order-save', $cont . '@order_save');
            Route::put('update/order-state', $cont . '@update_order_state');
            Route::put('dlv-comment', $cont . '@dlv_comment');
            Route::put('order-memo', $cont . '@order_memo');
            Route::put('cancel-order', $cont . '@cancel_orders');
            Route::put('confirm-orders', $cont . '@confirm_orders');
            Route::put('claim-message-save', $cont . '@claim_message_save');
            Route::put('order-goods/{ord_no}/{ord_opt_no}', $cont . '@order_goods_save');
            Route::put('{ord_no}/{ord_opt_no}/cash-receipt', $cont . '@set_cash_receipt');
            Route::put('{ord_no}/{ord_opt_no}/tax-receipt', $cont . '@set_tax_receipt');
        });

        //수기판매
        Route::get('ord02', 'ord02Controller@index');
        Route::get('ord02/search', 'ord02Controller@search');
        Route::delete('ord02', 'ord02Controller@del_order');
        Route::post('ord02/save', 'ord02Controller@save');
        Route::get('ord02/{ord_no}/{ord_opt_no?}', 'ord02Controller@show');

        // 수기판매일괄
        Route::get('ord03', 'ord03Controller@index');
        Route::post('ord03/upload/{prefix?}', 'ord03Controller@upload');
        Route::post('ord03/save', 'ord03Controller@save');
        Route::get('ord03/import', 'ord03Controller@import_show');
        Route::get('ord03/fmt', 'ord03Controller@format_show');
        Route::get('ord03/fmt/search', 'ord03Controller@format_search');
        Route::post('ord03/fmt/save', 'ord03Controller@format_save');
        Route::put('ord03/get-fee', 'ord03Controller@get_fee');

        //입금확인
        Route::get('ord05', 'ord05Controller@index');
        Route::get('ord05/search', 'ord05Controller@search');

        Route::put('ord05/pay', 'ord05Controller@pay');

        //입금확인(뱅크다)
        Route::get('ord06', 'ord06Controller@index');
        Route::get('ord06/search', 'ord06Controller@search');
        Route::get('ord06/account', 'ord06Controller@account');
        Route::get('ord06/get_account_list', 'ord06Controller@account_list');
        Route::get('ord06/account_log/{bkdate?}', 'ord06Controller@account_log');
        Route::get('ord06/account_log_search', 'ord06Controller@account_log_search');
        Route::get('ord06/pop_log/{log_no?}', 'ord06Controller@pop_log');

        Route::put('ord06/save_account', 'ord06Controller@save_account');
        Route::put('ord06/del_account', 'ord06Controller@delete_account');
        Route::put('ord06/save_memo', 'ord06Controller@save_memo');

        Route::put('ord06/pay', 'ord06Controller@pay');
        Route::put('ord06/pay_hold', 'ord06Controller@pay_hold');

        //수기판매
        //Route::post('ord20/save', 'ord20Controller@save');
        Route::get('ord20/show', 'ord20Controller@index');

        //배송 출고요청
        Route::get('ord21', 'ord21Controller@index');
        Route::get('ord21/search', 'ord21Controller@search');
        Route::put('ord21/update/state', 'ord21Controller@update_state');
        Route::put('ord21/update/kind', 'ord21Controller@update_kind');

        //배송출고처리
        Route::prefix("ord22")->group(function () {
            $cont = 'ord22Controller';
            Route::get('/', $cont . '@index');
            Route::get('show', $cont . '@show');
            Route::get('show/sale', $cont . '@sale');
            Route::get('search', $cont . '@search');
            Route::get('dlv-import', $cont . '@dlv_import');
            Route::get('dlv-import/search', $cont . '@dlv_import_search');
            Route::get('download/delivery_list', $cont . '@download_delivery_list');
            Route::get('download/baesong_list', $cont . '@download_baesong_list');
            Route::get('download/baesong_list/sale', $cont . '@download_baesong_list_sale');

            Route::put('kind', $cont . '@update_kind');
            Route::put('state', $cont . '@update_state');
            Route::put('out-complete', $cont . '@out_complete');
            Route::put('dlv-import/upload', $cont . '@dlv_import_upload');
        });

        //광고주문내역
        Route::get('ord51', 'ord51Controller@index');
        Route::get('ord51/search', 'ord51Controller@search');
    });

    //[클레임/CS]
    Route::prefix("cs")->namespace('cs')->group(function () {
        //클레임 내역
        Route::get('cs01', 'cs01Controller@index');
        Route::get('cs01/search', 'cs01Controller@search');

        //환불완료(무통장/가상계좌)
        Route::get('cs05', 'cs05Controller@index');
        Route::get('cs05/search', 'cs05Controller@search');

        Route::put('cs05/series_comm', 'cs05Controller@SeriesCommand');
        Route::put('cs05/multi_refunds', 'cs05Controller@Refunds');

        //환불완료(카드/계좌이체)
        Route::get('cs06', 'cs06Controller@index');
        Route::get('cs06/search', 'cs06Controller@search');
        Route::get('cs06/refund', 'cs06Controller@Refund');

        Route::put('cs06/balanceamt', 'cs06Controller@CheckRealBalanceAmt');
        Route::put('cs06/refundcmd', 'cs06Controller@RefundCommand');


        //업체 클레임 조회 (팝업)
        Route::get('cs21', 'cs21Controller@index');
        Route::get('cs21/search', 'cs21Controller@search');
    });

    //회원관리
    Route::prefix("member")->namespace('member')->group(function () {
        //회원관리
        Route::get('mem01/search', 'mem01Controller@search');
        Route::get('mem01/check-id/{id}', 'mem01Controller@check_id');
        Route::get('mem01/download', 'mem01Controller@download');
        Route::get('mem01/download/show', 'mem01Controller@download_show');
        Route::get('mem01/show/search/{type}/{id}', 'mem01Controller@show_search');
        Route::get('mem01/show/{type}/{id?}', 'mem01Controller@show');
        Route::get('mem01/{type?}', 'mem01Controller@index');
        Route::get('mem01/{user_id}/get', 'mem01Controller@get');

        Route::post('mem01/user', 'mem01Controller@add_user');
        Route::post('mem01/user/group/{id}', 'mem01Controller@add_group');

        Route::put('mem01/pw/{id}', 'mem01Controller@change_pw');
        Route::put('mem01/user/{id}', 'mem01Controller@edit_user');
        Route::put('mem01/active-user/{id}', 'mem01Controller@active_user');

        Route::delete('mem01/user/{id}', 'mem01Controller@delete_user');
        Route::delete('mem01/user/group/{id}', 'mem01Controller@del_group');
        //
        //휴면회원
        Route::get('mem02', 'mem02Controller@index');
        Route::get('mem02/search', 'mem02Controller@search');
        Route::put('mem02/active', 'mem02Controller@active');

        //회원계급
        Route::get('mem03', 'mem03Controller@index');
        Route::get('mem03/search', 'mem03Controller@search');
        Route::get('mem03/search/grade', 'mem03Controller@search_grade');
        Route::get('mem03/search/ext-goods/{id}', 'mem03Controller@search_ext_goods');
        Route::get('mem03/search/group-user/{id}', 'mem03Controller@search_group_user');

        Route::get('mem03/grade', 'mem03Controller@grade');
        Route::get('mem03/ext-goods/{id}', 'mem03Controller@ext_goods');
        Route::get('mem03/group-user/{id}', 'mem03Controller@group_user');
        Route::get('mem03/group-value/{id}', 'mem03Controller@group_value');
        Route::get('mem03/show/{type?}/{id?}', 'mem03Controller@show');

        Route::post('mem03', 'mem03Controller@add_group');
        Route::post('mem03/ext-goods/{id}', 'mem03Controller@add_ext_goods');
        Route::post('mem03/group-user/{id}', 'mem03Controller@add_group_user');

        Route::put('mem03/{id?}', 'mem03Controller@edit_group');
        Route::put('mem03/grade/{id}', 'mem03Controller@edit_grade');
        Route::put('mem03/group-user/{id}', 'mem03Controller@move_group_user');

        Route::delete('mem03/{id?}', 'mem03Controller@del_group');
        Route::delete('mem03/ext-goods/{id}', 'mem03Controller@del_ext_goods');
        Route::delete('mem03/group-user/{id}', 'mem03Controller@del_group_user');

        //적립금현황
        Route::get('mem04', 'mem04Controller@index');
        Route::get('mem04/search', 'mem04Controller@search');

        //적립금내역
        Route::get('mem05', 'mem05Controller@index');
        Route::get('mem05/search', 'mem05Controller@search');

        //1:1문의
        Route::get('mem20/search', 'mem20Controller@search');
        Route::get('mem20/{type?}', 'mem20Controller@index');
        Route::get('mem20/show/{idx?}', 'mem20Controller@show');
        Route::put('mem20/get-data-image/{idx}', 'mem20Controller@GetImage');

        Route::put('mem20/check', 'mem20Controller@Check');
        Route::put('mem20/show/{idx?}', 'mem20Controller@save');
        Route::put('mem20/change', 'mem20Controller@ChangeShow');

        //상품Q&A
        Route::get('mem21/search', 'mem21Controller@search');
        Route::get('mem21/{type?}', 'mem21Controller@index');
        Route::get('mem21/show/{qa_no?}', 'mem21Controller@show');

        Route::put('mem21/check/{qa_no?}', 'mem21Controller@check');
        Route::put('mem21/comm/{qa_no?}', 'mem21Controller@command');


        //상품명
        Route::get('mem22', 'mem22Controller@index');
        Route::get('mem22/search', 'mem22Controller@search');
        Route::get('mem22/{no?}', 'mem22Controller@show');
        Route::get('mem22/get_template/{no?}', 'mem22Controller@GetTemplate');

        Route::post('mem22/add_comment', 'mem22Controller@addComment');

        Route::put('mem22/change_best_type/', 'mem22Controller@ChangeBestType');
        Route::put('mem22/delcmd', 'mem22Controller@delete');
        Route::put('mem22/get_comment/{no?}', 'mem22Controller@GetComment');
        Route::put('mem22/del_comment/{cmt_no?}', 'mem22Controller@DelComment');
        Route::put('mem22/change', 'mem22Controller@ChangeUse');
        Route::put('mem22/change-best-yn', 'mem22Controller@ChangeBestYn');
        Route::put('mem22/change-use-yn', 'mem22Controller@ChangeUseYn');
    });

    //[프로모션/promotion]
    Route::prefix("promotion")->namespace('promotion')->group(function () {

        Route::get('prm01', 'prm01Controller@index');
        Route::get('prm01/search', 'prm01Controller@search');
        Route::get('prm01/create', 'prm01Controller@create');
        Route::get('prm01/{no}', 'prm01Controller@show');
        Route::get('prm01/del/{no}', 'prm01Controller@destroy');

        Route::put('prm01/store', 'prm01Controller@store');
        Route::put('prm01/edit/{no}', 'prm01Controller@update');



        Route::get('prm02', 'prm02Controller@index');
        Route::get('prm02/search', 'prm02Controller@search');
        Route::get('prm02/{idx?}', 'prm02Controller@Detail');

        Route::put('prm02/store', 'prm02Controller@store');

        Route::get('prm03', 'prm03Controller@index');
        Route::get('prm03/search', 'prm03Controller@search');
        Route::get('prm03/{credit_card_cd}', 'prm03Controller@GetInfomation');

        Route::put('prm03/store/{cmd?}', 'prm03Controller@Command');

        Route::get('prm04', 'prm04Controller@index');
        Route::get('prm04/search', 'prm04Controller@search');
        Route::get('prm04/create', 'prm04Controller@create');
        Route::get('prm04/show/{idx}', 'prm04Controller@show');

        Route::put('prm04/add', 'prm04Controller@add');
        Route::put('prm04/update/{idx}', 'prm04Controller@update');
        Route::delete('prm04/delete/{idx}', 'prm04Controller@delete');

        Route::get('prm05', 'prm05Controller@index');
        Route::get('prm05/code/{code?}', 'prm05Controller@code');
        Route::get('prm05/show/{type}/{code?}', 'prm05Controller@show');
        Route::get('prm05/search', 'prm05Controller@search');

        Route::post('prm05', 'prm05 Controller@add_banner');

        Route::put('prm05/banner-reset', 'prm05Controller@banner_reset');
        Route::put('prm05/{code}', 'prm05Controller@edit_banner');

        Route::delete('prm05/{code}', 'prm05Controller@delete_banner');

        Route::get('prm06', 'prm06Controller@index');
        Route::get('prm06/search', 'prm06Controller@search');
        Route::get('prm06/search_goods/{gift_no?}', 'prm06Controller@getGoods');
        Route::get('prm06/search_exgoods/{gift_no?}', 'prm06Controller@getExGoods');
        Route::get('prm06/create', 'prm06Controller@create');

        Route::get('prm06/{gift_no?}', 'prm06Controller@show');
        Route::post('prm06/comm', 'prm06Controller@command');
        Route::put('prm06/del', 'prm06Controller@delGift');

        Route::get('prm10', 'prm10Controller@index');
        Route::get('prm10/search', 'prm10Controller@search');
        Route::get('prm10/search/used/{no}', 'prm10Controller@search_used');
        Route::get('prm10/search/serial/{no}', 'prm10Controller@search_serial');
        Route::get('prm10/search/gift/user', 'prm10Controller@gift_user_search');
        Route::get('prm10/search/gift/coupon', 'prm10Controller@gift_coupon_search');
        Route::get('prm10/search/show/{type}/{no}', 'prm10Controller@grid_data');
        Route::get('prm10/used/{no}', 'prm10Controller@used_show');
        Route::get('prm10/serial/{no}', 'prm10Controller@serial_show');
        Route::get('prm10/gift', 'prm10Controller@gift_show');
        Route::get('prm10/gift/auto', 'prm10Controller@auto_show');
        Route::get('prm10/show/{type}/{no?}', 'prm10Controller@show');
        Route::get('prm10/{type}', 'prm10Controller@index');

        Route::post('prm10', 'prm10Controller@add_coupon');

        Route::put('prm10/{no}', 'prm10Controller@edit_coupon');
        Route::put('prm10/gift/coupon', 'prm10Controller@gift_coupon');
        Route::put('prm10/gift/auto/{no}', 'prm10Controller@edit_auto_coupon');

        Route::delete('prm10/used/{no}', 'prm10Controller@del_used_coupon');
        Route::delete('prm10/{no}', 'prm10Controller@del_coupon');

        Route::get('prm20', 'prm20Controller@index');
        Route::get('prm20/search', 'prm20Controller@search');
        Route::get('prm20/detail', 'prm20Controller@newEvent');
        Route::get('prm20/detail/{idx?}', 'prm20Controller@Detail');
        Route::get('prm20/member/{idx?}', 'prm20Controller@ViewMember');
        Route::get('prm20/mem_search/{idx?}', 'prm20Controller@MemberSearch');
        Route::get('prm20/attend/{idx?}', 'prm20Controller@ViewAttend');
        Route::get('prm20/attend_search/{idx?}', 'prm20Controller@AttendSearch');

        Route::put('prm20/winner/{user_id?}', 'prm20Controller@SetWinner');
        Route::put('prm20/store', 'prm20Controller@Save');
        Route::put('prm20/del', 'prm20Controller@Del');



        Route::get('prm21', 'prm21Controller@index');
        Route::get('prm21/search', 'prm21Controller@search');
        Route::get('prm21/event_info/{idx?}', 'prm21Controller@GetInfomation');
        Route::get('prm21/search_comment/{idx?}', 'prm21Controller@GetInformationList');
        Route::get('prm21/arraygift/{idx?}', 'prm21Controller@ArrayGjift');


        Route::put('prm21/store', 'prm21Controller@store');
        Route::put('prm21/change_list/{event_idx?}', 'prm21Controller@change_list');

        Route::get('prm30', 'prm30Controller@index');
        Route::get('prm30/search', 'prm30Controller@search');

        Route::put('prm30/mpv', 'prm30Controller@edit_mpv');
        Route::put('prm30/rank', 'prm30Controller@edit_rank');
        Route::put('prm30/disp', 'prm30Controller@edit_disp');
        Route::put('prm30/point', 'prm30Controller@edit_point');
        Route::put('prm30/ex-pop', 'prm30Controller@edit_ex_pop');
        Route::put('prm30/synonym', 'prm30Controller@edit_synonym');

        Route::get('prm31', 'prm31Controller@index');
        Route::get('prm31/search', 'prm31Controller@search');

        Route::get('prm32', 'prm32Controller@index');
        Route::get('prm32/show/{idx?}', 'prm32Controller@show');
        Route::get('prm32/search', 'prm32Controller@search');

        Route::put('prm32/save/{idx}', 'prm32Controller@edit_search');
        Route::post('prm32/save', 'prm32Controller@add_search');

        //트레킹 컨텐츠
        // - 트레킹 공지사항
        Route::get('prm11', 'prm11Controller@index');
        Route::get('prm11/search', 'prm11Controller@search');
        Route::get('prm11/create', 'prm11Controller@create');
        Route::post('prm11/create', 'prm11Controller@save');
        Route::get('prm11/show/{idx?}', 'prm11Controller@show');
        Route::get('prm11/event-pop', 'prm11Controller@event_list');
        Route::get('prm11/event-search', 'prm11Controller@event_search');
        Route::get('prm11/del', 'prm11Controller@destroy');
        // - 트레킹 명단 관리
        Route::get('prm13', 'prm13Controller@index');
        Route::put('prm13', 'prm13Controller@chgstate_arr');
        Route::get('prm13/search', 'prm13Controller@search');
        Route::get('prm13/show/{order_no}', 'prm13Controller@show');
        Route::get('prm13/show/{order_no}/{user_code}', 'prm13Controller@show2');
        Route::put('prm13/show/{order_no}/{user_code}', 'prm13Controller@update');
        Route::put('prm13/chgstate', 'prm13Controller@chgstate');
        // - 트레킹 결재내역 관리
        Route::get('prm12', 'prm12Controller@index');
        Route::get('prm12/search', 'prm12Controller@search');
        //Route::get('prm12/{order_no}', 'prm12Controller@show'); 사용하지 않음
    });

    //[커뮤니티/community]
    Route::prefix("community")->namespace('community')->group(function () {
        Route::get('com01', 'com01Controller@index');
        Route::get('com01/search', 'com01Controller@search');
        Route::get('com01/show/{type}/{id?}', 'com01Controller@show');
        Route::get('com01/id-chk/{id}', 'com01Controller@id_chk');

        Route::post('com01/show/{id}', 'com01Controller@add_comunity');
        Route::put('com01/show/{id}', 'com01Controller@edit_comunity');

        Route::delete('com01/show/{id}', 'com01Controller@del_comunity');

        Route::get('com02', 'com02Controller@index');
        Route::get('com02/search', 'com02Controller@search');
        Route::get('com02/detail', 'com02Controller@Detail');
        Route::get('com02/{b_no?}', 'com02Controller@Read');
        Route::get('com02/{type}/{id}', 'com02Controller@index');

        Route::put('com02/add_comment/', 'com02Controller@AddComment');
        Route::put('com02/editsecret/{c_no?}', 'com02Controller@EditSecret');
        Route::put('com02/delcomment/{c_no?}', 'com02Controller@DelComment');
        Route::put('com02/store', 'com02Controller@Save');
        Route::put('com02/del', 'com02Controller@Del');


        Route::get('com03', 'com03Controller@index');
        Route::get('com03/search', 'com03Controller@search');
        Route::get('com03/{type}/{id}', 'com03Controller@index');

        Route::put('com03/editsecret', 'com03Controller@EditSecret');
        Route::put('com03/delcomment', 'com03Controller@DelComment');
    });

    //[커뮤니티/community]
    Route::prefix("product")->namespace('product')->group(function () {

        Route::get('prd08', 'prd08Controller@index');
        Route::get('prd08/search', 'prd08Controller@search');

        Route::put('prd08/save_point', 'prd08Controller@Command');

        Route::get('prd09', 'prd09Controller@index');
        Route::get('prd09/search', 'prd09Controller@search');
    });


    Route::prefix("sales")->namespace('sales')->group(function () {
        //일별 매출 통계
        Route::get('sal02', 'sal02Controller@index');
        Route::get('sal02/search', 'sal02Controller@search');

        //월별 매출 통계
        Route::get('sal03', 'sal03Controller@index');
        Route::get('sal03/search', 'sal03Controller@search');

        //상품별 매출 통계
        Route::get('sal04', 'sal04Controller@index');
        Route::get('sal04/search', 'sal04Controller@search');

        //업체별 매출 통계
        Route::get('sal05', 'sal05Controller@index');
        Route::get('sal05/search', 'sal05Controller@search');

        //업체별 매출 통계
        Route::get('sal06', 'sal06Controller@index');
        Route::get('sal06/search', 'sal06Controller@search');

        //일별 주문 통계
        Route::get('sal22', 'sal22Controller@index');
        Route::get('sal22/search', 'sal22Controller@search');

        //상품별 주문 통계
        Route::get('sal23', 'sal23Controller@index');
        Route::get('sal23/search', 'sal23Controller@search');

        //회원별 주문 통계
        Route::get('sal25', 'sal25Controller@index');
        Route::get('sal25/search', 'sal25Controller@search');

        // 광고별 통계
        Route::get('sal26', 'sal26Controller@index');
        Route::get('sal26/search', 'sal26Controller@search');
    });

    Route::prefix("cs")->namespace('cs')->group(function () {
        //일별 클레임 통계
        Route::get('cs71', 'cs71Controller@index');
        Route::get('cs71/search', 'cs71Controller@search');

        //월별 클레임 통계
        Route::get('cs72', 'cs72Controller@index');
        Route::get('cs72/search', 'cs72Controller@search');

        //상품별 클레임 통계
        Route::get('cs73', 'cs73Controller@index');
        Route::get('cs73/search', 'cs73Controller@search');
    });

    Route::prefix("partner")->namespace('partner')->group(function () {

        //입점업체 공지사항
        Route::get('pat01', 'pat01Controller@index');
        Route::get('pat01/search', 'pat01Controller@search');
        Route::get('pat01/create', 'pat01Controller@create');
        Route::get('pat01/{no}', 'pat01Controller@show');
        Route::get('pat01/del/{no}', 'pat01Controller@destroy');

        Route::put('pat01/store', 'pat01Controller@store');
        Route::put('pat01/edit/{no}', 'pat01Controller@update');


        //입점업체 문의
        Route::get('pat02', 'pat02Controller@index');
        Route::get('pat02/search', 'pat02Controller@search');
        Route::put('pat02/{idx}', 'pat02Controller@update');
        Route::get('pat02/{idx}', 'pat02Controller@show');
    });

    Route::prefix("account")->namespace('account')->group(function () {

        // 정산내역
        Route::get('acc01', 'acc01Controller@index');
        Route::get('acc01/search', 'acc01Controller@search');

        // 정산내역
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

        // 정산 및 지급계산서
        Route::get('acc04', 'acc04Controller@index');
        Route::get('acc04/search', 'acc04Controller@search');
        Route::prefix('acc04/show')->group(function () {
            Route::match(['get', 'post'], '/', 'acc04Controller@show');
            Route::get('/search', 'acc04Controller@show_search');
        });
        Route::prefix('acc04/tax')->group(function () {
            Route::post('/pub', 'acc04Controller@pubTax');
            Route::post('/pay', 'acc04Controller@payTaxSheet');
        });

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

        //그룹관리
        Route::get('sys03', 'sys03Controller@index');
        Route::post('sys03', 'sys03Controller@store');
        Route::get('sys03/search', 'sys03Controller@search');
        Route::get('sys03/create', 'sys03Controller@create');
        Route::get('sys03/{code?}', 'sys03Controller@show');
        Route::put('sys03/{code?}', 'sys03Controller@update');
        Route::delete('sys03/{code}', 'sys03Controller@delete');

        Route::get('sys03/{code?}/search', 'sys03Controller@user_search');

        //환경설정
        Route::get('sys04', 'sys04Controller@index');
        Route::post('sys04', 'sys04Controller@store');
        Route::get('sys04/search', 'sys04Controller@search');
        Route::get('sys04/create', 'sys04Controller@create');
        Route::get('sys04/{type}/{name}/{idx}', 'sys04Controller@show');
        Route::get('sys04/get/{type}/{name}/{idx}', 'sys04Controller@get');
        Route::put('sys04/{type}/{name}/{idx}', 'sys04Controller@update');
        Route::delete('sys04/{type}/{name}/{idx}', 'sys04Controller@delete');
    });

    //XMD(개발작업)
    Route::prefix("xmd")->namespace('xmd')->group(function () {
        //코드관리
        Route::prefix("code")->namespace('code')->group(function () {
            //시스템 부속코드
            Route::get('code01',			'code01Controller@index');
			Route::get('code01/search',		'code01Controller@search');
			Route::get('code01/show',		'code01Controller@show');
			Route::put('code01/show',		'code01Controller@update');
			Route::post('code01/upload',	'code01Controller@upload');

            //매장관리
            Route::get('code02',			'code02Controller@index');
			Route::get('code02/search',		'code02Controller@search');
			Route::get('code02/show',		'code02Controller@show');
			Route::put('code02/show',		'code02Controller@update');
			Route::post('code02/upload',	'code02Controller@upload');
			Route::get('code02/view/{com_id}',	'code02Controller@view');
			Route::post('code02/view/{com_id}',	'code02Controller@store_update');
            Route::delete('code02/view/{com_id}', 'code02Controller@delete');
        });

        //매장관리
        Route::prefix("store")->namespace('store')->group(function () {
            //매장판매일보
            Route::get('store01',			'store01Controller@index');
			Route::get('store01/search',	'store01Controller@search');
			Route::get('store01/show',		'store01Controller@show');
			Route::post('store01/show',		'store01Controller@update');
			Route::post('store01/upload',	'store01Controller@upload');
        });

	});

});

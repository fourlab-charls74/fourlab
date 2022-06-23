@extends('store_with.layouts.layout')
@section('title','수선관리 추가')
@section('content')

<style>

    .search_cum_form .row {
        margin-top: 20px;
    }

    .card, #search-area .card-header {
        background: #eeeef0 !important;
    }

    .card {
        border: 1px solid #ddd;
    }
    .required::after {
        position: relative;
        top: 2px;
    }
</style>

<div class="page_tit">
    <h3 class="d-inline-flex">수선관리 추가</h3>
    <div class="d-inline-flex location">
        <span class="home"></span>
        <span>/ 고객 / 수선관리 / 추가</span>
    </div>
</div>

<form method="get" name="search">
    <div id="search-area" class="search_cum_form">
        <div class="card mb-3">
            <div class="d-flex card-header justify-content-between">
                <h4>입력</h4>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-lg-4 inner-td">
                        <div class="form-group">
                            <label for="receipt_date" class="required">접수일자</label>
                            <div class="form-inline">
                                <div class="docs-datepicker form-inline-inner input_box w-100">
                                    <div class="input-group">
                                        <input type="text" class="form-control form-control-sm docs-date" name="receipt_date" value="{{ $sdate }}" id="receipt_date" autocomplete="off">
                                        <div class="input-group-append">
                                            <button type="button" class="btn btn-outline-secondary docs-datepicker-trigger p-0 pl-2 pr-2" disable="">
                                                <i class="fa fa-calendar" aria-hidden="true"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="docs-datepicker-container"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 inner-td">
                        <div class="form-group">
                            <label for="as_type" class="required">수선구분</label>
                            <div class="flex_box">
                                <select id="as_type" name="as_type" class="form-control form-control-sm">
                                    <option value="">선택</option>
                                    <option value="고객수선">고객수선</option>
                                    <option value="매장수선">매장수선</option>
                                    <option value="본사수선">본사수선</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 inner-td">
                        <div class="form-group">
                            <label for="sale_date">판매일자</label>
                            <div class="form-inline">
                                <div class="docs-datepicker form-inline-inner input_box w-100">
                                    <div class="input-group">
                                        <input type="text" class="form-control form-control-sm docs-date" name="sale_date" value="" id="sale_date" autocomplete="off">
                                        <div class="input-group-append">
                                            <button type="button" class="btn btn-outline-secondary docs-datepicker-trigger p-0 pl-2 pr-2" disable="">
                                                <i class="fa fa-calendar" aria-hidden="true"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="docs-datepicker-container"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-4 inner-td">
                        <div class="form-group">
                            <label for="receipt_no">접수번호</label>
                            <div class="flex_box">
                                <input type='text' class="form-control form-control-sm" name='receipt_no' value=''>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 inner-td">
                        <div class="form-group">
                            <!-- 추후 api 작업 예상됨 -->
                            <label for="store_no" class="required">매장번호/매장명</label>
                            <div class="form-inline">
                                <div class="form-inline-inner input_box">
                                    <input type='text' class="form-control form-control-sm ac-style-no search-enter" name='store_no' id="store_no" value="">
                                </div>
                                <span class="text_line">/</span>
                                <div class="form-inline-inner input_box">
                                    <input type="text" class="form-control form-control-sm search-enter" name="store_nm" value="">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 inner-td">
                        <div class="form-group">
                            <label for="item" class="required">품목구분</label>
                            <div class="flex_box">
                                <select name="item" id="item" class="form-control form-control-sm">
                                    <option value="CLOTH TOP">CLOTH TOP</option>
                                    <option value="CLOTH BOTTOM">CLOTH BOTTOM</option>
                                    <option value="BAG">BAG</option>
                                    <option value="SHOES">SHOES</option>
                                    <option value="ACC">ACC</option>
                                    <option value="SAMPLE">SAMPLE</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-4 inner-td">
                        <div class="form-group">
                            <!-- 추후 api 작업 예상됨 -->
                            <label for="as_place">수선처</label>
                            <div class="flex_box">
                                <input type='text' class="form-control form-control-sm" name='as_place' value=''>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 inner-td">
                        <div class="form-group">
                            <!-- 추후 api 작업 예상됨 -->
                            <label for="customer_no" class="required">고객번호/고객명</label>
                            <div class="form-inline">
                                <div class="form-inline-inner input_box">
                                    <input type='text' class="form-control form-control-sm ac-style-no search-enter" name='customer_no' id="customer_no" value="">
                                </div>
                                <span class="text_line">/</span>
                                <div class="form-inline-inner input_box">
                                    <input type="text" class="form-control form-control-sm search-enter" name="customer" value="">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 inner-td">
                        <div class="form-group">
                            <label for="item" class="required">핸드폰</label>
                            <div class="flex_box">
                                <div class="form-inline mr-0 mr-sm-1" style="width:100%;">
                                    <div class="form-inline-inner input_box" style="width:30%;">
                                        <input type="text" name="mobile" class="form-control form-control-sm" maxlength="3" value="" onkeyup="onlynum(this)">
                                    </div>
                                    <span class="text_line">-</span>
                                    <div class="form-inline-inner input_box" style="width:29%;">
                                        <input type="text" name="mobile" class="form-control form-control-sm" maxlength="4" value="" onkeyup="onlynum(this)">
                                    </div>
                                    <span class="text_line">-</span>
                                    <div class="form-inline-inner input_box" style="width:29%;">
                                        <input type="text" name="mobile" class="form-control form-control-sm" maxlength="4" value="" onkeyup="onlynum(this)">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-8 inner-td">
                        <div class="form-group">
                            <label for="addr2" onclick="openFindAddress('zip_cd', 'addr1')">집주소</label>
                            <div class="input_box flex_box address_box">
                                <input type="text" id="zip_cd" name="zip_cd" class="form-control form-control-sm" value="" style="width:calc(25% - 10px);margin-right:10px;" readonly="readonly">
                                <input type="text" id="addr1" name="addr1" class="form-control form-control-sm" value="" style="width:calc(25% - 10px);margin-right:10px;" readonly="readonly">
                                <input type="text" id="addr2" name="addr2" class="form-control form-control-sm" value="" style="width:calc(25% - 10px);margin-right:10px;">
                                <a href="javascript:;" onclick="openFindAddress('zip_cd', 'addr1')" class="btn btn-sm btn-primary shadow-sm fs-12" style="width:80px;">
                                    <i class="fas fa-search fa-sm text-white-50"></i>
                                    검색
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 inner-td">
                        <div class="form-group">
                            <!-- 추후 api 작업 예상됨 -->
                            <label for="goods_nm" class="required">상품</label>
                            <div class="flex_box">
                                <input type="text" class="form-control form-control-sm" name="goods_nm" id="goods_nm" value="" autocomplete="off">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-4 inner-td">
                        <div class="form-group">
                            <label for="color">칼라/사이즈</label>
                            <div class="form-inline">
                                <div class="form-inline-inner input_box">
                                    <input type='text' class="form-control form-control-sm ac-style-no search-enter" name='color' id="color" value="">
                                </div>
                                <span class="text_line">/</span>
                                <div class="form-inline-inner input_box">
                                    <input type="text" class="form-control form-control-sm search-enter" name="size" value="">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 inner-td">
                        <div class="form-group">
                            <label for="storing_nm">입고처</label>
                            <div class="flex_box">
                                <input type='text' class="form-control form-control-sm" name='storing_nm' value=''>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 inner-td">
                        <div class="form-group">
                            <label for="quantity" class="required">수량</label>
                            <div class="flex_box">
                                <input type='text' class="form-control form-control-sm" name='quantity' value='' onkeyup="onlynum(this)">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-4 inner-td">
                        <div class="form-group">
                            <label for="use_y" class="required">수선유료구분</label>
                            <div class="form-inline form-radio-box">
                                <div class="custom-control custom-radio">
                                    <input type="radio" name="is_free" id="use_y" class="custom-control-input" value="Y"/>
                                    <label class="custom-control-label" for="use_y">유료</label>
                                </div>
                                <div class="custom-control custom-radio">
                                    <input type="radio" name="is_free" id="use_n" class="custom-control-input" value="N" checked/>
                                    <label class="custom-control-label" for="use_n">무료</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 inner-td">
                        <div class="form-group">
                            <label for="charged_price">수선금액</label>
                            <div class="form-inline">
                                <div class="form-inline-inner input_box">
                                    <input type='text' class="form-control form-control-sm search-enter" name='charged_price' id="charged_price" value="" placeholder="유료비용" onkeyup="onlynum(this)">
                                </div>
                                <span class="text_line">/</span>
                                <div class="form-inline-inner input_box">
                                    <input type="text" class="form-control form-control-sm search-enter" name="free_price" value="" placeholder="무료비용" onkeyup="onlynum(this)">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12 inner-td">
                        <div class="form-group">
                            <label for="as_content" class="required">수선내용</label>
                            <div class="flex_box">
                                <textarea name="content" id="as_content" class="form-control form-control-sm" style="height: 200px;"></textarea>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="card mb-3">
            <div class="d-flex card-header justify-content-between">
                <h4>본사처리 내용</h4>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-lg-4 inner-td">
                        <div class="form-group">
                            <label for="h_receipt_date">본사접수일</label>
                            <div class="form-inline">
                                <div class="docs-datepicker form-inline-inner input_box w-100">
                                    <div class="input-group">
                                        <input type="text" class="form-control form-control-sm docs-date" name="h_receipt_date" value="" id="h_receipt_date" autocomplete="off">
                                        <div class="input-group-append">
                                            <button type="button" class="btn btn-outline-secondary docs-datepicker-trigger p-0 pl-2 pr-2" disable="">
                                                <i class="fa fa-calendar" aria-hidden="true"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="docs-datepicker-container"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 inner-td">
                        <div class="form-group">
                            <label for="due_date">수선예정일</label>
                            <div class="form-inline">
                                <div class="docs-datepicker form-inline-inner input_box w-100">
                                    <div class="input-group">
                                        <input type="text" class="form-control form-control-sm docs-date" name="due_date" value="" id="due_date" autocomplete="off">
                                        <div class="input-group-append">
                                            <button type="button" class="btn btn-outline-secondary docs-datepicker-trigger p-0 pl-2 pr-2" disable="">
                                                <i class="fa fa-calendar" aria-hidden="true"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="docs-datepicker-container"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 inner-td">
                        <div class="form-group">
                            <label for="start_date">수선인도일</label>
                            <div class="form-inline">
                                <div class="docs-datepicker form-inline-inner input_box w-100">
                                    <div class="input-group">
                                        <input type="text" class="form-control form-control-sm docs-date" name="start_date" value="" id="start_date" autocomplete="off">
                                        <div class="input-group-append">
                                            <button type="button" class="btn btn-outline-secondary docs-datepicker-trigger p-0 pl-2 pr-2" disable="">
                                                <i class="fa fa-calendar" aria-hidden="true"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="docs-datepicker-container"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-4 inner-td">
                        <div class="form-group">
                            <label for="end_date">수선완료일</label>
                            <div class="form-inline">
                                <div class="docs-datepicker form-inline-inner input_box w-100">
                                    <div class="input-group">
                                        <input type="text" class="form-control form-control-sm docs-date" name="end_date" value="" id="end_date" autocomplete="off">
                                        <div class="input-group-append">
                                            <button type="button" class="btn btn-outline-secondary docs-datepicker-trigger p-0 pl-2 pr-2" disable="">
                                                <i class="fa fa-calendar" aria-hidden="true"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="docs-datepicker-container"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12 inner-td">
                        <div class="form-group">
                            <label for="h_explain">본사설명</label>
                            <div class="flex_box">
                                <textarea name="h_explain" id="h_explain" class="form-control form-control-sm" style="height: 200px;"></textarea>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div style="text-align: center;">
            <a href="/store/standard/std11/create" class="btn btn-sm btn-primary shadow-sm pl-2"><i class="bx bx-save mr-1"></i>저장하기</a>
            <a href="/store/standard/std11/" class="btn btn-sm btn-primary shadow-sm pl-2"><i class="bx bx-list-ul mr-1"></i>목록으로 이동</a>
        </div>
    </div>
</form>
<script language="javascript">
var columns = [
        // this row shows the row index, doesn't use any data from the row
        {headerName: '#', width:35, pinned:'left', maxWidth: 100,valueGetter: 'node.id', cellRenderer: 'loadingRenderer', cellStyle: {"background":"#F5F7F7"}},
        {field:"",headerName:"접수일자"},
        {field:"",headerName:"고객번호"},
        {field:"",headerName:"고객명"},
        {field:"",headerName:"수선구분"},
        {field:"",headerName:"판매일자"},
        {field:"",headerName:"본사접수일"},
        {field:"",headerName:"수선인도일"},
        {field:"",headerName:"수선예정일"},
        {field:"",headerName:"수선완료일"},
        {field:"",headerName:"접수번호"},
        {field:"",headerName:"매장번호"},
        {field:"",headerName:"매장명"},
        {field:"",headerName:"수선품목"},
        {field:"",headerName:"제품코드"},
        {field:"",headerName:"제품명"},
        {field:"",headerName:"칼라"},
        {field:"",headerName:"사이즈"},
        {field:"",headerName:"수량"},
        {field:"",headerName:"수선구분"},
        {field:"",headerName:"유료수선금액"},
        {field:"",headerName:"무료수선금액"},
        {field:"",headerName:"연락처1"},
        {field:"",headerName:"연락처2"},
        {field:"",headerName:"연락처3"},
        {field:"",headerName:"우편번호"},
        {field:"",headerName:"주소1"},
        {field:"",headerName:"주소2"},
        {field:"",headerName:"수선내용"},
        {field:"",headerName:"본사설명"},
        {field:"",headerName:"수선처코드"},
        {field:"",headerName:"수선처명"},
];

</script>
<script src="https://t1.daumcdn.net/mapjsapi/bundle/postcode/prod/postcode.v2.js"></script>
<script type="text/javascript" charset="utf-8">
    const pApp = new App('',{
        gridId:"#div-gd",
    });
    let gx;
    $(document).ready(function() {
        pApp.ResizeGrid(265);
        pApp.BindSearchEnter();
        let gridDiv = document.querySelector(pApp.options.gridId);
        gx = new HDGrid(gridDiv, columns);
        Search();
    });
    function Search() {
        let data = $('form[name="search"]').serialize();
        gx.Request('/store/standard/std02/search', data,1);
    }

    function openFindAddress(zipName, addName) {
        new daum.Postcode({
            // 팝업에서 검색결과 항목을 클릭했을때 실행할 코드를 작성하는 부분입니다..
            oncomplete: function(data) {
                $("#" + zipName).val(data.zonecode);
                $("#" + addName).val(data.address);
            }
        }).open();
    }
</script>

@stop

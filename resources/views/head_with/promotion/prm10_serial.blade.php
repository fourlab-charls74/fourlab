@extends('head_with.layouts.layout-nav')
@section('title','쿠폰')
@section('content')
<div class="container-fluid py-3">
    <div class="page_tit d-flex align-items-center justify-content-between">
        <div>
            <h3 class="d-inline-flex">[{{$coupon->coupon_type}}] - {{$coupon->coupon_nm}}</h3>
            <div class="d-inline-flex location">
                <span class="home"></span>
                <span>/ 프로모션</span>
                <span>/ {{$coupon->coupon_nm}}</span>
            </div>
        </div>
        <div>
        <a href="#" onclick="window.close()" class="btn btn-sm btn-primary shadow-sm">닫기</a>
        </div>
    </div>
    <form method="get" name="search">
        <input type="hidden" name="fields">
        <div id="search-area" class="search_cum_form">
            <div class="card mb-3">
                <div class="d-flex card-header justify-content-between">
                    <h4>검색</h4>
                    <div class="flax_box">
                        <button type="button" id="search_sbtn" onclick="return Search();" class="btn btn-sm btn-primary shadow-sm mr-1"><i class="fas fa-search fa-sm text-white-50"></i> 조회</button>
                        <div id="search-btn-collapse" class="btn-group mb-0 mb-sm-0"></div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <!-- 사용여부 -->
                        <div class="col-lg-6 inner-td">
                            <div class="form-group">
                                <label for="">사용여부</label>
                                <div class="flax_box">
                                    <select name="use_yn" id="use_yn" class="form-control form-control-sm">
                                        <option value="">전체</option>
                                        @foreach($use_yn as $val)
                                            <option value="{{$val->code_id}}">{{$val->code_val}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <!-- 이름 -->
                        <div class="col-lg-6 inner-td">
                            <div class="form-group">
                                <label for="">인증번호</label>
                                <div class="flax_box">
                                    <input type="text" name="off_serial" id="off_serial" class="form-control form-control-sm">
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- end row -->
                    <div class="row">
                        <!-- 아이디 -->
                        <div class="col-lg-6 inner-td">
                            <div class="form-group">
                                <label for="">사용자 아이디</label>
                                <div class="flax_box">
                                    <input type="text" name="user_id" id="user_id" class="form-control form-control-sm">
                                </div>
                            </div>
                        </div>
                        <!-- 아이디 -->
                        <div class="col-lg-6 inner-td">
                            <div class="form-group">
                                <label for="">사용자 </label>
                                <div class="flax_box">
                                    <input type="text" name="user_nm" id="user_nm" class="form-control form-control-sm">
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- end row -->
                </div>
            </div>
        </div>
    </form>
    <div class="card shadow mb-3">
        <div class="card-body shadow">
            <div class="card-title">
                <div class="filter_wrap">
                    <div class="fl_box">
                        <h6 class="m-0 font-weight-bold">총 : <span id="gd-total" class="text-primary">0</span> 건</h6>
                    </div>
                    <div class="fr_box form-inline">
                    </div>
                </div>
            </div>
            <div class="table-responsive">
                <div id="div-gd" style="height:calc(100vh - 50vh); width:100%;" class="ag-theme-balham"></div>
            </div>
        </div>
    </div>
</div>
<script>
const coupon_no = '{{$coupon_no}}';
var columns = [
    {field:"serial" , headerName:"인증번호"},
    {field:"use_yn", headerName:"인증번호 사용"},
    {field:"down_date" , headerName:"등록일"},
    {field:"admin_id", headerName:"관리자아이디"},
    {field:"user_id", headerName:"사용자아이디", type:"HeadUserType"},
    {field:"name" , headerName:"사용자"},
    {field:"use_cnt", headerName:"사용횟수"},
    {field:"use_date", headerName:"최종사용일"},
    { width:"auto" }
];

const pApp = new App('', {gridId: "#div-gd"});
const gridDiv = document.querySelector(pApp.options.gridId);
const gx = new HDGrid(gridDiv, columns);

pApp.ResizeGrid();

function Search() {
    let data = $('form[name="search"]').serialize();
    gx.Request(`/head/promotion/prm10/search/serial/${coupon_no}`, data, -1);
}

$('.gift-btn').click(() => {
    openCoupon('', coupon_no);
});

Search();
</script>
@stop

@extends('head_with.layouts.layout-nav')
@section('title','쿠폰')
@section('content')
<div class="container-fluid py-3">
    <div class="page_tit d-flex align-items-center justify-content-between">
        <div>
            <h3 class="d-inline-flex">쿠폰 사용 내역</h3>
            <div class="d-inline-flex location">
                <span class="home"></span>
                <span>/ 프로모션</span>
                <span>/ {{$coupon_nm}}</span>
            </div>
        </div>
        <div>
            <a href="#" class="btn btn-sm btn-primary shadow-sm gift-btn">쿠폰지급</a>
            <a href="#" class="btn btn-sm btn-primary shadow-sm remove-btn">쿠폰회수</a>
        </div>
    </div>
<form method="get" name="search">
    <input type="hidden" name="fields">
    <div id="search-area" class="search_cum_form">
        <div class="card mb-3">
            <div class="d-flex card-header justify-content-between">
                <h4>검색</h4>
                <div class="flax_box">
					<button id="search_sbtn" onclick="Search(); return false;" class="btn btn-search btn-sm btn-primary shadow-sm mr-1"><i class="fas fa-search fa-sm text-white-50"></i> 조회</button>
                    <div id="search-btn-collapse" class="btn-group mb-0 mb-sm-0"></div>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <!-- 아이디 -->
                    <div class="col-lg-6 inner-td">
                        <div class="form-group">
                            <label for="">아이디</label>
                            <div class="flax_box">
                                <input type="text" name="user_id" id="user_id" class="form-control form-control-sm">
                            </div>
                        </div>
                    </div>
                    <!-- 이름 -->
                    <div class="col-lg-6 inner-td">
                        <div class="form-group">
                            <label for="">이름</label>
                            <div class="flax_box">
                                <input type="text" name="name" id="name" class="form-control form-control-sm">
                            </div>
                        </div>
                    </div>
                </div>
                <!-- end row -->
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
                    <!-- 회원명 -->
                    <div class="col-lg-6 inner-td">
                        <div class="form-group">
                            <label for="dlv_kind">스타일넘버/상품코드</label>
                            <div class="form-inline">
                                <div class="form-inline-inner input_box">
                                    <div class="form-group">
                                        <input type="text" class="form-control form-control-sm search-all ac-style-no search-enter" name="style_no" value="">
                                    </div>
                                </div>
                                <span class="text_line">/</span>
                                <div class="form-inline-inner input_box">
                                    <div class="form-group">
                                        <input type="text" class="form-control form-control-sm search-all search-enter" name="goods_no" value="">
                                    </div>
                                </div>
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
                    <h6 class="m-0 font-weight-bold">총 <span id="gd-total" class="text-primary">0</span> 건</h6>
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

<script>
const coupon_no = '{{$coupon_no}}';
var columns = [
    {
        headerName: '',
        headerCheckboxSelection: true,
        checkboxSelection: true,
        width:28,
        pinned:'left'
    },
    {field:"user_id" , headerName:"아이디", type:"HeadUserType"},
    {field:"name", headerName:"이름"},
    {field:"down_date" , headerName:"다운로드 일시"},
    {field:"use_date", headerName:"사용일시", width: 120},
    {field:"ord_no", headerName:"주문번호", type:"HeadOrderNoType"},
    {field:"serial" , headerName:"쿠폰일련번호"},
    { width:"auto" }
];

const pApp = new App('', {gridId: "#div-gd"});
const gridDiv = document.querySelector(pApp.options.gridId);
const gx = new HDGrid(gridDiv, columns);

pApp.ResizeGrid();

function Search() {
    let data = $('form[name="search"]').serialize();
    gx.Request(`/head/promotion/prm10/search/used/${coupon_no}`, data, 1);
}

$('.gift-btn').click(() => {
    openCoupon('', coupon_no);
});

$('.remove-btn').click(function(){
    const rows = gx.getSelectedRows();

    if (rows.length === 0) {
        alert("쿠폰을 회수할 회원을 선택해주세요.");
        return;
    }

    if(confirm('지급 또는 다운로드 한 쿠폰를 회수하시겠습니까? 단 사용전 쿠폰만 회수가 가능합니다.') == false) return;

    const idxs = rows.map((row)=> row.idx);

    $.ajax({    
        type: "delete",
        url: `/head/promotion/prm10/used/${coupon_no}`,
        data: { idxs  },
        success: function(data) {
            alert("삭제되었습니다.");
            Search();
            opener.Search();
        },
        error : function(res, a, b) {
            alert(res.responseJSON.message);
        }
    });
});
Search();
</script>
@stop

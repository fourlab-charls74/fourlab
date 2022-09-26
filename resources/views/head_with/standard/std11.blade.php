@extends('head_with.layouts.layout')
@section('title','광고할인')
@section('content')
<div class="page_tit">
    <h3 class="d-inline-flex">광고할인관리</h3>
    <div class="d-inline-flex location">
        <span class="home"></span>
        <span>/ 기준정보</span>
        <span>/ 광고할인관리</span>
    </div>
</div>
<div id="search-area" class="search_cum_form">
    <form method="get" name="search">
        <div class="card mb-3">
            <div class="d-flex card-header justify-content-between">
                <h4>검색</h4>
                <div>
                    <a href="#" id="search_sbtn" onclick="return Search();" class="btn btn-sm btn-primary shadow-sm pl-2"><i class="fas fa-search fa-sm text-white-50"></i> 조회</a>
                    <a href="#" onclick="openAddDCPopup()" class="btn btn-sm btn-outline-primary shadow-sm pl-2"><i class="bx bx-plus fs-16"></i> 추가</a>
                    <div id="search-btn-collapse" class="btn-group mb-0 mb-sm-0"></div>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-lg-4 inner-td">
                        <div class="form-group">
                            <label for="sale_name">할인명</label>
                            <div class="flax_box">
                                <input type='text' class="form-control form-control-sm search-enter" name='name' id="sale_name" value='' />
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 inner-td">
                        <div class="form-group">
                            <label for="">사용</label>
                            <div class="flax_box">
                                <select name='use_yn' class="form-control form-control-sm">
                                    <option value="">전체</option>
                                    <option value="Y">Y</option>
                                    <option value="N">N</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 inner-td">
                        <div class="form-group">
                            <label for="">범위</label>
                            <div class="flax_box">
                                <select name="dc_range" id="dc_range" class="form-control form-control-sm">
                                    <option value="A">전체</option>
                                    <option value="G">상품</option>
                                    <!-- <option value="">상품</option> -->
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-lg-4 inner-td">
                        <div class="form-group">
                            <label for="name">쿠폰제한</label>
                            <div class="flax_box">
                                <select name="limit_coupon_yn" id="limit_coupon_yn" class="form-control form-control-sm">
                                    <option value="">전체</option>
                                    <option value="Y">Y</option>
                                    <option value="N">N</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 inner-td">
                        <div class="form-group">
                            <label for="">적립금제한</label>
                            <div class="flax_box">
                                <select name="limit_point_yn" id="limit_point_yn" class="form-control form-control-sm">
                                    <option value="">전체</option>
                                    <option value="Y">Y</option>
                                    <option value="N">N</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 inner-td">
                        <div class="form-group">
                            <label for="state">적립금지급</label>
                            <div class="flax_box">
                                <select name="add_point_yn" id="add_point_yn" class="form-control form-control-sm">
                                    <option value="">전체</option>
                                    <option value="Y">Y</option>
                                    <option value="N">N</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="resul_btn_wrap mb-3">
            <a href="#" id="search_sbtn" onclick="return Search();" class="btn btn-sm btn-primary shadow-sm pl-2"><i class="fas fa-search fa-sm text-white-50"></i> 조회</a>
            <a href="#" onclick="openAddDCPopup()" class="btn btn-sm btn-outline-primary shadow-sm pl-2"><i class="bx bx-plus fs-16"></i> 추가</a>
            <div class="search_mode_wrap btn-group mr-2 mb-0 mb-sm-0"></div>
        </div>
    </form>
</div>

<div id="filter-area" class="card shadow-none mb-0 search_cum_form ty2 last-card">
    <div class="card-body shadow">
        <div class="card-title mb-3">
            <div class="filter_wrap">
                <div class="fl_box">
                    <h6 class="m-0 font-weight-bold">총 <span id="gd-total" class="text-primary">0</span> 건</h6>
                </div>
                <div class="fr_box">
                   
                </div>
            </div>
        </div>
        <div class="table-responsive">
            <div id="div-gd" style="height:calc(100vh - 370px);width:100%;" class="ag-theme-balham"></div>
        </div>
    </div>
</div>

<script>
    const columnDefs = [
        {headerName: '#', pinned: 'left', type: 'NumType'},
        {
            field: "name", 
            headerName: "할인명",
            cellRenderer: function(params) {
                return `<a href="#" onClick="openDetailPopup('${params.data.no}')">${params.value}</a>`;
            }
        },
        {field: "use_yn", headerName: "사용"},
        {field: "dc_range", headerName: "범위"},
        {field: "dc_rate", headerName: "할인율(%)", type:'currencyType' },
        {field: "dc_amt", headerName: "할인금액(원)", type:'currencyType' },
        {field: "date_from", headerName: "할인기간 시작", width:100},
        {field: "date_to", headerName: "할인기간 종료", width:100},
        {field: "limit_margin_rate", headerName: "마진율제한(%)", type:'currencyType'},
        {field: "limit_coupon_yn", headerName: "쿠폰제한"},
        {field: "limit_point_yn", headerName: "적립금제한"},
        {field: "add_point_yn", headerName: "적립금지급"},
        {field: "add_point_rate", headerName: "추가적립율(%)", type:'currencyType'},
        {field: "add_point_amt", headerName: "추가적립금액(원)", type:'currencyType'},
        {field: "admin_nm", headerName: "관리자명"},
        {field: "rt", headerName: "등록일시", width: 130},
        {field: "ut", headerName: "수정일시", width: 130},
        {field: "", headerName: "", width: "auto"}
    ];

    const pApp = new App('', { gridId: "#div-gd" });
    const gridDiv = document.querySelector(pApp.options.gridId);
    const gx = new HDGrid(gridDiv, columnDefs);

    gx.gridOptions.getRowNodeId = function(data) {
        return data.id;
    }

    pApp.ResizeGrid(275);

    function Search(){
            let data = $('form[name="search"]').serialize();
        gx.Request('/head/standard/std11/search', data);
    }

    function openDetailPopup(no) {
        const url='/head/standard/std11/show/dc/' + no;
        const product=window.open(url,"_blank","toolbar=no,scrollbars=yes,resizable=yes,status=yes,top=100,left=100,width=1200,height=1000");
    }
    
    function openAddDCPopup() {
        const url='/head/standard/std11/show/dc/';
        const product=window.open(url,"_blank","toolbar=no,scrollbars=yes,resizable=yes,status=yes,top=500,left=500,width=1024,height=600");
    }
    Search();

    $(function() {
        $("[name=name]").on("keypress", function(e) {
            if(e.which == 13) {
                e.preventDefault();
                Search();
            }
        });
    });
</script>
@stop

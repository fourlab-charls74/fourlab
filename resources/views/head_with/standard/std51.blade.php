@extends('head_with.layouts.layout')
@section('title','코드관리')
@section('content')

<!-- 상단 타이틀 -->
<div class="page_tit">
    <h3 class="d-inline-flex">코드관리</h3>
    <div class="d-inline-flex location">
        <span class="home"></span>
        <span>/ 기준정보</span>
        <span>/ 코드관리</span>
    </div>
</div>

<!-- 검색 영역 -->
<form method="get" name="search">
    <div id="search-area" class="search_cum_form">
        <div class="card mb-3">
            <div class="d-flex card-header justify-content-between">
                <h4>검색</h4>
                <div>
                    <a href="#" id="search_sbtn" onclick="return Search();" class="btn btn-sm btn-primary shadow-sm pl-2"><i class="fas fa-search fa-sm text-white-50"></i> 조회</a>
                    <a href="#" class="btn btn-sm btn-outline-primary shadow-sm pl-2 add-btn"><i class="bx bx-plus fs-16"></i> 추가</a>
		            <a href="#" onclick="document.search.reset()" class="btn btn-sm btn-outline-primary shadow-sm">검색조건 초기화</a>
                    <div id="search-btn-collapse" class="btn-group mb-0 mb-sm-0"></div>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-lg-4 inner-td">
                        <div class="form-group">
                            <label for="code_kind_cd">코드 종류</label>
                            <div class="flax_box">
                                <input type="text" name="code_kind_cd" id="code_kind_cd" class="form-control form-control-sm search-enter">
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 inner-td">
                        <div class="form-group">
                            <label for="code_id">코드ID</label>
                            <div class="flax_box">
                                <input type="text" name="code_id" id="code_id" class="form-control form-control-sm search-enter">
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 inner-td">
                        <div class="form-group">
                            <label for="code_val">코드값</label>
                            <div class="flax_box">
                                <input type="text" name="code_val" id="code_val" class="form-control form-control-sm search-enter">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-4 inner-td">
                        <div class="form-group">
                            <label for="dlv_kind">사용여부</label>
                            <div class="form-inline form-radio-box">
                                <div class="custom-control custom-radio">
                                    <input type="radio" name="use_yn" id="sch_use_yn_" class="custom-control-input" checked="" value="">
                                    <label class="custom-control-label" for="sch_use_yn_">전체</label>
                                </div>
                                <div class="custom-control custom-radio">
                                    <input type="radio" name="use_yn" id="sch_use_yn_y" class="custom-control-input" value="Y">
                                    <label class="custom-control-label" for="sch_use_yn_y">Y</label>
                                </div>
                                <div class="custom-control custom-radio">
                                    <input type="radio" name="use_yn" id="sch_use_yn_n" class="custom-control-input" value="N">
                                    <label class="custom-control-label" for="sch_use_yn_n">N</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 inner-td">
                        <div class="form-group">
                            <label for="item">자료수/정렬</label>
                            <div class="form-inline">
                                <div class="form-inline-inner input_box" style="width:24%;">
                                    <select name="limit" class="form-control form-control-sm">
                                        <option value="100" >100</option>
                                        <option value="500" >500</option>
                                        <option value="1000" >1000</option>
                                        <option value="2000" >2000</option>
                                    </select>
                                </div>
                                <span class="text_line">/</span>
                                <div class="form-inline-inner input_box" style="width:45%;">
                                    <select name="ord_field" class="form-control form-control-sm">
                                        <option value="no" selected>-- 선택 --</option>
                                        <option value="code_id">코드ID</option>
                                        <option value="rt" >생성일</option>
                                    </select>
                                </div>
                                <div class="form-inline-inner input_box sort_toggle_btn" style="width:24%;margin-left:1%;">
                                    <div class="btn-group" role="group">
                                        <label class="btn btn-primary primary" for="sort_desc" data-toggle="tooltip" data-placement="top" title="" data-original-title="내림차순"><i class="bx bx-sort-down"></i></label>
                                        <label class="btn btn-secondary" for="sort_asc" data-toggle="tooltip" data-placement="top" title="" data-original-title="오름차순"><i class="bx bx-sort-up"></i></label>
                                    </div>
                                    <input type="radio" name="ord" id="sort_desc" value="desc" checked="">
                                    <input type="radio" name="ord" id="sort_asc" value="asc">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="resul_btn_wrap mb-3">
            <a href="#" id="search_sbtn" onclick="return Search();" class="btn btn-sm btn-primary shadow-sm pl-2"><i class="fas fa-search fa-sm text-white-50"></i> 조회</a>
            <a href="#" onclick="Cmder('add')" class="btn btn-sm btn-outline-primary shadow-sm pl-2 add-btn"><i class="bx bx-plus fs-16"></i> 추가</a>
            <a href="#" onclick="document.search.reset()" class="btn btn-sm btn-outline-primary shadow-sm">검색조건 초기화</a>
            <div class="search_mode_wrap btn-group mr-2 mb-0 mb-sm-0"></div>
        </div>
    </div>
</form>

<!-- 코드 데이터 상세정보 -->
<div id="filter-area" class="card shadow-none search_cum_form ty2 last-card">
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
            <div id="div-gd" class="ag-theme-balham"></div>
        </div>
    </div>
</div>

<!-- Javascript -->
<script language="javascript">
    var columns = [
        { headerName: '#', width: 50, maxWidth: 100, valueGetter: 'node.id', cellRenderer: 'loadingRenderer' },
        { 
            field: "no", 
            headerName: "No",
            width: 70,
            cellRenderer: function (params) {
                if (params.value) {
                    return `<a href="#" onclick="openCodeDetailPopup('${params.value}')">${params.value}</a>`
                }
            }
        },
        { field: "code_kind_cd", headerName: "코드 종류", width: 120 },
        { field: "code_id", headerName: "코드ID", width: 120 },
        { field: "code_val", headerName: "코드값1", width: 200 },
        { field: "code_val2", headerName: "코드값2", width: 200 },
        { field: "code_val3", headerName: "코드값3", width: 200 },
        { field: "code_val_eng", headerName: "코드값(영문)", width: 200 },
        { field: "use_yn", headerName: "사용여부", width: 80 },
        { field: "admin_id", headerName: "어드민ID", width: 100 },
        { field: "admin_nm", headerName: "어드민네임", width: 100 },
        { field: "rt", headerName: "생성일", width: 130 },
        { field: "ut", headerName: "업데이트일", width: 130 },
    ];

    function openCodeDetailPopup(no) {
        var url = `/head/standard/std51/` + no;
        window.open(url, "_blank", "toolbar=no,scrollbars=yes,resizable=yes,status=yes,top=500,left=500,width=1024,height=530");
    }
</script>

<script type="text/javascript" charset="utf-8">

    var pApp = new App('', {
        gridId: "#div-gd",
    });
    var gx;
    $(document).ready(function() {
        pApp.ResizeGrid(275); //280
        pApp.BindSearchEnter();
        var gridDiv = document.querySelector(pApp.options.gridId);
        gx = new HDGrid(gridDiv, columns);
        Search();
    });

</script>

<script type="text/javascript" charset="utf-8">

    function Search() {
        var data = $('form[name="search"]').serialize();
        gx.Request('/head/standard/std51/search', data, 1);
    }

    // 등록
    $('.add-btn').click(function(e){
        var url = '/head/standard/std51/create';
        window.open(url, "_blank", "toolbar=no,scrollbars=yes,resizable=yes,status=yes,top=500,left=500,width=800,height=530");
    });

</script>
@stop
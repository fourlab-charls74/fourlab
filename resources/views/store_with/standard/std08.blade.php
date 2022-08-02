@extends('store_with.layouts.layout')
@section('title','매장등급관리')

@section('content')
<div class="page_tit">
    <h3 class="d-inline-flex">매장등급관리</h3>
    <div class="d-inline-flex location">
        <span class="home"></span>
        <span>/ 코드관리</span>
    </div>
</div>

<style>
    @media (max-width: 740px) {
        #div-gd {height: 130px !important;}
    }
</style>

<form method="get" name="search">
    <div id="search-area" class="search_cum_form">
        <div class="card mb-3">
            <div class="d-flex card-header justify-content-between">
                <h4>검색</h4>
                <div>
                    <a href="#" id="search_sbtn" onclick="return Search();" class="btn btn-sm btn-primary shadow-sm pl-2"><i class="fas fa-search fa-sm text-white-50"></i> 조회</a>
		            <a href="#" onclick="formReset('search')" class="btn btn-sm btn-outline-primary shadow-sm">검색조건 초기화</a>
                    <div id="search-btn-collapse" class="btn-group mb-0 mb-sm-0"></div>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-lg-6">
                        <div class="form-group">
                            <label for="good_types">기간</label>
                            <div class="form-inline date-select-inbox">
                                <div class="docs-datepicker form-inline-inner input_box">
                                    <div class="input-group">
                                        <input type="text" class="form-control form-control-sm docs-date" name="sdate" value="{{ $sdate }}" autocomplete="off" disable>
                                        <div class="input-group-append">
                                            <button type="button" class="btn btn-outline-secondary docs-datepicker-trigger p-0 pl-2 pr-2" disable>
                                                <i class="fa fa-calendar" aria-hidden="true"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="docs-datepicker-container"></div>
                                </div>
                                <span class="text_line">~</span>
                                <div class="docs-datepicker form-inline-inner input_box">
                                    <div class="input-group">
                                        <input type="text" class="form-control form-control-sm docs-date" name="edate" value="{{ $edate }}" autocomplete="off">
                                        <div class="input-group-append">
                                            <button type="button" class="btn btn-outline-secondary docs-datepicker-trigger p-0 pl-2 pr-2">
                                                <i class="fa fa-calendar" aria-hidden="true"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="docs-datepicker-container"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6 inner-td">
                        <div class="form-group">
							<label for="store_nm">사용여부</label>
                            <div class="form-inline">
                            </div>
						</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="resul_btn_wrap mb-3">
            <a href="#" id="search_sbtn" onclick="return Search();" class="btn btn-sm btn-primary shadow-sm pl-2"><i class="fas fa-search fa-sm text-white-50"></i> 조회</a>
            <a href="#" onclick="formReset('search')" class="btn btn-sm btn-outline-primary shadow-sm">검색조건 초기화</a>
        </div>
    </div>
</form>

<div class="row show_layout">
    <div class="col-lg-12">
        <div class="card shadow-none mb-0">
            <div class="card-header mb-0 d-flex justify-content-between align-items-left align-items-sm-center flex-column flex-sm-row">
                <h5 class="m-0 mb-3 mb-sm-2"><span id="select_store_nm"></span>총 N 개수</h5>
                <div class="d-flex align-items-center justify-content-center justify-content-sm-end">
                    {{-- <button type="button" class="btn btn-sm btn-primary shadow-sm pl-2 mr-1" onclick="updateStoreFee()"><i class="fas fa-save fa-sm text-white-50 mr-1"></i> 저장</button>
                    <button type="button" class="btn btn-sm btn-outline-primary shadow-sm" onclick="resetStoreFee()">전체 초기화</button> --}}
                </div>
            </div>
            <div class="card-body shadow pt-2">
                <div class="table-responsive">
                    <div id="div-gd-store-fee" class="ag-theme-balham"></div>
                </div>
            </div>
        </div>
    </div>
</div>
<script language="javascript">

    let columns = [
        {field: "pr_code_cd", headerName: "등급", width: 60},
        {field: "pr_code_cd", headerName: "정상",
            children: [
                { headerName: "금액", field: "wonga", type: 'numberType',width:100 },
                { headerName: "수수료율", field: "wonga", type: 'numberType',width:100 },
            ]
        },
        {field: "pr_code_cd", headerName: "정상2",
            children: [
                { headerName: "금액", field: "wonga", type: 'numberType',width:100 },
                { headerName: "수수료율", field: "wonga", type: 'numberType',width:100 },
            ]
        },
        {field: "pr_code_cd", headerName: "정상3",
            children: [
                { headerName: "금액", field: "wonga", type: 'numberType',width:100 },
                { headerName: "수수료율", field: "wonga", type: 'numberType',width:100 },
            ]
        },
        {field: "pr_code_cd", headerName: "특판", width: 60},
        {field: "pr_code_cd", headerName: "용품", width: 60},
        {field: "pr_code_cd", headerName: "특약온라인", width: 60},
        {field: "pr_code_cd", headerName: "비고", width: 60},
        {width: "auto"},
    ];
</script>
<script type="text/javascript" charset="utf-8">
    let gx, gx2;

    const pApp = new App('', { gridId: "#div-gd-store-fee" });
    let cur_store_cd = "";
    let cur_store_nm = "";

    $(document).ready(function() {
        // 매장목록
        pApp.ResizeGrid(275);
        pApp.BindSearchEnter();
        let gridDiv = document.querySelector(pApp.options.gridId);
        gx = new HDGrid(gridDiv, columns);

        // 최초검색
        //Search();

        // 검색조건 숨김 시 우측 grid 높이 설정
        $(".search_mode_wrap .dropdown-menu a").on("click", function(e) {
            if(pApp2.options.grid_resize == true){
                pApp2.ResizeGrid(275);
            }
        });
    });

    // 매장목록 조회
    function Search() {
        const data = $('[name=search]').serialize();
        gx.Request("/store/standard/std07/search", data, -1, function(d) {
            if(cur_store_cd === "" && d.body.length > 0) {
                SearchDetail(d.body[0].store_cd, d.body[0].store_nm);
            }
        });
    }

    // 검색조건 초기화
    function formReset(id) {
        document[id].reset();
    }
</script>
@stop

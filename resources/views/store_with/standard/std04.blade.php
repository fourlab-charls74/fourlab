@extends('store_with.layouts.layout')
@section('title','동종업계관리')
@section('content')
<div class="page_tit">
    <h3 class="d-inline-flex">동종업계관리</h3>
    <div class="d-inline-flex location">
        <span class="home"></span>
        <span>/ 코드관리</span>
    </div>
</div>
<form method="get" name="search">
    <div id="search-area" class="search_cum_form">
        <div class="card mb-3">
            <div class="d-flex card-header justify-content-between">
                <h4>검색</h4>
                <div>
                    <a href="#" id="search_sbtn" onclick="return Search();" class="btn btn-sm btn-primary shadow-sm pl-2"><i class="fas fa-search fa-sm text-white-50"></i> 조회</a>
                    <a href="#" onclick="Cmder('add')" class="btn btn-sm btn-outline-primary shadow-sm pl-2"><i class="bx bx-plus fs-16"></i> 추가</a>
		            <a href="#" onclick="formReset()" class="btn btn-sm btn-outline-primary shadow-sm">검색조건 초기화</a>
                    <div id="search-btn-collapse" class="btn-group mb-0 mb-sm-0"></div>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-lg-4 inner-td">
                        <div class="form-group">
							<label for="name">이름</label>
                            <div class="form-inline inline_select_box">
                                <div class="form-inline-inner input-box w-100">
                                    <div class="form-inline inline_btn_box">
                                        <input type="hidden" id="com_id" name="com_id">
                                        <input type="text" id="com_nm" name="com_nm" class="form-control form-control-sm ac-company" style="width:100%;">
                                        <a href="#" class="btn btn-sm btn-outline-primary sch-company"><i class="bx bx-dots-horizontal-rounded fs-16"></i></a>
                                    </div>
                                </div>
                            </div>
						</div>
                    </div>
                    <div class="col-lg-4 inner-td">
                        <div class="form-group">
                            <label for="dlv_kind">구분</label>
                            <div class="flax_box">
                                <select id="com_type" name="com_type" class="form-control form-control-sm">
                                    <option value="">전체</option>
                                    @foreach ($com_types as $com_type)
                                        <option value="{{ $com_type->code_id }}">{{ $com_type->code_val }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 inner-td">
                        <div class="form-group">
                            <label for="formrow-firstname-input">사용여부</label>
                            <div class="flax_box">
                                <select class="form-control form-control-sm" name="use_yn">
                                    <option value="">전체</option>
                                    <option selected value="Y">사용</option>
                                    <option value="N">미사용</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="resul_btn_wrap mb-3">
            <a href="#" id="search_sbtn" onclick="return Search();" class="btn btn-sm btn-primary shadow-sm pl-2"><i class="fas fa-search fa-sm text-white-50"></i> 조회</a>
            <a href="#" onclick="Cmder('add')" class="btn btn-sm btn-outline-primary shadow-sm pl-2"><i class="bx bx-plus fs-16"></i> 추가</a>
            <div class="search_mode_wrap btn-group mr-2 mb-0 mb-sm-0"></div>
        </div>
    </div>
</form>

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
<script language="javascript">
var columns = [
        // this row shows the row index, doesn't use any data from the row
        {headerName: '#', width:35, pinned:'left', maxWidth: 100,valueGetter: 'node.id', cellRenderer: 'loadingRenderer', cellStyle: {"background":"#F5F7F7"}},
        {field:"com_type",headerName:"구분", pinned:'left', cellStyle:StyleGoodsTypeNM,editable: true},
        {field:"com_id",headerName:"코드", pinned:'left',
            cellRenderer: function(params) {
                if (params.value !== undefined && params.data.no != "") {
                    return '<a href="#" onclick="ComDetail(\''+ params.value +'\');" >'+ params.value+'</a>';
                }
            }
        },
        {field:"com_nm",headerName:"이름", pinned:'left'},
        {field:"",headerName:"대표번호"},
        {field:"",headerName:"핸드폰번호"},
        {field:"",headerName:"FAX번호"},
        {field:"",headerName:"주소",width:300},
        {field:"",headerName:"사용여부"},
        {field:"", headerName:"", width: "auto"},
];

</script>
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

</script>

@stop

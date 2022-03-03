@extends('head_skote.layouts.app')
@section('title','상품 Q&A')
@section('content')

<div class="d-flex align-items-center justify-content-between mb-2">
    <h1 class="h3 mb-0 text-gray-800">상품 Q&A</h1>
    <div>
        <a href="#" id="search_sbtn" onclick="Search();" class="btn btn-sm btn-primary shadow-sm"><i class="fas fa-search fa-sm text-white-50"></i> 검색</a>
        <div id="search-btn-collapse" class="btn-group mr-2 mb-0 mb-sm-0"></div>
    </div>
</div>
<form method="get" name="search">
    <div id="search-area" class="search_cum_form">
        <div class="card mb-1">
            <div class="card-body">
                <div class="row">
                    <div class="col-lg-4 inner-td">
                        <div class="form-group">
                            <label for="formrow-firstname-input">작성일 :</label>
                            <div class="form-inline inline_input_box">
                                <div class="docs-datepicker form-inline-inner">
                                    <div class="input-group">
                                        <input type="text" class="form-control form-control-sm docs-date" name="sdate" value="{{ $sdate }}" autocomplete="off">
                                        <div class="input-group-append">
                                            <button type="button" class="btn btn-outline-secondary docs-datepicker-trigger p-0 pl-2 pr-2">
                                            <i class="fa fa-calendar" aria-hidden="true"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="docs-datepicker-container"></div>
                                </div>
                                <span class="text_line">~</span>
                                <div class="docs-datepicker form-inline-inner">
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
                    <div class="col-lg-4 inner-td">
                        <div class="form-group">
                            <label for="style_no">스타일넘버 :</label>
                            <div class="flax_box">
                                <input type='text' class="form-control form-control-sm search-all" name='style_no' value=''>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 inner-td">
                        <div class="form-group">
                            <label for="formrow-email-input">출력여부 :</label>
                            <div class="flax_box">
                                <select name="show_yn" id="show_yn" class="form-control form-control-sm">
                                <option value="">전체</option>
                                <option value="Y">Y</option>
                                <option value="N">N</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-4 inner-td">
                        <div class="form-group">
                            <label for="style_no">상품코드 :</label>
                            <div class="form-inline inline_input_box">
                                <div class="form-inline-inner text-box" style="width:51%;margin-right:2%;">
                                    <div class="form-group">
                                        <input type="text" class="form-control form-control-sm search-all" name="user_nm" value="">
                                    </div>
                                </div>
                                <div class="form-inline-inner text-box">
                                    <div class="form-group">
                                        <input type="text" class="form-control form-control-sm search-all" name="r_nm" value="">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 inner-td">
                        <div class="form-group">
                            <label for="goods_nm">상품명 :</label>
                            <div class="flax_box">
                                <input type='text' class="form-control form-control-sm search-all" name='goods_nm' value=''>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 inner-td">
                        <div class="form-group">
                            <label for="formrow-inputZip">검색 :</label>
                            <div class="form-inline inline_select_box">
                                <div class="form-inline-inner select-box">
                                  <select name="column" id="column" class="form-control form-control-sm">
                                    <option value="a.user_nm">작성자 이름</option>
                                    <option value="a.user_id">작성자 ID</option>
                                    <option value="a.admin_nm">답변자 이름</option>
                                    <option value="a.admin_id">답변자 ID</option>
                                  </select>
                                </div>
                                <div class="form-inline-inner input-box">
                                    <input type='text' name='keyword' class="form-control form-control-sm search-all"  value=''>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="search-area-ext d-none row">
                    <div class="col-lg-4 inner-td">
                        <div class="form-group">
                            <label for="formrow-inputCity">진행상태 :</label>
                            <div class="flax_box">
                                <select name="answer_yn" id="answer_yn" class="form-control form-control-sm">
                                <option value="">전체</option>
                                <option value="N" selected>답변대기</option>
                                <option value="Y">답변완료</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 inner-td">
                        <div class="form-group">
                            <label for="formrow-inputCity">품목 :</label>
                            <div class="flax_box">
                                <select name='item' class="form-control form-control-sm">
                                    <option value=''>전체</option>
                                    @foreach ($items as $item)
                                        <option value='{{ $item->cd }}'>{{ $item->val }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="resul_btn_wrap d-sm-none">
                    <a href="javascript:;" class="btn btn-sm w-xs btn-primary shadow-sm apply-btn" onclick="return Search();"><i class="fas fa-search fa-sm text-white-50"></i> 검색</a>
                </div>
            </div>
        </div>
    </div>
</form>
<!-- DataTales Example -->
<div class="card shadow mb-4 last-card pt-2 pt-sm-0">
  <div class="card-body">
    <div class="card-title form-inline text-right">
        <h6 class="m-0 font-weight-bold">총 : <span id="gd-total" class="text-primary">0</span> 건</h6>
    </div>
    <div class="table-responsive">
        <div id="div-gd" style="width:100%;" class="ag-theme-balham"></div>
    </div>
  </div>
</div>
<script language="javascript">
    var columns = [
        {headerName: "#", field: "num",sortable:"ture",filter:true,width:50,valueGetter: function(params) {return params.node.rowIndex+1;}},
        {headerName: "출력", field: "show_yn",filter:"ture",filter:true},
        {headerName: "상태", field: "answer_yn", filter:"ture", filter:true, cellRenderer: function(params) {return params.value === 'Y' ? '답변완료' : '답변대기';}},
        {headerName: "제목", field: "subject", sortable:"ture", filter:true, width:200, cellRenderer: function(params) {return '<a href="/head/cs/cs02/show/' + params.data.no +'" rel="noopener">'+ params.value+'</a>';}},
        {headerName:"이미지", field:"img", type:'GoodsImageType'},
        {headerName: "상품명", field: "goods_nm", type: "GoodsNameType",},
        {headerName: "작성자", field: "user_id", sortable:"ture", cellRenderer: function(params) {if (params.value !== undefined) {return `${params.data.user_nm}(${params.value})`;}}},
        {headerName: "답변자", field: "admin_id", sortable:"ture", cellRenderer: function(params) {if (params.value !== undefined) {return `${params.data.admin_nm}(${params.value})`;}}},
        {headerName: "질문일시", field: "q_date",sortable:"ture",filter:true },
        {headerName: "답변일시", field: "a_date",sortable:"ture",filter:true},
        {headerName: "", field: "nvl"}
    ];
</script>
<script type="text/javascript" charset="utf-8">
    const pApp = new App('',{
        gridId:"#div-gd",
    });
    let gx;
    $(document).ready(function() {
        pApp.ResizeGrid();
        let gridDiv = document.querySelector(pApp.options.gridId);
        gx = new HDGrid(gridDiv, columns);
        Search();
    });
    function Search() {
        let data = $('form[name="search"]').serialize();
        gx.Request('/head/cs/cs02/search', data,1);
    }
</script>


@stop

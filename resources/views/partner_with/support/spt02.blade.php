@extends('partner_with.layouts.layout')
@section('title','Q&A')
@section('content')
<div class="page_tit">
    <h3 class="d-inline-flex">Q&A</h3>
    <div class="d-inline-flex location">
        <span class="home"></span>
        <span>/ Q&A</span>
    </div>
</div>
<form method="get" name="search" id="search">
    <div id="search-area" class="search_cum_form">
        <div class="card mb-3">
            <div class="d-flex card-header justify-content-between">
                <h4>검색</h4>
                <div>
                    <a href="#" id="search_sbtn" onclick="return Search();" class="btn btn-sm btn-primary shadow-sm pl-2"><i class="fas fa-search fa-sm text-white-50"></i> 조회</a>
                    <a href="/partner/support/spt02/create" class="btn btn-sm btn-primary shadow-sm pl-2"><i class="fas fa-search fa-sm text-white-50"></i> 문의</a>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-lg-6 inner-td">
                        <div class="form-group">
                            <label for="formrow-firstname-input">문의일</label>
                            <div class="form-inline">
                                <div class="docs-datepicker form-inline-inner input_box">
                                    <div class="input-group">
                                        <input type="text" class="form-control form-control-sm docs-date" name="sdate" value="{{ $sdate }}" autocomplete="off">
                                        <div class="input-group-append">
                                            <button type="button" class="btn btn-outline-secondary docs-datepicker-trigger p-0 pl-2 pr-2" disable="">
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
                            <label for="formrow-email-input">상태</label>
                            <div class="flax_box">
                                <select name='state' class="form-control form-control-sm">
                                    <option value=''>전체</option>
                                    @foreach ($qna_states as $qna_state)
                                        <option value='{{ $qna_state->code_id }}'>
                                            {{ $qna_state->code_val }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-6 inner-td">
                        <div class="form-group">
                            <label for="formrow-email-input">분류</label>
                            <div class="flax_box">
                                <select name='qna_type' class="form-control form-control-sm">
                                    <option value=''>전체</option>
                                    @foreach ($qna_types as $qna_type)
                                        <option value='{{ $qna_type->code_id }}'>
                                            {{ $qna_type->code_val }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6 inner-td">
                        <div class="form-group">
                            <label for="formrow-email-input">제목</label>
                            <div class="flax_box">
                                <input type='text' class="form-control form-control-sm search-all search-enter" name='subject' value=''>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
          <div class="resul_btn_wrap mb-3">
              <a href="javascript:;" class="btn btn-sm w-xs btn-primary shadow-sm apply-btn" onclick="return Search();"><i class="fas fa-search fa-sm text-white-50"></i> 조회</a>
              <div class="search_mode_wrap btn-group mr-2 mb-0 mb-sm-0"></div>
          </div>
    </div>
</form>
<div id="filter-area" class="card shadow-none mb-4 ty2 last-card">
    <div class="card-body shadow">
        <div class="card-title">
            <div class="filter_wrap">
                <div class="fl_box">
                    <h6 class="m-0 font-weight-bold">총 : <span id="gd-total" class="text-primary">0</span> 건</h6>
                </div>
                <div class="fr_box flax_box">
                </div>
            </div>
        </div>
        <div class="table-responsive">
            <div id="div-gd" style="width:100%;" class="ag-theme-balham"></div>
        </div>
     </div>
</div>
<script language="javascript">
    var columns = [
        {headerName: "#", field: "num",type:'NumType'},
        {headerName: "분류", field: "type"},
        {headerName: "제목", field: "subject",width:500,
            cellRenderer: function(params) {
                return '<a href="/partner/support/spt02/' + params.data.no +'" rel="noopener">'+ params.value+'</a>'
            }},
        {headerName: "문의일자", field: "question_date",type:'DateTimeType'},
        {headerName: "처리완료일", field: "answer_date",type:'DateTimeType'},
        {headerName: "상태", field: "state"},
        {headerName: "", width: 'auto', field: ""},
    ];
</script>
<script type="text/javascript" charset="utf-8">
    const pApp = new App('',{
        gridId:"#div-gd",
    });
    let gx;

    $(document).ready(function() {
        pApp.ResizeGrid(300);
        pApp.BindSearchEnter();
        let gridDiv = document.querySelector(pApp.options.gridId);
        gx = new HDGrid(gridDiv, columns);
        Search();
    });

    function Search() {
        let data = $('form[name="search"]').serialize();
        gx.Request('/partner/support/spt02/search', data, -1, searchCallback);
    }

    function searchCallback(data) {}

</script>

@stop

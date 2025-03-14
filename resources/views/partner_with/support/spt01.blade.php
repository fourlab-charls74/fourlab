@extends('partner_with.layouts.layout')
@section('title','공지사항')
@section('content')
<div class="page_tit">
    <h3 class="d-inline-flex">공지사항</h3>
    <div class="d-inline-flex location">
        <span class="home"></span>
        <span>/ 공지사항</span>
    </div>
</div>

<form method="get" name="search" id="search">
    <div id="search-area" class="search_cum_form">
        <div class="card mb-3">
            <div class="d-flex card-header justify-content-between">
                <h4>검색</h4>
                <div>
                    <a href="#" id="search_sbtn" onclick="return Search();" class="btn btn-sm btn-primary shadow-sm pl-2"><i class="fas fa-search fa-sm text-white-50"></i> 조회</a>
                    <div id="search-btn-collapse" class="btn-group mb-0 mb-sm-0"></div>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-lg-4">
                        <div class="form-group">
                            <label for="formrow-firstname-input">등록일</label>
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
                    <div class="col-lg-4">
                        <div class="form-group">
                          <label for="subject">제목</label>
                          <div class="flax_box">
                            <input type='text' id="subject" class="form-control form-control-sm search-all search-enter" name='subject' value=''>
                          </div>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="form-group">
                          <label for="content">내용</label>
                          <div class="flax_box">
                            <input type='text' id="content" class="form-control form-control-sm search-all search-enter" name='content' value=''>
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
<!-- DataTales Example -->
<!-- DataTales Example -->
<div id="filter-area" class="card shadow-none mb-4 ty2 last-card">
    <div class="card-body shadow">
       <div class="card-title">
            <div class="filter_wrap">
                <div id="info_box" class="fl_box">
                    <h6 id="info" style="font-weight: bold">조회버튼을 클릭해 주십시오.</h6>
                    <h6 style="display: none;" class="total font-weight-bold">총 : <span id="gd-total" class="text-primary">0</span> 건</h6>
                </div>
                <div class="fr_box flax_box">
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
        {headerName: "#", field: "num",type:'NumType'},
        {headerName: "구분", field: "com_choice"},
        {headerName: "제목", field: "subject",width:500,
            cellRenderer: function(params) {
                return '<a href="/partner/support/spt01/' + params.data.idx +'" rel="noopener">'+ params.value+'</a>'
            }},
        // {headerName: "조회수", field: "cnt",type:'numberType'},
        {headerName: "작성자", field: "name"},
        {headerName: "등록일시", field: "regi_date",type:"DateTimeType" },
        {headerName: "글번호", field: "idx",hide:true },
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
    });

    function Search() {
        let data = $('form[name="search"]').serialize();
        gx.Request('/partner/support/spt01/search', data, -1, searchCallback);
    }

    function searchCallback(data) {
        const box = document.querySelector('#info_box');
        const info = document.querySelector('#info');
        if (info) {
            box.removeChild(info);
            const total = document.querySelector('.total');
            total.style.display = 'block';
        }
    }

</script>
@stop

@extends('head_with.layouts.layout')
@section('title','로그')
@section('content')
    <div class="page_tit">
        <h3 class="d-inline-flex">로그</h3>
        <div class="d-inline-flex location">
            <span class="home"></span>
            <span>/ 로그</span>
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
                        <div class="col-lg-4 inner-td">
                            <div class="form-group">
                                <label for="formrow-firstname-input">일자</label>
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
                    </div>
                </div>
            </div>
            <div class="resul_btn_wrap mb-3">
                <a href="javascript:;" class="btn btn-sm w-xs btn-primary shadow-sm apply-btn" onclick="return Search();"><i class="fas fa-search fa-sm text-white-50"></i> 조회</a>

                <div class="search_mode_wrap btn-group mr-2 mb-0 mb-sm-0"></div>
            </div>
        </div>
    </form>
    <div id="filter-area" class="card shadow-none ty2 last-card">
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
                <div id="div-gd" class="ag-theme-balham"></div>
            </div>
        </div>
    </div>
    <script src="https://unpkg.com/ag-charts-community@2.1.0/dist/ag-charts-community.min.js"></script>
    <script language="javascript">
        var columns = [
            {headerName: "로그시간", field: "log_time",width:150,cellClass:'hd-grid-code'},
            {headerName: "메뉴번호", field: "menu_no"},
            {headerName: "PID", field: "pid",
                cellRenderer:function(params) {
                    let pid = params.data.pid
                    return pid.toLowerCase();
                }
            },
            {headerName: "메뉴명", field: "menu_nm",width:200},
            {headerName: "URI", field: "cmd", width:250},
            {headerName: "이름", field: "name"},
            {headerName: "IP", field: "ip",width:100},
			{width: "auto"}
        ];
    </script>
    <script type="text/javascript" charset="utf-8">
        let chart_data = null;

        const pApp = new App('',{
            gridId:"#div-gd", height: 265
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
            gx.Request('/head/user/log_search', data, 1);
        }
    </script>

@stop

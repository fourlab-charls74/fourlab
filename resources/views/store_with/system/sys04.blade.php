@extends('store_with.layouts.layout')
@section('title','로그')
@section('content')
    <div class="page_tit">
        <h3 class="d-inline-flex">로그</h3>
        <div class="d-inline-flex location">
            <span class="home"></span>
            <span>/ 시스템</span>
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
                        <div class="col-lg-4 inner-td">
                            <div class="form-group">
                                <label for="id">메뉴명</label>
                                <div class="flex_box">
                                    <input type='text' class="form-control form-control-sm search-enter" name='menu_nm' id="menu_nm" value=''>
                                </div>
                            </div>
					    </div>
                        <div class="col-lg-4 inner-td">
                            <div class="form-group">
                                <label for="id">아이디</label>
                                <div class="flex_box">
                                    <input type='text' class="form-control form-control-sm search-enter" name='id' id="id" value=''>
                                </div>
                            </div>
					    </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-4 inner-td">
                            <div class="form-group">
                                <label for="">자료수/정렬</label>
                                <div class="form-inline">
                                    <div class="form-inline-inner input_box" style="width:24%;">
                                        <select name="limit" class="form-control form-control-sm">
                                            <option value="100">100</option>
                                            <option value="500">500</option>
                                            <option value="1000">1000</option>
                                            <option value="5000">5000</option>
                                        </select>
                                    </div>
                                    <span class="text_line">/</span>
                                    <div class="form-inline-inner input_box" style="width:45%;">
                                        <select name="ord_field" class="form-control form-control-sm">
                                            <option value="log_time">로그시간별</option>
                                            <option value="pid">PID별</option>
                                        </select>
                                    </div>
                                    <div class="form-inline-inner input_box sort_toggle_btn" style="width:24%;margin-left:1%;">
                                        <div class="btn-group" role="group">
                                            <label class="btn btn-primary primary" for="sort_desc" data-toggle="tooltip" data-placement="top" title="내림차순"><i class="bx bx-sort-down"></i></label>
                                            <label class="btn btn-secondary" for="sort_asc" data-toggle="tooltip" data-placement="top" title="오름차순"><i class="bx bx-sort-up"></i></label>
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
                <a href="javascript:;" class="btn btn-sm w-xs btn-primary shadow-sm apply-btn" onclick="return Search();"><i class="fas fa-search fa-sm text-white-50"></i> 조회</a>

                <div class="search_mode_wrap btn-group mr-2 mb-0 mb-sm-0"></div>
            </div>
        </div>
    </form>
    <div class="card shadow mb-0 last-card pt-2 pt-sm-0">
	<div class="card-body">
		<div class="card-title">
			<div class="filter_wrap">
				<div class="fl_box">
					<h6 class="m-0 font-weight-bold">총 : <span id="gd-total" class="text-primary">0</span>건</h6>
				</div>
			</div>
		</div>
		<div class="table-responsive">
			<div id="div-gd" style="height:calc(100vh - 370px);width:100%;" class="ag-theme-balham"></div>
		</div>
	</div>
</div>
    <script src="https://unpkg.com/ag-charts-community@2.1.0/dist/ag-charts-community.min.js"></script>
    <script language="javascript">
        var columns = [
            {headerName: "로그시간", field: "log_time",width:150,cellClass:'hd-grid-code'},
            {headerName: "메뉴번호", field: "menu_no"},
            {headerName: "PID", field: "pid",
                cellRenderer(params) {
                    let pid = params.data.pid;
                    return pid.toLowerCase();
                }
            },
            {headerName: "메뉴명", field: "menu_nm",width:200},
            {headerName: "URI", field: "cmd", width:250},
            {headerName: "이름", field: "name"},
            {headerName: "ID", field: "id",width:70},
            {headerName: "IP", field: "ip",width:100}
        ];
    </script>
    <script type="text/javascript" charset="utf-8">
        let chart_data = null;

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
            gx.Request('/store/system/sys04/search', data,1);
        }
    </script>

@stop

@extends('store_with.layouts.layout')
@section('title','배분현황')
@section('content')

<div class="page_tit">
	<h3 class="d-inline-flex">배분현황</h3>
	<div class="d-inline-flex location">
		<span class="home"></span>
		<span>/ 영업관리</span>
		<span>/ 배분현황</span>
	</div>
</div>

<div id="search-area" class="search_cum_form">
	<form method="get" name="search">
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
							<label>배분일자</label>
                            <div class="form-inline">
                                <input type="text" class="form-control form-control-sm docs-date month mr-2" name="sdate" value="" autocomplete="off">
                                <a href="javascript:void(0);" class="btn btn-sm btn-outline-primary sch-baebun"><i class="bx bx-dots-horizontal-rounded fs-16"></i></a>
                                <input type="text" class="form-control form-control-sm ml-2" name='rel' style="width:50px;">차
                            </div>
						</div>
                    </div>
                    <div class="col-lg-4 inner-td">
                        <div class="form-group">
							<label>출고일자</label>
                            <div class="form-inline">
								<div class="docs-datepicker form-inline-inner input_box w-100">
                                    <div class="input-group">
                                        <input type="text" class="form-control form-control-sm docs-date month" name="sdate" value="{{ @$sdate }}" autocomplete="off">
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
                            <label>배분구분</label>
                            <div class="form-inline form-check-box">
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input" name="baebun_type[0]" id="baebun_type_1" value="1" onclick="changeBaebunType()" checked>
                                    <label class="custom-control-label" for="baebun_type_1">전체</label>
                                </div>
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input" name="baebun_type[1]" id="baebun_type_2" value="2" onclick="changeBaebunType()">
                                    <label class="custom-control-label" for="baebun_type_2">초도</label>
                                </div>
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input" name="baebun_type[2]" id="baebun_type_3" value="3" onclick="changeBaebunType()">
                                    <label class="custom-control-label" for="baebun_type_3">판매</label>
                                </div>
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input" name="baebun_type[3]" id="baebun_type_4" value="4" onclick="changeBaebunType()">
                                    <label class="custom-control-label" for="baebun_type_4">요청</label>
                                </div>
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input" name="baebun_type[4]" id="baebun_type_5" value="5" onclick="changeBaebunType()">
                                    <label class="custom-control-label" for="baebun_type_5">일반</label>
                                </div>
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input" name="baebun_type[5]" id="baebun_type_6" value="6" onclick="changeBaebunType()">
                                    <label class="custom-control-label" for="baebun_type_6">온라인</label>
                                </div>
                            </div>
                        </div>
                    </div>
				</div>
			</div>
		</div>
		<div class="resul_btn_wrap mb-3">
			<a href="#" id="search_sbtn" onclick="return Search();" class="btn btn-sm btn-primary shadow-sm pl-2"><i class="fas fa-search fa-sm text-white-50"></i> 조회</a>
			<a href="#" onclick="gx.Download();" class="btn btn-sm btn-outline-primary shadow-sm pl-2"><i class="bx bx-download fs-16"></i> 엑셀다운로드</a>
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
	const columns = [
		{headerName: '#',	width:35,	type:'NumType',	cellStyle: {"background":"#F5F7F7", "text-align":"center"}},
		{field: "stock_state_date",	headerName: "일자",	width: 120,	cellClass: 'hd-grid-code'},
		{field: "in_qty",	headerName: "입고",	width: 100, type: "currencyType"},
		{field: "out_qty",	headerName: "출고",	width: 100, type: "currencyType"},
		{field: "return_qty",	headerName: "반품",	width: 100, type: "currencyType"},
		{field: "loss_qty",	headerName: "LOSS",	width: 100, type: "currencyType"},
		{field: "term_qty",	headerName: "재고",	width: 100, type: "currencyType"},
		{field: "",	headerName: "",	width: "auto"}
	];
</script>
<script type="text/javascript" charset="utf-8">
	const pApp = new App('', {
		gridId: "#div-gd",
	});
	let gx;

	$(document).ready(function() {
		pApp.ResizeGrid(275);
		pApp.BindSearchEnter();
		let gridDiv = document.querySelector(pApp.options.gridId);
		gx = new HDGrid(gridDiv, columns);
		Search();
	});

	function Search() {
		let data = $('form[name="search"]').serialize();
		gx.Request('/store/sale/sal29/search', data, 1);
	}

    function changeBaebunType() {
        
    }
</script>
	
@stop

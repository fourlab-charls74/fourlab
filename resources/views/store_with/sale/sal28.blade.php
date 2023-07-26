@extends('store_with.layouts.layout')
@section('title','창고 일자별 수불 현황')
@section('content')

<div class="page_tit">
	<h3 class="d-inline-flex">창고 일자별 수불 현황</h3>
	<div class="d-inline-flex location">
		<span class="home"></span>
		<span>/ 영업관리</span>
		<span>/ 창고 일자별 수불 현황</span>
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
							<label for="user_yn">일자</label>
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
					<div class="col-lg-4 inner-td">
						<div class="form-group">
							<label for="">창고</label>
							<div class="d-flex">
								<select name='storage_cd' class="form-control form-control-sm">
									@foreach (@$storages as $storage)
										<option value='{{ $storage->storage_cd }}'>{{ $storage->storage_nm }}</option>
									@endforeach
								</select>
							</div>
						</div>
					</div>
				</div>
			</div>
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
		{field: "rt",	headerName: "일자",	width: 200,	cellClass: 'hd-grid-code'},
		{field: "match_y_cnt",	headerName: "입고",	width: 100,	cellClass: 'hd-grid-code'},
		{field: "match_n_cnt",	headerName: "출고",	width: 100,	cellClass: 'hd-grid-code'},
		{field: "store_cnt",	headerName: "반품",	width: 100,	cellClass: 'hd-grid-code'},
		{field: "id",	headerName: "LOSS",	width: 100,	cellClass: 'hd-grid-code'},
		{field: "store_cnt",	headerName: "재고",	width: 100,	cellClass: 'hd-grid-code'},
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
		//let data = $('form[name="search"]').serialize();
		//gx.Request('/store/product/prd06/search', data, 1);
	}
</script>
	
@stop

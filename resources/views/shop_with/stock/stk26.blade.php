@extends('shop_with.layouts.layout')
@section('title','매장실사/LOSS관리')
@section('content')
<div class="page_tit">
	<h3 class="d-inline-flex">매장실사/LOSS관리</h3>
	<div class="d-inline-flex location">
		<span class="home"></span>
		<span>매장관리</span>
		<span>/ 매장실사/LOSS관리</span>
	</div>
</div>
<div id="search-area" class="search_cum_form">
	<form method="get" name="search">
		<div class="card mb-3">
			<div class="d-flex card-header justify-content-between">
				<h4>검색</h4>
				<div>
					<a href="javascript:void(0);" id="search_sbtn" onclick="return Search();" class="btn btn-sm btn-primary shadow-sm pl-2"><i class="fas fa-search fa-sm text-white-50 mr-1"></i> 조회</a>
					<div id="search-btn-collapse" class="btn-group mb-0 mb-sm-0"></div>
				</div>
			</div>
			<div class="card-body">
				<div class="row">
					<div class="col-lg-4 inner-td">
						<div class="form-group">
							<label>실사일자</label>
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
							<label>LOSS처리여부</label>
							<div class="form-inline form-radio-box">
								<div class="custom-control custom-radio">
									<input type="radio" class="custom-control-input" id="sc_state_A" name="sc_state" value="" checked />
									<label class="custom-control-label" for="sc_state_A">전체</label>
								</div>
								<div class="custom-control custom-radio">
									<input type="radio" class="custom-control-input" id="sc_state_Y" name="sc_state" value="Y" />
									<label class="custom-control-label" for="sc_state_Y">처리완료</label>
								</div>
								<div class="custom-control custom-radio">
									<input type="radio" class="custom-control-input" id="sc_state_N" name="sc_state" value="N" />
									<label class="custom-control-label" for="sc_state_N">미처리</label>
								</div>
							</div>
						</div>
					</div>
					<div class="col-lg-4 inner-td">
						<div class="form-group">
							<label>LOSS사유</label>
							<div class="flex_box">
								<select name='loss_reason' id="loss_reason" class="form-control form-control-sm">
									<option value=''>전체</option>
									@foreach ($loss_reasons as $reason)
										<option value='{{ $reason->code_id }}'>{{ $reason->code_val }}</option>
									@endforeach
								</select>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="resul_btn_wrap mb-3">
			<a href="#" id="search_sbtn" onclick="return Search();" class="btn btn-sm btn-primary shadow-sm pl-2"><i class="fas fa-search fa-sm text-white-50"></i> 조회</a>
			<div class="search_mode_wrap btn-group mr-2 mb-0 mb-sm-0"></div>
		</div>
	</form>
</div>

<!-- DataTales Example -->
<div class="card shadow mb-0 last-card pt-2 pt-sm-0">
	<div class="card-body">
		<div class="card-title">
			<div class="filter_wrap">
				<div class="d-flex justify-content-between">
					<h6 class="m-0 font-weight-bold">총 : <span id="gd-total" class="text-primary">0</span>건</h6>
				</div>
			</div>
		</div>
		<div class="table-responsive">
			<div id="div-gd" class="ag-theme-balham"></div>
		</div>
	</div>
</div>

<script language="javascript">
	let columns = [
		{headerName: "No", pinned: "left", valueGetter: "node.id", cellRenderer: "loadingRenderer", width: 50, cellStyle: {"text-align": "center"}},
		{field: "sc_date", headerName: "실사일자", width: 80, cellStyle: {"text-align": "center"}},
		{field: "sc_cd", headerName: "실사코드", width: 140, cellStyle: {"text-align": "center"},
			cellRenderer: function(params) {
				let sc_date = params.data.sc_date;
				let date = sc_date.replace('-','');
				let stock_check_date = date.replace('-','');
				let sc_cd = params.data.sc_cd.toString();
				let sc_code = sc_cd.padStart(3, '0');

				return `<a href="javascript:void(0);" onclick="openDetailPopup(${params.value})">${params.data.store_cd}_${stock_check_date}_${sc_code}</a>`;
			}
		},
		{field: "sc_type", headerName: "실사구분", width: 70, cellStyle: (params) => ({"text-align": "center", "color": params.value === 'B' ? '#2aa876' : params.value === 'C' ? '#ff7f00' : 'none'}),
			cellRenderer: (params) => params.value === 'G' ? '일반' : params.value === 'B' ? '일괄' : params.value === 'C' ? '바코드' : '-',
		},
		{field: "store_cd", headerName: "매장코드", width: 80, cellStyle: {"text-align": "center"}},
		{field: "store_nm", headerName: "매장명", width: 150},
		{field: "store_qty", headerName: "매장보유재고", width: 90, type: "currencyType"},
		{field: "qty", headerName: "실사재고", width: 80, type: "currencyType"},
		{field: "loss_qty", headerName: "LOSS 총수량", width: 90, type: "currencyType"},
		{field: "loss_price", headerName: "LOSS 금액", width: 90, type: "currencyType"},
		{field: "sc_state", headerName: "LOSS 처리여부", width: 100,
			cellStyle: (params) => ({"text-align": "center", "color": params.value == "N" ? "red" : params.value == "Y" ? "green" : "none"}),
			cellRenderer: (params) => params.value === "Y" ? "처리완료" : "미처리",
		},
		{field: "md_id", hide: true},
		{field: "md_nm", headerName: "담당자", width: 80, cellStyle: {"text-align": "center"}},
		{field: "comment", headerName: "메모", width: 0},
	];
</script>
<script type="text/javascript" charset="utf-8">
	let gx;
	const pApp = new App('', { gridId: "#div-gd", height: 265 });

	$(document).ready(function() {
		pApp.ResizeGrid(265);
		pApp.BindSearchEnter();
		let gridDiv = document.querySelector(pApp.options.gridId);
		gx = new HDGrid(gridDiv, columns);

		Search();
	});

	function Search() {
		let data = $('form[name="search"]').serialize();
		gx.Request('/shop/stock/stk26/search', data, -1);
	}

	function openDetailPopup(sc_cd = '') {
		const url = '/shop/stock/stk26/show/' + sc_cd;
		window.open(url, "_blank", "toolbar=no,scrollbars=yes,resizable=yes,status=yes,top=300,left=300,width=1700,height=880");
	}
</script>
@stop

@extends('store_with.layouts.layout')
@section('title','매장회원 구매통계')
@section('content')

	<div class="page_tit">
		<h3 class="d-inline-flex">매장회원 구매통계</h3>
		<div class="d-inline-flex location">
			<span class="home"></span>
			<span>/ 영업관리</span>
			<span>/ 매장회원 구매통계</span>
		</div>
	</div>

	<form method="get" name="search">
		<div id="search-area" class="search_cum_form">
			<div class="card mb-3">
				<div class="d-flex card-header justify-content-between">
					<h4>검색</h4>
					<div class="flax_box">
						<a href="#" id="search_sbtn" onclick="Search();" class="btn btn-sm btn-primary shadow-sm mr-1"><i class="fas fa-search fa-sm text-white-50"></i> 조회</a>
						{{--						<a href="javascript:gx.Download('판매유형별매출통계_{{ date('YmdH') }}');" class="btn btn-download btn-sm btn-outline-primary shadow-sm mr-1"><i class="bx bx-download fs-16"></i> 엑셀다운로드</a>--}}
						<a href="javascript:void(0);" class="export-excel btn btn-sm btn-primary shadow-sm pl-2 mr-1"><i class="bx bx-download fs-16"></i> 엑셀다운로드</a>
						<div id="search-btn-collapse" class="btn-group mb-0 mb-sm-0"></div>
					</div>
				</div>
				<div class="card-body">

					<div class="row">
						<div class="col-lg-4 inner-td">
							<div class="form-group">
								<label for="formrow-firstname-input">판매상태일</label>
								<div class="form-inline">
									<div class="docs-datepicker form-inline-inner input_box">
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
								<label for="good_types">판매채널/매장구분</label>
								<div class="d-flex align-items-center">
									<div class="flex_box w-100">
										<select name='store_channel' id="store_channel" class="form-control form-control-sm" onchange="chg_store_channel();">
											<option value=''>전체</option>
											@foreach ($store_channel as $sc)
												<option value='{{ $sc->store_channel_cd }}' @if(@$p_store_channel === $sc->store_channel_cd) selected @endif>{{ $sc->store_channel }}</option>
											@endforeach
										</select>
									</div>
									<span class="mr-2 ml-2">/</span>
									<div class="flex_box w-100">
										<select id='store_channel_kind' name='store_channel_kind' class="form-control form-control-sm" @if(@$p_store_kind == '') disabled @endif>
											<option value=''>전체</option>
											@foreach ($store_kind as $sk)
												<option value='{{ $sk->store_kind_cd }}' @if(@$p_store_kind === $sk->store_kind_cd) selected @endif>{{ $sk->store_kind }}</option>
											@endforeach
										</select>
									</div>
								</div>
							</div>
						</div>
						<div class="col-lg-4 inner-td">
							<div class="form-group">
								<label>매장명</label>
								<div class="form-inline inline_btn_box">
									<input type='hidden' id="store_nm" name="store_nm" value="{{ @$store->store_nm }}">
									<select id="store_no" name="store_no" class="form-control form-control-sm select2-store"></select>
									<a href="javascript:void(0);" class="btn btn-sm btn-outline-primary sch-store"><i class="bx bx-dots-horizontal-rounded fs-16" style="line-height: 27px;"></i></a>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="resul_btn_wrap mb-3">
				<a href="#" id="search_sbtn" onclick="Search();" class="btn btn-sm btn-primary shadow-sm mr-1"><i class="fas fa-search fa-sm text-white-50"></i> 조회</a>
			</div>
		</div>
	</form>
	<!-- DataTales Example -->

	<div id="filter-area" class="card shadow-none mb-0 search_cum_form ty2 last-card">
		<div class="card-body shadow">
			<div class="card-title">
				<div class="filter_wrap">
					<div class="fl_box">
						<h6 class="m-0 font-weight-bold">총 <span id="gd-total" class="text-primary">0</span> 건</h6>
					</div>
					<div class="d-flex justify-content-end">
						<div class="custom-control custom-checkbox form-check-box pr-2" style="display:inline-block;">
							<input type="checkbox" class="custom-control-input" name="grid_expand" id="grid_expand" onchange="return setAllRowGroupExpanded(this.checked);" checked>
							<label class="custom-control-label font-weight-normal" for="grid_expand">항목펼쳐보기</label>
						</div>
					</div>
				</div>
			</div>
			<div class="table-responsive">
				<div id="div-gd" style="height:calc(100vh - 370px);width:100%;" class="ag-theme-balham"></div>
			</div>
		</div>
	</div>
	<style>
		.ag-row-level-1 {
			background-color: #F1F1F1 !important;
		}
		.ag-row-level-2 {
			background-color: #edf4fd !important;
		}
	</style>
	<script language="javascript">
		const pinnedRowData = [{ store_nm:'합계' , ratio1:'0:0', ratio2:'0:0', qty:0, recv_amt:0, u_qty:0, u_recv_amt:0, ord_cnt:0, avg_qty:0, join_cnt:0, n_off_qty:0, n_off_recv_amt:0, n_on_qty:0, n_on_recv_amt:0 }];
		const sumValuesFunc = (params) => params.values.reduce((a,c) => a + (c * 1), 0);

		let columns = [
			{headerName: "판매채널", field: "store_channel_nm", rowGroup:true, hide:true, width: 120, cellClass: 'hd-grid-code'},
			{headerName: '판매채널', showRowGroup: 'store_channel_nm', cellRenderer: 'agGroupCellRenderer', width: 120},
			{headerName: "매장구분", field: "store_kind_nm", rowGroup:true, hide:true, width: 80, cellClass: 'hd-grid-code'},
			{headerName: "매장구분", showRowGroup: "store_kind_nm", cellRenderer: 'agGroupCellRenderer',  width: 150,
				cellStyle: (params) => {
					if (params.node.rowPinned === 'top') {
						return { 'text-align' : 'center' };
					}
				}
			},
			{headerName: "매장명", field: "store_nm", width: 120},
			{headerName: '회원:비회원(%)(수량)',
				children: [
					{headerName: "전체",			field: "ratio1",	width: 80, cellClass: 'hd-grid-code',
						aggFunc: (params) => {
							return Math.round((params.rowNode.allLeafChildren.reduce((a, c) => (c.data?.u_qty * 1) + a, 0) / params.rowNode.allLeafChildren.reduce((a, c) => (c.data?.qty * 1) + a, 0) ) * 100.0) + ':' + Math.round(((params.rowNode.allLeafChildren.reduce((a, c) => (c.data?.n_off_qty * 1) + a, 0) + params.rowNode.allLeafChildren.reduce((a, c) => (c.data?.n_on_qty * 1) + a, 0)) / params.rowNode.allLeafChildren.reduce((a, c) => (c.data?.qty * 1) + a, 0) ) * 100.0);
						},
					},
					{headerName: "온라인제외",	field: "ratio2",	width: 80, cellClass: 'hd-grid-code',
						aggFunc: (params) => {
							let mem = "";
							let nomem = "";
							if((params.rowNode.allLeafChildren.reduce((a, c) => (c.data?.qty * 1) + a, 0) - params.rowNode.allLeafChildren.reduce((a, c) => (c.data?.n_on_qty * 1) + a, 0)) === 0)	
								mem = '0';
							else
								mem	= Math.round((params.rowNode.allLeafChildren.reduce((a, c) => (c.data?.u_qty * 1) + a, 0) / (params.rowNode.allLeafChildren.reduce((a, c) => (c.data?.qty * 1) + a, 0) - params.rowNode.allLeafChildren.reduce((a, c) => (c.data?.n_on_qty * 1) + a, 0)) ) * 100.0);
							
							if((params.rowNode.allLeafChildren.reduce((a, c) => (c.data?.qty * 1) + a, 0) - params.rowNode.allLeafChildren.reduce((a, c) => (c.data?.n_on_qty * 1) + a, 0)) === 0)
								nomem	= '0';
							else
								nomem	= Math.round((params.rowNode.allLeafChildren.reduce((a, c) => (c.data?.n_off_qty * 1) + a, 0) / (params.rowNode.allLeafChildren.reduce((a, c) => (c.data?.qty * 1) + a, 0) - params.rowNode.allLeafChildren.reduce((a, c) => (c.data?.n_on_qty * 1) + a, 0)) ) * 100.0);
							
							return mem + ':' + nomem;
						},
					},
				]
			},
			{headerName: '매출합계',
				children: [
					{headerName: "판매수량",		field: "qty",		width: 80, type: 'numberType', aggFunc: sumValuesFunc},
					{headerName: "실결제금액",	field: "recv_amt",	width: 100, type: 'numberType', aggFunc: sumValuesFunc},
				]
			},
			{headerName: '회원',
				children: [
					{headerName: "판매수량",		field: "u_qty",		width: 80, type: 'numberType', aggFunc: sumValuesFunc},
					{headerName: "실결제금액",	field: "u_recv_amt",width: 100, type: 'numberType', aggFunc: sumValuesFunc},
					{headerName: "구매회원수",	field: "ord_cnt",	width: 80, type: 'numberType', aggFunc: sumValuesFunc},
					{headerName: "평균구매수",	field: "avg_qty",	width: 80, type: 'numberType', type: 'percentType',
						aggFunc: (params) => {
							if(params.rowNode.allLeafChildren.reduce((a, c) => (c.data?.ord_cnt * 1) + a, 0) === 0) 
								return '0.00';
							else
								return Math.round((params.rowNode.allLeafChildren.reduce((a, c) => (c.data?.ord_qty * 1) + a, 0) / params.rowNode.allLeafChildren.reduce((a, c) => (c.data?.ord_cnt * 1) + a, 0) ) * 100.0) / 100.0;
						},
					},
					{headerName: "기간가입수",	field: "join_cnt",	width: 80, type: 'numberType', aggFunc: sumValuesFunc},
				]
			},
			{headerName: '비회원(오프라인)',
				children: [
					{headerName: "판매수량",		field: "n_off_qty",	width: 80, type: 'numberType', aggFunc: sumValuesFunc},
					{headerName: "실결제금액",	field: "n_off_recv_amt",	width: 100, type: 'numberType', aggFunc: sumValuesFunc},
				]
			},
			{headerName: '비회원(온라인)',
				children: [
					{headerName: "판매수량",		field: "n_on_qty",	width: 80, type: 'numberType', aggFunc: sumValuesFunc},
					{headerName: "실결제금액",	field: "n_on_recv_amt",	width: 100, type: 'numberType', aggFunc: sumValuesFunc},
				]
			},
			{width: 'auto'}
		];

	</script>

	<script type="text/javascript" charset="utf-8">
		const pApp = new App('', { gridId:"#div-gd", height: 265 });
		let gx;

		$(document).ready(function() {
			pApp.ResizeGrid(265);
			let gridDiv = document.querySelector(pApp.options.gridId);
			gx = new HDGrid(gridDiv, columns, {
				pinnedTopRowData: pinnedRowData,
				getRowStyle: (params) => {
					if (params.node.rowPinned)  return {'font-weight': 'bold', 'background': '#eee !important', 'border': 'none'};
				},
				rollup: true,
				groupSuppressAutoColumn: true,
				suppressAggFuncInHeader: true,
				enableRangeSelection: true,
				animateRows: true,

			});
			pApp.BindSearchEnter();

			$(".export-excel").on("click", function (e) {
				depthExportChecker.Open({
					depths: ['판매채널별', '매장구분별'],
					download: (level) => {
						gx.Download('매장회원구매통계_{{ date('YmdH') }}.xlsx', { type: 'excel', level: level });
					}
				});
			});

			// 판매채널 선택되지않았을때 매장구분 disabled처리하는 부분
			load_store_channel();

			//Search();
		});

		function Search() {
			let data = $('form[name="search"]').serialize();
			gx.Request('/store/sale/sal40/search', data, -1, function(e){
				let t = e.head.total_data;

				const pinnedRowData = {
					sale_type_nm : "합계",
					ratio1 :		t.ratio1,
					ratio2 :		t.ratio2,
					qty :			t.qty,
					recv_amt :		t.recv_amt,
					u_qty :			t.u_qty,
					u_recv_amt :	t.u_recv_amt,
					ord_cnt :		t.ord_cnt,
					avg_qty :		t.avg_qty,
					join_cnt :		t.join_cnt,
					n_off_qty :		t.n_off_qty,
					n_off_recv_amt :t.n_off_recv_amt,
					n_on_qty :		t.n_on_qty,
					n_on_recv_amt :	t.n_on_recv_amt
				};

				gx.gridOptions.api.setPinnedTopRowData([pinnedRowData]);

				setAllRowGroupExpanded($("#grid_expand").is(":checked"))
			});
		}

	</script>
@stop

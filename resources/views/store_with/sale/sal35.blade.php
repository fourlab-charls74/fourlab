@extends('store_with.layouts.layout')
@section('title','채널별목표대비실적현황')
@section('content')
	<div class="page_tit">
		<h3 class="d-inline-flex">채널별목표대비실적현황</h3>
		<div class="d-inline-flex location">
			<span class="home"></span>
			<span>/ 경영관리</span>
			<span>/ 채널별목표대비실적현황</span>
		</div>
	</div>
	<form method="get" name="search">
		<div id="search-area" class="search_cum_form">
			<div class="card mb-3">
				<div class="d-flex card-header justify-content-between">
					<h4>검색</h4>
					<div>
						<a href="#" id="search_sbtn" onclick="return Search();" class="btn btn-sm btn-primary shadow-sm pl-2"><i class="fas fa-search fa-sm text-white-50"></i> 조회</a>
						<a href="javascript:void(0);" class="export-excel btn btn-sm btn-primary shadow-sm pl-2 mr-1"><i class="bx bx-download fs-16"></i> 엑셀다운로드</a>
						<!-- 2023-05-25 검색조건 초기화 주석처리 -양대성- -->
						<!-- <a href="#" onclick="formReset()" class="btn btn-sm btn-outline-primary shadow-sm">검색조건 초기화</a> -->
						<div id="search-btn-collapse" class="btn-group mb-0 mb-sm-0"></div>
					</div>
				</div>
				<div class="card-body">
					<div class="row">
						<div class="col-lg-4">
							<div class="form-group">
								<label for="">기간</label>
								<div class="docs-datepicker flex_box">
									<div class="input-group">
										<input type="text" class="form-control form-control-sm docs-date month" name="sdate" value="{{ $sdate }}" autocomplete="off">
										<div class="input-group-append">
											<button type="button" class="btn btn-outline-secondary docs-datepicker-trigger p-0 pl-2 pr-2" disable>
												<i class="fa fa-calendar" aria-hidden="true"></i>
											</button>
										</div>
									</div>
									<div class="docs-datepicker-container"></div>
								</div>
							</div>
						</div>
	{{--						<div class="col-lg-4 inner-td">--}}
	{{--							<div class="form-group">--}}
	{{--								<label for="good_types">판매채널/매장구분</label>--}}
	{{--								<div class="d-flex align-items-center">--}}
	{{--									<div class="flex_box w-100">--}}
	{{--										<select name='store_channel' id="store_channel" class="form-control form-control-sm" onchange="chg_store_channel();">--}}
	{{--											<option value=''>전체</option>--}}
	{{--											@foreach ($store_channel as $sc)--}}
	{{--												<option value='{{ $sc->store_channel_cd }}'>{{ $sc->store_channel }}</option>--}}
	{{--											@endforeach--}}
	{{--										</select>--}}
	{{--									</div>--}}
	{{--									<span class="mr-2 ml-2">/</span>--}}
	{{--									<div class="flex_box w-100">--}}
	{{--										<select id='store_channel_kind' name='store_channel_kind' class="form-control form-control-sm" disabled>--}}
	{{--											<option value=''>전체</option>--}}
	{{--											@foreach ($store_kind as $sk)--}}
	{{--												<option value='{{ $sk->store_kind_cd }}'>{{ $sk->store_kind }}</option>--}}
	{{--											@endforeach--}}
	{{--										</select>--}}
	{{--									</div>--}}
	{{--								</div>--}}
	{{--							</div>--}}
	{{--						</div>--}}
	{{--						<div class="col-lg-4 inner-td">--}}
	{{--							<div class="form-group">--}}
	{{--								<label for="store_no">매장명</label>--}}
	{{--								<div class="form-inline inline_btn_box">--}}
	{{--									<select id="store_no" name="store_no" class="form-control form-control-sm select2-store"></select>--}}
	{{--									<a href="javascript:void(0);" class="btn btn-sm btn-outline-primary sch-store"><i class="bx bx-dots-horizontal-rounded fs-16"></i></a>--}}
	{{--								</div>--}}
	{{--							</div>--}}
	{{--						</div>--}}
					</div>
				</div>
			</div>
			<div class="resul_btn_wrap mb-3">
				<a href="#" id="search_sbtn" onclick="return Search();" class="btn btn-sm btn-primary shadow-sm pl-2"><i class="fas fa-search fa-sm text-white-50"></i> 조회</a>
				<a href="javascript:void(0);" class="export-excel btn btn-sm btn-primary shadow-sm pl-2 mr-1"><i class="bx bx-download fs-16"></i> 엑셀다운로드</a>
				<div class="search_mode_wrap btn-group mr-2 mb-0 mb-sm-0"></div>
			</div>
		</div>
	</form>
	<div id="filter-area" class="card shadow-none mb-0 search_cum_form ty2 last-card">
		<div class="card-body shadow">
			<div class="table-responsive">
				<div id="div-gd2" style="height:100%;min-height:176px " class="ag-theme-balham"></div>
			</div>
		</div>
		<div class="card-body shadow">
			<div class="card-title mb-3">
				<div class="filter_wrap">
					<div class="fl_box">
						<h6 class="m-0 font-weight-bold">총 <span id="gd-total" class="text-primary">0</span> 건</h6>
					</div>
				</div>
			</div>
			<div class="table-responsive">
				<div id="div-gd" style="height:calc(100vh - 370px);width:100%;" class="ag-theme-balham"></div>
			</div>
		</div>
	</div>
	<script language="javascript">
		/*  
		* 달성율 = 판매금액 / 목표금액 * 100
		* 신장율 = 판매금액 / 전년판매 * 100
		* */
		const pinnedRowData = [{ stores : "total", proj_amt : 0, recv_amt : 0, progress_proj_rate : 0, last_recv_amt : 0, elongation_rate : 0 }];
		const columns = [
			{headerName: "Retail Store Sales", field: "stores", width: 160,
				cellStyle : (params) => params.node.rowPinned === 'top' ? {'text-align' : 'center'} : {},
			},
			{headerName: "Budget ", field: "proj_amt", width: 130, type: "currencyType"},
			{headerName: "Sales ", field: "recv_amt", width: 130, type: "currencyType"},
			{headerName: "Sales vs Budget %", field: "progress_proj_rate", width: 130, type: "percentType",
				cellRenderer: (params) => {
					if (params.node.rowPinned === 'top') {
						let t_proj_amt = parseInt(params.node.data.proj_amt);
						let t_recv_amt = parseInt(params.node.data.recv_amt);

						return (t_proj_amt > 0) ? (t_recv_amt / t_proj_amt * 100).toFixed(2) : "0.00";
					} else {
						let recv_amt = parseInt(params.data.recv_amt);
						let proj_amt = parseInt(params.data.proj_amt);
						return (recv_amt > 0 && proj_amt > 0) ? params.value : "0.00";
					}
				}
			},
			{headerName: "Sales LY", field: "last_recv_amt", width: 130, type: "currencyType"},
			{headerName: "Sales vs LY %", field: "elongation_rate", width: 130, type: "percentType",
				cellRenderer: (params) => {
					if (params.node.rowPinned === 'top') {
						let t_recv_amt = parseInt(params.node.data.recv_amt);
						let t_last_recv_amt = parseInt(params.node.data.last_recv_amt);

						return (t_recv_amt > 0) ? (t_recv_amt / t_last_recv_amt * 100).toFixed(2) : "0.00";
					} else {
						let recv_amt = parseInt(params.data.recv_amt);
						let last_recv_amt = parseInt(params.data.last_recv_amt);
						return (recv_amt > 0 && last_recv_amt > 0) ? params.value : "0.00";
					}
				}
			},
			{width: "auto"}
		];

		const columns2 = [
			{headerName: "Sales By Channels", field: "channels", width: 160, aggSum: "total"
				,cellStyle : (params) => params.node.rowPinned === 'top' ? {'text-align' : 'center'} : {}
			},
			{headerName: "Budget ", field: "channel_proj_amt", width: 130, type: "currencyType", aggregation: true },
			{headerName: "Sales ", field: "channel_recv_amt", width: 130, type: "currencyType", aggregation: true },
			{headerName: "Sales vs Budget %", field: "channel_progress_proj_rate", width: 130, type: "percentType",
				cellRenderer: (params) => {
					if (params.node.rowPinned === 'top') {
						let t_channel_proj_amt = parseInt(params.node.data.channel_proj_amt);
						let t_channel_recv_amt = parseInt(params.node.data.channel_recv_amt);

						return (t_channel_proj_amt > 0) ? (t_channel_recv_amt / t_channel_proj_amt * 100).toFixed(2) : "0.00";
					} else {
						let channel_recv_amt = parseInt(params.data.channel_recv_amt);
						let channel_proj_amt = parseInt(params.data.channel_proj_amt);
						return (channel_recv_amt > 0 && channel_proj_amt > 0) ? params.value : "0.00";
					}
				}
			},
			{headerName: "Sales LY", field: "channel_last_recv_amt", width: 130, type: "currencyType", aggregation: true },
			{headerName: "Sales vs LY %", field: "channel_elongation_rate", width: 130, type: "percentType",
				cellRenderer: (params) => {
					if (params.node.rowPinned === 'top') {
						let t_channel_recv_amt = parseInt(params.node.data.channel_recv_amt);
						let t_channel_last_recv_amt = parseInt(params.node.data.channel_last_recv_amt);

						return (t_channel_recv_amt > 0) ? (t_channel_recv_amt / t_channel_last_recv_amt * 100).toFixed(2) : "0.00";
					} else {
						let channel_recv_amt = parseInt(params.data.channel_recv_amt);
						let channel_last_recv_amt = parseInt(params.data.channel_last_recv_amt);
						return (channel_recv_amt > 0 && channel_last_recv_amt > 0) ? params.value : "0.00";
					}
				}
			},
			{width: "auto"}
		];

	</script>
	<script type="text/javascript" charset="utf-8">
		const pApp = new App('',{
			gridId:"#div-gd",
		});
		const pApp2 = new App('',{
			gridId:"#div-gd2",
		});

		let gx;
		let gx2;
		$(document).ready(function() {
			pApp.ResizeGrid(500);
			pApp.BindSearchEnter();
			let gridDiv = document.querySelector(pApp.options.gridId);
			gx = new HDGrid(gridDiv, columns, {
				pinnedTopRowData: pinnedRowData,
				getRowStyle: (params) => {
					if (params.node.rowPinned)  return {'font-weight': 'bold', 'background': '#eee !important', 'border': 'none'};
				},
			});

			pApp2.ResizeGrid(1000);
			pApp2.BindSearchEnter();
			let gridDiv2 = document.querySelector(pApp2.options.gridId);
			gx2 = new HDGrid(gridDiv2, columns2, {
				getRowStyle: (params) => params.node.rowPinned ? ({'font-weight': 'bold', 'background-color': '#eee', 'border': 'none'}) : false,
			});
			Search();
			Search2();

			$(".export-excel").on("click", function (e) {
				let gridOptions = gx.gridOptions;
				let excelParams = {
					fileName: '채널별목표대비실적현황_{{ date('YmdH') }}.xlsx',
					sheetName: 'Sheet1',
				};

				gridOptions.api.exportDataAsExcel(excelParams);
			});

			// 판매채널 선택되지않았을때 매장구분 disabled처리하는 부분
			// load_store_channel();
		});

			function Search() {
				let data = $('form[name="search"]').serialize();
				gx.Request('/store/sale/sal35/search', data, -1, function(e) {

					let t = e.head.total_data;
					let format_date = e.head.format_date;


					const pinnedRowData = {
						stores : "total",
						proj_amt : t.total_proj_amt,
						recv_amt : t.total_recv_amt,
						last_recv_amt : t.total_last_recv_amt,
						progress_proj_rate : t.total_progress_proj_rate,
						elongation_rate : t.total_elongation_rate,
					};

					gx.gridOptions.api.setPinnedTopRowData([pinnedRowData]);
					
					changeColumn(format_date);
				});
				Search2();
			}

			function Search2() {
				let data = $('form[name="search"]').serialize();
				gx2.Aggregation({ sum: "top"});
				gx2.Request('/store/sale/sal35/search2', data, -1);
			}
			
			// 기간을 변경하면 해당 기간으로 컬럼명을 변경하는 부분
			function changeColumn(date) {
				let headerName = "Budget " + date;
				let gridColumn = gx.gridOptions.columnApi.getColumn("proj_amt");
				let gridColumn2 = gx2.gridOptions.columnApi.getColumn("channel_proj_amt");

				if (gridColumn) {
					gridColumn.colDef.headerName = headerName;
					gx.gridOptions.api.refreshHeader();
				}
				
				if (gridColumn2) {
					gridColumn2.colDef.headerName = headerName;
					gx2.gridOptions.api.refreshHeader();
				}
			}
	</script>
@stop
	
						
						
						
						
						
						
						
						
						
						
						
						
						
						
						
						

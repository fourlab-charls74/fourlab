@extends('store_with.layouts.layout')
@section('title','월간재고소진현황')
@section('content')

	<div class="page_tit">
		<h3 class="d-inline-flex">월간재고소진현황</h3>
		<div class="d-inline-flex location">
			<span class="home"></span>
			<span>/ 영업관리</span>
			<span>/ 월간재고소진현황</span>
		</div>
	</div>

	<form method="get" name="search">
		<div id="search-area" class="search_cum_form">
			<div class="card mb-3">
				<div class="d-flex card-header justify-content-between">
					<h4>검색</h4>
					<div class="flax_box">
						<a href="javascript:void(0);" id="search_sbtn" onclick="Search();" class="btn btn-sm btn-primary shadow-sm mr-1"><i class="fas fa-search fa-sm text-white-50"></i> 조회</a>
						<!-- 2023-05-25 검색조건 초기화 주석처리 -양대성- -->
						<!-- <a href="javascript:void(0);" onclick="initSearchInputs()" class="btn btn-sm btn-outline-primary mr-1">검색조건 초기화</a> -->
						<a href="javascript:void(0);" class="export-excel btn btn-sm btn-primary shadow-sm pl-2 mr-1"><i class="bx bx-download fs-16"></i> 엑셀다운로드</a>
						<div id="search-btn-collapse" class="btn-group mb-0 mb-sm-0"></div>
					</div>
				</div>
				<div class="card-body">
					<div class="row">
						<div class="col-lg-4 inner-td">
							<div class="form-group">
								<label for="formrow-firstname-input">판매기간</label>
								<div class="form-inline">
									<div class="docs-datepicker form-inline-inner input_box">
										<div class="input-group">
											<input type="text" class="form-control form-control-sm docs-date month" name="sdate" value="{{ $sdate }}" autocomplete="off">
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
											<input type="text" class="form-control form-control-sm docs-date month" name="edate" value="{{ $edate }}" autocomplete="off">
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
								<label for="">브랜드</label>
								<div class="flex_box">
									<select name='brand' class="form-control form-control-sm">
										<option value='F'>피엘라벤</option>
										<option value='W'>한바그</option>
										<option value='P'>프리머스</option>
										<option value='TR'>티에라</option>
									</select>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="resul_btn_wrap mb-3">
				<a href="javascript:void(0);" id="search_sbtn" onclick="Search();" class="btn btn-sm btn-primary shadow-sm mr-1"><i class="fas fa-search fa-sm text-white-50"></i> 조회</a>
				<!-- 2023-05-25 검색조건 초기화 주석처리 -양대성- -->
				<!-- <a href="javascript:void(0);" onclick="initSearchInputs()" class="btn btn-sm btn-outline-primary mr-1">검색조건 초기화</a> -->
				<a href="javascript:void(0);" class="export-excel btn btn-sm btn-primary shadow-sm pl-2 mr-1"><i class="bx bx-download fs-16"></i> 엑셀다운로드</a>
				<div id="search-btn-collapse" class="btn-group mb-0 mb-sm-0"></div>
			</div>
		</div>
	</form>
	<!-- DataTales Example -->
	<div id="filter-area" class="card shadow-none mb-0 search_cum_form ty2 last-card">
		<div class="card-body shadow">
			<div class="table-responsive">
				<div id="div-gd" style="height:calc(100vh - 370px);width:100%;" class="ag-theme-balham"></div>
			</div>
		</div>
	</div>
	<script language="javascript">

		const pinnedRowData = [{ rank_idx : "랭크" }];

		let columns = [
			{field : "season_brand", headerName: "시즌별", pinned: "left", width: 100, cellStyle: {"text-align": "center"},
				cellRenderer: function(params) {
					if (params.node.rowPinned === 'top') {
						return "total";
					} else {
						return params.value;
					}
				}
			},
			{width: 'auto'}
		];

		const pApp = new App('',{
			gridId:"#div-gd",
		});
		
		let gx;

		$(document).ready(function() {
			pApp.ResizeGrid(235);
			pApp.BindSearchEnter();
			let gridDiv = document.querySelector(pApp.options.gridId);
			gx = new HDGrid(gridDiv, columns, {
				pinnedTopRowData : pinnedRowData,
				getRowStyle: (params) => {
					if (params.node.rowPinned)  return {'font-weight': 'bold', 'background': '#eee !important', 'border': 'none'};
				},
			});

			Search();

		});

		function setColumn(months) {
			console.log(months);
			if(!months) return;
			columns.splice(1);

			for(let i = 0; i < months.length; i++) {
				let val = months[i].val;
				let fmt = months[i].fmt;
				columns.push({
					field: val,
					headerName: fmt,
					children: [
						{field: val + '_total_qty', headerName: '누적판매수', minWidth: 80},
						{field: val + '_total_recv_amt', headerName: '누적실판매가', type: "currencyType", minWidth: 150},
						{field: val + '_total_wonga', headerName: '누적판매원가', type: "currencyType", minWidth: 150},
						{field: val + '_total_sale_rate_wonga', headerName: '총판매율(원가)', type: "percentType", minWidth: 90},
						{field: val + '_total_sale_rate_qty', headerName: '총판매율(수량)', type: "percentType", minWidth: 90},
						{field: val + '_ord_amt', headerName: '기간판매수', type: "currencyType", minWidth: 80},
						{field: val + '_wty', headerName: '재고수', type: "currencyType", minWidth: 70},
						{field: val + '_wonga', headerName: '재고원가', type: "currencyType", minWidth: 150},
						{field: val + '_exp_soldout_date', headerName: '예상소진일', type: "currencyType", minWidth: 80},
					],
				});
			}
			columns.push({ width: "auto" });
			gx.gridOptions.api.setColumnDefs(columns);
		}

		//검색
		function Search() {
			let data = $('form[name="search"]').serialize();
			gx.Request('/store/sale/sal37/search', data, 1, function(e) {
				setColumn(e.head.months);
			});
		}
		
	</script>


@stop

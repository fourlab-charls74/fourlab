@extends('store_with.layouts.layout-nav')
@section('title', '출고거부 리스트')
@section('content')
	<div class="py-3 px-sm-3">
		<div class="page_tit d-flex justify-content-between">
			<div class="d-flex">
				<h3 class="d-inline-flex">출고거부 리스트</h3>
				<div class="d-inline-flex location">
					<span class="home"></span>
					<span>/ 판매관리</span>
					<span>/ 온라인출고요청</span>
					<span>/ 출고거부 리스트</span>
				</div>
			</div>
		</div>

		{{-- 검색 --}}
		<div id="search-area" class="search_cum_form mb-2">
			<form name="search" method="get">
				<div class="card">
					<div class="d-flex card-header justify-content-between">
						<h4>검색</h4>
						<div class="flax_box">
							<a href="javascript:void(0);" id="search_sbtn" onclick="return Search();" class="btn btn-sm btn-primary shadow-sm pl-2"><i class="fas fa-search fa-sm text-white-50"></i> 조회</a>
						</div>
					</div>
					<div class="card-body">
						<div class="row">
							<div class="col-lg-6 inner-td">
								<div class="form-group">
									<label for="good_types">조회일자</label>
									<div class="form-inline date-select-inbox">
										<div class="docs-datepicker form-inline-inner input_box">
											<div class="input-group">
												<input type="text" class="form-control form-control-sm docs-date" name="sdate" value="{{ @$sdate }}" autocomplete="off" disable>
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
												<input type="text" class="form-control form-control-sm docs-date" name="edate" value="{{ @$edate }}" autocomplete="off">
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
									<label for="prd_cd">바코드</label>
									<div class="flax_box">
										<input type='text' class="form-control form-control-sm search-enter" name='prd_cd' id="prd_cd" value=''>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</form>
		</div>
		{{-- 출고거부 리스트 --}}
		<div class="card shadow mb-0 last-card pt-2 pt-sm-0">
			<div class="card-body pb-2">
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
	</div>

	<style>
		.ag-row-level-1 {
			background-color: #f2f2f2 !important;
		}
	</style>

	<script language="javascript">

		let columns = [
			{headerName: '#', width: 40, maxWidth: 100, valueGetter: 'node.id', cellRenderer: 'loadingRenderer',pinned: 'left', cellStyle:{'text-align': 'center'}},
			{field: "ord_no", headerName: "주문번호", width: 130, pinned: "left", cellStyle:{'text-align': 'center'},
				cellRenderer: (params) => {
					let ord_no = params.data.ord_no;
					let ord_opt_no = params.data.ord_opt_no;
					return '<a href="javascript:void(0);" onclick="return openStoreOrder(\'' + ord_no + '\',\'' + ord_opt_no +'\');">'+ params.value +'</a>';
				}
			},
			{field: "ord_opt_no", headerName: "일련번호", width: 60, pinned: "left", cellStyle:{'text-align': 'center'},
				cellRenderer: (params) => {
					let ord_no = params.data.ord_no;
					let ord_opt_no = params.data.ord_opt_no;
					return '<a href="javascript:void(0);" onclick="return openStoreOrder(\'' + ord_no + '\',\'' + ord_opt_no +'\');">'+ params.value +'</a>';
				}
			},
			{field: "prd_cd", headerName: "바코드", width: 130, cellStyle:{'text-align': 'center'} },
			{field: "prd_cd_p", headerName: "품번", width: 100, cellStyle:{'text-align': 'center'}},
			{field: "qty", headerName: "수량", width: 50, cellStyle:{'text-align': 'center'}},
			{field: "dlv_location_nm", headerName: "거부매장/창고", width: 90, cellStyle:{'text-align': 'center'}},
			{field: "reject_yn", headerName: "출고거부여부", width: 90, cellStyle:{'text-align': 'center'}},
			{field: "reject_reason", headerName: "출고거부사유", width: 100, cellStyle:{'text-align': 'center'}},
			{field: "admin_id", headerName: "접수아이디", width: 80, cellStyle:{'text-align': 'center'}},
			{field: "admin_nm", headerName: "접수자", width: 80, cellStyle:{'text-align': 'center'}},
			{field: "rt", headerName: "거부일시", width: 120, cellStyle:{'text-align': 'center'}},
			{field: "ut", headerName: "수정일시", width: 120, cellStyle:{'text-align': 'center'}},
		];
	</script>
	<script>
		const pApp = new App('', { gridId: "#div-gd" });
		let gx;
	
		$(document).ready(async function() {
			pApp.ResizeGrid(275);
			pApp.BindSearchEnter();
			let gridDiv = document.querySelector(pApp.options.gridId);
			if (gridDiv !== null) {
				gx = new HDGrid(gridDiv, columns, {});
			}
			
			Search();
		});

		function Search() {
			let product_code = '{{ @$product_code }}';
			let data = $("form[name=search]").serialize();
			gx.Request('/store/order/ord02/search-reject-list/' + product_code , data);
		}
	</script>
@stop

@extends('shop_with.layouts.layout')
@section('title','기간별 Best/Worst 판매현황')
@section('content')
	<style>
		.ag-row-level-1 {
			background-color: #edf4fd !important;
		}

	</style>
	<div class="page_tit">
		<h3 class="d-inline-flex">기간별 Best/Worst 판매현황</h3>
		<div class="d-inline-flex location">
			<span class="home"></span>
			<span>/ 경영관리</span>
			<span>/ 기간별 Best/Worst 판매현황</span>
		</div>
	</div>
	<form method="get" name="search">
		<div id="search-area" class="search_cum_form">
			<div class="card mb-3">
				<div class="d-flex card-header justify-content-between">
					<h4>검색</h4>
					<div class="flax_box">
						<a href="#" id="search_sbtn" onclick="Search();" class="btn btn-sm btn-primary shadow-sm mr-1"><i class="fas fa-search fa-sm text-white-50"></i> 검색</a>
						<!-- 2023-05-25 검색조건 초기화 주석처리 -양대성- -->
						<!-- <a href="#" onclick="initSearch()" class="d-none search-area-ext d-sm-inline-block btn btn-sm btn-outline-primary mr-1 shadow-sm">검색조건 초기화</a> -->
						<div id="search-btn-collapse" class="btn-group mb-0 mb-sm-0"></div>
					</div>
				</div>
				<div class="card-body">
					<div class="row">
						<div class="col-lg-4">
							<div class="form-group">
								<label for="good_types">판매기간</label>
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
						<div class="col-lg-4">
							<div class="form-group">
								<label for="style_no">스타일넘버/온라인코드</label>
								<div class="form-inline">
									<div class="form-inline-inner input_box">
										<input type='text' class="form-control form-control-sm ac-style-no search-enter" name='style_no' id="style_no" value="{{ $style_no }}">
									</div>
									<span class="text_line">/</span>
									<div class="form-inline-inner input-box" style="width:47%">
										<div class="form-inline-inner inline_btn_box">
											<input type='text' class="form-control form-control-sm w-100 search-enter" name='goods_no' id='goods_no' value=''>
											<a href="#" class="btn btn-sm btn-outline-primary sch-goods_nos"><i class="bx bx-dots-horizontal-rounded fs-16"></i></a>
										</div>
									</div>
								</div>
							</div>
						</div>
						<div class="col-lg-4">
							<div class="form-group">
								<label for="form row-email-input">상품명</label>
								<div class="flex_box">
									<input type='text' class="form-control form-control-sm ac-goods-nm search-enter" name='goods_nm' value=''>
								</div>
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-lg-4">
							<div class="form-group">
								<label for="goods_nm_eng">상품명(영문)</label>
								<div class="flax_box">
									<input type='text' class="form-control form-control-sm ac-goods-nm-eng search-enter" name='goods_nm_eng' id="goods_nm_eng" value=''>
								</div>
							</div>
						</div>
						<div class="col-lg-4 inner-td">
							<div class="form-group">
								<label for="prd_cd">바코드</label>
								<div class="form-inline">
									<div class="form-inline-inner input-box w-100">
										<div class="form-inline inline_btn_box">
											<input type='text' id="prd_cd" name='prd_cd' class="form-control form-control-sm w-100 ac-style-no search-enter">
											<a href="#" class="btn btn-sm btn-outline-primary sch-prdcd"><i class="bx bx-dots-horizontal-rounded fs-16"></i></a>
										</div>
									</div>
								</div>
							</div>
						</div>
						<div class="col-lg-4 inner-td">
							<div class="form-group">
								<label for="prd_cd">상품검색조건</label>
								<div class="form-inline">
									<div class="form-inline-inner input-box w-100">
										<div class="form-inline inline_btn_box">
											<input type='hidden' id="prd_cd_range" name='prd_cd_range'>
											<input type='text' id="prd_cd_range_nm" name='prd_cd_range_nm' onclick="openApi();" class="form-control form-control-sm w-100 ac-style-no" readonly style="background-color: #fff;">
											<a href="#" class="btn btn-sm btn-outline-primary sch-prdcd-range"><i class="bx bx-dots-horizontal-rounded fs-16"></i></a>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-lg-4 inner-td">
							<div class="form-group">
								<label for="formrow-email-input">조회 기준</label>
								<div class="form-inline form-radio-box">
									<div class="custom-control custom-radio">
										<input type="radio" name="best_worst" value="B" id="best" class="custom-control-input" checked>
										<label class="custom-control-label" for="best">Best</label>
									</div>
									<div class="custom-control custom-radio">
										<input type="radio" name="best_worst" value="W" id="worst" class="custom-control-input">
										<label class="custom-control-label" for="worst">Worst</label>
									</div>
								</div>
							</div>
						</div>
						<div class="col-lg-4 inner-td">
							<div class="form-group">
								<label for="formrow-email-input">보기</label>
								<div class="form-inline form-radio-box">
									<div class="custom-control custom-radio">
										<input type="radio" name="group_type_condition" value="product_code_p" id="product_code_p" class="custom-control-input" checked>
										<label class="custom-control-label" for="product_code_p">품번</label>
									</div>
									<div class="custom-control custom-radio">
										<input type="radio" name="group_type_condition" value="color_and_size" id="color_and_size" class="custom-control-input">
										<label class="custom-control-label" for="color_and_size">컬러, 사이즈</label>
									</div>
								</div>
							</div>
						</div>
						<div class="col-lg-4 inner-td">
							<div class="form-group">
								<label for="">순위/구분</label>
								<div class="form-inline">
									<div class="form-inline-inner input_box" style="width:24%;">
										<select name="limit" class="form-control form-control-sm">
											<option value="10">10위</option>
											<option value="20">20위</option>
											<option value="30">30위</option>
											<option value="50">50위</option>
											<option value="100">100위</option>
											<option value="200">200위</option>
											<option value="300">300위</option>
											<option value="400">400위</option>
											<option value="500">500위</option>
											<option value="1000">1000위</option>
										</select>
									</div>
									<span class="text_line">/</span>
									<div class="form-inline-inner input_box" style="width:45%;">
										<select name="ord_field" class="form-control form-control-sm">
											<option value='ord_qty'>수량</option>
											{{--<option value='sale_rate'>기간판매율</option>--}}
											<option value='ord_amt'>기간판매금액</option>
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
				<a href="#" id="search_sbtn" onclick="Search();" class="btn btn-sm btn-primary shadow-sm mr-1"><i class="fas fa-search fa-sm text-white-50"></i> 검색</a>
				<!-- 2023-05-25 검색조건 초기화 주석처리 -양대성- -->
				<!-- <input type="reset" id="search_reset" value="검색조건 초기화" class="btn btn-sm btn-outline-primary shadow-sm" onclick="initSearch()"> -->
				<div class="search_mode_wrap btn-group mr-2 mb-0 mb-sm-0"></div>
			</div>
		</div>
	</form>
	<!-- DataTales Example -->
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
	<script language="javascript">

		const pinnedRowData = [{
			goods_no : '합계'
			, "total_ord_qty" : 0
			, "total_ord_amt" : 0
			, "ord_qty" : 0
			, "ord_amt" : 0
			, "store_wqty" : 0
		}];

		var columns = [
			{headerName: '#', width:40, pinned: 'left', valueGetter: 'node.id', cellRenderer: 'loadingRenderer', cellStyle: StyleLineHeight,
				cellRenderer: (params) => params.node.rowPinned === 'top' ? '' : parseInt(params.value) + 1,
			},
			{field: "prd_cd", headerName: "바코드", width: 120, pinned: "left", cellStyle: StyleLineHeight},
			{
				field: "goods_no",
				headerName: "온라인코드",
				width: 70,
				pinned: 'left',
				cellStyle: StyleLineHeight,
				cellRenderer: function (params) {
					if(params.node.rowPinned === 'top') {
						return '합계'
					} else {
						if (params.value) {
							return `<a href="{{config('shop.front_url')}}/app/product/detail/${params.value}" target="_blank">${params.value}</a>`
						}
					}
				}
			},
			{field: "brand_nm", headerName: "브랜드", cellStyle: StyleLineHeight, width: 70},
			{field: "style_no", headerName: "스타일넘버", width: 70, cellStyle: StyleLineHeight},
			{field: "img", headerName: "이미지", type: 'GoodsImageType', width: 50, cellStyle: {"line-height": "30px"}, surl:"{{config('shop.front_url')}}"},
			{field: "img", headerName: "이미지_url", hide: true},
			{field: "goods_nm", headerName: "상품명", cellStyle: {"line-height": "30px"}, width: 300,
				cellRenderer: function (params) {
					if (params.data?.goods_no == '' || params.node.aggData?.goods_no == '') {
						return '<a href="javascript:void(0);" onclick="return blank_goods_no();">' + (params.value || '') + '</a>';
					} else {
						let goods_no = params.data ? params.data.goods_no : params.node.aggData ? params.node.aggData.goods_no : '';
						return '<a href="#" onclick="return openHeadProduct(\'' + goods_no + '\');">' + (params.value || '') + '</a>';
					}
				}
			},
			{field: "goods_nm_eng", headerName: "상품명(영문)", width: 300, cellStyle: {"line-height": "30px"}},
			{field: "prd_cd_p", headerName: "품번", cellStyle: StyleLineHeight, width: 90},
			{field: "color", headerName: "컬러", cellStyle: StyleLineHeight, width: 55},
			{field: "size", headerName: "사이즈", cellStyle: StyleLineHeight, width: 55},
			{field: "goods_opt", headerName: "옵션", cellStyle: {"line-height": "30px"}, width: 130},
			{field: "total_sale", headerName: "총판매",
				children: [
					{headerName: "수량", field: "total_ord_qty", type: 'numberType', width: 60},
					{headerName: "금액", field: "total_ord_amt", type: 'currencyMinusColorType', width: 100},
				]
			},
			{field: "sale",	headerName: "기간판매",
				children: [
					{headerName: "수량", field: "ord_qty", type: 'numberType', width: 60},
					{headerName: "금액", field: "ord_amt", type: 'currencyMinusColorType', width: 70},
				]
			},
			{field: "store_wqty", headerName: "매장재고", type: 'numberType', width: 60},
			{width: "auto"}
		];
	</script>
	<script type="text/javascript" charset="utf-8">

		const pApp = new App('',{
			gridId:"#div-gd",
		});
		let gx;

		$(document).ready(function() {
			pApp.ResizeGrid(265);
			pApp.BindSearchEnter();
			let gridDiv = document.querySelector(pApp.options.gridId);

			gx = new HDGrid(gridDiv, columns, {
				pinnedTopRowData: pinnedRowData,
				getRowStyle: (params) => {
					if (params.node.rowPinned)  return {'font-weight': 'bold', 'background': '#eee !important', 'border': 'none'};
				},
			});

			gx.gridOptions.defaultColDef = {
				suppressMenu: true,
				resizable: true,
				sortable: true,
			};
			Search();
		});
		async function Search() {
			await setColumn();
			let data = $('form[name="search"]').serialize();
			gx.Request('/shop/sale/sal03/search', data,-1, function(e) {
				let pinnedRow = gx.gridOptions.api.getPinnedTopRow(0);
				let total_data = e.head.total_data;

				if(pinnedRow && total_data != '') {
					gx.gridOptions.api.setPinnedTopRowData([
						{ ...pinnedRow.data, ...total_data }
					]);
				}
			});
		}

		function setColumn() {
			let view  = $("input[name='group_type_condition']:checked").val();
			if (view === 'product_code_p') {
				let prd_columns = columns.map(c => c.field === "prd_cd_p"
					? ({...c, pinned: "left"})
					: c.headerName === "바코드" ? ({...c, hide: true})
						: c.headerName === "온라인코드" ? ({...c, hide: true})
							: c.headerName === "컬러" ? ({...c, hide: true})
								: c.headerName === "사이즈" ? ({...c, hide: true})
									: c.headerName === "옵션" ? ({...c, hide: true})
										: c.field === "goods_no" ? ({...c, pinned: "auto"}) : c);
				gx.gridOptions.api.setColumnDefs(prd_columns);
			} else {
				let prd_columns = columns.map(c => c.field === "prd_cd_p"
					? ({...c, pinned: "auto"})
					: c.headerName === "바코드" ? ({...c, hide: false})
						: c.headerName === "온라인코드" ? ({...c, hide: false})
							: c.headerName === "컬러" ? ({...c, hide: false})
								: c.headerName === "사이즈" ? ({...c, hide: false})
									: c.headerName === "옵션" ? ({...c, hide: false})
										: c.field === "goods_no" ? ({...c, cellStyle: StyleGoodsNo}) : c);
				gx.gridOptions.api.setColumnDefs(prd_columns);
			}
		}

		//상품범위검색 input창 클릭시 자동으로 상품옵션 범위검색 API 오픈
		function openApi() {
			document.getElementsByClassName('sch-prdcd-range')[0].click();
		}

		function blank_goods_no() {
			alert('온라인코드가 비어있는 상품입니다.');
		}
	</script>
@stop

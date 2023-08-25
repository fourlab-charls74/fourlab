@extends('head_with.layouts.layout')
@section('title','브랜드')
@section('content')
	<script type="text/javascript" src="/handle/editor/editor.js"></script>
	<script type="text/javascript" src="/handle/editor/summernote/summernote-lite.min.js"></script>
	<script type="text/javascript" src="/handle/editor/summernote/lang/summernote-ko-KR.js"></script>

	<style>
		center img {
			margin: 9px;
			width: 80px;
			height: 80px;
		}
	</style>

	<style>
		.select2.select2-container .select2-selection {
			border: 1px solid rgb(210, 210, 210);
		}
		::placeholder {
			font-size: 13px;
			font-family: "Montserrat","Noto Sans KR",'mg', Dotum,"돋움",Helvetica,AppleSDGothicNeo,sans-serif;
			font-weight: 300;
			padding: 0px 2px 1px;
			color: black;
		}
	</style>
	<script>
		//멀티 셀렉트 박스2
		$(document).ready(function() {
			$('.multi_select').select2({
				placeholder :'전체',
				multiple: true,
				width : "50%",
				closeOnSelect: false,
			});
		});
	</script>

	<div class="page_tit">
		<h3 class="d-inline-flex">재고현황</h3>
		<div class="d-inline-flex location">
			<span class="home"></span>
			<span>/ 재고</span>
			<span>/ 재고현황</span>
		</div>
	</div>

	<form method="get" name="search">
		<div id="search-area" class="search_cum_form">
			<div class="card mb-3">
				<div class="d-flex card-header justify-content-between">
					<h4>검색</h4>
					<div>
						<a href="#" id="search_sbtn" class="btn btn-sm btn-primary shadow-sm pl-2"><i class="fas fa-search fa-sm text-white-50"></i> 조회</a>
						<div id="search-btn-collapse" class="btn-group mb-0 mb-sm-0"></div>
					</div>
				</div>
				<div class="card-body">
					<div class="row">
						<div class="col-lg-4 inner-td">
							<div class="form-group">
								<label for="item">품목</label>
								<div class="flax_box">
									<select id="item" name="item" class="form-control form-control-sm">
										<option value="">전체</option>
										@foreach ($items as $item)
											<option value="{{ $item->cd }}">{{ $item->val }}</option>
										@endforeach
									</select>
								</div>
							</div>
						</div>
						<div class="col-lg-4 inner-td">
							<div class="form-group">
								<label for="brand_cd">브랜드</label>
								<div class="form-inline inline_btn_box">
									<input type="hidden" class="form-control form-control-sm search-all sch-brand" name="brand" id="brand_nm" value="" style="width:100%;">
									<select id="brand_cd" name="brand_cd" class="form-control form-control-sm select2-brand"></select>
									<a href="#" class="btn btn-sm btn-outline-primary sch-brand"><i class="bx bx-dots-horizontal-rounded fs-16"></i></a>
								</div>
							</div>
						</div>
						<div class="col-lg-4 inner-td">
							<div class="form-group">
								<label for="com_type">업체</label>
								<div class="form-inline inline_select_box">
									<div class="form-inline-inner select-box">
										<select name="com_type" id="com_type" class="form-control form-control-sm">
											<option value="">전체</option>
											@foreach($com_types as $com_type)
												<option value="{{ $com_type->code_id }}">{{ $com_type->code_val }}</option>
											@endforeach
										</select>
									</div>
									<div class="form-inline-inner input-box">
										<div class="form-inline inline_btn_box">
											<input type="hidden" id="com_nm" name="com_nm" class="form-control form-control-sm ac-company sch-company" style="width:100%;">
											<select id="com_id" name="com_id" class="form-control form-control-sm select2-company" style="width:100%;"></select>
											<a href="#" class="btn btn-sm btn-outline-primary sch-company"><i class="bx bx-dots-horizontal-rounded fs-16"></i></a>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-lg-4 inner-td">
							<div class="form-group">
								<label for="goods_stat">상품상태 / 상품명</label>
								<div class="d-flex justify-content-between align-items-center">
									<select id="goods_stat" name='goods_stat[]' class="form-control form-control-sm multi_select w-50" multiple>
										<option value=''>전체</option>
										@foreach ($goods_stats as $goods_stat)
											<option value='{{ $goods_stat->code_id }}'>{{ $goods_stat->code_val }}</option>
										@endforeach
									</select>
									<span> / </span>
									<div class="flax_box">
										<input type='text' class="form-control form-control-sm ac-goods-nm search-enter" name='goods_nm' id="goods_nm" value=''>
									</div>
								</div>
							</div>
						</div>
						<div class="col-lg-4 inner-td">
							<div class="form-group">
								<label for="style_no">스타일넘버/상품코드</label>
								<div class="form-inline">
									<div class="form-inline-inner input_box">
										<input type='text' class="form-control form-control-sm ac-style-no search-enter" name='style_no' id="style_no">
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
						<div class="col-lg-4 inner-td">
							<div class="form-group">
								<label for="">자료수/정렬</label>
								<div class="form-inline">
									<div class="form-inline-inner input_box" style="width:24%;">
										<select name="limit" class="form-control form-control-sm">
											<option value="100">100</option>
											<option value="500">500</option>
											<option value="1000">1000</option>
											<option value="2000">2000</option>
											<option value="-1">모두</option>
										</select>
									</div>
									<span class="text_line">/</span>
									<div class="form-inline-inner input_box" style="width:45%;">
										<select name="ord_field" class="form-control form-control-sm">
											<option value="goods_no">상품번호</option>
											<option value="goods_nm">상품명</option>
											<option value="wonga">원가</option>
											<option value="com_nm">공급업체</option>
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
		</div>
	</form>
	<div class="show_layout">
		<form name="f1" id="f1" method="POST" enctype="multipart/form-data">

			<input type="hidden" name="c">
			<input type="hidden" name="cmd">

			<div class="row">
				<div class="col-lg-4">
					<div class="card_wrap">
						<div class="card">
							<div class="card-title mb-3">
								<div class="filter_wrap">
									<div class="fl_box">
										<h6 class="m-0 font-weight-bold">총 <span id="gd-total" class="text-primary">0</span> 건</h6>
									</div>
									<div class="fr_box">
										<button type="button" class="setting-grid-col ml-2"><i class="fas fa-cog text-primary"></i></button>
									</div>
								</div>
							</div>
							<div class="card-body">
								&nbsp;&nbsp;
								<input type="hidden" id="chart-type" value="date">
								<ul class="nav nav-tabs" id="type_tab" role="tablist">
									<li class="nav-item" role="presentation">
										<a class="nav-link active" id="itemnbrand-tab" data-toggle="tab" href="#home" role="tab" aria-controls="itemnbrand" aria-selected="true">품목 / 브랜드</a>
									</li>
									<li class="nav-item" role="presentation">
										<a class="nav-link" id="company-tab" data-toggle="tab" href="#contact" role="tab" aria-controls="company" aria-selected="false">업체별</a>
									</li>
								</ul>
								<div class="row">
									<div class="col-12">
										<div class="table-responsive" id = "itemnbrand_grid">
											<div id="div-gd" class="ag-theme-balham"></div>
										</div>
										<div class="table-responsive" id = "company_grid" style="display: none;">
											<div id="div-gd3" class="ag-theme-balham"></div>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="col-lg-8 pl-1">
					<div class="card_wrap">
						<div class="card">
							<div class="card-title mb-3">
								<div class="filter_wrap">
									<div class="fl_box">
										<h6 class="m-0 font-weight-bold">총 <span id="gd2-total" class="text-primary">0</span> 건</h6>
									</div>
									<div class="fr_box">
										<button type="button" class="setting-grid-col-2 ml-2"><i class="fas fa-cog text-primary"></i></button>
									</div>
								</div>
							</div>
							<div class="card-body pt-3">
								<div class="row">
									<div class="col-12">
										<div class="table-responsive">
											<div id="div-gd2" class="ag-theme-balham"></div>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</form>
		<script language="javascript">
			let company_columns = [
				{
					field: "com_id",
					headerName: "업체아이디",
					hide: true
				},
				{
					field: "com_nm",
					headerName: "업체",
					width: 100,
					cellClass: 'hd-grid-code',
					cellRenderer: function(params) {
						return '<a href="#" onclick="return SearchGoodsByCom(\'' + params.data.com_id + '\');">' + params.value + '</a>';
					}
				},
				{
					field: "com_type_nm",
					headerName: "업체구분",
					width: 100,
				},
				{
					field: "goods_cnt",
					headerName: "상품수",
					type: 'numberType',
					width: 50,
				},
				{
					field: "qty",
					headerName: "재고수",
					width: 50,
					type: 'numberType'
				},
				{
					field: "wqty",
					headerName: "보유재고수",
					width: 100,
					type: 'numberType'
				},
				{
					field: "t_wonga",
					headerName: "총원가",
					width: 100,
					type: 'numberType'
				}
			];

			let itemnbrand_columns = [
				{
					field: "opt_kind_nm",
					headerName: "품목",
					rowGroup: true,
					hide: true,
					pinned: 'left',
					cellRenderer: function(params) {
						if (params.data !== undefined) {
							return '<a href="#" onclick="return SearchGoodsByOpt(\'' + params.data.opt_kind_cd + '\');">' + params.value + '</a>';
						} else {
							return '<a href="#" onclick="return SearchGoodsByOpt(\'' + params.node.allLeafChildren[0].data.opt_kind_cd + '\');">' + params.value + '</a>';
						}
					},
				},
				{
					field: "brand",
					headerName: "브랜드아이디",
					hide: true
				},
				{
					field: "brand_nm",
					headerName: "브랜드",
					cellClass: 'hd-grid-code',
					width: 100,
					aggFunc: params => {
						return '';
					},
					cellRenderer: function(params) {
						if (params.data !== undefined) {
							return '<a href="#" onclick="return SearchGoodsByBrand(\'' + params.data.brand + '\', \'' + params.data.opt_kind_cd + '\');">' + params.value + '</a>';
						} else {
							return '<a href="#" onclick="return SearchGoodsByBrand(\'' + params.node.allLeafChildren[0].data.brand + '\', \'' + params.node.allLeafChildren[0].data.opt_kind_cd + '\' );">' + params.value + '</a>';
						}
					},
				},
				{
					field: "goods_cnt",
					headerName: "상품수",
					cellClass: 'hd-grid-code',
					type: 'numberType',
					width: 50,
					aggFunc: params => {
						let total = 0;
						params.values.forEach(value => total += Number(value));
						return total;
					}
				},
				{
					field: "t_qty",
					headerName: "재고수",
					cellClass: 'hd-grid-code',
					width: 50,
					type: 'numberType',
					aggFunc: params => {
						let total = 0;
						params.values.forEach(value => total += Number(value));
						return total;
					}
				},
				{
					field: "t_wqty",
					headerName: "보유재고수",
					cellClass: 'hd-grid-code',
					width: 100,
					type: 'numberType',
					aggFunc: params => {
						let total = 0;
						params.values.forEach(value => total += Number(value));
						return total;
					}
				},
				{
					field: "t_wonga",
					headerName: "총원가",
					cellClass: 'hd-grid-code',
					width: 100,
					type: 'numberType',
					aggFunc: params => {
						let total = 0;
						params.values.forEach(value => total += Number(value));
						return total;
					}
				}
			];

			let stock_info_by_com_colums = [
				{
					field: "com_nm",
					headerName: "업체",
					width: 100,
				},
				{
					field: "opt_kind_nm",
					headerName: "품목",
					width: 100,
				},
				{
					field: "brand_nm",
					headerName: "브랜드",
					width: 100,
				},
				{
					field: "style_no",
					headerName: "스타일넘버",
					width: 120,
				},
				{
					field: "goods_type_nm",
					headerName: "상품구분",
					width: 100,
				},
				{
					field: "is_unlimited_nm",
					headerName: "재고구분",
					width: 100,
				},
				{field:"goods_code", headerName:"상품코드", width:120,
					children : [
						{
							headerName : "NO",
							field : "goods_no"
						},
						{
							headerName : "SUB",
							field : "goods_sub"
						},
					]
				},
				{
					field: "goods_nm",
					headerName: "상품명",
					type: 'HeadGoodsNameType',
					width: 200,
				},
				{
					field: "sale_stat_cl_nm",
					headerName: "상태",
					width: 100,
				},
				{
					field: "wonga",
					headerName: "원가",
					width: 100,
				},
				{
					field: "goods_opt",
					headerName: "옵션",
					width: 100,
				},
				{field:"qty", headerName:"재고수", width:120,
					children : [
						{
							headerName : "온라인",
							field : "good_qty"
						},
						{
							headerName : "보유",
							field : "wqty"
						},
					]
				},
				{field:"warehouse_loc", headerName:"창고위치", width:120,
					children : [
						{
							headerName : "NO",
							field : "wqty_LOC",
							width: 120,
						},
					]
				},
				{
					field: "goods_location",
					headerName: "상품위치",
					width: 100,
				},
			];

		</script>
		<script type="text/javascript" charset="utf-8">
			const pApp = new App('', { gridId: "#div-gd", height: 265});
			const pApp3 = new App('', { gridId: "#div-gd3", height: 265});
			let gx;
			let gx2;
			let gx3;
			const pinnedRowData = [{'brand_nm': '합계'}];

			const basic_autoGroupColumnDef = (headerName, width = 100) => ({
				headerName: headerName,
				cellRenderer: 'agGroupCellRenderer'
			});

			$(document).ready(function() {
				let gridDiv = document.querySelector(pApp.options.gridId);
				let gridDiv3 = document.querySelector(pApp3.options.gridId);

				pApp.ResizeGrid(265);
				pApp.BindSearchEnter();
				//
				pApp3.ResizeGrid(265);
				pApp3.BindSearchEnter();

				gx = new HDGrid(gridDiv, itemnbrand_columns, {
					rollup: true,
					pinnedTopRowData: pinnedRowData,
					autoGroupColumnDef: basic_autoGroupColumnDef('품목'),
					groupDefaultExpanded: 0, // 0: close, 1: open
					suppressAggFuncInHeader: true,
					animateRows: true,
					suppressDragLeaveHidesColumns: true,
					suppressMakeColumnVisibleAfterUnGroup: true,
				});

				SearchItemnBrand();
				
				/*let url_path_array = String(window.location.href).split('?')[0].split('/');
				let pid = filter_pid(String(url_path_array[url_path_array.length - 1]).toLocaleUpperCase());
				pid = pid + '_1';

				get_indiv_columns(pid, itemnbrand_columns, function(data) {

					gx = new HDGrid(gridDiv, data, {
						rollup: true,
						pinnedTopRowData: pinnedRowData,
						autoGroupColumnDef: basic_autoGroupColumnDef('품목'),
						groupDefaultExpanded: 0, // 0: close, 1: open
						suppressAggFuncInHeader: true,
						animateRows: true,
						suppressDragLeaveHidesColumns: true,
						suppressMakeColumnVisibleAfterUnGroup: true,
					});

					setMyGridHeader.Init(gx,
						indiv_grid_save.bind(this, pid, gx),
						indiv_grid_init.bind(this, pid),
						'setting-grid-col'
					);

					SearchItemnBrand();
				});*/

				gx3 = new HDGrid(gridDiv3, company_columns);
				SearchCompany();
				
				/*let pid2 = filter_pid(String(url_path_array[url_path_array.length - 1]).toLocaleUpperCase());
				pid2 = pid2 + '_2';

				get_indiv_columns(pid2, company_columns, function(data) {
					gx3 = new HDGrid(gridDiv3, data);

					setMyGridHeaderSec.Init(gx3,
						indiv_grid_save.bind(this, pid2, gx3),
						indiv_grid_init.bind(this, pid2),
						'setting-grid-col-1'
					);

					SearchCompany();
				});*/

				function SearchItemnBrand() {
					let req_data = $('form[name="search"]').serialize();
					gx.Request('/head/stock/stk36/item-n-brand/search', req_data, -1, function(data) {
						gx.gridOptions.api.setPinnedTopRowData([{
							...data.head.total_row
						}]);
					});
				}

				function SearchCompany() {
					let req_data = $('form[name="search"]').serialize();
					gx3.Request('/head/stock/stk36/company/search', req_data, -1, null);
				}

				$("#itemnbrand-tab").click(function() {
					$('#itemnbrand_grid').show();
					$('#company_grid').hide();

					SearchItemnBrand();
				});

				$("#company-tab").click(function() {
					$('#company_grid').show();
					$('#itemnbrand_grid').hide();

					SearchCompany();
				});

				$('#search_sbtn').click(function(e) {
					if($('#itemnbrand-tab').attr("aria-selected") === "true") {
						SearchItemnBrand();
					} else {
						SearchCompany();
					}
				});
			});

			function SearchGoodsByCom(com_id) {
				let data = $('form[name="search"]').serialize();
				data +=`&com_id=${com_id}`;

				gx2.Request('/head/stock/stk36/goods-by-com/search', data, 1);
			}

			function SearchGoodsByBrand(brand, opt_kind_cd) {
				let data = $('form[name="search"]').serialize();
				data +=`&brand_cd=${brand}&opt_kind_cd=${opt_kind_cd}`;

				gx2.Request('/head/stock/stk36/goods-by-com/search', data, 1);
			}

			function SearchGoodsByOpt(opt_kind_cd) {
				let data = $('form[name="search"]').serialize();
				data +=`&opt_kind_cd=${opt_kind_cd}`;

				gx2.Request('/head/stock/stk36/goods-by-com/search', data, 1);
			}
		</script>
		<script>
			const pApp2 = new App('', { gridId: "#div-gd2", height: 265});
			$(document).ready(function() {
				pApp2.ResizeGrid(265);
				pApp2.BindSearchEnter();

				let gridDiv2 = document.querySelector(pApp2.options.gridId);

				gx2 = new HDGrid(gridDiv2, stock_info_by_com_colums);
				
				/*let url_path_array2 = String(window.location.href).split('?')[0].split('/');
				let pid3 = filter_pid(String(url_path_array2[url_path_array2.length - 1]).toLocaleUpperCase());
				pid3 = pid3 + '_3';

				get_indiv_columns(pid3, stock_info_by_com_colums, function(data) {

					gx2 = new HDGrid(gridDiv2, data);

					setMyGridHeaderThir.Init(gx2,
						indiv_grid_save.bind(this, pid3, gx2),
						indiv_grid_init.bind(this, pid3),
						'setting-grid-col-2'
					);
				});*/
			});
		</script>
		<link rel="stylesheet" href="/handle/editor/summernote/summernote-lite.min.css">
		<link rel="stylesheet" href="/handle/editor/summernote/plugin/summernote-ext-ssm-emoji/summernote-ext-ssm-emoji.css?v=2020081821">
@stop

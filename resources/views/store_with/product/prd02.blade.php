@extends('store_with.layouts.layout')
@section('title','상품')
@section('content')
<style>
.ag-row-level-1 {
		background-color: #edf4fd !important;
	}

</style>

	<div class="page_tit">
		<h3 class="d-inline-flex">상품코드등록</h3>
		<div class="d-inline-flex location">
			<span class="home"></span>
			<span>/ 상품관리</span>
		</div>
	</div>
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
				width : "100%",
				closeOnSelect: false,
			});
		});
	</script>
	<!--div class="d-flex align-items-center justify-content-between mb-2">
			<h1 class="h3 mb-0 text-gray-800">상품</h1>
			<div>
				<a href="#" id="search_sbtn" onclick="return Search();" class="btn btn-sm btn-primary shadow-sm"><i class="fas fa-search fa-sm text-white-50"></i> 검색</a>
				<a href="#" onclick="AddProduct();" class="btn btn-sm btn-primary shadow-sm">상품추가</a>
				<a href="#" onclick="gx.Download();" class="btn btn-sm btn-primary shadow-sm">다운로드</a>
				<div id="search-btn-collapse" class="btn-group mr-2 mb-0 mb-sm-0"></div>
			</div>
		</div-->
	<form method="get" name="search" id="search">
		@csrf
		<input type='hidden' name='goods_nos' value=''>
		<div id="search-area" class="search_cum_form">
			<div class="card mb-3">
				<div class="d-flex card-header justify-content-between">
					<h4>검색</h4>
					<div>
						<a href="#" id="search_sbtn" onclick="return Search();" class="btn btn-sm btn-primary shadow-sm pl-2"><i class="fas fa-search fa-sm text-white-50"></i> 조회</a>
						<a href="#" onclick="AddProduct_upload();" class="btn btn-sm btn-outline-primary shadow-sm pl-2"><i class="bx bx-plus fs-16"></i> 바코드 등록</a>
						<a href="#" onclick="AddProduct();" class="btn btn-sm btn-outline-primary shadow-sm pl-2"><i class="bx bx-plus fs-16"></i> 상품 매칭</a>
						<a href="#" onclick="AddProducts();" class="btn btn-sm btn-outline-primary shadow-sm pl-2"><i class="bx fs-16"></i> 상품일괄매칭</a>
						<a href="#" onclick="initSearchInputs()" class="btn btn-sm btn-outline-primary mr-1">검색조건 초기화</a>
						<a href="#" onclick="gx.Download();" class="btn btn-sm btn-outline-primary shadow-sm pl-2"><i class="bx bx-download fs-16"></i> 엑셀다운로드</a>
						<div id="search-btn-collapse" class="btn-group mb-0 mb-sm-0"></div>
					</div>
				</div>
				<div class="card-body">
					<div class="row">
						<div class="col-lg-4 inner-td">
							<div class="form-group">
								<label>바코드</label>
								<div class="flex_box">
									<input type='text' id="prd_cd" name='prd_cd' class="form-control form-control-sm ac-style-no search-enter">
									<a href="#" class="btn btn-sm btn-outline-primary sch-prdcd" hidden><i class="bx bx-dots-horizontal-rounded fs-16"></i></a>
								</div>
							</div>
						</div>
						<div class="col-lg-4 inner-td">
							<div class="form-group">
								<label>품번</label>
								<div class="flex_box">
									<input type='text' id="prd_cd_p" name='prd_cd_p' class="form-control form-control-sm ac-style-no search-enter">
								</div>
							</div>
						</div>
						<div class="col-lg-4 inner-td">
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
					</div>
					<div class="row">
						<div class="col-lg-4 inner-td">
							<div class="form-group">
								<label for="goods_nm">상품명</label>
								<div class="flex_box">
									<input type='text' class="form-control form-control-sm ac-goods-nm search-enter" name='goods_nm' id="goods_nm" value=''>
								</div>
							</div>
						</div>
						<div class="col-lg-4 inner-td">
							<div class="form-group">
								<label for="goods_nm_eng">상품명(영문)</label>
								<div class="flex_box">
									<input type='text' class="form-control form-control-sm ac-goods-nm-eng search-enter" name='goods_nm_eng' id="goods_nm_eng" value=''>
								</div>
							</div>
						</div>
						<div class="col-lg-4 inner-td">
							<div class="form-group">
								<label for="goods_stat">전시상태</label>
								<div class="flex_box">
									<select name="goods_stat[]" class="form-control form-control-sm multi_select w-100" multiple>
										<option value=''>전체</option>
										@foreach ($goods_stats as $goods_stat)
											<option value='{{ $goods_stat->code_id }}'>{{ $goods_stat->code_val }}</option>
										@endforeach
									</select>
								</div>
							</div>
						</div>
					</div>
					<div class="row">
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
						<div class="col-lg-4 inner-td">
							<div class="form-group">
								<label for="">자료수/정렬</label>
								<div class="form-inline">
									<div class="form-inline-inner input_box" style="width:24%;">
										<select name="limit" class="form-control form-control-sm">
											<option value="1000">1000</option>
											<option value="5000">5000</option>
											<option value="10000">10000</option>
										</select>
									</div>
									<span class="text_line">/</span>
									<div class="form-inline-inner input_box" style="width:45%;">
										<select name="ord_field" class="form-control form-control-sm">
											<option value="prd_cd1">등록일(품번별)</option>
											<option value="pc.rt">등록일</option>
											<option value="pc.ut">수정일</option>
											<option value="g.goods_no">온라인코드</option>
											<option value="g.goods_nm">상품명</option>
											<option value="pc.prd_cd">바코드</option>
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
					<div class="row search-area-ext d-none">
						<div class="col-lg-4 inner-td">
							<div class="form-group">
								<label for="store_type">매장구분</label>
								<div class="flex_box">
									<select name='store_type' class="form-control form-control-sm">
										<option value=''>전체</option>
										@foreach ($store_types as $store_type)
											<option value='{{ $store_type->code_id }}'>{{ $store_type->code_val }}</option>
										@endforeach
									</select>
								</div>
							</div>
						</div>
						<div class="col-lg-4 inner-td">
							<div class="form-group">
								<label>매장명</label>
								<div class="form-inline inline_btn_box">
									<input type='hidden' id="store_nm" name="store_nm">
									<select id="store_no" name="store_no[]" class="form-control form-control-sm select2-store multi_select" multiple></select>
									<a href="javascript:void(0);" class="btn btn-sm btn-outline-primary sch-store"><i class="bx bx-dots-horizontal-rounded fs-16"></i></a>
								</div>
							</div>
						</div>
						<div class="col-lg-4 inner-td">
							<div class="form-group">
							<label for="formrow-email-input">매칭여부</label>
								<div class="form-inline form-radio-box">
									<div class="custom-control custom-radio">
										<input type="radio" name="match_yn1" value="A" id="match_all1" class="custom-control-input" checked>
										<label class="custom-control-label" for="match_all1">전체</label>
									</div>
									<div class="custom-control custom-radio">
										<input type="radio" name="match_yn1" value="Y" id="match_y1" class="custom-control-input">
										<label class="custom-control-label" for="match_y1">Y</label>
									</div>
									<div class="custom-control custom-radio">
										<input type="radio" name="match_yn1" value="N" id="match_n1" class="custom-control-input">
										<label class="custom-control-label" for="match_n1">N</label>
									</div>
								</div>
							</div>
						</div>
					</div>
					<div class="row search-area-ext d-none">
						<div class="col-lg-4 inner-td">
							<div class="form-group">
								<label for="brand_cd">브랜드</label>
								<div class="form-inline inline_btn_box">
									<select id="brand_cd" name="brand_cd" class="form-control form-control-sm select2-brand"></select>
									<a href="#" class="btn btn-sm btn-outline-primary sch-brand"><i class="bx bx-dots-horizontal-rounded fs-16"></i></a>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="resul_btn_wrap mb-3">
				<a href="#" id="search_sbtn" onclick="return Search();" class="btn btn-sm btn-primary shadow-sm pl-2"><i class="fas fa-search fa-sm text-white-50"></i> 조회</a>
				<a href="#" onclick="AddProduct_upload();" class="btn btn-sm btn-outline-primary shadow-sm pl-2"><i class="bx bx-plus fs-16"></i> 바코드 등록</a>
				<a href="#" onclick="AddProduct();" class="btn btn-sm btn-outline-primary shadow-sm pl-2"><i class="bx bx-plus fs-16"></i> 상품 매칭</a>
				<a href="#" onclick="AddProducts();" class="btn btn-sm btn-outline-primary shadow-sm pl-2"><i class="bx fs-16"></i> 상품일괄매칭</a>
				<a href="#" onclick="initSearchInputs()" class="btn btn-sm btn-outline-primary mr-1">검색조건 초기화</a>
				<a href="#" onclick="gx.Download();" class="btn btn-sm btn-outline-primary shadow-sm pl-2"><i class="bx bx-download fs-16"></i> 엑셀다운로드</a>
			<div class="search_mode_wrap btn-group mr-2 mb-0 mb-sm-0"></div>
		</div>
	</div>
</form>
	<!-- DataTales Example -->
	<form method="post" name="save" action="/head/stock/stk01">
		@csrf
		<div id="filter-area" class="card shadow-none mb-0 ty2 last-card">
			<div class="card-body">
				<div class="card-title mb-3">
					<div class="filter_wrap">
						<div class="fl_box">
							<h6 class="m-0 font-weight-bold">총 <span id="gd-total" class="text-primary">0</span> 건</h6>
						</div>
						<div class="fr_box flex_box">
						<!--
							<span style="font-weight:500;line-height:30px;margin-left:5px;vertical-align:middle;" class="mr-1">선택한 바코드를 온라인코드</span>
							<div>
								<input type="text" id="goods_no" class="form-control form-control-sm" name="goods_no" value="">
							</div>
							<span style="font-weight:500;line-height:30px;margin-left:5px;vertical-align:middle;" class="mr-1">로</span>
							<a href="#" onclick="Save();" class="btn btn-sm btn-primary shadow-sm pl-2"><i class="fas fa-sm text-white-50"></i>매칭</a>
						//-->
							<div class="fr_box">
								<div class="custom-control custom-checkbox form-check-box pr-2" style="display:inline-block;">
									<input type="checkbox" class="custom-control-input" name="ext_storage_qty" id="ext_storage_qty" value="Y">
									<label class="custom-control-label font-weight-normal" for="ext_storage_qty">창고재고 0 제외</label>
								</div>
								<div class="custom-control custom-checkbox form-check-box pr-2" style="display:inline-block;">
									<input type="checkbox" class="custom-control-input" name="grid_expand" id="grid_expand" onchange="return setAllRowGroupExpanded(this.checked);">
									<label class="custom-control-label font-weight-normal" for="grid_expand">항목펼쳐보기</label>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="table-responsive">
					<div id="div-gd" style="min-height:300px;height:calc(100vh - 370px);width:100%;" class="ag-theme-balham gd-lh50 ty2"></div>
				</div>
			</div>
		</div>
	</form>
	<style>
		/* 전시카테고리 상품 이미지 사이즈 픽스 */
		.img {
			height:30px;
		}
	</style>
	<script language="javascript">

		// const pinnedRowData = [{ goods_nm: '' , goods_sh: 0, price: 0, wonga: 0 , margin_amt : 0, wqty : 0, sqty : 0}];

		const columns = [
			{headerName: '#', pinned: 'left', type: 'NumType', width:40, cellStyle: StyleLineHeight},
			
			{field: "prd_cd", headerName: "바코드", width:120, pinned: 'left', cellStyle: StyleLineHeight,
				cellRenderer: function(params) {
					if (params.value !== undefined) {
						return '<a href="#" onclick="return EditProduct(\'' + params.value + '\',\'' + params.data.goods_no + '\');">' + params.value + '</a>';
					}
				}
			},
			{field: "goods_no", headerName: "온라인코드", pinned: 'left',width: 70, cellStyle: StyleLineHeight, aggFunc: "first"},
			{field: "style_no", headerName: "스타일넘버", pinned: 'left', cellStyle: {"line-height": "30px", "text-align": "center"}, aggFunc: "first"},

			{field: "img", headerName: "이미지", type: 'GoodsImageType', width:50, cellStyle: {"line-height": "30px"}, surl:"{{config('shop.front_url')}}",
				aggFunc: (params) => params.values.length > 0 ? params.values[0] : '',
			},
			{field: "img", headerName: "이미지_url", hide: true},
			{field: "goods_nm", headerName: "상품명", width: 270, aggFunc: "first",
				cellRenderer: function (params) {
					if (params.data?.goods_no == '' || params.node.aggData?.goods_no == '') {
						return '<a href="javascript:void(0);" onclick="return blank_goods_no();">' + (params.value || '') + '</a>';
					} else {
						let goods_no = params.data ? params.data.goods_no : params.node.aggData ? params.node.aggData.goods_no : '';
						return '<a href="#" onclick="return openHeadProduct(\'' + goods_no + '\');">' + (params.value || '') + '</a>';
					}
				}
			},
			{field: "goods_nm_eng", headerName: "상품명(영문)", width: 270, aggFunc: "first", 
				cellRenderer: function (params) {
					if (params.data?.goods_no == '' || params.node.aggData?.goods_no == '') {
						return '<a href="javascript:void(0);" onclick="return blank_goods_no();">' +(params.value || '') + '</a>';
					} else {
						let goods_no = params.data ? params.data.goods_no : params.node.aggData ? params.node.aggData.goods_no : '';
						return '<a href="#" onclick="return openHeadProduct(\'' + goods_no + '\');">' + (params.value || '') + '</a>';
					}
				}
			},
			{field: "prd_cd1", headerName: "품번", width:100, cellStyle: StyleLineHeight, rowGroup: true, hide: true},
			{field: "color", headerName: "컬러", width:50, cellStyle: {"text-align": "center"}},
			{field: "color_nm", headerName: "컬러명", width:90},
			{field: "size", headerName: "사이즈", width:50, cellStyle: {"text-align": "center"}},
			{field: "size_nm", headerName: "사이즈명", width:100},
			// {field: "goods_opt", headerName: "옵션", width:190},
			// {field: "opt_kind_nm", headerName: "품목", width:70, cellStyle: {"line-height": "30px", "text-align": "center"}},
			{field: "brand_nm", headerName: "브랜드", cellStyle: {"line-height": "30px", "text-align": "center"},aggFunc: "first"},
			
			{{--
			// {field: "wqty", headerName: "창고재고", width:70,type: 'currencyType', cellStyle: {"line-height": "30px"},
			// 	aggFunc: (params) => {
			// 		return params.values.reduce((a,c) => a + (c * 1), 0);
			// 	},
			// 	cellRenderer: function(params) {
			// 		if (params.value === undefined) return "";
			// 		if (params.node.rowPinned === 'top') {
			// 		} else if (params.data) {
			// 			return '<a href="#" onclick="return openStoreStock(\'' + (params.data.prd_cd || '') + '\', \'' + $("[name=sdate]").val() + '\');">' + params.value + '</a>';
			// 		} else if (params.node.aggData) {
			// 			return `<a href="#" onclick="return OpenStockPopup('${params.node.key}', '${$("[name=sdate]").val() || ''}');">${params.value}</a>`;
			// 		}
			// 	}
			// },
			// {
			// 	field: "sqty", headerName: "매장재고", width:70, type: 'currencyType', cellStyle: {"line-height": "30px"},
			// 		aggFunc: (params) => {
			// 		return params.values.reduce((a,c) => a + (c * 1), 0);
			// 		},
			// 		cellRenderer: function(params) {
			// 			if (params.value === undefined) return "";
			// 			if (params.node.rowPinned === 'top') {
			// 			} else if (params.data) {
			// 				return '<a href="#" onclick="return openStoreStock(\'' + (params.data.prd_cd || '') + '\', \'' + $("[name=sdate]").val() + '\');">' + params.value + '</a>';
			// 			} else if (params.node.aggData) {
			// 				return `<a href="#" onclick="return OpenStockPopup('${params.node.key}', '${$("[name=sdate]").val() || ''}');">${params.value}</a>`;
			// 			}
			// 		}
			// },
			--}}

			{field: "goods_sh", headerName: "정상가", type: 'currencyType', cellStyle: {"line-height": "30px"}, aggFunc: 'first'},
			{field: "price", headerName: "판매가", type: 'currencyType', width:80, cellStyle: {"line-height": "30px"}, aggFunc: 'first'},
			{field: "wonga", headerName: "원가", type: 'currencyType', width:80, cellStyle: {"line-height": "30px"}, aggFunc: 'first'},
			{field: "margin_amt", headerName: "마진액", type: 'numberType', width:80, cellStyle: {"line-height": "30px"}, aggFunc: 'first'},
			{field: "margin_rate", headerName: "마진율", type: 'percentType', width:80, cellStyle: {"line-height": "30px"}},
			{field: "org_nm", headerName: "원산지", cellStyle: {"line-height": "30px"}},
			{field: "com_nm", headerName: "업체", width:84, cellStyle: {"line-height": "30px"}},
			{{--
			// {field: "reg_dm", headerName: "등록일자", width:110, cellStyle: {"line-height": "30px"}},
			// {field: "upd_dm", headerName: "수정일자", width:110, cellStyle: {"line-height": "30px"}},
			--}}
			{field: "match_yn", headerName: "매칭여부", width:60, hide:true},
			{width:"auto"}
		];

		const basic_autoGroupColumnDef = (headerName, width = 150) => ({
			headerName: headerName,
			headerClass: 'bizest',
			minWidth: width,
			maxWidth: width,
			cellRenderer: 'agGroupCellRenderer',
			pinned: 'left'
		});

		const pApp = new App('', {
			gridId: "#div-gd",
		});

		const gridDiv = document.querySelector(pApp.options.gridId);
		let gx;

		$(document).ready(function() {
			gx = new HDGrid(gridDiv, columns, {
				rollup: true,
				autoGroupColumnDef: basic_autoGroupColumnDef('품번'),
				groupDefaultExpanded: 0, // 0: close, 1: open
				suppressAggFuncInHeader: true,
				animateRows: true,
				suppressDragLeaveHidesColumns: true,
				suppressMakeColumnVisibleAfterUnGroup: true,
				// rowGroupPanelShow: 'always',
				});
				gx.gridOptions.defaultColDef = {
					suppressMenu: true,
					resizable: true,
					sortable: true,
				};
				pApp.ResizeGrid(275);
				pApp.BindSearchEnter();
				Search();
			});

		function onCellValueChanged(e) {
			e.node.setSelected(true);
		}

		async function Search() {
			await setColumn();
			let ischeck = $('#ext_storage_qty').is(':checked');
			let data = $('form[name="search"]').serialize();
			data += '&ext_storage_qty=' + ischeck;

			gx.Request('/store/product/prd02/search', data, 1, function(d) {
                setAllRowGroupExpanded($("#grid_expand").is(":checked"));
            });
		}

		function setColumn() {
		let ord_field = $("[name=ord_field]").val();
		if(ord_field === "prd_cd1") {
			let prd_columns = columns.map(c => c.field === "prd_cd1" 
				? ({...c, rowGroup: true, hide: true, pinned: "left"}) 
				: c.type === "NumType" ? ({...c, hide: true})
				: c.field === "goods_no" ? ({...c, cellStyle: StyleLineHeight}) : c);
			gx.gridOptions.api.setColumnDefs(prd_columns);
		} else {
			let prd_columns = columns.map(c => c.field === "prd_cd1" 
				? ({...c, rowGroup: false, hide: false, pinned: "auto"}) 
				: c.type === "NumType" ? ({...c, hide: false})
				: c.field === "goods_no" ? ({...c, cellStyle: StyleGoodsNo}) : c);
			gx.gridOptions.api.setColumnDefs(prd_columns);
		}
	}


		const initSearchInputs = () => {
			document.search.reset(); // 모든 일반 input 초기화
			searchGoodsNos.Init(); // 스타일 넘버 api 초기화
			$('#brand_cd').val(null).trigger('change'); // 브랜드 select2 박스 초기화
			$('#cat_cd').val(null).trigger('change'); // 카테고리 select2 박스 초기화
		};

		/**
		 * @return {boolean}
		 */
		function UpdateStates(){

			var checkRows		= gx.getSelectedRows();
			var chg_sale_stat	= $("#chg_sale_stat").val();
			var goods_nos		= checkRows.map(function(row) {
				return row.goods_no;
			});

			if( chg_sale_stat === "" ){
				alert('변경할 상품상태를 선택해 주십시오.');
				return false;
			}

			if( goods_nos.length === 0 ){
				alert("상품상태를 변경할 상품을 선택해 주십시오.");
				return false;
			}
		}

		function AddProduct_upload() {
			var url = '/store/product/prd02/product_upload';
			var product = window.open(url, "_blank", "toolbar=no,scrollbars=yes,resizable=yes,status=yes,top=100,left=100,width=1024,height=900");
		}


		function AddProduct() {
			var url = '/store/product/prd02/create';
			var product = window.open(url, "_blank", "toolbar=no,scrollbars=yes,resizable=yes,status=yes,top=100,left=100,width=1024,height=900");
		}

		function EditProduct(product_code, goods_no) {
			var url = '/store/product/prd02/edit-goods-no/' + product_code + '/' + goods_no;
			var product = window.open(url, "_blank", "toolbar=no,scrollbars=yes,resizable=yes,status=yes,top=100,left=100,width=1100,height=900");
		}

		function AddProducts() {
			var url = '/store/product/prd02/batch-create';
			var product = window.open(url, "_blank", "toolbar=no,scrollbars=yes,resizable=yes,status=yes,top=100,left=100,width=1024,height=900");
		}

		function blank_goods_no() {
			alert('온라인코드가 비어있는 상품입니다.');
		}

		function AddProductImages() {
			var url = '/head/product/prd23';
			var product = window.open(url, "_blank", "toolbar=no,scrollbars=yes,resizable=yes,status=yes,top=100,left=100,width=1024,height=900");
		}

		function ShowProductImages() {
			const goods_nos = gx.gridOptions.api.getSelectedRows().map(row => row.goods_no);
			if(goods_nos.length < 1) return alert("상품을 선택해주세요.");
			var url = '/head/product/prd02/slider?goods_nos=' + goods_nos.join(",");
			var product = window.open(url, "_blank", "toolbar=no,scrollbars=yes,resizable=yes,status=yes,top=100,left=100,width=1024,height=900");
		}

		function openApi() {
			document.getElementsByClassName('sch-prdcd-range')[0].click();
		}

		function OpenStockPopup(prd_cd_p, date, color = '', size = '') {
			var url = `/store/product/prd04/stock?prd_cd_p=${prd_cd_p}&date=${date}&color=${color}&size=${size}`;
			var product = window.open(url, "_blank", "toolbar=no,scrollbars=yes,resizable=yes,status=yes,top=100,left=100,width=1000,height=900");
		}

	</script>
@stop

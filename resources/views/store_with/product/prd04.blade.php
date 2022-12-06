@extends('store_with.layouts.layout')
@section('title','상품')
@section('content')
<div class="page_tit">
	<h3 class="d-inline-flex">상품재고관리</h3>
	<div class="d-inline-flex location">
		<span class="home"></span>
		<span>/ 상품관리</span>
		<span>/ 상품재고관리</span>
	</div>
</div>

<form method="get" name="search" id="search">
	@csrf
	<input type='hidden' name='goods_nos' value=''>
	<div id="search-area" class="search_cum_form">
		<div class="card mb-3">
			<div class="d-flex card-header justify-content-between">
				<h4>검색</h4>
				<div>
					<a href="#" id="search_sbtn" onclick="return Search();" class="btn btn-sm btn-primary shadow-sm pl-2"><i class="fas fa-search fa-sm text-white-50"></i> 조회</a>
					<a href="#" onclick="AddStock('wonga');" class="btn btn-sm btn-outline-primary shadow-sm pl-2"><i class="bx bx-plus fs-16"></i> 1. 원가/상품 업로드</a>
					<a href="#" onclick="AddStock('storage');" class="btn btn-sm btn-outline-primary shadow-sm pl-2"><i class="bx bx-plus fs-16"></i> 2. 창고 재고 업로드</a>
					<a href="#" onclick="AddStock('store');" class="btn btn-sm btn-outline-primary shadow-sm pl-2"><i class="bx bx-plus fs-16"></i> 3. 매장 재고 업로드</a>
					<a href="#" onclick="gx.Download();" class="btn btn-sm btn-outline-primary shadow-sm pl-2"><i class="bx bx-download fs-16"></i> 엑셀다운로드</a>
					<div id="search-btn-collapse" class="btn-group mb-0 mb-sm-0"></div>
				</div>
			</div>
			<div class="card-body">

				<div class="row">
					<div class="col-lg-4">
						<div class="form-group">
							<label for="good_types">검색일자</label>
							<div class="docs-datepicker flex_box">
								<div class="input-group">
								<input type="text" class="form-control form-control-sm docs-date" name="sdate" value="{{ @$sdate }}" autocomplete="off">
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
				</div>
				<div class="row">
					<div class="col-lg-4 inner-td">
						<div class="form-group">
							<label>상품코드</label>
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
				</div>
				<div class="row">
					<div class="col-lg-4 inner-td">
						<div class="form-group">
							<label for="prd_cd">상품옵션 범위검색</label>
							<div class="form-inline">
								<div class="form-inline-inner input-box w-100">
									<div class="form-inline inline_btn_box">
										<input type='hidden' id="prd_cd_range" name='prd_cd_range'>
										<input type='text' id="prd_cd_range_nm" name='prd_cd_range_nm' class="form-control form-control-sm w-100 ac-style-no" readonly style="background-color: #fff;">
										<a href="#" class="btn btn-sm btn-outline-primary sch-prdcd-range"><i class="bx bx-dots-horizontal-rounded fs-16"></i></a>
									</div>
								</div>
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
					<div class="col-lg-4 inner-td">
						<div class="form-group">
							<label for="">자료수/정렬</label>
							<div class="form-inline">
								<div class="form-inline-inner input_box" style="width:24%;">
									<select name="limit" class="form-control form-control-sm">
										<option value="500">500</option>
										<option value="1000">1000</option>
										<option value="2000">2000</option>
										<option value="5000">5000</option>
									</select>
								</div>
								<span class="text_line">/</span>
								<div class="form-inline-inner input_box" style="width:45%;">
									<select name="ord_field" class="form-control form-control-sm">
										<option value="prd_cd_p">등록일(코드일련별)</option>
										<option value="pc.rt">등록일</option>
										<option value="pc.prd_cd">상품코드</option>
										<option value="pc.goods_no">상품번호</option>
										<option value="g.goods_nm">상품명</option>
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
							<label for="style_no">스타일넘버/상품번호</label>
							<div class="form-inline">
								<div class="form-inline-inner input_box">
									<input type='text' class="form-control form-control-sm ac-style-no search-enter" name='style_no' id="style_no" value="">
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
			</div>
		</div>
		<div class="resul_btn_wrap mb-3">
			<a href="#" id="search_sbtn" onclick="return Search();" class="btn btn-sm btn-primary shadow-sm pl-2"><i class="fas fa-search fa-sm text-white-50"></i> 조회</a>
			<a href="#" onclick="AddStock('wonga');" class="btn btn-sm btn-outline-primary shadow-sm pl-2"><i class="bx bx-plus fs-16"></i> 1. 원가/상품 업로드</a>
			<a href="#" onclick="AddStock('storage');" class="btn btn-sm btn-outline-primary shadow-sm pl-2"><i class="bx bx-plus fs-16"></i>2. 창고 재고 업로드</a>
			<a href="#" onclick="AddStock('store');" class="btn btn-sm btn-outline-primary shadow-sm pl-2"><i class="bx bx-plus fs-16"></i>3. 매장 재고 업로드</a>
			<a href="#" onclick="gx.Download();" class="btn btn-sm btn-outline-primary shadow-sm pl-2"><i class="bx bx-download fs-16"></i> 엑셀다운로드</a>
			<div class="search_mode_wrap btn-group mr-2 mb-0 mb-sm-0"></div>
		</div>
	</div>

	<div id="filter-area" class="card shadow-none mb-0 ty2 last-card">
		<div class="card-body">
			<div class="card-title mb-3">
				<div class="filter_wrap">
					<div class="fl_box">
						<h6 class="m-0 font-weight-bold">총 <span id="gd-total" class="text-primary">0</span> 건</h6>
					</div>
					<div class="fr_box">
						<div class="custom-control custom-checkbox form-check-box pr-2" style="display:inline-block;">
							<input type="checkbox" class="custom-control-input" name="ext_store_qty" id="ext_store_qty" value="Y">
							<label class="custom-control-label font-weight-normal" for="ext_store_qty">매장재고 0 제외</label>
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
	.ag-row-level-1 {
		background-color: #edf4fd !important;
	}
</style>
<script language="javascript">
	const pinnedRowData = [{ goods_nm: '', goods_sh: 0, price: 0, wqty: 0, sqty: 0 }];
		
	const columns = [
		{headerName: '#', pinned: 'left', type: 'NumType', width:40, cellStyle: StyleLineHeight,
			cellRenderer: (params) => params.node.rowPinned === 'top' ? '' : parseInt(params.value) + 1,
		},
		{field: "prd_cd", headerName: "상품코드", width:120, cellStyle: StyleLineHeight,
			cellRenderer: function(params) {
				if (params.node.rowPinned === 'top') return "합계";
				if (params.value !== undefined) {
					return `<a href="javascript:void(0);" onclick="return OpenStockPopup('${params.data.prd_cd_p}', '${$("[name=sdate]").val() || ''}', '${params.data.color}', '${params.data.size}');">${params.value}</a>`;
				}
			}
		},
		{field: "goods_no", headerName: "상품번호", width: 70, cellStyle: StyleLineHeight, aggFunc: "first"},
		{field: "img", headerName: "이미지", type: 'GoodsImageType', width:50, surl:"{{config('shop.front_url')}}"},
		{field: "img", headerName: "이미지_url", hide: true},
		{field: "goods_nm", headerName: "상품명", width: 270, aggFunc: "first",
			cellRenderer: function (params) {
				if (params.data?.goods_no == '' || params.node.aggData?.goods_no == '') {
					return '<a href="javascript:void(0);" onclick="return blank_goods_no();">' + params.value + '</a>';
				} else {
					let goods_no = params.data ? params.data.goods_no : params.node.aggData ? params.node.aggData.goods_no : '';
					return '<a href="#" onclick="return openHeadProduct(\'' + goods_no + '\');">' + params.value + '</a>';
				}
			}
		},
		{field: "goods_nm_eng", headerName: "상품명(영문)", width: 270, aggFunc: "first",
			cellRenderer: function (params) {
				if (params.data?.goods_no == '' || params.node.aggData?.goods_no == '') {
					return '<a href="javascript:void(0);" onclick="return blank_goods_no();">' + (params.value || '') + '</a>';
				} else {
					let goods_no = params.data ? params.data.goods_no : params.node.aggData ? params.node.aggData.goods_no : '';
					return '<a href="#" onclick="return openHeadProduct(\'' + goods_no + '\');">' + (params.value || '') + '</a>';
				}
			}
		},
		{field: "prd_cd_p", headerName: "코드일련", width: 100, cellStyle: StyleLineHeight, rowGroup: true, hide: true,
			cellRenderer: function (params) {
				if(params.value === undefined) return "";
				return `<a href="javascript:void(0);" onclick="return OpenStockPopup('${params.value}', '${$("[name=sdate]").val() || ''}');">${params.value}</a>`;
			}
		},
		{field: "color", headerName: "컬러", width: 55, cellStyle: StyleLineHeight},
		{field: "color_nm", headerName: "컬러명", width: 110, cellStyle: {"line-height": "30px"}},
		{field: "size", headerName: "사이즈", width: 55, cellStyle: StyleLineHeight},
		{field: "goods_opt", headerName: "옵션", width: 200},
		{field: "goods_sh", headerName: "TAG가", type: 'currencyType', width:85, aggFunc: 'first'},
		{field: "price", headerName: "판매가", type: 'currencyType', width:85, aggFunc: 'first'},
		{field: "wqty", headerName: "창고재고", width:70, type: 'currencyType', 
			aggFunc: (params) => {
				return params.values.reduce((a,c) => a + (c * 1), 0);
			},
			cellRenderer: function(params) {
				if (params.value === undefined) return "";
				if (params.node.rowPinned === 'top') {
                    return params.value;
                } else if (params.data) {
					return '<a href="#" onclick="return openStoreStock(\'' + (params.data.prd_cd || '') + '\', \'' + $("[name=sdate]").val() + '\');">' + Comma(params.value) + '</a>';
                } else if (params.node.aggData) {
					return `<a href="#" onclick="return OpenStockPopup('${params.node.key}', '${$("[name=sdate]").val() || ''}');">${Comma(params.value)}</a>`;
				}
			}
		},
		{field: "sqty", headerName: "매장재고", width:70, type: 'currencyType',
			aggFunc: (params) => {
				return params.values.reduce((a,c) => a + (c * 1), 0);
			},
			cellRenderer: function(params) {
				if (params.value === undefined) return "";
				if (params.node.rowPinned === 'top') {
                    return params.value;
                } else if (params.data) {
					return '<a href="#" onclick="return openStoreStock(\'' + (params.data.prd_cd || '') + '\', \'' + $("[name=sdate]").val() + '\');">' + Comma(params.value) + '</a>';
                } else if (params.node.aggData) {
					return `<a href="#" onclick="return OpenStockPopup('${params.node.key}', '${$("[name=sdate]").val() || ''}');">${Comma(params.value)}</a>`;
				}
			}
		},
		{field: "style_no", headerName: "스타일넘버", width: 70, cellStyle: StyleLineHeight, aggFunc: "first"},
		{field: "brand_nm", headerName: "브랜드", width: 70, cellStyle: StyleLineHeight, aggFunc: "first"},
		{field: "match_yn", headerName: "매칭여부", hide:true},
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
			// onCellValueChanged: onCellValueChanged,
			pinnedTopRowData: pinnedRowData,
			getRowStyle: (params) => {
                if (params.node.rowPinned)  return {'font-weight': 'bold', 'background': '#eee !important', 'border': 'none'};
            },
			autoGroupColumnDef: basic_autoGroupColumnDef('코드일련'),
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

	function blank_goods_no() {
		alert('상품번호가 비어있는 상품입니다.');
	}

	function onCellValueChanged(e) {
		e.node.setSelected(true);
	}

	async function Search() {
		await setColumn();

		let data = $('form[name="search"]').serialize();
		gx.Request('/store/product/prd04/search', data, 1, function(e) {
			const t = e.head.total_row;
			gx.gridOptions.api.setPinnedTopRowData([{ 
				goods_nm: '', goods_sh: t.total_goods_sh, 
				price: t.total_price, 
				wqty: Comma(t.total_wqty),
				sqty: Comma(t.total_sqty),
			}]);
		});
	}

	// 정렬 타입에 따른 column 업데이트
	function setColumn() {
		let ord_field = $("[name=ord_field]").val();
		if(ord_field === "prd_cd_p") {
			let prd_columns = columns.map(c => c.field === "prd_cd_p" 
				? ({...c, rowGroup: true, hide: true, pinned: "left"}) 
				: c.field === "color" || c.field === "size" || c.field === "color_nm" ? ({...c, pinned: "left"})
				: c.type === "NumType" ? ({...c, hide: true})
				: c.field === "goods_no" ? ({...c, cellStyle: StyleLineHeight}) : c);
			gx.gridOptions.api.setColumnDefs(prd_columns);
		} else {
			let prd_columns = columns.map(c => c.field === "prd_cd_p" 
				? ({...c, rowGroup: false, hide: false, pinned: "auto"}) 
				: c.field === "color" || c.field === "size" || c.field === "color_nm" ? ({...c, pinned: "auto"}) 
				: c.type === "NumType" ? ({...c, hide: false})
				: c.field === "goods_no" ? ({...c, cellStyle: StyleGoodsNo}) : c);
			gx.gridOptions.api.setColumnDefs(prd_columns);
		}
	}

	function AddStock(item){
		if( item == 'wonga' )			url = '/store/product/prd04/batch_wonga';
		else if( item == 'storage' )	url = '/store/product/prd04/batch';
		else if( item == 'store' )		url = '/store/product/prd04/batch_store';

		window.open(url,"_blank","toolbar=no,scrollbars=yes,resizable=yes,status=yes,top=500,left=500,width=1200,height=800");
	}

	function OpenStockPopup(prd_cd_p, date, color = '', size = '') {
		var url = `/store/product/prd04/stock?prd_cd_p=${prd_cd_p}&date=${date}&color=${color}&size=${size}`;
		var product = window.open(url, "_blank", "toolbar=no,scrollbars=yes,resizable=yes,status=yes,top=100,left=100,width=1000,height=900");
	}

</script>

@stop

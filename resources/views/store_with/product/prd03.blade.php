@extends('store_with.layouts.layout')
@section('title','원부자재상품관리')
@section('content')
	<div class="page_tit">
		<h3 class="d-inline-flex">원부자재관리</h3>
		<div class="d-inline-flex location">
			<span class="home"></span>
			<span>/ 원부자재관리</span>
			<span>/ 원부자재관리</span>
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
	<form method="get" name="search" id="search">
		@csrf
		<input type='hidden' name='goods_nos' value=''>
		<div id="search-area" class="search_cum_form">
			<div class="card mb-3">
				<div class="d-flex card-header justify-content-between">
					<h4>검색</h4>
					<div>
						<a href="#" id="search_sbtn" onclick="return Search();" class="btn btn-sm btn-primary shadow-sm pl-2"><i class="fas fa-search fa-sm text-white-50"></i> 조회</a>
						<a href="#" onclick="AddProduct();" class="btn btn-sm btn-outline-primary shadow-sm pl-2"><i class="bx bx-plus fs-16"></i> 등록</a>
						<a href="#" onclick="gx.Download();" class="btn btn-sm btn-outline-primary shadow-sm pl-2"><i class="bx bx-download fs-16"></i> 엑셀다운로드</a>
						<a href="/store/cs/cs03" class="btn btn-sm btn-primary shadow-sm pl-2">원부자재관리</a>
						<div id="search-btn-collapse" class="btn-group mb-0 mb-sm-0"></div>
					</div>
				</div>
				<div class="card-body">
					<div class="row">
						<div class="col-lg-4 inner-td">
							<div class="form-group">
								<label for="type">구분</label>
								<div class="flex_box">
									<select name="type" class="form-control form-control-sm w-100">
										<option value=''>전체</option>
										@foreach ($types as $type)
											<option value='{{ $type->code_id }}'>{{ $type->code_val }}</option>
										@endforeach
									</select>
								</div>
							</div>
						</div>
						<div class="col-lg-4 inner-td">
							<div class="form-group">
								<label>원부자재코드</label>
								<div class="form-inline">
									<div class="form-inline-inner input-box w-100">
										<div class="form-inline inline_btn_box">
											<input type='text' id="prd_cd_sub" name='prd_cd_sub' class="form-control form-control-sm w-100 ac-style-no search-enter">
											<a href="#" class="btn btn-sm btn-outline-primary sch-prdcd_sub"><i class="bx bx-dots-horizontal-rounded fs-16"></i></a>
										</div>
									</div>
								</div>
							</div>
						</div>
						<div class="col-lg-4 inner-td">
							<div class="form-group">
								<label for="prd_nm">원부자재명</label>
								<div class="flex_box">
									<input type='text' class="form-control form-control-sm search-enter" name='prd_nm' id="prd_nm" value=''>
								</div>
							</div>
						</div>
					</div>
					<div class="row">
						{{--
						<div class="col-lg-4 inner-td">
							<div class="form-group">
								<label for="name">원부자재업체</label>
								<div class="form-inline inline_select_box">
									<div class="form-inline-inner input-box w-100">
										<div class="form-inline inline_btn_box">
											<input type="hidden" id="com_cd" name="com_cd" />
											<input onclick="" type="text" id="com_nm" name="com_nm" class="form-control form-control-sm search-all search-enter" style="width:100%;" autocomplete="off" />
											<a href="#" class="btn btn-sm btn-outline-primary sch-sup-company"><i class="bx bx-dots-horizontal-rounded fs-16"></i></a>
										</div>
									</div>
								</div>
							</div>
						</div>
						--}}
						<div class="col-lg-4 inner-td">
							<div class="form-group">
								<label for="">자료수/정렬</label>
								<div class="form-inline">
									<div class="form-inline-inner input_box" style="width:24%;">
										<select name="limit" class="form-control form-control-sm">
											<option value="500">500</option>
											<option value="1000">1000</option>
											<option value="2000">2000</option>
										</select>
									</div>
									<span class="text_line">/</span>
									<div class="form-inline-inner input_box" style="width:45%;">
										<select name="ord_field" class="form-control form-control-sm">
											<option value="prd_cd">바코드</option>
											<option value="prd_nm">원부자재명</option>
											<option value="p.price">판매가</option>
											<option value="p.wonga">원가</option>
											<option value="p.rt" selected>등록일자</option> 
											<option value="p.ut">수정일자</option>
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
				<a href="#" id="search_sbtn" onclick="return Search();" class="btn btn-sm btn-primary shadow-sm pl-2"><i class="fas fa-search fa-sm text-white-50"></i> 조회</a>
				<a href="#" onclick="AddProduct();" class="btn btn-sm btn-outline-primary shadow-sm pl-2"><i class="bx bx-plus fs-16"></i> 추가</a>
				<a href="#" onclick="gx.Download();" class="btn btn-sm btn-outline-primary shadow-sm pl-2"><i class="bx bx-download fs-16"></i> 엑셀다운로드</a>
				<a href="#" class="btn btn-sm btn-primary shadow-sm pl-2">원부자재관리</a>
				<div class="search_mode_wrap btn-group mr-2 mb-0 mb-sm-0"></div>
			</div>
		</div>
	<!-- DataTales Example -->
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
		</form>	
				<form method="post" name="save" action="/head/stock/stk01">
					@csrf
					<div class="table-responsive">
						<div id="div-gd" style="min-height:300px;height:calc(100vh - 370px);width:100%;" class="ag-theme-balham gd-lh50 ty2"></div>
					</div>
				</form>
			</div>
		</div>
	<style>
		/* 전시카테고리 상품 이미지 사이즈 픽스 */
		.img {
			width: 30px;
			height: 30px;
		}

	</style>
	<script language="javascript">

		const DEFAULT = { lineHeight : "30px" };

		const columns = [
			{headerName: '#', pinned: 'left', type: 'NumType', width:40, cellStyle: {"line-height": "30px", 'text-align' : 'center'}},
			{field: "prd_cd", headerName: "원부자재코드", width:120, cellStyle: DEFAULT,
				cellRenderer: function(params) {
					if (params.value !== undefined) {
						return '<a href="#" onclick="return EditProduct(\'' + params.value + '\');">' + params.value + '</a>';
					}
				}
			},
			{
				field: "prd_nm",
				headerName: "원부자재명",
				width: 100,
				cellStyle: DEFAULT
			},
			{
				field: "type_nm",
				headerName: "구분",
				width: 70,
				cellStyle: DEFAULT,
				cellRenderer: function(params){
					if (params.value !== undefined) {
						if (params.data.type_nm == '부자재') {
							return "<p style=color:#009999>" + params.data.type_nm + "</p>";
        				} else if (params.data.type_nm == '사은품') {
							return "<p>" + params.data.type_nm + "</p>";
						}
    				}
				}
			},
			{
				field: "opt",
				headerName: "품목",
				width: 80,
				cellStyle: DEFAULT
			},
			{field: "img", headerName: "이미지", type: 'GoodsImageType', width:50, cellStyle: DEFAULT, surl:"{{config('shop.front_url')}}"},
			{field: "img", headerName: "이미지_url", hide: true},
			
			{
				field: "color",
				headerName: "컬러명",
				cellStyle: DEFAULT,
				width: 80
			},
			{
				field: "size",
				headerName: "사이즈명",
				cellStyle: DEFAULT,
				width: 80
			},
			{
				field: "tag_price",
				headerName: "정상가",
				type: 'currencyType',
				cellStyle: DEFAULT,
				width: 80
			},
			{
				field: "price",
				headerName: "현재가",
				type: 'currencyType',
				cellStyle: DEFAULT,
				width: 80
			},
			{
				field: "wonga",
				headerName: "원가",
				type: 'currencyType',
				cellStyle: DEFAULT,
				width: 80
			},
			{
				field: 'stock_qty',
				headerName: '창고재고', 
				type: "currencyType",
				width: 80,
				cellRenderer: function(params) {
					if (params.value !== undefined) {
						return '<a href="#" onclick="return openStoreStock(\'' + params.data.prd_cd + '\');">' + params.value + '</a>';
					}
				}
			},
			{
				field: 'store_qty',
				headerName: '매장재고', 
				type: "currencyType",
				width: 80,
				cellRenderer: function(params) {
                	if (params.value !== undefined) {
						return '<a href="#" onclick="return openStoreStock(\'' + params.data.prd_cd + '\');">' + params.value + '</a>';
					}
				}
			},
			{
				field: "unit",
				headerName: "단위",
				cellStyle: DEFAULT,
				width: 120
			},
			{
				field: "sup_com",
				headerName: "공급업체(거래선)",
				cellStyle: DEFAULT,
				width: 120
			},
			{field: "rt", headerName: "등록일자", width:110, cellStyle: DEFAULT},
			{field: "ut", headerName: "수정일자", width:110, cellStyle: DEFAULT},
			{field: "nvl", headerName: "", cellStyle: DEFAULT, width: "auto"}
		];

		const pApp = new App('', {
			gridId: "#div-gd",
		});
		const gridDiv = document.querySelector(pApp.options.gridId);
		let gx;
		$(document).ready(function() {
			gx = new HDGrid(gridDiv, columns);
			gx.alwaysShowHorizontalScroll = true;
			pApp.ResizeGrid(275);
			pApp.BindSearchEnter();
			Search();
		});

		function Search() {
			let data = $('form[name="search"]').serialize();
			gx.Request('/store/product/prd03/search', data, 1);
		}

		const initSearchInputs = () => {
			document.search.reset(); // 모든 일반 input 초기화
			searchGoodsNos.Init(); // 스타일 넘버 api 초기화
			$('#brand_cd').val(null).trigger('change'); // 브랜드 select2 박스 초기화
			$('#cat_cd').val(null).trigger('change'); // 카테고리 select2 박스 초기화
		};

		function AddProduct() {
			var url = '/store/product/prd03/create';
			var product = window.open(url, "_blank", "toolbar=no,scrollbars=yes,resizable=yes,status=yes,top=100,left=100,width=1024,height=900");
		}

		function EditProduct(product_code) {
			var url = '/store/product/prd03/edit/' + product_code;
			var product = window.open(url, "_blank", "toolbar=no,scrollbars=yes,resizable=yes,status=yes,top=100,left=100,width=1100,height=900");
		}

		//원부자재 업체 검색
		$( ".sch-sup-company" ).on("click", () => {
        	searchCompany.Open(null, '6', 'wonboo');
   		});

	</script>
@stop

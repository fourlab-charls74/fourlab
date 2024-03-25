@extends('head_with.layouts.layout-nav')
@section('title','카테고리 매칭 매장 상품')
@section('content')

<div class="show_layout py-3 px-sm-3">
	<div class="page_tit d-flex justify-content-between">
		<div class="d-flex">
			<h3 class="d-inline-flex">카테고리 매칭 매장상품</h3>
			<div class="d-inline-flex location">
				<span class="home"></span>
				<span>/ 상품</span>
				<span>/ 전시</span>
			</div>
		</div>
		<div class="d-flex">
			<a href="javascript:void(0);" onclick="return Search();" class="btn btn-primary mr-1"><i class="fas fa-search fa-sm text-white-50 mr-1"></i>조회</a>
			<a href="javascript:void(0);" onclick="window.close();" class="btn btn-outline-primary"><i class="fas fa-times fa-sm mr-1"></i>닫기</a>
		</div>
	</div>

	<form name="f1" id="f1">
		<input type="hidden" name="d_cat_cd" id="d_cat_cd" value="{{$d_cat_cd}}">

		<div id="search-area" class="search_cum_form">
			<div class="card mb-3">
				<div class="card-body">
					<div class="row">
						<div class="col-lg-4 inner-td">
							<div class="form-group">
								<label for="goods_stat">상품상태</label>
								<div class="flax_box">
									<select id="goods_stat" name='goods_stat' class="form-control form-control-sm">
										<option value=''>전체</option>
										@foreach ($goods_stats as $goods_stat)
											<option value='{{ $goods_stat->code_id }}'>{{ $goods_stat->code_val }}</option>
										@endforeach
									</select>
								</div>
							</div>
						</div>
						<div class="col-lg-4 inner-td">
							<div class="form-group">
								<label for="style_no">스타일넘버/온라인코드</label>
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
						<div class="col-lg-4 inner-td">
							<div class="form-group">
								<label for="formrow-email-input">상품명</label>
								<div class="flax_box">
									<input type='text' class="form-control form-control-sm ac-goods-nm search-enter" name='goods_nm' value=''>
								</div>
							</div>
						</div>
					</div>
				</div>
						
						
			</div>
		</div>

		<div class="card">
			<div class="card-body pt-2">
				<div class="card-title">
					<div class="filter_wrap">
						<div class="fl_box px-0 mx-0">
							<h6 class="m-0 font-weight-bold">총 : <span id="gd-total" class="text-primary">0</span> 건</h6>
						</div>
					</div>
				</div>
				<div class="table-responsive">
					<div id="div-gd" class="ag-theme-balham"></div>
				</div>
			</div>
		</div>

	</form>
</div>

<script>
	const columns = [
		{field:"goods_no",		headerName: "온라인트코드",	width:90,
			cellStyle: function(params) {
				if( params.data.category_goods_yn === 'N' ){
					return {
						'background' : '#FF0000'
					};
				}
			}		
		},
		{field:"style_no",		headerName: "스타일번호",		width:100, cellStyle:{'text-align':'center'}},
		{field:"head_desc",		headerName: "상단홍보글",		width:150},
		{field:"goods_nm",		headerName: "상품명",		width:300, type: 'HeadGoodsNameType'},
		{field:"sale_stat_cl",	headerName: "상품상태",		width:80,
			cellStyle: function(params) {
				var state = {
					"판매중지": "#808080",
					"등록대기중": "#669900",
					"판매대기중": "#000000",
					"임시저장": "#000000",
					"판매중": "#0000ff",
					"품절[수동]": "#ff0000",
					"품절": "#AAAAAA",
					"휴지통": "#AAAAAA"
				};
				if (params.value !== undefined) {
					if (state[params.value]) {
						var color = state[params.value];
						return {
							color: color,
							"text-align": "center",
						};
					}
				}
			}
		},
		{
			field: "goods_sh",
			headerName: "온라인 정상가",
			type: 'currencyType',
		},
		{
			field: "price",
			headerName: "온라인 판매가",
			type: 'currencyType',
		},
		{ field: "category_goods_yn", hide:true},
		{width:"auto"}
	];
</script>
<script type="text/javascript" charset="utf-8">

	const pApp = new App('', {
		gridId: "#div-gd",
	});
	let gx;

	$(document).ready(function() {
		pApp.ResizeGrid(210);
		pApp.BindSearchEnter();
		let gridDiv = document.querySelector(pApp.options.gridId);
		let options = {}
		gx = new HDGrid(gridDiv, columns, options);
		gx.gridOptions.animateRows = true;
		Search();
	});

	function Search() {
		let data = $('form[name="f1"]').serialize();
		gx.Request('/head/product/prd10/store_goods_search/', data);
	}
</script>


@stop

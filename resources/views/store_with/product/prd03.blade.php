@extends('store_with.layouts.layout')
@section('title','원부자재상품관리')
@section('content')
	<div class="page_tit">
		<h3 class="d-inline-flex">원부자재상품관리</h3>
		<div class="d-inline-flex location">
			<span class="home"></span>
			<span>/ 원부자재상품관리</span>
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
						<a href="#" onclick="AddProduct();" class="btn btn-sm btn-outline-primary shadow-sm pl-2"><i class="bx bx-plus fs-16"></i> 추가</a>
						<a href="#" onclick="gx.Download();" class="btn btn-sm btn-outline-primary shadow-sm pl-2"><i class="bx bx-download fs-16"></i> 엑셀다운로드</a>
						<a href="#" class="btn btn-sm btn-primary shadow-sm pl-2">원부자재관리</a>
						<div id="search-btn-collapse" class="btn-group mb-0 mb-sm-0"></div>
					</div>
				</div>
				<div class="card-body">
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
								<label for="prd_nm">원부자재명</label>
								<div class="flex_box">
									<input type='text' class="form-control form-control-sm ac-goods-nm search-enter" name='prd_nm' id="prd_nm" value=''>
								</div>
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-lg-4 inner-td">
							<div class="form-group">
								<label for="goods_stat">진행상태</label>
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
						<div class="col-lg-4 inner-td">
							<div class="form-group">
								<label for="name">공급업체</label>
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
										</select>
									</div>
									<span class="text_line">/</span>
									<div class="form-inline-inner input_box" style="width:45%;">
										<select name="ord_field" class="form-control form-control-sm">
											<option value="prd_cd">상품코드</option>
											<option value="prd_nm">원부자재명</option>
											<option value="i.rt">등록일자</option>
											<option value="i.ut">수정일자</option>
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
							<span style="font-weight:500;line-height:30px;margin-left:5px;vertical-align:middle;" class="mr-1">선택한 상품코드를 상품번호</span>
							<div>
								<input type="text" id="goods_no" class="form-control form-control-sm" name="goods_no" value="">
							</div>
							<span style="font-weight:500;line-height:30px;margin-left:5px;vertical-align:middle;" class="mr-1">로</span>
							<a href="#" onclick="Save();" class="btn btn-sm btn-primary shadow-sm pl-2"><i class="fas fa-sm text-white-50"></i>매칭</a>
						//-->
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
		const columns = [
			{headerName: '#', pinned: 'left', type: 'NumType', width:40, cellStyle: {"line-height": "30px"}},
/*
			{field: "chk", headerName: '', cellClass: 'hd-grid-code', headerCheckboxSelection: true, checkboxSelection: true, width: 28, pinned: 'left', sort: null},
*/
			{
				field: "type_nm",
				headerName: "구분",
				width: 70
			},
			{
				field: "opt",
				headerName: "품목",
				width: 80
			},
			{field: "img", headerName: "이미지", type: 'GoodsImageType', width:50, cellStyle: {"line-height": "30px"}, surl:"{{config('shop.front_url')}}"},
			{field: "img", headerName: "이미지_url", hide: true},
			{field: "prd_cd", headerName: "상품코드", width:120, cellStyle: {"line-height": "30px"},
				cellRenderer: function(params) {
					if (params.value !== undefined) {
						return '<a href="#" onclick="return EditProduct(\'' + params.value + '\',\'' + params.data.goods_no + '\');">' + params.value + '</a>';
					}
				}
			},
			{
				field: "color",
				headerName: "칼라",
				width: 80
			},
			{
				field: "size",
				headerName: "사이즈",
				width: 80
			},
			{
				field: "prd_nm",
				headerName: "원부자재명",
				width: 100
			},
			{
				field: "seq",
				headerName: "순서",
				width: 50
			},
			{
				field: "year",
				headerName: "년도",
				width: 80
			},
			{
				field: "season",
				headerName: "시즌",
				width: 80
			},
			{
				field: "gender",
				headerName: "성별",
				width: 80
			},
			{
				field: "item",
				headerName: "아이템",
				width: 80
			},
			{
				field: "sup_com",
				headerName: "공급업체(거래선)",
				width: 120
			},
			{
				field: "unit",
				headerName: "단위",
				width: 120
			},
			{field: "rt", headerName: "등록일자", width:110, cellStyle: {"line-height": "30px"}},
			{field: "ut", headerName: "수정일자", width:110, cellStyle: {"line-height": "30px"}},
			{field: "nvl", headerName: "", width: "auto"}
		];

		const pApp = new App('', {
			gridId: "#div-gd",
		});
		const gridDiv = document.querySelector(pApp.options.gridId);
		let gx;
		$(document).ready(function() {
			gx = new HDGrid(gridDiv, columns, {onCellValueChanged: onCellValueChanged});
			pApp.ResizeGrid(275);
			pApp.BindSearchEnter();
			Search();
		});

		function onCellValueChanged(e) {
			e.node.setSelected(true);
		}

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

			if( confirm("선택된 상품의 상품상태를 변경하시겠습니까?") ){
				$.ajax({
					async: true,
					type: 'put',
					url: '/head/product/prd01/update/state',
					data: {
						"goods_no[]": goods_nos,
						"chg_sale_stat": chg_sale_stat,
					},
					success: function(res) {
						console.log(res);
						if (res.code === 200) {
							var fail = res.head.fail;
							if (fail === 0) {
								alert('상품상태를 변경하였습니다.');
								Search(1);
							} else {
								alert(fail + ' 개의 상품이 재고부족으로 판매중 상태로 변경되지 않았습니다.\n해당 상품은 재고를 먼저 확인하신 후 판매중으로 상태 변경하시기 바랍니다.');
							}
						} else {
							console.log(res);
						}
					},
					error: function(request, status, error) {
						console.log("error")
					}
				});
			}
		}

		function AddProduct() {
			var url = '/store/product/prd03/create';
			var product = window.open(url, "_blank", "toolbar=no,scrollbars=yes,resizable=yes,status=yes,top=100,left=100,width=1024,height=900");
		}

		function EditProduct(product_code, goods_no) {
			var url = '/store/product/prd03/edit-goods-no/' + product_code + '/' + goods_no;
			var product = window.open(url, "_blank", "toolbar=no,scrollbars=yes,resizable=yes,status=yes,top=100,left=100,width=1100,height=900");
		}

		function AddProducts() {
			var url = '/store/product/prd03/batch-create';
			var product = window.open(url, "_blank", "toolbar=no,scrollbars=yes,resizable=yes,status=yes,top=100,left=100,width=1024,height=900");
		}

		const EditProducts = () => {
			const goods_nos = gx.gridOptions.api.getSelectedRows().map((row) => {
				return row.goods_no + "_" + row.goods_sub;
			});

			const POP_URL = '/head/product/prd01/edit';
			const target = "popForm";

			const [ top, left, width, height ] = [ 100, 100, 1700, 1200 ];
			const child_window = window.open(POP_URL, target, `toolbar=no,scrollbars=yes,resizable=yes,status=yes,top=${top},left=${left},width=${width},height=${height}`);

			const form = document.search;
			form.action = POP_URL;
			form.method = 'post';
			form.target = target;
			form.goods_nos.value = goods_nos;
			form.submit();
		};

		function AddProductImages() {
			var url = '/head/product/prd23';
			var product = window.open(url, "_blank", "toolbar=no,scrollbars=yes,resizable=yes,status=yes,top=100,left=100,width=1024,height=900");
		}

		function ShowProductImages() {
			const goods_nos = gx.gridOptions.api.getSelectedRows().map(row => row.goods_no);
			if(goods_nos.length < 1) return alert("상품을 선택해주세요.");
			var url = '/head/product/prd03/slider?goods_nos=' + goods_nos.join(",");
			var product = window.open(url, "_blank", "toolbar=no,scrollbars=yes,resizable=yes,status=yes,top=100,left=100,width=1024,height=900");
		}

		// 수정된 상품정보 저장
		function SaveSelectedProducts() {
			var data  = [];
			for(row = 0;row < gx.gridOptions.api.getDisplayedRowCount();row++){
				var rowNode = gx.gridOptions.api.getDisplayedRowAtIndex(row);
				if(rowNode.selected == true){
					data.push(
						{
							'goods_no': rowNode.data.goods_no,
							'style_no':rowNode.data.style_no,
							'head_desc':rowNode.data.head_desc,
							'goods_nm':rowNode.data.goods_nm,
							'ad_desc':rowNode.data.ad_desc,
							'price':rowNode.data.price,
							'goods_memo':rowNode.data.goods_memo,
						}
					)
				}
			}
			if(data.length < 1) return alert("수정할 상품을 선택해주세요.");
			if(!confirm("선택한 상품의 수정사항을 저장하시겠습니까?")) return;
			$.ajax({
				async: true,
				type: 'post',
				dataType:'json',
				url: '/head/product/prd01/update',
				data: {'data': data},
				success: function (res) {
					if(res.code == 200){
						alert(res.msg);
						Search();
					} else {
						alert(res.msg +"\n다시 시도하여 주십시오.");;
						console.log(res);
					}
				},
				error: function(e) {
					console.log(e.responseText);
				}
			});
		}

		//휴지통 상품 삭제
		function DeleteTrash(){
			var data  = [];
			const row = gx.getRows();

			for( i = 0; i < row.length; i++ ) {
				if( row[i]['sale_stat_cl'] == "휴지통"){
					data.push(row[i]['goods_no']);
				}
			}

			if( confirm("리스트에 있는 휴지통 상품을 삭제하시겠습니까?")){
				$.ajax({
					async: true,
					type: 'post',
					url: '/head/product/prd01/cleanup-trash',
					data: { "datas" : data },
					success: function (data) {
						if( data.data == 0 )
							alert("휴지통 상품삭제 처리되었습니다.\n단, 주문내역이 존재하는 휴지통 상품은 처리되지 않습니다.");
						else
							alert("상품삭제 중에 에러가 발생했습니다." + data.data);

						Search();
					},
					error: function(request, status, error) {
						console.log("error")
					}
				});
			}
		}
	</script>
@stop

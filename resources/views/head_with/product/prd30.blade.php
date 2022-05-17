@extends('head_with.layouts.layout')
@section('title','사방넷 상품연동')
@section('content')

<div class="page_tit">
	<h3 class="d-inline-flex">사방넷 상품연동</h3>
	<div class="d-inline-flex location">
		<span class="home"></span>
		<span>/ 상품·전시</span>
		<span>/ 사방넷</span>
		<span>/ 상품연동</span>
	</div>
</div>

<form method="get" name="search" id="search">
	<div id="search-area" class="search_cum_form">
		<div class="card mb-3">

			<div class="d-flex card-header justify-content-between">
				<h4>검색</h4>
				<div class="flax_box">
					<a href="#" id="search_sbtn" onclick="Search();" class="btn btn-sm btn-primary shadow-sm mr-1"><i class="fas fa-search fa-sm text-white-50"></i> 검색</a>
					<div id="search-btn-collapse" class="btn-group mb-0 mb-sm-0"></div>
				</div>
			</div>

			<div class="card-body">
				<div class="row">
					<div class="col-lg-4 inner-td">
						<div class="form-group">
							<label for="">상품상태 :</label>
							<div class="flax_box">
								<select name='s_goods_stat' class="form-control form-control-sm">
									<option value=''>전체</option>
									@foreach ($goods_stats as $goods_stats)
									<option value='{{ $goods_stats->code_id }}' @if( $goods_stats->code_id == $s_goods_stat ) selected @endif>{{ $goods_stats->code_val }}</option>
									@endforeach
								</select>
							</div>
						</div>
					</div>
					<div class="col-lg-4 inner-td">
						<div class="form-group">
							<label for="style_no">스타일넘버/상품코드</label>
							<div class="form-inline">
								<div class="form-inline-inner input_box">
									<input type='text' class="form-control form-control-sm ac-style-no search-enter" name='style_no' id="style_no" value="">
								</div>
								<span class="text_line">/</span>
								<div class="form-inline-inner input-box" style="width:47%">
									<div class="form-inline-inner inline_btn_box">
										<input type='text' class="form-control form-control-sm w-100" name='goods_no' id='goods_no' value=''>
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
								<input type='text' class="form-control form-control-sm ac-goods-nm" name='goods_nm' value=''>
							</div>
						</div>
					</div>
				</div>

				<div class="row">
					<div class="col-lg-4 inner-td">
						<div class="form-group">
							<label for="brand_cd">브랜드</label>
							<div class="form-inline inline_btn_box">
								<select id="brand_cd" name="brand_cd" class="form-control form-control-sm select2-brand"></select>
								<a href="#" class="btn btn-sm btn-outline-primary sch-brand"><i class="bx bx-dots-horizontal-rounded fs-16"></i></a>
							</div>
						</div>
					</div>
					<div class="col-lg-4 inner-td">
						<div class="form-group">
							<label for="">분류 :</label>
							<div class="flax_box">
								<select name='s_class' class="form-control form-control-sm">
									<option value=''>전체</option>
									@foreach ($s_class as $s_class)
									<option value='{{ $s_class->code }}'>{{ $s_class->val }}</option>
									@endforeach
								</select>
							</div>
						</div>
					</div>
					<div class="col-lg-4 inner-td">
						<div class="form-group">
							<label for="name">품목 :</label>
							<div class="flax_box">
								<select name="s_opt_kind_cd" class="form-control form-control-sm">
									<option value="">전체</option>
									@foreach ($items as $item)
									<option value="{{ $item->cd }}">{{ $item->val }}</option>
									@endforeach
								</select>
							</div>
						</div>
					</div>
				</div>

				<div class="row">
					<div class="col-lg-4 inner-td">
						<div class="form-group">
							<label for="formrow-email-input">연동대상</label>
							<div class="form-inline form-check-box">
								<div class="custom-control custom-checkbox">
									<input type="checkbox" name="s_product" id="s_product" class="custom-control-input" value="NE">
									<label class="custom-control-label" for="s_product">상품정보변경</label>
								</div>
								<div class="custom-control custom-checkbox">
									<input type="checkbox" name="s_stock" id="s_stock" class="custom-control-input" value="NE">
									<label class="custom-control-label" for="s_stock">재고불일치</label>
								</div>
							</div>
						</div>
					</div>
					<div class="col-lg-4 inner-td">
						<div class="form-group">
							<label for="item">연동여부</label>
							<div class="form-inline form-radio-box">
								<div class="custom-control custom-radio">
									<input type="radio" name="s_api_yn" id="s_api_yn1" class="custom-control-input" value="" @if($s_api_yn=='' ) checked @endif>
									<label class="custom-control-label" for="s_api_yn1" value="">모두</label>
								</div>
								<div class="custom-control custom-radio">
									<input type="radio" name="s_api_yn" id="s_api_yn2" class="custom-control-input" value="Y" @if($s_api_yn=='Y' ) checked @endif>
									<label class="custom-control-label" for="s_api_yn2" value="Y">연동 상품</label>
								</div>
								<div class="custom-control custom-radio">
									<input type="radio" name="s_api_yn" id="s_api_yn3" class="custom-control-input" value="N" @if($s_api_yn=='N' ) checked @endif>
									<label class="custom-control-label" for="s_api_yn3" value="N">미연동 상품</label>
								</div>
							</div>
						</div>
					</div>
					<div class="col-lg-4 inner-td">
						<div class="form-group">
							<label for="name">자료/정렬순서</label>
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
										<option value="goods_no" selected>상품번호</option>
										<option value="goods_nm">상품명</option>
										<option value="price">판매가</option>
										<option value="com_nm">공급업체</option>
										<option value="md_nm">담당MD별</option>
										<option value="upd_dm">수정일</option>
									</select>
								</div>
								<div class="form-inline-inner input_box sort_toggle_btn" style="width:24%;margin-left:1%;">
									<div class="btn-group" role="group">
										<label class="btn btn-primary primary" for="sort_desc" data-toggle="tooltip" data-placement="top" title="" data-original-title="내림차순"><i class="bx bx-sort-down"></i></label>
										<label class="btn btn-secondary" for="sort_asc" data-toggle="tooltip" data-placement="top" title="" data-original-title="오름차순"><i class="bx bx-sort-up"></i></label>
									</div>
									<input type="radio" name="ord" id="sort_desc" value="desc" checked="">
									<input type="radio" name="ord" id="sort_asc" value="asc">
								</div>
							</div>
						</div>
					</div>
				</div>

				<div class="row d-none search-area-ext">

					<div class="col-lg-4 inner-td">
						<div class="form-group">
							<label for="name">판매처</label>
							<div class="flax_box">
								<select name='site' class="form-control form-control-sm">
									<option value=''>전체</option>
									@foreach ($sale_places as $sale_place)
									<option value='{{ $sale_place->com_id }}'>{{ $sale_place->com_nm }}</option>
									@endforeach
								</select>
							</div>
						</div>
					</div>
					<div class="col-lg-4 inner-td">
						<div class="form-group">
							<label for="item">카테고리</label>
							<div class="form-inline inline_select_box">
								<div class="form-inline-inner select-box">
									<select name='cat_type' class="form-control form-control-sm">
										<option value='DISPLAY'>전시</option>
										<option value='ITEM'>용도</option>
									</select>
								</div>
								<div class="form-inline-inner input-box">
									<div class="form-inline inline_btn_box">
										<input type='hidden' name='cat_cd' id='cat_cd' value=''>
										<input type='text' class="form-control form-control-sm" name='cat_nm' id='cat_nm' value=''>
										<a href="#" class="btn btn-sm btn-outline-primary sch-category"><i class="bx bx-dots-horizontal-rounded fs-16"></i></a>
									</div>
								</div>
							</div>
						</div>
					</div>
					<div class="col-lg-4 inner-td">
						<div class="form-group">
							<label for="name">업체 :</label>

							<div class="form-inline inline_select_box">
								<div class="form-inline-inner input-box w-25 pr-1">
									<select id="s_com_type" name="s_com_type" class="form-control form-control-sm w-100">
										<option value="">전체</option>
										@foreach ($com_types as $com_type)
										<option value="{{ $com_type->code_id }}">{{ $com_type->code_val }}</option>
										@endforeach
									</select>
								</div>
								<div class="form-inline-inner input-box w-75">
									<div class="form-inline inline_btn_box">
										<select id="s_com_id" name="s_com_id" class="form-control form-control-sm select2-company" style="width:100%;"></select>
										<a href="#" class="btn btn-sm btn-outline-primary sch-company"><i class="bx bx-dots-horizontal-rounded fs-16"></i></a>
									</div>
								</div>
							</div>
						</div>
					</div>

				</div>

				<div class="row d-none search-area-ext">

					<div class="col-lg-4 inner-td">
						<div class="form-group">
							<label for="name">상단홍보글</label>
							<div class="flax_box">
								<input type='text' class="form-control form-control-sm" name='s_desc' value=''>
							</div>
						</div>
					</div>
					<div class="col-lg-4 inner-td">
						<div class="form-group">
							<label for="name">상품구분</label>
							<div class="flax_box">
								<select name='s_goods_type' class="form-control form-control-sm">
									<option value=''>전체</option>
									@foreach ($goods_types as $goods_type)
									<option value='{{ $goods_type->code_id }}'>{{ $goods_type->code_val }}</option>
									@endforeach
								</select>
							</div>
						</div>
					</div>

				</div>

			</div>
		</div>

		<div class="resul_btn_wrap mb-3">
			<a href="#" id="search_sbtn" onclick="Search();" class="btn btn-sm btn-primary shadow-sm mr-1"><i class="fas fa-search fa-sm text-white-50"></i> 검색</a>
			<div class="search_mode_wrap btn-group mr-2 mb-0 mb-sm-0"></div>
		</div>

	</div>
</form>
<!-- DataTales Example -->
<form method="get" name="f2">
	<div class="card shadow mb-0 last-card pt-2 pt-sm-0">
		<div class="card-body">
			<div class="card-title">
				<div class="filter_wrap">
					<div class="fl_box">
						<h6 class="m-0 font-weight-bold">총 : <span id="gd-total" class="text-primary">0</span>건</h6>
					</div>
					<div class="fr_box flax_box" style="">
						판매가의&nbsp;
						<input type='text' name='shop_margin' value='0' style="text-align:center;width:70px;height:30px;">&nbsp;
						<select name="shop_margin_type" style="width:50px;height:30px;padding-left:10px;">
							<option value="%">%</option>
							<option value="WON">원</option>
						</select> &nbsp;
						<button class="btn-sm btn btn-primary mr-1" onclick="SetShopPrice();return false;">판매가 설정</button>
						<button class="btn-sm btn btn-primary mr-1" onclick="SetPatternPop();return false;">상품상세 연동설정</button>
						<button class="btn-sm btn btn-primary mr-1" onclick="Cmder('add');return false;">상품등록 및 수정 연동</button>
						<button class="btn-sm btn btn-primary mr-1" onclick="Cmder('stock');return false;">상품재고 연동</button>
						<button class="btn-sm btn btn-primary" onclick="Cmder('delete');return false;">연동상품 삭제</button>
					</div>
				</div>
			</div>
			<div class="table-responsive">
				<div id="div-gd" style="height:calc(100vh - 370px);width:100%;" class="ag-theme-balham"></div>
			</div>
		</div>
	</div>
</form>
<script language="javascript">
	var columns = [{
			headerName: '#',
			width: 35,
			maxWidth: 100,
			// it is important to have node.id here, so that when the id changes (which happens
			// when the row is loaded) then the cell is refreshed.
			valueGetter: 'node.id',
			cellRenderer: 'loadingRenderer',
			pinned: 'left',
		},
		{
			headerName: '',
			headerCheckboxSelection: true,
			checkboxSelection: true,
			width: 28,
			pinned: 'left'
		},
		{
			headerName: "비제스트",
			children: [{
					field: "goods_no",
					headerName: "상품번호",
					width: 58,
					type: 'HeadGoodsNameType',
					pinned: 'left'
				},
				{
					field: "style_no",
					headerName: "스타일넘버",
					width: 85,
					pinned: 'left'
				},
			]
		},
		{
			headerName: "비제스트",
			children: [{
					field: "goods_type",
					headerName: "구분",
					width: 46
				},
				{
					field: "head_desc",
					headerName: "상단홍보글",
					width: 100
				},
				{
					field: "goods_nm",
					headerName: "상품명",
					width: 220,
					type: 'HeadGoodsNameType'
				},
				{
					field: "sale_stat_cl",
					headerName: "상태",
					width: 58,
					type: 'GoodsStateType'
				},
				{
					field: "qty",
					headerName: "재고",
					width: 46,
					type: 'numberType'
				},
				{
					field: "price",
					headerName: "판매가",
					width: 60,
					type: 'numberType'
				},
				{
					field: "wonga",
					headerName: "원가",
					width: 60,
					type: 'numberType'
				},
				{
					field: "margin_rate",
					headerName: "마진율",
					width: 60,
					cellStyle: {
						"text-align": "right"
					}
				},
				{
					field: "class",
					headerName: "분류",
					width: 80
				},
				{
					field: "upd_dm",
					headerName: "최근수정일시",
					width: 110
				},
			]
		},
		{
			headerName: "사방넷",
			children: [{
					field: "shop_goods_no",
					headerName: "상품번호",
					width: 58
				},
				{
					field: "shop_status",
					headerName: "상태",
					width: 60
				},
				{
					field: "shop_qty",
					headerName: "재고",
					width: 46,
					type: 'numberType'
				},
				{
					field: "shop_price",
					headerName: "판매가",
					width: 60,
					editable: true,
					cellStyle: {
						"text-align": "right",
						"background-color": "#FFFF99"
					}
				},
				{
					field: "shop_ut",
					headerName: "최근수정일시",
					width: 110
				},
				{
					field: "shop_stock_ut",
					headerName: "재고연동일시",
					width: 110
				},
				{
					field: "shop_result_no",
					headerName: "결과",
					width: 60
				},
				{
					field: "shop_result_msg",
					headerName: "내용",
					width: 120
				},
				{	field:"", headerName:"", width:"auto"}
			]
		},
	];

	function SetShopPrice() {
		var checkNodes = gx.getSelectedNodes();

		if (checkNodes.length === 0) {
			alert("수정할 데이터를 선택해주세요.");
			return;
		}

		var margin = str2int(document.f2.shop_margin.value);
		var cnt = 0;
		var type = document.f2.shop_margin_type.value;

		gx.getSelectedNodes().forEach((selectedRow, index) => {
			var nodeid = selectedRow.id;
			var price = str2int(selectedRow.data.price);

			if (type == "%") var shop_price = Math.round(price * (1 + margin / 100));
			else var shop_price = price + margin;

			checkData = selectedRow.data;
			checkData.shop_price = shop_price;

			gx.gridOptions.api.getRowNode(nodeid).setData(checkData);

			cnt++;
		});

		//StyleShopPrice(gx,row,gx.getColIdx("shop_price"));
	}

	function SetPatternPop() {
		const url = '/head/product/prd30/pattern';
		window.open(url, "_blank", "toolbar=no,scrollbars=yes,resizable=yes,status=yes,top=500,left=500,width=800,height=330");
	}

	function str2int(str) {
		if (str == "") return 0;
		try {
			return parseInt(str);
		} catch (e) {
			return 0;
		}
	}

	function StyleShopPrice(gx, row, col) {
		var price = str2int(gx.Cell(0, row, gx.getColIdx("price")));
		var shop_price = str2int(gx.Cell(0, row, col));

		var style = "bgcolor=#ffff99;"
		if (shop_price == 0 || shop_price < price) {
			style = "bgcolor=#FFACAC";
		} else if (shop_price > price) {
			//style="bgcolor=#FFACAC";
			gx.SetStyle(row, col, "fontcolor=#ff0000");
		}
		gx.SetStyle(row, col, style);
	}

	function Cmder(cmd) {
		if (cmd == "add") AddShop();
		else if (cmd == "stock") StockShop();
		else if (cmd == "delete") DeleteShopGoods();
	}


	var selectedData = [];
	var _total_cnt = 0;
	var _proc_cnt = 0;


	function AddShop() {
		var checkNodes = gx.getSelectedNodes();
		var price_chk = "N";
		var error_chk = "N";

		if (checkNodes.length === 0) {
			alert("연동할 상품을 선택해주세요.");
			return;
		}

		selectedData = checkNodes;
		_total_cnt = checkNodes.length;

		gx.getSelectedNodes().forEach((selectedRow, index) => {
			if (selectedRow.data.shop_price == "" || selectedRow.data.shop_price == 0) {
				price_chk = "Y";
			}

			if (selectedRow.data.shop_goods_no != "") {
				error_chk = "Y";
			}
		});

		if (price_chk == "Y") {
			alert('판매가를 정확하게 입력해 주십시요.');
			return false;
		}

		if (error_chk == "Y") {
			//기존 연동 상품은 상품 정보 재등록으로 진행됨.
			//alert('등록되지 않은 상품만 등록이 가능합니다.');
			//return false;
		}

		if (confirm("선택한 상품을 연동하시겠습니까?")) {
			AddShopGood();
		}
	}

	function AddShopGood() {
		if (selectedData.length > 0) {
			var row = selectedData.shift();
			var nodeid = row.id;
			var goods_no = row.data.goods_no;
			var goods_sub = 0;
			var shop_goods_no = row.data.shop_goods_no;
			var shop_price = row.data.shop_price;
			//var data = goods_no +"\t" + goods_sub  +"\t" + shop_goods_no  +"\t" + shop_price;

			checkData = row.data;
			checkData.shop_result_msg = "상품 재고 연동 중 ...";

			gx.gridOptions.api.getRowNode(nodeid).setData(checkData);

			$.ajax({
				async: true,
				type: 'put',
				url: '/head/product/prd30/add',
				data: {
					goods_no: goods_no,
					goods_sub: goods_sub,
					shop_goods_no: shop_goods_no,
					shop_price: shop_price
				},
				success: function(data) {
					cbAddShopGood(data, row);
				},
				error: function(request, status, error) {
					console.log("error")
				}
			});

		} else {
			alert('선택한 ' + _total_cnt + ' 개 상품 중 ' + _proc_cnt + ' 건을 연동하여 등록 또는 수정하였습니다.');
		}
	}

	function cbAddShopGood(res, row) {
		nodeid = row.id;
		checkData = row.data;

		checkData.shop_result_no = res.result_no;
		checkData.shop_result_msg = res.result_msg;

		gx.gridOptions.api.getRowNode(nodeid).setData(checkData);

		if (res.result_no == "200") {
			_proc_cnt++;
			setTimeout("AddShopGood()", 100);
		} else if (res.result_no == "0") {
			setTimeout("AddShopGood()", 100);
		} else if (res.result_no == "-1") {
			setTimeout("AddShopGood()", 100);
		} else if (res.result_no == "-2") {
			setTimeout("AddShopGood()", 100);
		} else {
			setTimeout("AddShopGood()", 100);
		}
	}

	function StockShop() {
		var checkNodes = gx.getSelectedNodes();
		var error_chk = "N";

		if (checkNodes.length === 0) {
			alert("재고 연동할 상품을 선택해주세요.");
			return;
		}

		selectedData = checkNodes;
		_total_cnt = checkNodes.length;

		gx.getSelectedNodes().forEach((selectedRow, index) => {
			if (selectedRow.data.shop_goods_no == "") {
				error_chk = "Y";
			}
		});

		if (error_chk == "Y") {
			alert('재고연동은 상품등록 된 상품만 가능합니다.');

			return false;
		}

		if (confirm("선택한 상품을 재고 연동하시겠습니까?")) {
			StockShopGood();
		}
	}

	function DeleteShopGoods() {
		var checkRows = gx.getSelectedRows();

		if (checkRows.length === 0) {
			alert("삭제하실 상품을 선택해주세요.");
			return;
		}

		if (confirm("선택하신 상품을 삭제 하시겠습니까?")) {
			$.ajax({
				async: true,
				type: 'put',
				url: '/head/product/prd30/delete',
				data: {
					data: JSON.stringify(checkRows),
				},
				success: function(data) {
					if (data.code == "200") {
						alert("선택하신 상품이 삭제 되었습니다.");
						Search();
					} else {
						alert("상품 삭제가 실패하였습니다.");
						console.log(data.code);
					}
				},
				error: function(request, status, error) {
					alert("시스템 에러입니다. 관리자에게 문의하여 주십시요.");
					console.log("error")
				}
			});
		}
	}

	function StockShopGood() {
		if (selectedData.length > 0) {
			var row = selectedData.shift();
			var nodeid = row.id;
			var goods_no = row.data.goods_no;
			var goods_sub = 0;
			var shop_goods_no = row.data.shop_goods_no;
			var shop_price = row.data.shop_price;
			//var data = goods_no +"\t" + goods_sub  +"\t" + shop_goods_no  +"\t" + shop_price;

			checkData = row.data;
			checkData.shop_result_msg = "상품 재고 연동 중 ...";

			gx.gridOptions.api.getRowNode(nodeid).setData(checkData);

			$.ajax({
				async: true,
				type: 'put',
				url: '/head/product/prd30/stock',
				data: {
					goods_no: goods_no,
					goods_sub: goods_sub,
					shop_goods_no: shop_goods_no,
					shop_price: shop_price
				},
				success: function(data) {
					cbStockShopGood(data, row);
				},
				error: function(request, status, error) {
					console.log("error")
				}
			});

		} else {
			alert('선택한 ' + _total_cnt + ' 개 상품 중 ' + _proc_cnt + ' 건을 연동하여 재고 연동 하였습니다.');
		}

	}

	function cbStockShopGood(res, row) {
		nodeid = row.id;
		checkData = row.data;

		checkData.shop_result_no = res.result_no;
		checkData.shop_result_msg = res.result_msg;

		gx.gridOptions.api.getRowNode(nodeid).setData(checkData);

		if (res.result_no == "200") {
			_proc_cnt++;
			setTimeout("StockShopGood()", 100);
		} else if (res.result_no == "0") {
			setTimeout("StockShopGood()", 100);
		} else if (res.result_no == "-1") {
			setTimeout("StockShopGood()", 100);
		} else if (res.result_no == "-2") {
			setTimeout("StockShopGood()", 100);
		} else {
			setTimeout("StockShopGood()", 100);
		}
	}
</script>
<script type="text/javascript" charset="utf-8">
	const pApp = new App('', {
		gridId: "#div-gd",
	});
	let gx;

	$(document).ready(function() {
		pApp.ResizeGrid(275);
		pApp.BindSearchEnter();
		let gridDiv = document.querySelector(pApp.options.gridId);
		gx = new HDGrid(gridDiv, columns);
		Search();
	});

	function Search() {
		let data = $('form[name="search"]').serialize();
		gx.Request('/head/product/prd30/search', data, 1);
	}
</script>
@stop
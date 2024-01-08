@extends('store_with.layouts.layout')
@section('title','주문내역관리')
@section('content')

<div class="page_tit">
	<h3 class="d-inline-flex">주문내역관리</h3>
	<div class="d-inline-flex location">
		<span class="home"></span>
		<span>판매관리</span>
		<span>/ 주문내역관리</span>
	</div>
</div>

<form method="get" name="search">
	<div id="search-area" class="search_cum_form">
		<div class="card mb-3">
			<div class="d-flex card-header justify-content-between">
				<h4>검색</h4>
				<div class="flax_box">
					<a href="javascript:void(0);" id="search_sbtn" onclick="Search();" class="btn btn-sm btn-primary shadow-sm mr-1"><i class="fas fa-search fa-sm text-white-50"></i> 검색</a>
					<div id="search-btn-collapse" class="btn-group mb-0 mb-sm-0"></div>
				</div>
			</div>
			<div class="card-body">
				<div class="row">
					<div class="col-lg-4 inner-td">
						<div class="form-group">
							<label for="user_yn">주문일자</label>
							<div class="date-switch-wrap form-inline">
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
								<div class="custom-control custom-switch date-switch-pos"  data-toggle="tooltip" data-placement="top" data-original-title="주문일자 사용">
									<input type="checkbox" class="custom-control-input" id="switch4" name="nud" checked="">
									<label class="" for="switch4" data-on-label="ON" data-off-label="OFF"></label>
								</div>
							</div>
						</div>
					</div>
					<div class="col-lg-4 inner-td">
						<div class="form-group">
							<label for="good_types">판매채널/매장구분</label>
							<div class="d-flex align-items-center">
								<div class="flex_box w-100">
									<select name='store_channel' id="store_channel" class="form-control form-control-sm" onchange="chg_store_channel();">
										<option value=''>전체</option>
										@foreach ($store_channel as $sc)
											<option value='{{ $sc->store_channel_cd }}' @if(@$p_store_channel === $sc->store_channel_cd) selected @endif>{{ $sc->store_channel }}</option>
										@endforeach
									</select>
								</div>
								<span class="mr-2 ml-2">/</span>
								<div class="flex_box w-100">
									<select id='store_channel_kind' name='store_channel_kind' class="form-control form-control-sm" @if(@$p_store_kind == '') disabled @endif>
										<option value=''>전체</option>
										@foreach ($store_kind as $sk)
											<option value='{{ $sk->store_kind_cd }}' @if(@$p_store_kind === $sk->store_kind_cd) selected @endif>{{ $sk->store_kind }}</option>
										@endforeach
									</select>
								</div>
							</div>
						</div>
					</div>
					<div class="col-lg-4 inner-td">
						<div class="form-group">
							<label for="store_no">주문매장</label>
							<div class="form-inline inline_btn_box">
								<input type='hidden' id="store_nm" name="store_nm" value="{{ @$store->store_nm }}">
								<select id="store_no" name="store_no" class="form-control form-control-sm select2-store"></select>
								<a href="javascript:void(0);" class="btn btn-sm btn-outline-primary sch-store"><i class="bx bx-dots-horizontal-rounded fs-16"></i></a>
							</div>
						</div>
					</div>
				</div>
{{--				<div class="row">--}}
{{--					<div class="col-lg-4 inner-td">--}}
{{--						<div class="form-group">--}}
{{--							<label>바코드</label>--}}
{{--							<div class="flex_box">--}}
{{--								<input type='text' id="prd_cd" name='prd_cd' class="form-control form-control-sm ac-style-no search-enter">--}}
{{--								<a href="#" class="btn btn-sm btn-outline-primary sch-prdcd" hidden><i class="bx bx-dots-horizontal-rounded fs-16"></i></a>--}}
{{--							</div>--}}
{{--						</div>--}}
{{--					</div>--}}
{{--					<div class="col-lg-4 inner-td">--}}
{{--						<div class="form-group">--}}
{{--							<label for="prd_cd">상품검색조건</label>--}}
{{--							<div class="form-inline">--}}
{{--								<div class="form-inline-inner input-box w-100">--}}
{{--									<div class="form-inline inline_btn_box">--}}
{{--										<input type='hidden' id="prd_cd_range" name='prd_cd_range'>--}}
{{--										<input type='text' id="prd_cd_range_nm" name='prd_cd_range_nm' class="form-control form-control-sm w-100 sch-prdcd-range" readonly style="background-color: #fff;">--}}
{{--										<a href="#" class="btn btn-sm btn-outline-primary sch-prdcd-range"><i class="bx bx-dots-horizontal-rounded fs-16"></i></a>--}}
{{--									</div>--}}
{{--								</div>--}}
{{--							</div>--}}
{{--						</div>--}}
{{--					</div>--}}
{{--					<div class="col-lg-4 inner-td">--}}
{{--						<div class="form-group">--}}
{{--							<label for="style_no">스타일넘버/온라인코드</label>--}}
{{--							<div class="form-inline">--}}
{{--								<div class="form-inline-inner input_box">--}}
{{--									<input type='text' class="form-control form-control-sm ac-style-no search-enter" name='style_no' id="style_no">--}}
{{--								</div>--}}
{{--								<span class="text_line">/</span>--}}
{{--								<div class="form-inline-inner input-box" style="width:47%">--}}
{{--									<div class="form-inline-inner inline_btn_box">--}}
{{--										<input type='text' class="form-control form-control-sm w-100 search-enter" name='goods_no' id='goods_no' value=''>--}}
{{--										<a href="#" class="btn btn-sm btn-outline-primary sch-goods_nos"><i class="bx bx-dots-horizontal-rounded fs-16"></i></a>--}}
{{--									</div>--}}
{{--								</div>--}}
{{--							</div>--}}
{{--						</div>--}}
{{--					</div>--}}
{{--				</div>--}}
				<div class="row">
{{--					<div class="col-lg-4 inner-td">--}}
{{--						<div class="form-group">--}}
{{--							<label for="goods_nm">상품명</label>--}}
{{--							<div class="flex_box">--}}
{{--								<input type='text' class="form-control form-control-sm ac-goods-nm search-enter" name='goods_nm' id="goods_nm" value='{{ @$goods_nm }}'>--}}
{{--							</div>--}}
{{--						</div>--}}
{{--					</div>--}}
					<div class="col-lg-4 inner-td">
						<div class="form-group">
							<label for="ord_no">주문번호</label>
							<div class="flax_box">
								<input type='text' class="form-control form-control-sm search-enter" name='ord_no' id="ord_no" value=''>
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
										<option value="o.ord_date">주문일자</option>
										<option value="o.ord_no">주문번호</option>
										<option value="o.user_nm">주문자명</option>
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
			<a href="javascript:void(0);" id="search_sbtn" onclick="Search();" class="btn btn-sm btn-primary shadow-sm mr-1"><i class="fas fa-search fa-sm text-white-50"></i> 검색</a>
			<div class="search_mode_wrap btn-group mr-2 mb-0 mb-sm-0"></div>
		</div>
	</div>
</form>

<div class="row show_layout">
	<div class="col-lg-4 pr-1">
		<div class="card shadow-none mb-0">
			<div class="card-header mb-0 pt-1 pb-1">
				<h5 class="m-0">주문내역 <span id="gd-total" class="text-primary">0</span> 건</h5>
			</div>
			<div class="card-body shadow pt-2">
				<div class="table-responsive">
					<div id="div-gd" class="ag-theme-balham"></div>
				</div>
			</div>
		</div>
	</div>
	<div class="col-lg-8">
		<div class="card shadow-none mb-0">
			<div class="card-header mb-0 d-flex justify-content-between align-items-left align-items-sm-center flex-column flex-sm-row">
				<h5 class="m-0 mb-3 mb-sm-0">주문상품내역<span id="select_ord_no" class="text-danger fs-14 ml-2"></span></h5>
				<div class="d-flex align-items-center justify-content-center justify-content-sm-end">
					<p class="text-secondary mr-2">* <span class="text-danger font-weight-bold">당월주문건</span>이고 <span class="text-danger font-weight-bold">정산대기</span> 상태인 <span class="text-danger font-weight-bold">교환/환불 처리되지 않은</span> 주문건만 수정 가능합니다.</p>
				    <button type="button" class="btn btn-sm btn-primary shadow-sm pl-2 mr-1" onclick="return saveProductRow();"><i class="fas fa-save fa-sm text-white-50 mr-1"></i> 저장</button>
					<button type="button" class="btn btn-sm btn-outline-primary shadow-sm" onclick="return resetProductRow();"><i class="fas fa-redo fa-sm mr-1"></i> 초기화</button> 
				</div>
			</div>
			<div class="card-body shadow pt-2">
				<div class="table-responsive">
					<div id="div-gd-product" class="ag-theme-balham"></div>
				</div>
			</div>
		</div>
	</div>
</div>

<script type="text/javascript" charset="utf-8">
	// 주문내역 컬럼
	const columns = [
		// {headerName: "No", pinned: "left", valueGetter: "node.id", cellRenderer: "loadingRenderer", width: 40, cellClass: 'hd-grid-code'},
		{field: "ord_date", headerName: "판매일자", pinned: 'left', width: 80, cellClass: 'hd-grid-code'},
		{field: "ord_state_nm", headerName: "판매구분", pinned: 'left', width: 55, cellClass: 'hd-grid-code',
			cellStyle: (params) => ({ color: params.data.ord_state == 30 ? '#4444ff' : '#ff4444' }),
		},
		{field: "ord_no", headerName: "주문번호", pinned: 'left', width: 150, cellStyle: StyleOrdNo, cellClass: 'hd-grid-code',
			cellRenderer: (params) => `<a href="javascript:setDetailOrder('${params.value}', '${params.data.ord_state}');">${params.value}</a>`,
		},
		{field: "store_nm", headerName: "주문매장", width: 90},
		{field: "user_nm", headerName: "주문자(아이디)", width: 90},
		{field: "qty", headerName: "판매수량", width: 55, type: "currencyType"},
		{field: "ord_amt", headerName: "판매금액", width: 80, type: "currencyType"},
		{field: "recv_amt", headerName: "실결제금액", width: 80, type: "currencyType"},
		{field: "dlv_amt", headerName: "배송비", width: 60, type: "currencyType"},
		{field: "pay_type", headerName: "결제방법", width: 80, cellClass: 'hd-grid-code'},
		{field: "ord_type", headerName: "주문구분", width: 60, cellClass: 'hd-grid-code'},
	];

	// 주문상품내역 컬럼
	const product_columns = [
		// {headerName: "No", pinned: "left", valueGetter: "node.id", cellRenderer: "loadingRenderer", width: 40, cellClass: 'hd-grid-code'},
		{field: "ord_opt_no", headerName: "일련번호", pinned: 'left', width: 60, type: 'StoreOrderNoType'},
		{field: "clm_state_nm", headerName: "클레임상태", pinned: 'left', width: 65, cellStyle: StyleClmState},
		{field: "prd_cd", headerName: "바코드", pinned: 'left', width: 120, cellClass: 'hd-grid-code'},
		{field: "goods_no", headerName: "온라인코드", pinned: 'left', width: 70, cellClass: 'hd-grid-code'},
		{field: "style_no", headerName: "스타일넘버", pinned: 'left', width: 70, cellClass: 'hd-grid-code'},
		{field: "goods_nm", headerName: "상품명", pinned: 'left', width: 150, type: "HeadGoodsNameType"},
		{field: "goods_nm_eng", headerName: "상품명(영문)", width: 150},
		{field: "prd_cd_p", headerName: "품번", width: 90, cellClass: 'hd-grid-code'},
		{field: "color", headerName: "컬러", width: 55, cellClass: 'hd-grid-code'},
		{field: "size", headerName: "사이즈", width: 55, cellClass: 'hd-grid-code'},
		{field: "opt_val", headerName: "옵션", width: 130},
		{field: "qty", headerName: "판매수량", width: 55, type: "currencyType", editable: getEditableYn, cellClass: (params) => ([ getEditableYn(params) ? 'hd-grid-edit' : '', 'hd-grid-number' ]), onCellValueChanged: setProductRow},
		{field: "wonga", headerName: "원가", width: 85, type: "currencyType"},
		{field: "goods_sh", headerName: "정상가", width: 85, type: "currencyType"},
		{field: "goods_price", headerName: "자사몰판매가", width: 85, type: "currencyType"},
		{field: "price", headerName: "현재가", width: 85, type: "currencyType"},
		{field: "sale_dc_rate", headerName: "할인율(%)", width: 65, type: "currencyType"},
		{field: "sale_kind_nm", headerName: "판매유형", width: 100, editable: getEditableYn, cellClass: (params) => getEditableYn(params) ? 'hd-grid-edit' : '',
			onCellValueChanged: function (params) {
				if (params.oldValue !== params.newValue) {
					let sale_kind = params.data.sale_kinds.filter(st => st.sale_type_nm === params.newValue)[0];
					params.data.sale_kind_cd = sale_kind?.code_id || '';
				}
				setProductRow(params);
			},
			cellEditorSelector: function(params) {
				return {
					component: 'agRichSelectCellEditor',
					params: {
						values: params.data.sale_kinds?.map(s => s.sale_type_nm) || [],
					},
				};
			},
		},
		{field: "sale_price", headerName: "판매단가", width: 80, type: "currencyType", editable: getEditableYn, cellClass: (params) => ([ getEditableYn(params) ? 'hd-grid-edit' : '', 'hd-grid-number' ]), onCellValueChanged: setProductRow},
		{field: "dc_rate", headerName: "할인율(%)", width: 65, type: "currencyType"},
		{field: "ord_amt", headerName: "판매금액", width: 80, type: "currencyType"},
		{field: "coupon_amt", headerName: "쿠폰할인", width: 65, type: "currencyType"},
		{field: "point_amt", headerName: "적립금사용", width: 65, type: "currencyType"},
		{field: "recv_amt", headerName: "실결제금액", width: 80, type: "currencyType"},
		{field: "pr_code_nm", headerName: "판매처수수료", width: 80, editable: getEditableYn, cellClass: (params) => getEditableYn(params) ? 'hd-grid-edit' : '',
			onCellValueChanged: function (params) {
				if (params.oldValue !== params.newValue) {
					let pr_code = params.data.pr_codes.filter(pc => pc.pr_code_nm === params.newValue)[0];
					params.data.pr_code_cd = pr_code?.pr_code || '';
				}
			},
			cellEditorSelector: function(params) {
				return {
					component: 'agRichSelectCellEditor',
					params: {
						values: params.data.pr_codes?.map(s => s.pr_code_nm) || [],
					},
				};
			},
		},
		{field: "memo", headerName: "메모", width: 150, editable: getEditableYn, cellClass: (params) => getEditableYn(params) ? 'hd-grid-edit' : '', onCellValueChanged: setProductRow},
	];

	const pApp = new App('', { gridId: "#div-gd", height: 275 });
	const pApp2 = new App('', { gridId: "#div-gd-product", height: 275 });
	let gx;
	let gx2;
	let cur_ord_no = '', cur_ord_state = '';

	$(document).ready(function() {
		pApp.ResizeGrid(275);
		pApp.BindSearchEnter();
		let gridDiv = document.querySelector(pApp.options.gridId);
		gx = new HDGrid(gridDiv, columns);

		pApp2.ResizeGrid(275);
		let gridDiv2 = document.querySelector(pApp2.options.gridId);
		gx2 = new HDGrid(gridDiv2, product_columns);

		// 판매채널 선택되지않았을때 매장구분 disabled처리하는 부분
		load_store_channel();

		Search();
	});

	// 주문내역 조회
	function Search() {
		let data = $('form[name="search"]').serialize();
		gx.Request('/store/order/ord05/search', data, 1);
	}
	
	// 해당 주문상품정보 수정가능여부 판단
	function getEditableYn(params) {
		return params.data.is_editable === 'Y' && params.data.ord_state == 30 && params.data.clm_state < 40;
	}
	
	// 주문상품내역 조회
	function setDetailOrder(ord_no, ord_state) {
		$("#select_ord_no").text(ord_no);
		let data = 'ord_state=' + (ord_state || '');
		gx2.Request('/store/order/ord05/search/' + ord_no, data, 1, function (e) {
			let nodes = [];
			gx2.gridOptions.api.forEachNode(node => {
				node.data.pr_codes = e.head.pr_codes;
				node.data.sale_kinds = e.head.sale_kinds;
				node.data.ori_qty = node.data.qty;
				node.data.ori_sale_price = node.data.sale_price;
				node.data.ori_dc_rate = node.data.dc_rate;
				node.data.ori_ord_amt = node.data.ord_amt;
				node.data.ori_recv_amt = node.data.recv_amt;
				node.data.ori_sale_kind_amt = node.data.sale_kind_amt;
				node.data.ori_sale_kind_cd = node.data.sale_kind_cd;
				node.data.ori_sale_kind_nm = node.data.sale_kind_nm;
				node.data.ori_pr_code_cd = node.data.pr_code_cd;
				node.data.ori_pr_code_nm = node.data.pr_code_nm;
				node.data.ori_memo = node.data.memo;
				nodes.push(node);
			});
			gx2.gridOptions.api.redrawRows({ rowNodes: nodes });
			cur_ord_no = ord_no;
			cur_ord_state = ord_state;
		});
	}
	
	// 주문상품내역 수정 시, 새로 계산된 정보 반영
	function setProductRow(params) {
		if (['qty', 'sale_price', 'sale_kind_nm'].includes(params.column.colId)) {
			let goods_price	= 0;
			let price		= 0;
			let sale_kind_amt = params.data.sale_kind_amt;

			if (params.column.colId === 'sale_kind_nm' && params.data.sale_kind_nm !== '') {
				let sale_kind = params.data.sale_kinds.filter(st => st.sale_type_nm === params.data.sale_kind_nm)[0] || {};

				if(sale_kind.sale_apply === 'tag'){
					goods_price = parseInt(params.data.goods_sh);
					price		= parseInt(params.data.goods_sh);
				}else{
					goods_price = parseInt(params.data.goods_price);
					price		= params.data.price;
				}

				sale_kind_amt = sale_kind.amt_kind === 'per' ? (goods_price * sale_kind.sale_per / 100) : (sale_kind.sale_amt || 0) * 1;
				params.data.sale_kind_amt = sale_kind_amt;
				params.data.sale_price = price - sale_kind_amt;
			}

			params.data.dc_rate = (1 - (params.data.sale_price / params.data.goods_sh)) * 100;
			params.data.ord_amt = params.data.qty * params.data.sale_price;
			params.data.recv_amt = params.data.ord_amt + (params.data.coupon_amt || 0) + (params.data.point_amt || 0);
			gx2.gridOptions.api.redrawRows({ rowNodes: [ params.node ] });
			gx2.setFocusedWorkingCell();
		}
	}
	
	// 주문상품내역 초기화
	function resetProductRow() {
		let nodes = [];
		gx2.gridOptions.api.forEachNode(node => {
			node.data.qty = node.data.ori_qty;
			node.data.sale_price = node.data.ori_sale_price;
			node.data.dc_rate = node.data.ori_dc_rate;
			node.data.ord_amt = node.data.ori_ord_amt;
			node.data.recv_amt = node.data.ori_recv_amt;
			node.data.sale_kind_amt = node.data.ori_sale_kind_amt;
			node.data.sale_kind_cd = node.data.ori_sale_kind_cd;
			node.data.sale_kind_nm = node.data.ori_sale_kind_nm;
			node.data.pr_code_cd = node.data.ori_pr_code_cd;
			node.data.pr_code_nm = node.data.ori_pr_code_nm;
			node.data.memo = node.data.ori_memo;
			nodes.push(node);
		});
		gx2.gridOptions.api.redrawRows({ rowNodes: nodes });
	}
	
	// 주문상품내역 저장
	function saveProductRow() {
		if (!confirm("해당 주문건의 상품정보를 저장하시겠습니까?\n(저장된 정보는 되돌릴 수 없습니다.)")) return;

		let rows = gx2.getRows()
			.filter(row => row.is_editable === 'Y' && row.ord_state == 30 && row.clm_state < 40)
			.map(row => ({ 
				ord_opt_no: row.ord_opt_no,
				qty: row.qty || 0, 
				sale_kind_cd: row.sale_kind_cd || '',
				ori_sale_price: row.ori_sale_price || 0,
				sale_price: row.sale_price || 0, 
				pr_code_cd: row.pr_code_cd || '', 
				memo: row.memo || ''  
			}));

		$.ajax({
			async: true,
			dataType: "json",
			type: 'post',
			url: "/store/order/ord05/update",
			data: { data: rows },
			success: function (res) {
				if (res.code == '200') {
					alert("저장되었습니다.");
					setDetailOrder(cur_ord_no, cur_ord_state);
				}
				else alert(res.msg);
			},
			error: function(e) {
				console.log('[error] ' + e.responseText);
				let err = JSON.parse(e.responseText);
				if (err.hasOwnProperty("code") && err.code == "500") {
					alert(err.msg);
				}
			},
		});
	}
</script>

@stop

@extends('store_with.layouts.layout-nav')
@section('title','특약(온라인) 판매내역 - ' . @$store_nm)
@section('content')

<form method="post" name="search">
	<div id="search-area" class="search_cum_form">
		<div class="card mb-3">
			<div class="d-flex card-header justify-content-between">
				<h4>특약(온라인) 판매내역 - {{ @$store_nm }}</h4>
				<div class="flex_box">
					<a href="javascript:void(0);" onclick="Search();" class="btn btn-sm btn-primary shadow-sm mr-1"><i class="fas fa-search fa-sm text-white-50"></i> 검색</a>
					<a href="javascript:void(0);" onclick="gridDownload();" class="btn btn-sm btn-outline-primary shadow-sm"><i class="fas fa-download fa-sm"></i> 자료받기</a>
				</div>
			</div>
			<div class="card-body">
				<div class="row">
					<div class="col-lg-4">
						<div class="form-group">
							<label for="sdate">판매일자</label>
							<div class="docs-datepicker flex_box">
								<div class="input-group">
									<input type="text" id="sdate" class="form-control form-control-sm docs-date month" name="sdate" value="{{ $sdate }}" autocomplete="off">
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
					<div class="col-lg-4">
						<div class="form-group">
							<label for="store_no">매장명</label>
							<div class="form-inline inline_btn_box">
								<input type='hidden' id="store_nm" name="store_nm">
								<select id="store_no" name="store_no" class="form-control form-control-sm select2-store"></select>
								<a href="javascript:void(0);" class="btn btn-sm btn-outline-primary sch-store"><i class="bx bx-dots-horizontal-rounded fs-16"></i></a>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="resul_btn_wrap mb-3">
			<a href="javascript:void(0);" onclick="Search();" class="btn btn-sm btn-primary shadow-sm mr-1"><i class="fas fa-search fa-sm text-white-50"></i> 검색</a>
			<a href="javascript:void(0);" onclick="gridDownload();" class="btn btn-sm btn-outline-primary shadow-sm"><i class="fas fa-download fa-sm"></i> 자료받기</a>
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
	const CENTER = { 'text-align': 'center' };
	const columns = [
		{field: "num", headerName: "#", type: 'NumType', pinned: 'left', aggSum: "합계", cellStyle: CENTER, width: 40,
			cellRenderer: (params) => params.node.rowPinned === 'top' ? '합계' : parseInt(params.value) + 1,
		},
		{field: "sale_type", headerName: "매출구분", width: 55, pinned: 'left', cellStyle: CENTER},
		{field: "state_date", headerName: "일자", width: 80, pinned: 'left', cellStyle: CENTER},
		{field: "ord_no", headerName: "주문번호", width: 140, pinned: 'left'},
		{field: "ord_opt_no", headerName: "일련번호", width: 60, cellStyle: CENTER, type: 'StoreOrderNoType', pinned: 'left'},
		{field: "multi_order", headerName: "복수주문", width: 60, pinned: 'left',
			cellStyle: (params) => ({ ...CENTER, "background-color": params.value === 'Y' ? "#ffff99" : "none" }),
			cellRenderer: (params) => params.node.rowPinned === 'top' 
				? '' : params.value === 'Y' 
					? `<a href="javascript:void(0);" onclick="return openStoreOrder('${params.data.ord_opt_no}');">${params.value}</a>` : "-",
		},
		{field: "prd_cd", headerName: "상품코드", width: 125, cellStyle: CENTER},
		{field: "goods_no", headerName: "상품번호",	width: 70, cellStyle: CENTER},
		{field: "style_no",	headerName: "스타일넘버", width: 70, cellStyle: CENTER},
		{field: "goods_nm", headerName: "상품명", width: 180, type: 'HeadGoodsNameType'},
		{field: "prd_cd_p", headerName: "코드일련",	width: 100, cellStyle: CENTER},
		{field: "color", headerName: "컬러", width: 55, cellStyle: CENTER},
		{field: "size", headerName: "사이즈", width: 55, cellStyle: CENTER},
		{field: "goods_opt", headerName: "옵션", width: 150},
		{field: "qty", headerName: "수량", width: 50, type: 'currencyType', aggregation: true},
		{field: "goods_sh", headerName: "정상가", width: 70, type: 'currencyType'},
		{field: "price", headerName: "판매가", width: 70, type: 'currencyType'},
		{field: "sale_amt",	headerName: "판매금액",	width: 90, type: 'currencyType', aggregation: true}, // 판매금액 = 수량 * 판매가
		{field: "dc_amt", headerName: "할인금액",	width: 90, type: 'currencyType', aggregation: true}, // 할인금액 = dc_apply_amt
		{field: "coupon_amt", headerName: "쿠폰금액",	width: 90, type: 'currencyType', aggregation: true}, // 쿠폰금액 = coupon_apply_amt
		{field: "recv_amt", headerName: "매출금액",	width: 90, type: 'currencyType', aggregation: true, 
			cellStyle: (params) => ({"background-color": params.node.rowPinned === 'top' ? "none" : "#E9EFFF"})
		},
		{field: "ord_type_nm", headerName: "주문구분", width: 60, cellStyle: CENTER},
		{field: "pr_code_nm", headerName: "행사구분", width: 60, cellStyle: CENTER},
		{field: "sale_place_nm", headerName: "판매처", width: 80, cellStyle: CENTER},
		{field: "store_cd",	headerName: "배송매장코드", width: 80, cellStyle: CENTER},
		{field: "store_nm",	headerName: "배송매장명", width: 100},
		{field: "user_nm", headerName: "주문자", width: 60, cellStyle: CENTER},
		{field: "pay_type_nm",	headerName: "결제방법",	width: 70, cellStyle: CENTER},
		{field: "tax_yn", headerName: "과세", width: 40, cellStyle: CENTER},
		// {field: "dlv_amt", headerName: "배송비", width: 80, type: 'currencyType', aggregation: true},
		{field: "ord_state_nm", headerName: "주문상태", width: 70, cellStyle: StyleOrdState},
		// {field: "clm_state_nm",headerName: "클레임상태", width: 70, cellStyle: StyleClmState},
		{field: "ord_date",	headerName: "주문일", width: 80, cellStyle: CENTER},
		{field: "dlv_end_date", headerName: "배송완료일", width: 80},
		// {field: "clm_end_date", headerName: "클레임완료일",	width: 80},
		{width: "auto"}
	];
</script>
<script type="text/javascript" charset="utf-8">
	const pApp = new App('', { gridId:"#div-gd" });
	let gx;

	$(document).ready(function() {

		// 매장명 초기화 - select2, autocomplete input에 값 할당
        $("#store_no").select2({data:[{
            id: "{{ @$store_cd ? @$store_cd : '' }}",
            text: "{{ @$store_nm ? @$store_nm : '' }}"
        }], tags: true});
        document.search.store_nm.value = "{{ @$store_nm ? @$store_nm : '' }}";

		// ag-grid 설정
		pApp.ResizeGrid(95);
		pApp.BindSearchEnter();
		let gridDiv = document.querySelector(pApp.options.gridId);
        let options = {
			getRowStyle: (params) => {
				if (params.node.rowPinned === 'top') {
					return { 'background': '#eee', 'font-weight': 'bold' }
				}
			}
        };
		gx = new HDGrid(gridDiv, columns, options);
		gx.Aggregation({
			"sum": "top"
		});
		Search();
	});

	function Search() {
		let data = $('form[name="search"]').serialize();
		gx.Request('/store/account/acc06/show-online/search', data, -1);
	}

	function gridDownload() {
		gx.Download("특약(온라인)_판매내역_" + "{{ @$store_nm ?? '' }}" + ".csv");
	}
</script>

@stop
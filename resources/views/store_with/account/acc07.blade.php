@extends('store_with.layouts.layout')
@section('title','매장중간관리자마감정산')
@section('content')

<div class="page_tit">
	<h3 class="d-inline-flex">매장중간관리자마감정산</h3>
	<div class="d-inline-flex location">
		<span class="home"></span>
		<span>/ 매장관리</span>
		<span>/ 정산/마감관리</span>
	</div>
</div>

<form method="get" name="search">
	<div id="search-area" class="search_cum_form">
		<div class="card mb-3">
			<div class="d-flex card-header justify-content-between">
				<h4>검색</h4>
				<div class="flax_box">
					<a href="#" id="search_sbtn" onclick="Search();" class="btn btn-sm btn-primary shadow-sm mr-1"><i class="fas fa-search fa-sm text-white-50"></i> 검색</a>
					<a href="#" onclick="gx.Download();" class="btn btn-sm btn-outline-primary shadow-sm pl-2 mr-1"><i class="bx bx-download fs-16"></i> 엑셀다운로드</a>
					<div id="search-btn-collapse" class="btn-group mb-0 mb-sm-0"></div>
				</div>
			</div>

			<div class="card-body">
				<div class="row">
					<div class="col-lg-4">
						<div class="form-group">
							<label for="sdate">마감연월</label>
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
					<div class="col-lg-4">
						<div class="form-group">
							<label for="store_kind">매장종류</label>
							<div class="flex_box">
								<select name='store_kind' class="form-control form-control-sm">
									<option value=''>전체</option>
									@foreach ($store_kinds as $store_kind)
										<option value='{{ $store_kind->code_id }}'>{{ $store_kind->code_val }}</option>
									@endforeach
								</select>
							</div>
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-lg-4">
						<div class="form-group">
							<label for="store_cd">매장명</label>
							<div class="form-inline inline_btn_box">
								<select id="store_cd" name="store_cd" class="form-control form-control-sm select2-store"></select>
								<a href="javascript:void(0);" class="btn btn-sm btn-outline-primary sch-store"><i class="bx bx-dots-horizontal-rounded fs-16"></i></a>
							</div>
						</div>
					</div>
					<div class="col-lg-4">
                        <div class="form-group">
                            <label for="closed_yn">마감상태</label>
                            <div class="flax_box">
                                <select name="closed_yn" id="closed_yn" class="form-control form-control-sm">
									<option value="">전체</option>
									<option value="N">마감추가</option>
									<option value="Y">마감완료</option>
                                </select>
                            </div>
                        </div>
                    </div>
				</div>
			</div>
		</div>

		<div class="resul_btn_wrap mb-3">
			<a href="#" id="search_sbtn" onclick="Search();" class="btn btn-sm btn-primary shadow-sm mr-1"><i class="fas fa-search fa-sm text-white-50"></i> 검색</a>
			<a href="#" onclick="gx.Download();" class="btn btn-sm btn-outline-primary shadow-sm pl-2 mr-1"><i class="bx bx-download fs-16"></i> 엑셀다운로드</a>
			<div id="search-btn-collapse" class="btn-group mb-0 mb-sm-0"></div>
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
{{-- 
<div class="card shadow">
	<div class="card-body">
		<div class="card-title">
			<h6 class="m-0 font-weight-bold text-primary fas fa-question-circle"> Help</h6>
		</div>
		<ul class="mb-0">
			<li>매출금액 = 판매금액 - 클레임금액 - 할인금액 - 쿠폰금액(업체부담) + 배송비 + 기타정산액</li>
			<li>판매수수료 = 수수료지정 : 판매가격 * 수수료율, 공급가지정 : 판매가격 - 공급가액</li>
			<li>수수료 = 판매수수료 - 할인금액</li>
			<li>정산금액 = 매출금액 - 수수료</li>
			<li>쿠폰금액(본사부담) = 판매촉진비 수수료 매출 신고</li>
			<li>카드수수료 등 수수료 부담의 주체가 귀사에 있으므로 입점업체의 경우 매출 신고 시에 해당 매출금액에 대하여 현금성으로 신고</li>
		</ul>
	</div>
</div> --}}

<script type="text/javascript" charset="utf-8">
	const CENTER = { 'text-align': 'center' };
	const columns = [
		{ field: "num", headerName: "#", type: 'NumType', pinned: 'left', aggSum: "합계", cellStyle: CENTER, width: 40,
			cellRenderer: (params) => params.node.rowPinned === 'top' ? '합계' : parseInt(params.value) + 1,
		},
		{ field: "closed_yn", headerName: "마감상태", width: 60, pinned: 'left', cellStyle: (params) => ({...CENTER, "color": params.value == "Y" ? "#ff0000" : ""}),
			cellRenderer: (params) => params.node.rowPinned === 'top' ? '' : params.value === "Y" ? "마감완료" : "마감추가",
		},
		{ field: "closed_day", headerName: "마감대상기간", pinned: 'left', width: 140, cellStyle: {...CENTER, "text-decoration": "underline"},
			cellRenderer: (params) => params.node.rowPinned === 'top' ? '' : `<a href="javascript:void(0);" onclick="return openDetailPopup('${params.data.idx}');">${params.data.sday} ~ ${params.data.eday}</a>`,
		},
        { field: "store_cd", headerName: "매장코드", pinned: 'left', width: 55, cellStyle: CENTER },
        { field: "store_nm", headerName: "매장명", pinned: 'left', width: 150 },
        { field: "manager_nm", headerName: "매니저", width: 55, cellStyle: CENTER },
		{ field: "sale_amt", headerName: "판매금액", width: 90, type: "currencyType", aggregation: true },
		{ field: "clm_amt", headerName: "클레임금액", width: 90, type: "currencyType", aggregation: true },
		{ field: "dc_amt", headerName: "할인금액", width: 90, type: "currencyType", aggregation: true },
		{ headerName: "쿠폰금액",
			children: [
				{ field: "coupon_com_amt", headerName: "업체부담", width: 90, type: "currencyType", aggregation: true },
				{ field: "allot_amt", headerName: "본사부담", width: 90, type: "currencyType", aggregation: true },
			]
        },
		{ field: "dlv_amt", headerName: "배송비", width: 90, type: "currencyType", aggregation: true },
		// { field: "etc_amt", headerName: "기타정산액", width: 90, type: "currencyType", aggregation: true },
        { headerName: "매출",
			children: [
				{ field: "sale_net_taxation_amt", headerName: "과세", width: 90, type: "currencyType", aggregation: true },
				{ field: "sale_net_taxfree_amt", headerName: "비과세", width: 50, type: "currencyType", aggregation: true },
				{ field: "sale_net_amt", headerName: "매출합계", width: 90, type: "currencyType", aggregation: true },
				{ field: "sales_amt_except_vat", headerName: "매출합계(-VAT)", width: 90, type: "currencyType", aggregation: true },
			]
        },
		{ headerName: "중간관리자 수수료",
            children: [
				{ field: "fee_JS1", headerName: "정상1", width: 90, type: "currencyType", aggregation: true },
				{ field: "fee_JS2", headerName: "정상2", width: 90, type: "currencyType", aggregation: true },
				{ field: "fee_JS3", headerName: "정상3", width: 90, type: "currencyType", aggregation: true },
				{ field: "fee_TG", headerName: "특가", width: 90, type: "currencyType", aggregation: true },
				{ field: "fee_YP", headerName: "용품", width: 90, type: "currencyType", aggregation: true },
				{ field: "fee_OL", headerName: "특가(온라인)", width: 90, type: "currencyType", aggregation: true },
				{ field: "fee_amt", headerName: "수수료소계", width: 90, type: "currencyType", aggregation: true },
			]
		},
		{ field: "extra_amt", headerName: "기타재반", width: 90, type: "currencyType", aggregation: true },
		{ field: "fee", headerName: "정산금액", width: 100, type: "currencyType", cellStyle: {"color": "#dd0000"}, aggregation: true },
		{ field: "pay_day", headerName: "정산지급일", width: 80, cellStyle: CENTER },
		{ field: "tax_no", headerName: "세금계산서", width: 80, cellStyle: CENTER },
		{ field: "admin_nm", headerName: "마감자", width: 60, cellStyle: CENTER },
		{ field: "closed_date", headerName: "마감일자", width: 80, cellStyle: CENTER },
		{ width: "auto" },
	];

    // var columns_d = [
	// 	{field: "num",			headerName: "#", type:'NumType', pinned: 'left'},
	// 	{field: "closed_yn",		headerName: "마감",			width:65, pinned: 'left', cellStyle: { 'text-align': 'center' }},
	// 	{field: "closed_day",			headerName: "마감일자",		width:140, pinned: 'left', cellStyle: { 'text-align': 'center' },
	// 		cellRenderer: (params) => {
	// 			if (params.node.rowPinned) return params.value;
	// 			if (params.value != undefined) {
	// 				return '<a href="#" class="pop" onClick="popDetail(\''+ params.data.idx +'\')">' + params.value+'</a>';
	// 			}
	// 		}
	// 	},
	// 	{field: "store_nm",		headerName: "매장명",	width:140 },
	// 	// {field: "margin_type",	headerName: "수수료지정",	width:100},
	// 	{field: "sale_amt",		headerName: "판매금액",		width:100, type: 'currencyType', aggregation: true},
	// 	{field: "clm_amt",		headerName: "클레임금액",	width:100, type: 'currencyType', aggregation: true},
	// 	{field: "dc_amt",	headerName: "할인금액",		width:90, type: 'currencyType', aggregation: true},
	// 	{
	// 		headerName: '쿠폰금액',
	// 		children: [{
	// 				field: "coupon_com_amt",
	// 				headerName: "(업체부담)",
	// 				type: 'currencyType',
	// 				aggregation: true
	// 			}
	// 		]
	// 	},
	// 	{field: "dlv_amt",		headerName: "배송비",		width:90, type: 'currencyType', aggregation: true},
	// 	{field: "etc_amt",	headerName: "기타정산액",	width:110, type: 'currencyType', aggregation: true},
	// 	{
	// 		headerName: '매출금액',
	// 		children: [{
	// 				field: "sale_net_taxation_amt",
	// 				headerName: "과세",
	// 				width:100,
	// 				type: 'currencyType',
	// 				aggregation: true
	// 			},
	// 			{
	// 				field: "sale_net_taxfree_amt",
	// 				headerName: "비과세",
	// 				type: 'currencyType',
	// 				aggregation: true
	// 			},
	// 			{
	// 				field: "sale_net_amt",
	// 				headerName: "소계",
	// 				width:100,
	// 				type: 'currencyType',
	// 				aggregation: true
	// 			},
	// 		]
	// 	},
	// 	{field: "tax_amt",	headerName: "부가세",	type: 'currencyType',	hide:true},
	// 	{
	// 		headerName: '수수료',
	// 		children: [{
	// 				field: "fee",
	// 				headerName: "판매수수료",
	// 				type: 'currencyType',
	// 				aggregation: true
	// 			},
	// 			{
	// 				field: "fee_dc_amt",
	// 				headerName: "할인금액",
	// 				type: 'currencyType',
	// 				aggregation: true
	// 			},
	// 			{
	// 				field: "fee_net",
	// 				headerName: "소계",
	// 				type: 'currencyType',
	// 				aggregation: true,
	// 				width: 100
	// 			},
	// 		]
	// 	},
	// 	{field: "acc_amt",	headerName: "정산금액",	width:100,	type: 'currencyType', aggregation: true},
	// 	{
	// 		headerName: '쿠폰금액',
	// 		children: [{
	// 				field: "allot_amt",
	// 				headerName: "(본사부담)",
	// 				type: 'currencyType',
	// 				aggregation: true
	// 			}
	// 		]
	// 	},
	// 	// {field: "tax_day", headerName: "세금계산서" },
	// 	{field: "pay_day", headerName: "지급일" },
	// 	{ width: 'auto' }
	// ];

	const pApp = new App('', { gridId: "#div-gd", height: 265 });
	let gx;

    $(document).ready(function() {
        pApp.ResizeGrid(265);
        pApp.BindSearchEnter();
        let gridDiv = document.querySelector(pApp.options.gridId);
        let options = {
            getRowStyle: (params) => {
                if (params.node.rowPinned === 'top') {
                    return { 'background': '#eee' }
                }
            }
        };
        gx = new HDGrid(gridDiv, columns, options);

		Search();
    });

	function Search() {
        let data = $('form[name="search"]').serialize();
        gx.Aggregation({ "sum": "top" });
        gx.Request('/store/account/acc07/search', data, -1);
    }

	const openDetailPopup = (idx) => {
		const url = '/store/account/acc07/show/' + idx;
		window.open(url, "_blank", "toolbar=no,scrollbars=yes,resizable=yes,status=yes,top=100,left=100,width=2100,height=1200");
	};
</script>

@stop
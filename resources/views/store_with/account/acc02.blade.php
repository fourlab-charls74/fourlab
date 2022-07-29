@extends('store_with.layouts.layout')
@section('title','정산')
@section('content')

<div class="page_tit">
	<h3 class="d-inline-flex">정산</h3>
	<div class="d-inline-flex location">
		<span class="home"></span>
		<span>/ 입점/정산</span>
		<span>/ 정산</span>
	</div>
</div>

<form method="get" name="search">
	<div id="search-area" class="search_cum_form">
		<div class="card mb-3">

			<div class="d-flex card-header justify-content-between">
				<h4>검색</h4>
				<div class="flax_box">
					<a href="#" id="search_sbtn" onclick="Search();" class="btn btn-sm btn-primary shadow-sm mr-1"><i class="fas fa-search fa-sm text-white-50"></i> 검색</a>
					<a href="#" onclick="gridDownload();" class="btn btn-sm btn-outline-primary shadow-sm pl-2 mr-1"><i class="bx bx-plus fs-16"></i>자료받기</a>
					<div id="search-btn-collapse" class="btn-group mb-0 mb-sm-0"></div>
				</div>
			</div>

			<div class="card-body">
				<div class="row">
					<div class="col-lg-4">
						<div class="form-group">
							<label for="good_types">정산일자 :</label>
							<div class="form-inline date-select-inbox">
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
							</div>
						</div>
					</div>
					<div class="col-lg-4">
						<div class="form-group">
							<label for="store_cd">매장명</label>
							<div class="form-inline inline_btn_box">
								<select id="store_cd" name="store_cd" class="form-control form-control-sm select2-store"></select>
								<a href="javascript:void(0);" class="btn btn-sm btn-outline-primary sch-store"><i class="bx bx-dots-horizontal-rounded fs-16"></i></a>
							</div>
						</div>
					</div>
				</div>
			</div>

		</div>

		<div class="resul_btn_wrap mb-3">
			<a href="#" id="search_sbtn" onclick="Search();" class="btn btn-sm btn-primary shadow-sm mr-1"><i class="fas fa-search fa-sm text-white-50"></i> 검색</a>
			<a href="#" onclick="Add()" class="btn btn-sm btn-outline-primary shadow-sm pl-2 mr-1"><i class="bx bx-plus fs-16"></i> 자료받기</a>
			<div class="search_mode_wrap btn-group mr-2 mb-0 mb-sm-0"></div>
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
</div>



<script language="javascript">
	var columns = [
		{field: "num",			headerName: "#", type:'NumType', pinned: 'left'},
		{field: "closed",		headerName: "마감",			width:65, pinned: 'left'},
		{field: "day",			headerName: "정산일자",		width:140, pinned: 'left'},
		{field: "store_nm",		headerName: "매장명", width:140,
			cellRenderer: function(params) {
				if( params.value != undefined ) {
					return '<a href="#" onClick="popDetail(\''+ params.data.store_cd +'\')">' + params.value+'</a>';
				}
			}
		 },
		// {field: "margin_type",	headerName: "수수료지정",	width:100},
		{field: "sale_amt",		headerName: "판매금액",		width:100, type: 'currencyType', aggregation: true},
		{field: "clm_amt",		headerName: "클레임금액",	width:100, type: 'currencyType', aggregation: true},
		{field: "dc_apply_amt",	headerName: "할인금액",		width:90, type: 'currencyType', aggregation: true},
		{
			headerName: '쿠폰금액',
			children: [{
					field: "coupon_com_amt",
					headerName: "(매장부담)",
					type: 'currencyType',
					aggregation: true
				}
			]
		},
		{field: "dlv_amt",		headerName: "배송비",		width:90, type: 'currencyType', aggregation: true},
		{field: "fee_etc_amt",	headerName: "기타정산액",	width:90, type: 'currencyType', aggregation: true},
		{
			headerName: '매출금액',
			children: [{
					field: "sale_net_taxation_amt",
					headerName: "과세",
					width:100,
					type: 'currencyType',
					aggregation: true
				},
				{
					field: "sale_net_taxfree_amt",
					headerName: "비과세",
					type: 'currencyType',
					aggregation: true
				},
				{
					field: "sale_net_amt",
					headerName: "소계",
					width:100,
					type: 'currencyType',
					aggregation: true
				},
			]
		},
		{field: "tax_amt",	headerName: "부가세",	type: 'currencyType',	hide:true},
		{
			headerName: '수수료',
			children: [{
					field: "fee",
					headerName: "판매수수료",
					type: 'currencyType',
					aggregation: true
				},
				{
					field: "fee_dc_amt",
					headerName: "할인금액",
					type: 'currencyType',
					aggregation: true
				},
				{
					field: "fee_net_amt",
					headerName: "소계",
					width:100,
					type: 'currencyType',
					aggregation: true
				},
			]
		},
		{field: "acc_amt",	headerName: "정산금액",	width:100,	type: 'currencyType', aggregation: true},
		{
			headerName: '쿠폰금액',
			children: [{
					field: "fee_allot_amt",
					headerName: "(본사부담)",
					type: 'currencyType',
					aggregation: true
				}
			]
		},
		{field: "store_cd",	hide:true},
		// {field: "acc_idx",	hide:true},
		{ width: 'auto' }
	];

</script>
<script type="text/javascript" charset="utf-8">
	const pApp = new App('',{
		gridId:"#div-gd",
	});
	let gx;

	$(document).ready(function() {
		pApp.ResizeGrid(275);
		pApp.BindSearchEnter();
		let gridDiv = document.querySelector(pApp.options.gridId);
        let options = {
            getRowStyle: (params) => {
                if (params.node.rowPinned === 'top') {
                    return { 'background': '#eee' }
                }
            },
			onPinnedRowDataChanged: (params) => {
				let pinnedRow = gx.gridOptions.api.getPinnedTopRow(0);
				if (pinnedRow == undefined) return false;
				gx.gridOptions.api.setPinnedTopRowData([ { ...pinnedRow.data, closed: '합계' } ]);
			}
        };
		gx = new HDGrid(gridDiv, columns, options);
		Search();
	});

	function Search() {
		let data = $('form[name="search"]').serialize();
        gx.Aggregation({ "sum": "top" });
		gx.Request('/store/account/acc02/search', data,-1);
	}

	function gridDownload() {
		gx.Download("정산내역.csv");
	}

	function popDetail(store_cd){
		let sdate	= $('input[name="sdate"]').val();
		let edate	= $('input[name="edate"]').val();
		const url	='/store/account/acc02/show/' + store_cd + '/' + sdate + '/' + edate;
		window.open(url,"_blank","toolbar=no,scrollbars=yes,resizable=yes,status=yes,top=500,left=500,width=1200,height=800");
	}

</script>

@stop

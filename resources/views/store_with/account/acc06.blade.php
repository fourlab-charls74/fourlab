@extends('store_with.layouts.layout')
@section('title','매장별중간관리정산')
@section('content')

<style>
	/* 기본옵션 ag grid 3단 가운데 정렬 css 적용 */
	.basic-option .ag-header-row.ag-header-row-column-group + .ag-header-row.ag-header-row-column > .bizest.ag-header-cell {
        transform: translateY(-65%);
        height: 320%;
    }

	/**
	 * 3단이 포함되지 않은 2단 셀 깨지는 부분 css 처리
	 */
	.merged-cell {
		height: 200%;
		top: -107%;
		padding-top: 4px;
	}
</style>
<div class="page_tit">
	<h3 class="d-inline-flex">매장별중간관리정산</h3>
	<div class="d-inline-flex location">
		<span class="home"></span>
		<span>정산관리</span>
		<span>/ 매장별중간관리정산</span>
	</div>
</div>
<form method="get" name="search">
	<div id="search-area" class="search_cum_form">
		<div class="card mb-3">
			<div class="d-flex card-header justify-content-between">
				<h4>검색</h4>
				<div class="flax_box">
					<a href="#" id="search_sbtn" onclick="Search();" class="btn btn-sm btn-primary shadow-sm mr-1"><i class="fas fa-search fa-sm text-white-50"></i> 검색</a>
					<!-- 2023-05-25 검색조건 초기화 주석처리 -양대성- -->
					<!-- <a href="#" onclick="initSearch()" class="d-none search-area-ext d-sm-inline-block btn btn-sm btn-outline-primary mr-1 shadow-sm">검색조건 초기화</a> -->
					<div id="search-btn-collapse" class="btn-group mb-0 mb-sm-0"></div>
				</div>
			</div>

			<div class="card-body">
				<div class="row">
					<div class="col-lg-4 inner-td">
						<div class="form-group">
							<label for="sdate">판매기간(판매연월)</label>
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
					<div class="col-lg-4 inner-td">
						<div class="form-group">
							<label for="good_types">판매채널/매장구분</label>
							<div class="d-flex align-items-center">
								<div class="flex_box w-100">
									<select name='store_channel' id="store_channel" class="form-control form-control-sm" onchange="chg_store_channel();">
										<option value=''>전체</option>
									@foreach ($store_channel as $sc)
										<option value='{{ $sc->store_channel_cd }}'>{{ $sc->store_channel }}</option>
									@endforeach
									</select>
								</div>
								<span class="mr-2 ml-2">/</span>
								<div class="flex_box w-100">
									<select id='store_channel_kind' name='store_channel_kind' class="form-control form-control-sm" disabled>
										<option value=''>전체</option>
									@foreach ($store_kind as $sk)
										<option value='{{ $sk->store_kind_cd }}'>{{ $sk->store_kind }}</option>
									@endforeach
									</select>
								</div>
							</div>
						</div>
					</div>
					<div class="col-lg-4 inner-td">
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
					<div class="col-lg-4 inner-td">
						<div class="form-group">
                            <label for="store_cd">매장명</label>
							<div class="form-inline inline_btn_box">
								<select id="store_cd" name="store_cd" class="form-control form-control-sm select2-store"></select>
								<a href="javascript:void(0);" class="btn btn-sm btn-outline-primary sch-store"><i class="bx bx-dots-horizontal-rounded fs-16"></i></a>
							</div>
                        </div>
					</div>
					<div class="col-lg-4 inner-td">
						<div class="form-group">
							<label for="store_kind">마감상태</label>
							<div class="flex_box">
								<select name='closed_yn' id="closed_yn" class="form-control form-control-sm">
									<option value=''>전체</option>
									<option value='Z'>상태없음</option>
									<option value='N'>마감추가</option>
									<option value='Y'>마감완료</option>
								</select>
							</div>
                        </div>
					</div>		
				</div>
			</div>
		</div>
		<div class="resul_btn_wrap mb-3">
			<a href="#" id="search_sbtn" onclick="Search();" class="btn btn-sm btn-primary shadow-sm mr-1"><i class="fas fa-search fa-sm text-white-50"></i> 검색</a>
			<!-- 2023-05-25 검색조건 초기화 주석처리 -양대성- -->
			<!-- <a href="#" onclick="initSearch()" class="d-none search-area-ext d-sm-inline-block btn btn-sm btn-outline-primary mr-1 shadow-sm">검색조건 초기화</a> -->
			<div class="search_mode_wrap btn-group mr-2 mb-0 mb-sm-0"></div>
		</div>
	</div>
</form>
<!-- DataTales Example -->
<div class="card shadow mb-0 last-card pt-2 pt-sm-0">
	<div class="card-body">
		<div class="card-title">
			<div class="filter_wrap">
				<div class="fl_box flex_box">
					<h6 class="m-0 font-weight-bold">총 : <span id="gd-total" class="text-primary">0</span>건</h6>
					<p id="current_date" class="ml-3 pl-2 pr-2 fs-14 text-white bg-secondary rounded"></p>
				</div>
			</div>
		</div>
		<div class="table-responsive basic-option">
			<div id="div-gd" style="height:calc(100vh - 370px);width:100%;" class="ag-theme-balham"></div>
		</div>
	</div>
</div>
<script language="javascript">
	const CLOSED_STATUS = { 'Y': '마감완료', 'N': '마감추가' };
	const CENTER = { 'text-align': 'center' };

    let columns = [
        { headerName: "#", field: "num", type: 'NumType', pinned: 'left', width: 30, cellStyle: CENTER,
			cellRenderer: (params) => params.node.rowPinned === 'top' ? '' : parseInt(params.value) + 1,
        },
		{ field: "closed_yn", headerName: "마감상태", pinned: 'left', width: 57,
            cellRenderer: (params) => params.node.rowPinned === 'top' ? '' : (CLOSED_STATUS[params.value] || '-'),
            cellStyle: (params) => ({
                ...CENTER, 
                "background-color": params.value === 'Y' ? '#E2FFE0' : params.value === 'N' ? '#FFE9E9' : 'none',
                "color": params.value === 'Y' ? '#0BAC00' : params.value === 'N' ? '#ff0000' : 'none'
            }),
        },
        { field: "store_cd", headerName: "매장코드", pinned: 'left', width: 57, cellStyle: CENTER,
			cellRenderer: (params) => params.node.rowPinned === 'top' ? '합계' : params.value,
		},
        { field: "store_type_nm", headerName: "매장구분", pinned: 'left', width: 70, cellStyle: CENTER },
        { field: "store_nm", headerName: "매장명", pinned: 'left', width: 150 },
        { field: "manager_nm", headerName: "매니저", pinned: 'left', width: 55, cellStyle: CENTER },
        { field: "grade_nm", headerName: "수수료등급", pinned: 'left', width: 65, cellStyle: CENTER },
        { headerName: "매출",
			children: [
				@foreach (@$pr_codes as $pr_code)
				{ field: "sales_{{ $pr_code->code_id }}_amt", headerName: "{{ $pr_code->code_val }}", type: 'currencyType', width: 100, headerClass: "merged-cell", aggregation: true },
				@endforeach
				{ field: "sales_amt", headerName: "매출합계", width: 100, headerClass: "merged-cell", type: 'currencyType',  aggregation: true,
					cellRenderer: (params) => {
						if (params.value == undefined) return 0;
						if (params.node.rowPinned === 'top') return params.valueFormatted;
						return '<a href="#" onClick="openDetailPopup(\''+ params.data.store_cd +'\')">' + params.valueFormatted +'</a>';
					}
				},
				{ field: "sales_amt_except_vat", headerName: "매출합계(-VAT)", width: 100, headerClass: "merged-cell", type: 'currencyType', aggregation: true },
			]
        },
        { headerName: "중간관리자 수수료",
            children: [
                { headerName: "기본수수료",
                    children: [
                        { headerName: "매출액(-VAT)", field: "ord_JS1_amt_except_vat", type: 'currencyType', width: 90, aggregation: true, },
                        { headerName: "수수료율", field: "fee1", type: 'percentType', width: 60 },
                        { headerName: "수수료", field: "fee_amt_JS1", type: 'currencyType', width: 90, aggregation: true },
                    ]
                },
                { headerName: "초과수수료1",
                    children: [
						{ headerName: "매출액(-VAT)", field: "ord_JS2_amt_except_vat", type: 'currencyType', width: 90, aggregation: true },
                        { headerName: "수수료율", field: "fee2", type: 'percentType', width: 60 },
                        { headerName: "수수료", field: "fee_amt_JS2", type: 'currencyType', width: 90, aggregation: true },
                    ]
                },
                { headerName: "초과수수료2",
                    children: [
						{ headerName: "매출액(-VAT)", field: "ord_JS3_amt_except_vat", type: 'currencyType', width: 90, aggregation: true },
                        { headerName: "수수료율", field: "fee3", type: 'percentType', width: 60 },
                        { headerName: "수수료", field: "fee_amt_JS3", type: 'currencyType', width: 90, aggregation: true },
                    ]
                },
                { headerName: "특가",
                    children: [
						{ headerName: "매출액(-VAT)", field: "ord_TG_amt_except_vat", type: 'currencyType', width: 90, aggregation: true },
                        { headerName: "수수료율", field: "fee_10", type: 'percentType', width: 60 },
                        { headerName: "수수료", field: "fee_amt_TG", type: 'currencyType', width: 90, aggregation: true },
                    ]
                },
                { headerName: "용품",
                    children: [
						{ headerName: "매출액(-VAT)", field: "ord_YP_amt_except_vat", type: 'currencyType', width: 90, aggregation: true },
                        { headerName: "수수료율", field: "fee_11", type: 'percentType', width: 60 },
                        { headerName: "수수료", field: "fee_amt_YP", type: 'currencyType', width: 90, aggregation: true },
                    ]
                },
                { headerName: "특가(온라인)",
                    children: [
						{ headerName: "매출액(-VAT)", field: "ord_OL_amt_except_vat", type: 'currencyType', width: 90, aggregation: true },
                        { headerName: "수수료율", field: "fee_12", type: 'percentType', width: 60 },
                        { headerName: "수수료", field: "fee_amt_OL", type: 'currencyType', width: 90, aggregation: true,
							cellRenderer: (params) => ['0', null].includes(params.value) ? 0 : (params.node.rowPinned === 'top' ? params.valueFormatted : '<a href="javascript:void(0);" onClick="openOnlineFeePopup(\''+ params.data.store_cd +'\')">' + params.valueFormatted +'</a>')
						},
                    ]
                },
				{ headerName: "수수료 합계", field: "fee_amt", type: 'currencyType', width: 90, aggregation: true, headerClass: "merged-cell" },
            ]
        },
		{ headerName: "기타제반자료",
			children: [
				{ field: "extra_P_amt", headerName: "인건비", type: 'currencyType', width: 70, aggregation: true, headerClass: "merged-cell" },
				{ field: "extra_S_amt", headerName: "매장부담금", type: 'currencyType', width: 70, aggregation: true, headerClass: "merged-cell" },
				{ field: "extra_C_amt", headerName: "본사부담금", type: 'currencyType', width: 70, aggregation: true, headerClass: "merged-cell" },
				{ field: "extra_amt", headerName: "기타제반 합계", type: 'currencyType', width: 90, aggregation: true, headerClass: "merged-cell",
					cellRenderer: (params) => params.node.rowPinned === 'top' ? params.valueFormatted : '<a href="javascript:void(0);" onClick="openExtraAmtPopup(\''+ params.data.store_cd +'\')">' + params.valueFormatted +'</a>'
				},
			]
		},
        { field: "total_fee_amt", headerName: "최종지급액", type: 'currencyType', width: 100, aggregation: true, cellStyle: { "font-weight": "bold", "color": "#dd0000" } },
        { width: "auto" }
    ];
</script>
<script type="text/javascript" charset="utf-8">
	const pApp = new App('', { gridId:"#div-gd", height: 265 });
	let gx;
	
	$(document).ready(function() {
		pApp.ResizeGrid(265);
		pApp.BindSearchEnter();
		let gridDiv = document.querySelector(pApp.options.gridId);
		gx = new HDGrid(gridDiv, columns, {
			getRowStyle: (params) => params.node.rowPinned === 'top' ? { 'background': '#ededed', 'font-weight': '600' } : {},
		});
		gx.Aggregation({ "sum": "top" });

		Search();

		// 판매채널 선택되지않았을때 매장구분 disabled처리하는 부분
		load_store_channel();
	});

	function Search() {
		let data = $('form[name="search"]').serialize();
		gx.Request('/store/account/acc06/search', data, -1, function(d) {
			$("#current_date").text(d.head.date);
		});
	}

	// 판매내역 상세
	function openDetailPopup(store_cd) {
		const sdate = $('input[name="sdate"]').val();
		const url = '/store/account/acc06/show/' + store_cd + '/' + sdate;
		window.open(url,"_blank","toolbar=no,scrollbars=yes,resizable=yes,status=yes,top=100,left=100,width=1200,height=800");
	}

	// 특약(온라인) 판매내역 상세
	function openOnlineFeePopup(store_cd) {
		const sdate = $('input[name="sdate"]').val();
		const url = '/store/account/acc06/show-online?store_cd=' + store_cd + '&sdate=' + sdate;
		window.open(url,"_blank","toolbar=no,scrollbars=yes,resizable=yes,status=yes,top=100,left=100,width=1200,height=800");
	}

	// 기타제반자료 상세
	function openExtraAmtPopup(store_cd) {
		const sdate = $('input[name="sdate"]').val();
		const url = '/store/account/acc05/show?date=' + sdate.replaceAll('-', '') + '&store_cd=' + store_cd;
		window.open(url,"_blank","toolbar=no,scrollbars=yes,resizable=yes,status=yes,top=100,left=100,width=1200,height=800");
	}

</script>
@stop

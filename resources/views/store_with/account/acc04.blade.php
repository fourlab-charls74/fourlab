@extends('store_with.layouts.layout')
@section('title','매장별매출현황')
@section('content')
<style>
	/* 기본옵션 ag grid 3단 가운데 정렬 css 적용 */
	.ag-header-row.ag-header-row-column-group + .ag-header-row.ag-header-row-column > .bizest.ag-header-cell {
        transform: translateY(-65%);
        height: 320%;
		padding-top: 2px;
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
	<h3 class="d-inline-flex">매장별매출현황</h3>
	<div class="d-inline-flex location">
		<span class="home"></span>
		<span>매장관리</span>
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
					<a href="#" onclick="initSearch(['#store_no'])" class="d-none search-area-ext d-sm-inline-block btn btn-sm btn-outline-primary mr-1 shadow-sm">검색조건 초기화</a>
					<div id="search-btn-collapse" class="btn-group mb-0 mb-sm-0"></div>
				</div>
			</div>

			<div class="card-body">
				<div class="row">
					<div class="col-lg-4 inner-td">
						<div class="form-group">
							<label for="sdate">판매기간(판매연월)</label>
							<div class="form-inline date-select-inbox">
								<div class="docs-datepicker form-inline-inner input_box">
									<div class="input-group">
										<input type="text" class="form-control form-control-sm docs-date month" name="sdate" value="{{ $sdate }}" autocomplete="off" disable>
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
										<input type="text" class="form-control form-control-sm docs-date month" name="edate" value="{{ $sdate }}" autocomplete="off">
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
								<select id="store_no" name="store_no[]" class="form-control form-control-sm select2-store multi_select" multiple></select>
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
                    <div class="col-lg-4 inner-td">
                        <div class="form-group">
                            <label for="sale_yn">매출여부</label>
                            <div class="flex_box">
                                <div class="form-inline form-radio-box">
                                    <div class="custom-control custom-radio">
                                        <input type="radio" name="sale_yn" id="sale_y" value="Y" class="custom-control-input" checked/>
                                        <label class="custom-control-label" for="sale_y">Y</label>
                                    </div>
                                    <div class="custom-control custom-radio">
                                        <input type="radio" name="sale_yn" id="sale_n" value="N" class="custom-control-input"/>
                                        <label class="custom-control-label" for="sale_n">N</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
			</div>
		</div>
		<div class="resul_btn_wrap mb-3">
			<a href="#" id="search_sbtn" onclick="Search();" class="btn btn-sm btn-primary shadow-sm mr-1"><i class="fas fa-search fa-sm text-white-50"></i> 검색</a>
			<input type="reset" id="search_reset" value="검색조건 초기화" class="btn btn-sm btn-outline-primary shadow-sm" onclick="initSearch()">
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
<script language="javascript">
	const CLOSED_STATUS = { 'Y': '마감완료', 'N': '마감추가' };
	const CENTER = { 'text-align': 'center' };

	/**
	 * 마감상태
	 * 매장코드
	 * 매장구분
	 * 매장명
	 * 매니저
	 * 수수료등급
	 * 매출정보
	 * 	- 매출합계
	 * 	- 매출합계(-VAT)
	 * 	- 마일리지(-VAT) M1
	 * 	- 원가
	 * 	- 매출이익 : 매출합계(-VAT) - 마일리지(-VAT) - 원가
	 * 판매수수료
	 * 	- 정상 (매출액 / 매출액(-VAT) / 수수료율 / 수수료)
	 * 	- 행사 (매출액 / 매출액(-VAT) / 수수료율 / 수수료)
	 * 	- 균일 (매출액 / 매출액(-VAT) / 수수료율 / 수수료)
	 * 	- 용품 (매출액 / 매출액(-VAT) / 수수료율 / 수수료)
	 * 	- 수수료합계
	 * 중간관리자수수료
	 * 	- 정상1 (매출액 / 매출액(-VAT) / 수수료율 / 수수료)
	 * 	- 정상2 (매출액 / 매출액(-VAT) / 수수료율 / 수수료)
	 * 	- 정상3 (매출액 / 매출액(-VAT) / 수수료율 / 수수료)
	 * 	- 특가 (매출액 / 매출액(-VAT) / 수수료율 / 수수료)
	 * 	- 용품 (매출액 / 매출액(-VAT) / 수수료율 / 수수료)
	 * 	- 특가(온라인) (매출액 / 매출액(-VAT) / 수수료율 / 수수료)
	 * 	- 수수료합계
	 * 인건비 P (온라인, 인센티브, 패널티, 기타수수료, 인건비합계)
	 * 기타운영경비(본사부담) O (외부창고, 사용경비(기타), 운영경비합계)
	 * 영업이익 : 매출이익 - 판매수수료 - 중간관리자수수료 - 인건비합계 - 기타운영경비합계
	 * 영업이익율 : 영업이익 / 매출 * 100
	 * 매장부담금
	 * 	- 매장운영비용 S (전화요금, 인터넷, 본사수선비(-VAT), 외부창고/보안비)
	 * 	- 사은품 G
	 * 	- 소모품 E
	*/

    const columns = [
        // { headerName: "#", field: "num", type: 'NumType', pinned: 'left', aggSum: "합계", cellStyle: CENTER,
		// 	cellRenderer: (params) => params.node.rowPinned === 'top' ? '합계' : (parseInt(params.value) + 1),
        // },
        // { field: "ymonth", headerName: "판매기간", rowGroup: true, hide: true },
		// { headerName: '판매기간', showRowGroup: 'ymonth', cellRenderer: 'agGroupCellRenderer', minWidth: 100, pinned: 'left' },
		{ headerName: '판매기간', field: 'ymonth', width: 100, pinned: 'left' },
		{ field: "closed_yn", headerName: "마감상태", pinned: 'left', width: 57,
            cellRenderer: (params) => params.node.rowPinned === 'top' ? '' : (CLOSED_STATUS[params.value] || '-'),
            cellStyle: (params) => ({
                ...CENTER, 
                "background-color": params.value === 'Y' ? '#E2FFE0' : params.value === 'N' ? '#FFE9E9' : 'none',
                "color": params.value === 'Y' ? '#0BAC00' : params.value === 'N' ? '#ff0000' : 'none'
            }),
        },
        { field: "store_cd", headerName: "매장코드", pinned: 'left', width: 60, cellStyle: CENTER },
        { field: "store_type_nm", headerName: "매장구분", pinned: 'left', width: 70, cellStyle: CENTER },
        { field: "store_nm", headerName: "매장명", pinned: 'left', type: 'StoreNameType', width: 150 },
        { field: "manager_nm", headerName: "매니저", pinned: 'left', width: 55, cellStyle: CENTER },
		{ field: "grade_nm", headerName: "수수료등급", pinned: 'left', width: 65, cellStyle: CENTER },
        { headerName: "매출",
            children: [
				{ field: "sales_amt", headerName: "매출합계", width: 100, headerClass: "merged-cell", type: 'currencyType',
					cellRenderer: (params) => {
						if (params.value == undefined) return 0;
						if (params.node.rowPinned === 'top') return params.valueFormatted;
						return '<a href="#" onClick="openDetailPopup(\''+ params.data.store_cd +'\')">' + params.valueFormatted +'</a>';
					}
				},
				{ field: "sales_amt_except_vat", headerName: "매출합계(-VAT)", width: 100, headerClass: "merged-cell", type: 'currencyType', cellStyle: {"background-color": "#ededed"} },
            ]
        },
        // { field: "wonga_total", headerName: "원가", width: 100, type: 'currencyMinusColorType' },
        // { field: "sales_profit", headerName: "매출이익", width: 100, type: 'currencyMinusColorType' }, // 매출이익 = 결제금액 - 원가 합계금액
        // { field: "profit_rate",	headerName: "이익율(%)", width: 80, type: 'percentType' }, // 매출이익 분의 매출액 = 이익율
        { headerName: "판매 수수료",
            children: [
				@foreach (@$pr_codes as $pr_code)
                { headerName: "{{ @$pr_code->code_val }}",
                    children: [
						{ headerName: "매출액(-VAT)", field: "sales_{{ @$pr_code->code_id }}_amt_except_vat", type: 'currencyType', width: 90, },
                        { headerName: "수수료율", field: "sales_{{ @$pr_code->code_id }}_fee_rate", type: 'percentType', width: 60 },
                        { headerName: "수수료", field: "sales_{{ @$pr_code->code_id }}_fee", type: 'numberType', width: 100 },
                    ]
                },
				@endforeach
                { headerName: "수수료 합계", field: "sales_fee", type: 'numberType', width: 100, headerClass: "merged-cell" },
                // { headerName: "임대관리비", field: "management_fee", type: 'numberType', width: 100, headerClass: "merged-cell" },
            ]
        },
		{ headerName: "중간관리자 수수료",
            children: [
                { headerName: "정상1",
                    children: [
                        { headerName: "매출액(-VAT)", field: "ord_JS1_amt_except_vat", type: 'currencyType', width: 90, },
                        { headerName: "수수료율", field: "fee1", type: 'percentType', width: 60 },
                        { headerName: "수수료", field: "fee_amt_JS1", type: 'currencyType', width: 90 },
                    ]
                },
                { headerName: "정상2",
                    children: [
						{ headerName: "매출액(-VAT)", field: "ord_JS2_amt_except_vat", type: 'currencyType', width: 90 },
                        { headerName: "수수료율", field: "fee2", type: 'percentType', width: 60 },
                        { headerName: "수수료", field: "fee_amt_JS2", type: 'currencyType', width: 90 },
                    ]
                },
                { headerName: "정상3",
                    children: [
						{ headerName: "매출액(-VAT)", field: "ord_JS3_amt_except_vat", type: 'currencyType', width: 90 },
                        { headerName: "수수료율", field: "fee3", type: 'percentType', width: 60 },
                        { headerName: "수수료", field: "fee_amt_JS3", type: 'currencyType', width: 90 },
                    ]
                },
                { headerName: "특가",
                    children: [
						{ headerName: "매출액(-VAT)", field: "ord_TG_amt_except_vat", type: 'currencyType', width: 90 },
                        { headerName: "수수료율", field: "fee_10", type: 'percentType', width: 60 },
                        { headerName: "수수료", field: "fee_amt_TG", type: 'currencyType', width: 90 },
                    ]
                },
                { headerName: "용품",
                    children: [
						{ headerName: "매출액(-VAT)", field: "ord_YP_amt_except_vat", type: 'currencyType', width: 90 },
                        { headerName: "수수료율", field: "fee_11", type: 'percentType', width: 60 },
                        { headerName: "수수료", field: "fee_amt_YP", type: 'currencyType', width: 90 },
                    ]
                },
                { headerName: "특가(온라인)",
                    children: [
						{ headerName: "매출액(-VAT)", field: "ord_OL_amt_except_vat", type: 'currencyType', width: 90 },
                        { headerName: "수수료율", field: "fee_12", type: 'percentType', width: 60 },
                        { headerName: "수수료", field: "fee_amt_OL", type: 'currencyType', width: 90,
							// cellRenderer: (params) => ['0', null].includes(params.value) ? 0 : (params.node.rowPinned === 'top' ? params.valueFormatted : '<a href="javascript:void(0);" onClick="openOnlineFeePopup(\''+ params.data.store_cd +'\')">' + params.valueFormatted +'</a>')
						},
                    ]
                },
				{ headerName: "수수료 합계", field: "fee_amt", type: 'currencyMinusColorType', width: 90, headerClass: "merged-cell", cellStyle: {"background-color": "#ededed"} },
            ]
        },
        { field: "extra_amt", headerName: "기타재반", type: 'currencyMinusColorType', width: 70,
			// cellRenderer: (params) => ['0', null].includes(params.value) ? 0 : (params.node.rowPinned === 'top' ? params.valueFormatted : '<a href="javascript:void(0);" onClick="openExtraAmtPopup(\''+ params.data.store_cd +'\')">' + params.valueFormatted +'</a>')
		},
        { field: "real_profit", headerName: "총마진금액", type: 'currencyMinusColorType', width: 100 },
        { field: "real_profit_rate", headerName: "마진율(%)", type: 'percentType', width: 60 },
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
            getRowStyle: (params) => { // 고정된 row styling
                if (params.node.rowPinned)  return { 'font-weight': 'bold', 'background': '#eee', 'border': 'none'};
            },
		});

		Search();

		//매장 다중 선택
		$(".sch-store").on("click", function() {
			searchStore.Open(null, "multiple");
        });
	});

	function Search() {
		const data = $('form[name="search"]').serialize();
		gx.Request('/store/account/acc04/search', data, -1);
	}

</script>
@stop

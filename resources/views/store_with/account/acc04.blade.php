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
		<span>/ 경영관리</span>
	</div>
</div>
<form method="get" name="search">
	<div id="search-area" class="search_cum_form">
		<div class="card mb-3">

			<div class="d-flex card-header justify-content-between">
				<h4>검색</h4>
				<div class="flax_box">
					<a href="#" id="search_sbtn" onclick="Search();" class="btn btn-sm btn-primary shadow-sm mr-1"><i class="fas fa-search fa-sm text-white-50"></i> 검색</a>
					<a href="#" onclick="initSearch(['#store_no', '#sale_kind'])" class="d-none search-area-ext d-sm-inline-block btn btn-sm btn-outline-primary mr-1 shadow-sm">검색조건 초기화</a>
					<div id="search-btn-collapse" class="btn-group mb-0 mb-sm-0"></div>
				</div>
			</div>

			<div class="card-body">
				<div class="row">
					<div class="col-lg-4 inner-td">
						<div class="form-group">
							<label for="good_types">판매기간</label>
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
                            <label for="store_cd">매장명</label>
							<div class="form-inline inline_btn_box">
								<select id="store_no" name="store_no[]" class="form-control form-control-sm select2-store multi_select" multiple></select>
								<a href="javascript:void(0);" class="btn btn-sm btn-outline-primary sch-store"><i class="bx bx-dots-horizontal-rounded fs-16"></i></a>
							</div>
                        </div>
					</div>
				</div>
				<div class="row">
					<div class="col-lg-4 inner-td">
                        <div class="form-group">
                            <label for="pr_code">판매유형</label>
                            <div class="flax_box">
                                <select id="sale_kind" name="sale_kind[]" class="form-control form-control-sm multi_select w-100" multiple>
                                    @foreach (@$sale_kinds as $sale_kind)
                                    <option value='{{ $sale_kind->code_id }}' @if (@$sale_kind_id == $sale_kind->code_id) selected @endif>{{ $sale_kind->code_val }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    {{-- <div class="col-lg-4 inner-td">
                        <div class="form-group">
                            <label for="brand_cd">브랜드</label>
                            <div class="form-inline inline_btn_box">
                                <select id="brand_cd" name="brand_cd" class="form-control form-control-sm select2-brand"></select>
                                <a href="#" class="btn btn-sm btn-outline-primary sch-brand"><i class="bx bx-dots-horizontal-rounded fs-16"></i></a>
                            </div>
                        </div>
                    </div> --}}
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
    const columns = [
        { headerName: "#", field: "num", type: 'NumType', pinned: 'left', aggSum: "합계", cellStyle: { 'text-align': "center" },
			cellRenderer: (params) => params.node.rowPinned === 'top' ? '합계' : (parseInt(params.value) + 1),
        },
        { field: "store_type_nm", headerName: "매장구분", pinned: 'left', width: 70, cellStyle: { 'text-align': "center" } },
        { field: "store_cd", headerName: "매장코드", pinned: 'left', width: 60, cellStyle: { 'text-align': "center" } },
        { field: "store_nm", headerName: "매장명", pinned: 'left', type: 'StoreNameType', width: 150 },
        { headerName: "매출",
            children: [
                { headerName: "소계", field: "sales_total", type: 'numberType', width: 100, headerClass: "merged-cell", aggregation: true },
				@foreach (@$pr_codes as $pr_code)
                { headerName: "{{ @$pr_code->code_val }}", field: "sales_{{ @$pr_code->code_id }}", type: 'numberType', width: 100, headerClass: "merged-cell", aggregation: true},
				@endforeach
                { headerName: "기타", field: "sales_etc", type: 'numberType', width: 100, headerClass: "merged-cell", aggregation: true },
            ]
        },
        // { field: "sale_qty", headerName: "판매상품수량", width: 100, hide: true, type: 'currencyType' },
        { field: "wonga_total", headerName: "원가", width: 100, type: 'currencyMinusColorType', aggregation: true },
        { field: "sales_profit", headerName: "매출이익", width: 100, type: 'currencyMinusColorType', aggregation: true }, // 매출이익 = 결제금액 - 원가 합계금액
        { field: "profit_rate",	headerName: "이익율(%)", width: 80, type: 'percentType' }, // 매출이익 분의 매출액 = 이익율
        { headerName: "수수료",
            children: [
                { headerName: "수수료합계", field: "total_fee", type: 'numberType', width: 100, headerClass: "merged-cell", aggregation: true },
                // { headerName: "임대관리비", field: "management_fee", type: 'numberType', width: 100, headerClass: "merged-cell", aggregation: true },
				@foreach (@$pr_codes as $pr_code)
                { headerName: "{{ @$pr_code->code_val }}",
                    children: [
                        { headerName: "수수료율", field: "{{ @$pr_code->code_id }}_fee_rate", type: 'percentType', width: 60 },
                        { headerName: "수수료", field: "{{ @$pr_code->code_id }}_fee", type: 'numberType', width: 100, aggregation: true },
                    ]
                },
				@endforeach
                { headerName: "기타",
                    children: [
                        { headerName: "수수료율", field: "etc_fee_rate", type: 'percentType', width: 60 },
                        { headerName: "수수료", field: "etc_fee", type: 'numberType', width: 100, aggregation: true },
                    ]
                },
            ]
        },
        { field: "sales_real_profit", headerName: "매출이익-수수료", type: 'currencyMinusColorType', width: 100, aggregation: true },
        { field: "real_profit_rate", headerName: "수수료제외이익율(%)", type: 'percentType', width: 120 },
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

		gx.Aggregation({
            "sum": "top",
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

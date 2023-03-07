@extends('store_with.layouts.layout')
@section('title','매장브랜드별매출분석')
@section('content')
<div class="page_tit">
	<h3 class="d-inline-flex">매장브랜드별매출분석</h3>
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
					<a href="javascript:void(0);" id="search_sbtn" onclick="Search();" class="btn btn-sm btn-primary shadow-sm mr-1"><i class="fas fa-search fa-sm text-white-50"></i> 검색</a>
					<a href="javascript:void(0);" onclick="initSearch()" class="d-none search-area-ext d-sm-inline-block btn btn-sm btn-outline-primary mr-1 shadow-sm">검색조건 초기화</a>
					<a href="javascript:void(0);" onclick="gx.Download();" class="btn btn-sm btn-outline-primary shadow-sm pl-2 mr-1"><i class="bx bx-download fs-16"></i> 엑셀다운로드</a>
					<div id="search-btn-collapse" class="btn-group mb-0 mb-sm-0"></div>
				</div>
			</div>

			<div class="card-body">
				<div class="row">
					<div class="col-lg-4 mb-2 mb-lg-0">
						<div class="form-group">
							<label for="good_types">판매기간</label>
							<div class="form-inline date-select-inbox">
								<div class="docs-datepicker form-inline-inner input_box">
									<div class="input-group">
										<input type="text" class="form-control form-control-sm docs-date" name="sdate" value="{{ @$sdate }}" autocomplete="off" disable>
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
										<input type="text" class="form-control form-control-sm docs-date" name="edate" value="{{ @$edate }}" autocomplete="off">
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
					<div class="col-lg-4 mb-2 mb-lg-0">
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
                            <label for="store_cd">매장명</label>
							<div class="form-inline inline_btn_box">
								<select id="store_no" name="store_no[]" class="form-control form-control-sm select2-store multi_select" multiple></select>
								<a href="javascript:void(0);" class="btn btn-sm btn-outline-primary sch-store"><i class="bx bx-dots-horizontal-rounded fs-16"></i></a>
							</div>
                        </div>
					</div>
				</div>
				<div class="row">
					<div class="col-lg-4 mb-2 mb-lg-0">
						<div class="form-group">
							<label for="brand_cd">브랜드</label>
							<div class="form-inline inline_btn_box">
								<select id="brand_cd" name="brand_cd" class="form-control form-control-sm select2-brand"></select>
								<a href="#" class="btn btn-sm btn-outline-primary sch-brand"><i class="bx bx-dots-horizontal-rounded fs-16"></i></a>
							</div>
						</div>
					</div>
					<div class="col-lg-4">
						<div class="form-group">
                            <label for="formrow-email-input">상품명</label>
                            <div class="flex_box">
                                <input type='text' class="form-control form-control-sm ac-goods-nm search-enter" name='goods_nm' value=''>
                            </div>
                        </div>
					</div>
					<div class="col-lg-4 inner-td">
						<div class="form-group">
							<label for="goods_nm_eng">상품명(영문)</label>
							<div class="flex_box">
								<input type='text' class="form-control form-control-sm ac-goods-nm-eng search-enter" name='goods_nm_eng' id="goods_nm_eng" value=''>
							</div>
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-lg-4 inner-td">
						<div class="form-group">
							<label>바코드</label>
							<div class="flex_box">
								<input type='text' id="prd_cd" name='prd_cd' class="form-control form-control-sm ac-style-no search-enter">
								<a href="#" class="btn btn-sm btn-outline-primary sch-prdcd" hidden><i class="bx bx-dots-horizontal-rounded fs-16"></i></a>
							</div>
						</div>
					</div>
					<div class="col-lg-4 inner-td">
						<div class="form-group">
							<label for="prd_cd">상품검색조건</label>
							<div class="form-inline">
								<div class="form-inline-inner input-box w-100">
									<div class="form-inline inline_btn_box">
										<input type='hidden' id="prd_cd_range" name='prd_cd_range'>
										<input type='text' id="prd_cd_range_nm" name='prd_cd_range_nm' class="form-control form-control-sm w-100 ac-style-no sch-prdcd-range" readonly style="background-color: #fff;">
										<a href="#" class="btn btn-sm btn-outline-primary sch-prdcd-range"><i class="bx bx-dots-horizontal-rounded fs-16"></i></a>
									</div>
								</div>
							</div>
						</div>
					</div>
					<div class="col-lg-4 inner-td">
                        <div class="form-group">
                            <label for="item">품목</label>
                            <div class="flax_box">
                                <select name="item" class="form-control form-control-sm">
                                    <option value="">전체</option>
                                    @foreach ($items as $item)
                                        <option value="{{ $item->cd }}">{{ $item->val }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
				</div>
			</div>
		</div>
		<div class="resul_btn_wrap mb-3">
			<a href="javascript:void(0);" id="search_sbtn" onclick="Search();" class="btn btn-sm btn-primary shadow-sm mr-1"><i class="fas fa-search fa-sm text-white-50"></i> 검색</a>
			<a href="javascript:void(0);" onclick="initSearch()" class="d-none search-area-ext d-sm-inline-block btn btn-sm btn-outline-primary mr-1 shadow-sm">검색조건 초기화</a>
			<a href="javascript:void(0);" onclick="gx.Download();" class="btn btn-sm btn-outline-primary shadow-sm pl-2 mr-1"><i class="bx bx-download fs-16"></i> 엑셀다운로드</a>
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
					{{-- <h6 class="m-0 font-weight-bold">총 : <span id="gd-total" class="text-primary">0</span>건</h6> --}}
					<p class="fs-14">* 매장구분 > 매장 > 브랜드</ㅔ>
				</div>
				<div class="d-flex justify-content-end">
					<div class="custom-control custom-checkbox form-check-box pr-2" style="display:inline-block;">
						<input type="checkbox" class="custom-control-input" name="grid_expand" id="grid_expand" onchange="return setAllRowGroupExpanded(this.checked);" checked>
						<label class="custom-control-label font-weight-normal" for="grid_expand">항목펼쳐보기</label>
					</div>
				</div>
			</div>
		</div>
		<div class="table-responsive">
			<div id="div-gd" style="height:calc(100vh - 370px);width:100%;" class="ag-theme-balham"></div>
		</div>
	</div>
</div>

<style>
	.ag-row-level-1 {background-color: #ffffcc !important;}
	.ag-row-level-2 {background-color: #f2f2f2 !important;}
	.ag-row-level-3 {background-color: #e2e2e2 !important;}
</style>

<script language="javascript">
	const pinnedRowData = [{ store_cd: 'total', sale_amt: 0, recv_amt: 0, wonga_amt: 0, margin_amt: 0, margin_rate: 0 }];
	const sumValuesFunc = (params) => params.values.reduce((a,c) => a + (c * 1), 0);

    let columns = [
		{field: "store_type", hide: true},
		{field: "store_type_nm" , headerName: "매장구분", rowGroup: true, hide: true},
		{field: "store_nm" , headerName: "매장명", rowGroup: true, hide: true},
		{field: "brand_nm", headerName: "브랜드명", rowGroup: true, hide: true},
		{headerName: '매장구분', showRowGroup: 'store_type_nm', cellRenderer: 'agGroupCellRenderer', minWidth: 115},
		{field: "store_cd" , headerName: "매장코드", width: 60, cellStyle: {"text-align": "center"},
			aggFunc: (params) => params.values.length > 0 ? params.values[0] : '',
			cellRenderer: (params) => params.value == 'total' ? '합계' : params.node.level == 1 ? params.value : '',
		},
		{headerName: '매장명', showRowGroup: 'store_nm', cellRenderer: 'agGroupCellRenderer', minWidth: 150},
		{field: "brand", headerName: "브랜드", width: 55, cellStyle: {"text-align": "center"},
			aggFunc: (params) => params.values.length > 0 ? params.values[0] : '',
			cellRenderer: (params) => params.node.level == 2 ? params.value : '',
		},
		{headerName: '브랜드명', showRowGroup: 'brand_nm', cellRenderer: 'agGroupCellRenderer', minWidth: 100},
		{field: "pr_code", headerName: "행사코드", width: 55, cellStyle: {"text-align": "center"}},
		{field: "pr_code_nm", headerName: "행사명", width: 55, cellStyle: {"text-align": "center"}},
		{field: "sale_amt", headerName: "판매금액", width: 90, type: "currencyMinusColorType", aggFunc: sumValuesFunc},
		{field: "recv_amt", headerName: "실입금액", width: 90, type: "currencyMinusColorType", aggFunc: sumValuesFunc},
		{field: "wonga_amt", headerName: "원가금액", width: 90, type: "currencyMinusColorType", aggFunc: sumValuesFunc},
		{field: "margin_amt", headerName: "이익금액", width: 90, type: "currencyMinusColorType", aggFunc: sumValuesFunc},
		{field: "margin_rate", headerName: "이익율(%)", width: 70, type: "currencyType",
			aggFunc: (params) => {
				return params.rowNode.allLeafChildren.reduce((a, c) => (c.data?.margin_rate * 1) + a, 0) / params.rowNode.allLeafChildren.length;
			},
		},
		{width: "auto"}
    ];
</script>

<script type="text/javascript" charset="utf-8">
	const pApp = new App('', { gridId:"#div-gd" });
	let gx;

	$(document).ready(function() {
		pApp.ResizeGrid(275);
		pApp.BindSearchEnter();
		let gridDiv = document.querySelector(pApp.options.gridId);
		gx = new HDGrid(gridDiv, columns, {
			pinnedTopRowData: pinnedRowData,
            getRowStyle: (params) => { // 고정된 row styling
                if (params.node.rowPinned)  return { 'font-weight': 'bold', 'background': '#ffff77', 'border': 'none'};
            },
			rollup: true,
			groupSuppressAutoColumn: true,
			suppressAggFuncInHeader: true,
			enableRangeSelection: true,
			animateRows: true,
		});

		Search();

		// 매장 다중검색
		$( ".sch-store" ).on("click", function() {
			searchStore.Open(null, "multiple");
		});
	});
	
	function Search() {
		let data = $('form[name="search"]').serialize();
		gx.Request('/store/sale/sal08/search', data, -1, function(d) {
			setAllRowGroupExpanded($("#grid_expand").is(":checked"));
			updatePinnedRow();
		});
	}

	const updatePinnedRow = () => {
        let [ sale_amt, recv_amt, wonga_amt, margin_amt, margin_rate, cnt ] = [ 0, 0, 0, 0, 0, 0 ];
        const rows = gx.getRows();
        if (rows && Array.isArray(rows) && rows.length > 0) {
            rows.forEach((row, idx) => {
                sale_amt += parseInt(row?.sale_amt || 0);
                recv_amt += parseInt(row?.recv_amt || 0);
                wonga_amt += parseInt(row?.wonga_amt || 0);
                margin_amt += parseInt(row?.margin_amt || 0);
                margin_rate += parseFloat(row?.margin_rate || 0);
				cnt += row !== undefined ? 1 : 0;
            });
        }
		margin_rate = margin_rate / (cnt || 1);

        let pinnedRow = gx.gridOptions.api.getPinnedTopRow(0);
        gx.gridOptions.api.setPinnedTopRowData([
            { ...pinnedRow.data, sale_amt: sale_amt, recv_amt: recv_amt, wonga_amt: wonga_amt, margin_amt: margin_amt, margin_rate: margin_rate }
        ]);
    };
</script>
@stop

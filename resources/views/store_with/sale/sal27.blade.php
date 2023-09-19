@extends('store_with.layouts.layout')
@section('title','품번별종합분석현황(기간)')
@section('content')
<div class="page_tit">
	<h3 class="d-inline-flex">품번별종합분석현황(기간)</h3>
	<div class="d-inline-flex location">
		<span class="home"></span>
		<span>/ 경영관리</span>
		<span>/ 품번별종합분석현황(기간)</span>
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
                    <!-- <a href="#" onclick="formReset()" class="d-none search-area-ext d-sm-inline-block btn btn-sm btn-outline-primary mr-1 shadow-sm">검색조건 초기화</a> -->
					<a href="javascript:void(0);" class="export-excel btn btn-sm btn-outline-primary shadow-sm mr-1"><i class="bx bx-download fs-16"></i> 엑셀다운로드</a>
					<div id="search-btn-collapse" class="btn-group mb-0 mb-sm-0"></div>
				</div>
			</div>
			<div class="card-body">
				<div class="row">
					<div class="col-lg-4 inner-td">
						<div class="form-group">
							<label>일자</label>
							<div class="form-inline">
								<div class="docs-datepicker form-inline-inner input_box">
									<div class="input-group">
										<input type="text" class="form-control form-control-sm docs-date" name="sdate" value="{{ $sdate }}" autocomplete="off">
										<div class="input-group-append">
											<button type="button" class="btn btn-outline-secondary docs-datepicker-trigger p-0 pl-2 pr-2" disable="">
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
                            <label>매장명</label>
                            <div class="form-inline inline_btn_box">
                                <input type='hidden' id="store_nm" name="store_nm">
                                <select id="store_no" name="store_no[]" class="form-control form-control-sm select2-store multi_select" multiple></select>
                                <a href="javascript:void(0);" class="btn btn-sm btn-outline-primary sch-store-multiple"><i class="bx bx-dots-horizontal-rounded fs-16"></i></a>
                            </div>
                        </div>
                    </div>
				</div>
                <div class="row">
					<div class="col-lg-4 inner-td">
						<div class="form-group">
							<label for="prd_cd">바코드</label>
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
                            <label for="prd_cd">상품검색조건</label>
                            <div class="form-inline">
                                <div class="form-inline-inner input-box w-100">
                                    <div class="form-inline inline_btn_box">
                                        <input type='hidden' id="prd_cd_range" name='prd_cd_range'>
                                        <input type='text' id="prd_cd_range_nm" name='prd_cd_range_nm' class="form-control form-control-sm w-100 sch-prdcd-range" readonly style="background-color: #fff;">
                                        <a href="#" class="btn btn-sm btn-outline-primary sch-prdcd-range"><i class="bx bx-dots-horizontal-rounded fs-16"></i></a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 inner-td">
                        <div class="form-group">
                            <label>창고명</label>
                            <div class="form-inline inline_btn_box">
                                <input type='hidden' id="storage_nm" name="storage_nm">
                                <select id="storage_no" name="storage_no[]" class="form-control form-control-sm select2-storage multi_select"  multiple></select>
                                <a href="javascript:void(0);" class="btn btn-sm btn-outline-primary sch-storage"><i class="bx bx-dots-horizontal-rounded fs-16"></i></a>
                            </div>
                        </div>
                    </div>
                </div>
			</div>
		</div>
		<div class="resul_btn_wrap mb-3">
			<a href="#" id="search_sbtn" onclick="Search();" class="btn btn-sm btn-primary shadow-sm mr-1"><i class="fas fa-search fa-sm text-white-50"></i> 검색</a>
			<!-- 2023-05-25 검색조건 초기화 주석처리 -양대성- -->
            <!-- <input type="reset" id="search_reset" value="검색조건 초기화" class="btn btn-sm btn-outline-primary shadow-sm" onclick="formReset()"> -->
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
                <div class="d-flex justify-content-end">
	                <div class="custom-control custom-checkbox form-check-box" style="width:170px;">
		                <select name='grid_expand_select' id="grid_expand_select" class="form-control form-control-sm">
			                <option value=''>전체 펼쳐보기</option>
			                <option value='0'>품목별 펼쳐보기</option>
			                <option value='1'>브랜드별 펼쳐보기</option>
			                <option value='2'>품번별 펼쳐보기</option>
			                <option value='3'>컬러별 펼쳐보기</option>
		                </select>
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
	.ag-row-level-1 {background-color: #f2f2f2 !important;}
	.ag-row-level-2 {background-color: #e2e2e2 !important;}
	.ag-row-level-3 {background-color: #d2d2d2 !important;}
	.ag-row-level-4 {background-color: #edf4fd !important;}
</style>

<script type="text/javascript" charset="utf-8">

    const pinnedRowData = [{ item: 'total' }];
    const sumValuesFunc = (params) => params.values.reduce((a,c) => a + ((c || 0) * 1), 0);

    const columns = [
        { field: "item_nm", headerName: "품목명", rowGroup: true, hide: true },
        { field: "brand_nm", headerName: "브랜드명", rowGroup: true, hide: true },
        { field: "prd_cd_p", headerName: "품번", rowGroup: true, hide: true },
        { field: "color_nm", headerName: "컬러명", rowGroup: true, hide: true },

        { field: "item", headerName: "품목", width: 60, pinned: 'left', cellClass: 'hd-grid-code',
            aggFunc: (params) => params.values.length > 0 ? params.values[0] : '',
			cellRenderer: (params) => params.value == 'total' ? '합계' : params.node.level == 0 ? params.value : '',
        },
        { headerName: '품목명', showRowGroup: 'item_nm', cellRenderer: 'agGroupCellRenderer', width: 130, pinned: 'left' },
        { field: "brand", headerName: "브랜드", pinned: 'left', width: 60, cellClass: 'hd-grid-code',
            aggFunc: (params) => params.values.length > 0 ? params.values[0] : '',
			cellRenderer: (params) => params.value == 'total' ? '합계' : params.node.level == 1 ? params.value : '',
        },
        { headerName: '브랜드명', showRowGroup: 'brand_nm', cellRenderer: 'agGroupCellRenderer', width: 130, pinned: 'left' },
        { headerName: '품번', showRowGroup: 'prd_cd_p', cellRenderer: 'agGroupCellRenderer', width: 150, pinned: 'left' },
        { field: "prd_cd", headerName: "바코드", width: 130, pinned: 'left' },
        { field: "goods_no", headerName: "온라인코드", width: 70, cellClass: 'hd-grid-code',
            aggFunc: (params) => (params?.rowNode?.level || 0) > 1 ? params.values[0] : '',
        },
        { field: "goods_nm", headerName: "상품명", width: 200,
			aggFunc: (params) => (params?.rowNode?.level || 0) > 1 ? params.values[0] : '',
            cellRenderer: function (params) {
                if (params.data?.goods_no == '' || params.node.aggData?.goods_no == '') {
                    return '<a href="javascript:void(0);" onclick="return blank_goods_no();">' + (params.value || '') + '</a>';
                } else {
                    let goods_no = params.data ? params.data.goods_no : params.node.aggData ? params.node.aggData.goods_no : '';
                    return '<a href="#" onclick="return openStoreProduct(\'' + goods_no + '\');">' + (params.value || '') + '</a>';
                }
            }
        },
        { field: "goods_nm_eng", headerName: "상품명(영문)", width: 150,
			aggFunc: (params) => (params?.rowNode?.level || 0) > 1 ? params.values[0] : '',
            cellRenderer: function (params) {
                if (params.data?.goods_no == '' || params.node.aggData?.goods_no == '') {
                    return '<a href="javascript:void(0);" onclick="return blank_goods_no();">' + (params.value || '') + '</a>';
                } else {
                    let goods_no = params.data ? params.data.goods_no : params.node.aggData ? params.node.aggData.goods_no : '';
                    return '<a href="#" onclick="return openHeadProduct(\'' + goods_no + '\');">' + (params.value || '') + '</a>';
                }
            }
        },
        { field: "color", headerName: "컬러", width: 50, cellClass: 'hd-grid-code',
			aggFunc: (params) => params.values.length > 0 ? params.values[0] : '',
			cellRenderer: (params) => params.node.level == 3 ? params.value : '',
        },
		{ headerName: '컬러명', showRowGroup: 'color_nm', cellRenderer: 'agGroupCellRenderer', width: 130 },
        { field: "size", headerName: "사이즈", width: 50, cellClass: 'hd-grid-code' },
        { field: "goods_opt", headerName: "옵션", width: 150 },
        { field: "goods_sh", headerName: "정상가", width: 80, type: 'currencyType', aggFunc: sumValuesFunc },
        { field: "price", headerName: "현재가", width: 80, type: 'currencyType', aggFunc: sumValuesFunc },
        { field: "wonga", headerName: "원가", width: 80, type: 'currencyType', aggFunc: sumValuesFunc },
        { field: "total_in_qty", headerName: "발주량", width: 60, type: 'currencyType', aggFunc: sumValuesFunc },
        {
            headerName: "입고",
            children: [
                {field: "term_in_qty", headerName: "수량", width: 60, type: "currencyType", aggFunc: sumValuesFunc },
                {field: "term_in_goods_sh", headerName: "정상가", width: 80, type: "currencyType", aggFunc: sumValuesFunc },
                {field: "term_in_price", headerName: "현재가", width: 80, type: "currencyType", aggFunc: sumValuesFunc },
                {field: "term_in_wonga", headerName: "원가", width: 80, type: "currencyType", aggFunc: sumValuesFunc },
            ]
        },
        {
            headerName: "출고",
            children: [
                {field: "first_release_date", headerName: "최초출고일", width: 80, cellClass: 'hd-grid-code' },
                {field: "term_release_qty", headerName: "출고", width: 60, type: "currencyType", aggFunc: sumValuesFunc },
                {field: "term_return_qty", headerName: "반품", width: 60, type: "currencyType", aggFunc: sumValuesFunc },
                {field: "term_out_qty", headerName: "총출고", width: 60, type: "currencyType", aggFunc: sumValuesFunc },
            ]
        },
        {
            headerName: "판매",
            children: [
				{field: "first_sale_date", headerName: "최초판매일", width: 80, cellClass: 'hd-grid-code' },
                {field: "sale_qty", headerName: "수량", width: 60, type: "currencyType", aggFunc: sumValuesFunc },
                {field: "sale_goods_sh", headerName: "정상가", width: 80, type: "currencyType", aggFunc: sumValuesFunc },
                {field: "sale_price", headerName: "현재가", width: 80, type: "currencyType", aggFunc: sumValuesFunc },
                {field: "sale_recv_price", headerName: "실판매가", width: 80, type: "currencyType", aggFunc: sumValuesFunc },
                {field: "sale_wonga", headerName: "원가", width: 80, type: "currencyType", aggFunc: sumValuesFunc },
                {field: "sale_ratio", headerName: "판매율", width: 80, cellStyle: {'text-align' : 'right'},
	                aggFunc: (params) => {
						return ( params.rowNode ? params.rowNode.aggData ? Math.round(params.rowNode.aggData.sale_qty / (params.rowNode.aggData.term_in_qty === 0 ? 1 : params.rowNode.aggData.term_in_qty) * 100) : 0 : 0 ) + '%';
	                } 
                },
                {field: "discount_ratio", headerName: "할인율", width: 80, cellStyle: {'text-align' : 'right'},
					cellRenderer: function(params) {
						if(params.value != undefined)
							return params.value + '%';
					}
				},
            ]
        },
        {
            headerName: "창고재고",
            children: [
				{field: "term_storage_qty", headerName: "수량", width: 60, type: "currencyType", aggFunc: sumValuesFunc },
				{field: "term_storage_goods_sh", headerName: "정상가", width: 80, type: "currencyType", aggFunc: sumValuesFunc },
				{field: "term_storage_price", headerName: "현재가", width: 80, type: "currencyType", aggFunc: sumValuesFunc },
				{field: "term_storage_wonga", headerName: "원가", width: 80, type: "currencyType", aggFunc: sumValuesFunc },
            ]
        },
		{
			headerName: "매장재고",
			children: [
				{field: "term_store_qty", headerName: "수량", width: 60, type: "currencyType", aggFunc: sumValuesFunc },
				{field: "term_store_goods_sh", headerName: "정상가", width: 80, type: "currencyType", aggFunc: sumValuesFunc },
				{field: "term_store_price", headerName: "현재가", width: 80, type: "currencyType", aggFunc: sumValuesFunc },
				{field: "term_store_wonga", headerName: "원가", width: 80, type: "currencyType", aggFunc: sumValuesFunc },
			]
		},
		{
			headerName: "총재고",
			children: [
				{field: "term_total_qty", headerName: "수량", width: 60, type: "currencyType", aggFunc: sumValuesFunc },
				{field: "term_total_goods_sh", headerName: "정상가", width: 80, type: "currencyType", aggFunc: sumValuesFunc },
				{field: "term_total_price", headerName: "현재가", width: 80, type: "currencyType", aggFunc: sumValuesFunc },
				{field: "term_total_wonga", headerName: "원가", width: 80, type: "currencyType", aggFunc: sumValuesFunc },
			]
		},
		{ field: "term_sale_qty", headerName: "판매계", width: 60, type: "currencyType", aggFunc: sumValuesFunc },
		{ field: "com_id", headerName: "업체코드", width: 60, cellClass: 'hd-grid-code',
			aggFunc: (params) => (params?.rowNode?.level || 0) > 1 ? params.values[0] : '',
		},
		{ field: "com_nm", headerName: "업체명", width: 80,
			aggFunc: (params) => (params?.rowNode?.level || 0) > 1 ? params.values[0] : '',
		},
    ];

	const pApp = new App('', { gridId:"#div-gd", height: 265 });
	let gx;

	$(document).ready(function() {
		pApp.ResizeGrid(265);
		pApp.BindSearchEnter();
		let gridDiv = document.querySelector(pApp.options.gridId);
		gx = new HDGrid(gridDiv, columns, {
			defaultColDef: {
				suppressMenu: true,
				resizable: true,
				sortable: true,
			},
			pinnedTopRowData: pinnedRowData,
            getRowStyle: (params) => { // 고정된 row styling
                if (params.node.rowPinned)  return { 'font-weight': 'bold', 'background-color': '#eee', 'border': 'none'};
            },
			rollup: true,
			groupSuppressAutoColumn: true,
			suppressAggFuncInHeader: true,
			enableRangeSelection: true,
			animateRows: true,
		});

		load_store_channel();
		
		$("#grid_expand_select").on('change', function (e) {
			setAllRowGroupExpandedBySelectedItem(e.target.value);
		});

		// 엑셀다운로드 레이어 오픈
		$(".export-excel").on("click", function (e) {
			depthExportChecker.Open({
				depths: ['품목별', '브랜드별', '품번별', '컬러별'],
				download: (level) => {
					gx.Download('품번별종합분석현황(기간)_{{ date('YmdH') }}.xlsx', { type: 'excel', level: level });
				}
			});
		});
	});

	function Search() {
        let data = $('form[name="search"]').serialize();
        gx.Request('/store/sale/sal27/search', data, -1, function(e) {
			setMonthSaleColumn(e.head?.sale_month || []);
        });
	}
	
	function setMonthSaleColumn(sale_month) {
		columns.splice(30);
		const cols = columns.concat(sale_month.map(mon => ({ field: mon.key, headerName: mon.kor_nm, width: 100, type: "currencyType", aggFunc: sumValuesFunc })));
		gx.gridOptions.api.setColumnDefs([]);
		gx.gridOptions.api.setColumnDefs(cols);
		setAllRowGroupExpandedBySelectedItem($("#grid_expand_select").val());
		updatePinnedRow();
	}

	function setAllRowGroupExpandedBySelectedItem(value) {
		if (!gx) return;
		if (value === '') gx.gridOptions.api.expandAll();
		else {
			gx.gridOptions.api.forEachNode(node => {
				node.expanded = node.level !== value * 1;
			});
			gx.gridOptions.api.onGroupExpandedOrCollapsed();
		}
	}

	const updatePinnedRow = () => {
		const keys = gx.gridOptions.api.getColumnDefs()
			.reduce((a, c) => (!!c.children && Array.isArray(c.children)) ? a.concat(c.children.concat(c)) : a.concat(c), [])
			.filter(col => col.type === 'currencyType')
			.map(col => col.field);
		const totals = {};

		const rows = gx.getRows();
		if (rows && Array.isArray(rows) && rows.length > 0) {
			rows.forEach(row => {
				for (let i = 0; i < keys.length; i++) {
					totals[keys[i]] = (totals[keys[i]] ? totals[keys[i]] : 0) + parseInt(row?.[keys[i]] || 0);
				}
			});
		}

		let pinnedRow = gx.gridOptions.api.getPinnedTopRow(0);
		gx.gridOptions.api.setPinnedTopRowData([{ 
			...pinnedRow.data, 
			...totals, 
			sale_ratio: Math.round( totals.sale_qty / (totals.term_in_qty === 0 ? 1 : totals.term_in_qty) * 100 ) + '%',
		}]);
	};
</script>
@stop

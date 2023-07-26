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
										<input type="text" class="form-control form-control-sm docs-date month" name="sdate" value="{{ $sdate }}" autocomplete="off">
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
										<input type="text" class="form-control form-control-sm docs-date month" name="edate" value="{{ $edate }}" autocomplete="off">
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
                                        <input type='text' id="prd_cd_range_nm" name='prd_cd_range_nm' onclick="openApi();" class="form-control form-control-sm w-100 ac-style-no" readonly style="background-color: #fff;">
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
				<div class="row">
                    <div class="col-lg-4 inner-td">
                        <div class="form-group">
                            <label for="">자료수/정렬</label>
                            <div class="form-inline">
                                <div class="form-inline-inner input_box" style="width:24%;">
                                    <select name="limit" class="form-control form-control-sm">
                                        <option value="500">500</option>
                                        <option value="1000">1000</option>
                                        <option value="2000">2000</option>
                                    </select>
                                </div>
                                <span class="text_line">/</span>
                                <div class="form-inline-inner input_box" style="width:45%;">
                                    <select name="ord_field" class="form-control form-control-sm">
                                        <option value="pc.prd_cd">바코드</option>
                                        <option value="g.goods_no">온라인코드</option>
                                        <option value="g.goods_nm">상품명</option>
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

<script type="text/javascript" charset="utf-8">

    const pinnedRowData = [{ br_cd: 'total', tag_price: 0, price: 0, wonga: 0, order_amt: 0,
                            order_qty: 0, order_tag_price: 0, order_price: 0, order_wonga: 0, 
                            release_qty: 0, release_qty: 0, return_qty: 0, total_release_qty: 0, sale_qty: 0,
                            sale_tag_price: 0, sale_price: 0, sale_recv_amt: 0, sale_wonga: 0, sale_rate: 0,
                            sale_discount_rate: 0, storage_stock_qty: 0, store_stock_qty: 0, total_stock_qty: 0
    }];
    const sumValuesFunc = (params) => params.values.reduce((a,c) => a + (c * 1), 0);

	var columns = [
        { field: "item_nm", headerName: "품목명", pinned:'left', maxWidth:20, cellStyle: { 'text-align': "center" }, rowGroup: true, hide: true},
        { field: "brand_nm", headerName: "브랜드명", width:90, rowGroup: true, hide: true},
        { field: "prd_cd_p", headerName: "상품코드일련", width:120, rowGroup: true, hide: true },

        { field: "item", headerName: "품목", width:60, pinned:'left',cellStyle: { 'text-align': "center" },
            aggFunc: (params) => params.values.length > 0 ? params.values[0] : '',
			cellRenderer: (params) => params.value == 'total' ? '합계' : params.node.level == 0 ? params.value : '',
        },
        {headerName: '품목명', showRowGroup: 'item_nm', cellRenderer: 'agGroupCellRenderer', width: 130, pinned:'left'},
        { field: "br_cd", headerName: "브랜드", pinned:'left', width:60, cellStyle: { 'text-align': "center" },
            aggFunc: (params) => params.values.length > 0 ? params.values[0] : '',
			cellRenderer: (params) => params.value == 'total' ? '합계' : params.node.level == 1 ? params.value : '',
        },
        {headerName: '브랜드명', showRowGroup: 'brand_nm', pinned:'left', cellRenderer: 'agGroupCellRenderer', width: 120, pinned:'left'},
        {headerName: '상품코드일련', showRowGroup: 'prd_cd_p', cellRenderer: 'agGroupCellRenderer', width: 130, pinned:'left'},
        { field: "prd_cd", headerName: "바코드", width:120, pinned:'left',
            aggFunc: (params) => params.values.length > 0 ? params.values[0] : '',
			cellRenderer: (params) => params.value == 'total' ? '합계' : params.node.level == 3 ? params.value : '',
        },
        { field: "goods_no", headerName: "온라인코드", minWidth: 70},
        { field: "goods_nm", headerName: "상품명", minWidth:250,
            cellRenderer: function (params) {
                if (params.data?.goods_no == '' || params.node.aggData?.goods_no == '') {
                    return '<a href="javascript:void(0);" onclick="return blank_goods_no();">' + (params.value || '') + '</a>';
                } else {
                    let goods_no = params.data ? params.data.goods_no : params.node.aggData ? params.node.aggData.goods_no : '';
                    return '<a href="#" onclick="return openHeadProduct(\'' + goods_no + '\');">' + (params.value || '') + '</a>';
                }
            }
        },
        { field: "goods_nm_eng", headerName: "상품명(영문)", minWidth:250,
            cellRenderer: function (params) {
                if (params.data?.goods_no == '' || params.node.aggData?.goods_no == '') {
                    return '<a href="javascript:void(0);" onclick="return blank_goods_no();">' + (params.value || '') + '</a>';
                } else {
                    let goods_no = params.data ? params.data.goods_no : params.node.aggData ? params.node.aggData.goods_no : '';
                    return '<a href="#" onclick="return openHeadProduct(\'' + goods_no + '\');">' + (params.value || '') + '</a>';
                }
            }
        },
        { field: "color", headerName: "컬러", minWidth:50},
        { field: "color_nm", headerName: "컬러명", minWidth:80},
        { field: "size", headerName: "사이즈", minWidth:50},
        { field: "goods_opt", headerName: "옵션", minWidth:100},
        { field: "tag_price", headerName: "Tag가", minWidth:80, type: 'currencyType',aggFunc: sumValuesFunc},
        { field: "price", headerName: "현재가", minWidth:80, type: 'currencyType',aggFunc: sumValuesFunc},
        { field: "wonga", headerName: "원가", minWidth:80, type: 'currencyType',aggFunc: sumValuesFunc},
        { field: "order_amt", headerName: "발주량", minWidth:100,type: 'currencyType', aggFunc: sumValuesFunc},
        {
            headerName: "입고",
            children: [
                {field: "order_qty", headerName: "수량", minWidth: 60, type: "currencyType", aggFunc: sumValuesFunc},
                {field: "order_tag_price", headerName: "TAG가", minWidth: 80, type: "currencyType", aggFunc: sumValuesFunc},
                {field: "order_price", headerName: "현재가", minWidth: 80, type: "currencyType", aggFunc: sumValuesFunc},
                {field: "order_wonga", headerName: "원가", minWidth: 80, type: "currencyType", aggFunc: sumValuesFunc},
            ]
        },
        {
            headerName: "출고",
            children: [
                {field: "release_first_date", headerName: "최초출고일", minWidth: 100, cellStyle: { 'text-align': "center" }},
                {field: "release_qty", headerName: "출고", minWidth: 60, type: "currencyType", aggFunc: sumValuesFunc},
                {field: "return_qty", headerName: "반품", minWidth: 80, type: "currencyType", aggFunc: sumValuesFunc},
                {field: "total_release_qty", headerName: "총출고", minWidth: 80, type: "currencyType", aggFunc: sumValuesFunc},
            ]
        },
        {
            headerName: "판매",
            children: [
                {field: "sale_qty", headerName: "수량", minWidth: 60, type: "currencyType", aggFunc: sumValuesFunc},
                {field: "sale_tag_price", headerName: "TAG가", minWidth: 80, type: "currencyType", aggFunc: sumValuesFunc},
                {field: "sale_price", headerName: "현재가", minWidth: 80, type: "currencyType", aggFunc: sumValuesFunc},
                {field: "sale_recv_amt", headerName: "실판매가", minWidth: 80, type: "currencyType", aggFunc: sumValuesFunc},
                {field: "sale_wonga", headerName: "원가", minWidth: 80, type: "currencyType", aggFunc: sumValuesFunc},
                {field: "sale_rate", headerName: "판매율", minWidth: 80, type: "currencyType", aggFunc: sumValuesFunc},
                {field: "sale_discount_rate", headerName: "할인율", minWidth: 80, type: "currencyType", aggFunc: sumValuesFunc},
            ]
        },
        {
            headerName: "재고",
            children: [
                {field: "storage_stock_qty", headerName: "창고", minWidth: 80, type: "currencyType", aggFunc: sumValuesFunc},
                {field: "store_stock_qty", headerName: "매장", minWidth: 80, type: "currencyType", aggFunc: sumValuesFunc},
                {field: "total_stock_qty", headerName: "총재고", minWidth: 80, type: "currencyType", aggFunc: sumValuesFunc},
            ]
        },
        // { headerName: "", field: "", width: "auto" }
    ];

	const pApp = new App('', { gridId:"#div-gd" });
	let gx;

	$(document).ready(function() {
		pApp.ResizeGrid(275);
		pApp.BindSearchEnter();
		let gridDiv = document.querySelector(pApp.options.gridId);
		gx = new HDGrid(gridDiv, columns, {
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
	});
    
    var mutable_cols = [];

	function Search() {
		return alert("개발중입니다.");
        let data = $('form[name="search"]').serialize();
        gx.Request('/store/sale/sal27/search', data, -1, function(e) {
            updatePinnedRow();
            formatMonth(e);
            setAllRowGroupExpanded($("#grid_expand").is(":checked"));
        });
	}


    const formatMonth = async (e) => {
		months = e.head.months;
        month = months.length;
		setMutableColumns(month);
	};

    const setMutableColumns = (month) => {
		gx.gridOptions.api.setColumnDefs([]);
		mutable_cols = [];
		columns.map(col => {
			mutable_cols.push(col);
		});

        // console.log(mutable_cols);
		mutable_cols.push(MonthColumns(month));
		mutable_cols.push({ headerName: "", field: "", width: "auto" });
		gx.gridOptions.api.setColumnDefs(mutable_cols);
		// autoSizeColumns(gx, ["nvl"]);
		// gx.CalAggregation();
	};

    const MonthColumns = (month) => {
        let col = { fields: "month", headerName: "기간", children: [] };
        for (let i = 0; i < month; i++) {
            const month_field = months[i].val;
            const month_headerName = months[i].fmt;
            autoSizeColumns(gx, [""]);
            let add_col = {field: month_field, headerName: month_headerName, minWidth: 80, type: "currencyType", aggFunc: sumValuesFunc};
            col.children.push(add_col)
        }
        return col;
	};

    const autoSizeColumns = (grid, except = [], skipHeader = false) => {
		const allColumnIds = [];
		gx.gridOptions.columnApi.getAllColumns().forEach((column) => {
			if (except.includes(column.getId())) return;
			allColumnIds.push(column.getId());
		});
		gx.gridOptions.columnApi.autoSizeColumns(allColumnIds, skipHeader);
	};

    // 검색조건 초기화
    const formReset = () => {
		document.search.reset();
	};

    //상품옵션 범위검색 인풋박스 클릭시 API 오픈
    function openApi() {
        document.getElementsByClassName('sch-prdcd-range')[0].click();
    }


    const updatePinnedRow = () => {
        let [ tag_price, price, wonga, order_amt, order_qty, order_tag_price, order_price, order_wonga
            , release_qty, return_qty, total_release_qty, sale_qty, sale_tag_price, sale_price
            , sale_recv_amt, sale_wonga, sale_rate, sale_discount_rate, storage_stock_qty
            , store_stock_qty, total_stock_qty ] = [ 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0 ];
        const rows = gx.getRows();

        if (rows && Array.isArray(rows) && rows.length > 0) {
            rows.forEach((row, idx) => {
                tag_price += parseInt(row?.tag_price || 0);
                price += parseInt(row?.price || 0);
                wonga += parseInt(row?.wonga || 0);
                order_amt += parseInt(row?.order_amt || 0);
                order_qty += parseInt(row?.order_qty || 0);
                order_tag_price += parseInt(row?.order_tag_price || 0);
                order_price += parseInt(row?.order_price || 0);
                order_wonga += parseInt(row?.order_wonga || 0);
                release_qty += parseInt(row?.release_qty || 0);
                return_qty += parseInt(row?.return_qty || 0);
                total_release_qty += parseInt(row?.total_release_qty || 0);
                sale_qty += parseInt(row?.sale_qty || 0);
                sale_tag_price += parseInt(row?.sale_tag_price || 0);
                sale_price += parseInt(row?.sale_price || 0);
                sale_recv_amt += parseInt(row?.sale_recv_amt || 0);
                sale_wonga += parseInt(row?.sale_wonga || 0);
                sale_rate += parseInt(row?.sale_rate || 0);
                sale_discount_rate += parseInt(row?.sale_discount_rate || 0);
                storage_stock_qty += parseInt(row?.storage_stock_qty || 0);
                store_stock_qty += parseInt(row?.store_stock_qty || 0);
                total_stock_qty += parseInt(row?.total_stock_qty || 0);
                
            });
        }
        let pinnedRow = gx.gridOptions.api.getPinnedTopRow(0);
        gx.gridOptions.api.setPinnedTopRowData([
            { ...pinnedRow.data, tag_price: tag_price, price: price, wonga: wonga, order_amt: order_amt, order_qty: order_qty, order_tag_price: order_tag_price, order_price: order_price
                , order_wonga: order_wonga, release_qty: release_qty, return_qty: return_qty, total_release_qty: total_release_qty, sale_qty: sale_qty, sale_tag_price: sale_tag_price
                , sale_price: sale_price, sale_recv_amt: sale_recv_amt, sale_wonga: sale_wonga, sale_rate: sale_rate, sale_discount_rate: sale_discount_rate
                , storage_stock_qty: storage_stock_qty, store_stock_qty: store_stock_qty, total_stock_qty: total_stock_qty
            }
        ]);
    };

     // 매장 검색 클릭 이벤트 바인딩 및 콜백 사용
     $( ".sch-storage" ).on("click", function() {
        searchStorage.Open();
    });

</script>
@stop

@extends('shop_with.layouts.layout')
@section('title','일반RT')
@section('content')
<div class="page_tit">
    <h3 class="d-inline-flex">매장요청RT</h3>
    <div class="d-inline-flex location">
        <span class="home"></span>
        <span>매장관리</span>
        <span>/ 매장RT관리</span>
        <span>/ 매장요청RT</span>
    </div>
</div>

<style>
    .hide-element-title {position: relative; top: -80px;}
</style>

<form method="get" name="search">
    <div id="search-area" class="search_cum_form">
        <div class="card mb-3">

            <div class="d-flex card-header justify-content-between">
                <h4>검색</h4>
                <div class="flax_box">
                    <a href="#" id="search_sbtn" onclick="Search();" class="btn btn-sm btn-primary shadow-sm mr-1"><i class="fas fa-search fa-sm text-white-50"></i> 검색</a>
                    <!-- 2023-05-25 검색조건 초기화 주석처리 -양대성- -->
                    <!-- <a href="javascript:void(0);" class="btn btn-sm btn-outline-primary shadow-sm pl-2 mr-1" onclick="initSearch()">검색조건 초기화</a> -->
                    <a href="/shop/stock/stk20" class="btn btn-sm btn-outline-primary shadow-sm pl-2 mr-1"><i class="fas fa-step-backward fa-sm"></i> 매장RT 리스트</a>
                    <div id="search-btn-collapse" class="btn-group mb-0 mb-sm-0"></div>
                </div>
            </div>

            <input type='hidden' name='goods_nos' value='' />
            <div class="card-body">
                <div class="row">
                    <div class="col-lg-4 inner-td">
                        <div class="form-group">
                            <label for="prd_cd">바코드</label>
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
                            <label for="style_no">스타일넘버/온라인코드</label>
                            <div class="form-inline">
                                <div class="form-inline-inner input_box">
                                    <input type='text' class="form-control form-control-sm ac-style-no search-enter" name='style_no' id="style_no" value="{{ $style_no }}">
                                </div>
                                <span class="text_line">/</span>
                                <div class="form-inline-inner input-box" style="width:47%">
                                    <div class="form-inline-inner inline_btn_box">
                                        <input type='text' class="form-control form-control-sm w-100 search-enter" name='goods_no' id='goods_no' value=''>
                                        <a href="#" class="btn btn-sm btn-outline-primary sch-goods_nos"><i class="bx bx-dots-horizontal-rounded fs-16"></i></a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-4 inner-td">
                        <div class="form-group">
                            <label for="goods_nm">상품명</label>
                            <div class="flax_box">
                                <input type='text' class="form-control form-control-sm ac-goods-nm search-enter" name='goods_nm' id="goods_nm" value=''>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 inner-td">
                        <div class="form-group">
                            <label for="goods_nm_eng">상품명(영문)</label>
                            <div class="flax_box">
                                <input type='text' class="form-control form-control-sm ac-goods-nm-eng search-enter" name='goods_nm_eng' id="goods_nm_eng" value=''>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 inner-td">
                        <div class="form-group">
                            <label for="">자료수/정렬</label>
                            <div class="form-inline">
                                <div class="form-inline-inner input_box" style="width:24%;">
                                    <select name="limit" class="form-control form-control-sm">
                                        <option value="500">500</option>
                                        <option value="1000">1000</option>
                                        <option value="2000">2000</option>
                                        <option value="5000">5000</option>
                                    </select>
                                </div>
                                <span class="text_line">/</span>
                                <div class="form-inline-inner input_box" style="width:45%;">
                                    <select name="ord_field" class="form-control form-control-sm">
                                        <option value="pc.rt">등록일</option>
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
                    <div class="col-lg-4" style="display:none">
                        <div class="form-group">
                            <label for="good_types">검색일자</label>
                            <div class="docs-datepicker flex_box">
                                <div class="input-group">
                                    <input type="text" class="form-control form-control-sm docs-date" name="sdate" value="{{ @$today }}" autocomplete="off">
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
                </div>
            </div>
        </div>

        <div class="resul_btn_wrap mb-3">
            <a href="#" id="search_sbtn" onclick="Search();" class="btn btn-sm btn-primary shadow-sm mr-1"><i class="fas fa-search fa-sm text-white-50"></i> 검색</a>
            <!-- 2023-05-25 검색조건 초기화 주석처리 -양대성- -->
            <!-- <a href="javascript:void(0);" class="btn btn-sm btn-outline-primary shadow-sm pl-2 mr-1" onclick="initSearch()">검색조건 초기화</a> -->
            <a href="/shop/stock/stk20" class="btn btn-sm btn-outline-primary shadow-sm pl-2 mr-1"><i class="fas fa-step-backward fa-sm"></i> 매장RT 리스트</a>
            <div class="search_mode_wrap btn-group mr-2 mb-0 mb-sm-0"></div>
        </div>

    </div>
</form>

<div class="row show_layout mb-3">
    <div class="col-lg-5 pr-1">
        <div class="card shadow mb-0 pt-2 pt-sm-0">
            <div class="card-title">
                <div class="filter_wrap mt-2 pt-2">
                    <div class="d-flex justify-content-between">
                        <h6 class="m-0 font-weight-bold">총 : <span id="gd-product-total" class="text-primary">0</span>건</h6>
                        <div class="custom-control custom-checkbox form-check-box pr-2" style="display:inline-block;">
                            <input type="checkbox" class="custom-control-input" name="grid_expand" id="grid_expand" onchange="return setAllRowGroupExpanded(this.checked);" checked>
                            <label class="custom-control-label font-weight-normal" for="grid_expand">항목펼쳐보기</label>
                        </div>
                    </div>
                </div>
            </div>
            <div class="table-responsive">
                <div id="div-gd-product" class="ag-theme-balham"></div>
            </div>
        </div>
    </div>
    <div class="col-lg-7">
		<div class="card shadow mb-0 pt-2 pt-sm-0">
			<div class="card-title">
				<div class="filter_wrap mt-2 pt-2">
					<h6 class="font-weight-bold m-0 mr-2">총 : <span id="gd-stock-total" class="text-primary">0</span>건 <strong id="selected_prd_nm" class="ml-2 fs-14 text-danger font-weight-normal"></strong></h6>
					<div class="d-flex justify-content-between mt-1">
						<div class="d-flex">
							<span class="mr-2">보내는매장 : 판매채널/매장구분</span>
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
									<select id='store_channel_kind' name='store_channel_kind' class="form-control form-control-sm" disabled >
										<option value=''>전체</option>
										@foreach ($store_kind as $sk)
											<option value='{{ $sk->store_kind_cd }}'>{{ $sk->store_kind }}</option>
										@endforeach
									</select>
								</div>
							</div>
						</div>
						<div class="d-flex">
							<p class="mr-2">(대표)창고재고 <span id="storage_stock" class="text-primary font-weight-bold" style="cursor: pointer;" onclick="return openStorageStockPopup();">0</span>개 / 우리매장재고 <span id="store_stock" class="text-primary font-weight-bold" style="cursor: pointer;" onclick="return openStorageStockPopup();">0</span>개</p>
							<a href="javascript:void(0);" onclick="AddRTToFinalTable()" class="btn btn-sm btn-outline-primary shadow-sm" style="min-width:130px;">RT리스트에 등록</a>
						</div>
					</div>
				</div>
			</div>
			<div class="table-responsive">
				<div id="div-gd-stock" class="ag-theme-balham"></div>
			</div>
		</div>
    </div>
</div>

<div id="final_table_area" class="hide-element-title"></div>

<!-- RT 최종 등록 테이블 -->
<div class="card shadow mb-0 last-card pt-2 pt-sm-0">
    <div class="card-body">
        <div class="card-title">
            <div class="filter_wrap">
                <div class="d-flex justify-content-between">
                    <h6 class="m-0 font-weight-bold">총 : <span id="gd-rt-total" class="text-primary">0</span>건</h6>
                    <div class="d-flex">
                        <a href="javascript:void(0);" onclick="RequestRT()" class="btn btn-sm btn-primary shadow-sm mr-2">RT등록</a>
                        <a href="javascript:void(0);" onclick="DeleteRows()" class="btn btn-sm btn-outline-primary shadow-sm">삭제</a>
                    </div>
                </div>
            </div>
        </div>
        <div class="table-responsive">
            <div id="div-gd-rt" class="ag-theme-balham"></div>
        </div>
    </div>
</div>

<style>
    .ag-row-level-1 {background-color: #f2f2f2 !important;}
</style>

<script type="text/javascript" charset="utf-8">
    const stores = <?= json_encode(@$stores) ?> ;

    let product_columns = [
        {field: "idx", hide: true},
        {field: "prd_cd_p", headerName: "품번", rowGroup: true, hide: true},
        {headerName: '품번', showRowGroup: 'prd_cd_p', pinned: "left", cellRenderer: 'agGroupCellRenderer', minWidth: 150},
        {field: "prd_cd", headerName: "바코드", pinned: 'left', width: 120, cellStyle: {"text-align": "center"},
            cellRenderer: (params) => {
                if (!params.data) return '';
                return `<a href="javascript:void(0);" onclick="SearchStock('${params.rowIndex}')">${params.value}</a>`;
            }
        },
        {field: "opt_kind_nm", headerName: "품목", width: 60, cellStyle: {"text-align": "center"}, aggFunc: "first"},
        {field: "brand_nm", headerName: "브랜드", width: 70, cellStyle: {"text-align": "center"}, aggFunc: "first"},
        {field: "style_no",	headerName: "스타일넘버", width: 70, cellStyle: {"text-align": "center"}, aggFunc: "first"},
        {field: "goods_nm",	headerName: "상품명", width: 150, aggFunc: "first",
            cellRenderer: function (params) {
                if (params.data?.goods_no == '' || params.node.aggData?.goods_no == '') {
                    return '<a href="javascript:void(0);" onclick="return alert(`온라인코드가 비어있는 상품입니다.`);">' + (params.value || '') + '</a>';
                } else {
                    let goods_no = params.data ? params.data.goods_no : params.node.aggData ? params.node.aggData.goods_no : '';
                    return '<a href="#" onclick="return openShopProduct(\'' + goods_no + '\');">' + (params.value || '') + '</a>';
                }
            }
        },
        {field: "goods_nm_eng", headerName: "상품명(영문)", width: 150, aggFunc: "first"},
        {field: "goods_opt", headerName: "옵션", width: 150, aggFunc: "first"},
        {field: "color", headerName: "컬러", width: 55, cellStyle: {"text-align": "center"}, aggFunc: "first"},
        {field: "color_nm", headerName: "컬러명", width: 70, aggFunc: "first"},
        {field: "size", headerName: "사이즈", width: 55, cellStyle: {"text-align": "center"}, aggFunc: "first"},
        {field: "goods_sh", headerName: "정상가", type: "currencyType", width: 60, aggFunc: "first"},
        {field: "price", headerName: "현재가", type: "currencyType", width: 60, aggFunc: "first"},
        {width: "auto"},
    ];

    const pinnedRowData = [{ dep_store_nm : '합계', qty : 0, wqty : 0, rt_qty : 0}];

    let stock_columns = [
        {field: "prd_cd", hide: true},
        {headerName: "No", pinned: "left", valueGetter: "node.id", cellRenderer: "loadingRenderer", width: 30, cellStyle: {"text-align": "center"},
			cellRenderer: (params) => params.node.rowPinned === 'top' ? '' : parseInt(params.value) + 1,
		},
        {field: "chk", headerName: '', cellClass: 'hd-grid-code', headerCheckboxSelection: true, checkboxSelection: true, sort: null, width: 30},
        {field: "dep_store_cd",	headerName: "매장코드", pinned: 'left', width: 70, cellStyle: {"text-align": "center"}},
        {field: "dep_store_nm",	headerName: "보내는 매장", pinned: 'left', width: 140},
        {field: "store_cd",	headerName: "매장코드", pinned: 'left', width: 70, cellStyle: {"text-align": "center"}, hide:true},
        {field: "store_nm",	headerName: "받는 매장", pinned: 'left', width: 140, hide:true},
        {field: "store_cd", hide: true},
        {headerName: "매장재고",
            children: [
                {field: "qty", headerName: "재고", type: "currencyType", width: 65,
                    cellRenderer: function(params) {
                        if (params.value !== undefined) {
                            if (params.node.rowPinned === 'top') {
                                return params.value;
                            } else {
                                return '<a href="#" onclick="return OpenStoreStockPopup(\'' + (params.data.prd_cd || '') + '\', \'' + $("[name=sdate]").val() + '\');">' + Comma(params.value) + '</a>';
                            }
                        }
                    }
                },
                {field: "wqty", headerName: "보유재고", type: "currencyType", width: 65,
                    cellRenderer: function(params) {
                        if (params.node.rowPinned === 'top') {
                            return params.value;
                        } else {
                            return '<a href="#" onclick="return OpenStoreStockPopup(\'' + (params.data.prd_cd || '') + '\', \'' + $("[name=sdate]").val() + '\');">' + Comma(params.value) + '</a>';
                        }
                    }
                },
            ]
        },
        {field: "rt_qty", headerName: "RT수량", type: "currencyType", width: 65, 
            editable: (params) => params.node.rowPinned === "top" ? false : true,
            cellStyle: (params) => params.node.rowPinned === "top" ? '' : {"background-color": "#ffFF99"}
        },
        {field: "comment", headerName: "메모", width: "auto",
            editable: (params) => params.node.rowPinned === "top" ? false : true,
            cellStyle: (params) => params.node.rowPinned === "top" ? '' : {"background-color": "#ffFF99"},
        },
        {width: 'auto'}
    ];

    let rt_columns = [
        {headerName: "No", pinned: "left", valueGetter: "node.id", cellRenderer: "loadingRenderer", width: 50, cellStyle: {"text-align": "center"}},
        {field: "chk", headerName: '', pinned: 'left', cellClass: 'hd-grid-code', headerCheckboxSelection: true, checkboxSelection: true, sort: null, width: 28},
        {field: "dep_store_cd",	headerName: "매장코드", pinned: 'left', width: 70, cellStyle: {"text-align": "center"}},
        {field: "dep_store_nm",	headerName: "보내는 매장", pinned: 'left', width: 140},
        {field: "store_cd",	headerName: "매장코드", pinned: 'left', width: 70, cellStyle: {"text-align": "center"},hide:true},
        {field: "store_nm",	headerName: "받는 매장", pinned: 'left', width: 140,hide:true},
        {field: "rt_qty", headerName: "RT수량", type: "numberType", pinned: 'left', cellStyle: {"font-weight": "700"}},
        {field: "prd_cd", headerName: "바코드", pinned: 'left', width: 120, cellStyle: {"text-align": "center"}},
        {field: "goods_no",	headerName: "온라인코드", width: 70, cellStyle: {"text-align": "center"}, pinned: 'left'},
        {field: "opt_kind_nm", headerName: "품목", width: 60, cellStyle: {"text-align": "center"}},
        {field: "brand_nm", headerName: "브랜드", width: 70, cellStyle: {"text-align": "center"}},
        {field: "style_no",	headerName: "스타일넘버", width: 70, cellStyle: {"text-align": "center"}},
        {field: "goods_nm",	headerName: "상품명", width: 150,
            cellRenderer: function (params) {
                if (params.data?.goods_no == '' || params.node.aggData?.goods_no == '') {
                    return '<a href="javascript:void(0);" onclick="return alert(`온라인코드가 비어있는 상품입니다.`);">' + (params.value || '') + '</a>';
                } else {
                    let goods_no = params.data ? params.data.goods_no : params.node.aggData ? params.node.aggData.goods_no : '';
                    return '<a href="#" onclick="return openShopProduct(\'' + goods_no + '\');">' + (params.value || '') + '</a>';
                }
            }
        },
        {field: "goods_nm_eng", headerName: "상품명(영문)", width: 150},
        {field: "prd_cd_p", headerName: "품번", width: 90, cellStyle: {"text-align": "center"}},
        {field: "color", headerName: "컬러", width: 55, cellStyle: {"text-align": "center"}},
        {field: "size", headerName: "사이즈", width: 55, cellStyle: {"text-align": "center"}},
        {field: "goods_opt", headerName: "옵션", width: 150},
        {field: "goods_sh", headerName: "정상가", type: "currencyType", width: 60},
        {field: "price", headerName: "현재가", type: "currencyType", width: 60},
        {field: "comment", headerName: "메모", width: 200},
    ];
</script>

<script type="text/javascript" charset="utf-8">
    let gx, gx2, gx3;
    const pApp = new App('', { gridId: "#div-gd-product" });
    const pApp2 = new App('', { gridId: "#div-gd-stock" });
    const pApp3 = new App('', { gridId: "#div-gd-rt" });
    let selected_prd = {};

    $(document).ready(function() {
        pApp.ResizeGrid(450, 435);
        pApp.BindSearchEnter();
        let gridDiv = document.querySelector(pApp.options.gridId);
        gx = new HDGrid(gridDiv, product_columns, {
            rollup: true,
            groupSuppressAutoColumn: true,
            suppressAggFuncInHeader: true,
            enableRangeSelection: true,
            animateRows: true,
        });

        pApp2.ResizeGrid(450, 400);
        pApp2.BindSearchEnter();
        let gridDiv2 = document.querySelector(pApp2.options.gridId);
        gx2 = new HDGrid(gridDiv2, stock_columns, {
            pinnedTopRowData: pinnedRowData,
            getRowStyle: (params) => { // 고정된 row styling
                if (params.node.rowPinned)  return { 'font-weight': 'bold', 'background': '#eee', 'border': 'none'};
            },
            defaultColDef: {
                suppressMenu: true,
                resizable: false,
                autoHeight: true,
                suppressSizeToFit: false,
                sortable:true,
            },
			suppressCopyRowsToClipboard: true,
            onCellValueChanged: (e) => {
                e.node.setSelected(true);
                if (e.column.colId == "rt_qty") {
                    if (isNaN(e.newValue) == true || e.newValue == "") {
                        alert("숫자만 입력가능합니다.");
                        gx2.gridOptions.api.startEditingCell({ rowIndex: e.rowIndex, colKey: e.column.colId });
                    } else {
                        updatePinnedRow();
                    }
                }
            }
        });

        pApp3.ResizeGrid(275, 400);
        pApp3.BindSearchEnter();
        let gridDiv3 = document.querySelector(pApp3.options.gridId);
        gx3 = new HDGrid(gridDiv3, rt_columns);

		$("[name=store_channel]").on("change", function(e) {
			SearchStock();
		})

		$("[name=store_channel_kind]").on("change", function(e) {
			SearchStock();
		})

        // 검색조건 숨김 시 grid 높이 설정
        $(".search_mode_wrap .dropdown-menu a").on("click", function(e) {
            pApp.ResizeGrid(450, 403);
            pApp2.ResizeGrid(450, 400);
            pApp3.ResizeGrid(275, 400);
        });

        Search();

		// 판매채널 선택되지않았을때 매장구분 disabled처리하는 부분
		load_store_channel();
    });

    // 상품검색
    function Search() {
        let data = $('form[name="search"]').serialize();
        gx.Request('/shop/stock/stk22/search-goods', data, 1, function(e) {
            setAllRowGroupExpanded($("#grid_expand").is(":checked"));
        });
    }

    // 매장/창고별 재고검색
    function SearchStock(rowIndex = '') {
        if(rowIndex !== '') {
            selected_prd = gx.gridOptions.api.getDisplayedRowAtIndex(rowIndex).data;
        }
        if(!selected_prd.prd_cd) return alert("좌측에서 상품을 선택해주세요.");

		let store_channel = $("[name=store_channel]").val();
		let store_channel_kind = $("[name=store_channel_kind]").val();
		let data = 'prd_cd=' + selected_prd.prd_cd + "&store_channel=" + store_channel + "&store_channel_kind=" + store_channel_kind;
        gx2.Request('/shop/stock/stk22/search-stock', data, -1, function(d) {
            $("#selected_prd_nm").html(`[${selected_prd.prd_cd}] ${selected_prd.goods_nm}`);
            // $("#storage_stock").html(d.body[0]?.storage_qty + ' / ' + d.body[0]?.storage_wqty);
            $("#store_stock").html(d.body[0]?.send_qty + ' / ' + d.body[0]?.send_wqty);
			$("#storage_stock").html('재고 ' + d.body[0]?.storage_qty + ' / ' + '가용재고 ' + d.body[0]?.storage_wqty);
            let pinnedRow = gx2.gridOptions.api.getPinnedTopRow(0);
            let total_data = d.head.total_data;
			if(pinnedRow && total_data != '') {
				gx2.gridOptions.api.setPinnedTopRowData([
					{ ...pinnedRow.data, ...total_data }
				]);
			}
        });
    }

    // 창고재고 클릭 시 팝업 오픈
    function openStorageStockPopup() {
        if (!selected_prd.prd_cd) return;
        OpenStoreStockPopup(selected_prd.prd_cd);
    }

    // 최종RT리스트에 등록
    function AddRTToFinalTable() {
        let rows = gx2.getSelectedRows();
        if(rows.length < 1) return alert("RT리스트에 등록할 항목을 선택해주세요.");
        if(rows.filter(r => !r.rt_qty || !r.rt_qty.trim() || r.rt_qty == 0 || isNaN(parseInt(r.rt_qty))).length > 0)
            return alert("선택한 항목의 RT수량을 입력해주세요.");
        if(rows.filter(r => !r.store_cd).length > 0)
            return alert("선택한 항목의 받는 매장을 선택해주세요.");

        let over_qty_rows = rows.filter(row => {
            if(row.wqty !== null) {
                if(row.wqty < parseInt(row.rt_qty)) return true;
                else return false;
            }
            return true; // 상품재고가 없는경우
        });
        if(over_qty_rows.length > 0) return alert(`보내는 매장의 보유재고보다 많은 수량을 요청하실 수 없습니다.\n보내는 매장명 : ${over_qty_rows.map(o => o.dep_store_nm).join(", ")}`);

        let same_item_rows = gx3.getRows().filter(final => {
            if(rows.filter(row => row.prd_cd === final.prd_cd && row.store_cd === final.store_cd && row.dep_store_cd === final.dep_store_cd).length > 0) return true;
            return false;
        });
        if(same_item_rows.length > 0) return alert(`이미 등록된 항목입니다.\n[보내는 매장] ${same_item_rows[0].dep_store_nm}\n[온라인코드] ${same_item_rows[0].prd_cd}`);

        rows = rows.map(r => ({...selected_prd, ...r, comment: r.comment ?? ''}));
        gx3.gridOptions.api.updateRowData({ add: rows });
        $("#gd-rt-total").html(gx3.getRows().length);
        document.getElementById("final_table_area").scrollIntoView({ behavior: "smooth" });
    }

    // 최종RT 리스트 중 선택항목 삭제
    function DeleteRows() {
        let rows = gx3.getSelectedRows();
        if(rows.length < 1) return alert("삭제할 항목을 선택해주세요.");
        if(!confirm("선택한 항목을 삭제하시겠습니까?")) return;

        gx3.gridOptions.api.updateRowData({ remove: rows });
    }

    // RT 요청
    function RequestRT() {
        let rows = gx3.getSelectedRows();
        if(rows.length < 1) return alert("RT등록할 항목을 선택해주세요.");
        if(!confirm("선택한 항목을 RT등록하시겠습니까?")) return;

        axios({
            url: '/shop/stock/stk22/request-rt',
            method: 'post',
            data: {data: rows},
        }).then(function (res) {
            if(res.data.code === 200) {
                alert(res.data.msg);
                location.href = "/shop/stock/stk20";
            } else if (res.data.code === 400) {
                alert(res.data.msg);
            } else {
                console.log(res.data);
                alert("RT등록 중 오류가 발생했습니다.\n관리자에게 문의해주세요.");
            }
        }).catch(function (err) {
            console.log(err);
        });
    }

    function OpenStoreStockPopup(prd_cd, date = '') {
        var url = `/shop/stock/stk01/${prd_cd}?date=${date}`;
        var product = window.open(url, "_blank", "toolbar=no,scrollbars=yes,resizable=yes,status=yes,top=100,left=100,width=1000,height=900");
    }

    function openShopProduct(prd_no){
        var url = '/shop/product/prd01/' + prd_no;
        var product = window.open(url, "_blank", "toolbar=no,scrollbars=yes,resizable=yes,status=yes,top=500,left=500,width=1024,height=900");
    }
    
    function updatePinnedRow() {
        const rows = gx2.getSelectedRows();

        let row = {};
        
        if (rows.length > 0) {
            row = rows.reduce((a, c) => ({
                    send_qty : a.send_qty + c.send_qty,
                    send_wqty : a.send_wqty + c.send_qty,
                    rt_qty : isNaN(parseInt(a.rt_qty) + parseInt(c.rt_qty)) ? 0 : parseInt(a.rt_qty) + parseInt(c.rt_qty)
                }), { dep_store_nm : '합계', qty : 0, wqty : 0, send_qty : 0, send_wqty : 0, rt_qty : 0}
            );
        }

        let pinnedRow = gx2.gridOptions.api.getPinnedTopRow(0);
        gx2.gridOptions.api.setPinnedTopRowData([{ ...pinnedRow.data, ...row }]);

    }
</script>
@stop

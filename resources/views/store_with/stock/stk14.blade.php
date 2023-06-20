@extends('store_with.layouts.layout')
@section('title','상품출고관리 > 요청분출고')
@section('content')
    <div class="page_tit">
        <h3 class="d-inline-flex">요청분출고</h3>
        <div class="d-inline-flex location">
            <span class="home"></span>
            <span>상품관리</span>
            <span>/ 상품출고관리</span>
            <span>/ 요청분출고</span>
        </div>
    </div>
    <form method="get" name="search">
        <div id="search-area" class="search_cum_form">
            <div class="card mb-3">

                <div class="d-flex card-header justify-content-between">
                    <h4>검색</h4>
                    <div class="flax_box">
                        <a href="#" id="search_sbtn" onclick="Search();" class="btn btn-sm btn-primary shadow-sm mr-1"><i class="fas fa-search fa-sm text-white-50"></i> 검색</a>
                        <a href="/store/stock/stk10" class="btn btn-sm btn-outline-primary shadow-sm pl-2 mr-1"><i class="fas fa-step-backward fa-sm"></i> 출고 리스트</a>
                        <div id="search-btn-collapse" class="btn-group mb-0 mb-sm-0"></div>
                    </div>
                </div>

                <div class="card-body">
                    <div class="row">
                        {{-- <div class="col-lg-4 inner-td">
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
                        </div> --}}
                        <div class="col-lg-4 inner-td">
                            <div class="form-group">
                                <label for="store_cd">요청매장</label>
                                <div class="form-inline inline_btn_box">
                                    <input type='hidden' id="store_nm" name="store_nm">
                                    <select id="store_no" name="store_no" class="form-control form-control-sm select2-store"></select>
                                    <a href="javascript:void(0);" class="btn btn-sm btn-outline-primary sch-store"><i class="bx bx-dots-horizontal-rounded fs-16"></i></a>
                                </div>
                            </div>
                        </div>
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
                                <label for="">자료수/정렬</label>
                                <div class="form-inline">
                                    <div class="form-inline-inner input_box" style="width:24%;">
                                        <select name="limit" class="form-control form-control-sm">
                                            <option value="1000">1000</option>
                                            <option value="2000">2000</option>
                                            <option value="5000">5000</option>
                                            <option value="10000">10000</option>
                                        </select>
                                    </div>
                                    <span class="text_line">/</span>
                                    <div class="form-inline-inner input_box" style="width:45%;">
                                        <select name="ord_field" class="form-control form-control-sm">
                                            <option value="goods_no">온라인코드</option>
                                            <option value="prd_cd">바코드</option>
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
                <div class="search_mode_wrap btn-group mr-2 mb-0 mb-sm-0"></div>
            </div>

        </div>
    </form>
    <!-- DataTales Example -->
    <div class="card shadow mb-0 last-card pt-2 pt-sm-0">
        <div class="card-body">
            <div class="card-title">
                <div class="filter_wrap d-flex justify-content-between">
                    <div class="d-flex">
                        <h6 class="m-0 font-weight-bold">총 : <span id="gd-total" class="text-primary">0</span>건</h6>
                    </div>
                    <div class="d-flex flex-grow-1 flex-column flex-lg-row justify-content-end align-items-end align-items-lg-center">
                        {{-- <div class="d-flex mr-1 mb-1 mb-lg-0">
                            <span class="mr-1">출고예정일</span>
                            <div class="docs-datepicker form-inline-inner input_box" style="width:130px;display:inline;">
                                <div class="input-group">
                                    <input type="text" class="form-control form-control-sm docs-date bg-white" name="exp_dlv_day" value="{{ $today }}" autocomplete="off" readonly />
                                    <div class="input-group-append">
                                        <button type="button" class="btn btn-outline-secondary docs-datepicker-trigger p-0 pl-2 pr-2">
                                            <i class="fa fa-calendar" aria-hidden="true"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="docs-datepicker-container"></div>
                            </div>
                        </div> --}}
                        <div class="d-flex">
                            <div class="custom-control custom-checkbox form-check-box pr-2" style="display:inline-block;">
                                <input type="checkbox" class="custom-control-input" name="grid_expand" id="grid_expand" onchange="return setAllRowGroupExpanded(this.checked);" checked>
                                <label class="custom-control-label font-weight-normal" for="grid_expand">항목펼쳐보기</label>
                            </div>
                        </div>
                        <div class="d-flex">
                            <div class="custom-control custom-checkbox form-check-box pr-2" style="display:inline-block;">
                                <input type="checkbox" class="custom-control-input" name="ext_storage_qty" id="ext_storage_qty" value="Y" checked>
                                <label class="custom-control-label font-weight-normal" for="ext_storage_qty">창고재고 0 제외</label>
                            </div>
                        </div>
                        <span class="d-none d-lg-block mr-2 tex-secondary">|</span>
                        <div class="d-flex">
                            {{-- <select id='rel_order' name='rel_order' class="form-control form-control-sm mr-2"  style='width:70px;display:inline'>
                                @foreach ($rel_orders as $rel_order)
                                    <option value='{{ $rel_order->code_val }}'>{{ $rel_order->code_val }}</option>
                                @endforeach
                            </select> --}}
                            <a href="#" onclick="requestRelease();" class="btn btn-sm btn-primary shadow-sm pl-2"><i class="fas fa-sm text-white-50"></i>출고요청</a>
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
        .ag-row-level-1 {
            background-color: #edf4fd !important;
        }
    </style>
    <script language="javascript">
        const pinnedRowData = [{ goods_nm: ''}];
        const sumValuesFunc = (params) => params.values.reduce((a,c) => a + (c * 1), 0);
        const stock_render = (params) => {
            if (params.value === undefined) return "";
            if (params.value === null) params.value = 0;
            if (params.data) {
                return '<a href="#" onclick="return OpenStoreStockPopup(\'' + (params.data.prd_cd || '') + '\', \'' + $("[name=sdate]").val() + '\');">' + Comma(params.value) + '</a>';
            } else if (params.node.aggData) {
                return `<a href="#" onclick="return OpenStockPopup('${params.node.key}', '${$("[name=sdate]").val() || ''}');">${Comma(params.value)}</a>`;
            } else {
                return '';
            }
        };

        let columns = [
            {field: "prd_cd", headerName: "바코드", pinned: 'left', width:150, cellStyle: StyleLineHeight, checkboxSelection: true,
                cellRenderer: function(params) {
                    if (params.node.rowPinned === 'top') return "합계";
                    if (params.value !== undefined) {
                        return `<a href="javascript:void(0);" onclick="return OpenStockPopup('${params.data.prd_cd_p}', '${$("[name=sdate]").val() || ''}', '${params.data.color}', '${params.data.size}');">${params.value}</a>`;
                    }
                }
            },
            {field: "goods_no", headerName: "온라인코드", width: 60, cellStyle: {"text-align": "center"}, aggFunc: "first"},
            {field: "opt_kind_nm", headerName: "품목", width: 60, cellStyle: {"text-align": "center"}},
            {field: "brand_nm", headerName: "브랜드", width: 80, cellStyle: {"text-align": "center"}},
            {field: "style_no",	headerName: "스타일넘버", width: 80, cellStyle: {"text-align": "center"}, aggFunc: "first"},
            {field: "goods_nm",	headerName: "상품명", type: 'HeadGoodsNameType', width: 250, aggFunc: "first",
                cellRenderer: function (params) {
                    if (params.data?.goods_no == '' || params.node.aggData?.goods_no == '') {
                        return '<a href="javascript:void(0);" onclick="return blank_goods_no();">' + params.value + '</a>';
                    } else {
                        let goods_no = params.data ? params.data.goods_no : params.node.aggData ? params.node.aggData.goods_no : '';
                        return '<a href="#" onclick="return openHeadProduct(\'' + goods_no + '\');">' + params.value + '</a>';
                    }
                }
            },
            {field: "goods_nm_eng",	headerName: "상품명(영문)", type: 'HeadGoodsNameType', width: 250, aggFunc: "first",
                cellRenderer: function (params) {
                    if (params.data?.goods_no == '' || params.node.aggData?.goods_no == '') {
                        return '<a href="javascript:void(0);" onclick="return blank_goods_no();">' + params.value + '</a>';
                    } else {
                        let goods_no = params.data ? params.data.goods_no : params.node.aggData ? params.node.aggData.goods_no : '';
                        return '<a href="#" onclick="return openHeadProduct(\'' + goods_no + '\');">' + params.value + '</a>';
                    }
                }
            },
            {field: "prd_cd_p", headerName: "품번", width: 100, cellStyle: StyleLineHeight, rowGroup: true, hide: true, checkboxSelection: true,
                cellRenderer: function (params) {
                    if(params.value === undefined) return "";
                    return `<a href="javascript:void(0);" onclick="return OpenStockPopup('${params.value}', '${$("[name=sdate]").val() || ''}');">${params.value}</a>`;
                }
            },
            {field: "color",	headerName: "컬러", width: 60, cellStyle: {"text-align": "center"}},
            {field: "color_nm",	headerName: "컬러명", width: 100},
            {field: "size",	headerName: "사이즈", width: 60, cellStyle: {"text-align": "center"}},
            {field: "goods_opt", headerName: "옵션", width: 200},
            {
                headerName: '창고재고', // 대표창고의 재고를 조회
                children: [
                    {field: "storage_qty", headerName: "재고", type: 'currencyType', aggFunc: sumValuesFunc, cellRenderer: stock_render,},
                    {field: "storage_wqty", headerName: "보유재고", type: 'currencyType', aggFunc: sumValuesFunc, cellRenderer: stock_render,},
                ]
            },
            {
                field: 'store_info',
                headerName: '',
                hide: true,
                children: [
                    {
                        field: 'store_qty',
                        headerName: '재고', 
                        type: "currencyType",
                        hide: true,
                        width: 50,
                        aggFunc: sumValuesFunc,
                        cellRenderer: stock_render,
                    },
                    {
                        field: 'store_wqty',
                        headerName: '보유재고',
                        type: "currencyType",
                        hide: true,
                        width: 80,
                        aggFunc: sumValuesFunc,
                        cellRenderer: stock_render,

                    },
                    {
                        field: 'rel_qty',
                        headerName: '요청수량',
                        type: "currencyType",
                        hide: true,
                        width: 80,
                        editable: true,
                        cellStyle: {'background-color': '#ffff99'},
                        valueFormatter: formatNumber
                    },
                ],
            },
            {field: "req_comment", headerName: "매장메모", width: 300, editable: true,cellStyle: {'background-color': '#ffff99'}},
            {width: 'auto'}
        ];

        function setColumn(store) {
            if(!store) return;

            columns[12].headerName = store.store_nm;
            gx.gridOptions.api.setColumnDefs(columns);

            gx.gridOptions.columnApi.applyColumnState({
                state: [
                    {colId: 'store_info', hide: false},
                    {colId: 'store_qty', hide: false},
                    {colId: 'store_wqty', hide: false},
                    {colId: 'rel_qty', hide: false},
                ],
            });
        }
    </script>
    <script type="text/javascript" charset="utf-8">
        const basic_autoGroupColumnDef = (headerName, width = 150) => ({
            headerName: headerName,
            headerClass: 'bizest',
            minWidth: width,
            maxWidth: width,
            cellRenderer: 'agGroupCellRenderer',
            pinned: 'left'
        });

        let gx;
        const pApp = new App('', { gridId: "#div-gd" });

        $(document).ready(function() {
            pApp.ResizeGrid(275);
            pApp.BindSearchEnter();
            let gridDiv = document.querySelector(pApp.options.gridId);
            gx = new HDGrid(gridDiv, columns, {
                onCellValueChanged: (e) => {
                    e.node.setSelected(true);
                    if (e.column.colId == "rel_qty") {
                        if (isNaN(e.newValue) == true || e.newValue == "") {
                            alert("숫자만 입력가능합니다.");
                            gx.gridOptions.api.startEditingCell({ rowIndex: e.rowIndex, colKey: e.column.colId });
                        }
                    }
                },
                rollup: true,
                autoGroupColumnDef: basic_autoGroupColumnDef('품번'),
                groupDefaultExpanded: 0, // 0: close, 1: open
                suppressAggFuncInHeader: true,
                animateRows: true,
                suppressDragLeaveHidesColumns: true,
                suppressMakeColumnVisibleAfterUnGroup: true,
                onSelectionChanged: setRowGroupExpanded,
                groupSelectsChildren: true,
            });
            gx.gridOptions.defaultColDef = {
                suppressMenu: true,
                resizable: false,
                sortable: true,
            };
        });

        async function Search() {
            await setColumn2();
            if(!$("[name=store_no]").val()) return alert("요청매장을 선택 후 검색해주세요.");
            let data = $('form[name="search"]').serialize();
            data += "&ext_storage_qty=" + $("[name=ext_storage_qty]").is(":checked");

            axios({
                url: '/store/api/stores/search-storenm',
                method: 'post',
                data: {store_cds: [$("[name=store_no]").val()]},
            }).then(function (res) {
                if(res.data.code === 200) {
                    setColumn(res.data.body[0]);
                    gx.Request('/store/stock/stk14/search', data, 1, function(e){
                        setAllRowGroupExpanded($("#grid_expand").is(":checked"));
                    });
                } else {
                    console.log(res.data);
                }
            }).catch(function (err) {
                console.log(err);
            });
        }

        function setColumn2() {
            let ord_field = $("[name=ord_field]").val();
            if(ord_field === "prd_cd_p") {
                let prd_columns = columns.map(c => c.field === "prd_cd_p" 
                    ? ({...c, rowGroup: true, hide: true, pinned: "left"}) 
                    : c.type === "NumType" ? ({...c, hide: true})
                    : c.field === "goods_no" ? ({...c, cellStyle: StyleLineHeight}) : c);
                gx.gridOptions.api.setColumnDefs(prd_columns);
            } else {
                let prd_columns = columns.map(c => c.field === "prd_cd_p" 
                    ? ({...c, rowGroup: false, hide: false, pinned: "auto"}) 
                    : c.type === "NumType" ? ({...c, hide: false})
                    : c.field === "goods_no" ? ({...c, cellStyle: StyleGoodsNo}) : c);
                gx.gridOptions.api.setColumnDefs(prd_columns);
            }
        }

        // 출고요청
        function requestRelease() {
            let rows = gx.getSelectedRows();

            console.log(rows);
            if(rows.length < 1) return alert("출고요청할 상품을 선택해주세요.");
            if(rows.filter(r => !r.rel_qty || !r.rel_qty.trim() || r.rel_qty == 0 || isNaN(parseInt(r.rel_qty))).length > 0) return alert("선택한 상품의 배분수량을 입력해주세요.");

            let over_qty_rows = rows.filter(row => {
                if(row.storage_wqty !== null) {
                    if(row.storage_wqty < parseInt(row.rel_qty)) return true;
                    else return false;
                }
                return true; // 상품재고가 없는경우
            });
            if(over_qty_rows.length > 0) return alert(`창고의 보유재고보다 많은 수량을 요청하실 수 없습니다.\n바코드 : ${over_qty_rows.map(o => o.prd_cd).join(", ")}`);

            if(!confirm("해당 상품을 출고요청하시겠습니까?")) return;

            let store_cd = $("[name=store_no]").val();

            const data = {
                products: rows, 
                store_cd,
                // exp_dlv_day: $('[name=exp_dlv_day]').val(),
                // rel_order: $('[name=rel_order]').val(),
            };

            axios({
                url: '/store/stock/stk14/request-release',
                method: 'post',
                data: data,
            }).then(function (res) {
                if(res.data.code === 200) {
                    if(!confirm(res.data.msg + "\n출고요청을 계속하시겠습니까?")) {
                        location.href = "/store/stock/stk10";
                    } else {
                        Search();
                    }
                } else {
                    console.log(res.data);
                    alert("출고요청 중 오류가 발생했습니다.\n관리자에게 문의해주세요.");
                }
            }).catch(function (err) {
                console.log(err);
            });
        }

        function openApi() {
			document.getElementsByClassName('sch-prdcd-range')[0].click();
		}

        function blank_goods_no() {
            alert('온라인코드가 비어있는 상품입니다.');
        }

        function OpenStockPopup(prd_cd_p, date, color = '', size = '') {
            var url = `/store/product/prd04/stock?prd_cd_p=${prd_cd_p}&date=${date}&color=${color}&size=${size}`;
            var product = window.open(url, "_blank", "toolbar=no,scrollbars=yes,resizable=yes,status=yes,top=100,left=100,width=1000,height=900");
        }

        function OpenStoreStockPopup(prd_cd, date) {
            var url = `/store/stock/stk01/${prd_cd}?date=${date}`;
            var product = window.open(url, "_blank", "toolbar=no,scrollbars=yes,resizable=yes,status=yes,top=100,left=100,width=1000,height=900");
        }

    </script>
@stop

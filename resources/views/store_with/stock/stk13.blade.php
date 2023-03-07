@extends('store_with.layouts.layout')
@section('title','출고 > 판매분출고')
@section('content')
    <div class="page_tit">
        <h3 class="d-inline-flex">판매분출고</h3>
        <div class="d-inline-flex location">
            <span class="home"></span>
            <span>매장관리</span>
            <span>/ 출고</span>
            <span>/ 판매분출고</span>
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
                        <div class="col-lg-4 inner-td">
                            <div class="form-group">
                                <div class="form-group">
                                    <label for="good_types">판매일자</label>
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
                                    <input type='hidden' id="store_nm" name="store_nm">
                                    <select id="store_no" name="store_no[]" class="form-control form-control-sm select2-store multi_select" multiple></select>
                                    <a href="javascript:void(0);" class="btn btn-sm btn-outline-primary sch-store"><i class="bx bx-dots-horizontal-rounded fs-16"></i></a>
                                </div>
                            </div>
                        </div>
                    </div>
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
                                            <option value="store_cd">매장</option>
                                            <option value="prd_cd">바코드</option>
                                        </select>
                                    </div>
                                    <div class="form-inline-inner input_box sort_toggle_btn" style="width:24%;margin-left:1%;">
                                        <div class="btn-group" role="group">
                                            <label class="btn btn-secondary" for="sort_desc" data-toggle="tooltip" data-placement="top" title="내림차순"><i class="bx bx-sort-down"></i></label>
                                            <label class="btn btn-primary primary" for="sort_asc" data-toggle="tooltip" data-placement="top" title="오름차순"><i class="bx bx-sort-up"></i></label>
                                        </div>
                                        <input type="radio" name="ord" id="sort_desc" value="desc">
                                        <input type="radio" name="ord" id="sort_asc" value="asc" checked>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row search-area-ext d-none">
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
                        <div class="col-lg-4 inner-td">
                            <div class="form-group">
                                <label for="brand_cd">브랜드</label>
                                <div class="form-inline inline_btn_box">
                                    <select id="brand_cd" name="brand_cd" class="form-control form-control-sm select2-brand"></select>
                                    <a href="#" class="btn btn-sm btn-outline-primary sch-brand"><i class="bx bx-dots-horizontal-rounded fs-16"></i></a>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4 inner-td">
                            <div class="form-group">
                                <label for="name">공급업체</label>
                                <div class="form-inline inline_select_box">
                                    <div class="form-inline-inner input-box w-100">
                                        <div class="form-inline inline_btn_box">
                                            <input type="hidden" id="com_cd" name="com_cd" />
                                            <input onclick="" type="text" id="com_nm" name="com_nm" class="form-control form-control-sm search-all search-enter" style="width:100%;" autocomplete="off" />
                                            <a href="#" class="btn btn-sm btn-outline-primary sch-sup-company"><i class="bx bx-dots-horizontal-rounded fs-16"></i></a>
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
                    <div class="d-flex flex-grow-1 flex-column flex-lg-row justify-content-end align-items-end align-items-lg-center">
                        {{-- <div class="d-flex mb-1 mb-lg-0">
                            <span class="mr-1">창고</span>
                            <select id='storage' name='storage' class="form-control form-control-sm"  style='width:160px;display:inline'>
                                <option value=''>선택</option>
                                @foreach ($storages as $storage)
                                    <option value='{{ $storage->storage_cd }}' @if($storage->default_yn == "Y") selected @endif>{{ $storage->storage_nm }} @if($storage->default_yn == "Y") (대표) @endif </option>
                                @endforeach
                            </select>
                        </div> --}}
                        <div class="d-flex">
                            <div class="custom-control custom-checkbox form-check-box pr-2" style="display:inline-block;">
                                <input type="checkbox" class="custom-control-input" name="ext_storage_qty" id="ext_storage_qty" value="Y" checked>
                                <label class="custom-control-label font-weight-normal" for="ext_storage_qty">창고재고 0 제외</label>
                            </div>
                            <div class="custom-control custom-checkbox form-check-box pr-2" style="display:inline-block;">
                                <input type="checkbox" class="custom-control-input" name="grid_expand" id="grid_expand" onchange="return setAllRowGroupExpanded(this.checked);" checked>
                                <label class="custom-control-label font-weight-normal" for="grid_expand">항목펼쳐보기</label>
                            </div>
                        </div>
                        <span class="d-none d-lg-block mr-2 tex-secondary">|</span>
                        <div class="d-flex mr-1 mb-1 mb-lg-0">
                            <span class="mr-1">출고예정일</span>
                            <div class="docs-datepicker form-inline-inner input_box" style="width:130px;display:inline;">
                                <div class="input-group">
                                    <input type="text" class="form-control form-control-sm docs-date bg-white" name="exp_dlv_day" value="{{ $edate }}" autocomplete="off" readonly />
                                    <div class="input-group-append">
                                        <button type="button" class="btn btn-outline-secondary docs-datepicker-trigger p-0 pl-2 pr-2">
                                            <i class="fa fa-calendar" aria-hidden="true"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="docs-datepicker-container"></div>
                            </div>
                        </div>
                        <div class="d-flex">
                            <select id='rel_order' name='rel_order' class="form-control form-control-sm mr-2"  style='width:70px;display:inline'>
                                @foreach ($rel_order_res as $rel_order)
                                    <option value='{{ $rel_order->code_id }}'>{{ $rel_order->code_val }}</option>
                                @endforeach
                            </select>
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
        .ag-row-level-1 {background-color: #f8f8f8 !important;}
        .ag-row-selected {background-color: #b7e4ff !important;}
    </style>
    <script language="javascript">
        let columns = [
            {field: "store_nm" , headerName: "매장명", rowGroup: true, hide: true, width: 150, pinned: "left", checkboxSelection: true},
            {field: "store_cd" , headerName: "매장코드", width: 70, pinned: "left", cellStyle: {"text-align": "center"}, 
                aggFunc: (params) => params.values.length > 0 ? params.values[0] : '',
                cellRenderer: (params) => params.data ? '' : params.value,
            },
            {field: "prd_cd" , headerName: "바코드",  pinned: "left", width: 150, cellStyle: {"text-align": "center"}, checkboxSelection: true},
            {field: "goods_no", headerName: "온라인코드", width: 70, cellStyle: {"text-align": "center"}},
            {field: "opt_kind_nm", headerName: "품목", width: 70, cellStyle: {"text-align": "center"}},
            {field: "brand_nm", headerName: "브랜드", width: 80, cellStyle: {"text-align": "center"}},
            {field: "style_no",	headerName: "스타일넘버", width: 70, cellStyle: {"text-align": "center"}},
            {field: "goods_nm",	headerName: "상품명", type: 'HeadGoodsNameType', width: 200},
            {field: "goods_nm_eng",	headerName: "상품명(영문)", type: 'HeadGoodsNameType', width: 200},
            {field: "prd_cd_sm", headerName: "품번", width: 90, cellStyle: {"text-align": "center"}},
            {field: "color", headerName: "컬러", width: 55, cellStyle: {"text-align": "center"}},
            {field: "size", headerName: "사이즈", width: 55, cellStyle: {"text-align": "center"}},
            {field: "goods_opt", headerName: "옵션", width: 130},
            {
                headerName: '(대표)창고재고', // 대표창고의 재고를 조회
                children: [
                    {field: "storage_qty", headerName: "재고", type: 'currencyType', width: 60},
                    {field: "storage_wqty", headerName: "보유재고", type: 'currencyType', width: 60},
                ]
            },
            {
                headerName: '판매',
                children: [
                    {field: "total_sale_cnt", headerName: "총판매수량", type: 'currencyType', width: 70},
                    {field: "sale_cnt", headerName: "판매수량", type: 'currencyType', width: 60},
                ]
            },
            {
                headerName: '매장재고',
                children: [
                    {field: "store_qty", headerName: "재고", type: 'currencyType', width: 60},
                    {field: "store_wqty", headerName: "보유재고", type: 'currencyType', width: 60},
                    {field: "exp_soldout_day", headerName: "소진예상일", cellStyle: {"text-align": "center"}, width: 80},
                    {
                        field: "rel_qty",
                        headerName: "배분수량",
                        type: "currencyType",
                        width: 60,
                        editable: true,
                        cellStyle: function(params) {return params.value ? {"background-color": "#ffff99"} : {}},
                        valueFormatter: formatNumber,
                    }
                ]
            },
        ];

        const basic_autoGroupColumnDef = (headerName, minWidth = 230, maxWidth = 230) => ({
            headerName: headerName,
            headerClass: 'bizest',
            minWidth: minWidth,
            maxWidth: maxWidth,
            cellRenderer: 'agGroupCellRenderer',
            pinned: 'left'
        });
    </script>
    <script type="text/javascript" charset="utf-8">
        let gx;
        const pApp = new App('', { gridId: "#div-gd" });

        $(document).ready(function() {
            pApp.ResizeGrid(275);
            pApp.BindSearchEnter();
            let gridDiv = document.querySelector(pApp.options.gridId);
            gx = new HDGrid(gridDiv, columns, {
                rollup: true,
                autoGroupColumnDef: basic_autoGroupColumnDef('매장', 180, 180),
                groupDefaultExpanded: 1,
                rowSelection: 'multiple',
                groupSelectsChildren: true,
                suppressRowClickSelection: true,
                suppressAggFuncInHeader: true,
                animateRows: true,
                onSelectionChanged: setRowGroupExpanded,
                onCellValueChanged: (e) => {
                    e.node.setSelected(true);
                    if (e.column.colId === "rel_qty") {
                        if (isNaN(e.newValue) == true || e.newValue == "") {
                            alert("숫자만 입력가능합니다.");
                            gx.gridOptions.api.startEditingCell({ rowIndex: e.rowIndex, colKey: e.column.colId });
                        }
                    }
                }
            });
            gx.gridOptions.defaultColDef = {
                suppressMenu: true,
                resizable: false,
                sortable: true,
            };

            // 매장검색
            $( ".sch-store" ).on("click", function() {
                searchStore.Open(null, "multiple");
            });
        });

        // 검색버튼 클릭 시
        function Search() {
            setColumn();
            
            let data = $('form[name="search"]').serialize();
            data += "&ext_storage_qty=" + $("[name=ext_storage_qty]").is(":checked");
            gx.Request('/store/stock/stk13/search', data, -1, function(d) {
                setAllRowGroupExpanded($("#grid_expand").is(":checked"));
            });
        }

        // 정렬 타입에 따른 column 업데이트
        function setColumn() {
            let ord_field = $("[name=ord_field]").val();
            if(ord_field === "store_cd") {
                let prd_columns = columns.map(c => c.field === "store_nm" 
                    ? ({...c, rowGroup: true, hide: true}) 
                    : c.field === "prd_cd_sm" 
                        ? ({...c, rowGroup: false, hide: false}) 
                        : c.field === "prd_cd"
                            ? ({...c, width: 150})
                            : c
                );
                gx.gridOptions.api.setColumnDefs(prd_columns);
                gx.gridOptions.api.setAutoGroupColumnDef(basic_autoGroupColumnDef('매장', 180, 180));
            } else if(ord_field === "prd_cd") {
                let prd_columns = columns.map(c => c.field === "store_nm" 
                    ? ({...c, rowGroup: false, hide: false}) 
                    : c.field === "store_cd"
                        ? ({...c, aggFunc: false, cellRenderer: (params) => params.value})
                        : c.field === "prd_cd_sm" 
                            ? ({...c, rowGroup: true, hide: true}) 
                            : c.field === "prd_cd" 
                                ? ({...c, checkboxSelection: false, width: 110}) 
                                : c
                );
                gx.gridOptions.api.setColumnDefs(prd_columns);
                gx.gridOptions.api.setAutoGroupColumnDef(basic_autoGroupColumnDef('품번', 140, 140));
            }
        }

        // 출고요청
        function requestRelease() {
            let rows = gx.getSelectedRows();
            if(rows.length < 1) return alert("출고요청할 상품을 선택해주세요.");

            let empty_rows = rows.filter(r => {
                if(!r.rel_qty || !r.rel_qty.trim() || r.rel_qty == 0 || isNaN(parseInt(r.rel_qty))) return true;
                return false;
            });
            if(empty_rows.length > 0) return alert("선택한 상품의 배분수량을 모두 입력해주세요.");

            let over_qty_rows = rows.filter(r => {
                if(r.rel_qty > (r.storage_wqty || 0)) return true;
                return false;
            });
            if(over_qty_rows.length > 0) return alert(`대표창고의 재고보다 많은 수량을 요청하실 수 없습니다.\n바코드 : ${over_qty_rows.map(o => o.prd_cd).join(", ")}`);

            if(!confirm("해당 상품을 출고요청하시겠습니까?")) return;

            const data = {
                products: rows,
                exp_dlv_day: $('[name=exp_dlv_day]').val(),
                rel_order: $('[name=rel_order]').val(),
            };

            axios({
                url: '/store/stock/stk13/request-release',
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

    </script>
@stop

@extends('head_with.layouts.layout-nav')
@section('title','상품 관리 - 입고')
@section('content')
    <div class="show_layout py-3 px-sm-3">
        <div class="page_tit mb-3 d-flex align-items-center justify-content-between">
            <div>
                <h3 class="d-inline-flex">상품관리 - 입고</h3>
            </div>
        </div>
        <form name="stock">
            <input name="goods_no" type="hidden" class="d-none" value="{{ $goods_no }}">
            <div class="card_wrap aco_card_wrap">
                <div class="card shadow">
                    <div class="card-body mt-1">
                        <div class="row_wrap">
                            <div class="row">
                                <div class="col-12">
                                    <div class="table-box-ty2 mobile">
                                        <table class="table incont table-bordered" width="100%" cellspacing="0">
                                            <colgroup>
                                                <col width="94px">
                                            </colgroup>
                                            <tbody>
                                            <tr>
                                                <th>입고일자</th>
                                                <td>
                                                    <div class="docs-datepicker flex_box">
                                                        <div class="input-group">
                                                        <input type="text" class="form-control form-control-sm docs-date" name="sdate" value="{{ $sdate }}" autocomplete="off" style="max-width: 183px">
                                                            <div class="input-group-append">
                                                                <button type="button" class="btn btn-outline-secondary docs-datepicker-trigger p-0 pl-2 pr-2" disable>
                                                                    <i class="fa fa-calendar" aria-hidden="true"></i>
                                                                </button>
                                                            </div>
                                                        </div>
                                                        <div class="docs-datepicker-container"></div>
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th><label for="invoice_no">송장번호</label></th>
                                                <td>
                                                    <div class="flex_box">
                                                        <input type='text' class="form-control form-control-sm" name='invoice_no' id="invoice_no" value='{{ $invoice_no }}'>
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th><label for="wonga">원가</label></th>
                                                <td>
                                                    <div class="flex_box">
                                                        <div class="w-100 txt_box mr-2 mb-2">최근 원가 : {{ $wonga }} 원</div>
                                                        <input id="wonga" type='text' class="form-control form-control-sm mr-2" name='wonga' value='' style="max-width: 100px"> 원
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th><label for="loc">위치</label></th>
                                                <td>
                                                    <div class="flex_box">
                                                        <select id="loc" name='loc' class="form-control form-control-sm">
                                                            <option value=''>전체</option>
                                                            @foreach ($locs as $loc)
                                                                <option value='{{ $loc->code_id }}'>{{ $loc->code_val }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </td>
                                            </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card">
                    <div class="card-header mb-0">
                        <a href="#">입고 정보</a>
                    </div>
                    <div class="card-body pt-2">
                        <div class="card-title">
                            <div class="filter_wrap">
                                <div class="fl_box px-0 mx-0">
                                </div>
                                <div class="fr_box">
                                </div>
                            </div>
                        </div>
                        <div class="table-responsive">
                            <div id="div-gd" class="ag-theme-balham"></div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
        <div class="resul_btn_wrap mt-3 d-block">
            <a href="javascript:;" onclick="stockIn()" class="btn btn-sm btn-primary submit-btn">입고</a>
            <a href="javascript:;" class="btn btn-sm btn-secondary" onclick="window.close()">취소</a>
        </div>
    </div>
    
    <style>
        /* 상품 옵션 grid - gx2 셀 변경시 색깔 css */
        .opt-cell-changed {
            background: #DC3545 !important;
            color: white;
            font-weight: 700;
        }

        /* 옵션 컬럼 셀 잠금 */
        .locked-cell.ag-cell:focus{  border:none !important;  outline: none; border-right: 1px solid #bdc3c7 !important }
        .locked-cell.ag-cell.ag-cell-range-selected:not(.ag-cell-range-single-cell).ag-cell-range-left {
            border-left-color: transparent;
        }
        .locked-cell.ag-cell.ag-cell-range-selected:not(.ag-cell-range-single-cell).ag-cell-range-top {
            border-top-color: transparent;
        }
        .locked-cell.ag-cell.ag-cell-range-selected:not(.ag-cell-range-single-cell).ag-cell-range-right {
            border-right-color: transparent;
        }
        .locked-cell.ag-cell.ag-cell-range-selected:not(.ag-cell-range-single-cell).ag-cell-range-bottom {
            border-bottom-color: transparent;
        }
        .ag-theme-balham .ag-ltr .locked-cell.ag-cell-range-single-cell, 
        .ag-theme-balham .ag-ltr .locked-cell.ag-cell-range-single-cell.ag-cell-range-handle, 
        .ag-theme-balham .ag-ltr .ag-has-focus .locked-cell.ag-cell-focus:not(.ag-cell-range-selected), 
        .ag-theme-balham .ag-rtl .ag-cell-range-single-cell, .ag-theme-balham .ag-rtl .locked-cell.ag-cell-range-single-cell.ag-cell-range-handle, 
        .ag-theme-balham .ag-rtl .ag-has-focus .locked-cell.ag-cell-focus:not(.ag-cell-range-selected) {
            border: 1px solid transparent;
        }

        /* 수정 시 글자 색깔이 검은색으로 고정되도록 적용 (수정후 글자색이 흰색으로 변환되지 않도록 처리) */
        .ag-cell-inline-editing {
            color: black;
        }
    </style>
    <script type="text/javascript" charset="utf-8">

        const CELL_COLOR = {
            LOCKED: {'background' : '#f5f7f7'},
            YELLOW : {'background' : '#ffff99'}
        };

        const OPT1_KIND_NM = "{{ @$opt_kind_names[0] }}";
        const OPT2_KIND_NM = "{{ @$opt_kind_names[1] }}";

        const IS_SINGLE = "{{ @$is_single ? 'true' : 'false' }}";

        const optCellClassRules = { // 색 변경 규칙 정의
            "opt-cell-changed": params => {
                const column_name = params.colDef.field;
                if (params.data.hasOwnProperty('is_changed')) {
                    if (IS_SINGLE == "true") {
                        return params.data?.is_changed ? true : false;
                    } else if (IS_SINGLE == "false") {
                        return params.data?.is_changed[column_name] ? true : false;
                    }
                } else {
                    return false;
                }
            }
        };

        let opt1_kind_opts = [];
        @if (count(@$opt_matrix['opt1']) > 0)
            @foreach (@$opt_matrix['opt1'] as $idx => $obj)
                opt1_kind_opts.push({opt1_nm: "{{ $obj->opt_nm }}"});
            @endforeach
        @endif

        let opt2_kind_opts = [];
        @if (count(@$opt_matrix['opt2']) > 0)
            @foreach (@$opt_matrix['opt2'] as $idx => $obj)
                opt2_kind_opts.push({
                    headerName: "{{ $obj->opt_nm }}", field: "opt2_{{ $obj->opt_nm }}", 
                    suppressMovable: true, 
                    cellClassRules: optCellClassRules,
                    editable: true,
                    cellStyle: CELL_COLOR.YELLOW
                });
            @endforeach
        @endif

        const optStockColumns = (opt1_kind_nm, opt2_kind_nm) => {
            let cols = [];
            if (IS_SINGLE == "true") {
                // 단일 옵션인 경우
                cols = [
                    {field: "opt1_nm", headerName: "옵션", width:100, cellStyle: CELL_COLOR.LOCKED, cellClass: "locked-cell", pinned: 'left', suppressMovable: true},
                    {
                        field: "qty",
                        headerName: "입고수량",
                        width: 120,
                        cellStyle: CELL_COLOR.YELLOW,
                        editable: true,
                        cellClassRules: optCellClassRules
                    },
                    {field: "nvl", width: "auto", headerName: ""}
                ];
            } else if (IS_SINGLE == "false") {
                // 멀티 옵션인 경우
                cols = [
                    {field: "opt1_nm", headerName: "옵션", width:100, cellStyle: CELL_COLOR.LOCKED, cellClass: "locked-cell", pinned: 'left', suppressMovable: true},
                    {
                        field: "opt2_kind_name",
                        headerName: "입고수량",
                        width: 120
                    },
                    {field: "nvl", width: "auto", headerName: ""}
                ];
                if (opt2_kind_opts.length > 0) cols[1].children = opt2_kind_opts;
            }
            return cols;
        };

        const setOptRows = () => {
            let cols = opt1_kind_opts;
            gx.addRows(opt1_kind_opts);
        };

        const autoSizeColumns = (grid, except = [], skipHeader = false) => {
            const allColumnIds = [];
            grid.gridOptions.columnApi.getAllColumns().forEach((column) => {
                if (except.includes(column.getId())) return;
                allColumnIds.push(column.getId());
            });
            grid.gridOptions.columnApi.autoSizeColumns(allColumnIds, skipHeader);
        };

        const gxStartEditingCell = (row_index, col_key) => {
            gx.gridOptions.api.startEditingCell({ rowIndex: row_index, colKey: col_key });
        };

        const optEvtAfterEdit = (params) => {
            if (params.oldValue !== params.newValue) {
                row = params.data;
                const row_index = params.rowIndex;
                const column_name = params.column.colId;
                const value = params.newValue;

                if (isNaN(value) == true || value == "") {
                    alert("숫자만 입력가능합니다.");
                    gxStartEditingCell(row_index, column_name);
                }

                // 셀 값 수정시 빨간색으로 변경 - 단일 옵션인 경우
                if (IS_SINGLE == "true") {
                    row.is_changed = true;
                } else if (IS_SINGLE == "false") {
                    // 셀 값 수정시 빨간색으로 변경 - 2단 옵션인 경우
                    if (row.hasOwnProperty('is_changed')) {
                        row.is_changed[column_name] = true;
                    } else {
                        row.is_changed = {};
                        row.is_changed[`${column_name}`] = true;
                    }
                }
                gx.gridOptions.api.applyTransaction({ update : [row] });
            }
        };

        const pApp = new App('',{
            gridId:"#div-gd",
        });
        let gx;

        pApp.ResizeGrid(500);
        pApp.BindSearchEnter();
        let gridDiv = document.querySelector(pApp.options.gridId);
        const columns = optStockColumns(OPT1_KIND_NM, OPT2_KIND_NM)
        const options = {
            onCellValueChanged: (params) => optEvtAfterEdit(params)
        }   
        gx = new HDGrid(gridDiv, columns, options);

        setOptRows();
        autoSizeColumns(gx, ["nvl"]);

        const validate = () => {
            if ( document.stock.sdate.value == "" ) {
                alert("입고일자를 입력해주세요.");
                document.stock.sdate.focus();
                return false;
            }

            if ( document.stock.invoice_no.value == "" ) {
                alert("송장번호를 입력해주세요.");
                document.stock.invoice_no.focus();
                return false;
            }

            if ( document.stock.wonga.value == "" ) {
                alert("원가를 입력해주세요.");
                document.stock.wonga.focus();
                return false;
            }
            return true;
        };

        const refinedData = () => {
            const rows = gx.getRows();
            let data = [];
            if (IS_SINGLE == "true") {
                rows.map((row) => {
                    const opt = row.opt1_nm;
                    const qty = row.qty;
                    data.push({ opt: opt, qty: qty });
                });
            } else if (IS_SINGLE == "false") {
                rows.map((row) => {
                    let opt2_nms = [];
                    Object.keys(row).map(key => {
                        let regExp = /(?<=opt2_).+/i; // 후방탐색
                        const arr = key.match(regExp);
                        if (arr) opt2_nms.push(arr[0]);
                    })
                    const opt1_nm = row.opt1_nm;
                    opt2_nms.map(key => {
                        const opt = opt1_nm + "^" + key;
                        const qty = row[`opt2_${key}`];
                        data.push({ opt: opt, qty: qty });
                    });
                });
            }
            return data;
        };

        const stockIn = async () => {
            if (validate() === false) return false;
            if (confirm('입력하신 수량으로 입고 처리 하시겠습니까?')) {
                try {
                    const response = await axios({ 
                        url: `/head/product/prd01/stock-in`, method: 'post', 
                        data: {
                            'goods_no': document.stock.goods_no.value,
                            'invoice_no': document.stock.invoice_no.value,
                            'wonga': document.stock.wonga.value,
                            'loc': document.stock.loc.value,
                            'data': refinedData()
                        }
                    });
                    const { data, cfg_domain } = response;
                    if (data?.code == 200) {
                        alert("[ 입고완료 ] 입고 처리가 완료되었습니다.");
                        window.opener.location.reload();
                    } else if (data?.code == 500) { 
                        alert("[ 입고 실패 ] 관리자에게 문의해 주세요.");
                    } else if (data?.code == -1) {
                        alert("재고조정용 발주건 또는 송장번호가 존재하지 않습니다.\\n발주건은 공급처가 [%1] 만 가능합니다.\\n", cfg_domain);
                    }
                } catch (error) {
                    // console.log(error);
                }
            }
        };

    </script>
@stop

@extends('head_with.layouts.layout-nav')
@section('title','입고')
@section('content')

<!-- import excel lib -->
<script src="https://unpkg.com/xlsx-style@0.8.13/dist/xlsx.full.min.js"></script>

<style>
    #help legend { line-height: inherit; position: relative; width: 100%; left: 0; height: auto; z-index: 0;}
    #help li { list-style: disc; margin-left: 25px; font-size: 13px; }
    .wrong { color: #FF0000; }
    .form-control[readonly] {
        background: #eeeeee;
    }
    .form-control[readonly][name="custom_tax_rate"] {
        background: white !important;
    }
</style>
<div class="py-3 px-sm-3">
    <div class="page_tit">
        <h3 class="d-inline-flex">입고 {{ $invoice_no ? (" - " . $invoice_no) : "" }}</h3>
        <div class="d-inline-flex location">
            <span class="home"></span>
            <span>/ 재고</span>
        </div>
    </div>
    <form method="get" name="search">
        <input type="hidden" name="cmd" value="<?=$cmd ? $cmd : "" ?>">
        <input type="hidden" name='stock_no' value='<?=$stock_no ? $stock_no : ""?>'>
        <div id="search-area" class="search_cum_form">
            <div class="card mb-3">
                <div class="d-flex card-header justify-content-between">
                    <h4>기본 정보</h4>
                    <div>
                        @if ($state > 0 && $state < 30)
                            <a href="#" onclick="cmder('{{$cmd}}')" class="btn btn-sm btn-primary shadow-sm pl-2"><i class="bx bx-save mr-1"></i>저장</a>
                            @if ($stock_no != "" && $state < 30)
                            <a href="#" onclick="cmder('delcmd')" class="btn btn-sm btn-primary shadow-sm pl-2"><i class="bx mr-1"></i>입고삭제</a>
                            @endif
                        @elseif ($state == 30)
                            <a href="#" onclick="cmder('addstockcmd')" class="btn btn-sm btn-primary shadow-sm pl-2"><i class="bx mr-1"></i>추가입고</a>
                            <a href="#" onclick="cmder('cancelcmd')" class="btn btn-sm btn-primary shadow-sm pl-2"><i class="bx mr-1"></i>입고취소</a>
                        @endif
                        <a href="#" onclick="gx.Download();" class="btn btn-sm btn-outline-primary shadow-sm pl-2"><i class="bx bx-download fs-16"></i> 엑셀다운로드</a>
                        <a href="#" onclick="displayHelp()" class="btn btn-sm btn-outline-primary shadow-sm pl-2"><i class="bx mr-1"></i>도움말</a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label for="com_nm" class="required">공급처</label>
                                <div class="form-inline inline_select_box">
                                    <div class="form-inline-inner input-box w-75 pr-1">
                                        <div class="form-inline inline_btn_box">
                                            <input type="text" id="com_nm" name="com_nm" class="form-control form-control-sm ac-company sch-company" value="<?=$com_nm ? $com_nm : ""?>">
                                            <a href="#" class="btn btn-sm btn-outline-primary sch-company"><i class="bx bx-dots-horizontal-rounded fs-16"></i></a>
                                        </div>
                                    </div>
                                    <div class="form-inline-inner input-box w-25 pl-1">
                                        <input type="text" id="com_id" name="com_id" class="form-control form-control-sm" value="<?=$com_id ? $com_id : ""?>" readonly />
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label for="invoice_no" class="required">송장번호</label>
                                <div class="flex_box">
                                    <input type="text" onfocus="return getInvoiceNo();" class="form-control form-control-sm" name="invoice_no" value="<?=$invoice_no ? $invoice_no : '' ?>">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label for="" class="required">입고상태</label>
                                <div class="flex_box">
                                    <select name="state" class="form-control form-control-sm w-100">
                                        <?php
                                            $states->map(function ($item) use ($state) {
                                                $selected = ($state == $item['code_id']) ? 'selected' : "";
                                                $code_id = $item['code_id'];
                                                $code_val = $item['code_val'];
                                                echo "<option value='${code_id}' ${selected}>${code_val}</option>";
                                            });
                                        ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label for="formrow-firstname-input" class="required">입고일자</label>
                                <div class="flex_box">
                                    <div class="docs-datepicker form-inline-inner input_box">
                                        <div class="input-group">
                                        <?php $stock_date = substr($stock_date, 0, 4).'-'.substr($stock_date, 4, 2) . '-' . substr($stock_date, 6, 2); ?>
                                            <input type="text" class="form-control form-control-sm docs-date" id="stock_date" 
                                                name="stock_date" value="<?=$stock_date ? $stock_date : ""?>" autocomplete="off">
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
                    <div class="row">
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label for="f_sqty" class="required">환율</label>
                                <div class="form-inline inline_select_box">
                                    <div class="form-inline-inner input-box w-25 pr-2">
                                        <select id="currency_unit" name="currency_unit" class="form-control form-control-sm w-100" onchange="changeUnit(this)">
                                            <?php
                                                $currencies = ['KRW', 'USD', 'EUR', 'JPY', 'CNY', 'HKD'];
                                                collect($currencies)->map(function ($currency) use ($currency_unit) {
                                                    $selected = ($currency == $currency_unit) ? "selected" : "";
                                                    echo "<option value='${currency}' $selected>${currency}</option>";
                                                });
                                            ?>
                                        </select>
                                    </div>
                                    <div class="form-inline-inner input-box w-25">
                                        <input readonly disabled type='text' class="form-control form-control-sm" name='exchange_rate' id='exchange_rate' 
                                            value='<?= $exchange_rate ? $exchange_rate : 0 ?>' style="width:100%;" onkeypress="checkFloat(this);" onkeyup="com3(this);calCustomTaxRate();" onfocus="this.select()">
                                    </div>
                                    <span class="ml-2">원</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label for="custom_amt" class="required">(신고)금액</label>
                                <div class="flex_box">
                                    <input type="text" class="form-control form-control-sm" name="custom_amt" value="<?= $custom_amt ? $custom_amt : 0 ?>"
                                        onfocus="this.select()" onkeypress="checkFloat(this);" onkeyup="com3(this);calCustomTaxRate();"
                                    >
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label for="custom_tax">통관비</label>
                                <div class="flex_box">
                                    <input id="custom_tax" type="text" class="form-control form-control-sm" name="custom_tax" value="<?= $custom_tax ? $custom_tax : 0 ?>"
                                        onfocus="this.select()" onkeypress="checkFloat(this);" onkeyup="com(this);calCustomTaxRate();" <?=$currency_unit == "KRW" ? "readonly disabled" : ""?>
                                    >
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label for="custom_tax_rate">통관세율</label>
                                <div class="form-inline inline_select_box">
                                    <div class="form-inline-inner input-box w-25 mr-2">
                                        <input readonly type='text' class="form-control form-control-sm" name='custom_tax_rate' id='custom_tax_rate' value='{{ $custom_tax_rate ? $custom_tax_rate : "" }}' style="width:100%;"
                                            <?=$currency_unit == "KRW" ? "disabled" : ""?> onfocus="this.select()" onkeypress="checkFloat(this);"
                                        >
                                    </div>
                                    <span>%</span>
                                    <a href="#" class="btn btn-sm btn-outline-primary shadow-sm ml-2" onclick="calExchange();" onfocus="this.blur()">환율 및 세율 적용</a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label for="area_type">위치</label>
                                <div class="flex_box">
                                    <select name="loc" class="form-control form-control-sm w-100">
                                        @foreach ($locs as $item)
                                            <option value='{{ $item->code_id }}' selected='{{ $item == $loc ? selected : "" }}'>{{ $item->code_val }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label for="f_sqty">파일</label>
                                <div class="flex_box">
                                    <div class="custom-file w-50">
                                        <input name="excel_file" type="file" class="custom-file-input" id="excel_file">
                                        <label class="custom-file-label" for="file"></label>
                                    </div>
                                    <div class="btn-group ml-2">
                                        <button class="btn btn-outline-primary apply-btn" type="button" onclick="upload();">적용</button>
                                    </div>
                                    <a href="/sample/sample_receiving.xlsx" class="ml-2" style="text-decoration: underline !important;">샘플파일</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card mb-4" id="help" style="display: none;">
                    <div class="d-flex card-header justify-content-between" >
                        <fieldset class="FSHelp">
                            <legend class="Tip" style="font-size: 15px; color: blue;">도움말</legend>
                            <ul>
                                <li>금액 = 단가 * 수량</li>
                                <li>원가(원,VAT포함) = 단가 * ( 1 + 통관세율/100 ) * 환율 * 1.1</li>
                                <li>총원가(원) = 원가(원,VAT포함) * 수량</li>
                            </ul>
                        </fieldset>
                    </div>
                </div>
            </div>
            <div class="resul_btn_wrap mb-3">
                <a href="#" onclick="cmder('addstockcmd')" class="btn btn-sm btn-primary shadow-sm pl-2"><i class="bx mr-1"></i>추가입고</a>
                <a href="#" onclick="cmder('cancelcmd')" class="btn btn-sm btn-primary shadow-sm pl-2"><i class="bx mr-1"></i>입고취소</a>
                <a href="#" onclick="cmder('{{$cmd}}')" class="btn btn-sm btn-primary shadow-sm pl-2"><i class="bx bx-save mr-1"></i>저장</a>
                <a href="#" onclick="gx.Download();" class="btn btn-sm btn-outline-primary shadow-sm pl-2"><i class="bx bx-download fs-16"></i> 엑셀다운로드</a>
                <a href="#" onclick="displayHelp()" class="btn btn-sm btn-outline-primary shadow-sm pl-2"><i class="bx mr-1"></i>도움말</a>
            </div>
        </div>
        <div id="filter-area" class="card shadow-none mb-0 ty2 last-card">
            <div class="card-body">
                <div class="card-title mb-3">
                    <div class="filter_wrap">
                        <div class="fr_box">
                            <a href="#" onclick="deleteRows();" class="btn-sm btn btn-primary" onfocus="this.blur()">상품삭제</a>
                            <a href="#" onclick="getSearchGoods();" class="btn-sm btn btn-primary" onfocus="this.blur()">상품 가져오기</a>
                        </div>
                    </div>
                </div>
                <div class="table-responsive">
                    <div id="div-gd" style="height:calc(100vh - 370px);width:100%;" class="ag-theme-balham"></div>
                </div>
            </div>
        </div>
    </form>
</div>
<script type="text/javascript" charset="utf-8">

    const STATE = "{{$state}}";
    

    /**
     * ag-grid set field
     */

    const numberFormatter = (params) => {
        if (document.search.currency_unit.value == "KRW") {
            // console.log("원")
            return Math.round(params.value)
                .toString()
                .replace(/(\d)(?=(\d{3})+(?!\d))/g, '$1,');
        } else {
            // console.log("외화")
            return parseFloat(params.value).toFixed(2).toString()
                    .replace(/(\d)(?=(\d{3})+(?!\d))/g, '$1,');;
        }
    };

    // 기존에 공용으로 사용하던 화폐단위 type은 소수점을 전부 버리므로 반올림으로 커스텀하여 구현하였음
    const currencyFormatter = (params) => { 
        const value = Math.round(params.value).toString().replace(/(\d)(?=(\d{3})+(?!\d))/g, '$1,');
        return isNaN(value) ? 0 : value;
        
    };

    var columns= [
        {headerName: '#', pinned: 'left', type: 'NumType', width: 50,
            cellRenderer: params => params.node.rowPinned == 'top' ? '' : params.data.count,
            sortingOrder: ['desc', 'asc', 'null'],
            comparator: (valueA, valueB, nodeA, nodeB, isInverted) => { // 번호순으로 정렬이 안되는 문제 수정
                if (parseInt(valueA) == parseInt(valueB)) return 0;
                return (parseInt(valueA) > parseInt(valueB)) ? 1 : -1;
            },
        },
        {field:"chk", headerName: '', cellClass: 'hd-grid-code',
            headerCheckboxSelection: params => {
                const state = STATE; // 입고취소: -10, 입고대기: 10, 입고처리중: 20, 입고완료: 30
                // 입고 대기이거나 입고 처리중인 경우에만 체크박스 표시 -> 삭제 가능하게함

                return state == -10 || state == 30 ? false : true;
            },
            checkboxSelection: params => checkIsEditable(params),
            cellStyle: params => checkIsEditable(params) ? null : { display: 'none'},
            width: 40, pinned:'left',
            // hide: params => {
            //     const state = STATE; // 입고취소: -10, 입고대기: 10, 입고처리중: 20, 입고완료: 30
            //     // 입고 대기이거나 입고 처리중인 경우에만 체크박스 표시 -> 삭제 가능하게함
            //     return state == -10 || state == 30 ? false : true;
            // }
        },
        {field:"item" ,headerName:"품목",pinned:'left',width:96},

        // 기존 서비스에서는 브랜드와 스타일 넘버도 수정이 가능하였지만, 논의 후 불필요한 것으로 판단되어 제거
        {field:"brand" ,headerName:"브랜드",pinned:'left',width:96,
            // cellStyle: params => checkIsEditable(params) ? {backgroundColor: '#ffff99'} : null ,
            // editable: params => checkIsEditable(params)
        },
        {field:"style_no" ,headerName:"스타일넘버",pinned:'left',width:110,
            // cellStyle: params => checkIsEditable(params) ? {backgroundColor: '#ffff99'} : null ,
            // editable: params => checkIsEditable(params)
        },
        {headerName:"상품코드",
            children: [
                {headerName: "번호", field: "goods_no", width: 60, pinned:'left', cellStyle:{'text-align': 'center'}},
                {headerName: "보조", field: "goods_sub", width: 60, pinned:'left', cellStyle:{'text-align': 'center'}}
            ]
        },
        {field:"goods_nm" , headerName:"상품명", type:"HeadGoodsNameType", width:250, pinned:'left'},
        {field:"opt_kor",headerName:"옵션",pinned:'left',width:130,
            editable: params => checkIsEditable(params),
            cellStyle: params => checkIsEditable(params) ? {backgroundColor: '#ffff99'} : null,
        },
        {headerName: "수량", field: "qty", width: 60,
            editable: params => checkIsEditable(params),
            cellStyle: params => checkIsEditable(params) ? {backgroundColor: '#ffff99', textAlign: 'right'} : {textAlign: 'right'},

        },
        {headerName: "단가", field: "unit_cost", width: 60,
            editable: params => checkIsEditable(params),
            cellStyle: params => checkIsEditable(params) ? {backgroundColor: '#ffff99', textAlign: 'right'} : {textAlign: 'right'},
            valueFormatter: numberFormatter,
            cellRenderer: params => { return params.node.rowPinned == "top" ? "" : params.valueFormatted }
        },
        {headerName: "금액", field: "unit_total_cost", width: 72, cellStyle:{'text-align': 'right'}, 
            valueFormatter: numberFormatter
        },
        {headerName: "원가(원, VAT포함)", field: "cost", width: 120, cellStyle:{'text-align': 'right'}, 
            valueFormatter: numberFormatter
        },
        {headerName: "총원가(원)", field: "total_cost", width: 96, cellStyle:{'text-align': 'right'}, 
            valueFormatter: numberFormatter
        },
        {headerName: "총원가(원, VAT별도)", field: "total_cost_novat", width: 134, cellStyle:{'text-align': 'right'}, 
            valueFormatter: numberFormatter
        },
        {headerName: "최근입고일자", field: "stock_date", width:96, cellStyle: {"text-align" : 'center'}},
        {headerName:"", field:"", width:"auto"}
    ];

    
    /**
     * ag-grid init - 초기화 및 기타 logics
     */
    
    const pApp = new App('', {
        gridId: "#div-gd",
    });
    let gx;

    const pinnedRowData = [{ item: '합계', unit_total_cost: 0, cost: 0, total_cost: 0, count: 0 }];
    
    $(document).ready(() => {
        pApp.ResizeGrid(275);
        pApp.BindSearchEnter();
        let gridDiv = document.querySelector(pApp.options.gridId);
        let options = {
            pinnedTopRowData: pinnedRowData,
            getRowStyle: (params) => { // 고정된 row styling
                if (params.node.rowPinned)  return { 'font-weight': 'bold', 'background': '#eee', 'border': 'none'};
            },
            getRowNodeId: (data) => data.hasOwnProperty('count') ? data.count : "0", // 업데이터 및 제거를 위한 식별 ID를 count로 할당
            /**
             *  키보드 방향키 누르면 바로 입력할 수 있는 부분(개발중)
             */
            // onCellKeyDown : function(e) {
            //     let key = e.event.key;
            //     if (key == 'ArrowDown') {
            //         gx.gridOptions.api.stopEditing();
            //         let rowIndex = e.rowIndex + 1;
            //         let column = e.column.colId;
            //         gx.gridOptions.api.startEditingCell({ rowIndex: rowIndex, colKey: column });

            //     } else if (key == 'ArrowUp') {
            //         gx.gridOptions.api.stopEditing();
            //         let rowIndex = e.rowIndex - 1;
            //         let column = e.column.colId;
            //         gx.gridOptions.api.startEditingCell({ rowIndex: rowIndex, colKey: column });
            //     } 
            // },
            onCellValueChanged: async (params) => {
                await evtAfterEdit(params);
            },
        };
        gx = new HDGrid(gridDiv, columns,options);
        $("#img").click(() => {
            gx.gridOptions.columnApi.setColumnVisible('img',$("#img").is(":checked"));
        });
        const ff = document.search;
        if(ff.cmd.value == "editcmd") productListDraw();
    });

    const checkIsEditable = (params) => {
        return params.data.hasOwnProperty('isEditable') && params.data.isEditable ? true : false;
    };

    const strNumToPrice = (price) => {
        return typeof price == 'string' ? price.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ',') : "";
    };

    const productListDraw = () => {
        var ff = document.search;
        const stock_no = ff.stock_no.value;
        axios({
            url: COMMAND_URL,
            method: 'post',
            data: { cmd: 'product', stock_no: stock_no }
        }).then((response) => {
            const rows = response.hasOwnProperty('data') && response.data.hasOwnProperty('rows') ? response.data.rows : "";
            if (rows && Array.isArray(rows)) {
                rows.map((row, idx) => {
                    if (STATE == 10 || STATE == 20) { // 입고 대기거나 입고 처리중인 경우 체크박스 표시, 상품 삭제를 가능하게 합니다.
                        row.isEditable = true;
                    } else {
                        row.isEditable = false;
                    }
                    row.count = idx + 1;
                    gx.gridOptions.api.applyTransaction({add : [row]})
                });
                updatePinnedRow();
            }
        }).catch((error) => {
            console.log(error);
        });
    };

    var addRow = (row) => { // goods_api에서 opener 함수로 사용하기 위해 var로 선언
        const count = gx.gridOptions.api.getDisplayedRowCount();
        row = { ...row, 
            item: row.opt_kind_nm, qty: 0, cost: 0, unit_cost: 0, unit_total_cost: 0, total_cost: 0, total_cost_novat: 0,
            isEditable: true, count: count + 1, opt_kor: ''
        };
        gx.gridOptions.api.applyTransaction({add : [row]});
        // $('#gx-total').html(count);
    };

    const updatePinnedRow = () => { // 총 금액, 원가, 총원가를 반영한 PinnedRow를 업데이트
        let [ unit_total_cost, cost, total_cost, total_cost_novat ] = [ 0, 0, 0, 0 ];
        const rows = gx.getRows();
        if (rows && Array.isArray(rows) && rows.length > 0) {
            rows.map((row, idx) => {
                unit_total_cost += parseFloat(row.unit_total_cost);
                cost += parseFloat(row.cost);
                total_cost += parseFloat(row.total_cost);
                total_cost_novat += parseFloat(row.total_cost_novat);
            })
        }
        let pinnedRow = gx.gridOptions.api.getPinnedTopRow(0);
        gx.gridOptions.api.setPinnedTopRowData([
            { ...pinnedRow.data, unit_total_cost: unit_total_cost, cost: cost, total_cost: total_cost, total_cost_novat: total_cost_novat }
        ]);
    };

    const deleteRow = (row) => { gx.gridOptions.api.applyTransaction({remove : [row]}); };

    const deleteRows = () => {
        const ff = document.search;
        const state = STATE; // 입고취소: -10, 입고대기: 10, 입고처리중: 20, 입고완료: 30
        const rows = gx.getSelectedRows();
        if (Array.isArray(rows) && !(rows.length > 0)) {
            alert('선택된 항목이 없습니다.')
            return false;
        } else {
            if (state == -10 || state == 30) { // 입고취소나 완료인 경우 가져온 상품만 삭제
                rows.filter((row, idx) => row.isEditable).map((row) => { deleteRow(row); });
            } else if (state == 10 || state == 20) { // 입고대기나 입고처리중인 경우 저장했던 상품도 삭제 가능
                rows.map(row => { deleteRow(row); });
            }
            updatePinnedRow();
        };
    };

    /**
     * goods api logics - 상품 가져오기
     * window opener에서 콜백을 사용하려면 var로 선언해야 합니다.
     */

    var goodsCallback = (row) => {
        addRow(row);
    };

    var multiGoodsCallback = (rows) => {
        if (rows && Array.isArray(rows)) rows.map(row => addRow(row));
    };

    let goods_search_cmd = '';
    const getSearchGoods = () => {
        const ff = document.search;
        const com_id = ff.com_id.value;
        if (com_id == '') {
            alert('업체를 선택하여 주십시오.');
            ff.com_nm.click();
            return false;
        }
        const url=`/head/api/goods/show`;
        const pop_up = window.open(url,"_blank","toolbar=no,scrollbars=yes,resizable=yes,status=yes,top=100,left=100,width=1800,height=1000");
    };

    var beforeSearchCallback = (api_document) => {
        const [ search_form, api_search_form ] = [ document.search, api_document.search ];
        api_search_form.com_nm.value = search_form.com_nm.value;
        api_search_form.com_id.value = search_form.com_id.value;
    };

    /**
     * form logics - 데이터 전송 및 벨리데이션
     */

    const COMMAND_URL = '/head/stock/stk11/comm';

    const cmder = async (cmd) => {
        if (cmd == "editcmd" || cmd == "addcmd" || cmd == "addstockcmd") {
            if (validate() && await validateData()) saveCmd(cmd); // validateData
        } else if (cmd == "delcmd") {
            delCmd();
        } else if (cmd == "cancelcmd") {
            cancelCmd();
        }
    };

    const validate = () => {
        const ff = document.search;
        if( ff.com_id.value == "" ) {
            alert("공급처를 선택해 주십시오.");
            $('.sch-company').click();
            return false;
        }
        if( ff.invoice_no.value == "" ) {
            alert("송장번호를 입력해 주십시오.");
            ff.invoice_no.focus();
            return false;
        }
        if( ff.state.value == "" ) {
            alert("입고상태를 선택해 주십시오..");
            ff.state.focus();
            return false;
        }
        if( ff.stock_date.value.trim().length != 10 ) {
            alert("입고일자를 입력해 주십시오.");
            ff.stock_date.focus();
            return false;
        }
        if( ff.currency_unit.value != "KRW"){
            if( ff.exchange_rate.value == ""){
                alert("환율를 입력해 주십시오.");
                ff.exchange_rate.focus();
                return false;
            }
            if( ff.custom_tax_rate.value == ""){
                alert("통관세율를 입력해 주십시오.");
                ff.custom_tax_rate.focus();
                return false;
            }
        }
        return true;
    };

    const validateData = async (cmd) => {
        const rows = gx.getRows();
        let row;
        for (let i = 0; i < rows.length; i++) {
            row = rows[i];
            if (await checkValidateData(row) == false) return false;
        };
        return true;
    };

    const checkValidateData = async (row) => {
        
        const row_index = row.count - 1;
        const { qty, unit_cost, goods_no, style_no } = row;

        if (isNaN(parseInt(goods_no))) {
            gx.gridOptions.api.stopEditing(); // stop editing
            alert(`스타일넘버 ${style_no}은 유효하지 않은 상품입니다. \n상품을 삭제후 다시등록해주세요.`);
            const node = gx.gridOptions.api.getRowNode(row.count);
            node.setSelected(true);
            return false;
        }

        if (qty == "" || qty == 0) { // check qty
            gx.gridOptions.api.stopEditing(); // stop editing
            alert('입고수량을 입력해 주십시오.');
            startEditingCell(row_index, 'qty');
            return false;
        }
        if (unit_cost == "" || unit_cost == 0) { // check unit_cost
            gx.gridOptions.api.stopEditing(); // stop editing
            alert('단가를 입력해 주십시오.');
            startEditingCell(row_index, 'unit_cost');
            return false;
        }

        // const checked_opt = await checkOption(row); // check option
        // if (checked_opt == false) return false;
        
        return true;
    };

    // const checkOption = async (row) => {
    //     const CMD = 'checkopt';
    //     const data = { cmd: CMD, goods_no: row.goods_no, goods_sub: row.goods_sub, opt: row.opt_kor };
    //     let code;
    //     await axios({ url: COMMAND_URL, method: 'post', data: data })
    //         .then((response) => { code = response.data.code; }).catch((error) => { console.log(error); });
    //     if (code !=1) {
    //         const row_index = row.count - 1;
    //         gx.gridOptions.api.stopEditing(); // stop editing
    //         alert('옵션을 정확하게 입력해 주십시오.');
    //         startEditingCell(row_index, 'opt_kor');
    //         return false;
    //     }
    //     return true;
    // };

    const startEditingCell = (row_index, col_key) => {
        gx.gridOptions.api.startEditingCell({ rowIndex: row_index, colKey: col_key });
    };

    /*
        Function: unComma
            콤마 없애기

        Parameters:
            obj - object
    */
    var unComma = (input) => {
        var inputString = new String;
        var outputString = new String;
        var outputNumber = new Number;
        var counter = 0;
        inputString=input;
        outputString='';
        for (counter=0;counter <inputString.length; counter++)
        {
            outputString += (inputString.charAt(counter) != ',' ?inputString.charAt(counter) : '');
        }
        outputNumber = parseFloat(outputString);
        
        return (outputNumber);
    }

    /*
        Function: com3
            콤마처리 ( 마이너스와 소수점 처리 )

        Parameters:
            obj - object
    */
    function com3(obj) {
        var str = obj.value;
        if ( str != null && str != "" ) {

            var retStr = "";
            var m = "";
            var dot = "";
            var dotIdx = -1;
            str = str.replace(/^0*|\,/g,'');
            if( str.charAt(0) == "-" ) {
                m = "-";
                str = str.substr(1,str.length);
                //alert(str);
            }
            dotIdx = str.indexOf(".");
            if( dotIdx > 0 ) {
                dot = str.substr(dotIdx,str.length);
                str = str.substr(0,dotIdx);
                //alert(str);
                //alert(dotIdx);
            }
            var strLen = str.length;
            for(var i=0; i<strLen; i++){
                if ((i%3 == strLen%3) && (i != 0)) {
                    retStr += ",";
                }
                retStr += str.charAt(i);
            }
            obj.value = "" + m + retStr + dot + "";
        }
    }

    /*
        Function: currency
            숫자만 입력

        Parameters:
            obj - text
    */

    function checkFloat(obj)
    {
        var keycode = event.keyCode;
        if (keycode >= 48 && keycode <= 57) {
        } else if(keycode == 46){
        } else {
            event.returnValue = false;
        }
    }

    var evtAfterEdit = async (params) => { // edit 가능한 셀 수정시 계산하고 고정 row를 업데이트합니다.
        if (params.oldValue !== params.newValue) {
            const row = params.data;
            const ff = document.search;
            if (row.goods_no > 0) {
                const [ unit, exchange_rate, custom_tax_rate ] = [ ff.currency_unit.value, unComma(ff.exchange_rate.value), ff.custom_tax_rate.value ];
                await calProduct(row, unit, exchange_rate, custom_tax_rate);
                // checkOption(row);
            }
        }
        updatePinnedRow();
    };

    const calProduct = async (row, unit, exchange_rate, custom_tax_rate) => {

        const qty = row.qty ? parseInt(row.qty) : 0;
        let unit_cost;
        unit_cost = row.unit_cost ? row.unit_cost : 0;

        let cost, total_cost, total_cost_novat;
        if (unit == "KRW") {
		    cost = unit_cost;
	    } else {
            cost = unit_cost * exchange_rate;
		    cost = cost * (1 + custom_tax_rate / 100 );
		    cost = parseInt(cost);
        };

        total_cost = qty * cost;
        total_cost_novat = Math.round(total_cost/1.1,0); // 총원가 vat 별도

        await gx.gridOptions.api.applyTransaction({ update: [{...row,
            unit_total_cost: qty * unit_cost, // 금액
            cost: cost, // 원가 (원, VAT 포함)
            total_cost: total_cost, // 총원가 (원)
            total_cost_novat: total_cost_novat, // 총원가 (원, VAT 별도)
        }] });

    };

    const calExchange = async () => {

        var ff = document.search;
        var unit = ff.currency_unit.value;
        var exchange_rate = unComma(ff.exchange_rate.value);
        var custom_tax_rate = ff.custom_tax_rate.value;

        const state = STATE; // 입고취소: -10, 입고대기: 10, 입고처리중: 20, 입고완료: 30
        // 입고 대기이거나 입고 처리중인 경우에만 환율 및 세율 적용 가능하게함
        
        if (state == -10 || state == 30 ) {
        } else {
            const rows = gx.getRows();
            if (rows && Array.isArray(rows) && rows.length > 0) {
                await rows.map((row, idx) => {
                    calProduct(row, unit, exchange_rate, custom_tax_rate);
                })
                updatePinnedRow();
            }
        }

    };

    const calCustomTaxRate = () => {
        
        var ff = document.search;
        var currency_unit = ff.currency_unit.value;

        if (currency_unit != "KRW") {
            var exchange_rate = 0;
            var custom_tax = 0;
            var custom_tax_rate = 0;

            var exchange_rate = unComma(ff.exchange_rate.value);
            var custom_amt = unComma(ff.custom_amt.value);

            if (unComma(ff.custom_tax.value) > 0) {
                custom_tax = unComma(ff.custom_tax.value);
            }

            if (custom_amt > 0) {
                custom_tax_rate = custom_tax / ( custom_amt * exchange_rate ) * 100;
            } else {
            }

            ff.custom_tax_rate.value = Math.round(custom_tax_rate*100)/100;
        }

    };

    const getFormValue = (form, name) => {
        return (form.hasOwnProperty(name)) ? form[name].value : "";
    };

    const getInvoiceNo = () => {
        const ff = document.search;
	    const com_id = ff.com_id.value;
	    let invoice_no = ff.invoice_no.value;
        if (invoice_no == '' && com_id != "") {
            axios({
                url: COMMAND_URL,
                method: 'post',
                data: {
                    cmd: 'getinvoiceno',
                    com_id: com_id
                }
            }).then((response) => {
                invoice_no = response.data.invoice_no;
                ff.invoice_no.value = invoice_no;
            }).catch((error) => { 
                // console.log(error);
            });
        }
    };

    const saveCmd = (cmd) => {
        if (confirm('저장하시겠습니까?')) {
            const ff = document.search;
            const data = gx.getRows();

            axios({
                url: COMMAND_URL,
                method: 'post',
                data: {
                    cmd: cmd,
                    data: data,
                    stock_no: getFormValue(ff, 'stock_no'),
                    invoice_no: getFormValue(ff, 'invoice_no'),
                    stock_date: getFormValue(ff, 'stock_date'),
                    com_id: getFormValue(ff, 'com_id'),
                    currency_unit: getFormValue(ff, 'currency_unit'),
                    exchange_rate: getFormValue(ff, 'exchange_rate'),
                    state: getFormValue(ff, 'state'),
                    loc: getFormValue(ff, 'loc'),
                    area_type: getFormValue(ff, 'area_type'),
                    custom_tax_rate: getFormValue(ff, 'custom_tax_rate'),
                    custom_amt: getFormValue(ff, 'custom_amt'),
                    custom_tax: getFormValue(ff, 'custom_tax'),
                    exchange_rate: getFormValue(ff, 'exchange_rate'),
                    currency_unit: getFormValue(ff, 'currency_unit')
                }
            }).then((response) => {
                if (response.data.code == 1) {
                    window.opener.Search();
                    window.close();
                } else {
                    alert(response.data.message);
                }
            }).catch((error) => {
                // console.log(error);
            });
        };
    };

    const cancelCmd = () => {
        if (confirm('입고취소를 하시겠습니까?')) {
            const ff = document.search;
            const stock_no = ff.stock_no.value;
            if (ff.state.value == "30") {
                axios({
                    url: COMMAND_URL,
                    method: 'post',
                    data: { cmd : 'cancelcmd', stock_no : stock_no }
                }).then((response) => {
                    if (response.data.code == 1) {
                        window.opener.Search();
                        alert("입고취소 하였습니다.");
                        window.close();
                    } else {
                        alert("입고취소를 실패하였습니다. 다시 한번 시도하여 주십시오.");
                        console.log(response);
                    }
                }).catch((error) => {});
            }
        }
    };

    const delCmd = () => {
        if (confirm('정말로 삭제하시겠습니까?')) {
            const ff = document.search;
            const stock_no = ff.stock_no.value;
            axios({
                url: COMMAND_URL,
                method: 'post',
                data: { cmd : 'delcmd', stock_no : stock_no }
            }).then((response) => {
                if (response.data.code == 1) {
                    window.opener.Search();
                    window.close();
                } else {
                    alert('삭제를 실패하였습니다. 다시 한번 시도하여 주십시오. 코드번호 : ' + response.data.code );
                }
            }).catch((error) => { });
        }
    };

    const validateFile = () => {
        const target = $('#excel_file')[0].files;

        if (target.length > 1) {
            alert("파일은 1개만 올려주세요.");
            return false;
        }

        if (target === null || target.length === 0) {
            alert("업로드할 파일을 선택해주세요.");
            return false;
        }

        if (!/(.*?)\.(xlsx|XLSX)$/i.test(target[0].name)) {
            alert("Excel파일만 업로드해주세요.(xlsx)");
            return false;
        }

        return true;
    };

    $('#excel_file').change(function(e){
        if (validateFile() === false) {
            $('.custom-file-label').html("");
            return;
        }
        $('.custom-file-label').html(this.files[0].name);
    });

    /**
     * 아래부터 엑셀 관련 함수들
     * - read the raw data and convert it to a XLSX workbook
     */
    const convertDataToWorkbook = (data) => {
		/* convert data to binary string */
		data = new Uint8Array(data);
		const arr = new Array();

		for (let i = 0; i !== data.length; ++i) {
			arr[i] = String.fromCharCode(data[i]);
		}

		const bstr = arr.join("");

		return XLSX.read(bstr, {type: "binary"});
	};

	const makeRequest = (method, url, success, error) => {
		var httpRequest = new XMLHttpRequest();
		httpRequest.open("GET", url, true);
		httpRequest.responseType = "arraybuffer";

		httpRequest.open(method, url);
		httpRequest.onload = () => {
			success(httpRequest.response);
		};
		httpRequest.onerror = () => {
			error(httpRequest.response);
		};
		httpRequest.send();
	};

	const populateGrid = async (workbook) => {

		var firstSheetName = workbook.SheetNames[0]; // our data is in the first sheet
		var worksheet = workbook.Sheets[firstSheetName];

		var columns	= {
			'A': 'goods_no',
			'B': 'style_no',
			'C': 'opt_kor',
            'D': 'qty',
            'E': 'unit_cost',
		};

		var rowIndex = 5; // 엑셀 5번째 줄부터 시작 (샘플데이터 참고)

        alert('상품을 순차적으로 불러오고 스타일 넘버를 검사합니다. \n다소 시간이 소요될 수 있습니다.'); // progress
        let count = gx.gridOptions.api.getDisplayedRowCount();
		while (worksheet['A' + rowIndex]) { // iterate over the worksheet pulling out the columns we're expecting
			let row = {};
			Object.keys(columns).forEach((column) => {
				if(worksheet[column + rowIndex] !== undefined) {
					row[columns[column]] = worksheet[column + rowIndex].w;
				}
			});
            
            row.qty = row.qty ? row.qty : 0; // 수량
            row.style_no = row.style_no ? row.style_no : "NONE";
            row.unit_cost = row.unit_cost ? row.unit_cost : 0;  // 단가
            row.unit_total_cost = row.unit_total_cost ? row.unit_total_cost : 0;  // 금액
            row.opt_kor = row.opt_kor ? row.opt_kor : "";
            row = { ...row, 
                count: ++count, item: "", cost: 0, total_cost: 0, total_cost_novat: 0, isEditable: true, goods_no: "검사중..."
            };

            gx.gridOptions.api.applyTransaction({add : [row]}); // 한 줄씩 import
            
            rowIndex++;
            await getGood(row);

            const ff = document.search;
            const [ unit, exchange_rate, custom_tax_rate ] = [ ff.currency_unit.value, unComma(ff.exchange_rate.value), ff.custom_tax_rate.value ];
            await calProduct(row, unit, exchange_rate, custom_tax_rate);
		}
	};

	const importExcel = async (url) => {
		await makeRequest('GET',
			//'https://www.ag-grid.com/example-excel-import/OlymicData.xlsx',
			url,
			// success
			async (data) => {
				const workbook = convertDataToWorkbook(data);
				await populateGrid(workbook);
			},
			// error
			(error) => {
                console.log(error);
			}
		);
	};

    const getGood = async (row) => {
        const CMD = 'getgood';
        axios({
            cmd: CMD,
            url: COMMAND_URL,
            method: 'post',
            data: { cmd: CMD, style_no: row.style_no }
        }).then(async (response) => {
            
            const code = response.data.code; // 0: 상품없음, -1: 상품중복 또는 입점상품, 1: 존재하는 상품
            let good, message, checked_row;
            
            if (response.data.code == 1) {
                good = response.data.good;                
                checked_row = {...row, ...good};
            } else {
                message = response.data.message;
                checked_row = {...row, goods_no: message};
            }

            await gx.gridOptions.api.applyTransaction({update : [checked_row]});

        }).catch((error) => {
            console.log(error);
        });
    };

	const upload = () => {
		const file_data = $('#excel_file').prop('files')[0];
		const form_data = new FormData();
        form_data.append('cmd', 'import');
		form_data.append('file', file_data);
		form_data.append('_token', "{{ csrf_token() }}");

        axios({
            method: 'post',
            url: COMMAND_URL,
            data: form_data,
            headers: {
                "Content-Type": "multipart/form-data",
            }
        }).then(async (response) => {
            if (response.data.code == 1) {
                const file = response.data.file;
                await importExcel("/" + file);
                updatePinnedRow();
            } else {
                console.log(response.data.message);
            }
        }).catch((error) => {
            console.log(error);
        });
        
		return false;
	};


    var displayHelp = () => {
        const h = document.getElementById("help");
        if (h.style.display == 'none') {
            h.style.display = '';
        } else {
            h.style.display = 'none'
        };
    };

    const _ = (selector) => {
        const result = document.querySelectorAll(selector);
        if (result == undefined) return result;
        return result.length > 1 ? result : result[0];
    };

    const changeUnit = (select) => {

        const ff = document.search;

        if (select.value == "KRW") {
            _("#exchange_rate").readOnly = true;
            _("#custom_tax").readOnly = true;

            _("#exchange_rate").disabled = true;
            _("#custom_tax").disabled = true;
        } else {
            _("#exchange_rate").readOnly = false;
            _("#custom_tax").readOnly = false;

            _("#exchange_rate").disabled = false;
            _("#custom_tax").disabled = false;

            ff.exchange_rate.focus();
        }

        gx.gridOptions.api.redrawRows();

    };

</script>

@stop
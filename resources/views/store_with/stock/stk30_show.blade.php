@extends('store_with.layouts.layout-nav')
@php
    $title = "창고반품등록";
    if($cmd == "update") $title = "창고반품관리";
@endphp
@section('title', $title)
@section('content')
<div class="show_layout py-3 px-sm-3">
    <div class="page_tit d-flex justify-content-between">
        <div class="d-flex">
            <h3 class="d-inline-flex">{{ $title }}</h3>
            <div class="d-inline-flex location">
                <span class="home"></span>
                <span>/ 매장관리</span>
                <span>/ 창고반품</span>
            </div>
        </div>
        <div class="d-flex">
            @if(@$cmd == 'add')
                <a href="javascript:void(0)" onclick="Save('{{ @$cmd }}')" class="btn btn-primary mr-1"><i class="fas fa-save fa-sm text-white-50 mr-1"></i> 반품요청</a>
            @elseif (@$cmd == 'update')
                @if (@$sr_state == 10)
                    <a href="javascript:void(0)" onclick="Save('{{ @$cmd }}')" class="btn btn-primary mr-1"><i class="fas fa-save fa-sm text-white-50 mr-1"></i> 처리중 </a>
                    <a href="javascript:void(0)" onclick="ChangeState()" class="btn btn-primary mr-1"><i class="fas fa-save fa-sm text-white-50 mr-1"></i> 이동중</a>
                @endif
            @endif
            <a href="javascript:void(0)" onclick="window.close();" class="btn btn-outline-primary"><i class="fas fa-times fa-sm mr-1"></i> 닫기</a>
        </div>
    </div>

    <style> 
        .table th {min-width: 120px;}
        .table td {width: 25%;}
        
        @media (max-width: 740px) {
            .table td {float: unset !important;width: 100% !important;}
        }
    </style>

    <div class="card_wrap aco_card_wrap">
        <div class="card shadow">
            <div class="card-header d-flex justify-content-between align-items-left align-items-sm-center flex-column flex-sm-row mb-0">
                <a href="#">기본정보</a>
            </div>
            <div class="card-body">
                <form name="f1">
                    <div class="row">
                        <div class="col-12">
                            <div class="table-box-ty2 mobile">
                                <table class="table incont table-bordered" width="100%" cellspacing="0">
                                    <tbody>
                                        <tr>
                                            <th class="required">반품일자</th>
                                            <td>
                                                <div class="form-inline">
                                                    @if(@$cmd == 'add')
                                                    <div class="docs-datepicker form-inline-inner input_box w-100">
                                                        <div class="input-group">
                                                            <input type="text" class="form-control form-control-sm docs-date" name="sdate" value="{{ @$sdate }}" autocomplete="off">
                                                            <div class="input-group-append">
                                                                <button type="button" class="btn btn-outline-secondary docs-datepicker-trigger p-0 pl-2 pr-2">
                                                                    <i class="fa fa-calendar" aria-hidden="true"></i>
                                                                </button>
                                                            </div>
                                                        </div>
                                                        <div class="docs-datepicker-container"></div>
                                                    </div>
                                                    @else
                                                    <p class="fs-14">{{ $sr->sr_date }}</p>
                                                    @endif
                                                </div>
                                            </td>
                                            <th class="required">반품창고</th>
                                            <td>
                                                <div class="form-inline">
                                                    <select name='storage_cd' class="form-control form-control-sm w-100" @if(@$cmd == 'update') disabled @endif>
                                                        @foreach (@$storages as $storage)
                                                            <option value='{{ $storage->storage_cd }}' @if(@$cmd == 'update' && $sr->storage_cd == $storage->storage_cd) selected @endif>{{ $storage->storage_nm }}</option>
                                                        @endforeach
                                                        <input type="hidden" id="storage" value="{{ @$sr->storage_cd }}" class="form-control form-control-sm w-100" readonly />
                                                    </select>
                                                </div>
                                            </td>
                                            <th>반품코드</th>
                                            <td>
                                                <div class="form-inline">
                                                    <p id="sr_cd" class="fs-14">@if(@$sr != null) {{ @$sr->sr_cd }} @else {{ @$new_sr_cd }} @endif</p>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th class="required">매장</th>
                                            <td>
                                                <div class="form-inline inline_select_box">
                                                    @if(@$cmd == 'add')
                                                    <div class="form-inline-inner input-box w-100">
                                                        <div class="form-inline inline_btn_box">
                                                            <input type='hidden' id="store_nm" name="store_nm">
                                                            <select id="store_no" name="store_no" class="form-control form-control-sm select2-store"></select>
                                                            <a href="javascript:void(0);" class="btn btn-sm btn-outline-primary sch-store"><i class="bx bx-dots-horizontal-rounded fs-16"></i></a>
                                                        </div>
                                                    </div>
                                                    @else
                                                    <input type="text" name="store_nm" id="store_nm" value="{{ @$sr->store_nm }}" class="form-control form-control-sm w-100" readonly />
                                                    <input type="hidden" name="store_cd" id="store_cd" value="{{ @$sr->store_cd }}" class="form-control form-control-sm w-100" readonly />
                                                    @endif
                                                </div>
                                            </td>
                                            <th class="required">반품사유</th>
                                            <td>
                                                <div class="form-inline">
                                                    <select name='sr_reason' id="sr_reason" class="form-control form-control-sm w-100">
                                                        @foreach ($sr_reasons as $sr_reason)
                                                        <option value='{{ $sr_reason->code_id }}' @if(@$cmd == 'update' && $sr->sr_reason == $sr_reason->code_id) selected @endif>{{ $sr_reason->code_val }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </td>
                                            <th>메모</th>
                                            <td>
                                                <div class="form-inline">
                                                    <textarea name="comment" id="comment" class="w-100" rows="2">{{ @$sr->comment }}</textarea>
                                                </div>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <div class="card shadow mt-3">
            <div class="card-header d-flex justify-content-between align-items-left align-items-sm-center flex-column flex-sm-row mb-0">
                <a href="#">상품정보</a>
                <div class="d-flex align-items-center">
                    @if(@$cmd == 'add' || @$sr->sr_state == '10')
                    <div class="d-flex">
                        <button type="button" onclick="return setAllQty(false);" class="btn btn-sm btn-outline-primary shadow-sm mr-1" id="add_row_btn">전체재고반품처리</button>
                        <button type="button" onclick="return setAllQty(true);" class="btn btn-sm btn-outline-primary shadow-sm mr-1" id="add_row_btn">반품재고초기화</button>
                    </div>
                    @endif
                    <!-- @if(@$sr->sr_state == '10')
                        <button type="button" onclick="return resetReturnQty(false);" class="btn btn-sm btn-outline-primary shadow-sm mr-1" id="add_row_btn">매장반품수량으로 초기화</button>
                    @endif -->
                    @if(@$cmd == 'add')
                    <span class="ml-1 mr-2">|</span>
                    <div class="d-flex">
                        <button type="button" onclick="return addGoods();" class="btn btn-sm btn-primary shadow-sm mr-1" id="add_row_btn"><i class="bx bx-plus"></i> 상품추가</button>
                        <button type="button" onclick="return delGoods();" class="btn btn-sm btn-outline-primary shadow-sm mr-1" id="add_row_btn"><i class="bx bx-trash"></i> 삭제</button>
                    </div>
                    @endif
                  
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive mt-2">
                    <div id="div-gd" class="ag-theme-balham"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<script language="javascript">
    const cmd = '{{ @$cmd }}';
    const now_state = '{{ @$sr->sr_state }}';
    const pinnedRowData = [{ prd_cd: '합계', qty: 0, total_return_price: 0 , fixed_return_qty: 0, fixed_return_price : 0}];

    let columns = [
        {headerName: "No", pinned: "left", valueGetter: "node.id", cellRenderer: "loadingRenderer", width: 40, cellStyle: {"text-align": "center"},
            cellRenderer: params => params.node.rowPinned == 'top' ? '' : params.data.count,
            sortingOrder: ['desc', 'asc', 'null'],
            comparator: (valueA, valueB, nodeA, nodeB, isInverted) => {
                if (parseInt(valueA) == parseInt(valueB)) return 0;
                return (parseInt(valueA) > parseInt(valueB)) ? 1 : -1;
            },
        },
        {field: "chk", headerName: '', pinned: 'left', cellClass: 'hd-grid-code', checkboxSelection: true, headerCheckboxSelection: true, sort: null, width: 29},
        {field: "prd_cd", headerName: "바코드", pinned: 'left', width: 120, cellStyle: {"text-align": "center"}},
        {field: "goods_no", headerName: "온라인코드", width: 70, cellStyle: {"text-align": "center"}},
        {field: "opt_kind_nm", headerName: "품목", width: 70, cellStyle: {"text-align": "center"}},
        {field: "brand", headerName: "브랜드", width: 80, cellStyle: {"text-align": "center"}},
        {field: "style_no",	headerName: "스타일넘버", width: 70, cellStyle: {"text-align": "center"}},
        {field: "goods_nm",	headerName: "상품명", type: 'HeadGoodsNameType', width: 180},
        {field: "goods_nm_eng",	headerName: "상품명(영문)", width: 180},
        {field: "prd_cd_p",	headerName: "품번", width: 90, cellStyle: {"text-align": "center"}},
        {field: "color", headerName: "컬러", width: 55, cellStyle: {"text-align": "center"}},
        {field: "size",	headerName: "사이즈", width: 55, cellStyle: {"text-align": "center"}},
        {field: "goods_opt", headerName: "옵션", width: 130},
        {field: "goods_sh", headerName: "TAG가", type: "currencyType", width: 65},
        {field: "price", headerName: "판매가", type: "currencyType", width: 65},
        {field: "return_price", headerName: "반품단가", width: 70, type: 'currencyType',
            editable: (params) => checkIsEditable(params),
            cellStyle: (params) => checkIsEditable(params) ? {"background-color": "#ffff99"} : {}
        },
        {field: "store_wqty", headerName: "매장보유재고", width: 90, type: 'currencyType',
            cellStyle: (params) => params.data.store_wqty != 0 ? {"color" : "red"} : {}
        },
        {field: "qty", headerName: "반품수량", width: 60, type: 'currencyType', 
            editable: (params) => checkIsEditable(params),
            cellStyle: (params) => checkIsEditable(params) ? {"background-color": "#ffff99"} : {}
        },
        {field: "total_return_price", headerName: "반품금액", width: 80, type: 'currencyType'},
        @if (@$sr_state == 30 || @$sr_state == 40)
        {field: "fixed_return_qty", headerName: "확정수량", width: 60, type: 'currencyType',
            editable: (params) => checkIsEditable(params),
            cellStyle: (params) => checkIsEditable(params) ? {"background-color": "#ffff99"} : {}
        },
        {field: "fixed_return_price", headerName: "확정금액", width: 80, type: 'currencyType'},
        {field: "fixed_comment", headerName: "확정메모", width: 300, 
            editable: (params) => checkIsEditable(params),
            cellStyle: (params) => checkIsEditable(params) ? {"background-color": "#ffff99"} : {}
        
        }
        @endif
    ];
</script>

<script type="text/javascript" charset="utf-8">
    let gx;
    const pApp = new App('', { gridId: "#div-gd" });

    $(document).ready(function() {
        pApp.ResizeGrid(275, 470);
        pApp.BindSearchEnter();
        let gridDiv = document.querySelector(pApp.options.gridId);
        gx = new HDGrid(gridDiv, columns, {
            pinnedTopRowData: pinnedRowData,
            getRowStyle: (params) => { // 고정된 row styling
                if (params.node.rowPinned)  return { 'font-weight': 'bold', 'background': '#eee', 'border': 'none'};
            },
            getRowNodeId: (data) => data.hasOwnProperty('count') ? data.count : "0", // 업데이터 및 제거를 위한 식별 ID를 count로 할당
            onCellValueChanged: (e) => {
                if (e.column.colId === "return_price" || e.column.colId === "qty" || e.column.colId === "fixed_return_qty") {
                    if (isNaN(e.newValue) == true || e.newValue == "") {
                        alert("숫자만 입력가능합니다.");
                        gx.gridOptions.api.startEditingCell({ rowIndex: e.rowIndex, colKey: e.column.colId });
                    } else if(e.newValue < 0) {
                        alert("음수는 입력할 수 없습니다.");
                        gx.gridOptions.api.startEditingCell({ rowIndex: e.rowIndex, colKey: e.column.colId });
                    } else {
                        if(e.column.colId === "qty" && e.data.store_wqty < parseInt(e.data.qty)) {
                            alert("해당 매장의 보유재고보다 많은 수량을 반품할 수 없습니다.");
                            gx.gridOptions.api.startEditingCell({ rowIndex: e.rowIndex, colKey: e.column.colId });
                        } else {
                            e.node.setSelected(true);
                            e.data.total_return_price = parseInt(e.data.qty) * parseInt(e.data.return_price);
                            // e.data.fixed_return_price = parseInt(e.data.fixed_return_qty) * parseInt(e.data.return_price);
                            gx.gridOptions.api.updateRowData({update: [e.data]});
                            updatePinnedRow();
                        }
                        if (e.column.colId === "fixed_return_qty" && e.data.store_wqty < parseInt(e.data.fixed_return_qty)) {
                            alert("해당 매장의 보유재고보다 많은 수량을 반품할 수 없습니다.");
                            gx.gridOptions.api.startEditingCell({ rowIndex: e.rowIndex, colKey: e.column.colId });
                        } else {
                            e.node.setSelected(true);
                            e.data.fixed_return_price = parseInt(e.data.fixed_return_qty) * parseInt(e.data.return_price);
                            gx.gridOptions.api.updateRowData({update: [e.data]});
                            updatePinnedRow();
                        }
                    }
                }
            }
        });
        if('{{ @$cmd }}' === 'update') GetProducts();

        $("#store_no").on("change", function(e) {
            gx.gridOptions.api.setRowData([]);
            updatePinnedRow();
        });
    });

    // 등록된 상품리스트 가져오기
    function GetProducts() {
        let data = "sr_cd=" + '{{ @$sr->sr_cd }}';
        gx.Request('/store/stock/stk30/search-return-products', data, -1, function(e) {
            updatePinnedRow();
        });
    }

    // 상품반품 등록
    function Save(cmd) {
        if(!cmd) return;

        let sr_reason = document.f1.sr_reason.value;
        let comment = document.f1.comment.value;
        let rows = gx.getSelectedRows();

        if(cmd === 'add') {
            let sr_date = document.f1.sdate.value;
            let storage_cd = document.f1.storage_cd.value;
            let store_cd = document.f1.store_no.value;

            if(store_cd === '') {
                $(".sch-store").click();
                return alert("매장을 선택해주세요.");
            }
            if(rows.length < 1) return alert("반품등록할 상품을 선택해주세요.");

            // let zero_qtys = rows.filter(r => r.qty < 1);
            // if(zero_qtys.length > 0) return alert("반품수량이 0개인 항목이 존재합니다.");

            let excess_qtys = rows.filter(r => (r.qty * 1) > (r.store_wqty * 1));
            if(excess_qtys.length > 0) return alert("해당 매장의 보유재고보다 많은 수량을 반품할 수 없습니다.");

            if(!confirm("등록하시겠습니까?")) return;

            axios({
                url: '/store/stock/stk30/add-store-return',
                method: 'put',
                data: {
                    sr_date,
                    storage_cd,
                    store_cd,
                    sr_reason,
                    comment,
                    products: rows.map(r => ({ prd_cd: r.prd_cd, price: r.price, return_price: r.return_price, return_qty: r.qty })),
                },
            }).then(function (res) {
                if(res.data.code === 200) {
                    alert(res.data.msg);
                    opener.Search();
                    window.close();
                } else {
                    console.log(res.data);
                    alert("저장 중 오류가 발생했습니다.\n관리자에게 문의해주세요.");
                }
            }).catch(function (err) {
                console.log(err);
            });
        } else if(cmd === 'update') {
            let sr_state = '{{ @$sr->sr_state }}';
            let sr_cd = '{{ @$sr->sr_cd }}';

            if('{{ @$sr->sr_state }}' != 10) return alert("창고반품이 '요청'상태일떄만 수정가능합니다.");
            if(!confirm("수정하시겠습니까?")) return;

            axios({
                url: '/store/stock/stk30/update-store-return',
                method: 'put',
                data: {
                    sr_cd,
                    sr_reason,
                    comment,
                    products: rows.map(r => ({ sr_prd_cd: r.sr_prd_cd, return_price: r.return_price, return_qty: r.qty, fixed_return_price:r.fixed_return_price, fixed_return_qty:r.fixed_return_qty, fixed_comment:r.fixed_comment})),
                },
            }).then(function (res) {
                if(res.data.code === 200) {
                    alert(res.data.msg);
                    opener.Search();
                    window.close();
                } else {
                    console.log(res.data);
                    alert("수정 중 오류가 발생했습니다.\n관리자에게 문의해주세요.");
                }
            }).catch(function (err) {
                console.log(err);
            });
        }
    }

    const checkIsEditable = (params) => {
        return (cmd == 'add' || now_state == '10') && params.data.hasOwnProperty('isEditable') && params.data.isEditable ? true : false;
    };

    // 상품 삭제
    const deleteRow = (row) => { gx.gridOptions.api.applyTransaction({remove : [row]}); };

    const delGoods = () => {
        const ff = document.f1;
        const rows = gx.getSelectedRows();
        if (Array.isArray(rows) && !(rows.length > 0)) return alert("삭제할 상품을 선택해주세요.");

        rows.filter((row, idx) => row.isEditable).map((row) => { deleteRow(row); });
        updatePinnedRow();
    };

    /***************************************************************************/
    /******************************** 상품 추가 관련 ****************************/
    /***************************************************************************/

    // 상품 추가
    function addGoods() {
        const ff = document.f1;
        if (ff.store_no.value == '') {
            $(".sch-store").click();
            return alert('매장을 선택해주세요.');
        }

        const url = `/store/api/goods/show?store_cd=` + ff.store_no.value;
        window.open(url, "_blank","toolbar=no,scrollbars=yes,resizable=yes,status=yes,top=100,left=100,width=1800,height=1000");
    }

    /**
     * goods api logics - 상품 가져오기
     * window opener에서 콜백을 사용하려면 var로 선언해야 합니다.
     */

    let callbaackRows = [];

    var goodsCallback = (row) => {
        addRow(row);
        setStoreQty();
    };
    
    var multiGoodsCallback = (rows) => {
        if (rows && Array.isArray(rows)) rows.map(row => addRow(row));
        setStoreQty();
    };

    var addRow = (row) => { // goods_api에서 opener 함수로 사용하기 위해 var로 선언
        const count = gx.gridOptions.api.getDisplayedRowCount() + callbaackRows.length;
        row = { 
            ...row, 
            item: row.opt_kind_nm, 
            qty: 0, 
            return_price: row.price,
            // qty: row.store_wqty > 0 ? 1 : 0, 
            // total_return_price: row.price * (row.store_wqty > 0 ? 1 : 0),
            total_return_price: 0,
            fixed_return_qty : 0,
            fixed_return_price: 0,
            isEditable: true,
            count: count + 1,
        };
        callbaackRows.push(row);
    };
    
    var setStoreQty = () => {
        gx.gridOptions.api.applyTransaction({ add : callbaackRows });
        callbaackRows = [];
        updatePinnedRow();
    }

    const updatePinnedRow = () => { // 총 반품금액, 반품수량을 반영한 PinnedRow를 업데이트
        let [ qty, total_return_price, fixed_return_qty, fixed_return_price ] = [ 0, 0, 0, 0 ];
        const rows = gx.getRows();
        if (rows && Array.isArray(rows) && rows.length > 0) {
            rows.forEach((row, idx) => {
                qty += parseFloat(row.qty);
                total_return_price += parseFloat(row.total_return_price);
                fixed_return_qty += parseFloat(row.fixed_return_qty);
                fixed_return_price += parseFloat(row.fixed_return_price);
            });
        }

        let pinnedRow = gx.gridOptions.api.getPinnedTopRow(0);
        gx.gridOptions.api.setPinnedTopRowData([
            { ...pinnedRow.data, qty: qty, total_return_price: total_return_price, fixed_return_price, fixed_return_qty}
        ]);
    };

    const setAllQty = (is_zero = false) => {
        const rows = gx.getRows().map(row => ({
            ...row, 
            qty: is_zero ? 0 : row.store_wqty, 
            total_return_price: is_zero ? 0 : parseInt(row.return_price) * parseInt(row.store_wqty),
        }));
        gx.gridOptions.api.applyTransaction({ update : rows });
        gx.gridOptions.api.forEachNode(node => node.setSelected(!is_zero)); 
        updatePinnedRow();
    }

    const resetReturnQty = (is_return = false) => {
        const rows = gx.getRows().map(row => ({
            ...row,
            fixed_return_qty: is_return ? 0 : row.qty,
            fixed_return_price: is_return ? 0 : row.total_return_price
        }));
        gx.gridOptions.api.applyTransaction({update : rows});
        gx.gridOptions.api.forEachNode(node => node.setSelected(!is_return)); 
        updatePinnedRow();
    }

    function ChangeState() {
        let rows = gx.getSelectedRows();
        if(rows.length < 1) return alert("상태변경할 항목을 선택해주세요.");

        for (let row of rows) {
            if(row.qty == 0){
               return alert("반품수량을 입력해주세요.");
            }
        }
        let sr_cd = '{{ @$sr->sr_cd }}';
        let store_cd = $('#store_cd').val();
        let storage_cd = $('#storage').val();
        let sr_reason = $('#sr_reason').val();
        let chg_state = 30;

        if(!confirm("선택한 항목의 반품상태를 변경하시겠습니까?")) return;

        axios({
            url: '/store/stock/stk30/update-return-state',
            method: 'put',
            data: {
                data: rows,
                sr_cd : sr_cd,
                sr_reason : sr_reason,
                new_state: chg_state,
                store_cd : store_cd,
                storage_cd : storage_cd
            },
        }).then(function (res) {
            if(res.data.code === 200) {
                alert(res.data.msg);
                window.close();
                opener.Search();
            } else {
                console.log(res.data);
                alert("상태변경 중 오류가 발생했습니다.\n관리자에게 문의해주세요.");
            }
        }).catch(function (err) {
            console.log(err);
        });
    }

    function ChangeState2() {
        let rows = gx.getSelectedRows();
        if(rows.length < 1) return alert("상태변경할 항목을 선택해주세요.");

        let store_cd = $('#store_cd').val();
        let storage_cd = $('#storage').val();
        let chg_state = 40;

        if(!confirm("선택한 항목의 반품상태를 변경하시겠습니까?")) return;

        axios({
            url: '/store/stock/stk30/update-state',
            method: 'put',
            data: {
                data: rows,
                new_state: chg_state,
                store_cd : store_cd,
                storage_cd : storage_cd
            },
        }).then(function (res) {
            if(res.data.code === 200) {
                alert(res.data.msg);
                window.close();
                opener.Search();
            } else {
                console.log(res.data);
                alert("상태변경 중 오류가 발생했습니다.\n관리자에게 문의해주세요.");
            }
        }).catch(function (err) {
            console.log(err);
        });
    }
</script>
@stop

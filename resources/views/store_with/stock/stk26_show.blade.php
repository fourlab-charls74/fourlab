@extends('store_with.layouts.layout-nav')
@php
    $title = "매장실사등록";
    if($cmd == "update") $title = "매장실사관리";
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
                <span>/ 실사</span>
            </div>
        </div>
        <div class="d-flex">
            @if(@$cmd == 'add' or @$sc->sc_state == 'N')
            <a href="javascript:void(0)" onclick="Save('{{ @$cmd }}')" class="btn btn-primary mr-1"><i class="fas fa-save fa-sm text-white-50 mr-1"></i> 저장</a>
            @endif
            @if(@$cmd != 'add' and @$sc->sc_state == 'N')
            <a href="javascript:void(0)" onclick="DelStockCheck('{{ @$sc->sc_cd }}')" class="btn btn-primary mr-1"><i class="fas fa-trash fa-sm text-white-50 mr-1"></i> 삭제</a>
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
                                            <th class="required">실사일자</th>
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
                                                    <p class="fs-14">{{ $sc->sc_date }}</p>
                                                    @endif
                                                </div>
                                            </td>
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
                                                    <input type="text" name="store_nm" id="store_nm" value="{{ @$sc->store_nm }}" class="form-control form-control-sm w-100" readonly />
                                                    @endif
                                                </div>
                                            </td>
                                            <th>실사코드</th>
                                            <td>
                                                <div class="form-inline">
                                                    <p id="sc_cd" class="fs-14">@if(@$sc != null) {{ @$sc->sc_cd }} ({{ @$sc->sc_type == 'G' ? '일반등록' : (@$sc->sc_type == 'B' ? '일괄등록' : '-') }}) @else {{ @$new_sc_cd }} @endif</p>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th class="required">담당자</th>
                                            <td>
                                                <div class="form-inline">
                                                    @if(@$cmd == 'add')
                                                    <div class="form-inline inline_btn_box w-100">
                                                        <input type="hidden" id="md_id" name="md_id">
                                                        <input type="text" id="md_nm" name="md_nm" class="form-control form-control-sm w-100 bg-white sch-md" readonly>
                                                        <a href="javascript:void(0);" class="btn btn-sm btn-outline-primary sch-md"><i class="bx bx-dots-horizontal-rounded fs-16"></i></a>
                                                    </div>
                                                    @else
                                                    <p class="fs-14">{{ $sc->md_nm }}</p>
                                                    @endif
                                                </div>
                                            </td>
                                            <th>메모</th>
                                            <td colspan="3">
                                                <div class="form-inline">
                                                    <textarea name="comment" id="comment" class="w-100" rows="2">{{ @$sc->comment }}</textarea>
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
                @if(@$cmd == 'add')
                <div class="d-flex">
                    <button type="button" onclick="addGoods();" class="btn btn-sm btn-primary shadow-sm mr-1" id="add_row_btn"><i class="bx bx-plus"></i> 상품추가</button>
                    <button type="button" onclick="delGoods();" class="btn btn-sm btn-outline-primary shadow-sm mr-1" id="add_row_btn"><i class="bx bx-trash"></i> 삭제</button>
                </div>
                @endif
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
    const now_state = '{{ @$sc->sc_state }}';
    const pinnedRowData = [{ prd_cd: '합계', store_wqty: 0, qty: 0, loss_qty: 0, loss_price: 0 }];

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
        {field: "prd_cd", headerName: "상품코드", pinned: 'left', width: 120, cellStyle: {"text-align": "center"}},
        {field: "goods_no", headerName: "상품번호", width: 70, cellStyle: {"text-align": "center"}},
        {field: "opt_kind_nm", headerName: "품목", width: 70, cellStyle: {"text-align": "center"}},
        {field: "brand", headerName: "브랜드", width: 70, cellStyle: {"text-align": "center"}},
        {field: "style_no",	headerName: "스타일넘버", width: 70, cellStyle: {"text-align": "center"}},
        {field: "goods_nm",	headerName: "상품명", width: 200,
            cellRenderer: (params) => {
                if (params.data.goods_no === undefined) return '';
                if (params.data.goods_no != '0') {
                    return '<a href="javascript:void(0);" onclick="return openHeadProduct(\'' + params.data.goods_no + '\');">' + params.value + '</a>';
                } else {
                    return '<a href="javascript:void(0);" onclick="return alert(`상품번호가 없는 상품입니다.`);">' + params.value + '</a>';
                }
            }   
        },
        {field: "goods_nm_eng",	headerName: "상품명(영문)", width: 200},
        {field: "prd_cd_p", headerName: "코드일련", width: 90, cellStyle: {"text-align": "center"}},
        {field: "color", headerName: "컬러", width: 50, cellStyle: {"text-align": "center"}},
        {field: "size", headerName: "사이즈", width: 50, cellStyle: {"text-align": "center"}},
        {field: "goods_opt", headerName: "옵션", width: 130},
        {field: "goods_sh", headerName: "TAG가", type: "currencyType", width: 70},
        {field: "price", headerName: "판매가", type: "currencyType", width: 70},
        {field: "store_wqty", headerName: "매장보유재고", width: 100, type: 'currencyType'},
        {field: "qty", headerName: "실사재고", width: 60, type: 'currencyType', 
            editable: (params) => checkIsEditable(params),
            cellStyle: (params) => checkIsEditable(params) ? {"background-color": "#ffff99"} : {}
        },
        {field: "loss_qty", headerName: "LOSS수량", width: 80, type: 'currencyType'},
        {field: "loss_price", headerName: "LOSS금액", width: 80, type: 'currencyType'}
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
                if (e.column.colId === "qty") {
                    if (isNaN(e.newValue) == true || e.newValue == "") {
                        alert("숫자만 입력가능합니다.");
                        gx.gridOptions.api.startEditingCell({ rowIndex: e.rowIndex, colKey: e.column.colId });
                    } else if(e.newValue < 0) {
                        alert("음수는 입력할 수 없습니다.");
                        gx.gridOptions.api.startEditingCell({ rowIndex: e.rowIndex, colKey: e.column.colId });
                    } else {
                        e.data.loss_qty = parseInt(e.data.store_wqty) - parseInt(e.data.qty);
                        e.data.loss_price = parseInt(e.data.price) * parseInt(e.data.loss_qty);
                        gx.gridOptions.api.updateRowData({update: [e.data]});
                        updatePinnedRow();
                    }
                }
            }
        });
        if(cmd === 'update') GetProducts();

        $("#store_no").on("change", function(e) {
            gx.gridOptions.api.setRowData([]);
            updatePinnedRow();
        });
    });

    // 등록된 상품리스트 가져오기
    function GetProducts() {
        let data = "sc_cd=" + '{{ @$sc->sc_cd }}';
        gx.Request('/store/stock/stk26/search-check-products', data, -1, function(e) {
            updatePinnedRow();
        });
    }

    // 실사 등록
    function Save(cmd) {
        if(!cmd) return;

        let comment = document.f1.comment.value;
        let rows = gx.getRows();

        if(cmd === 'add') {
            let sc_date = document.f1.sdate.value;
            let store_cd = document.f1.store_no.value;
            let md_id = document.f1.md_id.value;

            if(store_cd === '') {
                $(".sch-store").click();
                return alert("매장을 선택해주세요.");
            }
            if(rows.length < 1) return alert("실사등록할 상품을 선택해주세요.");
            if($("[name=md_id]").val() === '') return alert("담당자를 선택해주세요.");

            if(!confirm("등록하시겠습니까?")) return;

            axios({
                url: '/store/stock/stk26/save',
                method: 'put',
                data: {
                    sc_type: "G",
                    sc_date,
                    store_cd,
                    md_id,
                    comment,
                    products: rows.map(r => ({ prd_cd: r.prd_cd, price: r.price, qty: r.qty, store_qty: r.store_wqty })),
                },
            }).then(function (res) {
                if(res.data.code === '200') {
                    alert("실사등록이 성공적으로 완료되었습니다.");
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
            let sc_state = '{{ @$sc->sc_state }}';
            let sc_cd = '{{ @$sc->sc_cd }}';

            if(sc_state != 'N') return alert("매장LOSS등록 이전에만 수정가능합니다.");
            if(!confirm("수정하시겠습니까?")) return;

            axios({
                url: '/store/stock/stk26/update',
                method: 'put',
                data: {
                    sc_cd,
                    comment,
                    products: rows.map(r => ({ sc_prd_cd: r.sc_prd_cd, qty: r.qty })),
                },
            }).then(function (res) {
                if(res.data.code === '200') {
                    alert("실사정보 수정이 성공적으로 완료되었습니다.");
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
        return (cmd == 'add' || now_state == 'N') && params.data.hasOwnProperty('isEditable') && params.data.isEditable ? true : false;
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
            goods_type_nm: row.goods_type,
            qty: 0, 
            loss_qty: (row.store_wqty * 1),
            loss_price: (row.store_wqty * 1) * row.price,
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
        let [ store_wqty, qty, loss_qty, loss_price ] = [ 0, 0, 0, 0 ];
        const rows = gx.getRows();
        if (rows && Array.isArray(rows) && rows.length > 0) {
            rows.forEach((row, idx) => {
                store_wqty += parseInt(row.store_wqty || 0);
                qty += parseInt(row.qty || 0);
                loss_qty += parseInt(row.loss_qty || 0);
                loss_price += parseInt(row.loss_price || 0);
            });
        }

        let pinnedRow = gx.gridOptions.api.getPinnedTopRow(0);
        gx.gridOptions.api.setPinnedTopRowData([
            { ...pinnedRow.data, store_wqty: store_wqty, qty: qty, loss_qty: loss_qty, loss_price: loss_price }
        ]);
    };

    const DelStockCheck = (sc_cd) => {
        if (!sc_cd) return;
        if (!confirm("실사정보를 삭제하시겠습니까?\n삭제된 실사정보는 되돌릴 수 없습니다.")) return;

        axios({
            url: '/store/stock/stk26/' + sc_cd,
            method: 'delete',
        }).then(function (res) {
            if(res.data.code === '200') {
                alert("실사정보가 삭제되었습니다.");
                opener.Search();
                window.close();
            } else {
                console.log(res.data);
                alert(res.data.msg);
            }
        }).catch(function (err) {
            console.log(err);
        });
    }
</script>
@stop

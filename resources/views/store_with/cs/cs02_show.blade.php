@extends('store_with.layouts.layout-nav')
@section('title', '상품반품등록')
@section('content')
<div class="show_layout py-3 px-sm-3">
    <div class="page_tit d-flex justify-content-between">
        <div class="d-flex">
            <h3 class="d-inline-flex">상품반품등록</h3>
            <div class="d-inline-flex location">
                <span class="home"></span>
                <span>/ 생산입고관리</span>
                <span>/ 상품반품등록</span>
            </div>
        </div>
        <div class="d-flex">
            <a href="javascript:void(0)" onclick="Save()" class="btn btn-primary mr-1"><i class="fas fa-save fa-sm text-white-50 mr-1"></i> 저장</a>
            <a href="javascript:void(0)" onclick="window.close();" class="btn btn-outline-primary"><i class="fas fa-times fa-sm mr-1"></i> 닫기</a>
        </div>
    </div>

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
                                    <colgroup>
                                        <col width="9%">
                                        <col width="25%">
                                        <col width="9%">
                                        <col width="25%">
                                        <col width="9%">
                                        <col width="25%">
                                    </colgroup>
                                    <tbody>
                                        <tr>
                                            <th class="required">반품일자</th>
                                            <td>
                                                <div class="form-inline">
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
                                                </div>
                                            </td>
                                            <th class="required">공급처</th>
                                            <td>
                                                <div class="form-inline inline_select_box w-100">
                                                    <div class="form-inline-inner input-box w-75 pr-1">
                                                        <div class="form-inline inline_btn_box">
                                                            <input type="text" id="com_nm" name="com_nm" class="form-control form-control-sm ac-company sch-company">
                                                            <a href="#" class="btn btn-sm btn-outline-primary sch-company"><i class="bx bx-dots-horizontal-rounded fs-16"></i></a>
                                                        </div>
                                                    </div>
                                                    <div class="form-inline-inner input-box w-25 pl-1">
                                                        <input type="text" id="com_id" name="com_id" class="form-control form-control-sm" readonly />
                                                    </div>
                                                </div>
                                            </td>
                                            <th class="required">창고</th>
                                            <td>
                                                <div class="form-inline">
                                                    <select name='storage_cd' class="form-control form-control-sm w-100">
                                                        @foreach (@$storages as $storage)
                                                            <option value='{{ $storage->storage_cd }}'>{{ $storage->storage_nm }}</option>
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
                </form>
            </div>
        </div>
        <div class="card shadow mt-3">
            <div class="card-header d-flex justify-content-between align-items-left align-items-sm-center flex-column flex-sm-row mb-0">
                <a href="#">상품정보</a>
                <div class="d-flex">
                    <button type="button" onclick="addGoods();" class="btn btn-sm btn-primary shadow-sm mr-1" id="add_row_btn"><i class="bx bx-plus"></i> 상품추가</button>
                    <button type="button" onclick="delGoods();" class="btn btn-sm btn-outline-primary shadow-sm mr-1" id="add_row_btn"><i class="bx bx-trash"></i> 삭제</button>
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
    const pinnedRowData = [{ prd_cd: '합계', qty: 0, total_price: 0 }];

    let columns = [
        {headerName: "No", pinned: "left", valueGetter: "node.id", cellRenderer: "loadingRenderer", width: 37, cellStyle: {"text-align": "center"},
            cellRenderer: params => params.node.rowPinned == 'top' ? '' : params.data.count,
            sortingOrder: ['desc', 'asc', 'null'],
            comparator: (valueA, valueB, nodeA, nodeB, isInverted) => {
                if (parseInt(valueA) == parseInt(valueB)) return 0;
                return (parseInt(valueA) > parseInt(valueB)) ? 1 : -1;
            },
        },
        {field: "chk", headerName: '', pinned: 'left', cellClass: 'hd-grid-code', checkboxSelection: true, headerCheckboxSelection: true, sort: null, width: 28},
        {field: "prd_cd", headerName: "상품코드", pinned: 'left', width: 120, cellStyle: {"text-align": "center"}},
        {field: "goods_no", headerName: "상품번호", pinned: 'left', cellStyle: {"text-align": "center"}},
        {field: "goods_type", headerName: "상품구분", pinned: 'left', cellStyle: StyleGoodsType},
        {field: "opt_kind_nm", headerName: "품목", pinned: 'left', width: 100, cellStyle: {"text-align": "center"}},
        {field: "brand", headerName: "브랜드", pinned: 'left', width: 80, cellStyle: {"text-align": "center"}},
        {field: "style_no",	headerName: "스타일넘버", pinned: 'left', cellStyle: {"text-align": "center"}},
        {field: "sale_stat_cl", headerName: "상품상태", pinned: 'left', cellStyle: StyleGoodsState},
        {field: "goods_nm",	headerName: "상품명", pinned: 'left', type: 'HeadGoodsNameType', width: 230},
        {field: "goods_opt", headerName: "옵션", pinned: 'left', width: 230},
        {field: "price", headerName: "반품단가", width: 80, type: 'currencyType'},
        {field: "total_price", headerName: "반품금액", width: 80, type: 'currencyType'},
        {field: "qty", headerName: "반품수량", width: 60, type: 'currencyType', 
            editable: (params) => checkIsEditable(params),
            cellStyle: (params) => checkIsEditable(params) ? {"background-color": "#ffff99"} : {}
        },
        {field: "reason", headerName: "반품사유", width: 90, 
            editable: (params) => checkIsEditable(params),
            cellStyle: (params) => checkIsEditable(params) ? {"background-color": "#ffff99"} : {},
            cellEditorSelector: function(params) {
                return {
                    component: 'agRichSelectCellEditor',
                    params: { 
                        values: ['임의반품']
                    },
                };
            },
        },
        {field: "comment", headerName: "메모", width: 200, 
            editable: (params) => checkIsEditable(params),
            cellStyle: (params) => checkIsEditable(params) ? {"background-color": "#ffff99"} : {}
        },
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
                if (e.column.colId.includes("fee")) {
                    if (isNaN(e.newValue) == true || e.newValue == "") {
                        alert("숫자만 입력가능합니다.");
                        gx.gridOptions.api.startEditingCell({ rowIndex: e.rowIndex, colKey: e.column.colId });
                    }
                }
            }
        });
    });

    // 상품반품 등록
    function Save() {
        // let rows = gx.getRows();

        // // 시작일 검사
        // let wrong_sdates = rows.reduce((a,b) => {
        //     if(!a) return a;
        //     if(!a.sdate || !b.sdate) return false;
        //     if(a.use_yn !== "A") return b;
        //     if(new Date(a.sdate).getTime() - new Date(b.sdate).getTime() < (1000 * 60 * 60 * 24)) return false;
        //     return b;
        // });
        // if(wrong_sdates === false) return alert("새로 추가되는 항목의 시작일을 기존 항목의 시작일보다 이후의 일자로 입력해주세요.");

        // if(!confirm("해당 매장의 마진정보 변경내역을 저장하시겠습니까?")) return;

        // axios({
        //     url: '/store/standard/std07/update-store-fee',
        //     method: 'put',
        //     data: rows,
        // }).then(function (res) {
        //     if(res.data.code === 200) {
        //         alert(res.data.msg);
        //         opener.SearchDetail(store_cd, store_nm);
        //         Search();
        //     } else {
        //         console.log(res.data);
        //         alert("저장 중 오류가 발생했습니다.\n관리자에게 문의해주세요.");
        //     }
        // }).catch(function (err) {
        //     console.log(err);
        // });
    }

    const checkIsEditable = (params) => {
        return params.data.hasOwnProperty('isEditable') && params.data.isEditable ? true : false;
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
        if (ff.com_id.value == '') {
            ff.com_nm.click();
            return alert('공급처를 선택하여 주십시오.');
        }

        const url = `/store/api/goods/show`;
        window.open(url, "_blank","toolbar=no,scrollbars=yes,resizable=yes,status=yes,top=100,left=100,width=1800,height=1000");
    }

    /**
     * goods api logics - 상품 가져오기
     * window opener에서 콜백을 사용하려면 var로 선언해야 합니다.
     */

    var goodsCallback = (row) => {
        addRow(row);
        updatePinnedRow();
    };
    
    var multiGoodsCallback = (rows) => {
        if (rows && Array.isArray(rows)) rows.map(row => addRow(row));
        updatePinnedRow();
    };

    var addRow = (row) => { // goods_api에서 opener 함수로 사용하기 위해 var로 선언
        const count = gx.gridOptions.api.getDisplayedRowCount();
        row = { 
            ...row, 
            item: row.opt_kind_nm, 
            qty: 1, 
            total_price: row.price,
            isEditable: true,
            count: count + 1,
            reason: '임의반품',
        };
        gx.gridOptions.api.applyTransaction({ add : [row] });
    };

    const updatePinnedRow = () => { // 총 반품금액, 반품수량을 반영한 PinnedRow를 업데이트
        let [ qty, total_price ] = [ 0, 0 ];
        const rows = gx.getRows();
        if (rows && Array.isArray(rows) && rows.length > 0) {
            rows.forEach((row, idx) => {
                qty += parseFloat(row.qty);
                total_price += parseFloat(row.total_price);
            });
        }

        let pinnedRow = gx.gridOptions.api.getPinnedTopRow(0);
        gx.gridOptions.api.setPinnedTopRowData([
            { ...pinnedRow.data, qty: qty, total_price: total_price }
        ]);
    };
</script>
@stop

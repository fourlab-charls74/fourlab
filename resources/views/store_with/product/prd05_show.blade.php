@extends('store_with.layouts.layout-nav')
@section('title', '상품가격 변경')
@section('content')
<div class="show_layout py-3 px-sm-3">
    <div class="page_tit d-flex justify-content-between">
        <div class="d-flex">
            <h3 class="d-inline-flex">상품가격 변경</h3>
            <div class="d-inline-flex location">
                <span class="home"></span>
                <span>/ 상품가격 관리</span>
                <span>/ 상품관리</span>
            </div>
        </div>
        <div class="d-flex">
            <a href="javascript:void(0)" onclick="Save('{{ @$cmd }}')" class="btn btn-primary mr-1"><i class="fas fa-save fa-sm text-white-50 mr-1"></i> 저장</a>
            <a href="javascript:void(0)" onclick="window.close();" class="btn btn-outline-primary"><i class="fas fa-times fa-sm mr-1"></i> 닫기</a>
        </div>
    </div>

    <style> 
        .table th {min-width: 120px;}
        .table td {width: 50%;}
        
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
                                            <th class="required">변경일자</th>
                                            <td>
                                                <div class="form-inline">
                                                    <div class="docs-datepicker form-inline-inner input_box w-100">
                                                        <div class="input-group">
                                                            <input type="text" class="form-control form-control-sm docs-date" name="sdate" value="" autocomplete="off">
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
                                            <th class="required"></th>
                                            <td>
                                                <div class="form-inline inline_select_box">
                                                    <div class="d-flex w-100">
                                                        <!-- <select name="target_type" id="target_type" class="form-control form-control-sm mr-1" style="min-width: 90px;">
                                                            <option value="C">공급업체</option>
                                                            <option value="S">창고</option>
                                                        </select>
                                                        <select name="target_cd" id="target_cd" class="form-control form-control-sm w-100">
                                                        </select> -->
                                                    </div>
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
    let columns = [
        {headerName: "No", pinned: "left", valueGetter: "node.id", cellRenderer: "loadingRenderer", width: 40, cellStyle: {"text-align": "center"},
            cellRenderer: params => params.node.rowPinned == 'top' ? '' : params.data.count,
        },
        {field: "chk", headerName: '', pinned: 'left', cellClass: 'hd-grid-code', checkboxSelection: true, headerCheckboxSelection: true, sort: null, width: 29},
        {field: "prd_cd", headerName: "바코드", pinned: 'left', width: 120, cellStyle: {"text-align": "center"}},
        {field: "goods_no", headerName: "온라인코드", pinned: 'left', width: 70, cellStyle: {"text-align": "center"}},
        {field: "opt_kind_nm", headerName: "품목", width: 70, cellStyle: {"text-align": "center"}},
        {field: "brand", headerName: "브랜드", width: 70, cellStyle: {"text-align": "center"}},
        {field: "style_no",	headerName: "스타일넘버", width: 70, cellStyle: {"text-align": "center"}},
        {field: "goods_nm",	headerName: "상품명", type: 'HeadGoodsNameType', width: 200},
        {field: "goods_nm_eng",	headerName: "상품명(영문)", width: 200},
        {field: "prd_cd_p", headerName: "품번", width: 90, cellStyle: {"text-align": "center"}},
        {field: "color", headerName: "컬러", width: 55, cellStyle: {"text-align": "center"}},
        {field: "size", headerName: "사이즈", width: 55, cellStyle: {"text-align": "center"}},
        {field: "goods_opt", headerName: "옵션", width: 153},
        {field: "goods_sh", headerName: "TAG가", type: "currencyType", width: 65},
        {field: "price", headerName: "판매가", type: "currencyType", width: 65},
        {field: "change_val", headerName: "변경금액(율)", type: "currencyType", width: 80, editable:true, cellStyle: {'background' : '#ffff99'}},
    ];
</script>

<script type="text/javascript" charset="utf-8">
    let gx;
    const pApp = new App('', { gridId: "#div-gd" });

    $(document).ready(function() {
        pApp.ResizeGrid(340);
        pApp.BindSearchEnter();
        let gridDiv = document.querySelector(pApp.options.gridId);
        gx = new HDGrid(gridDiv, columns);
        // Search();
    });


    // 등록된 상품리스트 가져오기
    function GetProducts() {
        let data = "sgr_cd=" + '{{ @$sgr->sgr_cd }}';
        gx.Request('/store/cs/cs02/search-return-products', data, -1, function(e) {
        });
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
        const url = `/store/api/goods/show/`;
        window.open(url, "_blank","toolbar=no,scrollbars=yes,resizable=yes,status=yes,top=100,left=100,width=1800,height=1000");
    }

    /**
     * goods api logics - 상품 가져오기
     * window opener에서 콜백을 사용하려면 var로 선언해야 합니다.
     */

    let callbaackRows = [];

    var goodsCallback = (row) => {
        addRow(row);
        setGoodsRows();
    };
    
    var multiGoodsCallback = (rows) => {
        if (rows && Array.isArray(rows)) rows.map(row => addRow(row));
        setGoodsRows();
    };

    var addRow = (row) => { // goods_api에서 opener 함수로 사용하기 위해 var로 선언
        const count = gx.gridOptions.api.getDisplayedRowCount() + callbaackRows.length;
        row = { 
            ...row, 
            item: row.opt_kind_nm, 
            qty: 0,
            return_price: row.price,
            total_return_price: 0,
            isEditable: true,
            count: count + 1,
        };
        callbaackRows.push(row);
    };
    
    var setGoodsRows = () => {
        gx.gridOptions.api.applyTransaction({ add : callbaackRows });
        callbaackRows = [];
    }

</script>
@stop

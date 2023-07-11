@extends('store_with.layouts.layout-nav')
@section('title','원부자재입고/반품')
@section('content')

<style>
    #help ul li {list-style: disc;list-style-position: inside;text-indent: -20px;padding-left: 20px;font-size: 13px;font-weight: 400;}
    #help p {font-size: 14px;font-weight: 700;padding-bottom: 5px;}
    .form-control[readonly] {background: #eeeeee;}
</style>

<div class="py-3 px-sm-3">
    <div class="page_tit">
        <h3 class="d-inline-flex">원부자재입고/반품</h3>
        <div class="d-inline-flex location">
            <span class="home"></span>
            <span>/ 원부자재관리</span>
            <span>/ 원부자재입고/반품관리</span>
        </div>
    </div>
    <div id="search-area" class="search_cum_form">
        <form method="get" name="search">
            <input type="hidden" name="cmd" value="{{ @$cmd }}">
            <input type="hidden" name='stock_no' value='{{ @$stock_no }}'>
            <div class="card mb-3" id="input-area">
                <div class="card-header d-flex justify-content-between">
                    <h4>기본 정보</h4>
                    <div>
                        <a href="javascript:void(0);" onclick="Cmder('save');" class="btn btn-sm btn-primary shadow-sm pl-2"><i class="bx bx-save mr-1"></i>저장</a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-4 mb-2 mb-lg-0">
                            <div class="form-group">
                                <label for="com_nm" class="required">원부자재업체</label>
                                <div class="form-inline inline_select_box">
                                    <div class="form-inline-inner input-box w-75 pr-1">
                                        <div class="form-inline inline_btn_box">
                                            <input type="text" id="com_nm" name="com_nm" class="form-control form-control-sm ac-company sch-sup-company" value="{{ @$com_nm }}">
                                            <a href="javascript:void(0);" class="btn btn-sm btn-outline-primary sch-sup-company"><i class="bx bx-dots-horizontal-rounded fs-16"></i></a>
                                        </div>
                                    </div>
                                    <div class="form-inline-inner input-box w-25 pl-1">
                                        <input type="text" id="com_id" name="com_id" class="form-control form-control-sm" value="{{ @$com_id }}" readonly />
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4 inner-td">
                            <div class="form-group">
								<label for="sdate" class="required">일자</label>
								<div class="form-inline">
                                    <div class="docs-datepicker form-inline-inner input_box w-100">
                                        <div class="input-group">
                                            <input type="text" class="form-control form-control-sm docs-date" name="sdate" id="sdate" value="{{ $sdate }}" autocomplete="off" disable>
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
                        <div class="col-lg-4 inner-td">
                            <div class="form-group">
								<label for="type" class="required">구분</label>
								<div class="flax_box">
                                    <select name="type" id="type" class="form-control form-control-sm w-100">
                                        <option value="">선택</option>
                                        <option value='10'>입고</option>
                                        <option value='-10'>반품</option>
                                    </select>
                                </div>
							</div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-4 mb-2 mb-lg-0">
                            <div class="form-group">
                                <label for="invoice_no" class="required">입고번호</label>
                                <div class="flex_box">
                                    <input type="text" onfocus="return getInvoiceNo();" class="form-control form-control-sm" name="invoice_no" id="invoice_no" value="{{ @$invoice_no }}">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="resul_btn_wrap mb-3">
                
            </div>
        </form>
        <div id="filter-area" class="card shadow-none mb-0 ty2">
            <div class="card-header d-flex justify-content-between">
                <h4>상품 정보</h4>
                <div>
                    <a href="javascript:void(0);" onclick="return getSearchGoods();" class="btn-sm btn btn-primary" onfocus="this.blur();"><i class="fa fa-plus fa-sm mr-1"></i> 상품 추가</a>
                    <a href="javascript:void(0);" onclick="return delGoods();" class="btn-sm btn btn-outline-primary" onfocus="this.blur();"><i class="fa fa-trash fa-sm mr-1"></i> 상품 삭제</a>
                </div>
            </div>
            <div class="card-body pt-3 pt-lg-1">
                <div class="table-responsive">
                    <div id="div-gd" class="ag-theme-balham"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- import excel lib -->
<script type="text/javascript" charset="utf-8">
    const STATE = "{{ @$state }}";
    const CMD = "{{ @$cmd }}";
    const COMMAND_URL = '/store/cs/cs01/comm';
    const StyleCenter = {"text-align": "center"};
    const StyleRight = {"text-align": "right"};

    let columns = [
        {headerName: '#', pinned: 'left', type: 'NumType', width: 50, cellStyle: StyleCenter,
            cellRenderer: params => params.node.rowPinned == 'top' ? '' : params.data.count,
            sortingOrder: ['desc', 'asc', 'null'],
            comparator: (valueA, valueB, nodeA, nodeB, isInverted) => { // 번호순으로 정렬이 안되는 문제 수정
                if (parseInt(valueA) == parseInt(valueB)) return 0;
                return (parseInt(valueA) > parseInt(valueB)) ? 1 : -1;
            },
        },
        {field: "chk", headerName: '', pinned: 'left', cellClass: 'hd-grid-code', checkboxSelection: true, headerCheckboxSelection: true, sort: null, width: 29},
        {field: "prd_cd", headerName: "바코드", width: 120, pinned: 'left', cellStyle: StyleCenter},
        // {field: "goods_no", headerName: "온라인코드", width: 70, pinned: 'left', cellStyle: StyleCenter},
        {field: "goods_nm", headerName: "상품명", type: "HeadGoodsNameType", width: 150,
            cellRenderer: (params) => {
                if (params.data.goods_no === undefined) return '';
                if (params.data.goods_no != '0') {
                    return '<a href="javascript:void(0);" onclick="return openHeadProduct(\'' + params.data.goods_no + '\');">' + params.value + '</a>';
                } else {
                    return '<a href="javascript:void(0);" onclick="return alert(`온라인코드가 없는 상품입니다.`);">' + params.value + '</a>';
                }
            }   
        },
        {field: "goods_nm_eng", headerName:"상품명(영문)", width: 150},
        {field: "prd_cd_p", headerName: "품번", width: 90, cellStyle: StyleCenter},
        {field: "color", headerName: "컬러", width: 55, cellStyle: StyleCenter},
        {field: "size", headerName: "사이즈", width: 55, cellStyle: StyleCenter},
        {field: "opt_kor", headerName: "옵션", width: 130},
        {field: "item" ,headerName: "품목", width: 70, cellStyle: StyleCenter},
        {field: "brand" ,headerName:"구분", width: 70, cellStyle: StyleCenter},
        // {field: "style_no" ,headerName:"스타일넘버", width: 80, cellStyle: StyleCenter},
        // {field: "total_qty", headerName: "총재고", type:'currencyType', width: 60},
        {field: "sg_qty", headerName: "창고재고", type:'currencyType', width: 60},
        {field: "qty", headerName: "수량", width: 70,
            editable: params => checkIsEditable(params),
            cellStyle: params => checkIsEditable(params) ? {backgroundColor: '#ffff99', textAlign: 'right'} : {textAlign: 'right', fontWeight: 'bold'},
        },
        {field: "goods_sh", headerName: "TAG가", width: 70, type: "currencyType"},
        {field: "price", headerName: "판매가", width: 70, type: "currencyType"},
        {field: "stock_date", headerName: "최근입고일자", width: 90, cellStyle: StyleCenter},
        {width: "auto"}
    ];


    const pApp = new App('', { gridId: "#div-gd" });
    let gx;
    const pinnedRowData = [{ prd_cd: '합계', qty: 0 }];

    $(document).ready(() => {
        pApp.ResizeGrid(100, window.screen.width >= 740 ? undefined : 400);
        let gridDiv = document.querySelector(pApp.options.gridId);
        gx = new HDGrid(gridDiv, columns, {
            getRowNodeId: (data) => data.hasOwnProperty('count') ? data.count : "0", // 업데이터 및 제거를 위한 식별 ID를 count로 할당
            pinnedTopRowData: pinnedRowData,
            getRowStyle: (params) => { // 고정된 row styling
                if (params.node.rowPinned)  return { 'font-weight': 'bold', 'background': '#eee', 'border': 'none'};
            },
            onCellValueChanged: (e) => {
                if (e.column.colId === "qty") {
                    if (isNaN(e.newValue) == true || e.newValue == "") {
                        alert("숫자만 입력가능합니다.");
                        gx.gridOptions.api.startEditingCell({ rowIndex: e.rowIndex, colKey: e.column.colId });
                    } else if(e.newValue < 0) {
                        alert("음수는 입력할 수 없습니다.");
                        gx.gridOptions.api.startEditingCell({ rowIndex: e.rowIndex, colKey: e.column.colId });
                    } else {
                        if ($('#type').val() == -10) {
                            if(e.column.colId === "qty" && e.data.sg_qty < parseInt(e.data.qty)) {
                                    alert("창고재고보다 많은 수량을 반품할 수 없습니다.");
                                    gx.gridOptions.api.startEditingCell({ rowIndex: e.rowIndex, colKey: e.column.colId });
                            } else {
                                e.node.setSelected(true);
                                e.data.total_return_price = parseInt(e.data.qty) * parseInt(e.data.return_price);
                                gx.gridOptions.api.updateRowData({update: [e.data]});
                                updatePinnedRow();
                            }
                        } else {
                            e.node.setSelected(true);
                            e.data.total_return_price = parseInt(e.data.qty) * parseInt(e.data.return_price);
                            gx.gridOptions.api.updateRowData({update: [e.data]});
                            updatePinnedRow();
                        }
                    }
                }
            }
        });
    });

    const updatePinnedRow = () => { // 총 반품금액, 반품수량을 반영한 PinnedRow를 업데이트
        let [ qty] = [ 0 ];
        const rows = gx.getRows();
        if (rows && Array.isArray(rows) && rows.length > 0) {
            rows.forEach((row, idx) => {
                qty += parseFloat(row.qty);
            });
        }

        let pinnedRow = gx.gridOptions.api.getPinnedTopRow(0);
        gx.gridOptions.api.setPinnedTopRowData([
            { ...pinnedRow.data, qty: qty}
        ]);
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

    //원부자재 업체 검색
    $( ".sch-sup-company" ).on("click", () => {
        searchCompany.Open(null, '6', 'wonboo');
    });

    /**********************************
     * Grid 사용함수
     *********************************/

    /** 수정가능한 셀인지 판단 */
    function checkIsEditable(params) {
        const cols = ['unit_cost', 'prd_tariff_rate'];
        const super_admin = '{{ @$super_admin }}';

        // 슈퍼관리자 권한설정
        if (super_admin == 'true') cols.push('qty');

        if (
            (cols.includes(params.column?.colId || '')) 
            && STATE > 0 
            && STATE < (super_admin == 'true' ? 41 : 40)
            && (STATE < 40 || params.data?.is_last == 1)
            && params.node.rowPinned != 'top'
        ) return true; 

        return params.data.hasOwnProperty('isEditable') && params.data.isEditable ? true : false;
    }

</script>

<script>
    /** 입고번호 생성 */
    function getInvoiceNo() {
        const ff = document.search;
	    const com_id = ff.com_id.value;
	    const invoice_no = ff.invoice_no.value;

        if (invoice_no == '' && com_id != "") {
            axios({
                url: COMMAND_URL,
                method: 'post',
                data: {
                    cmd: 'getinvoiceno',
                    com_id: com_id
                }
            }).then((response) => {
                ff.invoice_no.value = response.data.invoice_no;
            }).catch((error) => { 
                console.log(error);
            });
        } else if (invoice_no == '' && com_id == '') {
            $('.sch-sup-company').click();
        }
    }

    /**********************************
     * 상품 관리
     *********************************/

    /** 상품검색 팝업 오픈 */
    function getSearchGoods() {
        const com_id = document.search.com_id;
        if (com_id.value === '') {
            alert("원부자재업체를 선택해주세요.");
            return document.search.com_nm.click();
        }
        const url = '/store/api/goods/show';
        const pop_up = window.open(url, "_blank", "toolbar=no,scrollbars=yes,resizable=yes,status=yes,top=100,left=100,width=1800,height=1000");
    }

    /** 선택한 상품 적용 */
    var goods_search_cmd = '';
    var goodsCallback = (row) => addRow(row);
    var multiGoodsCallback = (rows) => {
        if (rows && Array.isArray(rows)) rows.forEach(row => addRow(row));
    }
    var beforeSearchCallback = (api_document) => {
        api_document.search.com_nm.value = document.search.com_nm.value;
        api_document.search.com_cd.value = document.search.com_id.value;
    };
    var addRow = (row) => { // goods_api에서 opener 함수로 사용하기 위해 var로 선언
        const count = gx.gridOptions.api.getDisplayedRowCount();
        row = { ...row, 
            item: row.opt_kind_nm, opt_kor: row.goods_opt,
            exp_qty: 0, qty: 0, unit_cost: 0, prd_tariff_rate: 0, unit_total_cost: 0, income_amt: 0, income_total_amt: 0, cost: 0, total_cost: 0, total_cost_novat: 0, 
            isEditable: true, count: count + 1,
        };
        gx.gridOptions.api.applyTransaction({ add: [row] });
    };

    function validate() {
        const ff = document.search;

        if(ff.com_id.value == "") {
            alert("원부자재업체를 선택해 주십시오.");
            $('.sch-sup-company').click();
            return false;
        }
        if(ff.invoice_no.value == "") {
            alert("입고번호를 입력해 주십시오.");
            ff.invoice_no.focus();
            return false;
        }
        if(ff.sdate.value.trim().length != 10) {
            alert("입고/반품 일자를 입력해 주십시오.");
            ff.sdate.focus();
            return false;
        }
        if(ff.type.value == "") {
            alert("구분을 선택해 주십시오.");
            ff.type.focus();
            return false;
        }

        const rows = gx.getRows();
        if (rows.length < 1) {
            alert("입고/반품 상품을 한 개 이상 등록해주세요.");
            return false;
        }

        return true;
    }

    function Cmder(cmd){
        if(cmd =="save"){
            if(validate()){
                Save();
            }
        }
    } 

    function Save() {
        let rows = gx.getSelectedRows();
        let com_id = $('#com_id').val();
        let sdate = $('#sdate').val();
        let type = $('#type').val();
        let invoice_no = $('#invoice_no').val();

        if(rows.length == 0) {
            return alert('입고/반품할 상품을 선택해주세요.')
        }

        if(!confirm('저장하시겠습니까?')){
            return false;
        }

        axios({
            url: '/store/cs/cs03/buy/save',
            method: 'post',
            data: {
                rows : rows,
                com_id : com_id,
                sdate : sdate,
                type : type,
                invoice_no : invoice_no
            }
        }).then((res) => {
            if(res.data.code == 200) alert('선택하신 상품이 입고대기로 저장되었습니다.');
            if(res.data.code == 201) alert('선택하신 상품이 반품대기로 저장되었습니다.');
            window.close();
            opener.Search();
        }).catch((error) => {
            alert('저장 중 문제가 발생하였습니다. 관리자에게 문의해주세요')
            console.log(error);
        });

    }


</script>
@stop

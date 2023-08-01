@extends('store_with.layouts.layout-nav')
@php
    $title = "창고재고조정 개별등록";
    if($cmd == "get") $title = "창고재고조정내역";
@endphp
@section('title', $title)
@section('content')
<div class="show_layout py-3 px-sm-3">
    <div class="page_tit d-flex justify-content-between">
        <div class="d-flex">
            <h3 class="d-inline-flex">{{ $title }}</h3>
            <div class="d-inline-flex location">
                <span class="home"></span>
	            <span>상품관리</span>
	            <span>/ 창고재고조정</span>
                <span>/ {{ $title }}</span>
            </div>
        </div>
        <div class="d-flex align-items-center">
            @if($cmd == 'add')
            <a href="javascript:void(0)" onclick="Save('{{ @$cmd }}')" class="btn btn-primary mr-1"><i class="fas fa-save fa-sm text-white-50 mr-1"></i> 등록</a>
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
                                            <th class="required">재고조정일자</th>
                                            <td>
                                                <div class="form-inline">
                                                    @if($cmd == 'add')
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
                                                    <p class="fs-14">{{ $ssc->ssc_date }}</p>
                                                    @endif
                                                </div>
                                            </td>
                                            <th class="required">창고</th>
                                            <td>
                                                <div class="form-inline inline_select_box">
                                                    @if($cmd == 'add')
                                                    <div class="form-inline-inner input-box w-100">
                                                        <div class="form-inline inline_btn_box">
	                                                        <input type='hidden' id="storage_nm" name="storage_nm">
	                                                        <select id="storage_no" name="storage_no" class="form-control form-control-sm select2-storage"></select>
	                                                        <a href="javascript:void(0);" class="btn btn-sm btn-outline-primary sch-storage-one"><i class="bx bx-dots-horizontal-rounded fs-16"></i></a>
                                                        </div>
                                                    </div>
                                                    @else
                                                    <input type="text" name="storage_nm" id="storage_nm" value="{{ @$ssc->storage_nm }}" class="form-control form-control-sm w-100" readonly />
                                                    @endif
                                                </div>
                                            </td>
                                            <th>재고조정코드</th>
                                            <td>
                                                <div class="form-inline">
                                                    <p id="ssc_cd" class="fs-14">@if(@$ssc != null) {{ @$ssc->ssc_code }} ({{ @$ssc->ssc_type_nm }}) @endif</p>
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
                                                    <p class="fs-14 py-2">{{ $ssc->md_nm }}</p>
                                                    @endif
                                                </div>
                                            </td>
                                            <th>메모</th>
                                            <td colspan="3">
                                                <div class="form-inline">
	                                                @if(@$cmd == 'add')
		                                                <textarea name="comment" id="comment" class="form-control w-100" rows="1">{{ @$ssc->comment }}</textarea>
	                                                @else
		                                                <p class="fs-14">{{ $ssc->comment }}</p>
	                                                @endif
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
                    <button type="button" onclick="delGoods();" class="btn btn-sm btn-outline-primary shadow-sm" id="add_row_btn"><i class="bx bx-trash"></i> 상품삭제</button>
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
    const pinnedRowData = [{ prd_cd: '합계', storage_wqty: 0, qty: 0, loss_qty: 0, loss_price: 0 }];
	
	const loss_reasons = <?= json_encode(@$loss_reasons) ?>;
	loss_reasons.unshift({ code_id: "", code_val: "-" });

    let columns = [
        {headerName: "No", pinned: "left", valueGetter: "node.id", cellRenderer: "loadingRenderer", width: 40, cellClass: 'hd-grid-code',
            cellRenderer: params => params.node.rowPinned == 'top' ? '' : params.data.count,
            sortingOrder: ['desc', 'asc', 'null'],
            comparator: (valueA, valueB, nodeA, nodeB, isInverted) => {
                if (parseInt(valueA) == parseInt(valueB)) return 0;
                return (parseInt(valueA) > parseInt(valueB)) ? 1 : -1;
            },
        },
        {field: "chk", headerName: '', pinned: 'left', cellClass: 'hd-grid-code', checkboxSelection: true, headerCheckboxSelection: true, sort: null, width: 28},
        {field: "prd_cd", headerName: "바코드", pinned: 'left', width: 130, cellClass: 'hd-grid-code'},
        {field: "goods_no", headerName: "온라인코드", width: 70, cellClass: 'hd-grid-code'},
        {field: "opt_kind_nm", headerName: "품목", width: 80, cellClass: 'hd-grid-code'},
        {field: "brand", headerName: "브랜드", width: 80, cellClass: 'hd-grid-code'},
        {field: "style_no",	headerName: "스타일넘버", width: 70, cellClass: 'hd-grid-code'},
        {field: "goods_nm",	headerName: "상품명", width: 200,
            cellRenderer: (params) => {
                if (params.data.goods_no === undefined) return '';
                if (params.data.goods_no != '0') {
                    return '<a href="javascript:void(0);" onclick="return openHeadProduct(\'' + params.data.goods_no + '\');">' + params.value + '</a>';
                } else {
                    return '<a href="javascript:void(0);" onclick="return alert(`온라인코드가 없는 상품입니다.`);">' + params.value + '</a>';
                }
            }   
        },
        {field: "goods_nm_eng",	headerName: "상품명(영문)", width: 150},
        {field: "prd_cd_p", headerName: "품번", width: 100, cellClass: 'hd-grid-code'},
        {field: "color", headerName: "컬러", width: 50, cellClass: 'hd-grid-code'},
        {field: "size", headerName: "사이즈", width: 50, cellClass: 'hd-grid-code'},
        {field: "goods_opt", headerName: "옵션", width: 100},
        {field: "goods_sh", headerName: "정상가", type: "currencyType", width: 70},
        {field: "price", headerName: "현재가", type: "currencyType", width: 70},
        {field: "storage_wqty", headerName: "창고보유재고", width: 90, type: 'currencyType'},
        {field: "qty", headerName: "조정재고", width: 60, type: 'currencyType', 
            editable: (params)=> params.node.rowPinned !== 'top' && cmd == 'add',
			cellClass: (params) => (['hd-grid-number', params.node.rowPinned !== 'top' && cmd == 'add' ? 'hd-grid-edit' : '']),
        },
        {field: "loss_qty", headerName: "LOSS수량", width: 80, type: 'currencyType',
            cellStyle: (params) => ({ 'background-color': params.node.rowPinned !== 'top' && (params.value > 0 || params.value < 0) ? '#ff9999' : 'inherit' }),
        },
        {field: "loss_price", headerName: "LOSS금액", width: 80, type: 'currencyType',
            cellStyle: (params) => ({ 'background-color': params.node.rowPinned !== 'top' && (params.value > 0 || params.value < 0) ? '#ff9999' : 'inherit' }),
        },
		{field: "loss_tag_price", headerName: "TAG가 금액", width: 80, type: 'currencyType', hide: true},
		{field: "loss_price2", headerName: "현재가 금액", width: 80, type: 'currencyType', hide: true},
        {field: "loss_reason", hide: true},
        {field: "loss_reason_val", headerName: "LOSS사유", width: 90,
	        editable: (params)=> params.node.rowPinned !== 'top' && cmd == 'add',
	        cellClass: (params) => (['hd-grid-code', params.node.rowPinned !== 'top' && cmd == 'add' ? 'hd-grid-edit' : '']),
			cellEditor: 'agRichSelectCellEditor',
			cellEditorPopup: true,
			cellEditorParams: {
				values: loss_reasons.map(rs => rs.code_val),
				formatValue: (value) => {
					let code_id = loss_reasons.find(rs => rs.code_val === value)?.code_id;
					return `${code_id ? '[' + code_id + '] ' : ''}${value}`;
				},
			},
        },
        {field: "comment", headerName: "메모", width: 200,
	        editable: (params)=> params.node.rowPinned !== 'top' && cmd == 'add', 
	        cellClass: (params) => params.node.rowPinned !== 'top' && cmd == 'add' ? 'hd-grid-edit' : ''
        },
    ];
</script>

<script type="text/javascript" charset="utf-8">
    let gx;
    const pApp = new App('', { gridId: "#div-gd" });

    $(document).ready(function() {
        pApp.ResizeGrid(385);
        pApp.BindSearchEnter();
        let gridDiv = document.querySelector(pApp.options.gridId);
		
		if (cmd === 'get') {
			columns = columns.map(col => ['loss_price2', 'loss_tag_price'].includes(col.field) ? { ...col, hide: false } : col);
		}
		
        gx = new HDGrid(gridDiv, columns, {
            pinnedTopRowData: pinnedRowData,
            getRowStyle: (params) => { // 고정된 row styling
                if (params.node.rowPinned)  return { 'font-weight': 'bold', 'background': '#eee', 'border': 'none'};
            },
            getRowNodeId: (data) => data.hasOwnProperty('count') ? data.count : "0", // 업데이터 및 제거를 위한 식별 ID를 count로 할당
            onCellValueChanged: (e) => {
                if (e.column.colId === "qty") {
                    if (isNaN(e.newValue) || e.newValue === "") {
                        alert("숫자만 입력가능합니다.");
                        gx.gridOptions.api.startEditingCell({ rowIndex: e.rowIndex, colKey: e.column.colId });
                    } else if(e.newValue < 0) {
                        alert("음수는 입력할 수 없습니다.");
                        gx.gridOptions.api.startEditingCell({ rowIndex: e.rowIndex, colKey: e.column.colId });
                    } else {
						e.node.setDataValue('loss_qty', parseInt(e.data.storage_wqty) - parseInt(e.data.qty));
						e.node.setDataValue('loss_price', parseInt(e.data.price) * parseInt(e.data.loss_qty));
						updatePinnedRow();
                    }
                } else if (e.column.colId === "loss_reason_val") {
					e.node.setDataValue('loss_reason', loss_reasons.find(rs => rs.code_val === e.value)?.code_id || '');
                }
            }
        });
        if(cmd === 'get') GetProducts();

        $("#storage_no").on("change", function(e) {
            gx.gridOptions.api.setRowData([]);
            updatePinnedRow();
        });
    });

    // 등록된 상품리스트 가져오기
    function GetProducts() {
        let data = "ssc_cd=" + '{{ @$ssc->ssc_cd }}';
        gx.Request('/store/stock/stk27/search-check-products', data, -1, function(e) {
            updatePinnedRow();
        });
    }

    // 재고조정 등록
    function Save(cmd) {
        if(cmd !== 'add') return;

        let rows = gx.getRows();

        let ssc_date = document.f1.sdate.value;
        let storage_cd = document.f1.storage_no.value;
        let md_id = document.f1.md_id.value;
        let comment = document.f1.comment.value;

        if(storage_cd === '') {
            $(".sch-storage-one").click();
            return alert("창고를 선택해주세요.");
        }
        if(rows.length < 1) return alert("재고조정할 상품을 추가해주세요.");
        if(md_id === '') return alert("담당자를 선택해주세요.");

		let not_reason_rows = rows.filter(row => row.storage_wqty != row.qty && !row.loss_reason);
		if (not_reason_rows.length > 0) return alert("LOSS수량이 발생한 항목에는 반드시 LOSS사유를 입력해주세요.");

        if(!confirm("등록하시겠습니까?")) return;

        axios({
            url: '/store/stock/stk27/save',
            method: 'put',
            data: {
                ssc_type: "G",
                ssc_date,
				storage_cd,
                md_id,
                comment,
                products: rows.map(r => ({ ...r, storage_qty: r.storage_wqty })),
            },
        }).then(function (res) {
            if(res.data.code === '200') {
                alert("창고재고조정이 성공적으로 완료되었습니다.");
                opener.Search();
                window.close();
            } else {
                console.log(res.data);
                alert("저장 중 오류가 발생했습니다.\n관리자에게 문의해주세요.");
            }
        }).catch(function (err) {
            console.log(err);
        });
    }

    // 상품 삭제
    const deleteRow = (row) => { gx.gridOptions.api.applyTransaction({remove : [row]}); };

    const delGoods = () => {
        const ff = document.f1;
        const rows = gx.getSelectedRows();
        if (Array.isArray(rows) && !(rows.length > 0)) return alert("삭제할 상품을 선택해주세요.");

        rows.forEach((row) => { deleteRow(row); });
        updatePinnedRow();
    };

    /***************************************************************************/
    /******************************** 상품 추가 관련 ****************************/
    /***************************************************************************/

    // 상품 추가
    function addGoods() {
        const ff = document.f1;
        if (ff.storage_no.value == '') {
            $(".sch-storage-one").click();
            return alert('창고를 선택해주세요.');
        }

        const url = `/store/api/goods/show?storage_cd=` + ff.storage_no.value;
        window.open(url, "_blank","toolbar=no,scrollbars=yes,resizable=yes,status=yes,top=100,left=100,width=1800,height=1000");
    }

    /**
     * goods api logics - 상품 가져오기
     * window opener에서 콜백을 사용하려면 var로 선언해야 합니다.
     */

    let callbaackRows = [];

    var goodsCallback = (row) => {
        addRow(row);
		setStorageQty();
    };
    
    var multiGoodsCallback = (rows) => {
        if (rows && Array.isArray(rows)) rows.map(row => addRow(row));
		setStorageQty();
    };

    var addRow = (row) => { // goods_api에서 opener 함수로 사용하기 위해 var로 선언
        const count = gx.gridOptions.api.getDisplayedRowCount() + callbaackRows.length;
        row = {
            ...row, 
            item: row.opt_kind_nm, 
            goods_type_nm: row.goods_type,
            qty: 0, 
            loss_qty: (row.storage_wqty * 1),
            loss_price: (row.storage_wqty * 1) * row.price,
            count: count + 1,
        };
        callbaackRows.push(row);
    };
    
    var setStorageQty = () => {
        gx.gridOptions.api.applyTransaction({ add : callbaackRows });
        callbaackRows = [];
        updatePinnedRow();
    }

    const updatePinnedRow = () => { // 총 반품금액, 반품수량을 반영한 PinnedRow를 업데이트
        let [ storage_wqty, qty, loss_qty, loss_price, loss_price2, loss_tag_price ] = [ 0, 0, 0, 0, 0, 0, 0 ];
        const rows = gx.getRows();
        if (rows && Array.isArray(rows) && rows.length > 0) {
            rows.forEach((row, idx) => {
				storage_wqty += parseInt(row.storage_wqty || 0);
                qty += parseInt(row.qty || 0);
                loss_qty += parseInt(row.loss_qty || 0);
                loss_price += parseInt(row.loss_price || 0);
                loss_price2 += parseInt(row.loss_price2 || 0);
                loss_tag_price += parseInt(row.loss_tag_price || 0);
            });
        }

        let pinnedRow = gx.gridOptions.api.getPinnedTopRow(0);
        gx.gridOptions.api.setPinnedTopRowData([
            { ...pinnedRow.data, storage_wqty, qty, loss_qty, loss_price, loss_price2, loss_tag_price }
        ]);
    };
</script>
@stop

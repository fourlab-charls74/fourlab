@extends('store_with.layouts.layout-nav')
@php
    $title	= "매장반품등록";
    if($cmd == "update") $title = "매장반품내역";
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
                <span>/ 매장반품관리(매장)</span>
                <span>/ {{ $title }}</span>
            </div>
        </div>
        <div class="d-flex align-items-center">
            @if($cmd == 'add')
                <a href="javascript:void(0)" onclick="Save('{{ @$cmd }}')" class="btn btn-primary mr-1"><i class="fas fa-save fa-sm text-white-50 mr-1"></i> 반품요청</a>
            @elseif ($cmd == 'update')
                @if (@$sr_state == 10)
                    <a href="javascript:void(0)" onclick="return UpgradeState(30);" class="btn btn-danger"><i class="fas fa-check fa-sm text-white-50 mr-1"></i> 반품처리중</a>
                @endif
                @if (@$sr_state == 30)
	                <a href="javascript:void(0)" onclick="return UpgradeState(40);" class="btn btn-danger"><i class="fas fa-check fa-sm text-white-50 mr-1"></i> 반품완료</a>
                @endif
                @if (@$sr_state >= 10 && @$sr_state < 40)
	                <span class="mx-2">|</span>
	                <a href="javascript:void(0)" onclick="Save('{{ @$cmd }}')" class="btn btn-primary mr-1"><i class="fas fa-save fa-sm text-white-50 mr-1"></i> 저장</a>
                @endif
                <a href="javascript:void(0)" onclick="return DelReturn();" class="btn btn-primary mr-1"><i class="fas fa-trash fa-sm text-white-50 mr-1"></i> 삭제</a>
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
                                            <th class="required">반품요청일</th>
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
                                            <th class="required">보내는 매장</th>
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
                                                    <input type="hidden" name="store_cd" id="store_cd" value="{{ @$sr->store_cd }}" />
                                                    <input type="hidden" name="store_no" id="store_no" value="{{ @$sr->store_cd }}" />
                                                    @endif
                                                </div>
                                            </td>
                                            <th class="required">반품창고</th>
                                            <td>
                                                <div class="form-inline">
                                                    <select name='storage_cd' class="form-control form-control-sm w-100">
                                                        @foreach (@$storages as $storage)
                                                            <option value='{{ $storage->storage_cd }}' @if(@$cmd == 'update' && $sr->storage_cd == $storage->storage_cd) selected @elseif(@$cmd === 'add' && $storage->storage_cd == 'S0006') selected @endif>{{ $storage->storage_nm }}</option>
                                                        @endforeach
                                                        <input type="hidden" id="storage" value="{{ @$sr->storage_cd }}" class="form-control form-control-sm w-100" readonly />
                                                    </select>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
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
                                                    <textarea name="comment" id="comment" class="form-control w-100" rows="1">{{ @$sr->comment }}</textarea>
                                                </div>
                                            </td>
                                            <th>반품코드</th>
                                            <td>
                                                <div class="form-inline">
                                                    <p id="sr_cd" class="fs-14">@if(@$sr != null) {{ @$sr->sr_code }} @endif</p>
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
                @if(@$cmd == 'add')
                    <div class="d-flex">
                        <button type="button" onclick="return setReturnQty('store_wqty', 'qty');" class="btn btn-sm btn-outline-primary shadow-sm mr-1">전체재고반품요청</button>
                        <button type="button" onclick="return setReturnQty('', 'qty');" class="btn btn-sm btn-outline-primary shadow-sm">요청수량초기화</button>
                    </div>
	                <span class="mx-2">|</span>
	                <div class="d-flex">
		                <button type="button" onclick="return addGoods();" class="btn btn-sm btn-primary shadow-sm mr-1"><i class="bx bx-plus"></i> 상품추가</button>
		                <button type="button" onclick="return delGoods();" class="btn btn-sm btn-outline-primary shadow-sm"><i class="bx bx-trash"></i> 상품삭제</button>
	                </div>
                @endif
                @if(@$sr->sr_state == '10')
                    <button type="button" onclick="return setReturnQty('qty', 'return_p_qty');" class="btn btn-sm btn-outline-primary shadow-sm mr-1">요청수량 <i class="bx bx-right-arrow-alt"></i> 처리수량</button>
	                <button type="button" onclick="return addGoods();" class="btn btn-sm btn-primary shadow-sm"><i class="bx bx-plus"></i> 상품추가</button>
                @endif
                @if(@$sr->sr_state == '30')
                    <button type="button" onclick="return setReturnQty('return_p_qty', 'fixed_return_qty');" class="btn btn-sm btn-outline-primary shadow-sm mr-1">처리수량 <i class="bx bx-right-arrow-alt"></i> 확정수량</button>
	                <button type="button" onclick="return addGoods();" class="btn btn-sm btn-primary shadow-sm"><i class="bx bx-plus"></i> 상품추가</button>
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
    const pinnedRowData = [{ prd_cd: '합계' }];

	const reject_reasons = <?= json_encode(@$reject_reasons) ?>;
	reject_reasons.unshift({ code_id: "", code_val: "-" });

    let columns = [
        {headerName: "No", pinned: "left", valueGetter: "node.id", cellRenderer: "loadingRenderer", width: 40, cellStyle: {"text-align": "center"},
            cellRenderer: params => params.node.rowPinned == 'top' ? '' : params.data.count,
            sortingOrder: ['desc', 'asc', 'null'],
            comparator: (valueA, valueB, nodeA, nodeB, isInverted) => {
                if (parseInt(valueA) == parseInt(valueB)) return 0;
                return (parseInt(valueA) > parseInt(valueB)) ? 1 : -1;
            },
        },
        {field: "chk",		headerName: '', pinned: 'left', cellClass: 'hd-grid-code', checkboxSelection: true, headerCheckboxSelection: true, sort: null, width: 29},
		{field: "print", headerName: "명세서", pinned: 'left', cellStyle: {"text-align": "center", "color": "#4444ff", "font-size": '13px'},
			cellRenderer: function(params) {
				if(params.data.print !== '' && params.node.rowPinned !== 'top') {
					return `<a href="javascript:void(0);" style="color: inherit;" onclick="printDocument(${params.data.sr_cd}, '${params.data.box_no}')">출력</a>`;
				} else{
					return ' ';
				}
			}
		},
		{field: "box_no",	headerName: "박스번호", pinned: 'left', width: 80, cellStyle: {"text-align": "center"},
			cellClass: (params)	=> params.node.rowPinned !== 'top' && now_state < 40 ? 'hd-grid-edit' : '',
			editable: (params)	=> params.node.rowPinned !== 'top' && now_state < 40,
		},
		{field: "prd_cd",	headerName: "바코드", pinned: 'left', width: 130, cellStyle: {"text-align": "center"}},
        {field: "goods_no",	headerName: "온라인코드", width: 70, cellStyle: {"text-align": "center"}},
        {field: "opt_kind_nm",	headerName: "품목", width: 70, cellStyle: {"text-align": "center"}},
        {field: "brand",	headerName: "브랜드", width: 80, cellStyle: {"text-align": "center"}},
        {field: "style_no",	headerName: "스타일넘버", width: 70, cellStyle: {"text-align": "center"}},
        {field: "goods_nm",	headerName: "상품명", type: 'HeadGoodsNameType', width: 200},
        {field: "goods_nm_eng",	headerName: "상품명(영문)", width: 200},
        {field: "prd_cd_p",	headerName: "품번", width: 100, cellStyle: {"text-align": "center"}},
        {field: "color",	headerName: "컬러", width: 55, cellStyle: {"text-align": "center"}},
        {field: "size",		headerName: "사이즈", width: 55, cellStyle: {"text-align": "center"}},
        {field: "goods_opt", headerName: "옵션", width: 100},
        {field: "goods_sh", headerName: "정상가", type: "currencyType", width: 70},
        {field: "price",	headerName: "현재가", type: "currencyType", width: 70},
        {field: "return_price", headerName: "반품단가", width: 70, type: 'currencyType',
            editable: (params) => checkIsEditable(params) && now_state < 30,
            cellStyle: (params) => checkIsEditable(params) && now_state < 30 ? {"background-color": "#ffff99"} : {}
        },
		{headerName: "매장재고", children: [
			{field: "store_qty", headerName: "재고", width: 60, type: 'currencyType'},
	        {field: "store_wqty", headerName: "보유재고", width: 60, type: 'currencyType'},
		]},
        {field: "return_amt", headerName: "요청금액", width: 80, type: 'currencyType'},
        {field: "qty", headerName: "요청수량", width: 60, type: 'currencyType',
            editable: (params) => checkIsEditable(params) && now_state < 30,
            cellStyle: (params) => checkIsEditable(params) && now_state < 30 ? {"background-color": "#ffff99"} : {}
        },
		{field: "return_p_amt", headerName: "처리금액", width: 80, type: 'currencyType', hide: true},
        {field: "return_p_qty", headerName: "처리수량", width: 60, type: 'currencyType', hide: true,
            editable: (params) => checkIsEditable(params) && now_state < 30,
            cellStyle: (params) => checkIsEditable(params) && now_state < 30 ? {"background-color": "#ffff99"} : {}
        },
		{field: "fixed_return_price", headerName: "확정금액", width: 80, type: 'currencyType', hide: true},
		{field: "fixed_return_qty", headerName: "확정수량", width: 60, type: 'currencyType', hide: true,
			editable: (params) => checkIsEditable(params),
			cellStyle: (params) => checkIsEditable(params) ? {"background-color": "#ffff99"} : {}
		},
		{field: "reject_reason", hide: true},
		{field: "reject_reason_val", headerName: "반품거부사유", width: 90, hide: true,
			// editable: (params) => checkIsEditable(params),
			// cellStyle: (params) => checkIsEditable(params) ? {"background-color": "#ffff99"} : {},
			// cellEditor: 'agRichSelectCellEditor',
			// cellEditorPopup: true,
			// cellEditorParams: {
			// 	values: reject_reasons.map(rs => rs.code_val),
			// 	formatValue: (value) => {
			// 		let code_id = reject_reasons.find(rs => rs.code_val === value)?.code_id;
			// 		return `${code_id ? '[' + code_id + '] ' : ''}${value}`;
			// 	},
			// },
		},
		{field: "reject_comment", headerName: "반품거부메모", width: 200, hide: true,
			// editable: (params) => checkIsEditable(params),
			// cellStyle: (params) => checkIsEditable(params) ? {"background-color": "#ffff99"} : {},
		},
        {field: "fixed_comment", headerName: "확정메모", width: 200, hide: true, 
            editable: (params) => checkIsEditable(params),
            cellStyle: (params) => checkIsEditable(params) ? {"background-color": "#ffff99"} : {}
        
        },
		{width: 0}
    ];
</script>

<script type="text/javascript" charset="utf-8">
    let gx;
    const pApp = new App('', { gridId: "#div-gd" });

    $(document).ready(function() {
        pApp.ResizeGrid(390);
        pApp.BindSearchEnter();
        let gridDiv = document.querySelector(pApp.options.gridId);
		
		if (cmd !== 'add' && now_state >= 10) {
			columns = columns.map(col => ['return_p_qty', 'return_p_amt', 'reject_reason_val', 'reject_comment'].includes(col.field) ? { ...col, hide: false } : col);
		}
		if (cmd !== 'add' && now_state >= 30) {
			columns = columns.map(col => ['fixed_return_qty', 'fixed_return_price', 'fixed_comment'].includes(col.field) ? { ...col, hide: false } : col);
		}
		
        gx = new HDGrid(gridDiv, columns, {
            pinnedTopRowData: pinnedRowData,
            getRowStyle: (params) => { // 고정된 row styling
                if (params.node.rowPinned)  return { 'font-weight': 'bold', 'background': '#eee', 'border': 'none'};
            },
            getRowNodeId: (data) => data.hasOwnProperty('count') ? data.count : "0", // 업데이터 및 제거를 위한 식별 ID를 count로 할당
            onCellValueChanged: (e) => {
				e.node.setSelected(true);
                if (['return_price', 'qty', 'return_p_qty', 'fixed_return_qty'].includes(e.column.colId)) {
					if (isNaN(e.newValue) == true || e.newValue === "") {
						alert("숫자만 입력가능합니다.");
						gx.gridOptions.api.startEditingCell({rowIndex: e.rowIndex, colKey: e.column.colId});
					} else if (e.newValue < 0) {
						alert("음수는 입력할 수 없습니다.");
						gx.gridOptions.api.startEditingCell({rowIndex: e.rowIndex, colKey: e.column.colId});
					} else if (
						(e.column.colId === "qty" && e.data.store_wqty < parseInt(e.data.qty))
						|| (e.column.colId === "return_p_qty" && e.data.store_wqty < parseInt(e.data.return_p_qty))
						// 아래코드 주석처리 (확정수량은 처리 이후 현재의 보유재고보다 많을 수 있습니다.)
						// || (e.column.colId === "fixed_return_qty" && e.data.store_wqty < parseInt(e.data.fixed_return_qty))
					) {
						// alert("해당 매장의 보유재고보다 많은 수량을 반품할 수 없습니다.");
						// gx.gridOptions.api.startEditingCell({ rowIndex: e.rowIndex, colKey: e.column.colId });
					} else {
						if (e.column.colId === "return_price") {
							e.node.setDataValue('return_amt', parseInt(e.data.qty) * parseInt(e.data.return_price));
							e.node.setDataValue('return_p_amt', parseInt(e.data.return_p_qty) * parseInt(e.data.return_price));
							e.node.setDataValue('fixed_return_price', parseInt(e.data.fixed_return_qty) * parseInt(e.data.return_price));
						} else if (e.column.colId === "qty") {
							e.node.setDataValue('return_amt', parseInt(e.data.qty) * parseInt(e.data.return_price));
						} else if (e.column.colId === "return_p_qty") {
							e.node.setDataValue('return_p_amt', parseInt(e.data.return_p_qty) * parseInt(e.data.return_price));
						} else if (e.column.colId === "fixed_return_qty") {
							e.node.setDataValue('fixed_return_price', parseInt(e.data.fixed_return_qty) * parseInt(e.data.return_price));
						}
						updatePinnedRow();
					}
				} else if(e.column.colId === "box_no") {
					if(e.newValue !== ""){
						const regex = /^[a-zA-Z0-9\s]*$/;
						if(!regex.test(e.newValue)) {
							alert("박스번호는 영문과 숫자만 가능합니다.");
							gx.gridOptions.api.startEditingCell({rowIndex: e.rowIndex, colKey: e.column.colId});
						}
					}
                } else if (e.column.colId === "reject_reason_val") {
					e.node.setDataValue('reject_reason', reject_reasons.find(rs => rs.code_val === e.value)?.code_id || '');
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
        let data = "sr_cd=" + '{{ @$sr->sr_cd }}';
        gx.Request('/store/stock/stk30/search-return-products', data, -1, function(e) {
            updatePinnedRow();
        });
    }

    // 상품반품 등록
    function Save(cmd) {
        if(!cmd) return;

        let sr_reason	= document.f1.sr_reason.value;
        let comment		= document.f1.comment.value;
        let rows		= gx.getRows();

        if(cmd === 'add') {
            let sr_date		= document.f1.sdate.value;
            let storage_cd	= document.f1.storage_cd.value;
            let store_cd	= document.f1.store_no.value;

            if(store_cd === '') {
                $(".sch-store").click();
                return alert("매장을 선택해주세요.");
            }
            if(rows.length < 1) return alert("반품등록할 상품을 선택해주세요.");

            let excess_qtys = rows.filter(r => (r.qty * 1) > (r.store_wqty * 1));
            // if(excess_qtys.length > 0) return alert("해당 매장의 보유재고보다 많은 수량을 반품할 수 없습니다.");

			let return_prices	= rows.filter(r => r.return_price === undefined);
			if(return_prices.length > 0) return alert("반품단가는 반드시 입력해야 합니다.");
			
			
            if (!confirm("반품요청하시겠습니까?")) return;
			
            axios({
                url: '/store/stock/stk30/save',
                method: 'put',
                data: {
                    sr_date,
                    storage_cd,
                    store_cd,
                    sr_reason,
                    comment,
                    products: rows.map(r => ({ box_no: ( r.box_no !== undefined ? r.box_no : ''), prd_cd: r.prd_cd, price: r.price, return_price: r.return_price, return_qty: r.qty, store_wqty: r.store_wqty })),
                },
            }).then(function (res) {
                if(res.data.code === 200) {
                    alert(res.data.msg);
                    opener.Search();
                    window.close();
                } else if (res.data.code !== 500) {
					alert(res.data.msg);
                } else {
                    console.log(res.data);
                    alert("반품요청 중 오류가 발생했습니다.\n관리자에게 문의해주세요.");
                }
            }).catch(function (err) {
                console.log(err);
            });
        } else if(cmd === 'update') {
            let sr_cd = '{{ @$sr->sr_cd }}';

            if(now_state >= 40) return alert("반품완료된 내역은 수정할 수 없습니다.");
            if(!confirm("저장하시겠습니까?")) return;

            axios({
                url: '/store/stock/stk30/update',
                method: 'put',
                data: {
                    sr_cd,
                    sr_reason,
                    comment,
                    products: rows.map(r => ({ ...r, return_qty: r.qty })),
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
	
	// 반품내역 삭제
    function DelReturn() {
		if(!confirm("삭제한 매장반품정보는 다시 되돌릴 수 없습니다.\n선택한 항목을 삭제하시겠습니까?")) return;
		if(now_state >= 40 && !confirm("지금 삭제하면 재고가 매장으로 환원처리됩니다.\n환원된 재고는 되돌릴 수 없습니다. 삭제하시겠습니까?")) return;

		axios({
			url: '/store/stock/stk30/del-return',
			method: 'delete',
			data: {
				sr_cds: [ '{{ @$sr->sr_cd }}' ],
			},
		}).then(function (res) {
			if(res.data.code === 200) {
				alert(res.data.msg);
				opener.Search();
				window.close();
			} else {
				console.log(res.data);
				alert("삭제 중 오류가 발생했습니다.\n관리자에게 문의해주세요.");
			}
		}).catch(function (err) {
			console.log(err);
		});
    }

    const checkIsEditable = (params) => {
        return (cmd == 'add' || (now_state >= 10 && now_state < 40)) && params.data.hasOwnProperty('isEditable') && params.data.isEditable ? true : false;
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
			print: '',
            item: row.opt_kind_nm, 
            qty: 0, 
            return_price: row.price,
            return_amt: 0,
			return_p_amt: 0,
			return_p_qty: 0,
			fixed_return_price: 0,
			fixed_return_qty: 0,
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
        let [ store_qty, store_wqty, qty, return_amt, return_p_qty, return_p_amt, fixed_return_qty, fixed_return_price ] = [ 0, 0, 0, 0, 0, 0, 0, 0 ];
        const rows = gx.getRows();
        if (rows && Array.isArray(rows) && rows.length > 0) {
            rows.forEach((row, idx) => {
				store_qty += parseFloat(row.store_qty);
				store_wqty += parseFloat(row.store_wqty);
                qty += parseFloat(row.qty);
                return_amt += parseFloat(row.return_amt);
				return_p_qty += parseFloat(row.return_p_qty);
				return_p_amt += parseFloat(row.return_p_amt);
                fixed_return_qty += parseFloat(row.fixed_return_qty);
				fixed_return_price += parseFloat(row.fixed_return_price);
            });
        }

        let pinnedRow = gx.gridOptions.api.getPinnedTopRow(0);
        gx.gridOptions.api.setPinnedTopRowData([
            { ...pinnedRow.data, store_qty, store_wqty, qty, return_amt, return_p_qty, return_p_amt, fixed_return_qty, fixed_return_price }
        ]);
    };
	
	const setReturnQty = (copy_col = '', paste_col = '') => {
		if (paste_col === '') return;
		gx.gridOptions.api.forEachNode(node => {
			node.setDataValue(paste_col, node.data[copy_col] || 0);
		});
	}
	
	// 반품상태 변경
    function UpgradeState(new_state) {
		let rows = gx.getRows();
		let title = new_state === 30 ? '반품처리중' : new_state === 40 ? '반품완료' : '';
		
		if(new_state == 30) {
			if (rows.filter(r => r.return_p_qty == 0 || r.return_p_qty == '0').length > 0) {
				if (!confirm("처리수량이 0인 상품이 있습니다. 그래도 반품처리하시겠습니까?")) return;
			} else {
				if(!confirm(title + " 상태로 변경하시겠습니까?")) return;
			}
		}

		if (new_state == 40) {
			let wrong_list = rows.filter(row => row.qty != row.fixed_return_qty);
			if (wrong_list.length > 0 && !confirm("요청수량과 확정수량이 일치하지 않는 항목이 존재합니다.\n그래도 변경하시겠습니까?")) return;
			if (rows.filter(r => r.fixed_return_qty === 0).length > 0) {
				if (!confirm("완료수량이 0인 상품이 있습니다. 그래도 반품완료하시겠습니까?")) return;
			}
		}
		
		let sr_cd = '{{ @$sr->sr_cd }}';
		let sr_reason = document.f1.sr_reason.value;
		let comment = document.f1.comment.value;

		axios({
			url: '/store/stock/stk30/update',
			method: 'put',
			data: {
				sr_cd,
				sr_reason,
				comment,
				new_state,
				products: rows.map(r => ({ ...r, return_qty: r.qty })),
			},
		}).then(function (res) {
			if(res.data.code === 200) {
				alert(res.data.msg);
				opener.Search();
				window.close();
			} else {
				console.log(res.data);
				alert("상태변경 중 오류가 발생했습니다.\n관리자에게 문의해주세요.");
			}
		}).catch(function (err) {
			console.log(err);
		});
    }

	// 매장반품 거래명세서 출력(박스번호별)
	function printDocument(sr_cd, box_no) {
		location.href = '/store/stock/stk30/download?sr_cd=' + sr_cd + '&box_no=' + box_no;
	}
</script>
@stop

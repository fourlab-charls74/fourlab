@extends('shop_with.layouts.layout-nav')
@section('title', '매장반품내역')
@section('content')
<div class="show_layout py-3 px-sm-3">
    <div class="page_tit d-flex justify-content-between">
        <div class="d-flex">
            <h3 class="d-inline-flex">매장반품내역</h3>
            <div class="d-inline-flex location">
                <span class="home"></span>
                <span>/ 매장관리</span>
	            <span>/ 매장반품관리</span>
                <span>/ 매장반품내역</span>
            </div>
        </div>
        <div class="d-flex align-items-center">
        @if (@$sr_state == 10)
	        <a href="javascript:void(0)" onclick="return UpgradeState();" class="btn btn-danger"><i class="fas fa-check fa-sm text-white-50 mr-1"></i> 반품처리중</a>
	        <span class="mx-2">|</span>
	        <a href="javascript:void(0)" onclick="Save()" class="btn btn-primary mr-1"><i class="fas fa-save fa-sm text-white-50 mr-1"></i> 임시저장</a>
		@elseif (@$sr_state == 30)
			<u class="fs-14 text-primary font-weight-bold mr-2">반품처리중</u>
		@elseif (@$sr_state == 40)
	        <u class="fs-14 text-danger font-weight-bold mr-2">반품완료</u>
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
                                                    <p class="fs-14">{{ $sr->sr_date }}</p>
                                                </div>
                                            </td>
	                                        <th class="required">보내는 매장</th>
	                                        <td>
		                                        <div class="form-inline inline_select_box">
			                                        <input type="text" name="store_nm" id="store_nm" value="{{ @$sr->store_nm }}" class="form-control form-control-sm w-100" readonly />
		                                        </div>
	                                        </td>
                                            <th class="required">반품창고</th>
                                            <td>
                                                <div class="form-inline">
                                                    <select name='storage_cd' id="storage_cd" class="form-control form-control-sm w-100" disabled>
                                                        @foreach (@$storages as $storage)
                                                            <option value='{{ $storage->storage_cd }}' @if($sr->storage_cd == $storage->storage_cd) selected @endif>{{ $storage->storage_nm }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th class="required">반품사유</th>
                                            <td>
                                                <div class="form-inline">
                                                    <select name='sr_reason' class="form-control form-control-sm w-100" disabled>
                                                        @foreach ($sr_reasons as $sr_reason)
                                                        <option value='{{ $sr_reason->code_id }}' @if($sr->sr_reason == $sr_reason->code_id) selected @endif>{{ $sr_reason->code_val }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </td>
                                            <th>메모</th>
                                            <td>
                                                <div class="form-inline">
                                                    {{ @$sr->comment }}
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
                    @if($sr->sr_state == 10)
		                <button type="button" onclick="return setReturnQty('qty', 'return_p_qty');" class="btn btn-sm btn-outline-primary shadow-sm mr-1">전체반품처리</button>
                        <button type="button" onclick="return setReturnQty('', 'return_p_qty');" class="btn btn-sm btn-outline-primary shadow-sm" id="add_row_btn">처리수량초기화</button>
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
    const now_state = '{{ @$sr->sr_state }}';
    const pinnedRowData = [{ prd_cd: '합계', qty: 0, total_return_price: 0 , fixed_return_qty: 0, fixed_return_price : 0}];

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
        {field: "prd_cd", headerName: "바코드", pinned: 'left', width: 130, cellStyle: {"text-align": "center"}},
        {field: "goods_no", headerName: "온라인코드", width: 70, cellStyle: {"text-align": "center"}},
        {field: "opt_kind_nm", headerName: "품목", width: 70, cellStyle: {"text-align": "center"}},
        {field: "brand", headerName: "브랜드", width: 80, cellStyle: {"text-align": "center"}},
        {field: "style_no",	headerName: "스타일넘버", width: 70, cellStyle: {"text-align": "center"}},
        {field: "goods_nm",	headerName: "상품명", width: 200,
            cellRenderer: function (params) {
                if (params.value !== undefined) {
                    if(params.data.goods_no == null) return '존재하지 않는 상품입니다.';
                    return '<a href="#" onclick="return openShopProduct(\'' + params.data.goods_no + '\');">' + params.value + '</a>';
                }
            }
        },
        {field: "goods_nm_eng",	headerName: "상품명(영문)", width: 200},
        {field: "prd_cd_p",	headerName: "품번", width: 100, cellStyle: {"text-align": "center"}},
        {field: "color", headerName: "컬러", width: 55, cellStyle: {"text-align": "center"}},
        {field: "size",	headerName: "사이즈", width: 55, cellStyle: {"text-align": "center"}},
        {field: "goods_opt", headerName: "옵션", width: 100},
        {field: "goods_sh", headerName: "TAG가", type: "currencyType", width: 70},
        {field: "price", headerName: "판매가", type: "currencyType", width: 70},
        {field: "return_price", headerName: "반품단가", width: 70, type: 'currencyType'},
		{headerName: "매장재고", children: [
			{field: "store_qty", headerName: "실재고", width: 60, type: 'currencyType'},
			{field: "store_wqty", headerName: "보유재고", width: 60, type: 'currencyType'},
		]},
        {field: "qty", headerName: "요청수량", width: 60, type: 'currencyType'},
        {field: "return_amt", headerName: "요청금액", width: 80, type: 'currencyType'},
		{field: "return_p_qty", headerName: "처리수량", width: 60, type: 'currencyType',
			editable: (params) => checkIsEditable(params),
			cellStyle: (params) => checkIsEditable(params) ? {"background-color": "#ffff99"} : {}
		},
		{field: "return_p_amt", headerName: "처리금액", width: 80, type: 'currencyType'},
        {field: "fixed_return_qty", headerName: "확정수량", width: 60, type: 'currencyType', hide: true},
        {field: "fixed_return_price", headerName: "확정금액", width: 80, type: 'currencyType', hide: true},
		{field: "reject_reason", hide: true},
		{field: "reject_reason_val", headerName: "반품거부사유", width: 90,
			editable: (params) => checkIsEditable(params),
			cellStyle: (params) => checkIsEditable(params) ? {"background-color": "#ffff99"} : {},
			cellEditor: 'agRichSelectCellEditor',
			cellEditorPopup: true,
			cellEditorParams: {
				values: reject_reasons.map(rs => rs.code_val),
				formatValue: (value) => {
					let code_id = reject_reasons.find(rs => rs.code_val === value)?.code_id;
					return `${code_id ? '[' + code_id + '] ' : ''}${value}`;
				},
			},
		},
		{field: "reject_comment", headerName: "반품거부메모", width: 200,
			editable: (params) => checkIsEditable(params),
			cellStyle: (params) => checkIsEditable(params) ? {"background-color": "#ffff99"} : {},
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
        pApp.ResizeGrid(380);
        pApp.BindSearchEnter();
        let gridDiv = document.querySelector(pApp.options.gridId);

		if (now_state >= 30) {
			columns = columns.map(col => ['fixed_return_qty', 'fixed_return_price', 'fixed_comment'].includes(col.field) ? { ...col, hide: false } : col);
		}

        gx = new HDGrid(gridDiv, columns, {
            pinnedTopRowData: pinnedRowData,
            getRowStyle: (params) => { // 고정된 row styling
                if (params.node.rowPinned)  return { 'font-weight': 'bold', 'background': '#eee', 'border': 'none'};
            },
            getRowNodeId: (data) => data.hasOwnProperty('count') ? data.count : "0", // 업데이터 및 제거를 위한 식별 ID를 count로 할당
            onCellValueChanged: (e) => {
                if (e.column.colId === "return_p_qty") {
                    if (isNaN(e.newValue) == true || e.newValue === "") {
                        alert("숫자만 입력가능합니다.");
                        gx.gridOptions.api.startEditingCell({ rowIndex: e.rowIndex, colKey: e.column.colId });
                    } else if(e.newValue < 0) {
                        alert("음수는 입력할 수 없습니다.");
                        gx.gridOptions.api.startEditingCell({ rowIndex: e.rowIndex, colKey: e.column.colId });
					} else if(e.column.colId === "return_p_qty" && e.data.store_wqty < parseInt(e.data.return_p_qty)) {
						alert("해당 매장의 보유재고보다 많은 수량을 반품할 수 없습니다.");
						gx.gridOptions.api.startEditingCell({ rowIndex: e.rowIndex, colKey: e.column.colId });
                    } else {
						e.node.setDataValue('return_p_amt', parseInt(e.data.return_p_qty) * parseInt(e.data.return_price));
                        updatePinnedRow();
                    }
                } else if (e.column.colId === "reject_reason_val") {
					e.node.setDataValue('reject_reason', reject_reasons.find(rs => rs.code_val === e.value)?.code_id || '');
				}
            }
        });
        GetProducts();

        $("#store_no").on("change", function(e) {
            gx.gridOptions.api.setRowData([]);
            updatePinnedRow();
        });
    });

    // 등록된 상품리스트 가져오기
    function GetProducts() {
        let data = "sr_cd=" + '{{ @$sr->sr_cd }}';
        gx.Request('/shop/stock/stk30/search-return-products', data, -1, function(e) {
            updatePinnedRow();
        });
    }

    // 매장반품 수정
    function Save() {
        let rows = gx.getRows();

        let sr_cd = '{{ @$sr->sr_cd }}';

		if(now_state >= 30) return alert("반품처리중 이후의 내역은 수정할 수 없습니다.");
		if(!confirm("임시저장하시겠습니까?")) return;

        axios({
            url: '/shop/stock/stk30/update',
            method: 'put',
            data: {
                sr_cd,
                products: rows.map(r => ({ ...r, return_qty: r.qty })),
            },
        }).then(function (res) {
            if(res.data.code === 200) {
                alert(res.data.msg);
                opener.Search();
                window.close();
            } else {
                console.log(res.data);
                alert("임시저장 중 오류가 발생했습니다.\n관리자에게 문의해주세요.");
            }
        }).catch(function (err) {
            console.log(err);
        });
    }

    const checkIsEditable = (params) => {
        return now_state == '10' && params.data.hasOwnProperty('isEditable') && params.data.isEditable ? true : false;
    };

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
	function UpgradeState() {
		let rows = gx.getRows();
		
		let reject_reason_empty_list = rows.filter(row => row.qty != row.return_p_qty && !row.reject_reason);
		if (reject_reason_empty_list.length > 0) return alert("요청수량과 처리수량을 다르게 입력할 경우, 해당 항목의 반품거부사유를 반드시 선택해주세요.");

		if(!confirm("반품처리중 상태로 변경하시겠습니까?")) return;

		let sr_cd = '{{ @$sr->sr_cd }}';

		axios({
			url: '/shop/stock/stk30/update',
			method: 'put',
			data: {
				sr_cd,
				new_state: 30,
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

    function openShopProduct(prd_no){
        var url = '/shop/product/prd01/' + prd_no;
        var product = window.open(url, "_blank", "toolbar=no,scrollbars=yes,resizable=yes,status=yes,top=500,left=500,width=1024,height=900");
    }
</script>
@stop

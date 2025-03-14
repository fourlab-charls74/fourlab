@extends('store_with.layouts.layout-nav')
@php
    $title = "창고간상품이동 등록";
    if($cmd == "update") $title = "창고간상품이동 관리";
@endphp
@section('title', $title)
@section('content')
<div class="show_layout py-3 px-sm-3">
    <div class="page_tit d-flex justify-content-between">
        <div class="d-flex">
            <h3 class="d-inline-flex">{{ $title }}</h3>
            <div class="d-inline-flex location">
                <span class="home"></span>
                <span>/ 상품관리</span>
                <span>/ 창고간상품이동</span>
            </div>
        </div>
        <div class="d-flex">
            @if(@$cmd == 'add' or @$sgr->sgr_state == '10')
            <a href="javascript:void(0)" onclick="Save('{{ @$cmd }}')" class="btn btn-primary mr-1"><i class="fas fa-save fa-sm text-white-50 mr-1"></i> 저장</a>
            @endif
            <a href="javascript:void(0)" onclick="window.close();" class="btn btn-outline-primary"><i class="fas fa-times fa-sm mr-1"></i> 닫기</a>
        </div>
    </div>

    <style> 
        .table th {min-width: 160px;}
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
											<th>이동코드</th>
											<td>
												<div class="form-inline">
													<p id="sgr_cd" class="fs-14">@if(@$sgr != null) {{ @$sgr->sgr_cd }} @else {{ @$new_sgr_cd }} @endif</p>
												</div>
											</td>
	                                        <th class="required">출고창고</th>
	                                        <td>
		                                        <div class="form-inline">
			                                        <select name='storage_cd' id="storage_cd" class="form-control form-control-sm w-100" @if(@$cmd == 'update') disabled @endif>
				                                        @foreach (@$storages as $storage)
					                                        <option value='{{ $storage->storage_cd }}' @if(@$cmd == 'update' && $sgr->storage_cd == $storage->storage_cd) selected @endif>{{ $storage->storage_nm }}</option>
				                                        @endforeach
			                                        </select>
		                                        </div>
	                                        </td>
											<th class="required">이동창고</th>
											<td>
												<div class="form-inline inline_select_box">
													@if(@$cmd == 'add')
														<div class="d-flex w-100">
															<select name="target_cd" id="target_cd" class="form-control form-control-sm w-100">
																<option value="">선택</option>
																@foreach (@$storages as $storage)
																	<option value='{{ $storage->storage_cd }}' @if(@$cmd == 'update' && $sgr->target_cd == $storage->storage_cd) selected @endif>{{ $storage->storage_nm }}</option>
																@endforeach
															</select>
														</div>
													@else
														<input type="text" name="target_nm" id="target_nm" value="{{ @$sgr->target_nm }}" class="form-control form-control-sm w-100" readonly />
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
                    <button type="button" onclick="delGoods();" class="btn btn-sm btn-outline-primary shadow-sm" id="add_row_btn"><i class="bx bx-trash"></i> 삭제</button>
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
    const now_state = '{{ @$sgr->sgr_state }}';
    const pinnedRowData = [{ prd_cd: '합계', qty: 0, total_return_price: 0 }];

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
        {field: "prd_cd", headerName: "바코드", pinned: 'left', width: 130},
        {field: "goods_no", headerName: "온라인코드", pinned: 'left', width: 70, cellStyle: {"text-align": "center"}},
        {field: "opt_kind_nm", headerName: "품목", width: 70, cellStyle: {"text-align": "center"}},
        {field: "brand", headerName: "브랜드", width: 70, cellStyle: {"text-align": "center"}},
        {field: "style_no",	headerName: "스타일넘버", width: 70, cellStyle: {"text-align": "center"}},
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
        {field: "prd_cd_p", headerName: "품번", width: 100, cellStyle: {"text-align": "center"}},
        {field: "color", headerName: "컬러", width: 55, cellStyle: {"text-align": "center"}},
        {field: "size", headerName: "사이즈", width: 55, cellStyle: {"text-align": "center"}},
        {field: "goods_opt", headerName: "옵션", width: 150},
        {field: "goods_sh", headerName: "정상가", type: "currencyType", width: 70},
        {field: "price", headerName: "현재가", type: "currencyType", width: 70},
        {field: "return_price", headerName: "이동단가", width: 70, type: 'currencyType', hide:true,
            editable: (params) => checkIsEditable(params),
            cellStyle: (params) => checkIsEditable(params) ? {"background-color": "#ffff99"} : {}
        },
        {field: "storage_wqty", headerName: "창고재고", width: 60, type: 'currencyType'},
        {field: "qty", headerName: "이동수량", width: 60, type: 'currencyType', 
            editable: (params) => checkIsEditable(params),
            cellStyle: (params) => checkIsEditable(params) ? {"background-color": "#ffff99"} : {}
        },
        {field: "total_return_price", headerName: "이동금액", width: 80, type: 'currencyType'},
		{field: "comment", headerName: "메모", width: 250,
			editable: (params) => checkIsEditable(params),
			cellStyle: (params) => checkIsEditable(params) ? {"background-color": "#ffff99"} : {}
		},
        {width: 0}
    ];
</script>

<script type="text/javascript" charset="utf-8">
    let gx;
    const pApp = new App('', { gridId: "#div-gd" });

    let storages = <?= json_encode(@$storages) ?> ;

    $(document).ready(function() {
        pApp.ResizeGrid(385);
        pApp.BindSearchEnter();
        let gridDiv = document.querySelector(pApp.options.gridId);
        gx = new HDGrid(gridDiv, columns, {
            pinnedTopRowData: pinnedRowData,
            getRowStyle: (params) => { // 고정된 row styling
                if (params.node.rowPinned)  return { 'font-weight': 'bold', 'background': '#eee', 'border': 'none'};
            },
            getRowNodeId: (data) => data.hasOwnProperty('count') ? data.count : "0", // 업데이터 및 제거를 위한 식별 ID를 count로 할당
            onCellValueChanged: (e) => {
                if (e.column.colId === "return_price" || e.column.colId === "qty") {
                    if (isNaN(e.newValue) == true || e.newValue == "") {
                        alert("숫자만 입력가능합니다.");
                        gx.gridOptions.api.startEditingCell({ rowIndex: e.rowIndex, colKey: e.column.colId });
                    } else if(e.newValue < 0) {
                        alert("음수는 입력할 수 없습니다.");
                        gx.gridOptions.api.startEditingCell({ rowIndex: e.rowIndex, colKey: e.column.colId });
                    } else {
                        if(e.column.colId === "qty" && e.data.storage_wqty < parseInt(e.data.qty)) {
                            alert("해당 창고의 보유재고보다 많은 수량을 이동할 수 없습니다.");
                            gx.gridOptions.api.startEditingCell({ rowIndex: e.rowIndex, colKey: e.column.colId });
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
        if('{{ @$cmd }}' === 'update') GetProducts();

        $("#storage_cd").on("change", function(e) {
            gx.gridOptions.api.setRowData([]);
            updatePinnedRow();
        });
    });

    // 등록된 상품리스트 가져오기
    function GetProducts() {
        let data = "sgr_cd=" + '{{ @$sgr->sgr_cd }}';
        gx.Request('/store/cs/cs04/search-return-products', data, -1, function(e) {
            updatePinnedRow();
        });
    }

    // 상품반품 등록
    function Save(cmd) {
        if(!cmd) return;

        let rows = gx.getRows();

        if(cmd === 'add') {
            //let sgr_date = document.f1.sdate.value;
            let storage_cd = document.f1.storage_cd.value;
            let target_cd = document.f1.target_cd.value;

            if(rows.length < 1) return alert("이동등록할 상품을 선택해주세요.");

            let zero_qtys = rows.filter(r => r.qty < 1);
            if(zero_qtys.length > 0) return alert("이동수량이 0개인 항목이 존재합니다.");

            let excess_qtys = rows.filter(r => (r.qty * 1) > (r.storage_wqty * 1));
            if (excess_qtys.length > 0) return alert("해당 창고의 보유재고보다 많은 수량을 이동할 수 없습니다.");
			if (target_cd == '') return alert('이동창고를 선택해주세요.');

            if(!confirm("등록하시겠습니까?")) return;

            axios({
                url: '/store/cs/cs04/add-storage-return',
                method: 'put',
                data: {
                    //sgr_date,
                    storage_cd,
                    target_cd,
                    products: rows.map(r => ({ prd_cd: r.prd_cd, price: r.price, return_price: r.return_price, return_qty: r.qty, comment : r.comment })),
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
            //let sgr_state = '{{ @$sgr->sgr_state }}';
            let sgr_cd = '{{ @$sgr->sgr_cd }}';

            if('{{ @$sgr->sgr_state }}' != 10) return alert("창고간이동이 '접수'상태일떄만 수정가능합니다.");
            if(!confirm("수정하시겠습니까?")) return;

            axios({
                url: '/store/cs/cs04/update-storage-return',
                method: 'put',
                data: {
                    sgr_cd,
                    products: rows.map(r => ({ sgr_prd_cd: r.sgr_prd_cd, return_price: r.return_price, return_qty: r.qty, comment : r.comment })),
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
        const url = `/store/api/goods/show?storage_cd=` + document.f1.storage_cd.value + "&include_not_match=Y";
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
        updatePinnedRow();
    }

    const updatePinnedRow = () => { // 총 반품금액, 반품수량을 반영한 PinnedRow를 업데이트
        let [ qty, total_return_price ] = [ 0, 0 ];
        const rows = gx.getRows();
        if (rows && Array.isArray(rows) && rows.length > 0) {
            rows.forEach((row, idx) => {
                qty += parseFloat(row.qty);
                total_return_price += parseFloat(row.total_return_price);
            });
        }

        let pinnedRow = gx.gridOptions.api.getPinnedTopRow(0);
        gx.gridOptions.api.setPinnedTopRowData([
            { ...pinnedRow.data, qty: qty, total_return_price: total_return_price }
        ]);
    };
</script>
@stop

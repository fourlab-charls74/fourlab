@extends('shop_with.layouts.layout-nav')
@section('title', '매장실사내역')
@section('content')
<div class="show_layout py-3 px-sm-3">
	<div class="page_tit d-flex justify-content-between">
		<div class="d-flex">
			<h3 class="d-inline-flex">매장실사내역</h3>
			<div class="d-inline-flex location">
				<span class="home"></span>
				<span>/ 매장관리</span>
				<span>/ 매장실사/LOSS관리</span>
				<span>/ 매장실사내역</span>
			</div>
		</div>
		<div class="d-flex align-items-center">
			@if($sc->sc_state == 'N')
				<a href="javascript:void(0)" onclick="Save()" class="btn btn-primary mr-1"><i class="fas fa-save fa-sm text-white-50 mr-1"></i> LOSS사유 저장</a>
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
												<p class="fs-14">{{ $sc->sc_date }}</p>
											</div>
										</td>
										<th class="required">매장</th>
										<td>
											<div class="form-inline inline_select_box">
												<input type="text" name="store_nm" id="store_nm" value="{{ @$sc->store_nm }}" class="form-control form-control-sm w-100" readonly />
											</div>
										</td>
										<th>실사코드</th>
										<td>
											<div class="form-inline">
												<p id="sc_cd" class="fs-14">@if(@$sc != null) {{ @$sc->sc_code }} ({{ @$sc->sc_type_nm }}) @endif</p>
											</div>
										</td>
									</tr>
									<tr>
										<th class="required">담당자</th>
										<td>
											<div class="form-inline">
												<p class="fs-14 py-2">{{ $sc->md_nm }}</p>
											</div>
										</td>
										<th>메모</th>
										<td colspan="3">
											<div class="form-inline">
												<p class="fs-14">{{ $sc->comment }}</p>
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
	const now_state = '{{ @$sc->sc_state }}';
	const pinnedRowData = [{ prd_cd: '합계', store_wqty: 0, qty: 0, loss_qty: 0, loss_price: 0 }];

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
		{field: "chk", headerName: '', pinned: 'left', cellClass: 'hd-grid-code', checkboxSelection: true, headerCheckboxSelection: true, sort: null, width: 29},
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
		{field: "goods_sh", headerName: "TAG가", type: "currencyType", width: 70},
		{field: "price", headerName: "판매가", type: "currencyType", width: 70},
		{field: "store_wqty", headerName: "매장보유재고", width: 90, type: 'currencyType'},
		{field: "qty", headerName: "실사재고", width: 60, type: 'currencyType'},
		{field: "loss_qty", headerName: "LOSS수량", width: 80, type: 'currencyType',
			cellStyle: (params) => ({ 'background-color': params.node.rowPinned !== 'top' && now_state == 'N' && (params.value > 0 || params.value < 0) ? '#ff9999' : 'inherit' }),
		},
		{field: "loss_rec_qty", headerName: "LOSS인정수량", width: 90, type: 'currencyType',
			cellStyle: (params) => ({ 'background-color': params.node.rowPinned !== 'top' && (params.value > 0 || params.value < 0) ? '#ff9999' : 'none' }),
		},
		{field: "loss_price", headerName: "LOSS금액", width: 80, type: 'currencyType',
			cellStyle: (params) => ({ 'background-color': params.node.rowPinned !== 'top' && (params.value > 0 || params.value < 0) ? '#ff9999' : 'inherit' }),
		},
		{field: "loss_tag_price", headerName: "TAG가 금액", width: 80, type: 'currencyType'},
		{field: "loss_price2", headerName: "현재가 금액", width: 80, type: 'currencyType'},
		{field: "loss_reason", hide: true},
		{field: "loss_reason_val", headerName: "LOSS사유", width: 90,
			editable: (params)=> params.node.rowPinned !== 'top' && now_state == 'N',
			cellClass: (params) => (['hd-grid-code', params.node.rowPinned !== 'top' && now_state == 'N' ? 'hd-grid-edit' : '']),
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
			editable: (params)=> params.node.rowPinned !== 'top' && now_state == 'N',
			cellClass: (params) => params.node.rowPinned !== 'top' && now_state == 'N' ? 'hd-grid-edit' : '',
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
		gx = new HDGrid(gridDiv, columns, {
			pinnedTopRowData: pinnedRowData,
			getRowStyle: (params) => { // 고정된 row styling
				if (params.node.rowPinned)  return { 'font-weight': 'bold', 'background': '#eee', 'border': 'none'};
			},
			getRowNodeId: (data) => data.hasOwnProperty('count') ? data.count : "0", // 업데이터 및 제거를 위한 식별 ID를 count로 할당
			onCellValueChanged: (e) => {
				if (e.column.colId === "loss_reason_val") {
					e.node.setDataValue('loss_reason', loss_reasons.find(rs => rs.code_val === e.value)?.code_id || '');
				}
			}
		});

		GetProducts();
	});

	// 등록된 상품리스트 가져오기
	function GetProducts() {
		let data = "sc_cd=" + '{{ @$sc->sc_cd }}';
		gx.Request('/shop/stock/stk26/search-check-products', data, -1, function(e) {
			updatePinnedRow();
		});
	}

	// LOSS사유 등록
	function Save() {
		let rows = gx.getRows();
		let sc_state = '{{ @$sc->sc_state }}';

		if(sc_state != 'N') return alert("매장LOSS등록 이전에만 저장가능합니다.");

		let not_reason_rows = rows.filter(row => (row.loss_qty > 0 || row.loss_qty < 0) && !row.loss_reason);
		if (not_reason_rows.length > 0) {
			if(!confirm("LOSS사유가 입력되지 않은 LOSS항목이 존재합니다.\nLOSS사유를 저장하시겠습니까?")) return;
		} else {
			if(!confirm("LOSS사유를 저장하시겠습니까?")) return;
		}

		axios({
			url: '/shop/stock/stk26/update',
			method: 'put',
			data: {
				products: rows.map(r => ({
					sc_prd_cd: r.sc_prd_cd,
					loss_reason: r.loss_reason,
					comment: r.comment
				})),
			},
		}).then(function (res) {
			if(res.data.code === '200') {
				alert("LOSS사유가 성공적으로 저장되었습니다.");
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

	const updatePinnedRow = () => { // 총 반품금액, 반품수량을 반영한 PinnedRow를 업데이트
		let [ store_wqty, qty, loss_qty, loss_price, loss_rec_qty, loss_price2, loss_tag_price ] = [ 0, 0, 0, 0, 0, 0, 0 ];
		const rows = gx.getRows();
		if (rows && Array.isArray(rows) && rows.length > 0) {
			rows.forEach((row, idx) => {
				store_wqty += parseInt(row.store_wqty || 0);
				qty += parseInt(row.qty || 0);
				loss_qty += parseInt(row.loss_qty || 0);
				loss_price += parseInt(row.loss_price || 0);
				loss_rec_qty += parseInt(row.loss_rec_qty || 0);
				loss_price2 += parseInt(row.loss_price2 || 0);
				loss_tag_price += parseInt(row.loss_tag_price || 0);
			});
		}

		let pinnedRow = gx.gridOptions.api.getPinnedTopRow(0);
		gx.gridOptions.api.setPinnedTopRowData([
			{ ...pinnedRow.data, store_wqty, qty, loss_qty, loss_price, loss_rec_qty, loss_price2, loss_tag_price }
		]);
	};
</script>
@stop

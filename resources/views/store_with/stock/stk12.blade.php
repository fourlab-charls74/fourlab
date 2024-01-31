@extends('store_with.layouts.layout')
@section('title','출고 > 초도출고')
@section('content')
	<div class="page_tit">
		<h3 class="d-inline-flex">초도출고</h3>
		<div class="d-inline-flex location">
			<span class="home"></span>
			<span>상품관리</span>
			<span>/ 상품출고관리</span>
			<span>/ 초도출고</span>
		</div>
	</div>
	<form method="get" name="search">
		<div id="search-area" class="search_cum_form">
			<div class="card mb-3">
				<div class="d-flex card-header justify-content-between">
					<h4>검색</h4>
					<div class="flax_box">
						<a href="javascript:void(0);" onclick="openBatchPopup()" class="btn btn-sm btn-outline-primary shadow-sm pl-2 mr-1"><i class="bx bx-plus fs-16"></i> 엑셀 업로드</a>
						<a href="#" id="search_sbtn" onclick="Search();" class="btn btn-sm btn-primary shadow-sm mr-1"><i class="fas fa-search fa-sm text-white-50"></i> 검색</a>
						<a href="/store/stock/stk10" class="btn btn-sm btn-outline-primary shadow-sm pl-2 mr-1"><i class="fas fa-step-backward fa-sm"></i> 출고 리스트</a>
						<div id="search-btn-collapse" class="btn-group mb-0 mb-sm-0"></div>
					</div>
				</div>
				<div class="card-body">
					<div class="row">
						<div class="col-lg-4 inner-td">
							<div class="form-group">
								<label for="good_types">판매채널/매장구분</label>
								<div class="d-flex align-items-center">
									<div class="flex_box w-100">
										<select name='store_channel[]' id="store_channel" class="form-control form-control-sm multi_select" multiple onchange="chg_store_channel_multi();">
											<option value=''>전체</option>
											@foreach ($store_channel as $sc)
												<option value='{{ $sc->store_channel_cd }}'>{{ $sc->store_channel }}</option>
											@endforeach
										</select>
									</div>
									<span class="mr-2 ml-2">/</span>
									<div class="flex_box w-100">
										<select id='store_channel_kind' name='store_channel_kind[]' class="form-control form-control-sm multi_select" multiple disabled>
											<option value=''>전체</option>
											@foreach ($store_kind as $sk)
												<option value='{{ $sk->store_kind_cd }}'>{{ $sk->store_kind }}</option>
											@endforeach
										</select>
									</div>
								</div>
							</div>
						</div>
						<div class="col-lg-4 inner-td">
							<div class="form-group">
								<label>매장명</label>
								<div class="form-inline inline_btn_box">
									<input type='hidden' id="store_nm" name="store_nm">
									<select id="store_no" name="store_no[]" class="form-control form-control-sm select2-store multi_select" multiple></select>
									<a href="javascript:void(0);" class="btn btn-sm btn-outline-primary sch-store"><i class="bx bx-dots-horizontal-rounded fs-16"></i></a>
								</div>
							</div>
						</div>
						<div class="col-lg-4 inner-td">
							<div class="form-group">
								<label>송장번호</label>
								<div class="form-inline">
									<div class="form-inline-inner input-box w-100">
										<div class="form-inline inline_btn_box">
											<input type='text' id="invoice_no" name='invoice_no' class="form-control form-control-sm w-100 ac-style-no search-enter">
											<a href="#" class="btn btn-sm btn-outline-primary sch-invoice"><i class="bx bx-dots-horizontal-rounded fs-16"></i></a>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-lg-4 inner-td">
							<div class="form-group">
								<label for="prd_cd">바코드</label>
								<div class="flex_box">
									<input type='text' id="prd_cd" name='prd_cd' class="form-control form-control-sm ac-style-no search-enter">
									<a href="#" class="btn btn-sm btn-outline-primary sch-prdcd" hidden><i class="bx bx-dots-horizontal-rounded fs-16"></i></a>
								</div>
							</div>
						</div>
						<div class="col-lg-4 inner-td">
							<div class="form-group">
								<label for="goods_nm">상품명</label>
								<div class="flax_box">
									<input type='text' class="form-control form-control-sm ac-goods-nm search-enter" name='goods_nm' id="goods_nm" value=''>
								</div>
							</div>
						</div>
						<div class="col-lg-4 inner-td">
							<div class="form-group">
								<label for="goods_nm_eng">상품명(영문)</label>
								<div class="flax_box">
									<input type='text' class="form-control form-control-sm ac-goods-nm-eng search-enter" name='goods_nm_eng' id="goods_nm_eng" value=''>
								</div>
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-lg-4 inner-td">
							<div class="form-group">
								<label for="prd_cd">상품검색조건</label>
								<div class="form-inline">
									<div class="form-inline-inner input-box w-100">
										<div class="form-inline inline_btn_box">
											<input type='hidden' id="prd_cd_range" name='prd_cd_range'>
											<input type='text' id="prd_cd_range_nm" name='prd_cd_range_nm' onclick="openApi();" class="form-control form-control-sm w-100 ac-style-no" readonly style="background-color: #fff;">
											<a href="#" class="btn btn-sm btn-outline-primary sch-prdcd-range"><i class="bx bx-dots-horizontal-rounded fs-16"></i></a>
										</div>
									</div>
								</div>
							</div>
						</div>
						<div class="col-lg-4 inner-td">
							<div class="form-group">
								<label for="">자료수/정렬</label>
								<div class="form-inline">
									<div class="form-inline-inner input_box" style="width:24%;">
										<select name="limit" class="form-control form-control-sm">
											<option value="1000">1000</option>
											<option value="2000">2000</option>
											<option value="5000">5000</option>
										</select>
									</div>
									<span class="text_line">/</span>
									<div class="form-inline-inner input_box" style="width:45%;">
										<select name="ord_field" class="form-control form-control-sm">
											<option value="goods_no">온라인코드</option>
											<option value="prd_cd">바코드</option>
										</select>
									</div>
									<div class="form-inline-inner input_box sort_toggle_btn" style="width:24%;margin-left:1%;">
										<div class="btn-group" role="group">
											<label class="btn btn-primary primary" for="sort_desc" data-toggle="tooltip" data-placement="top" title="내림차순"><i class="bx bx-sort-down"></i></label>
											<label class="btn btn-secondary" for="sort_asc" data-toggle="tooltip" data-placement="top" title="오름차순"><i class="bx bx-sort-up"></i></label>
										</div>
										<input type="radio" name="ord" id="sort_desc" value="desc" checked="">
										<input type="radio" name="ord" id="sort_asc" value="asc">
									</div>
								</div>
							</div>
						</div>
					</div>
					<div class="row search-area-ext d-none">
						<div class="col-lg-4 inner-td">
							<div class="form-group">
								<label for="style_no">스타일넘버/온라인코드</label>
								<div class="form-inline">
									<div class="form-inline-inner input_box">
										<input type='text' class="form-control form-control-sm ac-style-no search-enter" name='style_no' id="style_no" value="{{ $style_no }}">
									</div>
									<span class="text_line">/</span>
									<div class="form-inline-inner input-box" style="width:47%">
										<div class="form-inline-inner inline_btn_box">
											<input type='text' class="form-control form-control-sm w-100 search-enter" name='goods_no' id='goods_no' value=''>
											<a href="#" class="btn btn-sm btn-outline-primary sch-goods_nos"><i class="bx bx-dots-horizontal-rounded fs-16"></i></a>
										</div>
									</div>
								</div>
							</div>
						</div>
						<div class="col-lg-4 inner-td">
							<div class="form-group">
								<label for="name">공급업체</label>
								<div class="form-inline inline_select_box">
									<div class="form-inline-inner input-box w-100">
										<div class="form-inline inline_btn_box">
											<input type="hidden" id="com_cd" name="com_cd" />
											<input onclick="" type="text" id="com_nm" name="com_nm" class="form-control form-control-sm search-all search-enter" style="width:100%;" autocomplete="off" />
											<a href="#" class="btn btn-sm btn-outline-primary sch-sup-company"><i class="bx bx-dots-horizontal-rounded fs-16"></i></a>
										</div>
									</div>
								</div>
							</div>
						</div>
						<div class="col-lg-4 inner-td">
							<div class="form-group">
								<label for="brand_cd">브랜드</label>
								<div class="form-inline inline_btn_box">
									<select id="brand_cd" name="brand_cd" class="form-control form-control-sm select2-brand"></select>
									<a href="#" class="btn btn-sm btn-outline-primary sch-brand"><i class="bx bx-dots-horizontal-rounded fs-16"></i></a>
								</div>
							</div>
						</div>
					</div>
					<div class="row search-area-ext d-none">

					</div>
				</div>
			</div>
			<div class="resul_btn_wrap mb-3">
				<a href="#" id="search_sbtn" onclick="Search();" class="btn btn-sm btn-primary shadow-sm mr-1"><i class="fas fa-search fa-sm text-white-50"></i> 검색</a>
				<div class="search_mode_wrap btn-group mr-2 mb-0 mb-sm-0"></div>
			</div>
		</div>
	</form>
	<!-- DataTales Example -->
	<div class="card shadow mb-0 last-card pt-2 pt-sm-0">
		<div class="card-body">
			<div class="card-title">
				<div class="filter_wrap">
					<div class="fl_box">
						<h6 class="m-0 font-weight-bold">총 : <span id="gd-total" class="text-primary">0</span>건</h6>
					</div>
					<div class="d-flex flex-grow-1 flex-column flex-lg-row justify-content-end align-items-end align-items-lg-center">
						{{-- <div class="d-flex mb-1 mb-lg-0">
							<span class="mr-1">창고</span>
							<select id='storage' name='storage' class="form-control form-control-sm"  style='width:160px;display:inline'>
								<option value=''>선택</option>
								@foreach ($storages as $storage)
									<option value='{{ $storage->storage_cd }}' @if($storage->default_yn == "Y") selected @endif>{{ $storage->storage_nm }} @if($storage->default_yn == "Y") (대표) @endif </option>
								@endforeach
							</select>
						</div> --}}
						<div class="d-flex">
							<div class="custom-control custom-checkbox form-check-box pr-2" style="display:inline-block;">
								<input type="checkbox" class="custom-control-input" name="ext_storage_qty" id="ext_storage_qty" value="Y" checked>
								<label class="custom-control-label font-weight-normal" for="ext_storage_qty">창고재고 0 제외</label>
							</div>
						</div>
						<span class="d-none d-lg-block mr-2 tex-secondary">|</span>
						<div class="d-flex mr-1 mb-1 mb-lg-0">
							<span class="mr-1">출고예정일</span>
							<div class="docs-datepicker form-inline-inner input_box" style="width:130px;display:inline;">
								<div class="input-group">
									<input type="text" class="form-control form-control-sm docs-date bg-white" name="exp_dlv_day" value="{{ $today }}" autocomplete="off" readonly />
									<div class="input-group-append">
										<button type="button" class="btn btn-outline-secondary docs-datepicker-trigger p-0 pl-2 pr-2">
											<i class="fa fa-calendar" aria-hidden="true"></i>
										</button>
									</div>
								</div>
								<div class="docs-datepicker-container"></div>
							</div>
						</div>
						<div class="d-flex">
							<select id='rel_order' name='rel_order' class="form-control form-control-sm mr-2"  style='width:70px;display:inline'>
								@foreach ($rel_order_res as $rel_order)
									<option value='{{ $rel_order->code_val}}'>{{ $rel_order->code_val}}</option>
								@endforeach
							</select>
							<a href="#" onclick="requestRelease();" class="btn btn-sm btn-primary shadow-sm pl-2"><i class="fas fa-sm text-white-50"></i>출고요청</a>
						</div>
						<div class="fr_box" hidden>
							<button type="button" id="gd-setting" class="setting-grid-col ml-2"><i class="fas fa-cog text-primary"></i></button>
						</div>
					</div>
				</div>
			</div>
			<div class="table-responsive">
				<div id="div-gd" style="height:calc(100vh - 370px);width:100%;" class="ag-theme-balham"></div>
			</div>
		</div>
	</div>
	<script language="javascript">
		const pinnedRowData = [{ style_no: '합계', storage_qty : 0, storage_wqty: 0 }];
		let columns = [
			{field: "chk", headerName: '', pinned: 'left', cellClass: 'hd-grid-code', headerCheckboxSelection: true, checkboxSelection: true, width: 28, sort: null},
			{field: "prd_cd", headerName: "바코드", pinned: 'left', width: 120, cellStyle: {"text-align": "center"}},
			{field: "goods_no", headerName: "온라인코드", pinned: 'left', width: 70, cellStyle: {"text-align": "center"}},
			{field: "opt_kind_nm", headerName: "품목", pinned: 'left', width: 80, cellStyle: {"text-align": "center"}},
			{field: "brand_nm", headerName: "브랜드", pinned: 'left', width: 60, cellStyle: {"text-align": "center"}},
			{field: "style_no",	headerName: "스타일넘버", pinned: 'left', width: 80, cellStyle: {"text-align": "center"}},
			{field: "goods_nm",	headerName: "상품명", pinned: 'left', type: 'HeadGoodsNameType', width: 250},
			{field: "goods_nm_eng",	headerName: "상품명(영문)", type: 'HeadGoodsNameType', width: 250},
			{field: "prd_cd_p", headerName: "품번", width: 100, cellStyle: {"text-align": "center"}},
			{field: "color", headerName: "컬러", width: 50, cellStyle: {"text-align": "center"}},
			{field: "color_nm", headerName: "컬러명", width: 100, cellStyle: {"text-align": "center"}},
			{field: "size", headerName: "사이즈", width: 50, cellStyle: {"text-align": "center"}},
			{
				headerName: '(대표)창고재고', // 대표창고의 재고를 조회
				children: [
					{field: "storage_qty", headerName: "재고", type: 'currencyType', width: 100},
					{field: "storage_wqty", headerName: "보유재고", type: 'currencyType',
						cellRenderer:function(params) {
							let storage_wqty = params.value;
							if (typeof storage_wqty === 'string' && storage_wqty.includes(' ')) {
								let qty = storage_wqty.split(' ');
								return qty[0] + " <span style='color:red'>" + qty[1] + "</span>";
							} else {
								return storage_wqty;
							}
						}
					},
				]
			},
		];

		function setColumn(stores) {
			if(!stores) return;
			columns.splice(13);

			for(let i = 0; i < stores.length; i++) {
				let cd = stores[i].store_cd;
				let nm = stores[i].store_nm;
				columns.push({
					field: cd,
					headerName: nm,
					children: [
						{field: cd + '_qty', headerName: '재고', type: "currencyType", width: 50,
							cellRenderer: function(params) {
								return params.data[cd + "_qty"] || 0;
							}
						},
						{field: cd + '_rel_qty', headerName: '배분수량', type: "currencyType", width: 80, valueFormatter: formatNumber,
							editable : (params) => params.node.rowPinned === 'top' ? false : true,
							cellStyle : (params) => params.node.rowPinned === 'top' ? {} : {'background-color' : '#ffff99'}
						},
					],
				});
			}
			columns.push({ width: "auto" });
			gx.gridOptions.api.setColumnDefs(columns);
		}
	</script>
	<script type="text/javascript" charset="utf-8">
		let gx;
		const pApp = new App('', { gridId: "#div-gd" });

		$(document).ready(function() {
			pApp.ResizeGrid(265);
			pApp.BindSearchEnter();
			let gridDiv = document.querySelector(pApp.options.gridId);
			gx = new HDGrid(gridDiv, columns, {
				pinnedTopRowData: pinnedRowData,
				getRowStyle: (params) => {
					if (params.node.rowPinned)  return {'font-weight': 'bold', 'background': '#eee !important', 'border': 'none'};
				},
				defaultColDef: {
					suppressMenu: true,
					resizable: false,
					sortable: true,
				},
				onCellValueChanged: (e) => {
					e.node.setSelected(true);
					if (e.column.colId.includes('rel_qty')) {
						if (isNaN(e.newValue) == true || e.newValue == "") {
							alert("숫자만 입력가능합니다.");
							gx.gridOptions.api.startEditingCell({ rowIndex: e.rowIndex, colKey: e.column.colId });

						} else {
							updatePinnedRow();

							if (isNaN(e.oldValue) == true) e.oldValue = undefined;	// 이전 데이터가 숫자가 아닐때 처리

							if (e.oldValue != undefined) {
								let oldValue = e.oldValue * 1;
								let newValue = e.newValue * 1;
								let qty = (parseInt(e.data.storage_wqty) + oldValue) - newValue;
								let total_qty = e.data.storage_wqty2 - qty;
								e.data.storage_wqty = qty + " (-" + total_qty + ")";
								gx.gridOptions.api.updateRowData({update: [e.data]});
							} else if (e.oldValue == undefined) {
								let newValue = e.newValue * 1;
								let qty = parseInt(e.data.storage_wqty) - newValue;
								let total_qty = e.data.storage_wqty2 - qty;
								e.data.storage_wqty = qty + " (-" + total_qty + ")";
								gx.gridOptions.api.updateRowData({update: [e.data]});
							}
						}
					}
				}
			});

			// 매장검색
			$( ".sch-store" ).on("click", function() {
				searchStore.Open(null, "multiple");
			});

			// 판매채널 선택되지않았을때 매장구분 disabled처리하는 부분
			// load_store_channel();

		});
		

		let stores = [];

		function Search() {
			let store_channel = $("#store_channel").val();
			let store_nos = $("[name='store_no[]']").val();
			if(store_nos.length < 1 && store_channel.length < 1) return alert("판매채널 또는 출고할 매장을 선택 후 검색해주세요.");

			let data = $('form[name="search"]').serialize();
			data += "&ext_storage_qty=" + $("[name=ext_storage_qty]").is(":checked");
			gx.Request('/store/stock/stk12/search', data, 1, function(d) {
				setColumn(d.head.stores);

				let t = d.head.total_data;
				stores = d.head.stores;

				const rows = gx.getSelectedRows();

				const pinnedRowData = {
					style_no : '합계',
					storage_qty: t.storage_qty,
					storage_wqty: t.storage_wqty
				};

				for (let i = 0; i < stores.length; i++) {
					let store_cd = stores[i].store_cd;
					pinnedRowData[`${store_cd}_qty`] = t[`${store_cd}_qty`] || 0;
					pinnedRowData[`${store_cd}_rel_qty`] = 0;
				}

				gx.gridOptions.api.setPinnedTopRowData([pinnedRowData]);
			});
		}

		function setInvoiceNo(invoice_no) {
			$("[name=invoice_no]").val(invoice_no);
		}

		// 출고요청
		function requestRelease() {
			let rows = gx.getSelectedRows();
			if(rows.length < 1) return alert("출고요청할 상품을 선택해주세요.");

			let rel_qty_values = [];

			for (let i = 0; i < rows.length; i++) {
				const prd_cd = rows[i].prd_cd;

				for (const key in rows[i]) {
					if (key.includes('_rel_qty')) {
						let keys = key;
						let keys_str = keys.split("_");
						let store_cd = keys_str[0];
						let rel_qty = parseInt(rows[i][key]);

						rel_qty_values.push({
							prd_cd: prd_cd,
							store_cd: store_cd,
							rel_qty: rel_qty,
						});
					}
				}
			}

			let over_qty_rows = rows.filter(r => {
				let cnt = 0;
				for (let store of stores) {
					let q = r[store.store_cd + '_rel_qty']?? 0;

					console.log(q);
					cnt += parseInt(q);
				}
				if (cnt > (r.storage_wqty2 || 0)) return true;
				else return false;
			});

			if(over_qty_rows.length > 0) return alert(`대표창고의 보유재고보다 많은 수량을 요청하실 수 없습니다.\n바코드 : ${over_qty_rows.map(o => o.prd_cd).join(", ")}`);

			if(!confirm("해당 상품을 출고요청하시겠습니까?")) return;

			const data = {
				products: rows,
				rel_qty_values : rel_qty_values,
				exp_dlv_day: $('[name=exp_dlv_day]').val(),
				rel_order: $('[name=rel_order]').val(),
			};

			axios({
				url: '/store/stock/stk12/request-release',
				method: 'post',
				data: data,
			}).then(function (res) {
				if(res.data.code === 200) {
					if(!confirm(res.data.msg + "\n출고요청을 계속하시겠습니까?")) {
						location.href = "/store/stock/stk10";
					} else {
						Search();
					}
				} else {
					console.log(res.data);
					alert("출고요청 중 오류가 발생했습니다.\n관리자에게 문의해주세요.");
				}
			}).catch(function (err) {
				console.log(err);
			});
		}

		function openApi() {
			document.getElementsByClassName('sch-prdcd-range')[0].click();
		}

		// 초도출고 엑셀 업로드 팝업 오픈
		const openBatchPopup = () => {
			const url = '/store/stock/stk12/batch';
			window.open(url, "_blank", "toolbar=no,scrollbars=yes,resizable=yes,status=yes,top=300,left=300,width=1700,height=880");
		}

		function updatePinnedRow() {
			const rows = gx.getSelectedRows();

			let store_rel_qty_map = {};

			for (let i = 0; i < rows.length; i++) {
				for (const key in rows[i]) {
					if (key.includes('_rel_qty')) {
						let keys = key;
						let keys_str = keys.split("_");
						let store_cd = keys_str[0];

						if (!store_rel_qty_map[store_cd]) {
							store_rel_qty_map[store_cd] = 0;
						}

						store_rel_qty_map[store_cd] += parseInt(rows[i][key]);
					}
				}
			}

			let pinnedRow = gx.gridOptions.api.getPinnedTopRow(0);

			for (const store_cd in store_rel_qty_map) {
				pinnedRow.data[`${store_cd}_rel_qty`] = store_rel_qty_map[store_cd];
			}

			gx.gridOptions.api.setPinnedTopRowData([pinnedRow.data]);
		}

	</script>
@stop

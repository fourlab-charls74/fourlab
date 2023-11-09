@extends('store_with.layouts.layout')
@section('title','배분현황')
@section('content')

<div class="page_tit">
	<h3 class="d-inline-flex">배분현황</h3>
	<div class="d-inline-flex location">
		<span class="home"></span>
		<span>/ 영업관리</span>
		<span>/ 배분현황</span>
	</div>
</div>

<div id="search-area" class="search_cum_form">
	<form method="get" name="search">
		<div class="card mb-3">
			<div class="d-flex card-header justify-content-between">
				<h4>검색</h4>
				<div>
					<a href="#" id="search_sbtn" onclick="return Search();" class="btn btn-sm btn-primary shadow-sm"><i class="fas fa-search fa-sm text-white-50"></i> 조회</a>
					<a href="javascript:void(0);" class="export-excel btn btn-sm btn-outline-primary shadow-sm"><i class="bx bx-download fs-16"></i> 엑셀다운로드</a>
					<div id="search-btn-collapse" class="btn-group mb-0 mb-sm-0"></div>
				</div>
			</div>
			<div class="card-body">
				<div class="row">
                    <div class="col-lg-4 inner-td">
                        <div class="form-group">
							<label>배분일자</label>
                            <div class="form-inline">
                                <input type="text" class="form-control form-control-sm docs-date mr-2" name="baebun_date" id="baebun_date" value="" autocomplete="off" onclick="openApi();">
                                <a href="javascript:void(0);" class="btn btn-sm btn-outline-primary sch-baebun"><i class="bx bx-dots-horizontal-rounded fs-16"></i></a>
                                <input type="text" class="form-control form-control-sm ml-2 mr-1" name='rel' id="rel" style="width:70px;" onclick="openApi();">차
                            </div>
						</div>
                    </div>
                    <div class="col-lg-4 inner-td">
                        <div class="form-group">
							<label>출고일자</label>
                            <div class="docs-datepicker flex_box">
								<div class="input-group">
								<input type="text" class="form-control form-control-sm docs-date" name="release_date" id="release_date" value="" autocomplete="off">
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
                    <div class="col-lg-4 inner-td">
                        <div class="form-group">
                            <label>배분구분</label>
                            <div class="form-inline form-check-box">
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input" name="baebun_type[0]" id="baebun_type_0" value="" onclick="changeBaebunType()" checked>
                                    <label class="custom-control-label" for="baebun_type_0">전체</label>
                                </div>
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input" name="baebun_type[1]" id="baebun_type_1" value="F" onclick="changeBaebunType()">
                                    <label class="custom-control-label" for="baebun_type_1">초도</label>
                                </div>
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input" name="baebun_type[2]" id="baebun_type_2" value="S" onclick="changeBaebunType()">
                                    <label class="custom-control-label" for="baebun_type_2">판매</label>
                                </div>
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input" name="baebun_type[3]" id="baebun_type_3" value="R" onclick="changeBaebunType()">
                                    <label class="custom-control-label" for="baebun_type_3">요청</label>
                                </div>
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input" name="baebun_type[4]" id="baebun_type_4" value="G" onclick="changeBaebunType()">
                                    <label class="custom-control-label" for="baebun_type_4">일반</label>
                                </div>
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input" name="baebun_type[5]" id="baebun_type_5" value="O" onclick="changeBaebunType()">
                                    <label class="custom-control-label" for="baebun_type_5">온라인</label>
                                </div>
                            </div>
                        </div>
                    </div>
				</div>
				<div class="row">
					<div class="col-lg-4 inner-td">
						<div class="form-group">
							<label for="">정렬</label>
							<div class="form-inline">
								<div class="form-inline-inner input_box w-100">
									<select name="ord_field" class="form-control form-control-sm">
										<option value="store">매장 - 상품별</option>
										<option value="product">상품 - 매장별</option>
									</select>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="resul_btn_wrap mb-3">
			<a href="#" id="search_sbtn" onclick="return Search();" class="btn btn-sm btn-primary shadow-sm"><i class="fas fa-search fa-sm text-white-50"></i> 조회</a>
			<a href="javascript:void(0);" class="export-excel btn btn-sm btn-outline-primary shadow-sm"><i class="bx bx-download fs-16"></i> 엑셀다운로드</a>
			<div class="search_mode_wrap btn-group mr-2 mb-0 mb-sm-0"></div>
		</div>
	</form>
</div>

<div id="filter-area" class="card shadow-none mb-0 search_cum_form ty2 last-card">
	<div class="card-body shadow">
		<div class="card-title mb-3">
			<div class="filter_wrap">
				<div class="d-flex justify-content-between">
					<h6 class="m-0 font-weight-bold">총 <span id="gd-total" class="text-primary">0</span> 건</h6>
					<div class="d-flex">
						<div class="custom-control custom-checkbox form-check-box pr-2" style="display:inline-block;">
							<input type="checkbox" class="custom-control-input" name="grid_expand" id="grid_expand" onchange="return setAllRowGroupExpanded(this.checked);" checked>
							<label class="custom-control-label font-weight-normal" for="grid_expand">항목펼쳐보기</label>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="table-responsive">
			<div id="div-gd" style="height:calc(100vh - 370px);width:100%;" class="ag-theme-balham"></div>
		</div>
	</div>
</div>


	<!-- 배분현황 배분일자 -->
	<!-- sample modal content -->
	<div id="SearchBaebunModal" class="modal fade" role="dialog" aria-labelledby="SearchBaebunModalLabel" aria-hidden="true">
        <div class="modal-dialog" style="max-width: 790px;">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title mt-0" id="myModalLabel">배분일자차수조회</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body show_layout" style="background:#f5f5f5;">
                    <div class="card_wrap search_cum_form write">
                        <div class="card shadow">
                            <form name="search_baebun" method="get" onsubmit="return false">
                                <div class="card-body">
                                    <div class="row_wrap">
                                        <div class="row">
                                            <div class="col-lg-12 inner-td">
                                                <div class="form-group">
                                                    <label for="">조회기준</label>
                                                    <div class="form-inline">
                                                        <div class="docs-datepicker form-inline-inner input_box">
                                                            <div class="input-group">
                                                                <input type="text" class="form-control form-control-sm docs-date" name="sdate" value="{{$sdate}}" autocomplete="off">
                                                                <div class="input-group-append">
                                                                    <button type="button" class="btn btn-outline-secondary docs-datepicker-trigger p-0 pl-2 pr-2">
                                                                        <i class="fa fa-calendar" aria-hidden="true"></i>
                                                                    </button>
                                                                </div>
                                                            </div>
                                                            <div class="docs-datepicker-container"></div>
                                                        </div>
                                                        <span class="text_line">~</span>
                                                        <div class="docs-datepicker form-inline-inner input_box">
                                                            <div class="input-group">
                                                                <input type="text" class="form-control form-control-sm docs-date" name="edate" value="{{$edate}}" autocomplete="off">
                                                                <div class="input-group-append">
                                                                    <button type="button" class="btn btn-outline-secondary docs-datepicker-trigger p-0 pl-2 pr-2">
                                                                        <i class="fa fa-calendar" aria-hidden="true"></i>
                                                                    </button>
                                                                </div>
                                                            </div>
                                                            <div class="docs-datepicker-container"></div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="resul_btn_wrap" style="padding-top:7px;text-align:right;display:block;">
                                        <a href="javascript:void(0);" id="search_store_sbtn" onclick="return searchBaebun.Search();" class="btn btn-sm btn-primary shadow-sm"><i class="fas fa-search fa-sm text-white-50"></i> 조회</a>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <div class="card shadow mb-1 pt-0">
                            <div class="card-body m-0">
                                <div class="card-title">
                                    <div class="filter_wrap">
                                        <div class="fl_box">
                                            <h6 class="m-0 font-weight-bold">총 : <span id="gd-baebun-total" class="text-primary">0</span> 건</h6>
                                        </div>
                                    </div>
                                </div>
                                <div class="table-responsive">
                                    <div id="div-gd-baebun" style="width:100%;height:300px;" class="ag-theme-balham"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->

<style>
	.ag-row-level-1 {background-color: #f2f2f2 !important;}
	.ag-row-level-2 {background-color: #e2e2e2 !important;}
</style>

<script>
	class CustomHeader {
		init(params) {
			this.eGui = document.createElement('div');
			this.eGui.classList.add('ag-cell-label-container');
			this.eGui.classList.add('ag-header-cell-sorted-none');
			this.eGui.innerHTML = `<span class="w-100 d-flex flex-column align-items-center">${params.displayName.split('\n').map(p => '<span style="height: 15px;">' + (p === ' ' ? '' : p) + '</span>').join('')}</span>`;
		}

		getGui() {
			return this.eGui;
		}

		refresh(params) {
			return false;
		}
	}
	const pinnedRowData = [{ qty : 0 }];
	const sumValuesFunc = (params) => params.values.reduce((a,c) => a + ((c || 0) * 1), 0);

	const columns_store = [
		{field: "baebun_type",	headerName: "배분구분", pinned:'left', width: 80, cellClass: 'hd-grid-code',
			aggFunc: (params) => params.values.length > 0 ? params.values[0] : '',
			cellRenderer: (params) => params.node.level == 0 ? params.value : '',
		},
		{field: "store_cd",	headerName: "매장코드",	width: 80, pinned:'left', cellClass: 'hd-grid-code',
			aggFunc: (params) => params.values.length > 0 ? params.values[0] : '',
			cellRenderer: function(params) {
				if (params.node.level == 0) {
					return params.value;
				} else if (params.node.rowPinned === 'top') {
					return '합계';
				} else {
					return '';
				}
			}
		},
		{field: "store_nm", headerName: "매장명", rowGroup: true, hide: true},
		{headerName: '매장명', showRowGroup: 'store_nm', cellRenderer: 'agGroupCellRenderer', minWidth: 150, pinned: 'left'},
		{field: "prd_cd_p",	headerName: "품번", width: 120, pinned:'left'},
		{field: "goods_nm",	headerName: "상품명", width: 200, pinned:'left',
			cellRenderer: function (params) {
				if (params.value !== undefined) {
					if(params.data.goods_no == null) return '존재하지 않는 상품입니다.';
					return '<a href="#" onclick="return openStoreProduct(\'' + params.data.goods_no + '\');">' + params.value + '</a>';
				}
			}
		},
		{field: "color", headerName: "컬러", pinned:'left', width: 80, cellClass: 'hd-grid-code'},
		{field: "color_nm",	headerName: "컬러명", pinned:'left', width: 100},
		{field: "size_kind_nm_p",	headerName: "사이즈구분", pinned:'left', width: 100, cellStyle: { "text-align": "center" }},
		{field: "qty",	headerName: "수량",	pinned:'left', width: 80, type: "currencyType", aggFunc: sumValuesFunc},
	];
	
	const columns_product = [
		{field: "baebun_type",	headerName: "배분구분", pinned:'left', width: 80, cellClass: 'hd-grid-code', aggFunc: 'first',
			cellRenderer: (params) => params.node.level == 0 ? params.value : '',
		},
		{field: "prd_cd_p",	headerName: "품번", width: 120, pinned:'left', aggFunc: 'first',
			cellRenderer: (params) => params.node.rowPinned === 'top' ? '합계' : params.node.level == 0 ? params.value : '',
		},
		{field: "goods_nm",	headerName: "상품명", width: 200, pinned:'left', aggFunc: 'first',
			cellRenderer: function (params) {
				if (params.data?.goods_no == '' || params.node.aggData?.goods_no == '') {
					return '존재하지 않는 상품입니다.';
				} else if (params.node?.level !== 0) {
					return '';
				} else {
					let goods_no = params.data ? params.data.goods_no : params.node.aggData ? params.node.aggData.goods_no : '';
					return '<a href="#" onclick="return openStoreProduct(\'' + goods_no + '\');">' + params.value + '</a>';
				}
			}
		},
		{field: "color", hide: true, aggFunc: 'first'},
		{field: "prd_cd_p_color", headerName: "컬러", rowGroup: true, hide: true},
		{headerName: '컬러', showRowGroup: 'prd_cd_p_color', minWidth: 80, maxWidth: 100, pinned: 'left',
			cellRenderer: 'agGroupCellRenderer',
			cellRendererParams: {
				innerRenderer: (params) => params.node.level == 0 ? params.node?.aggData?.color : ''
			},
		},
		{field: "color_nm", headerName: "컬러명", pinned:'left', width: 100, aggFunc: 'first',
			cellRenderer: (params) => params.node.level == 0 ? params.value : '',
		},
		{field: "store_cd",	headerName: "매장코드",	width: 80, pinned:'left', cellClass: 'hd-grid-code'},
		{field: 'store_nm', headerName: '매장명', width: 120, pinned: 'left'},
		{field: "qty",	headerName: "수량",	pinned:'left', width: 80, type: "currencyType", aggFunc: sumValuesFunc},
	];

	const getCurrentColumn = () => $("[name=ord_field]").val() === 'store' ? columns_store : columns_product;

</script>
<script type="text/javascript" charset="utf-8">
	const pApp = new App('', {
		gridId: "#div-gd",
	});
	let gx;

	$(document).ready(function() {
		pApp.ResizeGrid(275);
		pApp.BindSearchEnter();
		let gridDiv = document.querySelector(pApp.options.gridId);
		gx = new HDGrid(gridDiv, getCurrentColumn(), {
			defaultColDef: {
				suppressMenu: true,
				resizable: true,
				autoHeight: true,
				sortable: true,
			},
			pinnedTopRowData: pinnedRowData,
			getRowStyle: (params) => {
                if (params.node.rowPinned)  return {'font-weight': 'bold', 'background': '#eee !important', 'border': 'none'};
            },
			rollup: true,
            groupSuppressAutoColumn: true,
			suppressAggFuncInHeader: true,
			enableRangeSelection: true,
			animateRows: true,
			headerHeight:100,
		});

		// 엑셀다운로드
		$(".export-excel").on("click", function (e) {
			if(!validation()) return;
			alert("엑셀다운로드가 진행되고있습니다.\n잠시만 기다려주세요.");

			let data = $('form[name="search"]').serialize();
			
			let cols = gx.gridOptions.api.getColumnDefs().filter(c => !c.hide);
			let sort = cols.filter(col => col.sort)?.[0];

			if ($("[name=ord_field]").val() === 'store') {
				cols = cols
					.map(c => c.showRowGroup === 'store_nm' ? c.showRowGroup : c.colId === 'store_nm' ? '' : c.colId)
					.join('^');
			} else {
				cols = cols
					.map(c => c.showRowGroup === 'color' ? c.showRowGroup : c.colId === 'color' ? '' : c.colId)
					.join('^');
			}
			data += "&columns=" + cols;
			
			if (sort) {
				let colId = sort.colId;
				if (colId === '0') colId = sort.showRowGroup || '';
				data += "&sort_id=" + colId;
				data += "&sort_type=" + sort.sort;
			}

			location.href = '/store/sale/sal29/download?' + data;
		});
	});

	function Search() {
		if(!validation()) return;
		let data = $('form[name="search"]').serialize();
		gx.Request('/store/sale/sal29/search', data, -1, function(e) {
			setColumn(e.head.sizes);
			setAllRowGroupExpanded($("#grid_expand").is(":checked"));
		});
	}

	function setColumn(sizes) {
		if(!sizes) return;
		const columns = getCurrentColumn();
		columns.splice(9);
		
		let size_cols = sizes.map(size => size.map(s => s === 0 ? ({ empty_tag: ' ' }) : s));

		for (let i = 0; i < size_cols.length; i++) {
			let field_cds = size_cols[i].map(c => (c.size_kind_cd || '') + (c.size_kind_cd ? '^' : '') + (c.size_cd || ''));
			columns.push({ 
				field: 'SIZE_' + i,
				headerName: size_cols[i].map(c => c.size_cd || c.empty_tag).join('\n'),
				type: 'currencyType', 
				width: 80, 
				headerComponent: CustomHeader,
				aggFunc: sumValuesFunc,
				cellRenderer: (params) => {
					if (params.node.rowPinned === 'top') return params.data?.['SIZE_' + i] || 0;
					if (params.data === undefined) return params.node?.aggData?.['SIZE_' + i] || 0;

					let size_set = Object.keys(params.data)
						.filter(key => key.includes('SIZE^'))
						.reduce((a, key) => {
							if (a[key.split('SIZE^')[1]]) a[key.split('SIZE^')[1]] += params.data[key] * 1;
							else a[key.split('SIZE^')[1]] = params.data[key] * 1;
							return a;
						}, {});
					let col = field_cds.filter(c => Object.keys(size_set).includes(c) && size_set[c] > 0);

					return col.length > 1 ? '중복오류' : col.length < 1 ? 0 : size_set[col[0]];
				}
			});
			
			if (i < 1 && size_cols.length > 1) {
				columns.push({
					field: 'size_kind_nm',
					headerName: size_cols[1].map(c => c.size_kind_nm_s ? c.size_kind_nm_s : '').join('\n'),
					width: 70,
					headerComponent: CustomHeader,
				});
			}
		}
		columns.push({ width: 'auto' });

		gx.gridOptions.api.setColumnDefs([]);
		gx.gridOptions.api.setColumnDefs(columns);

		updatePinnedRow();
	}

	const updatePinnedRow = () => {
		const keys = gx.gridOptions.api.getColumnDefs()
			.reduce((a, c) => (!!c.children && Array.isArray(c.children)) ? a.concat(c.children.concat(c)) : a.concat(c), [])
			.filter(col => col.type === 'currencyType')
			.map(col => col.field);
		const totals = {};

		const rows = gx.getRows();
		if (rows && Array.isArray(rows) && rows.length > 0) {
			rows.forEach(row => {
				for (let i = 0; i < keys.length; i++) {
					totals[keys[i]] = (totals[keys[i]] ? totals[keys[i]] : 0) + parseInt(row?.[keys[i]] || 0);
				}
			});
		}

		let pinnedRow = gx.gridOptions.api.getPinnedTopRow(0);
		gx.gridOptions.api.setPinnedTopRowData([{
			...pinnedRow.data,
			...totals,
		}]);
	};

    function changeBaebunType() {
		let baebun_type_0 = $("input[name='baebun_type[0]']");
		let baebun_type_1 = $("input[name='baebun_type[1]']");
		let baebun_type_2 = $("input[name='baebun_type[2]']");
		let baebun_type_3 = $("input[name='baebun_type[3]']");
		let baebun_type_4 = $("input[name='baebun_type[4]']");
		let baebun_type_5 = $("input[name='baebun_type[5]']");
		let otherCheckboxes = $("input[name^='baebun_type']:not([name='baebun_type[0]'])");

		baebun_type_0.click(function() {
			if ($(this).is(":checked")) {
				otherCheckboxes.prop('checked', false);
			}
		});

		otherCheckboxes.change(function() {
			if ($(this).is(":checked")) {
				baebun_type_0.prop('checked', false);
			} else if (baebun_type_1.is(":checked") || baebun_type_2.is(":checked") || baebun_type_3.is(":checked") || baebun_type_4.is(":checked") || baebun_type_5.is(":checked")) {
				baebun_type_0.prop('checked', false);
			} else {
				baebun_type_0.prop('checked', true);
			}
		});
	}

	const validation = () => {
		if($('#baebun_date').val() == "") {
			openApi();
			return alert("배분일자를 선택해주세요.");
		}
		return true;
	}

	function openApi() {
        document.getElementsByClassName('sch-baebun')[0].click();
    }

	// 배분일자차수조회API
	function SearchBaebun(){
		this.grid = null;
	}

	SearchBaebun.prototype.Open = async function(callback = null){
		if(this.grid === null){
			this.SetGrid("#div-gd-baebun");
			$("#SearchBaebunModal").draggable();
			this.callback = callback;
		}
		$('#SearchBaebunModal').modal({
			keyboard: false
		});
	};

	SearchBaebun.prototype.SetGrid = function(divId){
		let columns = [];

		columns.push(
			{headerName: '#', width:40, pinned: 'left', valueGetter: 'node.id', cellRenderer: 'loadingRenderer', cellStyle: { "text-align": "center" }},
			{ field:"select_rows", headerName:"선택", pinned:'left', width:50, cellStyle: { "text-align": "center" },
				cellRenderer:function(params) {
					return `<a href="#" onclick="searchBaebun.Choice('${params.data.baebun_date}', '${params.data.rel_baebun}');">선택</a>`;
				}
			},
			{ field:"storage_cd", headerName:"창고코드", width:100, cellStyle: { "text-align": "center" }, hide:true},
			{ field:"storage_nm", headerName:"창고명", pinned: 'left', width:100, cellStyle: { "text-align": "center" }},
			{ field:"baebun_date", headerName:"배분일자", pinned: 'left',width:100, cellStyle: { "text-align": "center" }},
			{ field:"rel_baebun", headerName:"배분차수", width:70, cellStyle: { "text-align": "center" }},
			{ field:"rel_order", headerName:"배분차수", width:70, cellStyle: { "text-align": "center" }, hide:true},
			{ field:"store_cnt", headerName:"매장수", width:50, cellStyle: { "text-align": "right" }},
			{ field:"store_cd", headerName:"매장코드", width:100, cellStyle: { "text-align": "center" }, hide:true},
			{ field:"store_nm", headerName:"매장명", width:130, cellStyle: { "text-align": "center" },
				cellRenderer:function(params) {
					if(params.data.store_cnt == '1') {
						return params.data.store_nm;
					} else {
						return "";
					}
				}
			},
			{ field:"baebun_qty", headerName:"배분수량", width:60, cellStyle: { "text-align": "right" }},
			{ field:"state", headerName:"출고상태", width:100, cellStyle: {"text-align" : "center"},
				cellRenderer:function(params){
					if (params.data.state == '10') {
						return '출고요청'
					} else if (params.data.state == '20') {
						return '출고처리중'
					} else if (params.data.state == '30') {
						return '출고완료'
					} else if (params.data.state == '40') {
						return '매장입고'
					}
				}
			}
		);

		this.grid = new HDGrid(document.querySelector( divId ), columns, {
			getRowStyle: (params) => { // 고정된 row styling
				if (params.data.state == '30' || params.data.state == '40')  return {'background': '#eee', 'border': 'none'};
			},
		});
	};

	SearchBaebun.prototype.Search = function(e) {
		const event_type = e?.type;
		if (event_type == 'keypress') {
			if (e.key && e.key == 'Enter') {
				let data = $('form[name="search_baebun"]').serialize();
				this.grid.Request('/store/sale/sal29/searchBaebun', data);
			} else {
				return false;
			}
		} else {
			let data = $('form[name="search_baebun"]').serialize();
			this.grid.Request('/store/sale/sal29/searchBaebun', data);
		}
	};

	SearchBaebun.prototype.Choice = function(baebun_date, rel_baebun){
		if(this.callback !== null){
			this.callback(baebun_date, rel_baebun);
		} else {
			$('#baebun_date').val(baebun_date);
			$('#rel').val(rel_baebun);
			$('#release_date').val(baebun_date);
		}
		this.InitValue();
		$('#SearchBaebunModal').modal('toggle');
	};



	SearchBaebun.prototype.InitValue = () => {
		document.search_baebun.reset();
		searchBaebun.grid.setRows([]);
		$('#gd-baebun-total').html(0);
	};

	let searchBaebun = new SearchBaebun();
</script>
	
@stop

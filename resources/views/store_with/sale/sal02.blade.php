@extends('store_with.layouts.layout')
@section('title','매장별판매집계표(일별)')
@section('content')
<div class="page_tit">
	<h3 class="d-inline-flex">매장별판매집계표(일별)</h3>
	<div class="d-inline-flex location">
		<span class="home"></span>
		<span>/ 경영관리</span>
		<span>/ 매장별판매집계표(일별)</span>
	</div>
</div>

<form method="get" name="search">
	<div id="search-area" class="search_cum_form">
		<div class="card mb-3">

			<div class="d-flex card-header justify-content-between">
				<h4>조회</h4>
				<div class="flex_box">
					<a href="#" id="search_sbtn" onclick="Search();" class="btn btn-sm btn-primary shadow-sm mr-1"><i class="fas fa-search fa-sm text-white-50"></i> 검색</a>
					<!-- 2023-05-25 검색조건 초기화 주석처리 -양대성- -->
					<!-- <a href="#" onclick="formReset()" class="d-none search-area-ext d-sm-inline-block btn btn-sm btn-outline-primary mr-1 shadow-sm">검색조건 초기화</a> -->
					<div id="search-btn-collapse" class="btn-group mb-0 mb-sm-0"></div>
				</div>
			</div>

			<div class="card-body">
				<div class="row">
					<div class="col-lg-4">
						<div class="form-group">
							<label for="good_types">판매기간(판매연월)</label>
							<div class="docs-datepicker flex_box">
								<div class="input-group">
								<input type="text" class="form-control form-control-sm docs-date month" name="sdate" value="{{ $sdate }}" autocomplete="off">
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
							<label for="good_types">판매채널/매장구분</label>
							<div class="d-flex align-items-center">
								<div class="flex_box w-100">
									<select name='store_channel' id="store_channel" class="form-control form-control-sm" onchange="chg_store_channel();">
										<option value=''>전체</option>
									@foreach ($store_channel as $sc)
										<option value='{{ $sc->store_channel_cd }}'>{{ $sc->store_channel }}</option>
									@endforeach
									</select>
								</div>
								<span class="mr-2 ml-2">/</span>
								<div class="flex_box w-100">
									<select id='store_channel_kind' name='store_channel_kind' class="form-control form-control-sm" disabled>
										<option value=''>전체</option>
									@foreach ($store_kind as $sk)
										<option value='{{ $sk->store_kind_cd }}'>{{ $sk->store_kind }}</option>
									@endforeach
									</select>
								</div>
							</div>
						</div>
					</div>
					<div class="col-lg-4">
						<div class="form-group">
                            <label for="store_cd">매장명</label>
							<div class="form-inline inline_btn_box">
								<select id="store_cd" name="store_cd" class="form-control form-control-sm select2-store"></select>
								<a href="javascript:void(0);" class="btn btn-sm btn-outline-primary sch-store"><i class="bx bx-dots-horizontal-rounded fs-16"></i></a>
							</div>
                        </div>
					</div>
				</div>
				<div class="row">
					<div class="col-lg-4">
						<div class="form-group">
							<label for="">판매유형</label>
							<div class="flex_box">
								<select name='sell_type' class="form-control form-control-sm">
									<option value=''>전체</option>
								@foreach ($sell_types as $sell_type)
									<option value='{{ $sell_type->code_id }}'>{{ $sell_type->code_val }}</option>
								@endforeach
								</select>
							</div>
						</div>
					</div>
					<div class="col-lg-4">
						<div class="form-group">
                            <label for="formrow-email-input">상품명</label>
                            <div class="flex_box">
                                <input type='text' class="form-control form-control-sm ac-goods-nm search-enter" name='goods_nm' value=''>
                            </div>
                        </div>
					</div>
					<div class="col-lg-4">
						<div class="form-group">
							<label for="style_no">스타일넘버/바코드</label>
							<div class="form-inline">
								<div class="form-inline-inner input_box">
									<input type='text' class="form-control form-control-sm ac-style-no search-enter" name='style_no' id="style_no">
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
				</div>
				<div class="row">
					<div class="col-lg-4">
						<div class="form-group">
                            <label for="brand_cd">브랜드</label>
                            <div class="form-inline inline_btn_box">
                                <select id="brand_cd" name="brand_cd" class="form-control form-control-sm select2-brand"></select>
                                <a href="#" class="btn btn-sm btn-outline-primary sch-brand"><i class="bx bx-dots-horizontal-rounded fs-16"></i></a>
                            </div>
                        </div>
					</div>
					<div class="col-lg-4">
						<div class="form-group">
							<label for="qty">집계구분</label>
							<div class="flex_box">
								<div class="form-inline form-radio-box">
									<div class="custom-control custom-radio">
										<input type="radio" name="list_type" id="qty" value="qty" class="custom-control-input" />
										<label class="custom-control-label" for="qty">판매수량</label>
									</div>
									<!-- <div class="custom-control custom-radio">
										<input type="radio" name="list_type" id="ord_amt" value="ord_amt" class="custom-control-input" />
										<label class="custom-control-label" for="ord_amt">주문금액</label>
									</div> -->
									<div class="custom-control custom-radio">
										<input type="radio" name="list_type" id="recv_amt" value="recv_amt" class="custom-control-input" checked />
										<label class="custom-control-label" for="recv_amt">판매금액</label>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="resul_btn_wrap mb-3">
			<a href="#" id="search_sbtn" onclick="Search();" class="btn btn-sm btn-primary shadow-sm mr-1"><i class="fas fa-search fa-sm text-white-50"></i> 조회</a>
			<!-- 2023-05-25 검색조건 초기화 주석처리 -양대성- -->
			<!-- <input type="reset" id="search_reset" value="검색조건 초기화" class="btn btn-sm btn-outline-primary shadow-sm" onclick="formReset()"> -->
			<div class="search_mode_wrap btn-group mr-2 mb-0 mb-sm-0"></div>
		</div>
	</div>
</form>
<!-- DataTales Example -->
<div class="card shadow mb-0 last-card pt-2 pt-sm-0">
	<div class="card-body">
		<div class="card-title">
			<div class="filter_wrap">
				<div class="d-flex justify-content-between">
					<h6 class="m-0 font-weight-bold">총 : <span id="gd-total" class="text-primary">0</span>건</h6>
					<div class="d-flex">
						<div class="custom-control custom-checkbox form-check-box pr-2" style="display:inline-block;">
							<input type="checkbox" class="custom-control-input" name="grid_expand" id="grid_expand" onchange="return setAllRowGroupExpanded(this.checked);" checked>
							<label class="custom-control-label font-weight-normal" for="grid_expand">항목펼쳐보기</label>
						</div>
						<div class="custom-control custom-checkbox form-check-box pr-2" style="display:inline-block;">
                            <input type="checkbox" class="custom-control-input" name="store_type_grid_expand" id="store_type_grid_expand" onchange="return RowExpand('store_type', this.checked);">
                            <label class="custom-control-label font-weight-normal" for="store_type_grid_expand">매장구분별 접기</label>
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
<style>
    .hd-grid-red {
        color: red;
    }
	.hd-grid-blue {
        color: blue;
    }
	.ag-row-level-1 {
		background-color: #eee !important;
	}
	.ag-row-level-2 {
		background-color: #edf4fd !important;
	}
</style>
<script type="text/javascript" charset="utf-8">

	const yoil = {
		codes: [],
		format: ["일", "월", "화", "수", "목", "금", "토"],
	};
	const pinnedRowData = [{ store_cd: '합계', offline : 0, online: 0}];
	const sumValuesFunc = (params) => params.values.reduce((a,c) => a + (c * 1), 0)??0;
	var columns = [
		{headerName: '판매채널', showRowGroup: 'store_channel', cellRenderer: 'agGroupCellRenderer', minWidth: 130, pinned: 'left'},
		{field: "store_channel",	headerName: "판매채널", rowGroup: true, hide: true},
		{headerName: '매장구분', showRowGroup: 'store_channel_kind', cellRenderer: 'agGroupCellRenderer', minWidth: 130, pinned: 'left'},
        {field: "store_channel_kind", headerName: "매장구분", rowGroup: true, hide: true},
		{ field: "store_cd", headerName: "매장코드", pinned:'left', width:70, cellStyle: { 'text-align': "center" }, 
			cellRenderer: function (params) {
				if (params.node.rowPinned === 'top') {
					return "합계";
				} else {
					return params.value;
				}
			}
		},
		{ field: "store_nm", headerName: "매장명", pinned:'left', type: 'StoreNameType', minWidth: 120 },
		{ field: "proj_amt", headerName: "목표매출", pinned:'left', width:85, type: 'currencyType', aggFunc: sumValuesFunc},
        // { field: "progress_proj_amt", headerName: "달성율(%)", pinned:'left', width:85, type: 'percentType',
		// 	cellRenderer: function (params) {
		// 		if (params.node.rowPinned === 'top') {
		// 			return "";
		// 		} else {
		// 			let { proj_amt, recv_amt } = params.data;
		// 			/**
		// 			 * ( 목표 - 결제금액 ) / 목표 * 100 = 달성율(%)
		// 			 */
		// 			let progress = 0;
		// 			proj_amt = toInt(proj_amt);
		// 			recv_amt = toInt(recv_amt);

		// 			// if (progress > 100) return progress = 100; // 달성율 100 넘어가는 경우 100으로 고정
		// 			// if (proj_amt <= recv_amt) return progress = 100; // 목표액보다 큰 경우 100 처리

		// 			progress = ( recv_amt / proj_amt ) * 100;
		// 			progress = Comma(Math.round(progress * 1)); // 소수점 첫째짜리까지 반올림 처리

		// 			if (params.data.proj_amt == 0) {
		// 				return 0;
		// 			}

		// 			if (progress == -Infinity) progress = 0;

		// 			return progress;
		// 		}
		// 	}
		// },
		{ field : "progress_proj_amt", headerName: "달성율(%)", pinned: "left", width: 85, type: "percentType",
			cellRenderer: function(params) {
				if (params.node.rowPinned === 'top') {
					return "";
				} else if (params.data != undefined && params.node.level == 2) {
					let proj_amt = params.data.proj_amt;
					let recv_amt = params.data.recv_amt;

					let progress = 0;
					proj_amt = toInt(proj_amt);
					recv_amt = toInt(recv_amt);

					progress = (recv_amt / proj_amt) * 100;
					progress = Comma(Math.round(progress * 1));

					if (params.data.proj_amt == 0) {
						return 0;
					}

					if (progress == -Infinity) progress = 0;

					return progress;

				}
			}
		},
		{ field: "summary",	headerName: "합계",
			children: [
				{ headerName: "오프라인", field: "offline", type: 'numberType' },
				{ headerName: "온라인", field: "online", type: 'currencyType', type: 'currencyMinusColorType'},
				{ headerName: "판매수량", field: "qty", type: 'currencyType', type: 'currencyMinusColorType', aggFunc: sumValuesFunc },
				// { headerName: "주문금액", field: "ord_amt", type: 'currencyType', aggregation: true, type: 'currencyMinusColorType' },
				{ headerName: "판매금액", field: "recv_amt", type: 'currencyType', type: 'currencyMinusColorType', aggFunc: sumValuesFunc }
			]
		}
	];

	var mutable_cols = [];

	const toInt = (value) => {
		if (value == "" || value == NaN || value == null || value == undefined) return 0;
		return parseInt(value);
	};

	const setMutableColumns = (max_day) => {
		gx.gridOptions.api.setColumnDefs([]);
		mutable_cols = [];
		columns.map(col => {
			mutable_cols.push(col);
		});
		mutable_cols.push(dayColumns(max_day));
		mutable_cols.push({ headerName: "", field: "nvl", width: "auto" });
		gx.gridOptions.api.setColumnDefs(mutable_cols);
		autoSizeColumns(gx, ["nvl"]);
	};

	const dayColumns = (max_day) => {
		let obj = { fields: "day", headerName: "기간", children: [] };
		for ( var i=0; i < max_day; i++ ) {
			const day = i + 1;
			const code = yoil.codes[i];
			const day_of_week = yoil.format[code];
			const f_day = day + ` (${day_of_week})`;
			let col = { field: `${day}_val`, headerName: f_day, type: 'numberType', type: 'currencyMinusColorType', aggFunc: sumValuesFunc };
			if ( code == 0 ) {
				col.headerClass = 'hd-grid-red'; // 일요일 표시
			} else if ( code == 6 ) {
				col.headerClass = 'hd-grid-blue'; // 토요일 표시
			}
			obj.children.push(col);
		}
		return obj;
	};

	const pApp = new App('', {
		gridId:"#div-gd",
	});
	let gx;
	$(document).ready(function() {
		pApp.ResizeGrid(265);
		pApp.BindSearchEnter();
		let gridDiv = document.querySelector(pApp.options.gridId);
        gx = new HDGrid(gridDiv, columns, {
            pinnedTopRowData: pinnedRowData,
            getRowStyle: (params) => { // 고정된 row styling
                if (params.node.rowPinned)  return { 'font-weight': 'bold', 'background': '#eee', 'border': 'none'};
            },
            rollup: true,
            groupSuppressAutoColumn: true,
			suppressAggFuncInHeader: true,
			enableRangeSelection: true,
			animateRows: true,
        });
		Search();

		// 판매채널 선택되지않았을때 매장구분 disabled처리하는 부분
		load_store_channel();
	});

	const autoSizeColumns = (grid, except = [], skipHeader = false) => {
		const allColumnIds = [];
		gx.gridOptions.columnApi.getAllColumns().forEach((column) => {
			if (except.includes(column.getId())) return;
			allColumnIds.push(column.getId());
		});
		gx.gridOptions.columnApi.autoSizeColumns(allColumnIds, skipHeader);
	};

	const formatDay = async (e) => {
		yoil.codes = e.head.yoil_codes
		const max_day = yoil.codes.length;
		setMutableColumns(max_day);
	};

	function Search() {
		let data = $('form[name="search"]').serialize();
		gx.Request('/store/sale/sal02/search', data, -1, function(d) {
			formatDay(d);
			setAllRowGroupExpanded($("#grid_expand").is(":checked"));
			let pinnedRow = gx.gridOptions.api.getPinnedTopRow(0);
            let total_data = d.head.total_data;
            console.log(total_data);
			if(pinnedRow && total_data != '') {
				gx.gridOptions.api.setPinnedTopRowData([
					{ ...pinnedRow.data, ...total_data }
				]);
			}
		});
	}

	const formReset = () => {
		document.search.reset();
	};

	// 매장구분별 접기 기능
    function RowExpand(e, ischeck) {
        let check;
        if(ischeck == true) {
            check = false;
        } else {
            check = true;
        }
        gx.gridOptions.api.forEachNode(node => {
            if (e == 'store_type') {
                if (node.group && node.level == 1) {
                    node.setExpanded(check);
                }
            }
        });
        
    }

</script>
@stop

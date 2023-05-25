@extends('shop_with.layouts.layout')
@section('title','매장월별판매집계표')
@section('content')
<div class="page_tit">
	<h3 class="d-inline-flex">매장월별판매집계표</h3>
	<div class="d-inline-flex location">
		<span class="home"></span>
		<span>매장관리</span>
		<span>/ 경영관리</span>
	</div>
</div>

<form method="get" name="search">
	<div id="search-area" class="search_cum_form">
		<div class="card mb-3">

			<div class="d-flex card-header justify-content-between">
				<h4>검색</h4>
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
				</div>
				<div class="row">
					<div class="col-lg-4">
						<div class="form-group">
							<label for="style_no">스타일넘버/상품코드</label>
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
										<label class="custom-control-label" for="qty">주문수량</label>
									</div>
									<div class="custom-control custom-radio">
										<input type="radio" name="list_type" id="ord_amt" value="ord_amt" class="custom-control-input" />
										<label class="custom-control-label" for="ord_amt">주문금액</label>
									</div>
									<div class="custom-control custom-radio">
										<input type="radio" name="list_type" id="recv_amt" value="recv_amt" class="custom-control-input" checked />
										<label class="custom-control-label" for="recv_amt">결제금액</label>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="resul_btn_wrap mb-3">
			<a href="#" id="search_sbtn" onclick="Search();" class="btn btn-sm btn-primary shadow-sm mr-1"><i class="fas fa-search fa-sm text-white-50"></i> 검색</a>
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
				<div class="fl_box">
					<h6 class="m-0 font-weight-bold">총 : <span id="gd-total" class="text-primary">0</span>건</h6>
				</div>
				<div class="fr_box">
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
</style>
<script type="text/javascript" charset="utf-8">

	const yoil = {
		codes: [],
		format: ["일", "월", "화", "수", "목", "금", "토"],
	};

	var columns = [
		{ headerName: "#", field: "num", type:'NumType', pinned:'left', aggSum:"합계", aggAvg:"평균", cellStyle: { 'text-align': "center" },
			cellRenderer: function (params) {
				if (params.node.rowPinned === 'top') {
					return "합계";
				} else {
					return parseInt(params.value) + 1;
				}
			}
		},
		{ field: "store_cd", headerName: "매장코드", pinned:'left', width:70, cellStyle: { 'text-align': "center" } },
		{ field: "store_type_nm", headerName: "매장구분", pinned:'left', width:90, cellStyle: { 'text-align': "center" } },
		{ field: "store_nm", headerName: "매장명", pinned:'left', type: 'ShopNameType', width: 250 },
		{ field: "proj_amt", headerName: "목표", pinned:'left', width:85, type: 'currencyType', aggregation:true },
        { field: "progress_proj_amt", headerName: "달성율(%)", pinned:'left', width:85, type: 'percentType',
			cellRenderer: function (params) {
				if (params.node.rowPinned === 'top') {
					return "";
				} else {
					let { proj_amt, recv_amt } = params.data;
					/**
					 * ( 목표 - 결제금액 ) / 목표 * 100 = 달성율(%)
					 */
					let progress = 0;
					proj_amt = toInt(proj_amt);
					recv_amt = toInt(recv_amt);

					// if (progress > 100) return progress = 100; // 달성율 100 넘어가는 경우 100으로 고정
					// if (proj_amt <= recv_amt) return progress = 100; // 목표액보다 큰 경우 100 처리

					progress = ( recv_amt / proj_amt ) * 100;
					progress = Comma(Math.round(progress * 1)); // 소수점 첫째짜리까지 반올림 처리

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
				{ headerName: "오프라인", field: "offline", type: 'numberType', aggregation: true },
				{ headerName: "온라인", field: "online", type: 'currencyType', aggregation: true, type: 'currencyMinusColorType' },
				{ headerName: "주문수량", field: "qty", type: 'currencyType', aggregation: true, type: 'currencyMinusColorType' },
				{ headerName: "주문금액", field: "ord_amt", type: 'currencyType', aggregation: true, type: 'currencyMinusColorType' },
				{ headerName: "결제금액", field: "recv_amt", type: 'currencyType', aggregation: true, type: 'currencyMinusColorType' }
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
		gx.CalAggregation();
	};

	const dayColumns = (max_day) => {
		let obj = { fields: "day", headerName: "기간", children: [] };
		for ( var i=0; i < max_day; i++ ) {
			const day = i + 1;
			const code = yoil.codes[i];
			const day_of_week = yoil.format[code];
			const f_day = day + ` (${day_of_week})`;
			let col = { field: `${day}_val`, headerName: f_day, type: 'numberType', aggregation:true, type: 'currencyMinusColorType' };
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
		let options = {
			getRowStyle: (params) => {
				if (params.node.rowPinned === 'top') {
					return { 'background': '#eee' }
				}
			}
		}
		gx = new HDGrid(gridDiv, columns, options);
		Search();
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
		gx.Aggregation({ sum: "top" });
		gx.Request('/shop/sale/sal02/search', data, -1, (e) => formatDay(e));
	}

	const formReset = () => {
		document.search.reset();
	};

</script>
@stop

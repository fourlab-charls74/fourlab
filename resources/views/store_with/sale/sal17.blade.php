@extends('store_with.layouts.layout')
@section('title','매장목표')
@section('content')
<div class="page_tit">
	<h3 class="d-inline-flex">매장목표</h3>
	<div class="d-inline-flex location">
		<span class="home"></span>
		<span>매장목표</span>
		<span>/ 경영관리</span>
	</div>
</div>

<form method="get" name="search">
	<div id="search-area" class="search_cum_form">
		<div class="card mb-3">
			<input type='hidden' name='is_searched' value='{{$is_searched}}'>
			<div class="d-flex card-header justify-content-between">
				<h4>검색</h4>
				<div class="flax_box">
					<a href="#" id="search_sbtn" onclick="Search();" class="btn btn-sm btn-primary shadow-sm mr-1"><i class="fas fa-search fa-sm text-white-50"></i> 검색</a>
					<a href="#" onclick="formReset()" class="d-none search-area-ext d-sm-inline-block btn btn-sm btn-outline-primary mr-1 shadow-sm">검색조건 초기화</a>
					<div id="search-btn-collapse" class="btn-group mb-0 mb-sm-0"></div>
				</div>
			</div>
			<div class="card-body">
				<div class="row">
					<div class="col-lg-4 inner-td">
						<div class="form-group">
							<label for="good_types">판매기간</label>
							<div class="form-inline date-select-inbox">
								<div class="docs-datepicker form-inline-inner input_box">
									<div class="input-group">
										<input type="text" class="form-control form-control-sm docs-date month" name="sdate" value="{{ $sdate }}" autocomplete="off" disable>
										<div class="input-group-append">
											<button type="button" class="btn btn-outline-secondary docs-datepicker-trigger p-0 pl-2 pr-2" disable>
												<i class="fa fa-calendar" aria-hidden="true"></i>
											</button>
										</div>
									</div>
									<div class="docs-datepicker-container"></div>
								</div>
								<span class="text_line">~</span>
								<div class="docs-datepicker form-inline-inner input_box">
									<div class="input-group">
										<input type="text" class="form-control form-control-sm docs-date month" name="edate" value="{{ $edate }}" autocomplete="off">
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
					<div class="col-lg-4 inner-td">
						<div class="form-group">
							<label for="">매장구분</label>
							<div class="flax_box">
								<select name='store_type' class="form-control form-control-sm">
									<option value=''>전체</option>
									@foreach ($store_types as $store_type)
										<option value='{{ $store_type->code_id }}'>{{ $store_type->code_val }}</option>
									@endforeach
								</select>
							</div>
						</div>
					</div>
					<div class="col-lg-4 inner-td">
						<div class="form-group">
                            <label for="store_cd">매장명</label>
							<div class="form-inline inline_btn_box">
								<select id="store_cd" name="store_cd" class="form-control form-control-sm select2-store"></select>
								<a href="javascript:void(0);" class="btn btn-sm btn-outline-primary sch-store"><i class="bx bx-dots-horizontal-rounded fs-16"></i></a>
							</div>
                        </div>
                    </div>
				</div>
			</div>
		</div>
		<div class="resul_btn_wrap mb-3">
			<a href="#" id="search_sbtn" onclick="Search();" class="btn btn-sm btn-primary shadow-sm mr-1"><i class="fas fa-search fa-sm text-white-50"></i> 검색</a>
			<input type="reset" id="search_reset" value="검색조건 초기화" class="btn btn-sm btn-outline-primary shadow-sm" onclick="formReset()">
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
</style>
<script type="text/javascript" charset="utf-8">

	const IS_SEARCHED = document.search.is_searched.value;

	let col_keys = [];

	const init_cols = () => {
		return (IS_SEARCHED == 'y') 
			? [
				{ headerName: "#", field: "num", type:'NumType', pinned:'left', aggSum:"합계", cellStyle: { 'text-align': "center" },
					cellRenderer: function (params) {
						if (params.node.rowPinned === 'top') {
							return "합계";
						} else {
							return parseInt(params.value) + 1;
						}
					}
				},
				{ field: "store_type_nm", headerName: "매장구분", pinned:'left', width:90, cellStyle: { 'text-align': "center" } },
				{ field: "store_cd", headerName: "매장코드", pinned:'left', hide: true },
				{ field: "store_nm", headerName: "매장명", pinned:'left', type: 'StoreNameType', width: 250 },
				{
					field: "summary", headerName: "합계",
					children: [
						{field: "proj_amt", headerName: "목표", type: 'currencyType', aggregation: true },
						{field: "last_recv_amt", headerName: "전년", type: 'currencyMinusColorType', aggregation: true },
						{field: "recv_amt", headerName: "금액", type: 'currencyMinusColorType', aggregation: true },
						{field: "progress_proj_amt", headerName: "달성율(%)", type: 'currencyMinusColorType', aggregation: true,
							cellRenderer: params => goalProgress(params.data)
						},
					]
				}
			] 
			: []
	};

	let columns = init_cols();

	/**
	 * ( 목표 - 결제금액 ) / 목표 * 100 = 달성율(%)
	 */
	const goalProgress = (row, Ym) => {
		let prefix = "";
		let progress = 0;
		if (Ym) prefix = `_${Ym}`;
		let proj_amt = parseInt(row[`proj_amt${prefix}`]);
		let recv_amt = parseInt(row[`recv_amt${prefix}`]);
		proj_amt = isNaN(proj_amt) ? 0 : proj_amt;
		recv_amt = isNaN(recv_amt) ? 0 : recv_amt;

		if (proj_amt == 0 || proj_amt == null || proj_amt == "") progress; // 목표액이 없는경우 빈 값 할당
		if (progress > 100) return progress = 100; // 달성율 100 넘어가는 경우 100으로 고정
		if (proj_amt <= recv_amt) return progress = 100; // 목표액보다 큰 경우 100 처리

		progress = ( (proj_amt - recv_amt) / proj_amt );
		
		return progress;
	};

	const setColumns = (count) => {
		let cols = init_cols();
		gx.gridOptions.api.setColumnDefs(cols);
		for ( let i = 0; i < count; i++ ) {
			const Ym = `${col_keys[i]}`;
			const f = Ym.substr(0, 4) + "-" + Ym.substr(4, 2);
			let obj = { fields: `${Ym}`, headerName: `${f}`};
			obj.children = [
				{ field: `proj_amt_${Ym}`, headerName: "목표", type: 'currencyType', aggregation: true, 
					editable: params => params.node.rowPinned === 'top' ? false : true, 
					cellStyle: params => {
						if (params.node.rowPinned === 'top') {
							return {};
						} else {
							return { 'background': '#ffff99' };
						}
					}
				},
				{ field: `prev_recv_amt_${Ym}`, headerName: "전월", type: 'currencyMinusColorType', aggregation: true },
                { field: `last_recv_amt_${Ym}`, headerName: "전년", type: 'currencyMinusColorType', aggregation: true },
                { field: `recv_amt_${Ym}`, headerName: "금액", type: 'currencyMinusColorType', aggregation: true},
                { field: `progress_proj_amt_${Ym}`, headerName: "달성율(%)", type: 'currencyMinusColorType', aggregation: true,
				 	cellRenderer: params => goalProgress(params.data, Ym)
				}
			];
			cols.push(obj);
		}
		cols.push({ headerName: "", field: "nvl", width: "auto" });
		gx.gridOptions.api.setColumnDefs(cols);
		gx.CalAggregation();
		autoSizeColumns(gx, ["nvl"]);
	};

	const autoSizeColumns = (grid, except = [], skipHeader = false) => {
        const allColumnIds = [];
        gx.gridOptions.columnApi.getAllColumns().forEach((column) => {
            if (except.includes(column.getId())) return;
            allColumnIds.push(column.getId());
			
        });
        gx.gridOptions.columnApi.autoSizeColumns(allColumnIds, skipHeader);
    };

	const pApp = new App('',{
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
			},
			onCellValueChanged: params => evtAfterEdit(params)
		}
		gx = new HDGrid(gridDiv, columns, options);

		if (IS_SEARCHED == 'y') Search();

		// 매장 검색 클릭 이벤트 바인딩 및 콜백 사용
		$( ".sch-store" ).on("click", function() {
            searchStore.Open();
        });
	});

	function Search() {
		if (IS_SEARCHED == 'y') {
			let data = $('form[name="search"]').serialize();
			gx.Aggregation({ sum: "top" });
			gx.Request('/store/sale/sal17/search', data, -1, (e) => afterSearch(e));
		} else {
			document.search.is_searched.value = 'y';
			$('form[name="search"]').submit();
		}
	}

	const afterSearch = (e) => {
		col_keys = e.head.col_keys;
		const count = col_keys?.length;
		setColumns(count);
	};

	const formReset = () => {
		document.search.reset();
	};

	const startEditingCell = (row_index, col_key) => {
        gx.gridOptions.api.startEditingCell({ rowIndex: row_index, colKey: col_key });
    };

	const evtAfterEdit = async (params) => {
		let prev_value = params.oldValue;
        let value = params.newValue;

		if (prev_value !== value) {
			let row = params.data;
            const row_index = params.rowIndex;
            const column_name = params.column.colId;

			let regExp = /(?=proj_amt_).+/i;
			let arr = column_name.match(regExp);
			if (arr) {
				if (isNaN(value) == true || value == "" || parseFloat(value) < 0) {
					alert("숫자만 입력가능합니다.");
					startEditingCell(row_index, column_name);
					return false;
				}

				prev_value = toInt(params.oldValue);
				value = toInt(params.newValue);

				regExp = new RegExp(/[proj_amt_]/, "g");
				const Ym = column_name.replace(regExp, "");
				const rowNode = gx.gridOptions.api.getRowNode(row_index);

				// 목표 금액부터 반영
				row[column_name] = value;
				gx.gridOptions.api.applyTransaction({ update: [row] });

				// 목표 금액, 달성률 반영
				row['proj_amt'] = toInt(row['proj_amt']) - prev_value + value;
				row[`progress_proj_amt_${Ym}`] = goalProgress(row, Ym);

                const response = await axios({ 
                    url: `/store/sale/sal17/update`, method: 'post', 
                    data: { 'store_cd': row.store_cd, 'proj_amt': value, 'Ym': Ym }
                });
                const { data, status } = response;
                if (data?.code == 200) {
                    gx.gridOptions.api.applyTransaction({ update: [row] });
					gx.CalAggregation();
                } else if (data?.code == 500) {
					alert('목표 저장에 실패했습니다. 잠시후 다시 시도해주세요.');
				}
			}
		}
	};

	const toInt = (value) => {
		if (value == "" || value == NaN || value == null || value == undefined) return 0;
		return parseInt(value);
	};

</script>
@stop

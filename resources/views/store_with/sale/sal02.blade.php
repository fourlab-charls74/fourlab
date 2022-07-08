@extends('store_with.layouts.layout')
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
					<a href="#" onclick="formReset()" class="d-none search-area-ext d-sm-inline-block btn btn-sm btn-outline-primary mr-1 shadow-sm">검색조건 초기화</a>
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
							<label for="store_type">매장구분</label>
							<div class="flex_box">
								<select name='store_type' class="form-control form-control-sm">
									<option value=''>전체</option>
									@foreach ($store_types as $store_type)
										<option value='{{ $store_type->code_id }}'>{{ $store_type->code_val }}</option>
									@endforeach
								</select>
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
					<div class="col-lg-4">
						<div class="form-group">
							<label for="sale_yn">매출여부</label>
							<div class="flex_box">
								<div class="form-inline form-radio-box">
									<div class="custom-control custom-radio">
										<input type="radio" name="sale_yn" id="sale_y" value="Y" class="custom-control-input" checked/>
										<label class="custom-control-label" for="sale_y">Y</label>
									</div>
									<div class="custom-control custom-radio">
										<input type="radio" name="sale_yn" id="sale_n" value="N" class="custom-control-input"/>
										<label class="custom-control-label" for="sale_n">N</label>
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
		{headerName: "#", field: "num",type:'NumType',pinned: 'left'},
		{field: "store_type_nm", headerName: "매장구분", pinned: 'left'},
		{field: "store_cd", headerName: "매장코드",  pinned: 'left',hide: true},
		{field: "store_nm", headerName: "매장명",  pinned: 'left',type: 'StoreNameType'},
		{field: "proj_amt",	headerName: "목표", width:85, type: 'currencyMinusColorType'},
        {field: "",	headerName: "달성율(%)", width:85, type: 'percentType'}
	];

	const mutable_cols = (max_day) => [ ...columns, { ...sum_cols() }, { ...day_cols(max_day) }, { headerName: "", field: "nvl", width: "auto" } ];

	const sum_cols = () => {
		return (
			{field: "",	headerName: "합계",
				children: [
					{headerName: "오프라인", field: "", type: 'numberType'},
					{headerName: "온라인", field: "", type: 'currencyMinusColorType'},
					{headerName: "주문수량", field: "qty", type: 'currencyMinusColorType'},
					{headerName: "주문금액", field: "ord_amt", type: 'currencyMinusColorType'},
					{headerName: "결제금액", field: "recv_amt", type: 'currencyMinusColorType'}
				]
			}
		);
	};

	const day_cols = (max_day) => {
		let obj = { fields: "day", headerName: "기간", children: [] };
		for ( var i=0; i < max_day; i++ ) {
			const day = i + 1;
			const code = yoil.codes[i];
			const day_of_week = yoil.format[code];
			const f_day = day + ` (${day_of_week})`;
			let col = { field: `${day}_val`, headerName: f_day, type: 'currencyMinusColorType' };
			if ( code == 0 ) {
				col.headerClass = 'hd-grid-red'; // 일요일 표시
			} else if ( code == 6 ) {
				col.headerClass = 'hd-grid-blue'; // 토요일 표시
			}
			obj.children.push(col);
		}
		return obj;
	};

	const pApp = new App('',{
		gridId:"#div-gd",
	});
	let gx;
	$(document).ready(function() {

		pApp.ResizeGrid(265);
		pApp.BindSearchEnter();
		let gridDiv = document.querySelector(pApp.options.gridId);
		gx = new HDGrid(gridDiv, columns);
		Search();

		// 매장 검색 클릭 이벤트 바인딩 및 콜백 사용
        $( ".sch-store" ).on("click", function() {
            searchStore.Open();
        });
	});
	const autoSizeColumns = (grid, except = [], skipHeader = false) => {
        const allColumnIds = [];
        grid.gridOptions.columnApi.getAllColumns().forEach((column) => {
            if (except.includes(column.getId())) return;
            allColumnIds.push(column.getId());
        });
        grid.gridOptions.columnApi.autoSizeColumns(allColumnIds, skipHeader);
    };

	const formatDay = (e) => {
		yoil.codes = e.head.yoil_codes
		const max_day = yoil.codes.length;
		const columns = mutable_cols(max_day);
		console.log(columns);
		gx.gridOptions.api.setColumnDefs(columns);
		autoSizeColumns(gx, ["nvl"]);
	};

	function Search() {
		let data = $('form[name="search"]').serialize();
		gx.Request('/store/sale/sal02/search', data, 1, (e) => formatDay(e));
	}

	const formReset = () => {
		document.search.reset();
	};

</script>
@stop

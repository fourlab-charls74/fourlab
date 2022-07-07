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
				<div class="flax_box">
					<a href="#" id="search_sbtn" onclick="Search();" class="btn btn-sm btn-primary shadow-sm mr-1"><i class="fas fa-search fa-sm text-white-50"></i> 검색</a>
					<div id="search-btn-collapse" class="btn-group mb-0 mb-sm-0"></div>
				</div>
			</div>

			<div class="card-body">
				<div class="row">
					<div class="col-lg-4 inner-td">
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
							<label for="">매장구분</label>
							<div class="flax_box">
								<select name='com_type' class="form-control form-control-sm">
									<option value=''>전체</option>
									@foreach ($com_types as $com_type)
										<option value='{{ $com_type->code_id }}'>{{ $com_type->code_val }}</option>
									@endforeach
								</select>
							</div>
						</div>
					</div>
					<div class="col-lg-4 inner-td">
						<div class="form-group">
							<label for="">매장명</label>
							<div class="flax_box">
								<input type='text' class="form-control form-control-sm search-enter" name='com_nm' value=''>
							</div>
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-lg-4 inner-td">
						<div class="form-group">
							<label for="">판매유형</label>
							<div class="flax_box">
								<select name='sell_type' class="form-control form-control-sm">
									<option value=''>전체</option>
								@foreach ($sell_types as $sell_type)
									<option value='{{ $sell_type->code_id }}'>{{ $sell_type->code_val }}</option>
								@endforeach
								</select>
							</div>
						</div>
					</div>
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
				<div class="fr_box">
					<div class="form-inline form-radio-box">
						<div class="custom-control custom-radio">
							<input type="radio" name="list_type" id="total_qty" value="total_qty" class="custom-control-input" checked />
							<label class="custom-control-label" for="total_qty">주문수량</label>
						</div>
						<div class="custom-control custom-radio">
							<input type="radio" name="list_type" id="total_price" value="total_price" class="custom-control-input" />
							<label class="custom-control-label" for="total_price">주문금액</label>
						</div>
						<div class="custom-control custom-radio">
							<input type="radio" name="list_type" id="total_recv_amt" value="total_recv_amt" class="custom-control-input" />
							<label class="custom-control-label" for="total_recv_amt">결제금액</label>
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
</style>
<script type="text/javascript" charset="utf-8">

	// const qty column

	var columns = [
		{headerName: "#", field: "num",type:'NumType'},
		{field: "store_type_nm",headerName: "매장구분"},
		{field: "store_cd",	headerName: "매장코드"},
		{field: "store_nm",	headerName: "매장명", type: 'StoreNameType'},
		{field: "proj_amt",	headerName: "목표",type: 'numberType'},
        {field: "",	headerName: "달성율(%)"},
		{field: "",	headerName: "합계",
            children: [
                {headerName: "오프라인", field: "", type: 'numberType'},
                {headerName: "온라인", field: "", type: 'currencyType'},
                {headerName: "주문수량", field: "qty", type: 'currencyType'},
                {headerName: "주문금액", field: "ord_amt", type: 'currencyType'},
                {headerName: "결제금액", field: "recv_amt", type: 'currencyType'}
            ]
        },
        {
            headerName: "기간",
            children: [
                @foreach($months as $month)
                    @if($month["week"] == 'Sat' || $month["week"] == 'Sun')
                        {field: "{{$month["day"]}}_qty", headerName: "{{$month["day"]}} ({{$month["week"]}})",headerClass:'hd-grid-red'},
                    @else
                        {field: "{{$month["day"]}}_qty", headerName: "{{$month["day"]}} ({{$month["week"]}})"},
                    @endif
                @endforeach
            ]
        },
        {headerName: "", field: "nvl", width: "auto"}
	];

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

	});

	const yoil = {
		codes: [],
		format: ["일", "월", "화", "수", "목", "금", "토"],
	};

	// const renderWithYoil = (params) => {
	// 	if (params.value !== undefined) {
	// 		const value = params?.value;
	// 		let regExp = /\d+(?=_)/i;
	// 		const arr = value.match(regExp);

	// 		const day = arr[0];
	// 		const code = yoil.codes[day];
	// 		const f_yoil = yoil.format[code];

	// 		return `${value} (${f_yoil})`;

	// 		gx2.gridOptions.api.setColumnDefs([]);
	// 	}
	// };

	const formatDay = (e) => {
		yoil.codes = e.head.yoil_codes
		const columns = yoil.codes.map((code, index) => {
			const day = index + 1;
			const column = yoil.format[code];
			return column;
		});
		// gx2.gridOptions.api.setColumnDefs([]);
		console.log(columns);
	};

	function Search() {
		let data = $('form[name="search"]').serialize();
		gx.Request('/store/sale/sal02/search', data, 1, (e) => formatDay(e));
	}



</script>
@stop

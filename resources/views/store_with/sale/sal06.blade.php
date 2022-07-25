@extends('store_with.layouts.layout')
@section('title','상품별이익현황')
@section('content')
<div class="page_tit">
	<h3 class="d-inline-flex">매장상품별판매분석</h3>
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
					<a href="#" onclick="Add();" class="btn btn-sm btn-outline-primary shadow-sm pl-2 mr-1"><i class="bx bx-plus fs-16"></i>데이터업로드</a>
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
										<input type="text" class="form-control form-control-sm docs-date" name="sdate" value="{{ $sdate }}" autocomplete="off" disable>
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
										<input type="text" class="form-control form-control-sm docs-date" name="edate" value="{{ $edate }}" autocomplete="off">
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
					<div class="col-lg-4 inner-td">
						<div class="form-group">
							<label for="item">자료수/정렬</label>
							<div class="form-inline">
								<div class="form-inline-inner input_box" style="width:24%;">
									<select name="limit" class="form-control form-control-sm">
										<option value="100">100</option>
										<option value="500" selected>500</option>
										<option value="1000">1000</option>
										<option value="2000">2000</option>
									</select>
								</div>
								<span class="text_line">/</span>
								<div class="form-inline-inner input_box" style="width:45%;">
									<select name="ord_field" class="form-control form-control-sm">
										<option value="a.ord_date" selected>판매일자</option>
										<option value="a.com_nm" >매장명</option>
									</select>
								</div>
								<div class="form-inline-inner input_box sort_toggle_btn" style="width:24%;margin-left:1%;">
									<div class="btn-group" role="group">
										<label class="btn btn-primary primary" for="sort_desc" data-toggle="tooltip" data-placement="top" title="" data-original-title="내림차순"><i class="bx bx-sort-down"></i></label>
										<label class="btn btn-secondary" for="sort_asc" data-toggle="tooltip" data-placement="top" title="" data-original-title="오름차순"><i class="bx bx-sort-up"></i></label>
									</div>
									<input type="radio" name="ord" id="sort_desc" value="desc" checked="">
									<input type="radio" name="ord" id="sort_asc" value="asc">
								</div>
							</div>

						</div>
					</div>
				</div>

			</div>
		</div>
		<div class="resul_btn_wrap mb-3">
			<a href="#" id="search_sbtn" onclick="Search();" class="btn btn-sm btn-primary shadow-sm mr-1"><i class="fas fa-search fa-sm text-white-50"></i> 검색</a>
			<a href="#" onclick="Add()" class="btn btn-sm btn-outline-primary shadow-sm pl-2 mr-1"><i class="bx bx-plus fs-16"></i> 데이터업로드</a>
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
<script language="javascript">
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
        { field: "store_type_nm", headerName: "매장구분", pinned:'left', width:90, cellStyle: { 'text-align': "center" } },
        { field: "store_cd", headerName: "매장코드", pinned:'left', hide: true },
        { field: "store_nm", headerName: "매장명", pinned:'left', type: 'StoreNameType', width: 250 },
        // {field: "",	headerName: "TAG가"},
        {field: "s",	headerName: "판매유형",
            children: [
                {headerName: "일반판매", field: "discount", type: 'currencyMinusColorType'},
                {headerName: "쿠폰판매(10%)", field: "recv_amt", type: 'currencyMinusColorType'}, // 판매금액 + 포인트 합친게 결제(주문) 금액.
                {headerName: "브랜드데이10%", field: "", cellRenderer: (params) => 0, type: 'currencyMinusColorType'}, // 0 처리
            ]
        },
        {field: "s",	headerName: "판매현황",
            children: [
                {headerName: "수량", field: "qty", type: 'currencyMinusColorType'},
                {headerName: "단가", field: "wonga", type: 'numberType'},
                {headerName: "매출액", field: "amt", type: 'currencyMinusColorType'},
                {headerName: "할인", field: "discount", type: 'currencyMinusColorType'},
                {headerName: "결제금액", field: "recv_amt", type: 'currencyMinusColorType'}, // 판매금액 + 포인트 합친게 결제(주문) 금액.
                {headerName: "매장수수료", field: "", cellRenderer: (params) => 0, type: 'currencyMinusColorType'}, // 0 처리
                {headerName: "중간관리수수료", field: "", cellRenderer: (params) => 0, type: 'currencyMinusColorType'}, // 0 처리
            ]
        },
        {field: "sum_wonga", headerName: "원가", type: 'currencyMinusColorType'},
        {field: "sales_profit",	headerName: "매출이익", type: 'currencyMinusColorType'}, // 매출이익 = 결제금액 - 원가 합계금액
        {field: "profit_rate",	headerName: "이익율(%)", type:'percentType'}, // 매출이익 분의 매출액 = 이익율
        {headerName: "", field: "nvl", width: "auto"}
    ];

	function Add()
	{
		const url='/head/xmd/store/store01/show';
		window.open(url,"_blank","toolbar=no,scrollbars=yes,resizable=yes,status=yes,top=500,left=500,width=1200,height=800");
	}

</script>
<script type="text/javascript" charset="utf-8">
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
	function Search() {
		let data = $('form[name="search"]').serialize();
		gx.Request('/store/sale/sal01/search', data,1);
	}

</script>
@stop

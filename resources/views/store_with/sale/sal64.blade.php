@extends('store_with.layouts.layout')
@section('title','매장별 할인판매 현황')
@section('content')
	<div class="page_tit">
		<h3 class="d-inline-flex">매장별 할인판매 현황</h3>
		<div class="d-inline-flex location">
			<span class="home"></span>
			<span>/ 경영관리</span>
			<span>/ 매장별 할인판매 현황</span>
			<span>/ <a href="/store/sale/sal34">기존</a></span>
		</div>
	</div>
	<form method="get" name="search" id="search">
		<div id="search-area" class="search_cum_form">
			<div class="card mb-3">
				<div class="d-flex card-header justify-content-between">
					<h4>검색</h4>
					<div>
						<a href="#" id="search_sbtn" onclick="return Search();" class="btn btn-sm btn-primary shadow-sm pl-2"><i class="fas fa-search fa-sm text-white-50"></i> 조회</a>
						<a href="javascript:gx.Download('매장별 할인판매 현황_{{ date('YmdH') }}');" class="btn btn-download btn-sm btn-outline-primary shadow-sm mr-1"><i class="bx bx-download fs-16"></i> 엑셀다운로드</a>
						<div id="search-btn-collapse" class="btn-group mb-0 mb-sm-0"></div>
					</div>
				</div>
				<div class="card-body">
					<div class="row">
						<div class="col-lg-4 inner-td">
							<div class="form-group">
								<label for="formrow-firstname-input">판매기간</label>
								<div class="form-inline">
									<div class="docs-datepicker form-inline-inner input_box">
										<div class="input-group">
											<input type="text" class="form-control form-control-sm docs-date" name="sdate" value="{{ $sdate }}" autocomplete="off">
											<div class="input-group-append">
												<button type="button" class="btn btn-outline-secondary docs-datepicker-trigger p-0 pl-2 pr-2" disable="">
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
								<label for="good_types">판매채널/매장구분</label>
								<div class="d-flex align-items-center">
									<div class="flex_box w-100">
										<select name='store_channel' id="store_channel" class="form-control form-control-sm" onchange="chg_store_channel();">
											<option value=''>전체</option>
											@foreach ($store_channel as $sc)
												<option value='{{ $sc->store_channel_cd }}' @if(@$p_store_channel === $sc->store_channel_cd) selected @endif>{{ $sc->store_channel }}</option>
											@endforeach
										</select>
									</div>
									<span class="mr-2 ml-2">/</span>
									<div class="flex_box w-100">
										<select id='store_channel_kind' name='store_channel_kind' class="form-control form-control-sm" @if(@$p_store_kind == '') disabled @endif>
											<option value=''>전체</option>
											@foreach ($store_kind as $sk)
												<option value='{{ $sk->store_kind_cd }}' @if(@$p_store_kind === $sk->store_kind_cd) selected @endif>{{ $sk->store_kind }}</option>
											@endforeach
										</select>
									</div>
								</div>
							</div>
						</div>
						<div class="col-lg-4 inner-td">
							<div class="form-group">
								<label for="prd_cd">상품검색조건</label>
								<div class="form-inline">
									<div class="form-inline-inner input-box w-100">
										<div class="form-inline inline_btn_box">
											<input type='hidden' id="prd_cd_range" name='prd_cd_range'>
											<input type='text' id="prd_cd_range_nm" name='prd_cd_range_nm' class="form-control form-control-sm w-100 sch-prdcd-range" readonly style="background-color: #fff;">
											<a href="#" class="btn btn-sm btn-outline-primary sch-prdcd-range"><i class="bx bx-dots-horizontal-rounded fs-16"></i></a>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-lg-4 inner-td">
							<div class="form-group">
								<label>매장명</label>
								<div class="form-inline inline_btn_box">
									<input type='hidden' id="store_nm" name="store_nm" value="{{ @$store->store_nm }}">
									<select id="store_no" name="store_no" class="form-control form-control-sm select2-store"></select>
									<a href="javascript:void(0);" class="btn btn-sm btn-outline-primary sch-store"><i class="bx bx-dots-horizontal-rounded fs-16" style="line-height: 27px;"></i></a>
								</div>
							</div>
						</div>
						<div class="col-lg-4 inner-td">
							<div class="form-group">
								<label for="sell_type">판매유형</label>
								<div class="flax_box">
									<select id="sell_type" name="sell_type[]" class="form-control form-control-sm multi_select w-100" multiple>
										<option value=''>전체</option>
										@foreach ($sale_kinds as $sale_kind)
											<option value='{{ $sale_kind->code_id }}' @if(in_array($sale_kind->code_id, $sell_type_ids)) selected @endif>{{ $sale_kind->code_val }}</option>
										@endforeach
									</select>
								</div>
							</div>
						</div>
						<div class="col-lg-4 inner-td">
							<div class="form-group">
								<label for="formrow-inputZip">상품명</label>
								<div class="flax_box">
									<input type='text' class="form-control form-control-sm ac-goods-nm search-enter" id='goods_nm' name='goods_nm' value='{{ @$goods_nm }}'>
								</div>
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-lg-4 inner-td">
							<div class="form-group">
								<label for="prd_cd">바코드</label>
								<div class="form-inline">
									<div class="form-inline-inner input-box w-100">
										<div class="form-inline inline_btn_box">
											<input type='text' id="prd_cd" name='prd_cd' class="form-control form-control-sm w-100 ac-style-no search-enter">
											<a href="#" class="btn btn-sm btn-outline-primary sch-prdcd"><i class="bx bx-dots-horizontal-rounded fs-16"></i></a>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="resul_btn_wrap mb-3">
				<a href="javascript:;" class="btn btn-sm w-xs btn-primary shadow-sm apply-btn" onclick="return Search();"><i class="fas fa-search fa-sm text-white-50"></i> 조회</a>
				<a href="javascript:gx.Download('매장별 할인판매 현황_{{ date('YmdH') }}');" class="btn btn-download btn-sm btn-outline-primary shadow-sm mr-1"><i class="bx bx-download fs-16"></i> 엑셀다운로드</a>
				<div class="search_mode_wrap btn-group mr-2 mb-0 mb-sm-0"></div>
			</div>
		</div>
	</form>
	<!-- DataTales Example -->
	<div id="filter-area" class="card shadow-none mb-4 ty2 last-card">
		<div class="card-body shadow">
			<div class="card-title">
				<div class="filter_wrap">
					<div class="fl_box">
						<h6 class="m-0 font-weight-bold">총 : <span id="gd-total" class="text-primary">0</span> 건</h6>
					</div>
					<div class="fr_box flax_box">
					</div>
				</div>
			</div>
			<div class="table-responsive">
				<div id="div-gd" style="width:100%;min-height:600px;" class="ag-theme-balham"></div>
			</div>
		</div>
	</div>
	<div class="card shadow">
		<div class="card-body">
			<div class="card-title">
				<h6 class="m-0 font-weight-bold text-primary fas fa-question-circle"> Help</h6>
			</div>
			<ul class="mb-0">
				<li>매출액 = 판매결제금액 - 교환결제금액 - 환불결제금액</li>
				<li>할인 = 현재가(판매시점) - 결제금액</li>
				<li>부가세 = 매출액 - ( 매출액 / 1.1 )</li>
				<li>매출원가 = 실제판매원가</li>
			</ul>
		</div>
	</div>
	<script language="javascript">
		const columns = [
			{headerName: "판매채널", field: "store_channel",  width: 80, cellClass: 'hd-grid-code'},
			{headerName: "매장구분", field: "store_kind",  width: 80, cellClass: 'hd-grid-code'},
			{headerName: "매장코드", field: "store_cd", width: 100, cellClass: 'hd-grid-code', aggSum: "합계", aggAvg: "평균",
				cellRenderer:function(params) {
					if(params.value === '합계' || params.value === '평균') return params.value;
					let form_data = $('form[name="search"]').serialize();
					return `<a href="/store/order/ord06?${form_data}&store_cd=${params.data.store_cd}&date=${params.data.date || ''}" target="_blank">${params.value}</a>`;
				}
			},
			{headerName: "매장명", field: "store_nm",  width: 130},
			{ field: "sale_type", headerName: "판매유형별 할인",
				children: [
						@foreach ($sale_types as $sale_type)
					{ headerName: '{{ $sale_type->sale_type_nm }}', field: 'sale_kind_{{ $sale_type->sale_kind }}', type: 'numberType', aggregation: true },
						@endforeach
					{ headerName: '기타유형', field: 'etc_dc_amt', type: 'numberType', aggregation: true },
				]
			},
			{headerName: '매출액구분',
				children: [
					{headerName: "수량", field: "sum_qty", type: 'numberType', aggregation: true},
					{headerName: "적립금", field: "sum_point", type: 'currencyType', aggregation: true},
					{headerName: "할인", field: "sum_dc", type: 'currencyType', aggregation: true, width: 80},
					{headerName: "쿠폰", field: "sum_coupon", type: 'currencyType', aggregation: true},
					{headerName: "결제금액", field: "sum_recv", type: 'currencyType', aggregation: true},
				]
			},
			{headerName: "부가세", field: "vat", type: 'currencyType', aggregation: true, width:80},
			{headerName: "매출액(VAT별도)", field: "sum_amt", type: 'currencyType', aggregation: true},
			{headerName: "매출원가", field: "sum_wonga", type: 'currencyType', aggregation: true},
			{headerName: "마진율(%)", field: "margin", type: 'percentType',
				valueGetter: function(params) {
					if (params.data.date === "합계" || params.data.date === "평균") {
						const data = params.data;
						return (parseInt(data.sum_amt) - parseInt(data.sum_wonga)) / parseInt(data.sum_amt) * 100;
					}
					return params.data.margin;
				}
			},
			{headerName: '판매',
				children: [
					{headerName: "수량", field: "qty_30", type: 'numberType', aggregation: true},
					{headerName: "결제금액(VAT별도)", field: "recv_amt_30", type: 'currencyType', aggregation: true},
				]
			},
			{headerName: '교환',
				children: [
					{headerName: "수량", field: "qty_60", type: 'numberType', aggregation: true},
					{headerName: "결제금액(VAT별도)", field: "recv_amt_60", type: 'currencyType', aggregation: true},
				]
			},
			{
				headerName: '환불',
				children: [
					{headerName: "수량", field: "qty_61", type: 'numberType', aggregation: true},
					{headerName: "결제금액(VAT별도)", field: "recv_amt_61", type: 'currencyType', aggregation: true},
				]
			},
			{width: 0}
		];
	</script>
	<script type="text/javascript" charset="utf-8">
		const pApp = new App('', { gridId: "#div-gd" });
		let gx;
		let chart_data = null;

		$(document).ready(function() {
			pApp.ResizeGrid(430);
			pApp.BindSearchEnter();
			let gridDiv = document.querySelector(pApp.options.gridId);
			gx = new HDGrid(gridDiv, columns, {
				getRowStyle: (params) => {
					if (params.node.rowPinned === 'top') {
						return { 'background': '#eee' }
					}
				}
			});

			// 판매채널 선택되지않았을때 매장구분 disabled처리하는 부분
			load_store_channel();

			initSearchTab();
			//Search();
		});

		function initSearchTab() {
			let store_cd = "{{ @$store->store_cd }}";
			let store_nm = "{{ @$store->store_nm }}";
			let brand_cd = "{{ @$brand->brand }}";
			let brand_nm = "{{ @$brand->brand_nm }}";

			if (store_cd != '') {
				const option = new Option(store_nm, store_cd, true, true);
				$('#store_no').append(option).trigger('change');
			}

			if (brand_cd != '') {
				const option = new Option(brand_nm, brand_cd, true, true);
				$('#brand_cd').append(option).trigger('change');
			}

			let prd_cd_range = <?= json_encode(@$prd_cd_range) ?>;
			let prd_cd_range_nm = "{{ @$prd_cd_range_nm }}";
			prd_cd_range = Object.keys(prd_cd_range).reduce((a, c) => {
				if (c.includes('_contain') || c === 'match') return a;
				return a + prd_cd_range[c].map(rg => '&' + c +'[]=' + rg).join('');
			}, '');

			$('#prd_cd_range').val(prd_cd_range);
			$('#prd_cd_range_nm').val(prd_cd_range_nm);
		}

		function Search() {
			let data = $('form[name="search"]').serialize();
			gx.Aggregation({ "sum": "top", "avg": "top" });
			gx.Request('/store/sale/sal64/search', data, -1, function(data) {
			});
		}
	</script>

@stop

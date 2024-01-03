@extends('store_with.layouts.layout')
@section('title','판매유형별 매출통계')
@section('content')

	<div class="page_tit">
		<h3 class="d-inline-flex">판매유형별 매출통계</h3>
		<div class="d-inline-flex location">
			<span class="home"></span>
			<span>/ 경영관리</span>
			<span>/ 판매유형별 매출통계</span>
		</div>
	</div>

	<form method="get" name="search">
		<div id="search-area" class="search_cum_form">
			<div class="card mb-3">
				<div class="d-flex card-header justify-content-between">
					<h4>검색</h4>
					<div class="flax_box">
						<a href="#" id="search_sbtn" onclick="Search();" class="btn btn-sm btn-primary shadow-sm mr-1"><i class="fas fa-search fa-sm text-white-50"></i> 조회</a>
						<a href="javascript:gx.Download('판매유형별매출통계_{{ date('YmdH') }}');" class="btn btn-download btn-sm btn-outline-primary shadow-sm mr-1"><i class="bx bx-download fs-16"></i> 엑셀다운로드</a>
						<div id="search-btn-collapse" class="btn-group mb-0 mb-sm-0"></div>
					</div>
				</div>
				<div class="card-body">

					<div class="row">
						<div class="col-lg-4 inner-td">
							<div class="form-group">
								<label for="formrow-firstname-input">판매상태일</label>
								<div class="form-inline">
									<div class="docs-datepicker form-inline-inner input_box">
										<div class="input-group">
											<input type="text" class="form-control form-control-sm docs-date month" name="sdate" value="{{ $sdate }}" autocomplete="off">
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
								<label>매장명</label>
								<div class="form-inline inline_btn_box">
									<input type='hidden' id="store_nm" name="store_nm" value="{{ @$store->store_nm }}">
									<select id="store_no" name="store_no" class="form-control form-control-sm select2-store"></select>
									<a href="javascript:void(0);" class="btn btn-sm btn-outline-primary sch-store"><i class="bx bx-dots-horizontal-rounded fs-16" style="line-height: 27px;"></i></a>
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
											<input type='text' id="prd_cd_range_nm" name='prd_cd_range_nm' class="form-control form-control-sm w-100 sch-prdcd-range" readonly style="background-color: #fff;">
											<a href="#" class="btn btn-sm btn-outline-primary sch-prdcd-range"><i class="bx bx-dots-horizontal-rounded fs-16"></i></a>
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
			</div>
		</div>
	</form>
	<!-- DataTales Example -->

	<div id="filter-area" class="card shadow-none mb-0 search_cum_form ty2 last-card">
		<div class="card-body shadow">
			<div class="card-title">
				<div class="filter_wrap">
					<div class="fl_box">
						<h6 class="m-0 font-weight-bold">총 <span id="gd-total" class="text-primary">0</span> 건</h6>
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
	<script language="javascript">
		let columns = [
			{headerName: "년월", field: "ord_month",  width: 80, cellClass: 'hd-grid-code'},
			{headerName: "판매유형", field: "sale_type_nm",  width: 150},
			{headerName: "TAG가격", field: "tag_amt", type: 'numberType'},
			{headerName: "현재가격", field: "price_amt", type: 'numberType'},
			{headerName: "판매가격", field: "recv_amt", type: 'numberType'},
			{headerName: "할인율(%)", field: "sale_ratio", type: 'percentType',},
			{headerName: "비중(%)", field: "sg_ratio", type: 'percentType',},
			{width: 'auto'}
		];

	</script>

	<script type="text/javascript" charset="utf-8">
		const pApp = new App('', { gridId:"#div-gd", height: 265 });
		let gx;

		$(document).ready(function() {
			pApp.ResizeGrid(265);
			let gridDiv = document.querySelector(pApp.options.gridId);
			gx = new HDGrid(gridDiv, columns);
			pApp.BindSearchEnter();

			// 판매채널 선택되지않았을때 매장구분 disabled처리하는 부분
			load_store_channel();

			//Search();
		});

		function Search() {
			let data = $('form[name="search"]').serialize();
			gx.Request('/store/sale/sal36/search', data);
		}

	</script>
@stop

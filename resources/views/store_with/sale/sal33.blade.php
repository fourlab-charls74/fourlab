@extends('store_with.layouts.layout')
@section('title','매장RT 현황')
@section('content')

<div class="page_tit">
	<h3 class="d-inline-flex">매장 RT 현황</h3>
	<div class="d-inline-flex location">
		<span class="home"></span>
		<span>/ 영업관리</span>
		<span>/ 매장 RT 현황</span>
	</div>
</div>

<form method="get" name="search">
	<div id="search-area" class="search_cum_form">
		<div class="card mb-3">
			<div class="d-flex card-header justify-content-between">
				<h4>검색</h4>
				<div class="flax_box">
					<a href="#" id="search_sbtn" onclick="Search();" class="btn btn-sm btn-primary shadow-sm mr-1"><i class="fas fa-search fa-sm text-white-50"></i> 조회</a>
					<a href="javascript:gx.Download('매장RT현황_{{ date('YmdH') }}');" class="btn btn-download btn-sm btn-outline-primary shadow-sm mr-1"><i class="bx bx-download fs-16"></i> 엑셀다운로드</a>
					<div id="search-btn-collapse" class="btn-group mb-0 mb-sm-0"></div>
				</div>
			</div>
			<div class="card-body">

				<div class="row">
					<div class="col-lg-4 inner-td">
						<div class="form-group">
							<label for="formrow-firstname-input">등록일</label>
							<div class="form-inline">
								<div class="docs-datepicker form-inline-inner input_box">
									<div class="input-group">
										<input type="text" class="form-control form-control-sm docs-date" name="sdate" value="{{ $sdate }}" autocomplete="off">
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
							<label for="good_types">RT 타입</label>
							<div class="d-flex align-items-center">
								<select name='rt_type' id="rt_type" class="form-control form-control-sm">
									<option value=''>전체</option>
									<option value='R'>요청RT</option>
									<option value='G'>일반RT</option>
								</select>
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
		{headerName: "판매채널", field: "store_channel",  width: 80, cellClass: 'hd-grid-code'},
		{headerName: "매장구분", field: "store_kind",  width: 80, cellClass: 'hd-grid-code'},
		{headerName: "매장코드", field: "store_cd",  width: 80, hide:true},
		{headerName: "매장명", field: "store_nm",  width: 130},
		{headerName: '매장출고',
			children: [
				{headerName: "요청받은수", field: "out_rt_cnt", type: 'numberType'},
				{headerName: "미확인수", field: "out_req_cnt", type: 'numberType'},
				{headerName: "미확인율(%)", field: "out_req_ratio", type: 'numberType',
					cellRenderer: function(params) {
						if( params.data.out_req_ratio !== '-'){
							return params.data.out_req_ratio + "%";
						}else{
							return params.data.out_req_ratio;
						}
					}
				},
				{headerName: "접수수", field: "out_ing_cnt", type: 'numberType'},
				{headerName: "처리수", field: "out_end_cnt", type: 'numberType'},
				{headerName: "처리율(%)", field: "out_end_ratio", type: 'numberType',
					cellRenderer: function(params) {
						if(params.data.out_end_ratio !== '-'){
							return params.data.out_end_ratio + "%";
						}else{
							return params.data.out_end_ratio;
						}
					}
				},
				{headerName: "거부수", field: "out_rej_cnt", type: 'numberType'},
				{headerName: "거부율(%)", field: "out_rej_ratio", type: 'numberType',
					cellRenderer: function(params) {
						if(params.data.out_rej_ratio !== '-'){
							return params.data.out_rej_ratio + "%";
						}else{
							return params.data.out_rej_ratio;
						}
					}
				},
			]
		},
		{headerName: '매장입고',
			children: [
				{headerName: "요청한수", field: "in_rt_cnt", type: 'numberType'},
				{headerName: "미확인수", field: "in_req_cnt", type: 'numberType'},
				{headerName: "미확인율(%)", field: "in_req_ratio", type: 'numberType',
					cellRenderer: function(params) {
						if( params.data.in_req_ratio !== '-'){
							return params.data.in_req_ratio + "%";
						}else{
							return params.data.in_req_ratio;
						}
					}
				},
				{headerName: "접수수", field: "in_ing_cnt", type: 'numberType'},
				{headerName: "처리수", field: "in_end_cnt", type: 'numberType'},
				/*{headerName: "완료수", field: "in_fin_cnt", type: 'numberType'},*/
				{headerName: "처리율", field: "in_end_ratio", type: 'numberType',
					cellRenderer: function(params) {
						if(params.data.in_end_ratio !== '-'){
							return params.data.in_end_ratio + "%";
						}else{
							return params.data.in_end_ratio;
						}
					}
				},
				{headerName: "거부수", field: "in_rej_cnt", type: 'numberType'},
				{headerName: "거부율(%)", field: "in_rej_ratio", type: 'numberType',
					cellRenderer: function(params) {
						if(params.data.in_rej_ratio !== '-'){
							return params.data.in_rej_ratio + "%";
						}else{
							return params.data.in_rej_ratio;
						}
					}
				},
			]
		},
		{headerName: "반출:반입(%)", field: "rt_ratio", cellClass: 'hd-grid-code'},
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
		gx.Request('/store/sale/sal33/search', data);
	}

</script>
@stop

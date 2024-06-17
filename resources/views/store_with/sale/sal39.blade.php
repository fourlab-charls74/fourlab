@extends('store_with.layouts.layout')
@section('title','상품입출고 통합조회')
@section('content')

<div class="page_tit">
	<h3 class="d-inline-flex">상품입출고 통합조회</h3>
	<div class="d-inline-flex location">
		<span class="home"></span>
		<span>/ 영업관리</span>
		<span>/ 상품입출고 통합조회</span>
	</div>
</div>

<form method="get" name="search">
	<div id="search-area" class="search_cum_form">
		<div class="card mb-3">
			<div class="d-flex card-header justify-content-between">
				<h4>검색</h4>
				<div class="flax_box">
					<a href="#" id="search_sbtn" onclick="Search();" class="btn btn-sm btn-primary shadow-sm mr-1"><i class="fas fa-search fa-sm text-white-50"></i> 조회</a>
					<a href="javascript:gx.Download('상품입출고_{{ date('YmdH') }}');" class="btn btn-download btn-sm btn-outline-primary shadow-sm mr-1"><i class="bx bx-download fs-16"></i> 엑셀다운로드</a>
					<div id="search-btn-collapse" class="btn-group mb-0 mb-sm-0"></div>
				</div>
			</div>
			<div class="card-body">

				<div class="row">
					<div class="col-lg-4 inner-td">
						<div class="form-group">
							<label for="formrow-firstname-input">일자검색</label>
							<div class="d-flex">
								<div class="flex_box w-25 mr-2">
									<select name='date_type' class="form-control form-control-sm">
										<option value="req">요청일자</option>
										<option value="prc">처리중일자</option>
										<option value="fin">완료일자</option>
									</select>
								</div>
								<div class="form-inline date-select-inbox w-75">
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
							<label for="good_types">입출고구분/구분</label>
							<div class="d-flex align-items-center">
								<div class="flex_box w-100">
									<select name='kind' id="kind" class="form-control form-control-sm" onchange="chg_kind();">
										<option value=''>전체</option>
										<option value='release'>물류입고</option>
										<option value='return'>물류반품</option>
										<option value='rt_in'>RT입고</option>
										<option value='rt_out'>RT출고</option>
									</select>
								</div>
								<span class="mr-2 ml-2">/</span>
								<div class="flex_box w-100">
									<select id='type' name='type' class="form-control form-control-sm">
										<option value=''>전체</option>
									</select>
								</div>
							</div>
						</div>
					</div>
					<div class="col-lg-4 inner-td">
						<div class="form-group">
							<label for="good_types">처리상태</label>
							<div class="d-flex align-items-center">
								<div class="flex_box w-100">
									<select name='state' id="state" class="form-control form-control-sm">
										<option value=''>전체</option>
										<option value='10'>요청</option>
										<option value='30'>처리중</option>
										<option value='40'>완료</option>
										<option value='-10'>거부</option>
									</select>
								</div>
							</div>
						</div>
					</div>
					<div class="col-lg-4 inner-td">
						<div class="form-group">
							<label>바코드</label>
							<div class="flex_box">
								<input type='text' id="prd_cd" name='prd_cd' class="form-control form-control-sm ac-style-no search-enter">
								<a href="#" class="btn btn-sm btn-outline-primary sch-prdcd" hidden><i class="bx bx-dots-horizontal-rounded fs-16"></i></a>
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
										<input type='text' id="prd_cd_range_nm" name='prd_cd_range_nm' onclick="openApi();" class="form-control form-control-sm w-100 ac-style-no" readonly style="background-color: #fff;">
										<a href="#" class="btn btn-sm btn-outline-primary sch-prdcd-range"><i class="bx bx-dots-horizontal-rounded fs-16"></i></a>
									</div>
								</div>
							</div>
						</div>
					</div>
					<div class="col-lg-4 inner-td">
						<div class="form-group">
							<label for="style_no">스타일넘버/온라인코드</label>
							<div class="form-inline">
								<div class="form-inline-inner input_box">
									<input type='text' class="form-control form-control-sm ac-style-no search-enter" name='style_no' id="style_no" value="">
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
					<div class="col-lg-4 inner-td">
						<div class="form-group">
							<label for="goods_nm">상품명</label>
							<div class="flex_box">
								<input type='text' class="form-control form-control-sm ac-goods-nm search-enter" name='goods_nm' id="goods_nm" value=''>
							</div>
						</div>
					</div>
				</div>
				<div class="search-area-ext d-none row">
					<div class="col-lg-4 inner-td">
						<div class="form-group">
							<label for="goods_nm_eng">상품명(영문)</label>
							<div class="flex_box">
								<input type='text' class="form-control form-control-sm ac-goods-nm-eng search-enter" name='goods_nm_eng' id="goods_nm_eng" value=''>
							</div>
						</div>
					</div>
					<div class="col-lg-4 inner-td">
						<div class="form-group">
							<label for="">자료수/정렬</label>
							<div class="form-inline">
								<div class="form-inline-inner input_box" style="width:24%;">
									<select name="limit" class="form-control form-control-sm">
										<option value="1000">1000</option>
										<option value="5000">5000</option>
										<option value="10000">10000</option>
									</select>
								</div>
								<span class="text_line">/</span>
								<div class="form-inline-inner input_box" style="width:45%;">
									<select name="ord_field" class="form-control form-control-sm">
										<option value="req_rt">요청일</option>
										<option value="fin_rt">완료일</option>
										<option value="prd_nm">상품명</option>
										<option value="prd_cd">바코드</option>
									</select>
								</div>
								<div class="form-inline-inner input_box sort_toggle_btn" style="width:24%;margin-left:1%;">
									<div class="btn-group" role="group">
										<label class="btn btn-primary primary" for="sort_desc" data-toggle="tooltip" data-placement="top" title="내림차순"><i class="bx bx-sort-down"></i></label>
										<label class="btn btn-secondary" for="sort_asc" data-toggle="tooltip" data-placement="top" title="오름차순"><i class="bx bx-sort-up"></i></label>
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
	function styleState(params){
		if (params.value !== undefined) {
			if (params.data.kind == 'rt_out' || params.data.kind == 'return')
				return {'color': 'red'};
		}
	}

	const pinnedRowData = [{ prd_cd: '합계', qty: 0 }];
	
	let columns = [
		{headerName: "입출고구분",	field: "kind_nm",		width: 70,		cellClass: 'hd-grid-code',	cellStyle: styleState},
		{headerName: "구분",			field: "type_nm",		width: 70,		cellClass: 'hd-grid-code',	cellStyle: styleState},
		{headerName: "처리상태",		field: "state_nm",		width: 65,		cellClass: 'hd-grid-code',	cellStyle: styleState},
		{headerName: "전표번호",		field: "document_number",	width: 65,	cellClass: 'hd-grid-code',	cellStyle: styleState},
		{headerName: "요청일",		field: "req_rt",		width: 80,		cellClass: 'hd-grid-code',	cellStyle: styleState},
		{headerName: "처리중일",		field: "prc_rt",		width: 80,		cellClass: 'hd-grid-code',	cellStyle: styleState},
		{headerName: "완료일",		field: "fin_rt",		width: 80,		cellClass: 'hd-grid-code',	cellStyle: styleState},
		{headerName: "매장명",		field: "dep_store_nm",	width: 110,		cellClass: 'hd-grid-code',	cellStyle: styleState},
		{headerName: "창고/매장명",	field: "target_nm",		width: 110,		cellClass: 'hd-grid-code',	cellStyle: styleState},
		{headerName: "바코드",		field: "prd_cd",		width: 120,		cellClass: 'hd-grid-code',	cellStyle: styleState},
		{headerName: "스타일넘버",	field: "style_no",		width: 90,		cellClass: 'hd-grid-code',	cellStyle: styleState},
		{headerName: "품번",			field: "prd_cd_p",		width: 100,		cellClass: 'hd-grid-code',	cellStyle: styleState},
		{headerName: "상품명",		field: "prd_nm",		width: 300,		cellStyle: styleState,
			cellRenderer: function (params) {
				if (params.data?.goods_no == '' || params.node.aggData?.goods_no == '') {
					return '<a href="javascript:void(0);" onclick="return blank_goods_no();">' + (params.value || '') + '</a>';
				} else {
					let goods_no = params.data ? params.data.goods_no : params.node.aggData ? params.node.aggData.goods_no : '';
					return '<a href="#" onclick="return openHeadProduct(\'' + goods_no + '\');">' + (params.value || '') + '</a>';
				}
			}
		},
		{headerName: "컬러",			field: "color",			width: 50,		cellClass: 'hd-grid-code',	cellStyle: styleState},
		{headerName: "사이즈",		field: "size",			width: 50,		cellClass: 'hd-grid-code',	cellStyle: styleState},
		{headerName: "정상가",		field: "tag_price",		width: 70,		type: 'currencyType',		cellStyle: styleState},
		{headerName: "현재가",		field: "price",			width: 70,		type: 'currencyType',		cellStyle: styleState},
		{headerName: "수량",			field: "qty",			width: 50,		type: 'currencyType',		cellStyle: styleState},
		{headerName: "비고(본사메모)",	field: "comment",	width: 200,		cellStyle: styleState},

		{headerName: "kind",		field: "kind",			hide:true},
		{headerName: "goods_no",	field: "goods_no",		hide:true},
	];
</script>

<script type="text/javascript" charset="utf-8">
	const pApp = new App('', { gridId:"#div-gd", height: 265 });
	let gx;
	
	
	$(document).ready(function() {
		pApp.ResizeGrid(265);
		let gridDiv = document.querySelector(pApp.options.gridId);
		gx = new HDGrid(gridDiv, columns, {
			pinnedTopRowData: pinnedRowData,
			getRowStyle: (params) => {
				if (params.node.rowPinned)  return {'font-weight': 'bold', 'background': '#eee !important', 'border': 'none'};
			}
		});
		pApp.BindSearchEnter();

		// 판매채널 선택되지않았을때 매장구분 disabled처리하는 부분
		load_store_channel();

		//Search();
	});

	function Search() {
		let data = $('form[name="search"]').serialize();
		gx.Request('/store/sale/sal39/search', data, 1, function(e) {
			let total_data	= e.head.total_data;

			const pinnedRowData	= {
				prd_cd : '합계',
				qty: total_data
			};

			gx.gridOptions.api.setPinnedTopRowData([pinnedRowData]);

		});
	}

	function chg_kind() {
		$('#type').empty();

		$('#type').append($("<option value=''>전체</option>"));

		if($('#kind').val() == 'release'){
			$('#type').append($("<option value='F'>초도</option>"));
			$('#type').append($("<option value='S'>판매분</option>"));
			$('#type').append($("<option value='R'>요청분</option>"));
			$('#type').append($("<option value='G'>일반</option>"));
			$('#type').append($("<option value='SG'>창고처리</option>"));
		}

		if($('#kind').val() == 'return'){
			$('#type').append($("<option value='01'>정상반품</option>"));
			$('#type').append($("<option value='02'>불량반품</option>"));
			$('#type').append($("<option value='03'>시즌반품</option>"));
			$('#type').append($("<option value='04'>회수반품</option>"));
		}

		if($('#kind').val() == 'rt_in' || $('#kind').val() == 'rt_out'){
			$('#type').append($("<option value='G'>매장RT</option>"));
			$('#type').append($("<option value='R'>본사RT</option>"));
		}
	}

	function blank_goods_no() {
		alert('온라인코드가 비어있는 상품입니다.');
	}
	
</script>

@stop
	

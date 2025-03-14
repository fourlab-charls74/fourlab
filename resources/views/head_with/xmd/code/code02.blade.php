@extends('head_with.layouts.layout')
@section('title','XMD - 매장 관리')
@section('content')

<div class="page_tit">
	<h3 class="d-inline-flex">매장관리</h3>
	<div class="d-inline-flex location">
		<span class="home"></span>
		<span>/ XMD</span>
		<span>/ 코드</span>
		<span>/ 매장관리</span>
	</div>
</div>

<form method="get" name="search">
	<div id="search-area" class="search_cum_form">
		<div class="card mb-3">

			<div class="d-flex card-header justify-content-between">
				<h4>검색</h4>
				<div class="flax_box">
					<a href="#" id="search_sbtn" onclick="Search();" class="btn btn-sm btn-primary shadow-sm mr-1"><i class="fas fa-search fa-sm text-white-50"></i> 검색</a>
					<a href="#" onclick="Add()" class="btn btn-sm btn-outline-primary shadow-sm pl-2 mr-1"><i class="bx bx-plus fs-16"></i>데이터업로드</a>
					<div id="search-btn-collapse" class="btn-group mb-0 mb-sm-0"></div>
				</div>
			</div>

			<div class="card-body">
				<div class="row">
					<div class="col-lg-4 inner-td">
						<div class="form-group">
							<label for="good_types">매장구분 :</label>
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
							<label for="">매장종류 :</label>
							<div class="flax_box">
								<select name='store_kind' class="form-control form-control-sm">
									<option value=''>전체</option>
									@foreach ($store_kinds as $store_kind)
										<option value='{{ $store_kind->code_id }}'>{{ $store_kind->code_val }}</option>
									@endforeach
								</select>
							</div>
						</div>
					</div>
					<div class="col-lg-4 inner-td">
						<div class="form-group">
							<label for="">매장명 :</label>
							<div class="flax_box">
								<input type='text' class="form-control form-control-sm search-enter" name='com_nm' value=''>
							</div>
						</div>
					</div>
				</div>

				<div class="row">
					<div class="col-lg-4 inner-td">
						<div class="form-group">
							<label for="">매장코드 :</label>
							<div class="flax_box">
								<input type='text' class="form-control form-control-sm search-enter" name='com_id' value=''>
							</div>
						</div>
					</div>
					<div class="col-lg-4 inner-td">
						<div class="form-group">
							<label for="item">사용유무</label>
							<div class="form-inline form-radio-box">
								<div class="custom-control custom-radio">
									<input type="radio" name="use_yn" id="use_yn1" class="custom-control-input" value="Y" checked>
									<label class="custom-control-label" for="use_yn1" value="">Y</label>
								</div>
								<div class="custom-control custom-radio">
									<input type="radio" name="use_yn" id="use_yn2" class="custom-control-input" value="N">
									<label class="custom-control-label" for="use_yn2" value="Y">N</label>
								</div>
							</div>
						</div>
					</div>
					<div class="col-lg-4 inner-td">
						<div class="form-group">
							<label for="item">자료수/정렬 :</label>
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
										<option value="a.com_nm" selected>코드명</option>
										<option value="a.com_id" >코드아이디</option>
										<option value="a.seq" >SEQ</option>
									</select>
								</div>
								<div class="form-inline-inner input_box sort_toggle_btn" style="width:24%;margin-left:1%;">
									<div class="btn-group" role="group">
										<label class="btn btn-primary primary" for="sort_desc" data-toggle="tooltip" data-placement="top" title="" data-original-title="내림차순"><i class="bx bx-sort-down"></i></label>
										<label class="btn btn-secondary" for="sort_asc" data-toggle="tooltip" data-placement="top" title="" data-original-title="오름차순"><i class="bx bx-sort-up"></i></label>
									</div>
									<input type="radio" name="ord" id="sort_desc" value="desc">
									<input type="radio" name="ord" id="sort_asc" value="asc" checked="">
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
		{headerName: "#",			field: "num",			filter:true,width:50,valueGetter: function(params) {return params.node.rowIndex+1;},pinned:'left'},
		{headerName:"매장구분",		field:"com_type_nm",	width:90},
		{headerName:"매장코드",		field:"com_id",			width:90,
			cellRenderer: function(params) {
				return '<a href="#" onClick="popDetail(\''+ params.data.com_id +'\')">'+ params.value+'</a>'
			}
		},
		{headerName:"매장명",		field:"com_nm",			width:90},
		{headerName:"매장종류",		field:"store_kind_nm",	width:100},
		{headerName:"전화",			field:"phone",			width:100},
		{headerName:"모바일",		field:"mobile",			width:100},
		{headerName:"FAX",			field:"fax",			width:100},
		{headerName:"우편번호",		field:"zipcode",		width:100},
		{headerName:"주소",			field:"addr",			width:100},
		{headerName:"개장일",		field:"sdate",			width:100},
		{headerName:"폐점일",		field:"edate",			width:100},
		{headerName:"매니저",		children:[
			{headerName:"매니저명",	field:"manager_nm",		width:100},
			{headerName:"시작일",	field:"manager_sdate",	width:100},
			{headerName:"종료일",	field:"manager_edate",	width:100},
		]},
		{headerName:"매니저보증금",	field:"manager_deposit",width:100, type: 'currencyType'},
		{headerName:"매니저수수료",	children:[
			{headerName:"정상",		field:"manager_fee",	width:100, type: 'currencyType'},
			{headerName:"행사",		field:"manager_sfee",	width:100, type: 'currencyType'},
		]},
		{headerName:"보증금",		children:[
			{headerName:"현금",		field:"deposit_cash",	width:100, type: 'currencyType'},
			{headerName:"담보",		field:"deposit_coll",	width:100, type: 'currencyType'},
		]},
		{headerName:"인테리어",		children:[
			{headerName:"비용",		field:"interior_cost",	width:100, type: 'currencyType'},
			{headerName:"부담",		field:"interior_burden",width:100, type: 'currencyType'},
		]},
		{headerName:"기본수수료",	field:"fee",			width:100, type: 'currencyType'},
		{headerName:"판매수수료율",	field:"sale_fee",		width:100, cellStyle:{"text-align":"right"}},
		{headerName:"사용여부",		field:"use_yn",			width:90}
	];

	function Add()
	{
		const url='/head/xmd/code/code02/show';
		window.open(url,"_blank","toolbar=no,scrollbars=yes,resizable=yes,status=yes,top=500,left=500,width=1200,height=800");
	}

	function popDetail(com_id){
		const url='/head/xmd/code/code02/view/' + com_id;
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
		gx.Request('/head/xmd/code/code02/search', data,1);
	}

</script>
@stop

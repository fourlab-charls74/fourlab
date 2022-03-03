@extends('head_with.layouts.layout')
@section('title','XMD - 시스템 부속코드 관리')
@section('content')

<div class="page_tit">
	<h3 class="d-inline-flex">시스템부속코드관리</h3>
	<div class="d-inline-flex location">
		<span class="home"></span>
		<span>/ XMD</span>
		<span>/ 코드</span>
		<span>/ 시스템부속코드관리</span>
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
							<label for="good_types">코드구분 :</label>
							<div class="flax_box">
								<select name='code_kind_cd' class="form-control form-control-sm">
									<option value=''>전체</option>
									@foreach ($code_kinds as $code_kind)
										<option value='{{ $code_kind->code_kind_cd }}'>{{ $code_kind->code_kind_nm }}</option>
									@endforeach
								</select>
							</div>
						</div>
					</div>
					<div class="col-lg-4 inner-td">
						<div class="form-group">
							<label for="">코드ID :</label>
							<div class="flax_box">
								<input type='text' class="form-control form-control-sm search-enter" name='code_id' value=''>
							</div>
						</div>
					</div>
					<div class="col-lg-4 inner-td">
						<div class="form-group">
							<label for="">코드 :</label>
							<div class="flax_box">
								<input type='text' class="form-control form-control-sm search-enter" name='code_val' value=''>
							</div>
						</div>
					</div>
				</div>

				<div class="row">
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
										<option value="a.code_val" selected>코드명</option>
										<option value="a.code_id" >코드아이디</option>
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
		{headerName: "#", field: "num",type:'NumType'},
		{field: "code_kind_nm", headerName: "코드구분", width:100},
		{field: "code_id", headerName: "코드ID", width:100},
		{field: "code_val", headerName: "코드명", width: 250},
		{field: "code_val2", headerName: "코드명2", width: 200},
		{field: "code_val3", headerName: "코드명3", width: 200},
		{field: "code_val_eng", headerName: "코드명영문", width: 200},
		{field: "code_seq", headerName: "코드순서"},
		{field: "use_yn", headerName: "사용"},
		{field: "admin_id", headerName: "관리자ID"},
		{field: "admin_nm", headerName: "관리자명"},
		{field: "rt", headerName: "등록일"},
		{field: "ut", headerName: "수정일"},
	];

	function Add()
	{
		const url='/head/xmd/code/code01/show';
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
		gx.Request('/head/xmd/code/code01/search', data,1);
	}

</script>
@stop

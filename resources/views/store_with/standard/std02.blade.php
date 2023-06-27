@extends('store_with.layouts.layout')
@section('title','매장정보관리')
@section('content')
<div class="page_tit">
    <h3 class="d-inline-flex">매장정보관리</h3>
    <div class="d-inline-flex location">
        <span class="home"></span>
        <span>/ 기준정보관리</span>
        <span>/ 매장정보관리</span>
    </div>
</div>
<form method="get" name="search">
	<div id="search-area" class="search_cum_form">
		<div class="card mb-3">

			<div class="d-flex card-header justify-content-between">
				<h4>검색</h4>
				<div class="flax_box">
					<a href="#" id="search_sbtn" onclick="Search();" class="btn btn-sm btn-primary shadow-sm mr-1"><i class="fas fa-search fa-sm text-white-50"></i> 검색</a>
					<a href="#" onclick="Add()" class="btn btn-sm btn-outline-primary shadow-sm pl-2 mr-1"><i class="bx bx-plus fs-16"></i>등록</a>
					<div id="search-btn-collapse" class="btn-group mb-0 mb-sm-0"></div>
				</div>
			</div>

			<div class="card-body">
				<div class="row">
					<div class="col-lg-4 inner-td">
						<div class="form-group">
							<label for="good_types">판매채널/매장구분</label>
							<div class="d-flex align-items-center">
								<div class="flex_box w-100">
									<select name='store_channel' id="store_channel" class="form-control form-control-sm" onchange="chg_store_channel();">
										<option value=''>전체</option>
									@foreach ($store_channel as $sc)
										<option value='{{ $sc->store_channel_cd }}'>{{ $sc->store_channel }}</option>
									@endforeach
									</select>
								</div>
								<span class="mr-2 ml-2">/</span>
								<div class="flex_box w-100">
									<select id='store_channel_kind' name='store_channel_kind' class="form-control form-control-sm" disabled>
										<option value=''>전체</option>
									@foreach ($store_kind as $sk)
										<option value='{{ $sk->store_kind_cd }}'>{{ $sk->store_kind }}</option>
									@endforeach
									</select>
								</div>
							</div>
						</div>
					</div>
					<div class="col-lg-4 inner-td" style="display:none">
						<div class="form-group">
							<label for="">매장종류</label>
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
							<label for="">매장명</label>
							<div class="flax_box">
								<input type='text' class="form-control form-control-sm search-enter" name='store_nm' value=''>
							</div>
						</div>
					</div>
					<div class="col-lg-4 inner-td">
						<div class="form-group">
							<label for="">매장지역</label>
							<div class="flax_box">
								<select name='store_area' class="form-control form-control-sm">
									<option value=''>전체</option>
									@foreach ($store_areas as $store_area)
										<option value='{{ $store_area->code_id }}'>{{ $store_area->code_val }}</option>
									@endforeach
								</select>
							</div>
						</div>
					</div>
				</div>

				<div class="row">
					<!-- <div class="col-lg-4 inner-td">
						<div class="form-group">
							<label for="">매장코드 :</label>
							<div class="flax_box">
								<input type='text' class="form-control form-control-sm search-enter" name='store_cd' value=''>
							</div>
						</div>
					</div> -->
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
										<option value="a.store_nm" >매장명</option>
										<option value="a.store_cd" >매장코드</option>
										<option value="a.reg_date" selected>등록일</option>
									</select>
								</div>
								<div class="form-inline-inner input_box sort_toggle_btn" style="width:24%;margin-left:1%;">
									<div class="btn-group" role="group">
										<label class="btn btn-primary primary" for="sort_desc" data-toggle="tooltip" data-placement="top" title="" data-original-title="내림차순"><i class="bx bx-sort-down"></i></label>
										<label class="btn btn-secondary" for="sort_asc" data-toggle="tooltip" data-placement="top" title="" data-original-title="오름차순"><i class="bx bx-sort-up"></i></label>
									</div>
									<input type="radio" name="ord" id="sort_desc" value="desc" checked="">
									<input type="radio" name="ord" id="sort_asc" value="asc" >
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>

		<div class="resul_btn_wrap mb-3">
			<a href="#" id="search_sbtn" onclick="Search();" class="btn btn-sm btn-primary shadow-sm mr-1"><i class="fas fa-search fa-sm text-white-50"></i> 검색</a>
			<a href="#" onclick="Add()" class="btn btn-sm btn-outline-primary shadow-sm pl-2 mr-1"><i class="bx bx-plus fs-16"></i> 등록</a>
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
		{headerName: "#",			field: "num",			filter:true,width:40,valueGetter: function(params) {return params.node.rowIndex+1;}, cellStyle:{"text-align":"center"},pinned:'left'},
		{headerName:"매장코드",		field:"store_cd",			width:60, cellStyle:{"text-align":"center"}},
		// {headerName:"매장구분(구)",		field:"store_type_nm",	width:90, cellStyle:{"text-align":"center"}},
		// {headerName:"매장종류",		field:"store_kind_nm",	width:100, cellStyle:{"text-align":"center"}},
		{headerName:"판매채널",		field:"store_channel",	width:90, cellStyle:{"text-align":"center"}},
		{headerName:"매장구분",		field:"store_channel_kind",	width:90, cellStyle:{"text-align":"center"}},
		{headerName:"매장명",		field:"store_nm", type: 'StoreNameType'},
		{headerName:"매니저수수료등급",		field:"grade_nm",	width:110, cellStyle:{"text-align":"center"}},
		{headerName:"전화",			field:"phone",			width:100},
		{headerName:"모바일",		field:"mobile",			width:100},
		{headerName:"FAX",			field:"fax",			width:100},
		{headerName:"지역",			field:"store_area_nm",	width:72, cellStyle:{"text-align":"center"}},
		{headerName:"주소",			field:"addr1",			width:240},
		{headerName:"개장일",		field:"sdate",			width:60},
		{headerName:"폐점일",		field:"edate",			width:60},
		{headerName:"매니저",		children:[
			{headerName:"매니저명",	field:"manager_nm",		width:100},
			{headerName:"연락처",	field:"manager_mobile",	width:100},
		]},
		{headerName:"보증금",		children:[
			{headerName:"매장",		field:"deposit_cash",	width:72, type: 'currencyType'},
			{headerName:"담보",		field:"deposit_coll",	width:72, type: 'currencyType'},
		]},
		// {headerName:"점포수수료",	field:"fee", width:80, type: 'currencyType',
		// 	cellRenderer:function(params) {
		// 		if (params.data.fee == 0 || params.data.fee == null) {
		// 			return 0;
		// 		} else {
		// 			return '<a href="#" onClick="chargePopup(\''+ params.data.store_cd +'\')">'+ Comma(params.data.fee)+'</a>'
		// 		}
		// 	}
		// },
		{headerName:"판매수수료율",	field:"sale_fee",		width:84, cellStyle:{"text-align":"right"}},
		{headerName:"사용여부",		field:"use_yn",			width:60, cellStyle:{"text-align":"center"}},
		{headerName:"",				field:"",				width:0}
	];

	function Add()
	{
		const url='/store/standard/std02/show';
		window.open(url,"_blank","toolbar=no,scrollbars=yes,resizable=yes,status=yes,top=500,left=500,width=1200,height=800");
	}

	function popDetail(store_cd){
		const url='/store/standard/std02/show/' + store_cd;
		window.open(url,"_blank","toolbar=no,scrollbars=yes,resizable=yes,status=yes,top=500,left=500,width=1200,height=800");
	}

	//점포수수료 팝업
	function chargePopup(store_cd){
		const url='/store/standard/std02/charge/' + store_cd;
		window.open(url,"_blank","toolbar=no,scrollbars=yes,resizable=yes,status=yes,top=500,left=500,width=1000,height=350");
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
		gx.Request('/store/standard/std02/search', data,1);
	}

	// 판매채널 셀렉트박스가 선택되지 않으면 매장구분 셀렉트박스는 disabled처리
	$(document).ready(function() {
		const store_channel = document.getElementById("store_channel");
		const store_channel_kind = document.getElementById("store_channel_kind");

		store_channel.addEventListener("change", () => {
			if (store_channel.value) {
				store_channel_kind.disabled = false;
			} else {
				store_channel_kind.disabled = true;
			}
		});
	});

	// 판매채널이 변경되면 해당 판매채널의 매장구분을 가져오는 부분
	function chg_store_channel() {

		const sel_channel = document.getElementById("store_channel").value;

		$.ajax({
			method: 'post',
			url: '/store/standard/std02/show/chg-store-channel',
			data: {
				'store_channel' : sel_channel
				},
			dataType: 'json',
			success: function (res) {
				if(res.code == 200){
					$('#store_channel_kind').empty();
					let select =  $("<option value=''>전체</option>");
					$('#store_channel_kind').append(select);

					for(let i = 0; i < res.store_kind.length; i++) {
						let option = $("<option value="+ res.store_kind[i].store_kind_cd +">" + res.store_kind[i].store_kind + "</option>");
						$('#store_channel_kind').append(option);
					}

				} else {
					alert('처리 중 문제가 발생하였습니다. 다시 시도하여 주십시오.');
				}
			},
			error: function(e) {
				console.log(e.responseText)
			}
		});
	}	

</script>
@stop

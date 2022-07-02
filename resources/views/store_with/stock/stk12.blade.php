@extends('store_with.layouts.layout')
@section('title','판매일보')
@section('content')
<div class="page_tit">
	<h3 class="d-inline-flex">초도배분</h3>
	<div class="d-inline-flex location">
		<span class="home"></span>
		<span>매장관리</span>
		<span>/ 반품</span>
	</div>
</div>
<form method="get" name="search">
	<div id="search-area" class="search_cum_form">
		<div class="card mb-3">

			<div class="d-flex card-header justify-content-between">
				<h4>검색 - 어떻게 하면 편안하게 매장별로 초도배분 할 수 있을까?</h4>
				<div class="flax_box">
					<a href="#" id="search_sbtn" onclick="Search();" class="btn btn-sm btn-primary shadow-sm mr-1"><i class="fas fa-search fa-sm text-white-50"></i> 검색</a>
					<div id="search-btn-collapse" class="btn-group mb-0 mb-sm-0"></div>
				</div>
			</div>

			<div class="card-body">
				<div class="row">
					<div class="col-lg-4 inner-td">
						<div class="form-group">
							<label for="">반품상태</label>
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
                    <div class="col-lg-4 inner-td">
                        <div class="form-group">
                            <label for="">상품코드</label>
                            <div class="flax_box">
                                <input type='text' class="form-control form-control-sm search-enter" name='goods_code' value=''>
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
        {field: "",	headerName: "반품상태"},
		{field: "",	headerName: "상품코드"},
		{field: "",	headerName: "스타일넘버"},
		{field: "",	headerName: "상품명"},
		{field: "",	headerName: "옵션"},
		{field: "",	headerName: "수량"},
        {field: "",	headerName: "매장코드"},
        {field: "",	headerName: "주문번호"},
        {field: "",	headerName: "주문일련번호"},
        {field: "",	headerName: "주문상태"},
        {field: "",	headerName: "클레임상태"},
        {field: "",	headerName: "반품요청일시"},
        {field: "",	headerName: "반품완료일시"},
		{field: "",	headerName: "반품처리자"},
        {field: "nvl",headerName: ""}
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

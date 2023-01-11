@extends('shop_with.layouts.layout')
@section('title','배분현황')
@section('content')
<div class="page_tit">
	<h3 class="d-inline-flex">배분현황</h3>
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
							<label for="good_types">배분일자</label>
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
							<label for="">구분</label>
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
							<label for="">출고일자</label>
							<div class="flax_box">
								<input type='text' class="form-control form-control-sm search-enter" name='com_nm' value=''>
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
                    <h6 class="m-0 font-weight-bold">총 <span id="gd-total" class="text-primary">0</span> 건</h6>
                </div>
                <div class="fr_box">
                    <a href="#" onclick="SaveSelectedProducts();" class="btn btn-sm btn-primary shadow-sm pl-2">초도배분</a>
                    <a href="#" onclick="SaveSelectedProducts();" class="btn btn-sm btn-primary shadow-sm pl-2">주문배분</a>
                    <a href="#" onclick="SaveSelectedProducts();" class="btn btn-sm btn-primary shadow-sm pl-2">판매배분</a>
                    <a href="#" onclick="SaveSelectedProducts();" class="btn btn-sm btn-primary shadow-sm pl-2">매장배분</a>
                    <div class="btn-group dropleftbtm">
                        <button type="button" class="btn btn-primary waves-light waves-effect dropdown-toggle btn-sm pr-1" data-toggle="dropdown" aria-expanded="false">
                            <i class="fa fa-folder"></i> <i class="bx bx-chevron-down fs-12"></i>
                        </button>
                        <div class="dropdown-menu" style="">
                            <a class="dropdown-item" href="#" onclick="AddProducts();">일괄등록</a>
                            <a class="dropdown-item" href="#" onclick="EditProducts();">일괄수정</a>
                            <a class="dropdown-item" href="#" onclick="ShowProductImages();">이미지보기</a>
                            <a class="dropdown-item" href="#" onclick="AddProductImages();">이미지일괄등록</a>
                        </div>
                        <input type="hidden" name="data" id="data" value="" />
                    </div>
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
        {field: "",	headerName: "배분구분"},
        {field: "",	headerName: "매장코드"},
        {field: "",	headerName: "매장명"},
        {field: "",	headerName: "상품코드"},
        {field: "",	headerName: "상품명"},
        {field: "",	headerName: "칼라"},
        {field: "",	headerName: "칼라명"},
        {field: "",	headerName: "합계"},
        {field: "",	headerName: "사이즈",
            children: [
                {headerName: "XS", field: ""},
                {headerName: "S", field: ""},
                {headerName: "L", field: ""},
                {headerName: "XL", field: ""},
                {headerName: "32", field: ""},
                {headerName: "34", field: ""},
                {headerName: "36", field: ""},
            ]
        },
        {headerName: "", field: "nvl"}
	];
	function Add()
	{
		const url='/head/xmd/shop/store01/show';
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
		gx.Request('/shop/sale/sal01/search', data,1);
	}

</script>
@stop

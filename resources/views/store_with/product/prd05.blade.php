@extends('store_with.layouts.layout')
@section('title','상품가격 관리')
@section('content')

	<div class="page_tit">
		<h3 class="d-inline-flex">상품가격 관리</h3>
		<div class="d-inline-flex location">
			<span class="home"></span>
			<span>/ 상품관리</span>
		</div>
	</div>
	<form method="get" name="search" id="search">
		@csrf
		<input type='hidden' name='goods_nos' value=''>
		<div id="search-area" class="search_cum_form">
			<div class="card mb-3">
				<div class="d-flex card-header justify-content-between">
					<h4>검색</h4>
					<div>
                        <a href="#" onclick="Add();" class="btn btn-sm btn-outline-primary shadow-sm pl-2"><i class="bx bx-plus fs-16"></i> 가격변경 추가</a>
						<div id="search-btn-collapse" class="btn-group mb-0 mb-sm-0"></div>
					</div>
				</div>
				<div class="card-body">
					<div class="row">
						<div class="col-lg-4 inner-td">
							<div class="form-group">
								<label>바코드</label>
								<div class="flex_box">
									<input type='text' id="prd_cd" name='prd_cd' class="form-control form-control-sm ac-style-no search-enter">
									<a href="#" class="btn btn-sm btn-outline-primary sch-prdcd" hidden><i class="bx bx-dots-horizontal-rounded fs-16"></i></a>
								</div>
							</div>
						</div>
						<div class="col-lg-4 inner-td">
							<div class="form-group">
								<label for="style_no">스타일넘버/온라인코드</label>
								<div class="form-inline">
									<div class="form-inline-inner input_box">
										<input type='text' class="form-control form-control-sm ac-style-no search-enter" name='style_no' id="style_no">
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
					<div class="row">
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
								<label for="goods_stat">전시상태</label>
								<div class="flex_box">
									<select name="goods_stat[]" class="form-control form-control-sm multi_select w-100" multiple>
										
									</select>
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
											<input type='text' id="prd_cd_range_nm" name='prd_cd_range_nm' onclick="openApi();" class="form-control form-control-sm w-100 ac-style-no" readonly style="background-color: #fff;">
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
								<label for="">자료수/정렬</label>
								<div class="form-inline">
									<div class="form-inline-inner input_box" style="width:24%;">
										<select name="limit" class="form-control form-control-sm">
											<option value="">모두</option>
											<option value="1000" selected>1000</option>
											<option value="5000">5000</option>
											<option value="10000">10000</option>
										</select>
									</div>
									<span class="text_line">/</span>
									<div class="form-inline-inner input_box" style="width:45%;">
										<select name="ord_field" class="form-control form-control-sm">
											<option value="prd_cd1">등록일(품번별)</option>
											<option value="pc.rt">등록일</option>
											<option value="pc.ut">수정일</option>
											<option value="g.goods_no">온라인코드</option>
											<option value="g.goods_nm">상품명</option>
											<option value="pc.prd_cd">바코드</option>
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
				<!-- <a href="#" id="search_sbtn" onclick="return Search();" class="btn btn-sm btn-primary shadow-sm pl-2"><i class="fas fa-search fa-sm text-white-50"></i> 조회</a>
				<a href="#" onclick="AddProduct_upload();" class="btn btn-sm btn-outline-primary shadow-sm pl-2"><i class="bx bx-plus fs-16"></i> 바코드 등록</a>
				<a href="#" onclick="AddProduct();" class="btn btn-sm btn-outline-primary shadow-sm pl-2"><i class="bx bx-plus fs-16"></i> 상품 매칭</a>
				<a href="#" onclick="AddProducts();" class="btn btn-sm btn-outline-primary shadow-sm pl-2"><i class="bx fs-16"></i> 상품일괄매칭</a>
				<a href="#" onclick="initSearchInputs()" class="btn btn-sm btn-outline-primary mr-1">검색조건 초기화</a>
				<a href="#" onclick="gx.Download();" class="btn btn-sm btn-outline-primary shadow-sm pl-2"><i class="bx bx-download fs-16"></i> 엑셀다운로드</a> -->
			<div class="search_mode_wrap btn-group mr-2 mb-0 mb-sm-0"></div>
		</div>
	</div>
</form>
<div id="filter-area" class="card shadow-none mb-0 search_cum_form ty2 last-card">
    <div class="card-body shadow">
        <div class="card-title mb-3">
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
<script>
    let columns = [
            {field: "change_date", headerName: "변경일자", width: 100, cellClass: 'hd-grid-code'},
            {field: "change_date", headerName: "변경금액", width: 100, cellClass: 'hd-grid-code'},
            {field: "change_kind", headerName: "변경종류", width: 100, cellClass: 'hd-grid-code'},
            {field: "change_cnt", headerName: "변경상품수", width: 100, cellClass: 'hd-grid-code'},
            {field: "rt", headerName: "등록일자", width: 100, cellClass: 'hd-grid-code'},
            {width : 'auto'}
            
        ];
</script>

<script type="text/javascript" charset="utf-8">
    const pApp = new App('', {
        gridId: "#div-gd",
    });
    let gx;

    $(document).ready(function() {
        pApp.ResizeGrid(275);
        pApp.BindSearchEnter();
        let gridDiv = document.querySelector(pApp.options.gridId);
        gx = new HDGrid(gridDiv, columns);
        // Search();
    });

    // function Search() {
    //     let data = $('form[name="search"]').serialize();
    //     gx.Request('/store/system/sys02/search', data);
    // }

    function Add () {
        const url = '/store/product/prd05/show/';
        window.open(url, "_blank", "toolbar=no,scrollbars=yes,resizable=yes,status=yes,top=300,left=300,width=1000,height=880");
    };
</script>
	
@stop

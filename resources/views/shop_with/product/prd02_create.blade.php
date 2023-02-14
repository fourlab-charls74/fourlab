@extends('shop_with.layouts.layout-nav')
@section('title', '상품매칭 ')
@section('content')



<div class="show_layout py-3 px-sm-3">
	<div class="page_tit d-flex justify-content-between">
		<div class="d-flex">
			<h3 class="d-inline-flex">상품매칭</h3>
			<div class="d-inline-flex location">
				<span class="home"></span>
				<span>/ 상품관리</span>
				<span>/ 상품관리(코드)</span>
			</div>
		</div>
		<div class="d-flex">
			{{-- <!--<a href="javascript:void(0)" onclick="Cmder('delete')" class="btn btn-primary mr-1"><i class="fas fa-trash fa-sm text-white-50 mr-1"></i>삭제</a>//--> --}}
			<a href="javascript:void(0)" onclick="window.close();" class="btn btn-outline-primary"><i class="fas fa-times fa-sm mr-1"></i>닫기</a>
		</div>
	</div>

	<style> 
		.required:after {content:" *"; color: red;}
		.table th {min-width:120px;}

		@media (max-width: 740px) {
			.table td {float: unset !important;width:100% !important;}
		}
	</style>

		<div class="card_wrap aco_card_wrap">
			<div class="card shadow">
                <div class="card-header mb-0">
                    <ul class="nav nav-tabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="tab-nav-1" data-toggle="tab" href="#tab-1" role="tab" aria-controls="send" aria-selected="false">코드 생성 및 매칭</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="tab-nav-2" data-toggle="tab" href="#tab-2" role="tab" aria-controls="list" aria-selected="false">매칭</button>
                        </li>
                       
                    </ul>
                </div>
				<br>
				<div class="tab-content" id="myTabContent">
            		<div class="tab-pane fade" id="tab-1" role="tabpanel" aria-labelledby="send-tab">
						<div class="card-header mb-0" style="display:inline-block;">
							<a href="#">기본 정보</a>
						</div>
						<div style="display: inline-block;float:right">
							<a href="javascript:void(0)" onclick="addPrdCd();" class="btn btn-primary mr-1"><i class="fas fa-save fa-sm text-white-50 mr-1"></i>저장</a>
						</div>
						<div class="card-body">
							<div class="row">
								<div class="col-12">
									<div class="table-box-ty2 mobile">
										<form name="f1" id="f1">
											<table class="table incont table-bordered" width="100%" cellspacing="0">
												<tbody>
													<tr>
														<th class="required" style="text-align:center;vertical-align:middle;">상품번호</th>
														<td colspan="3">
														<div class="flax_box">
																<div class="form-inline-inner inline_btn_box">
																	<input type='text' class="form-control form-control-sm search-enter" style="width:100%;" name='goods_no' id='goods_no' value=''>
																	<a href="#" class="btn btn-sm btn-outline-primary sch-goods_no"><i class="bx bx-dots-horizontal-rounded fs-16"></i></a>
																</div>
															</div>
														</td>
													</tr>
													<tr>
														<th class="required" style="text-align:center;vertical-align:middle;">브랜드</th>
														<td style="width:35%;">
															<div class="flax_box">
																<select name='brand' class="form-control form-control-sm">
																	<option value=''>선택</option>
																	@foreach ($brands as $brand)
																		<option value='{{ $brand->br_cd }}'>{{ $brand->br_cd }} : {{ $brand->brand_nm }}</option>
																	@endforeach
																</select>
															</div>
														</td>
														<th class="required" style="text-align:center;vertical-align:middle;">년도</th>
														<td style="width:35%;">
															<div class="flax_box">
																<select name='year' class="form-control form-control-sm">
																	<option value=''>선택</option>
																	@foreach ($years as $year)
																		<option value='{{ $year->code_id }}'>{{ $year->code_id }} : {{ $year->code_val }}</option>
																	@endforeach
																</select>
															</div>
														</td>
													</tr>
													<tr>
														<th class="required" style="text-align:center;vertical-align:middle;">시즌</th>
														<td>
															<div class="flax_box">
																<select name='season' class="form-control form-control-sm">
																	<option value=''>선택</option>
																	@foreach ($seasons as $season)
																		<option value='{{ $season->code_id }}'>{{ $season->code_id }} : {{ $season->code_val }}</option>
																	@endforeach
																</select>
															</div>
														</td>
														<th class="required" style="text-align:center;vertical-align:middle;">성별</th>
														<td>
															<div class="flax_box">
																<select name='gender' class="form-control form-control-sm">
																	<option value=''>선택</option>
																	@foreach ($genders as $gender)
																		<option value='{{ $gender->code_id }}'>{{ $gender->code_id }} : {{ $gender->code_val }}</option>
																	@endforeach
																</select>
															</div>
														</td>
													</tr>
													<tr>
														<th class="required" style="text-align:center;vertical-align:middle;">아이템</th>
														<td>
															<div class="flax_box">
																<select name='item' class="form-control form-control-sm">
																	<option value=''>선택</option>
																	@foreach ($items as $item)
																		<option value='{{ $item->code_id }}'>{{ $item->code_id }} : {{ $item->code_val }}</option>
																	@endforeach
																</select>
															</div>
														</td>
														<th class="required" style="text-align:center;vertical-align:middle;">품목</th>
														<td>
															<div class="flax_box">
																<select name='opt' class="form-control form-control-sm">
																	<option value=''>선택</option>
																	@foreach ($opts as $opt)
																		<option value='{{ $opt->code_id }}'>{{ $opt->code_id }} : {{ $opt->code_val }}</option>
																	@endforeach
																</select>
															</div>
														</td>
													</tr>
													
												</tbody>
											</table>
											<div style="width:100%;padding-top:20px;text-align:center;">
												<button type="button" class="btn btn-primary ml-2" onclick="getOption()">옵션불러오기</button>
											</div>
											
										</form>
									</div>
								</div>
							</div>
						</div>
							<div class="card">
								<div class="card-header mb-0">
									<a href="#">상품코드정보</a>
								</div>
								<div class="card-body pt-2">
									<div class="card-title">
										<div class="filter_wrap">
											<div class="fl_box px-0 mx-0">
												<h6 class="m-0 font-weight-bold">총 : <span id="gd-total" class="text-primary">0</span> 건</h6>
											</div>
											<div class="fr_box">
											</div>
										</div>
									</div>
									<div class="table-responsive">
										<div id="div-gd" class="ag-theme-balham"></div>
									</div>
								</div>
							</div>		
						</div>
						
						<div class="tab-pane fade" id="tab-2" role="tabpanel" aria-labelledby="list-tab">
							<div class="card-header mb-0" style="display:inline-block;">
								<a href="#">기본 정보</a>
							</div>
							<div style="display: inline-block;float:right">
								<a href="javascript:void(0)" onclick="addPrdCd_product();" class="btn btn-primary mr-1"><i class="fas fa-save fa-sm text-white-50 mr-1"></i>저장</a>
							</div>
							<div class="card-body">
								<div class="row">
									<div class="col-12">
										<div class="table-box-ty2 mobile">
											<form name="f2" id="f2">
												<table class="table incont table-bordered" width="100%" cellspacing="0">
													<tbody>
														<tr>
															<th class="required" style="text-align:center;vertical-align:middle;">상품코드(매칭 X)</th>
															<td>
															<div class="flex_box">
																<input type='text' id="prd_cd" name='prd_cd' class="form-control form-control-sm ac-style-no search-enter">
																<a href="#" class="btn btn-sm btn-outline-primary sch-prdcd" hidden><i class="bx bx-dots-horizontal-rounded fs-16"></i></a>
															</div>
															</td>
															<th class="required" style="text-align:center;vertical-align:middle;">상품번호</th>
															<td>
																<div class="flax_box">
																	<div class="form-inline-inner inline_btn_box">
																		<input type='text' class="form-control form-control-sm search-enter" style="width:100%;" name='goods_no2' id='goods_no2' value=''>
																		<a href="#" class="btn btn-sm btn-outline-primary sch-goods_no2"><i class="bx bx-dots-horizontal-rounded fs-16"></i></a>
																	</div>
																</div>
															</td>
														</tr>
													</tbody>
												</table>

											<div style="width:100%;padding-top:20px;text-align:center;">
												<button type="button" class="btn btn-primary ml-2" onclick="getOption_goods_code()">옵션불러오기</button>
											</div>
											</form>
										</div>
									</div>
								</div>
							</div>
							<div class="card">
								<div class="card-header mb-0">
									<a href="#">상품코드정보</a>
								</div>
								<div class="card-body pt-2">
									<div class="card-title">
										<div class="filter_wrap">
											<div class="fl_box px-0 mx-0">
												<h6 class="m-0 font-weight-bold">총 : <span id="gd-code-total" class="text-primary">0</span> 건</h6>
											</div>
											<div class="fr_box">
											</div>
										</div>
									</div>
									<div class="table-responsive">
										<div id="div-gd-code" class="ag-theme-balham"></div>
									</div>
								</div>
							</div>		
						</div>	
					</div>
				</div>
			</div>
		</form>
	</div>
</div>


<script>
	const columns = [
		{field: "chk", headerName: '', cellClass: 'hd-grid-code', headerCheckboxSelection: true, checkboxSelection: true, width: 30, pinned: 'left', sort: null,
            checkboxSelection: function(params) {
                return params.data.match_yn == '';
            },
		},
		{field:"goods_no",	headerName: "상품번호",		width:72},
		{field:"style_no",	headerName: "스타일넘버",	width:72},
		{field:"goods_nm",	headerName: "상품명",		width:250},
		{field:"goods_opt",	headerName: "상품옵션",		width:200},
		{field:"prd_cd1",	headerName: "상품코드",		width:120},
		{field:"color",		headerName: "컬러",			width:72},
		{field:"size",		headerName: "사이즈",		width:72},
		{field:"match_yn", headerName: "매칭여부",		width:72},
		{field:"brand",		headerName:"브랜드",		hide:true},
		{field:"year",		headerName:"년도",			hide:true},
		{field:"season",	headerName:"시즌",			hide:true},
		{field:"gender",	headerName:"성별",			hide:true},
		{field:"item",		headerName:"아이템",		hide:true},
		{field:"opt",		headerName:"품목",			hide:true},
		{field:"seq",		headerName:"순서차수",		hide:true},
		{field: "", headerName:"", width:"auto"},
	];

	const columns_code = [
		{field: "chk", headerName: '', cellClass: 'hd-grid-code', headerCheckboxSelection: true, checkboxSelection: true, width: 30, pinned: 'left', sort: null,
            checkboxSelection: function(params) {
				return params.data.checkbox == true && params.data.is_product == 'Y';
            },
		},
		{field:"goods_no",	headerName: "상품번호",		width:72},
		{field:"style_no", headerName: "스타일넘버",	width:72},
		{field:"prd_nm", headerName: "상품명",			width:250},
		{field:"goods_opt",	headerName: "상품옵션",		width:200,
			cellRenderer:function(params){
				return params.data.goods_opt
			}
		},
		{field:"prd_cd",	headerName: "상품코드",		width:120},
		{field:"color",		headerName: "컬러",			width:72},
		{field:"size",		headerName: "사이즈",		width:72},
		{field:"", headerName: "알림",		width:120,
			cellRenderer: function(params) {
				if(params.data.checkbox == false && params.data.is_product == 'Y' ) {
					return "매칭할 수 없는 상품입니다."
				}
			},
			cellStyle: {'color':'red'},

		},
		{field:"match_yn", headerName: "매칭여부",		width:72, hide:true},
		{field:"seq",		headerName:"순서차수", hide:true},
		{field: "", headerName:"", width:"auto"},

	];
</script>
<script type="text/javascript" charset="utf-8">

    const pApp = new App('', {
        gridId: "#div-gd",
    });
	let gx;

	$(document).ready(function() {
		pApp.ResizeGrid(637);
		pApp.BindSearchEnter();
		let gridDiv = document.querySelector(pApp.options.gridId);
		gx = new HDGrid(gridDiv, columns);
		gx.gridOptions.rowDragManaged = true;
		gx.gridOptions.animateRows = true;
		//Search();
	});

    const pApp2 = new App('', {
        gridId: "#div-gd-code",
    });
	let gx2;

	$(document).ready(function() {
		pApp2.ResizeGrid(475);
		pApp2.BindSearchEnter();
		let gridDiv2 = document.querySelector(pApp2.options.gridId);
		gx2 = new HDGrid(gridDiv2, columns_code);
		gx2.gridOptions.rowDragManaged = true;
		gx2.gridOptions.animateRows = true;
		//Search();
	});
	
    function Search() {
        let data = $('form[name="f1"]').serialize();
        gx.Request('/shop/product/prd02/prd-search/', data);
    }

    function Search_goods_code() {
        let data = $('form[name="f2"]').serialize();
		console.log(data);
        gx2.Request('/shop/product/prd02/prd-search-code/', data);
    }

	//상품번호별 불러오기
	function getOption(){
		var frm	= $('form[name="f1"]');

		if(!validation()) return;

		Search();
	}

	//상품코드별 불러오기
	function getOption_goods_code(){
		var frm	= $('form[name="f2"]');

		if(!validation_code()) return;

		Search_goods_code();
	}

	const validation = (cmd) => {

		// 상품번호 입력여부
		if(f1.goods_no.value.trim() === '') {
			f1.goods_no.focus();
			return alert("상품번호를 입력해주세요.");
		}

		// 브랜드 선택 여부
		if(f1.brand.selectedIndex == 0) {
			f1.brand.focus();
			return alert("브랜드를 선택해주세요.");
		}

		// 년도 선택여부
		if(f1.year.selectedIndex == 0) {
			f1.year.focus();
			return alert("년도를 선택해주세요.");
		}

		// 시즌 선택여부
		if(f1.season.selectedIndex == 0) {
			f1.season.focus();
			return alert("시즌을 선택해주세요.");
		}

		// 성별 선택여부
		if(f1.gender.selectedIndex == 0) {
			f1.gender.focus();
			return alert("성별을 선택해주세요.");
		}

		// 아이템 선택여부
		if(f1.item.selectedIndex == 0) {
			f1.item.focus();
			return alert("아이템을 선택해주세요.");
		}

		// 품목 선택여부
		if(f1.opt.selectedIndex == 0) {
			f1.opt.focus();
			return alert("품목을 선택해주세요.");
		}

		return true;
	}
	const validation_code = (cmd) => {
		// 상품코드 입력 여부
		if(f2.prd_cd.value.trim() === '') {
			f2.prd_cd.focus();
			return alert("상품코드를 입력해주세요.");
		}

		// 상품번호 입력 여부
		if(f2.goods_no2.value.trim() === '') {
			f2.goods_no2.focus();
			return alert("상품번호를 입력해주세요.");
		}


		return true;
	}

	function addPrdCd(){

		let rows	= gx.getSelectedRows();
		if(rows.length < 1) return alert("저장할 상품코드 정보를 선택해주세요.");

		axios({
			url: '/shop/product/prd02/add-product-code',
			method: 'put',
			data: {
				data: rows, 
			},
		}).then(function (res) {
			if(res.data.code === 200) {
				alert(res.data.msg);
				opener.Search();
				self.close();
			} else {
				console.log(res.data);
				alert("상품코드 등록중 오류가 발생했습니다.\n관리자에게 문의해주세요.");
			}
		}).catch(function (err) {
			console.log(err);
		});
	}

	// 상품코드별 매칭 
	function addPrdCd_product(){

		let rows	= gx2.getSelectedRows();
		let goods_no2_val = document.getElementById('goods_no2').value;

		if(rows.length < 1) return alert("저장할 상품코드 정보를 선택해주세요.");

		axios({
			url: '/shop/product/prd02/add-product-product',
			method: 'put',
			data: {
				data: rows, 
			},
		}).then(function (res) {
			if(res.data.code === 200) {
				alert(res.data.msg);
				opener.Search();
				self.close();
			} else {
				console.log(res.data);
				alert("상품코드 등록중 오류가 발생했습니다.\n관리자에게 문의해주세요.");
			}
		}).catch(function (err) {
			console.log(err);
		});
	}

	$(document).ready(function(){
        $('#tab-nav-1').trigger("click");  
    }); 

	// 상품코드 검색 클릭 이벤트 바인딩 및 콜백 사용
	$(".sch-prdcd").on("click", function() {
		searchPrdcd.Open(null, "match");
    });

</script>


<!-- script -->
@include('store_with.product.prd02_js')
<!-- script -->

@stop

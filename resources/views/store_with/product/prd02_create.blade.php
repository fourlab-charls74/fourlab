@extends('store_with.layouts.layout-nav')
@section('title', '상품코드 등록')
@section('content')



<div class="show_layout py-3 px-sm-3">
	<div class="page_tit d-flex justify-content-between">
		<div class="d-flex">
			<h3 class="d-inline-flex">상품코드 등록</h3>
			<div class="d-inline-flex location">
				<span class="home"></span>
				<span>/ 상품관리</span>
				<span>/ 상품관리(재고)</span>
			</div>
		</div>
		<div class="d-flex">
			<a href="javascript:void(0)" onclick="Cmder('add')" class="btn btn-primary mr-1"><i class="fas fa-save fa-sm text-white-50 mr-1"></i>저장</a>
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

	<form name="f1" id="f1">
		<div class="card_wrap aco_card_wrap">
			<div class="card shadow">
				<div class="card-header mb-0">
					<a href="#">기본 정보</a>
				</div>
				<div class="card-body">
					<div class="row">
						<div class="col-12">
							<div class="table-box-ty2 mobile">
								<table class="table incont table-bordered" width="100%" cellspacing="0">
									<tbody>
										<tr>
											<th class="required">브랜드</th>
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
											<th class="required">년도</th>
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
											<th class="required">시즌</th>
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
											<th class="required">성별</th>
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
											<th class="required">아이템</th>
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
											<th class="required">품목</th>
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
										<tr>
											<th class="required">상품번호</th>
											<td colspan="3">
											<div class="flax_box">
													<div class="form-inline-inner inline_btn_box">
														<input type='text' class="form-control form-control-sm search-enter" style="width:100%;" name='goods_no' id='goods_no' value=''>
														<a href="#" class="btn btn-sm btn-outline-primary sch-goods_no"><i class="bx bx-dots-horizontal-rounded fs-16"></i></a>
													</div>
												</div>
											</td>
										</tr>
									</tbody>
								</table>

								<div style="width:100%;padding-top:20px;text-align:center;">
									<button type="button" class="btn btn-primary ml-2" onclick="getOption()">옵션불러오기</button>
								</div>

							</div>
						</div>
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

	</form>

</div>



<script>
	const columns = [
		{field: "chk", headerName: '', cellClass: 'hd-grid-code', headerCheckboxSelection: true, checkboxSelection: true, width: 30, pinned: 'left', sort: null,
            checkboxSelection: function(params) {
                return params.data.match_yn == '';
            },
		},
		{field: "goods_no", headerName: "상품번호",rowDrag: true,
			width:84
		},
		{field: "goods_nm", headerName: "상품명",
			width:250
		},
		{field: "goods_opt", headerName: "상품옵션",
			width:200
		},
		{field: "prd_cd1", headerName: "상품코드",
			editable: true,
			cellClass:['hd-grid-edit'],
			width:120
		},
		{field: "color", headerName: "컬러",
			editable: true,
			cellClass:['hd-grid-edit'],
			width:80
		},
		{field: "size", headerName: "사이즈",
			editable: true,
			cellClass:['hd-grid-edit'],
			width:80
		},
		{field: "match_yn", headerName: "등록유무",
			width:72
		},
		{field: "", headerName:"", width:"auto"},
	];
</script>
<script type="text/javascript" charset="utf-8">

    const pApp = new App('', {
        gridId: "#div-gd",
    });
	let gx;

	$(document).ready(function() {
		pApp.ResizeGrid(550);
		pApp.BindSearchEnter();
		let gridDiv = document.querySelector(pApp.options.gridId);
		gx = new HDGrid(gridDiv, columns);
		gx.gridOptions.rowDragManaged = true;
		gx.gridOptions.animateRows = true;
		//Search();
	});
	
    function Search() {
        let data = $('form[name="f1"]').serialize();
        gx.Request('/store/product/prd02/prd-search/', data);
    }

	//상품옵션 불러오기
	function getOption(){
		var frm	= $('form[name="f1"]');

		if(!validation()) return;

		Search();
	}

	const validation = (cmd) => {
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

		// 상품번호 입력여부
		if(f1.goods_no.value.trim() === '') {
			f1.goods_no.focus();
			return alert("상품번호를 입력해주세요.");
		}

		return true;
	}
</script>
@stop

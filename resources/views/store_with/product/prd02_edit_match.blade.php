@extends('store_with.layouts.layout-nav')
@section('title', '상품코드 매칭 정보')
@section('content')



<div class="show_layout py-3 px-sm-3">
	<div class="page_tit d-flex justify-content-between">
		<div class="d-flex">
			<h3 class="d-inline-flex">상품코드 매칭 정보</h3>
			<div class="d-inline-flex location">
				<span class="home"></span>
				<span>/ 상품관리</span>
				<span>/ 상품관리(코드)</span>
			</div>
		</div>
		<div class="d-flex">
			<a href="javascript:void(0)" onclick="addPrdCd();" class="btn btn-primary mr-1"><i class="fas fa-save fa-sm text-white-50 mr-1"></i>저장</a>
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
											<th>상품코드</th>
											<td style="width:35%;">
												<div class="flax_box">
													<input type="text" name="prd_cd" id="prd_cd" value="{{ $prd_cd }}" class="form-control form-control-sm" readonly />
												</div>
											</td>
											<th>상품번호</th>
											<td style="width:35%;">
												<div class="flax_box">
													<div class="form-inline-inner inline_btn_box">
														<input type='text' class="form-control form-control-sm search-enter" style="width:100%;" name='goods_no' id='goods_no' value=''>
														<a href="#" class="btn btn-sm btn-outline-primary sch-goods_no"><i class="bx bx-dots-horizontal-rounded fs-16"></i></a>
													</div>
												</div>
											</td>
										</tr>
										<tr>
											<th>상품명</th>
											<td>{{ $product->prd_nm }}</td>
											<th>아이템코드</th>
											<td>{{ $product->style_no }}</td>
										</tr>
										<tr>
											<th>색상</th>
											<td>{{ $product->color }} : {{ $product->color_nm }}</td>
											<th>사이즈</th>
											<td>{{ $product->size }}</td>
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

	const CELL_COLOR = {
		THISPRD: { 'background' : '#F8D3D4' }
	};

	const columns = [
		{field: "chk", headerName: '', cellClass: 'hd-grid-code', headerCheckboxSelection: true, checkboxSelection: true, width: 30, pinned: 'left', sort: null,
			checkboxSelection: function(params) {
				return params.data.match_yn == '';
			},
		},
		{field:"goods_no",	headerName: "상품번호",		width:72},
		{field:"style_no",	headerName: "아이템코드",	width:72},
		{field:"goods_nm",	headerName: "상품명",		width:250},
		{field:"goods_opt",	headerName: "상품옵션",		width:200},
		{field:"prd_cd1",	headerName: "코드일련",		width:120},
		{field:"color",		headerName: "컬러",			width:72},
		{field:"size",		headerName: "사이즈",		width:72},
		{field:"match_yn",  headerName: "등록유무",		width:60, cellStyle:{'text-align':'center'}},
		{field:"prd_cd",	headerName: "상품코드", hide:true},
		{field: "", headerName:"", width:"auto"},
	];
</script>
<script type="text/javascript" charset="utf-8">

	const pApp = new App('', {
		gridId: "#div-gd",
	});
	let gx;

	$(document).ready(function() {
		pApp.ResizeGrid(470);
		pApp.BindSearchEnter();
		let gridDiv = document.querySelector(pApp.options.gridId);

		let options = {
			getRowStyle: (params) => {
				//console.log(params.data);
				if (params.data.prd_cd == $("#prd_cd").val()) return CELL_COLOR.THISPRD;
			}
		}

		gx = new HDGrid(gridDiv, columns, options);
		gx.gridOptions.rowDragManaged = true;
		gx.gridOptions.animateRows = true;
		//Search();
	});
	
	function Search() {
		let data = $('form[name="f1"]').serialize();

		//console.log(data);

		gx.Request('/store/product/prd02/prd-edit-match-search/', data);
	}
	//상품옵션 불러오기
	function getOption(){
		var frm	= $('form[name="f1"]');

		if(!validation()) return;

		Search();
	}

	const validation = (cmd) => {
		// 상품번호 입력여부
		if(f1.goods_no.value.trim() === '') {
			f1.goods_no.focus();
			return alert("상품번호를 입력해주세요.");
		}

		return true;
	}

	function addPrdCd(){
		let rows	= gx.getSelectedRows();
		if(rows.length < 1) return alert("저장할 상품코드 정보를 선택해주세요.");

		//console.log(rows);

		axios({
			url: '/store/product/prd02/edit-match-product-code',
			method: 'put',
			data: {
				data: rows, 
			},
		}).then(function (res) {
			if(res.data.code === 200) {
				alert(res.data.msg);
				opener.Search();
				Search();
			} else {
				console.log(res.data);
				alert("상품코드 등록중 오류가 발생했습니다.\n관리자에게 문의해주세요.");
			}
		}).catch(function (err) {
			console.log(err);
		});
	}
</script>
@stop

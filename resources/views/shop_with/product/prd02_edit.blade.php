@extends('shop_with.layouts.layout-nav')
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
													<input type="text" name="goods_no" id="goods_no" value="{{ $goods_no }}" class="form-control form-control-sm" readonly />
												</div>
											</td>
										</tr>
										<tr>
											<th>상품명</th>
											<td colspan="3">{{ $product->goods_nm }}</td>
										</tr>
										<tr>
											<th>색상</th>
											<td>{{ $product->color }} : {{ $product->color_nm }}</td>
											<th>사이즈</th>
											<td>{{ $product->size }}</td>
										</tr>
										<tr>
											<th>상품옵션</th>
											<td>{{ $product->goods_opt }}</td>
											<th>아이템코드</th>
											<td>{{ $product->style_no }}</td>
										</tr>
									</tbody>
								</table>

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
		{field:"goods_no",	headerName: "상품번호",		width:72},
		{field:"style_no",	headerName: "아이템코드",	width:72},
		{field:"goods_nm",	headerName: "상품명",		width:250},
		{field:"goods_opt",	headerName: "상품옵션",		width:200},
		{field:"prd_cd1",	headerName: "상품코드",		width:120},
		{field:"color",		headerName: "컬러",			width:72},
		{field:"size",		headerName: "사이즈",		width:72},
		{field:"match_yn",  headerName: "등록유무",		width:60, cellStyle:{'text-align':'center'}},
		{field:"del",       headerName: "삭제",  		width:60, cellStyle:{'text-align':'center'},
			cellRenderer: function(params) {
				if (params.value !== undefined) {
					if( params.value != ''){
						return '<a href="#" onclick="return delPrdCd(\'' + params.data.prd_cd + '\');">' + params.value + '</a>';
					}
				}
			}
		},
		{field:"brand",		headerName:"브랜드",		hide:true},
		{field:"year",		headerName:"년도",			hide:true},
		{field:"season",	headerName:"시즌",			hide:true},
		{field:"gender",	headerName:"성별",			hide:true},
		{field:"item",		headerName:"아이템",		hide:true},
		{field:"opt",		headerName:"품목",			hide:true},
		{field:"seq",		headerName:"순서차수",		hide:true},
		{field:"prd_cd",	headerName:"상품코드",		hide:true},
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
				console.log(params.data);
                if (params.data.prd_cd == $("#prd_cd").val()) return CELL_COLOR.THISPRD;
            }
        }

		gx = new HDGrid(gridDiv, columns, options);
		gx.gridOptions.rowDragManaged = true;
		gx.gridOptions.animateRows = true;
		Search();
	});
	
	function Search() {
		let data = $('form[name="f1"]').serialize();

		//console.log(data);

		gx.Request('/shop/product/prd02/prd-edit-search/', data);
	}

	function delPrdCd(prd_cd){
		if(!window.confirm("상품코드를 삭제하면 기존 재고 정보도 함께 삭제됩니다.\r\n삭제하시겠습니까?")) return;

		axios({
			url: '/shop/product/prd02/del-product-code',
			method: 'put',
			data: {
				prd_cd : prd_cd, 
				goods_no : $("#goods_no").val()
			},
		}).then(function (res) {
			if(res.data.code === 200) {
				alert(res.data.msg);
				opener.Search();
				Search();
			} else {
				console.log(res.data);
				alert("상품코드 삭제중 오류가 발생했습니다.\n관리자에게 문의해주세요.");
			}
		}).catch(function (err) {
			console.log(err);
		});
	}
</script>
@stop

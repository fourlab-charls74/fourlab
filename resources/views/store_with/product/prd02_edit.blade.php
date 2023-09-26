@extends('store_with.layouts.layout-nav')
@section('title', '바코드 매칭 정보')
@section('content')



<div class="show_layout py-3 px-sm-3">
	<div class="page_tit d-flex justify-content-between">
		<div class="d-flex">
			<h3 class="d-inline-flex">바코드 매칭 정보</h3>
			<div class="d-inline-flex location">
				<span class="home"></span>
				<span>/ 상품관리</span>
				<span>/ 상품코드관리</span>
			</div>
		</div>
		<div class="d-flex">
			<a href="javascript:void(0)" onclick="save();" class="btn btn-primary mr-1"><i class="fas fa-save fa-sm text-white-50 mr-1"></i>저장</a>
			@if($goods_no == '0')<a href="javascript:void(0)" onclick="match();" class="btn btn-primary mr-1"><i class="fas fa-save fa-sm text-white-50 mr-1"></i>상품매핑</a>@endif
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
											<th>바코드</th>
											<td style="width:35%;">
												<div class="flax_box">
													<input type="text" name="prd_cd" id="prd_cd" value="{{ $prd_cd }}" class="form-control form-control-sm" readonly />
												</div>
											</td>
											<th>온라인코드</th>
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
										<tr>
											<th>정상가</th>
											<td>
												<div class="flax_box">
													<input type='text' class="form-control form-control-sm" name='tag_price' id="tag_price" value='{{ $product->goods_sh }}' onkeyup="onlynum(this)">
												</div>
											</td>
											<th>현재가</th>
											<td>
												<div class="flax_box">
													<input type='text' class="form-control form-control-sm" name='price' id="price" value='{{ $product->price }}' onkeyup="onlynum(this)">
												</div>
											</td>
										</tr>
										<tr>
											<th>사이즈구분</th>
											<td>
												<div class="flax_box">
													<select name="size_kind" id="size_kind" class="form-control form-control-sm">
														<option value=''> 선택 </option>
														@foreach ($size_kind as $sk)
															<option value='{{ $sk->size_kind_cd }}' @if ($product->size_kind === @$sk->size_kind_cd) selected @endif>{{ $sk->size_kind_cd }} : {{ $sk->size_kind_nm }}</option>
														@endforeach
													</select>
												</div>
											</td>
											<th>원산지</th>
											<td>
												<div class="flax_box">
													<input type='text' class="form-control form-control-sm" name='origin' id="origin" value='{{ $product->origin }}'>
												</div>
											</td>
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
				<a href="#">바코드정보</a>
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
		{field:"prd_cd",	headerName: "바코드",		width:120},
		{field:"goods_no",	headerName: "온라인코드",	width:72},
		{field:"style_no",	headerName: "아이템코드",	width:72},
		{field:"goods_nm",	headerName: "상품명",		width:250},
		{field:"goods_opt",	headerName: "상품옵션",		width:200},
		{field:"prd_cd_p",	headerName: "품번",			width:90},
		{field:"color",		headerName: "컬러",			width:72, cellStyle:{'text-align':'center'}},
		{field:"size",		headerName: "사이즈",		width:72, cellStyle:{'text-align':'center'}},
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
		{field:"del_mapping",       headerName: "맵핑삭제",  		width:60, cellStyle:{'text-align':'center'},
			cellRenderer: function(params) {
				if (params.value !== undefined) {
					if( params.value != ''){
						return '<a href="#" onclick="return delMapping(\'' + params.data.prd_cd + '\');">' + params.value + '</a>';
					}
				}
			}
		},
		{field:"brand",		headerName:"브랜드",		hide:true},
		{field:"year",		headerName:"년도",			hide:true},
		{field:"season",	headerName:"시즌",			hide:true},
		{field:"gender",	headerName:"성별",			hide:true},
		{field:"opt",		headerName:"품목",			hide:true},
		{field:"item",		headerName:"하위품목",		hide:true},
		{field:"seq",		headerName:"순서차수",		hide:true},
		{field:"prd_cd",	headerName:"바코드",		hide:true},
		{field: "", headerName:"", width:"auto"},
	];
</script>
<script type="text/javascript" charset="utf-8">

	const pApp = new App('', {
		gridId: "#div-gd",
	});
	let gx;

	$(document).ready(function() {
		pApp.ResizeGrid(530);
		pApp.BindSearchEnter();
		let gridDiv = document.querySelector(pApp.options.gridId);

		let options = {
            getRowStyle: (params) => {
                if (params.data.prd_cd == $("#prd_cd").val()) return CELL_COLOR.THISPRD;
            }
        }

		gx = new HDGrid(gridDiv, columns, options);
		gx.gridOptions.rowDragManaged = true;
		gx.gridOptions.animateRows = true;
		Search();
	});

	const onlyNum = (obj) => {
		val = obj.value;
		new_val = '';
		for (i=0; i<val.length; i++) {
			char = val.substring(i, i+1);
			if (char < '0' || char > '9') {
				alert('숫자만 입력가능 합니다.');
				obj.value = new_val;
				return;
			} else {
				new_val = new_val + char;
			}
		}
	};
	
	function Search() {
		let data = $('form[name="f1"]').serialize();

		//console.log(data);

		gx.Request('/store/product/prd02/prd-edit-search/', data);
	}

	function save() {
		let size_kind = $('#size_kind').val();
		if (size_kind == '') return alert('사이즈구분을 선택해주세요.');
		if(!window.confirm("품번이 같은 상품의 정상가, 현재가, 사이즈구분, 원산지가 변경됩니다.\n정보를 수정하시겠습니까?")) return;
		

		axios({
			url: '/store/product/prd02/update_product',
			method: 'post',
			data: {
				prd_cd : $('#prd_cd').val(),
				goods_no : $("#goods_no").val(),
				tag_price : $("#tag_price").val(),
				price : $("#price").val(),
				size_kind : $('#size_kind').val(),
				origin : $('#origin').val()
			},
		}).then(function(res) {
			if (res.data.code === 200) {
				alert("수정이 완료되었습니다.");
				opener.Search();
			} else {
				console.log(res.data);
				alert("바코드 매칭 정보 수정 중 오류가 발생했습니다.\n관리자에게 문의해주세요.");
			}
		}).catch(function(err) {
			console.log(err);
		});
	}

	function delPrdCd(prd_cd){
		if(!window.confirm("바코드를 삭제하면 기존 재고 정보도 함께 삭제됩니다.\r\n삭제하시겠습니까?")) return;

		axios({
			url: '/store/product/prd02/del-product-code',
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
			} else if (res.data.code === 201) {
				alert("해당 바코드의 재고가 존재합니다. 삭제할 수 없습니다.");
			} else {
				console.log(res.data);
				alert("바코드 삭제중 오류가 발생했습니다.\n관리자에게 문의해주세요.");
			}
		}).catch(function (err) {
			console.log(err);
		});
	}

	 // 바코드 맵핑 정보 삭제
	 function delMapping(prd_cd) {
		if(!confirm("해당상품의 맵핑정보를 삭제하시겠습니까?")) return ;

		axios({
			url: `/store/product/prd02/del-mapping`,
			method: 'put',
			data: {
				prd_cd : prd_cd,
			},
		}).then(function (res) {
			if(res.data.code === 200) {
				alert(res.data.msg);
				Search();
			} else {
				console.log(res.data);
				alert("바코드 맵핑 정보 삭제중 오류가 발생했습니다.\n관리자에게 문의해주세요.");
			}
		}).catch(function (err) {
			console.log(err);
		});
	}

	function match() {
		prd_cd	= '{{ $product->prd_cd_p }}';
		var url = '/store/product/prd02/create?prd_cd=' + prd_cd;
		var product = window.open(url, "_blank", "toolbar=no,scrollbars=yes,resizable=yes,status=yes,top=100,left=100,width=1100,height=900");
	}
</script>
@stop

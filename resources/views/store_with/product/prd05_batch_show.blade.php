@extends('store_with.layouts.layout-nav')
@section('title','상품가격 변경[일괄]')
@section('content')

	<script src="https://unpkg.com/xlsx-style@0.8.13/dist/xlsx.full.min.js"></script>
	
	<div class="show_layout py-3 px-sm-3">
		<div class="page_tit d-flex justify-content-between">
			<div class="d-flex">
				<h3 class="d-inline-flex">상품가격 변경[일괄]</h3>
				<div class="d-inline-flex location">
					<span class="home"></span>
					<span>/ 상품관리</span>
					<span>/ 상품가격 관리</span>
					<span>/ 상품가격 변경[일괄]</span>
				</div>
			</div>
			<div class="d-flex">
				<a href="javascript:void(0)" onclick="Save();" class="btn btn-primary mr-1"><i class="fas fa-save fa-sm text-white-50 mr-1"></i> 저장</a>
				<a href="javascript:void(0)" onclick="window.close();" class="btn btn-outline-primary"><i class="fas fa-times fa-sm mr-1"></i> 닫기</a>
			</div>
		</div>

		<style>
			.table th {min-width: 130px;}
			.table td {width: 50%;}

			@media (max-width: 740px) {
				.table td {float: unset !important;width: 100% !important;}
			}
		</style>

		<div class="card_wrap aco_card_wrap">
			<div class="card shadow">
				<div class="card-header d-flex justify-content-between align-items-left align-items-sm-center flex-column flex-sm-row mb-0">
					<a href="#">기본정보</a>
				</div>
				<div class="card-body">
					<form name="f1">
						<div class="row">
							<div class="col-12">
								<div class="table-box-ty2 mobile">
									<table class="table incont table-bordered" width="100%" cellspacing="0">
										<tbody>
										<tr>
											<th class="required">상품가격변경 구분</th>
											<td>
												<div class="form-inline form-radio-box">
													<div class="custom-control custom-radio">
														<input type="radio" name="product_price_type" value="reservation" id="reservation" class="custom-control-input" checked>
														<label class="custom-control-label" for="reservation">예약</label>
													</div>
													<div class="custom-control custom-radio">
														<input type="radio" name="product_price_type" value="now" id="now" class="custom-control-input">
														<label class="custom-control-label" for="now">즉시</label>
													</div>
												</div>
											</td>
											<th class="required">변경일자</th>
											<td>
												<div class="form-inline" id="sel_date">
													<div class="docs-datepicker form-inline-inner input_box w-100">
														<div class="input-group">
															<input type="text" class="form-control form-control-sm docs-date" name="change_date_res" id="change_date_res" value="{{$edate}}" autocomplete="off">
															<div class="input-group-append">
																<button type="button" class="btn btn-outline-secondary docs-datepicker-trigger p-0 pl-2 pr-2">
																	<i class="fa fa-calendar" aria-hidden="true"></i>
																</button>
															</div>
														</div>
														<div class="docs-datepicker-container"></div>
													</div>
												</div>
												<div class="form-inline" id="cur_date">
													<div class="docs-datepicker form-inline-inner input_box w-100">
														<div>
															<span id="change_date_now">{{$edate}}</span>
														</div>
														<div class="docs-datepicker-container"></div>
													</div>
												</div>
											</td>
										</tr>
										<tr>
											<th class="required">상품운영 구분</th>
											<td>
												<div class="flax_box">
													<select name='plan_category' id="plan_category" class="form-control form-control-sm">
														<option value=''>00 : 변경없음</option>
														<option value='01'>01 : 정상매장</option>
														<option value='02'>02 : 전매장</option>
														<option value='03'>03 : 이월취급점</option>
														<option value='04'>04 : 아울렛전용</option>
													</select>
												</div>
											</td>
											<th>&nbsp;</th>
											<td>&nbsp;</td>
										</tr>
										<tr>
											<th class="required">파일</th>
											<td>
												<div class="d-flex flex-column">
													<div class="d-flex" style="width:100%;">
														<input id="excelfile" type="file" name="excelfile" class="w-50 mr-2" />
														<button type="button" class="btn btn-outline-primary" onclick="Upload();"><i class="fas fa-sm"></i>자료 불러오기</button>
													</div>
												</div>
											</td>
											<th>샘플파일</th>
											<td><a href="/data/head/sample/상품매칭_샘플.xlsx"> 가격관리일괄.xlsx</a></td>
										</tr>
										</tbody>
									</table>
								</div>
							</div>
						</div>
					</form>
				</div>
			</div>
			<div class="card shadow mt-3">

				<!-- DataTales Example -->
				<div class="card-body pt-2">
					<div class="card-title">
						<div class="filter_wrap">
							<div class="fl_box">
								<h6 class="m-0 font-weight-bold">총 : <span id="gd-total" class="text-primary">0</span>건</h6>
							</div>
						</div>
					</div>
					<div class="table-responsive">
						<div id="div-gd" style="height:calc(100vh - 380px);width:100%;" class="ag-theme-balham"></div>
					</div>
				</div>

			</div>
		</div>
	</div>

	<script language="javascript">
		let columns = [
			{field: "num", headerName: "#", filter:true,width:50,valueGetter: function(params) {return params.node.rowIndex+1;},pinned:'left'},
			{field: "prd_cd", headerName: "바코드", pinned: 'left', width: 120, cellStyle: {"text-align": "center"}},
			{field: "goods_no", headerName: "온라인코드", pinned: 'left', width: 70, cellStyle: {"text-align": "center"}},
			{field: "opt_kind_nm", headerName: "품목", width: 70, cellStyle: {"text-align": "center"}},
			{field: "brand", headerName: "브랜드", width: 70, cellStyle: {"text-align": "center"}},
			{field: "style_no",	headerName: "스타일넘버", width: 70, cellStyle: {"text-align": "center"}},
			{field: "goods_nm",	headerName: "상품명", type: 'HeadGoodsNameType', width: 200},
			{field: "goods_nm_eng",	headerName: "상품명(영문)", width: 200},
			{field: "prd_cd_p", headerName: "품번", width: 90, cellStyle: {"text-align": "center"}},
			{field: "color", headerName: "컬러", width: 55, cellStyle: {"text-align": "center"}},
			{field: "size", headerName: "사이즈", width: 55, cellStyle: {"text-align": "center"}},
			{field: "goods_opt", headerName: "옵션", width: 153},
			{field: "goods_sh", headerName: "정상가", type: "currencyType", width: 65},
			{field: "price", headerName: "현재가", type: "currencyType", width: 65},
			{field: "change_val", headerName: "변경금액(율)", type: "currencyType", width: 80 ,editable:true, cellStyle: {'background' : '#ffff99'}},
			{width : 'auto'}
		];
	</script>

	<script type="text/javascript" charset="utf-8">
		let add_product = [];
		let gx;
		const pApp = new App('', { gridId: "#div-gd" });

		$(document).ready(function() {
			pApp.ResizeGrid(440);
			pApp.BindSearchEnter();
			let gridDiv = document.querySelector(pApp.options.gridId);
			gx = new HDGrid(gridDiv, columns);
			$('#cur_date').hide();
		});

		$("input[name='product_price_type']").change(function(){
			let type = $("input[name='product_price_type']:checked").val();

			if (type == 'reservation') {
				$('#sel_date').show();
				$('#cur_date').hide();

			} else {
				$('#sel_date').hide();
				$('#cur_date').show();
			}
		});
		

	</script>
@stop

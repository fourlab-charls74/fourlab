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
												<div class="form-inline">
													<input type="text" name="store_nm" id="store_nm" value="" class="form-control form-control-sm w-100" />
												</div>
											</td>
											<th class="required">품목</th>
											<td>
												<div class="form-inline">
													<input type="text" name="store_nm_s" id="store_nm_s" value="" class="form-control form-control-sm w-100" />
												</div>
											</td>
										</tr>
										<tr>
											<th class="required">상품번호</th>
											<td colspan="3">
												<div class="form-inline">
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
	</form>

</div>

@extends('head_with.layouts.layout-nav')
@section('title','매장 관리')
@section('content')

<div class="show_layout py-3">
	<div class="card_wrap aco_card_wrap">
		<div class="card shadow">
			<div class="card-header mb-0">
				<a href="#">매장 상세 내역</a>
			</div>
			<div class="card-body mt-1">
				<div class="row_wrap">
					<form name="f1">
						
						<div class="row">
							<div class="col-12" style="padding-top:30px;font-size:18px;font-weight:bold;">+ 기본정보</div>
						</div>
						
						<div class="row">
							<div class="col-12">
								<div class="table-box-ty2 mobile">
									<table class="table incont table-bordered" width="100%" cellspacing="0">
										<colgroup>
											<col width="15%">
											<col width="35%">
											<col width="15%">
											<col width="35%">
										</colgroup>
										<tbody>
											<tr>
												<th>매장코드</th>
												<td colspan="3">
													<div class="flax_box">
														<input type="text" name="com_id" id="com_id" class="form-control form-control-sm" value="{{@$com_id}}" readonly>
													</div>
												</td>
											</tr>
											<tr>
												<th>매장구분</th>
												<td>
													<div class="flax_box">
														<select name='com_type' class="form-control form-control-sm">
															<option value=''>전체</option>
															@foreach ($com_types as $com_type)
																<option value='{{ $com_type->code_id }}' @if(@$data->com_type == $com_type->code_id) selected @endif>{{ $com_type->code_val }}</option>
															@endforeach
														</select>
													</div>
												</td>
												<th>매장명</th>
												<td>
													<div class="flax_box">
														<input type="text" name="com_nm" id="com_nm" class="form-control form-control-sm" value="{{@$data->com_nm}}">
													</div>
												</td>
											</tr>
											<tr>
												<th>매장종류</th>
												<td>
													<div class="flax_box">
														<select name='store_kind' class="form-control form-control-sm">
															<option value=''>전체</option>
															@foreach ($store_kinds as $store_kind)
																<option value='{{ $store_kind->code_id }}' @if(@$data->store_kind == $store_kind->code_id) selected @endif>{{ $store_kind->code_val }}</option>
															@endforeach
														</select>
													</div>
												</td>
												<th>전화</th>
												<td>
													<div class="flax_box">
														<input type="text" name="phone" id="phone" class="form-control form-control-sm" value="{{@$data->phone}}">
													</div>
												</td>
											</tr>
											<tr>
												<th>모바일</th>
												<td>
													<div class="flax_box">
														<input type="text" name="mobile" id="mobile" class="form-control form-control-sm" value="{{@$data->mobile}}">
													</div>
												</td>
												<th>FAX</th>
												<td>
													<div class="flax_box">
														<input type="text" name="fax" id="fax" class="form-control form-control-sm" value="{{@$data->fax}}">
													</div>
												</td>
											</tr>
											<tr>
												<th>우편번호</th>
												<td>
													<div class="flax_box">
														<input type="text" name="zipcode" id="zipcode" class="form-control form-control-sm" value="{{@$data->zipcode}}">
													</div>
												</td>
												<th>주소</th>
												<td>
													<div class="flax_box">
														<input type="text" name="addr" id="addr" class="form-control form-control-sm" value="{{@$data->addr}}">
													</div>
												</td>
											</tr>
											<tr>
												<th>개장일</th>
												<td>
													<div class="flax_box">
														<input type="text" name="sdate" id="sdate" class="form-control form-control-sm" value="{{@$data->sdate}}">
													</div>
												</td>
												<th>페점일</th>
												<td>
													<div class="flax_box">
														<input type="text" name="edate" id="edate" class="form-control form-control-sm" value="{{@$data->edate}}">
													</div>
												</td>
											</tr>
											<tr>
												<th>매니저명</th>
												<td>
													<div class="flax_box">
														<input type="text" name="manager_nm" id="manager_nm" class="form-control form-control-sm" value="{{@$data->manager_nm}}">
													</div>
												</td>
												<th>매니저시작일</th>
												<td>
													<div class="flax_box">
														<input type="text" name="manager_sdate" id="manager_sdate" class="form-control form-control-sm" value="{{@$data->manager_sdate}}">
													</div>
												</td>
											</tr>
											<tr>
												<th>매니저종료일</th>
												<td>
													<div class="flax_box">
														<input type="text" name="manager_edate" id="manager_edate" class="form-control form-control-sm" value="{{@$data->manager_edate}}">
													</div>
												</td>
												<th>매니저보증금</th>
												<td>
													<div class="flax_box">
														<input type="text" name="manager_deposit" id="manager_deposit" class="form-control form-control-sm" value="{{@$data->manager_deposit}}">
													</div>
												</td>
											</tr>
											<tr>
												<th>매니저수수료-정상</th>
												<td>
													<div class="flax_box">
														<input type="text" name="manager_fee" id="manager_fee" class="form-control form-control-sm" value="{{@$data->manager_fee}}">
													</div>
												</td>
												<th>매니저수수료-행사</th>
												<td>
													<div class="flax_box">
														<input type="text" name="manager_sfee" id="manager_sfee" class="form-control form-control-sm" value="{{@$data->manager_sfee}}">
													</div>
												</td>
											</tr>
											<tr>
												<th>보증금-현금</th>
												<td>
													<div class="flax_box">
														<input type="text" name="deposit_cash" id="deposit_cash" class="form-control form-control-sm" value="{{@$data->deposit_cash}}">
													</div>
												</td>
												<th>보증금-담보</th>
												<td>
													<div class="flax_box">
														<input type="text" name="deposit_coll" id="deposit_coll" class="form-control form-control-sm" value="{{@$data->deposit_coll}}">
													</div>
												</td>
											</tr>
											<tr>
												<th>인테리어-비용</th>
												<td>
													<div class="flax_box">
														<input type="text" name="interior_cost" id="interior_cost" class="form-control form-control-sm" value="{{@$data->interior_cost}}">
													</div>
												</td>
												<th>인테리어-부담</th>
												<td>
													<div class="flax_box">
														<input type="text" name="interior_burden" id="interior_burden" class="form-control form-control-sm" value="{{@$data->interior_burden}}">
													</div>
												</td>
											</tr>
											<tr>
												<th>기본수수료</th>
												<td>
													<div class="flax_box">
														<input type="text" name="fee" id="fee" class="form-control form-control-sm" value="{{@$data->fee}}">
													</div>
												</td>
												<th>판매수수료율</th>
												<td>
													<div class="flax_box">
														<input type="text" name="sale_fee" id="sale_fee" class="form-control form-control-sm" value="{{@$data->sale_fee}}">
													</div>
												</td>
											</tr>
										</tbody>
									</table>
								</div>
							</div>
						</div>

						<div class="row">
							<div class="col-12" style="padding-top:30px;font-size:18px;font-weight:bold;">+ 사업자 정보</div>
						</div>
						
						<div class="row">
							<div class="col-12">
								<div class="table-box-ty2 mobile">
									<table class="table incont table-bordered" width="100%" cellspacing="0">
										<colgroup>
											<col width="15%">
											<col width="35%">
											<col width="15%">
											<col width="35%">
										</colgroup>
										<tbody>
											<tr>
												<th>등록번호</th>
												<td>
													<div class="flax_box">
														<input type="text" name="biz_num" id="biz_num" class="form-control form-control-sm" value="{{@$data->biz_num}}">
													</div>
												</td>
												<th>상호</th>
												<td>
													<div class="flax_box">
														<input type="text" name="biz_name" id="biz_name" class="form-control form-control-sm" value="{{@$data->biz_name}}">
													</div>
												</td>
											</tr>
											<tr>
												<th>대표자명</th>
												<td>
													<div class="flax_box">
														<input type="text" name="biz_ceo" id="biz_ceo" class="form-control form-control-sm" value="{{@$data->biz_ceo}}">
													</div>
												</td>
												<th>우편번호</th>
												<td>
													<div class="flax_box">
														<input type="text" name="biz_zipcode" id="biz_zipcode" class="form-control form-control-sm" value="{{@$data->biz_zipcode}}">
													</div>
												</td>
											</tr>
											<tr>
												<th>주소1</th>
												<td>
													<div class="flax_box">
														<input type="text" name="biz_addr1" id="biz_addr1" class="form-control form-control-sm" value="{{@$data->biz_addr1}}">
													</div>
												</td>
												<th>주소2</th>
												<td>
													<div class="flax_box">
														<input type="text" name="biz_addr2" id="biz_addr2" class="form-control form-control-sm" value="{{@$data->biz_addr2}}">
													</div>
												</td>
											</tr>
											<tr>
												<th>업태</th>
												<td>
													<div class="flax_box">
														<input type="text" name="biz_uptae" id="biz_uptae" class="form-control form-control-sm" value="{{@$data->biz_uptae}}">
													</div>
												</td>
												<th>업종</th>
												<td>
													<div class="flax_box">
														<input type="text" name="biz_upjong" id="biz_upjong" class="form-control form-control-sm" value="{{@$data->biz_upjong}}">
													</div>
												</td>
											</tr>
										</tbody>
									</table>
								</div>
							</div>
						</div>

						<div class="row">
							<div class="col-12" style="padding-top:30px;font-size:18px;font-weight:bold;">+ 환경 정보</div>
						</div>
						
						<div class="row">
							<div class="col-12">
								<div class="table-box-ty2 mobile">
									<table class="table incont table-bordered" width="100%" cellspacing="0">
										<colgroup>
											<col width="15%">
											<col width="35%">
											<col width="15%">
											<col width="35%">
										</colgroup>
										<tbody>
											<tr>
												<th>사용유무</th>
												<td>
													<div class="form-inline form-radio-box">
														<div class="custom-control custom-radio">
															<input type="radio" name="use_yn" id="use_yn_y" class="custom-control-input" value="Y" @if(@$data->use_yn == 'Y') checked @endif>
															<label class="custom-control-label" for="use_yn_y">Y</label>
														</div>
														<div class="custom-control custom-radio">
															<input type="radio" name="use_yn" id="use_yn_n" class="custom-control-input" value="N" @if(@$data->use_yn == 'N') checked @endif>
															<label class="custom-control-label" for="use_yn_n">N</label>
														</div>
													</div>
												</td>
												<th>관리기준</th>
												<td>
													<div class="form-inline form-radio-box">
														<div class="custom-control custom-radio">
															<input type="radio" name="manage_type" id="manage_type1" class="custom-control-input" value="중간관리식" @if(@$data->manage_type == '중간관리식') checked @endif>
															<label class="custom-control-label" for="manage_type1">중간관리식</label>
														</div>
														<div class="custom-control custom-radio">
															<input type="radio" name="manage_type" id="manage_type2" class="custom-control-input" value="사입식" @if(@$data->manage_type == '사입식') checked @endif>
															<label class="custom-control-label" for="manage_type2">사입식</label>
														</div>
													</div>
												</td>
											</tr>
											<tr>
												<th>경비관리유무</th>
												<td>
													<div class="form-inline form-radio-box">
														<div class="custom-control custom-radio">
															<input type="radio" name="exp_manage_yn" id="exp_manage_y" class="custom-control-input" value="Y" @if(@$data->exp_manage_yn == 'Y') checked @endif>
															<label class="custom-control-label" for="exp_manage_y">Y</label>
														</div>
														<div class="custom-control custom-radio">
															<input type="radio" name="exp_manage_yn" id="exp_manage_n" class="custom-control-input" value="N" @if(@$data->exp_manage_yn == 'N') checked @endif>
															<label class="custom-control-label" for="exp_manage_n">N</label>
														</div>
													</div>
												</td>
												<th>출고우선순위</th>
												<td>
													<div class="flax_box">
														<select name='priority' class="form-control form-control-sm">
															<option value=''>전체</option>
															@foreach ($prioritys as $priority)
																<option value='{{ $priority->code_id }}' @if(@$data->priority == $priority->code_id) selected @endif>{{ $priority->code_val }}</option>
															@endforeach
														</select>
													</div>
												</td>
											</tr>
											<tr>
												<th>동업계정보입력</th>
												<td>
													<div class="form-inline form-radio-box">
														<div class="custom-control custom-radio">
															<input type="radio" name="ocompany_info_yn" id="ocompany_info_yn_y" class="custom-control-input" value="Y" @if(@$data->ocompany_info_yn == 'Y') checked @endif>
															<label class="custom-control-label" for="ocompany_info_yn_y">Y</label>
														</div>
														<div class="custom-control custom-radio">
															<input type="radio" name="ocompany_info_yn" id="ocompany_info_yn_n" class="custom-control-input" value="N" @if(@$data->ocompany_info_yn == 'N') checked @endif>
															<label class="custom-control-label" for="ocompany_info_yn_n">N</label>
														</div>
													</div>
												</td>
												<th>POS사용여부</th>
												<td>
													<div class="form-inline form-radio-box">
														<div class="custom-control custom-radio">
															<input type="radio" name="pos_yn" id="pos_yn_y" class="custom-control-input" value="Y" @if(@$data->pos_yn == 'Y') checked @endif>
															<label class="custom-control-label" for="pos_yn_y">Y</label>
														</div>
														<div class="custom-control custom-radio">
															<input type="radio" name="pos_yn" id="pos_yn_n" class="custom-control-input" value="N" @if(@$data->pos_yn == 'N') checked @endif>
															<label class="custom-control-label" for="pos_yn_n">N</label>
														</div>
													</div>
												</td>
											</tr>
											<tr>
												<th>타매장재고조회</th>
												<td>
													<div class="form-inline form-radio-box">
														<div class="custom-control custom-radio">
															<input type="radio" name="ostore_stock_yn" id="ostore_stock_yn_y" class="custom-control-input" value="Y" @if(@$data->ostore_stock_yn == 'Y') checked @endif>
															<label class="custom-control-label" for="ostore_stock_yn_y">Y</label>
														</div>
														<div class="custom-control custom-radio">
															<input type="radio" name="ostore_stock_yn" id="ostore_stock_yn_n" class="custom-control-input" value="N" @if(@$data->ostore_stock_yn == 'N') checked @endif>
															<label class="custom-control-label" for="ostore_stock_yn_n">N</label>
														</div>
													</div>
												</td>
												<th>판매분배분여부</th>
												<td>
													<div class="form-inline form-radio-box">
														<div class="custom-control custom-radio">
															<input type="radio" name="sale_dist_yn" id="sale_dist_yn_y" class="custom-control-input" value="Y" @if(@$data->sale_dist_yn == 'Y') checked @endif>
															<label class="custom-control-label" for="sale_dist_yn_y">Y</label>
														</div>
														<div class="custom-control custom-radio">
															<input type="radio" name="sale_dist_yn" id="sale_dist_yn_n" class="custom-control-input" value="N" @if(@$data->sale_dist_yn == 'N') checked @endif>
															<label class="custom-control-label" for="sale_dist_yn_n">N</label>
														</div>
													</div>
												</td>
											</tr>
											<tr>
												<th>매장RT여부</th>
												<td>
													<div class="form-inline form-radio-box">
														<div class="custom-control custom-radio">
															<input type="radio" name="rt_yn" id="rt_yn_y" class="custom-control-input" value="Y" @if(@$data->rt_yn == 'Y') checked @endif>
															<label class="custom-control-label" for="rt_yn_y">Y</label>
														</div>
														<div class="custom-control custom-radio">
															<input type="radio" name="rt_yn" id="rt_yn_n" class="custom-control-input" value="N" @if(@$data->rt_yn == 'N') checked @endif>
															<label class="custom-control-label" for="rt_yn_n">N</label>
														</div>
													</div>
												</td>
												<th>매장RT시작일</th>
												<td>
													<div class="flax_box">
														<input type="text" name="rt_sdate" id="rt_sdate" class="form-control form-control-sm" value="{{@$data->rt_sdate}}">
													</div>
												</td>
											</tr>
											<tr>
												<th>마일리지적립</th>
												<td>
													<div class="form-inline form-radio-box">
														<div class="custom-control custom-radio">
															<input type="radio" name="point_in_yn" id="point_in_yn_y" class="custom-control-input" value="Y" @if(@$data->point_in_yn == 'Y') checked @endif>
															<label class="custom-control-label" for="point_in_yn_y">Y</label>
														</div>
														<div class="custom-control custom-radio">
															<input type="radio" name="point_in_yn" id="point_in_yn_n" class="custom-control-input" value="N" @if(@$data->point_in_yn == 'N') checked @endif>
															<label class="custom-control-label" for="point_in_yn_n">N</label>
														</div>
													</div>
												</td>
												<th>마일리지사용</th>
												<td>
													<div class="form-inline form-radio-box">
														<div class="custom-control custom-radio">
															<input type="radio" name="point_out_yn" id="point_out_yn_y" class="custom-control-input" value="Y" @if(@$data->point_out_yn == 'Y') checked @endif>
															<label class="custom-control-label" for="point_out_yn_y">Y</label>
														</div>
														<div class="custom-control custom-radio">
															<input type="radio" name="point_out_yn" id="point_out_yn_n" class="custom-control-input" value="N" @if(@$data->point_out_yn == 'N') checked @endif>
															<label class="custom-control-label" for="point_out_yn_n">N</label>
														</div>
													</div>
												</td>
											</tr>
											<tr>
												<th>미수금처리구분</th>
												<td>
													<div class="form-inline form-radio-box">
														<div class="custom-control custom-radio">
															<input type="radio" name="unpaid_proc_type" id="unpaid_proc_type1" class="custom-control-input" value="판매미수" @if(@$data->unpaid_proc_type == '판매미수') checked @endif>
															<label class="custom-control-label" for="unpaid_proc_type1">판매미수</label>
														</div>
														<div class="custom-control custom-radio">
															<input type="radio" name="unpaid_proc_type" id="unpaid_proc_type2" class="custom-control-input" value="출고미수" @if(@$data->unpaid_proc_type == '출고미수') checked @endif>
															<label class="custom-control-label" for="unpaid_proc_type2">출고미수</label>
														</div>
													</div>
												</td>
												<th>&nbsp;</th>
												<td>&nbsp;</td>
											</tr>

										</tbody>
									</table>
								</div>
							</div>
						</div>

					</form>
				</div>
			</div>
		</div>
	</div>

    <div class="resul_btn_wrap mt-3 d-block">
		<a href="javascript:;" class="btn btn-sm btn-primary btn-update">수정</a>
		<a href="javascript:;" class="btn btn-sm btn-primary btn-delete">삭제</a>
		<a href="javascript:;" class="btn btn-sm btn-primary" onclick="window.close()">닫기</a>
    </div>

</div>

<script>
$('.btn-update').click(function(e){
	if($('#com_id').val() != ""){
		if (confirm('매장 정보를 수정하시겠습니까?') === false) return;

		var frm	= $('form[name="f1"]');
		com_id	= $('#com_id').val();

		$.ajax({
			async: true,
			method: 'post',
			url: '/head/xmd/code/code02/view/' + com_id,
			data: frm.serialize(),
			dataType: 'json',
			success: function(res) {
				if (res.code == '200') {
					alert("수정 되었습니다.");
					//self.close(); 
					opener.Search();
				} else {
					alert('처리 중 문제가 발생하였습니다. 다시 시도하여 주십시오.');
					console.log(res.msg);
				}
			},
			error: function(e) {
				alert("시스템 에러입니다 관리자에게 문의하십시요.");
				console.log(e.responseText)
			}
		});

	}
});

$('.btn-delete').click(function(e){
	if($('#com_id').val() != ""){
		if (confirm('매장 정보를 삭제하시겠습니까?') === false) return;

		com_id	= $('#com_id').val();

		$.ajax({
			async: true,
			type: 'delete',
			url: '/head/xmd/code/code02/view/' + com_id,
			success: function (res) {
				if (res.code == '200') {
					alert("삭제되었습니다.");
					self.close();
					opener.Search();
				}else{
					alert('처리 중 문제가 발생하였습니다. 다시 시도하여 주십시오.');
				}
			},
			error: function(request, status, error) {
				alert("시스템 에러입니다 관리자에게 문의하십시요.");
				console.log("error")
			}
		});
	}
});
</script>
@stop

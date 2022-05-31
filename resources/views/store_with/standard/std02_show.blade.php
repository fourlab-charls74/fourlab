@extends('head_with.layouts.layout-nav')
@section('title','업체')
@section('content')

<script src="https://t1.daumcdn.net/mapjsapi/bundle/postcode/prod/postcode.v2.js"></script>

<div class="show_layout py-3 px-sm-3">
	<div class="page_tit mb-3 d-flex align-items-center justify-content-between">
		<div>
			<h3 class="d-inline-flex">업체</h3>
			<div class="d-inline-flex location">
				<span class="home"></span>
				<span>/ 업체 - {{ $company['com_id'] }}</span>
			</div>
		</div>
		<div>
			<a href="#" class="btn btn-sm btn-primary shadow-sm save-btn" onclick="Cmder('{{ $cmd }}');"><i class="bx bx-save mr-1"></i>저장</a>
		</div>
	</div>

	<style> .required:after {content:" *"; color: red;}</style>

	<form name="f1" id="f1">
		<input type="hidden" name="cmd" id="cmd" value="{{ $cmd }}">
		<div class="card_wrap aco_card_wrap">
			<!-- 업체 기본 정보 -->
			<div class="card shadow">
				<div class="card-header mb-0">
					<a href="#">업체 기본 정보</a>
				</div>
				<div class="card-body">
					<div class="row_wrap">
						<!-- 업체아이디/비밀번호/업체 -->
						<div class="row">
							<div class="col-12">
								<div class="table-box-ty2 mobile">
									<table class="table incont table-bordered" width="100%" cellspacing="0">
										<colgroup>
											<col width="100px">
											<col width="23%">
											<col width="100px">
											<col width="23%">
											<col width="100px">
											<col width="23%">
										</colgroup>
										<tbody>
											<tr>
												<th class="required">업체아이디</th>
												<td>
													<div class="input_box" style="display:flex;">
														<input type="text" name="com_id" id="com_id" class="form-control form-control-sm search-all" value="{{ $company['com_id'] }}" @if($cmd=='editcmd' ) readonly="readonly" @endif />
														<input type="hidden" name="com_id_chk">
														@if ($cmd == 'addcmd')
														<button name="com_id_check" class="btn btn-sm btn-primary fs-12 px-1 ml-1" style="width:100px;" onclick="checkdup();return false;">중복확인</button>
														@endif
														<div><span id="checkdupmessage"></span></div>
													</div>
												</td>
												<th class="required">비밀번호</th>
												<td colspan="3">
													<div class="txt_box flax_box">
														<input type="password" id="pwd" class="mwidth form-control form-control-sm" style="width:29%; display:inline" value="{{ $company['pwd'] }}">
														<div class="custom-control custom-checkbox form-check-box ml-1">
															<input type="checkbox" id="change_pwd" class="custom-control-input" value="y">
															<label class="custom-control-label" for="change_pwd">비밀번호 변경</label>
														</div>
													</div>
												</td>
											</tr>
											<tr>
												<th class="required">업체</th>
												<td>
													<div class="input_box flax_box">
														<input type="text" name="com_nm" class="form-control form-control-sm search-all" value="{{ $company['com_nm'] }}" />
													</div>
												</td>
												<th class="required">업체구분</th>
												<td>
													<div class="flax_box">
														<div style="min-width:100px;">
															<select name="com_type" class="form-control form-control-sm">
																<option value="">선택하세요.</option>
																@foreach ($com_types as $com_type)
																<option value="{{ $com_type->code_id }}" @if($company['com_type']==$com_type->code_id) selected @endif >
																	{{ $com_type->code_val }}
																</option>
																@endforeach
															</select>
														</div>
														<div class="custom-control custom-checkbox form-check-box ml-1">
															<input type="checkbox" id="site_yn" class="custom-control-input" value="y" @if($company['site_yn']=='y' ) checked="checked" @endif>
															<label class="custom-control-label" for="site_yn">본사판매처</label>
														</div>
													</div>
												</td>
												<th>판매구분</th>
												<td>
													<div class="form-inline form-radio-box">
														<div class="custom-control custom-radio">
															<input type="radio" name="sale_type" id="sale_type_D" class="custom-control-input" @if($company['sale_type']=='D' ) checked="checked" @endif value="D">
															<label class="custom-control-label" for="sale_type_D">직접</label>
														</div>
														<div class="custom-control custom-radio">
															<input type="radio" name="sale_type" id="sale_type_E" class="custom-control-input" @if($company['sale_type']=='E' ) checked="checked" @endif value="E">
															<label class="custom-control-label" for="sale_type_E">입점</label>
														</div>
														<div class="custom-control custom-radio">
															<input type="radio" name="sale_type" id="sale_type_C" class="custom-control-input" @if($company['sale_type']=='C' ) checked="checked" @endif value="C">
															<label class="custom-control-label" for="sale_type_C">위탁</label>
														</div>
													</div>
												</td>
											</tr>
											<tr>
												<th>홈페이지</th>
												<td colspan="6">
													<div class="flax_box txt_box">
														<input type="text" name="homepage" value="{{ $company['homepage'] }}" class="form-control form-control-sm search-all" value="" />
													</div>
												</td>
											</tr>
											<tr>
												<th>매장정보</th>
												<td colspan="5">
													<div class="row">
														<div class="col-lg-2">
															<div class="inner_flax_box">
																<label for="">구분 :</label>
																<input type="text" name="store_type" value="{{ $company['store_type'] }}" class="form-control form-control-sm">
															</div>
														</div>
														<div class="col-lg-3 mt-1 mt-lg-0">
															<div class="inner_flax_box">
																<label for="">매장명 :</label>
																<input type="text" name="store_nm" value="{{ $company['store_nm'] }}" class="form-control form-control-sm">
															</div>
														</div>
														<div class="col-lg-3 mt-1 mt-lg-0">
															<div class="inner_flax_box">
																<label for="">지점명 :</label>
																<input type="text" class="form-control form-control-sm" name="store_branch" value="{{ $company['store_branch'] }}">
															</div>
														</div>
														<div class="col-lg-2 mt-1 mt-lg-0">
															<div class="inner_flax_box">
																<label for="">지역 :</label>
																<input type="text" class="form-control form-control-sm" name="store_area" value="{{ $company['store_area'] }}">
															</div>
														</div>
														<div class="col-lg-2 mt-1 mt-lg-0">
															<div class="inner_flax_box">
																<label for="">종류 :</label>
																<input type="text" class="form-control form-control-sm" name="store_kind" value="{{ $company['store_kind'] }}">
															</div>
														</div>
													</div>
												</td>
											</tr>
											<tr>
												<th>판매구분</th>
												<td>
													<div class="form-inline form-radio-box">
														<div class="custom-control custom-radio">
															<input type="radio" name="sell_type" id="sell_type_2" class="custom-control-input" @if($company['sell_type']=='2' ) checked="checked" @endif value="2">
															<label class="custom-control-label" for="sell_type_2">온라인</label>
														</div>
														<div class="custom-control custom-radio">
															<input type="radio" name="sell_type" id="sell_type_1" class="custom-control-input" @if($company['sell_type']=='1' ) checked="checked" @endif value="1">
															<label class="custom-control-label" for="sell_type_1">오프라인</label>
														</div>
													</div>
												</td>
												<th>출력여부</th>
												<td>
													<div class="form-inline form-radio-box">
														<div class="custom-control custom-radio">
															<input type="radio" name="dp_yn" id="dp_yn1" class="custom-control-input" value="Y" @if($company['dp_yn']=='Y' || $company['dp_yn']=='' )checked="checked" @endif />
															<label class="custom-control-label" for="dp_yn1">예</label>
														</div>
														<div class="custom-control custom-radio">
															<input type="radio" name="dp_yn" id="dp_yn2" class="custom-control-input" value="N" @if($company['dp_yn']=='N' )checked="checked" @endif />
															<label class="custom-control-label" for="dp_yn2">아니오</label>
														</div>
													</div>
												</td>
												<th class="required">사용여부</th>
												<td>
													<div class="form-inline form-radio-box">
														<div class="custom-control custom-radio">
															<input type="radio" name="use_yn" id="use_yn1" class="custom-control-input" value="Y" @if($company['use_yn']=='Y' )checked="checked" @endif />
															<label class="custom-control-label" for="use_yn1">사용</label>
														</div>
														<div class="custom-control custom-radio">
															<input type="radio" name="use_yn" id="use_yn2" class="custom-control-input" value="N" @if($company['use_yn']=='N' )checked="checked" @endif />
															<label class="custom-control-label" for="use_yn2">미사용</label>
														</div>
													</div>
												</td>
											</tr>
											<tr>
												<th>cs 진행</th>
												<td>
													<div class="form-inline form-radio-box">
														<div class="custom-control custom-radio">
															<input type="radio" name="cs_yn" id="cs_yn1" class="custom-control-input" value="Y" @if($company['cs_yn']=='Y' )checked="checked" @endif />
															<label class="custom-control-label" for="cs_yn1">예</label>
														</div>
														<div class="custom-control custom-radio">
															<input type="radio" name="cs_yn" id="cs_yn2" class="custom-control-input" value="N" @if($company['cs_yn']=='N' )checked="checked" @endif />
															<label class="custom-control-label" for="cs_yn2">아니오</label>
														</div>
													</div>
												</td>
												<th>판매가격 변경</th>
												<td>
													<div class="form-inline form-radio-box">
														<div class="custom-control custom-radio">
															<input type="radio" name="price_yn" id="price_yn1" class="custom-control-input" value="Y" @if($company['price_yn']=='Y' )checked="checked" @endif />
															<label class="custom-control-label" for="price_yn1">예</label>
														</div>
														<div class="custom-control custom-radio">
															<input type="radio" name="price_yn" id="price_yn2" class="custom-control-input" value="N" @if($company['price_yn']=='N' )checked="checked" @endif />
															<label class="custom-control-label" for="price_yn2">아니오</label>
														</div>
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
			<!-- 업체 배송 정보 -->
			<div class="card shadow">
				<div class="card-header mb-0">
					<a href="#">업체 배송 정보</a>
				</div>
				<div class="card-body">
					<div class="row_wrap">
						<div class="row">
							<div class="col-12">
								<div class="table-box-ty2 mobile">
									<table class="table incont table-bordered" width="100%" cellspacing="0">
										<colgroup>
											<col width="94px">
											<col width="23%">
											<col width="94px">
											<col width="23%">
											<col width="94px">
											<col width="23%">
										</colgroup>
										<tbody>
											<tr>
												<th class="required">배송방식</th>
												<td colspan="5">
													<div class="form-inline form-radio-box flax_box txt_box">
														<select name="baesong_kind" id="baesong_kind" class="form-control form-control-sm mr-0 mr-sm-1">
															<option value="">선택하세요.</option>
															@foreach($baesong_kinds as $baesong_kind)
															<option value="{{ $baesong_kind->code_id}}" @if($baesong_kind->code_id == $company['baesong_kind']) selected @endif>
																{{ $baesong_kind->code_val }}
															</option>
															@endforeach
														</select>
														<select name="baesong_info" id="baesong_info" class="form-control form-control-sm mt-1 mt-sm-0">
															<option value="">선택하세요.</option>
															@foreach($baesong_infos as $baesong_info)
															<option value="{{ $baesong_info->code_id }}" @if( $baesong_info->code_id == $company['baesong_info']) selected @endif>
																{{ $baesong_info->code_val }}
															</option>
															@endforeach
														</select>
													</div>
												</td>
											</tr>
											<tr>
												<th class="required">배송비 정책</th>
												<td>
													<div class="form-inline form-radio-box">
														<div class="custom-control custom-radio">
															<input type="radio" name="dlv_policy" id="dlv_policy1" class="custom-control-input" value="s" @if($company['dlv_policy'] !="c" )checked="checked" @endif onclick="ShowPolicyLayer('s');">
															<label class="custom-control-label" for="dlv_policy1">쇼핑몰 정책</label>
														</div>
														<div class="custom-control custom-radio">
															<input type="radio" name="dlv_policy" id="dlv_policy2" class="custom-control-input" value="c" @if($company['dlv_policy']=="c" )checked="checked" @endif onclick="ShowPolicyLayer('c');">
															<label class="custom-control-label" for="dlv_policy2">업체 정책</label>
														</div>
													</div>
												</td>
												<th class="required">배송비</th>
												<td>
													<div id="dlv_policy_shop" style="display:@if($company['dlv_policy'] == 'c') none @else inline @endif;">
														<div class="txt_box">
															{{ number_format($shop_dlv_fee) }}원
															[ {{ number_format($shop_free_dlv_fee_limit) }}원 이상 무료배송 ]
														</div>
													</div>
													<div id="dlv_policy_company" style="display:@if($company['dlv_policy'] == 'c') inline @else none @endif;">
														<div class="flax_box">
															<div>
																<input type="text" class="form-control form-control-sm" name="dlv_amt" value="{{ $company['dlv_amt'] }}" style="display:inline;width:50px;text-align:right" onkeyup="com(this);">원
															</div>
															<div>
																[ <input type="text" class="form-control form-control-sm" name="free_dlv_amt_limit" value="{{ $company['free_dlv_amt_limit'] }}" style="display:inline;width:50px;text-align:right" onkeyup="com(this);">원 이상 무료배송 ]
															</div>
														</div>
													</div>
												</td>
												<th class="required">배송기간</th>
												<td>
													<div class="input_box flax_box">
														<input type="text" class="form-control form-control-sm" name="dlv_day" value="{{ $company['dlv_day'] }}" maxlength="2" style="width:80px;text-aling:right;ime-mode:disabled;" onkeydown="onlynum(this);" onfocus="this.select()" />일
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

			<!-- api 연동 정보 -->
			<div class="card shadow">
				<div class="card-header mb-0">
					<a href="#">API 연동 정보</a>
				</div>
				<div class="card-body">
					<div class="row_wrap">
						<div class="row">
							<div class="col-12">
								<div class="table-box-ty2 mobile">
									<table class="table incont table-bordered" width="100%" cellspacing="0">
										<colgroup>
											<col width="94px">
											<col width="23%">
											<col width="94px">
											<col width="23%">
											<col width="94px">
											<col width="23%">
										</colgroup>
										<tbody>
											<tr>
												<th>API 연동</th>
												<td>
													<div class="form-inline form-radio-box">
														<div class="custom-control custom-radio">
															<input type="radio" name="api_yn" id="api_yn1" class="custom-control-input" value="y" @if($company['api_yn']=='y' )checked="checked" @endif>
															<label class="custom-control-label" for="api_yn1">사용</label>
														</div>
														<div class="custom-control custom-radio">
															<input type="radio" name="api_yn" id="api_yn2" class="custom-control-input" value="n" @if($company['api_yn']=='n' )checked="checked" @endif>
															<label class="custom-control-label" for="api_yn2">미사용</label>
														</div>
													</div>
												</td>
												<th>API 인증키</th>
												<td>
													<div class="txt_box">
														{{ $company['api_key'] }}&nbsp;
													</div>
												</td>
												<th>인증키 변경</th>
												<td>
													<div class="input_box flax_box">
														@if($company['api_key'] == '' && $cmd == 'editcmd')
														<div id="form_api_key_add" style="float:left;">
															<button class="btn btn-sm btn-primary fs-12 px-1 ml-1" style="width:60px;" onclick="ChangeKey('add');">등록</button>
														</div>
														@elseif ($company['api_key'] != '' && $cmd == 'editcmd')
														<div id="form_api_key_edit" style="float:left;">
															<button class="btn btn-sm btn-primary fs-12 px-1 ml-1" style="width:60px;" onclick="ChangeKey('edit');">변경</button>
														</div>
														@else
														<div id="form_api_key_msg">&nbsp;</div>
														@endif
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

			<!-- 본사 담당자 및 거래 정보 -->
			<div class="card shadow">
				<div class="card-header mb-0">
					<a href="#">본사 담당자 및 거래 정보</a>
				</div>
				<div class="card-body">
					<div class="row_wrap">
						<div class="row">
							<div class="col-12">
								<div class="table-box-ty2 mobile">
									<table class="table incont table-bordered" width="100%" cellspacing="0">
										<colgroup>
											<col width="100px">
											<col width="20%">
											<col width="100px">
											<col width="20%">
											<col width="100px">
											<col width="20%">
										</colgroup>
										<tbody>
											<tr>
												<th class="required">MD명/정산담당자</th>
												<td class="ty2">
													<div class="form-inline">
														<div class="form-inline-inner input_box">
															<input type="text" class="form-control form-control-sm" name="md_nm" value="{{ $company['md_nm'] }}" maxlength="10">
														</div>
														<span class="text_line">/</span>
														<div class="form-inline-inner input_box">
															<input type="text" class="form-control form-control-sm" name="settle_nm" value="{{ $company['settle_nm'] }}" maxlength="10">
														</div>
													</div>
												</td>
												<th class="required">판매수수료율</th>
												<td>
													<div class="input_box flax_box">
														<input type="text" class="form-control form-control-sm" name="pay_fee" value="{{ $company['pay_fee'] }}" maxlength="5" style="width:100px;text-align:right"> % (공동구매)
													</div>
												</td>
												<th class="required">수수료적용방식</th>
												<td>
													<div class="input_box flax_box">
														<select name="margin_type" class="form-control form-control-sm">
															<option value=''>선택해 주십시오.</option>
															@foreach($margin_types as $margin_type_item)
															<option value="{{ $margin_type_item->code_id }}" @if($margin_type_item->code_id == $margin_type) selected @endif>
																{{ $margin_type_item->code_val }}
															</option>
															@endforeach
														</select>
													</div>
												</td>
											</tr>
											<tr>
												<th>메모</th>
												<td colspan="5">
													<div class="area_box">
														<textarea class="form-control form-control-sm" name="memo" id="" cols="30" rows="10">{{ $company['memo'] }}</textarea>
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

			<!-- 업체 담당자 -->
			<div class="card shadow">
				<div class="card-header mb-0">
					<a href="#">업체 담당자</a>
				</div>
				<div class="card-body">
					<div class="row_wrap">
						<div class="row">
							<div class="col-12">
								<div class="table-box-ty2 mobile">
									<table class="table incont table-bordered" width="100%" cellspacing="0">
										<colgroup>
											<col width="100px">
											<col width="20%">
											<col width="100px">
											<col width="20%">
											<col width="100px">
											<col width="20%">
										</colgroup>
										<tbody>
											<tr>
												<th>업체 담당자</th>
												<td>
													<div class="input_box flax_box">
														<input type="text" class="form-control form-control-sm" name="staff_nm1" value="{{ $company['staff_nm1'] }}" maxlength="10" />
													</div>
												</td>
												<th>이메일</th>
												<td>
													<div class="input_box flax_box">
														<input type="text" class="form-control form-control-sm" name="staff_email1" value="{{ $company['staff_email1'] }}" maxlength="50" />
													</div>
												</td>
												<th>연락처/휴대전화</th>
												<td>
													<div class="form-inline">
														<div class="form-inline-inner input_box">
															<input type="text" class="form-control form-control-sm" name="staff_phone1" value="{{ $company['staff_phone1'] }}" maxlength="15" />
														</div>
														<span class="text_line">/</span>
														<div class="form-inline-inner input_box">
															<input type="text" class="form-control form-control-sm" name="staff_hp1" value="{{ $company['staff_hp1'] }}" maxlength="15" />
														</div>
													</div>
												</td>
											</tr>
											<tr>
												<th>정산 담당자</th>
												<td>
													<div class="input_box flax_box">
														<input type="text" class="form-control form-control-sm" name="staff_nm2" value="{{ $company['staff_nm2'] }}" maxlength="10" />
													</div>
												</td>
												<th>이메일</th>
												<td>
													<div class="input_box flax_box">
														<input type="text" class="form-control form-control-sm" name="staff_email2" value="{{ $company['staff_email2'] }}" maxlength="50" />
													</div>
												</td>
												<th>연락처/휴대전화</th>
												<td>
													<div class="form-inline">
														<div class="form-inline-inner input_box">
															<input type="text" class="form-control form-control-sm" name="staff_phone2" value="{{ $company['staff_phone2'] }}" maxlength="15" />
														</div>
														<span class="text_line">/</span>
														<div class="form-inline-inner input_box">
															<input type="text" class="form-control form-control-sm" name="staff_hp2" value="{{ $company['staff_hp2'] }}" maxlength="15" />
														</div>
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

			<!-- 세금계산서 및 계좌 -->
			<div class="card shadow">
				<div class="card-header mb-0">
					<a href="#">세금계산서 및 계좌</a>
				</div>
				<div class="card-body">
					<div class="row_wrap">
						<div class="row">
							<div class="col-12">
								<div class="table-box-ty2 mobile">
									<table class="table incont table-bordered" width="100%" cellspacing="0">
										<colgroup>
											<col width="110px">
											<col width="20%">
											<col width="100px">
											<col width="20%">
											<col width="100px">
											<col width="20%">
										</colgroup>
										<tbody>
											<tr>
												<th>상호</th>
												<td>
													<div class="input_box flax_box">
														<input type="text" class="form-control form-control-sm" name="name" value="{{ $company['name'] }}" maxlength="100">
													</div>
												</td>
												<th>사업자등록번호</th>
												<td>
													<div class="input_box flax_box">
														<input type="text" class="form-control form-control-sm" name="biz_num" value="{{ $company['biz_num'] }}" maxlength="20">
													</div>
												</td>
												<th>대표자명</th>
												<td>
													<div class="input_box flax_box">
														<input type="text" class="form-control form-control-sm" name="ceo" value="{{ $company['ceo'] }}" maxlength="20">
													</div>
												</td>
											</tr>
											<tr>
												<th>주민(법인) 번호</th>
												<td>
													<div class="input_box flax_box">
														<input type="text" class="form-control form-control-sm" name="jumin_num" value="{{ $company['jumin_num'] }}" maxlength="20">
													</div>
												</td>
												<th>업태</th>
												<td>
													<div class="input_box flax_box">
														<input type="text" class="form-control form-control-sm" name="uptae" value="{{ $company['uptae'] }}" maxlength="15">
													</div>
												</td>
												<th>업종</th>
												<td>
													<div class="input_box flax_box">
														<input type="text" class="form-control form-control-sm" name="upjong" value="{{ $company['upjong'] }}" maxlength="25">
													</div>
												</td>
											</tr>
											<tr>
												<th>거래은행</th>
												<td>
													<div class="input_box flax_box">
														<input type="text" class="form-control form-control-sm" name="bank" value="{{ $company['bank'] }}" maxlength="25">
													</div>
												</td>
												<th>거래계좌</th>
												<td>
													<div class="input_box flax_box">
														<input type="text" class="form-control form-control-sm" name="account" value="{{ $company['account'] }}" maxlength="25">
													</div>
												</td>
												<th>예금주</th>
												<td>
													<div class="input_box flax_box">
														<input type="text" class="form-control form-control-sm" name="dipositor" value="{{ $company['dipositor'] }}" maxlength="25">
													</div>
												</td>
											</tr>
											<tr>
												<th>지불주기</th>
												<td colspan="5">
													<div class="input_box">
														<select name="pay_day" class="form-control form-control-sm" style="max-width:150px;">
															<option value=''>선택해 주십시오.</option>
															@foreach ($pay_days as $row)
															<option value="{{ $row->code_id }}" @if( $row->code_id == $pay_day ) selected @endif >
																{{ $row->code_val }}
															</option>
															@endforeach
														</select>
													</div>
												</td>
											</tr>
											<tr>
												<th>사업장주소</th>
												<td colspan="5">
													<div class="input_box flax_box address_box">
														<input type="text" id="zip_code" name="zip_code" class="form-control form-control-sm" value="{{ $company['zip_code'] }}" style="width:calc(25% - 10px);margin-right:10px;" readonly="readonly">
														<input type="text" id="addr1" name="addr1" class="form-control form-control-sm" value="{{ $company['addr1'] }}" style="width:calc(25% - 10px);margin-right:10px;" readonly="readonly">
														<input type="text" id="addr2" name="addr2" class="form-control form-control-sm" value="{{ $company['addr2'] }}" style="width:calc(25% - 10px);margin-right:10px;">
														<a href="javascript:;" onclick="execDaumPostcode('zip')" class="btn btn-sm btn-primary shadow-sm" style="width:80px;">
															<i class="fas fa-search fa-sm text-white-50"></i>
															검색
														</a>
													</div>
												</td>
											</tr>
											<tr>
												<th>반송지주소</th>
												<td colspan="5">
													<div class="input_box flax_box address_box">
														<input type="text" id="r_zip_code" name="r_zip_code" class="form-control form-control-sm" value="{{ $company['r_zip_code'] }}" style="width:calc(25% - 10px);margin-right:10px;" readonly="readonly">
														<input type="text" id="r_addr1" name="r_addr1" class="form-control form-control-sm" value="{{ $company['r_addr1'] }}" style="width:calc(25% - 10px);margin-right:10px;" readonly="readonly">
														<input type="text" id="r_addr2" name="r_addr2" class="form-control form-control-sm" value="{{ $company['r_addr2'] }}" style="width:calc(25% - 10px);margin-right:10px;">
														<a href="javascript:;" onclick="execDaumPostcode('r_zip')" class="btn btn-sm btn-primary shadow-sm" style="width:80px;margin-right:10px;">
															<i class="fas fa-search fa-sm text-white-50"></i>
															검색
														</a>
														<!-- d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm -->
														<a href="javascript:;" id="copyAddress" class="btn btn-sm btn-outline-primary shadow-sm" style="width:120px;">사업장주소 복사</a>
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

			<!-- 업체 cs 정보 -->
			<div class="card shadow">
				<div class="card-header mb-0">
					<h5 class="m-0 font-weight-bold">업체 cs 정보</h5>
				</div>
				<div class="card-body">
					<div class="row_wrap">
						<div class="row">
							<div class="col-12">
								<div class="table-box-ty2 mobile">
									<table class="table incont table-bordered" id="dataTable" width="100%" cellspacing="0">
										<colgroup>
											<col width="110px">
											<col width="20%">
											<col width="110px">
											<col width="20%">
											<col width="110px">
											<col width="20%">
										</colgroup>
										<tbody>
											<tr>
												<th>사업자구분</th>
												<td>
													<div class="form-inline form-radio-box flax_box">
														<div class="custom-control custom-radio">
															<input type="radio" name="biz_type" id="biz_type_c" value="C" class="custom-control-input" @if($company['biz_type']=='C' )checked="checked" @endif />
															<label class="custom-control-label" for="biz_type_c">법인</label>
														</div>
														<div class="custom-control custom-radio">
															<input type="radio" name="biz_type" id="biz_type_p" value="P" class="custom-control-input" @if($company['biz_type']=='P' )checked="checked" @endif />
															<label class="custom-control-label" for="biz_type_p">개인</label>
														</div>
													</div>
												</td>
												<th>통신판매신고</th>
												<td>
													<div class="input_box">
														<input type="text" class="form-control form-control-sm" name="mail_order_nm" value="{{ $company['mail_order_nm'] }}" maxlength="20">
													</div>
												</td>
												<th class="ty3">CS담당자</th>
												<td class="ty3">
													<div class="input_box">
														<input type="text" class="form-control form-control-sm" name="cs_nm" value="{{ $company['cs_nm'] }}" maxlength="10" />
													</div>
												</td>
											</tr>
											<tr>
												<th class="ty3">CS담당자 이메일</th>
												<td class="ty3">
													<div class="input_box">
														<input type="text" class="form-control form-control-sm" name="cs_email" value="{{ $company['cs_email'] }}" maxlength="50" />
													</div>
												</td>
												<th class="ty3">CS담당자 연락처</th>
												<td class="ty3">
													<div class="input_box flax_box">
														<input type="text" class="form-control form-control-sm" name="cs_phone" value="{{ $company['cs_phone'] }}" maxlength="15" style="width:75%;" /> (-)포함
													</div>
												</td>
												<th class="ty3">CS담당자 휴대전화</th>
												<td class="ty3">
													<div class="input_box flax_box">
														<input type="text" class="form-control form-control-sm" name="cs_hp" value="{{ $company['cs_hp'] }}" maxlength="15" style="width:75%;" /> (-)포함
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
			<!-- 전시카테고리/용도카테고리 -->
			<div class="row mt-3">
				<div class="col-sm-6">
					<div class="card">
						<div class="card-header mb-0 d-flex align-items-center justify-content-between">
							<a href="#">전시카테고리</a>
							<div class="btn-group ml-sm-2 mt-1 mt-sm-0" role="group">
								<button type="button" class="btn btn-sm btn-primary btn-display-add" data-toggle="tooltip" data-placement="top" title="" data-original-title="추가" onclick="popCategory('DISPLAY')">
									<i class="bx bx-plus"></i>
								</button>
								<button type="button" class="btn btn-sm btn-outline-secondary btn-display-delete" data-toggle="tooltip" data-placement="top" title="" data-original-title="삭제" onclick="DelCategory('DISPLAY');">
									<i class="far fa-trash-alt"></i>
								</button>
							</div>
						</div>
						<div class="card-body">
							<div class="table-responsive" style="height:300px;">
								<div id="div-gd1" style="width:100%;" class="ag-theme-balham"></div>
							</div>
						</div>
					</div>
				</div>
				<div class="col-sm-6">
					<div class="card">
						<div class="card-header mb-0 d-flex align-items-center justify-content-between">
							<a href="#">용도카테고리</a>
							<div class="btn-group ml-sm-2 mt-1 mt-sm-0" role="group">
								<button type="button" class="btn btn-sm btn-primary btn-display-add" data-toggle="tooltip" data-placement="top" title="" data-original-title="추가" onclick="popCategory('ITEM')">
									<i class="bx bx-plus"></i>
								</button>
								<button type="button" class="btn btn-sm btn-outline-secondary btn-display-delete" data-toggle="tooltip" data-placement="top" title="" data-original-title="삭제" onclick="DelCategory('ITEM');">
									<i class="far fa-trash-alt"></i>
								</button>
							</div>
						</div>
						<div class="card-body">
							<div class="table-responsive" style="height:300px;">
								<div id="div-gd2" style="width:100%;" class="ag-theme-balham"></div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</form>
</div>

<!--

-->
<!-- 공통 -->
<script language="javascript">
	var title = "{$title}";
	var cmd = "{$cmd}";
	var cat_cmd = "";
	var cate_type = "";
	const com_id = "{{ $company['com_id'] }}";

	$(function() {
		$("a").click(function(event) {
			event.preventDefault();
		});
	});
	var _isloading = false;
	var _grid_loading = false;

	function AddCategory(type) {
		@if($cmd == 'editcmd')
		cat_cmd = 'add_category';
		@endif
		cate_type = type;
		var url = "/head/api/category/" + type;
		const Com = window.open(url, "_blank", "toolbar=no,scrollbars=yes,resizable=yes,status=yes,top=2 00,left=500,width=580,height=600");
	}

	$('#copyAddress').click(function() {
		if ($("#zip_code").val() == "") {
			alert("사업장 주소를 검색후 클릭해주세요.");
			return;
		}

		$("#r_zip_code").val($("#zip_code").val());
		$("#r_addr1").val($("#addr1").val());
		$("#r_addr2").val($("#addr2").val());
	});

	function ShowPolicyLayer(policy) {
		if (policy == "s") {
			document.getElementById("dlv_policy_shop").style.display = "inline";
			document.getElementById("dlv_policy_company").style.display = "none";
		} else {
			document.getElementById("dlv_policy_shop").style.display = "none";
			document.getElementById("dlv_policy_company").style.display = "inline";
		}
	}

	function DelCategory(type) {
		@if($cmd == 'editcmd')
		cat_cmd = 'del_category';
		@endif

		if (cat_cmd == "del_category") {
			var selectedRowData = (type == "DISPLAY") ? gx_display.gridOptions.api.getSelectedRows() : gx_item.gridOptions.api.getSelectedRows();
			var cat_data = "";
			var cat_nm = (type == "DISPLAY") ? "전시" : "용도";
			var comId = $("[name=com_id]").val();
			cate_type = type;

			selectedRowData.forEach(function(selectedRowData, index) {
				if (selectedRowData.d_cat_cd != "") {
					if (cat_data == "") {
						cat_data = selectedRowData.d_cat_cd;
					} else {
						cat_data += ',' + selectedRowData.d_cat_cd;
					}
				}
			});

			if (cat_data == "") {
				alert("삭제할 " + cat_nm + "카테고리를 선택하세요.");

				return false;
			}

			$.ajax({
				async: true,
				type: 'get',
				url: '/head/standard/std02/delcate/' + comId,
				data: {
					"cat_cd": cat_data,
					"cat_type": cate_type
				},
				success: function(data) {
					if (data.cat_code == 1) {
						if (cate_type == "DISPLAY") {
							gx_display.gridOptions.api.updateRowData({
								remove: selectedRowData
							});

						} else if (cate_type == "ITEM") {
							gx_item.gridOptions.api.updateRowData({
								remove: selectedRowData
							});
						}
					} else {
						alert("장애가 발생했습니다.\n관리자에게 문의해 주십시오.");
					}


				},
				complete: function() {
					_grid_loading = false;
				},
				error: function(request, status, error) {
					alert("장애가 발생했습니다.\n관리자에게 문의해 주십시오.");

					console.log("error")
					//console.log("code:"+request.status+"\n"+"message:"+request.responseText+"\n"+"error:"+error);

				}
			});
		}

	}

	function SetDCategory(idx, text, mx_len) {
		//미설정 카테고리 변경 못함.
		if (idx == "000") {
			alert("미설정 카테고리로는 선택할 수 없습니다.\n다른 카테고리를 선택해 주십시오.");
			return false;
		}

		@if($cmd == 'editcmd')
		SetRepCategory(idx, text, mx_len, cate_type);
		@endif

	}
	/*
	 * 변경 또는 복사할 전시 카테고리 세팅
	 */
	function SetRepCategory(idx, text, mx_len, cateType) {

		//var cat_cd = 
		if (cat_cmd == "add_category") {
			var comId = $("[name=com_id]").val();
			var act_url = "";


			$.ajax({
				async: true,
				type: 'get',
				url: '/head/standard/std02/addcate/' + comId,
				data: {
					"cat_cd": idx,
					"cat_type": cateType
				},
				success: function(data) {
					if (data.cat_code == 1) {
						setRowData(idx, text, cate_type);
					} else {
						alert("장애가 발생했습니다.\n관리자에게 문의해 주십시오.");
					}

				},
				complete: function() {
					_grid_loading = false;
				},
				error: function(request, status, error) {
					alert("장애가 발생했습니다.\n관리자에게 문의해 주십시오.");

					console.log("error")
					//console.log("code:"+request.status+"\n"+"message:"+request.responseText+"\n"+"error:"+error);

				}
			});
		}

	}

	function setRowData(idx, text, cateType) {
		var add_row = [{
			"d_cat_cd": idx,
			"full_nm": text
		}];

		if (cateType == "DISPLAY") {
			gx_display.gridOptions.api.updateRowData({
				add: add_row
			});

		} else if (cateType == "ITEM") {
			gx_item.gridOptions.api.updateRowData({
				add: add_row
			});
		}
	}

	function checkdup() {
		var comId = $("[name=com_id]").val();

		if (Validate("check")) {
			//catCdCopy
			$.ajax({
				async: true,
				type: 'put',
				url: '/head/standard/std02/checkid/' + comId,
				success: function(data) {
					//console.log(data);
					cbcheckdup(data);

				},
				complete: function() {
					_grid_loading = false;
				},
				error: function(request, status, error) {
					alert("장애가 발생했습니다.\n관리자에게 문의해 주십시오.");

					console.log("error")
					//console.log("code:"+request.status+"\n"+"message:"+request.responseText+"\n"+"error:"+error);
				}
			});
		}
	}

	function cbcheckdup(res) {

		if (res.com_code == "1") {
			$("[name=com_id_chk]").val("Y");
			$("#checkdupmessage").html("<font color='blue' style='font-size:10px; letter-spacing:0px;'><b>입력하신 업체 아이디는 등록 가능합니다.</b></font>");
		} else {
			$("[name=com_id_chk]").val("");
			$("#checkdupmessage").html("<font color='red' style='font-size:10px; letter-spacing:0px;'><b>입력하신 업체 아이디는 이미 사용 중 입니다.</b></font>");
		}
	}

	function Cmder(cmd) {
		if (cmd == "addcmd" || cmd == "editcmd") {
			if (Validate(cmd)) {
				SaveCmd(cmd);
			}
		}
	}

	var _cmd = '';

	function SaveCmd(cmd) {
		var f1 = $("form[name=f1]");

		$.ajax({
			async: true,
			type: 'put',
			url: '/head/standard/std02/comm',
			data: $("form[name=f1]").serialize(),
			success: function(data) {
				cbSaveCmd(data);
			},
			complete: function() {
				_grid_loading = false;
			},
			error: function(request, status, error) {
				alert("장애가 발생했습니다.\n관리자에게 문의해 주십시오.");

				// console.log("error")
				console.log("code:"+request.status+"\n"+"message:"+request.responseText+"\n"+"error:"+error);
			}
		});
	}

	function cbSaveCmd(res) {
		var _cmd = $("[name=cmd]").val();

		if (res.result_code == 1) {
			if (_cmd == "addcmd") {
				alert('업체 정보를 등록하였습니다.');
			} else {
				alert('업체 정보를 수정하였습니다.');
			}
			var url = "/head/standard/std02/show/" + $("[name=com_id]").val();
			//			document.location.href = url;
			document.location.replace(url);

		} else if (res.result_code == 0) {
			alert("장애가 발생했습니다.\n관리자에게 문의해 주십시오.");
		} else if (res.result_code == -1) {
			alert("이미 등록된 아이디가 존재합니다.");
		}
	}

	function Validate(cmd) {
		if (cmd == "addcmd" || cmd == "editcmd") {
			if ($("[name=com_id]").val() == '') {
				alert('업체아이디를 입력해 주십시오.');
				$("[name=com_id]").focus();
				return false;
			}

			if (cmd == "addcmd") {
				if (!ValidId($("[name=com_id]"))) {
					$("[name=com_id]").focus();
					return false;
				}
				if ($("[name=com_id_chk]").val() == "") {
					alert("업체아이디 중복확인을 해 주십시오.");
					$("[name=com_id]").focus();
					return false;
				}
			}

			if ($("[name=pwd]").val() == '') {
				alert('비밀번호를 입력해 주십시오.');
				$("[name=pwd]").focus();
				return false;
			}

			if ($("[name=change_pwd]").checked == true) {
				if ($("[name=pwd]").val() == '') {
					alert('비밀번호를 입력해 주십시오.');
					$("[name=pwd]").focus();
					return false;
				}
			}

			if ($("[name=com_nm]").val() == '') {
				alert('업체을 입력해 주십시오.');
				$("[name=com_nm]").focus();
				return false;
			}

			if ($("[name=com_type]").val() == '') {
				alert('업체구분을 선택해 주십시오.');
				$("[name=com_type]").focus();
				return false;
			}

			if ($("[name=baesong_kind]").val() == '') {
				alert('배송방식을 선택해 주십시오.');
				$("[name=baesong_kind]").focus();
				return false;
			}

			if ($("[name=baesong_info]").val() == '') {
				alert('배송방식을 선택해 주십시오.');
				$("[name=baesong_info]").focus();
				return false;
			}

			if (document.f1.dlv_policy[1].checked) {
				if ($("[name=dlv_amt]").val() == '') {
					alert('배송비를 입력해 주십시오.');
					$("[name=dlv_amt]").focus();
					return false;
				}

				if ($("[name=free_dlv_amt_limit]").val() == '') {
					alert('무료 배송비를 입력해 주십시오.');
					$("[name=free_dlv_amt_limit]").focus();
					return false;
				}
			}

			if ($("[name=margin_type]").val() == 'FEE') {
				if ($("[name=pay_fee]").val() == '') {
					alert('판매수수료율을 입력해 주십시오.');
					$("[name=pay_fee]").focus();
					return false;
				}

				if ($("[name=pay_fee]").val() > 100) {
					alert('판매수수료율은 100%를 넘을 수 없습니다.');
					$("[name=pay_fee]").val() = 0;
					$("[name=pay_fee]").focus();
					return false;
				}
			}

			if ($("[name=md_nm]").val() == '') {
				alert('담당MD 이름을 입력해 주십시오.');
				$("[name=md_nm]").focus();
				return false;
			}
			
			if ($("[name=settle_nm]").val() == '') {
				alert('정산담당자 이름을 입력해 주십시오.');
				$("[name=settle_nm]").focus();
				return false;
			}

			if ($("[name=biz_num]").val() != '') {

				if ($("[name=name]").val() == '') {
					alert('상호를 정확하게 입력해 주십시오.');
					$("[name=name]").focus();
					return false;
				}

				if ($("[name=biz_num]").val() == '') {
					alert('상호를 정확하게 입력해 주십시오.');
					$("[name=biz_num]").focus();
					return false;
				}

				if ($("[name=ceo]").val() == '') {
					alert('대표자명을 정확하게 입력해 주십시오.');
					$("[name=ceo]").focus();
					return false;
				}

				if ($("[name=jumin_num]").val() == '') {
					alert('주민(법인)번호를 정확하게 입력해 주십시오.');
					$("[name=jumin_num]").focus();
					return false;
				}

				if ($("[name=uptae]").val() == '') {
					alert('업태를 정확하게 입력해 주십시오.');
					$("[name=uptae]").focus();
					return false;
				}
				if ($("[name=upjong]").val() == '') {
					alert('업종을 정확하게 입력해 주십시오.');
					$("[name=upjong]").focus();
					return false;
				}

				if ($("[name=bank]").val() == '') {
					alert('거래은행을 정확하게 입력해 주십시오.');
					$("[name=bank]").focus();
					return false;
				}

				if ($("[name=account]").val() == '') {
					alert('거래계좌를 정확하게 입력해 주십시오.');
					$("[name=account]").focus();
					return false;
				}

				if ($("[name=dipositor]").val() == '') {
					alert('예금주를 정확하게 입력해 주십시오.');
					$("[name=dipositor]").focus();
					return false;
				}

				if ($("[name=zip_code]").val() == '') {
					alert('사업장주소를 정확하게 입력해 주십시오.');
					$("[name=zip_code]").focus();
					return false;
				}

				if ($("[name=addr1]").val() == '') {
					alert('사업장주소를 정확하게 입력해 주십시오.');
					$("[name=addr1]").focus();
					return false;
				}

				if ($("[name=addr2]").val() == '') {
					alert('사업장주소를 정확하게 입력해 주십시오.');
					$("[name=addr2]").focus();
					return false;
				}
			}

			if ($("[name=margin_type]").val() == "") {
				alert("수수료적용 방식을 선택해 주십시오.");
				$("[name=margin_type]").focus();
				return false;
			}

		}
		if (cmd == "check") {
			var com_id = $("[name=com_id]").val();

			if (com_id.trim() == "") {
				alert("업체아이디를를 입력하세요.");
				$("[name=com_id]").focus();
				return false;
			}
		}

		return true;
	}

	function ValidId(obj) {
		var deny_pattern = /[^(_a-zA-Z0-9)]/;
		if (deny_pattern.test(obj.value)) {
			alert("아이디는 영문자와 숫자만을 허용합니다.");
			obj.value = "";
			return false;
		}
		return true;
	}

	function display_callback(data) {}

	function item_callback(data) {}


	/**************************************************************
	 * 우편번호 검색 기능
	 **************************************************************/
	function execDaumPostcode(type) {
		new daum.Postcode({
			oncomplete: function(data) {

				var postcode;
				var address;
				var address2;
				// 팝업에서 검색결과 항목을 클릭했을때 실행할 코드를 작성하는 부분.

				// 각 주소의 노출 규칙에 따라 주소를 조합한다.
				// 내려오는 변수가 값이 없는 경우엔 공백('')값을 가지므로, 이를 참고하여 분기 한다.
				var fullAddr = ''; // 최종 주소 변수
				var extraAddr = ''; // 조합형 주소 변수

				// 사용자가 선택한 주소 타입에 따라 해당 주소 값을 가져온다.
				if (data.userSelectedType === 'R') { // 사용자가 도로명 주소를 선택했을 경우
					fullAddr = data.roadAddress;

				} else { // 사용자가 지번 주소를 선택했을 경우(J)
					fullAddr = data.jibunAddress;
				}

				// 사용자가 선택한 주소가 도로명 타입일때 조합한다.
				if (data.userSelectedType === 'R') {
					//법정동명이 있을 경우 추가한다.
					if (data.bname !== '') {
						extraAddr += data.bname;
					}
					// 건물명이 있을 경우 추가한다.
					if (data.buildingName !== '') {
						extraAddr += (extraAddr !== '' ? ', ' + data.buildingName : data.buildingName);
					}
					// 조합형주소의 유무에 따라 양쪽에 괄호를 추가하여 최종 주소를 만든다.
					fullAddr += (extraAddr !== '' ? ' (' + extraAddr + ')' : '');
				}

				if (type == 'zip') {
					// 우편번호와 주소 정보를 해당 필드에 넣는다.
					document.getElementById('zip_code').value = data.zonecode; //5자리 새우편번호 사용
					document.getElementById('addr1').value = fullAddr;

					// 커서를 상세주소 필드로 이동한다.
					document.getElementById('addr2').focus();
				} else if (type == 'r_zip') {
					// 우편번호와 주소 정보를 해당 필드에 넣는다.
					document.getElementById('r_zip_code').value = data.zonecode; //5자리 새우편번호 사용
					document.getElementById('r_addr1').value = fullAddr;

					// 커서를 상세주소 필드로 이동한다.
					document.getElementById('r_addr2').focus();
				}

			}
		}).open();
	}
</script>
<!-- 전시카테고리 시작 -->
<script language="javascript">
	var display_columns = [
		// this row shows the row index, doesn't use any data from the row
		{
			headerName: '#',
			width: 40,
			maxWidth: 100,
			// it is important to have node.id here, so that when the id changes (which happens
			// when the row is loaded) then the cell is refreshed.
			valueGetter: 'node.id',
			cellRenderer: 'loadingRenderer',
		},
		{
			headerName: '',
			headerCheckboxSelection: true,
			checkboxSelection: true,
			width: 28,
			cellRenderer: function(params) {
				if (params.data.group_cd !== undefined && params.data.group_cd !== null) {
					return "<input type='checkbox' checked/>";
				}
			}
		},
		{
			field: "d_cat_cd",
			headerName: "코드",
			width: 100,
			cellStyle: StyleGoodsTypeNM,
			editable: true,
		},
		{
			field: "full_nm",
			headerName: "카테고리",
			width: "auto",
			editable: true,
		},
	];

	const pAppDisplay = new App('', {
		gridId: "#div-gd1"
	});
	const gridDivDisplay = document.querySelector(pAppDisplay.options.gridId);
	const gx_display = new HDGrid(gridDivDisplay, display_columns);

	pAppDisplay.ResizeGrid();

	function SearchDisplay() {
		var formData = "";
		gx_display.Request('/head/standard/std02/getcate1/' + com_id, formData, 1, display_callback);
	}



	$(function() {
		SearchDisplay();
	});
</script>
<!-- 전시카테고리 끝 -->


<!-- 용도카테고리 시작 -->
<script language="javascript">
	var item_columns = [
		// this row shows the row index, doesn't use any data from the row
		{
			headerName: '#',
			width: 40,
			maxWidth: 100,
			// it is important to have node.id here, so that when the id changes (which happens
			// when the row is loaded) then the cell is refreshed.
			valueGetter: 'node.id',
			cellRenderer: 'loadingRenderer',
		},
		{
			headerName: '',
			headerCheckboxSelection: true,
			checkboxSelection: true,
			width: 28,
			cellRenderer: function(params) {
				if (params.data.group_cd !== undefined && params.data.group_cd !== null) {
					return "<input type='checkbox' checked/>";
				}
			}
		},
		{
			field: "d_cat_cd",
			headerName: "코드",
			width: 100,
			cellStyle: StyleGoodsTypeNM,
			editable: true,
		},
		{
			field: "full_nm",
			headerName: "카테고리",
			width: "auto",
			editable: true,
		},
	];
	const pAppItem = new App('', {
		gridId: "#div-gd2"
	});
	const gridDivItem = document.querySelector(pAppItem.options.gridId);
	const gx_item = new HDGrid(gridDivItem, item_columns);

	pAppItem.ResizeGrid();

	function SearchItem() {
		var formData = "";
		gx_item.Request('/head/standard/std02/getcate2/' + com_id, formData, 1, item_callback);
	}

	$(function() {
		SearchItem();
	});
</script>
<!-- 용도카테고리 끝 -->

<!-- 전시, 용도 카테고리 api (카테고리 추가시 기존에 에러 발생하던 미구현된 부분들을 대체)-->
<script language="javascript">

	const CMD = '{{$cmd}}';
	
	const popCategory = (type) => {
		
		if (CMD != 'editcmd') {
			alert("업체 등록 후 설정 가능합니다.");
			return false;
		}
		
		if (type == 'DISPLAY') {
			searchCategory.Open('DISPLAY', (code, name, full_name) => {
				if (searchCategory.type === "ITEM") {
					alert("전시 카테고리만 설정가능합니다.");
					return false;
				}

				let rows = gx_display.getRows();
				const codes = rows.map(row => row.d_cat_cd );
				if (codes.includes(code)) {
					alert("이미 등록된 카테고리입니다.");
					return false;
				}

				var comId = $("[name=com_id]").val();
				$.ajax({
					async: true,
					type: 'get',
					url: '/head/standard/std02/addcate/' + comId,
					data: {
						"cat_cd": code,
						"cat_type": type
					},
					success: function(data) {
						if (data.cat_code == 1) {
							rows = [{d_cat_cd: code, full_nm: full_name}];
							gx_display.addRows(rows);
						} else {
							alert("장애가 발생했습니다.\n관리자에게 문의해 주십시오.");
						}

					},
					complete: function() {
						_grid_loading = false;
					},
					error: function(request, status, error) {
						alert("장애가 발생했습니다.\n관리자에게 문의해 주십시오.");

						console.log("error")
						//console.log("code:"+request.status+"\n"+"message:"+request.responseText+"\n"+"error:"+error);

					}
				});

				
			});
		} else if (type == 'ITEM') {
			searchCategory.Open('ITEM', (code, name, full_name) => {
				if (searchCategory.type === "DISPLAY") {
					alert("용도 카테고리만 설정가능합니다.");
					return false;
				}
				
				let rows = gx_item.getRows();
				const codes = rows.map(row => row.d_cat_cd );
				if (codes.includes(code)) {
					alert("이미 등록된 카테고리입니다.");
					return false;
				}

				var comId = $("[name=com_id]").val();
				$.ajax({
					async: true,
					type: 'get',
					url: '/head/standard/std02/addcate/' + comId,
					data: {
						"cat_cd": code,
						"cat_type": type
					},
					success: function(data) {
						if (data.cat_code == 1) {
							rows = [{d_cat_cd: code, full_nm: full_name}];
							gx_item.addRows(rows);
						} else {
							alert("장애가 발생했습니다.\n관리자에게 문의해 주십시오.");
						}

					},
					complete: function() {
						_grid_loading = false;
					},
					error: function(request, status, error) {
						alert("장애가 발생했습니다.\n관리자에게 문의해 주십시오.");

						console.log("error")
						//console.log("code:"+request.status+"\n"+"message:"+request.responseText+"\n"+"error:"+error);

					}
				});

			});
		}
	};
</script>

@stop
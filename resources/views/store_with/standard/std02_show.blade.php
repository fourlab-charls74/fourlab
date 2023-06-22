@extends('store_with.layouts.layout-nav')
@php
    if($cmd != "update")	$title = "매장 등록";
	else					$title = "매장 수정";
@endphp
@section('title', $title)
@section('content')
<div class="show_layout py-3 px-sm-3">
    <div class="page_tit d-flex justify-content-between">
        <div class="d-flex">
            <h3 class="d-inline-flex">{{ $title }}</h3>
            <div class="d-inline-flex location">
                <span class="home"></span>
                <span>/ 기준정보관리</span>
                <span>/ 매장정보관리</span>
                <span>/ {{ $title }}</span>
            </div>
        </div>
        <div class="d-flex">
            <a href="javascript:void(0)" onclick="Cmder('{{ $cmd }}')" class="btn btn-primary mr-1"><i class="fas fa-save fa-sm text-white-50 mr-1"></i>저장</a>
            {{-- <!--<a href="javascript:void(0)" onclick="Cmder('delete')" class="btn btn-primary mr-1"><i class="fas fa-trash fa-sm text-white-50 mr-1"></i>삭제</a>//--> --}}
            <a href="javascript:void(0)" onclick="window.close();" class="btn btn-outline-primary"><i class="fas fa-times fa-sm mr-1"></i>닫기</a>
        </div>
    </div>

    <style> 
        .required:after {content:" *"; color: red;}
        .table th {min-width:170px;}
        @media (max-width: 740px) {
            .table td {float: unset !important;width:100% !important;}
        }
		#multi_img {
			display: grid;
			grid-template-columns: 1fr 1fr 1fr;
		}
		.image {
			display: block;
			width: 100%;
		}
		#img_div {
		width: 660px;
		min-height: 0px;
		padding: 10px;
		}

		#img_div:empty:before {
		color: #999;
		font-size: .9em;
		}
    </style>

	<form name="f1" id="f1" enctype="multipart/form-data">
		<input type="hidden" name="cmd" id="cmd" value="{{ $cmd }}">
		<div class="card_wrap aco_card_wrap">
			<div class="card shadow">
				<div class="card-header mb-0">
					<a href="#">매장 정보</a>
				</div>
				<div class="card-body">
                    <div class="row">
                        <div class="col-12">
                            <div class="table-box-ty2 mobile">
                                <table class="table incont table-bordered" width="100%" cellspacing="0">
                                    <tbody>
                                        <tr>
                                            <th class="required">매장코드</th>
                                            <td style="width:35%;">
                                                <div class="d-flex flex-column">
                                                    <div class="d-flex">
                                                        <input type="text" name="store_cd" id="store_cd" value="{{ @$store->store_cd }}" class="form-control form-control-sm mr-2" style="width:100%" readonly/>
                                                    </div>
													<p class="pt-1" style="color:red"> * 매장코드는 판매채널 / 매장구분을 선택하면 자동으로 생성됩니다.</p>
													<input type="hidden" name="pre_store_cd" id="pre_store_cd" value="{{ @$store->store_cd }}">
                                                </div>
                                            </td>
											<th class="required">매장명</th>
											<td style="width:35%;">
												<div class="form-inline">
													<input type="text" name="store_nm" id="store_nm" value="{{ @$store->store_nm }}" class="form-control form-control-sm w-100" />
												</div>
											</td>
                                        </tr>
                                        <tr>
											<th class="required">판매채널/매장구분</th>
											<td>
												<div class="d-flex align-items-center">
													<div class="flex_box w-100">
														<select name='store_channel' id="store_channel" class="form-control form-control-sm"onchange="chg_store_channel();">
															<option value=''>전체</option>
															@if ($cmd == '') 
																@foreach ($store_channel as $sc)
																	<option value='{{ $sc->store_channel_cd }}'> {{ $sc->store_channel }} </option>
																@endforeach
															@else 
																@foreach ($store_channel as $sc)
																	<option value='{{ $sc->store_channel_cd }}' @if($sc->store_channel_cd == $store->store_channel ) selected @endif> {{ $sc->store_channel }} </option>
																@endforeach
															@endif
														</select>
													</div>
													<span class="mr-2 ml-2">/</span>
													<div class="flex_box w-100">
														<select id='store_channel_kind' name='store_channel_kind' class="form-control form-control-sm" @if($cmd == '') disabled @endif @if($cmd == '') onchange="createStoreCd()" @endif>
															<option value=''>전체</option>
															@if($cmd == 'update')
																@foreach ($sel_store_kind as $sk)
																	<option value='{{ $sk->store_kind_cd }}' @if($sk->store_kind_cd == $store->store_channel_kind ) selected @endif>{{ $sk->store_kind }}</option>
																@endforeach
															@endif

														</select>
													</div>
												</div>
											</td>
											<th></th>
											<td></td>
											<th class="required" hidden>매장명(약칭)</th>
											<td style="width:35%;" hidden>
												<div class="form-inline">
													<input type="text" name="store_nm_s" id="store_nm_s" value="{{ @$store->store_nm_s }}" class="form-control form-control-sm w-100" />
												</div>
											</td>
                                        </tr>
										<tr>
											<th class="required">주소</th>
											<td colspan="3">
												<div class="d-flex flex-column">
													<div class="d-flex mb-2">
														<input type="text" name="zipcode" id="zipcode" value="{{ @$store->zipcode }}" class="form-control form-control-sm w-50 mr-2" style="max-width:280px;" readonly />
														<button type="button" class="btn btn-outline-primary" onclick="searchZipcode()"><i class="fas fa-search fa-sm mr-1"></i>검색</button>
													</div>
													<input type="text" name="addr1" id="addr1" value="{{ @$store->addr1 }}" class="form-control form-control-sm mb-2 w-100" readonly />
													<input type="text" name="addr2" id="addr2" value="{{ @$store->addr2 }}" class="form-control form-control-sm w-100" />
												</div>
											</td>
										</tr>
										<tr>
											<th class="required">매장지역</th>
											<td>
												<div class="flex_box">
													<select name='store_area' class="form-control form-control-sm">
														<option value=''>전체</option>
														@foreach ($store_areas as $store_area)
															<option value='{{ $store_area->code_id }}' @if(@$store->store_area == $store_area->code_id) selected @endif>{{ $store_area->code_val }}</option>
														@endforeach
													</select>
												</div>
											</td>
											<th>전화번호</th>
											<td>
												<div class="form-inline">
													<input type="text" name="phone" id="phone" value="{{ @$store->phone }}" class="form-control form-control-sm w-100" />
												</div>
											</td>
										</tr>
										<tr>
											<th>FAX번호</th>
											<td>
												<div class="form-inline">
													<input type="text" name="fax" id="fax" value="{{ @$store->fax }}" class="form-control form-control-sm w-100" />
												</div>
											</td>
											<th>휴대폰</th>
											<td>
												<div class="form-inline">
													<input type="text" name="mobile" id="mobile" value="{{ @$store->mobile }}" class="form-control form-control-sm w-100" />
												</div>
											</td>
										</tr>
										<tr>
											<th hidden>점포수수료</th>
											<td hidden>
												<div class="form-inline">
													<input type="text" name="fee" id="fee" value="{{ @$store->fee }}" class="form-control form-control-sm w-100" />
												</div>
											</td>
											
										</tr>
										<tr>
											<th>오픈일</th>
											<td>
												<div class="form-inline">
													<input type="text" name="sdate" id="sdate" value="{{ @$store->sdate }}" class="form-control form-control-sm w-100" />
												</div>
											</td>
											<th>종료일</th>
											<td>
												<div class="form-inline">
													<input type="text" name="edate" id="edate" value="{{ @$store->edate }}" class="form-control form-control-sm w-100" />
												</div>
											</td>
										</tr>
										<tr>
											<th>매장사용여부</th>
											<td>
												<div class="form-inline form-radio-box">
													<div class="custom-control custom-radio">
														<input type="radio" class="custom-control-input" id="use_yn_Y" name="use_yn" value="Y" @if(@$store->use_yn != 'N') checked @endif />
														<label class="custom-control-label" for="use_yn_Y">Y</label>
													</div>
													<div class="custom-control custom-radio">
														<input type="radio" class="custom-control-input" id="use_yn_N" name="use_yn" value="N" @if(@$store->use_yn == 'N') checked @endif />
														<label class="custom-control-label" for="use_yn_N">N</label>
													</div>
												</div>
											</td>
											<th>부가세사용여부</th>
											<td>
												<div class="form-inline form-radio-box">
													<div class="custom-control custom-radio">
														<input type="radio" class="custom-control-input" id="vat_yn_Y" name="vat_yn" value="Y" @if(@$store->vat_yn != 'N') checked @endif />
														<label class="custom-control-label" for="vat_yn_Y">Y</label>
													</div>
													<div class="custom-control custom-radio">
														<input type="radio" class="custom-control-input" id="vat_yn_N" name="vat_yn" value="N" @if(@$store->vat_yn == 'N') checked @endif />
														<label class="custom-control-label" for="vat_yn_N">N</label>
													</div>
												</div>
											</td>
											<th hidden>로스인정률</th>
											<td hidden>
												<div class="d-flex flex-column">
													<div class="d-flex" style="width:100%;line-height:30px;">
														<input type="text" name="loss_rate" id="loss_rate" value="{{ @$store->loss_rate }}" class="form-control form-control-sm mr-1 w-50" />
														%
													</div>
												</div>
											</td>
										</tr>
										<tr>
											<th hidden>입고확인사용여부</th>
											<td hidden>
												<div class="form-inline form-radio-box">
													<div class="custom-control custom-radio">
														<input type="radio" class="custom-control-input" id="ipgo_yn_Y" name="ipgo_yn" value="Y" @if(@$store->ipgo_yn != 'N') checked @endif />
														<label class="custom-control-label" for="ipgo_yn_Y">Y</label>
													</div>
													<div class="custom-control custom-radio">
														<input type="radio" class="custom-control-input" id="ipgo_yn_N" name="ipgo_yn" value="N" @if(@$store->ipgo_yn == 'N') checked @endif />
														<label class="custom-control-label" for="ipgo_yn_N">N</label>
													</div>
												</div>
											</td>
											
										</tr>
										<tr>
											<th>정산관리여부(+수수료등급)</th>
											<td style="width:35%;">
												<div class="form-inline form-radio-box">
													<div class="custom-control custom-radio">
														<input type="radio" class="custom-control-input" id="account_y" name="account_yn" value="Y" @if(@$store->account_yn == 'Y') checked @endif />
														<label class="custom-control-label" for="account_y">Y</label>
													</div>
													<div class="custom-control custom-radio">
														<input type="radio" class="custom-control-input" id="account_n" name="account_yn" value="N" @if(@$store->account_yn != 'Y') checked @endif />
														<label class="custom-control-label" for="account_n">N</label>
													</div>
													&nbsp;&nbsp;&nbsp;
													<select name='grade_cd' id="grade_cd" class="form-control form-control-sm" style="width: 70%;">
														<option value=''>미등록</option>
														@foreach ($grades as $grade)
															<option value='{{ $grade->code_id }}' @if(@$store->grade_cd == $grade->code_id) selected @endif>{{ $grade->code_val }}</option>
														@endforeach
													</select>
												</div>
											</td>
											<th>출고우선순위</th>
											<td style="width:35%;">
												<div class="flex_box">
													<select name='priority' class="form-control form-control-sm">
														<option value=''>전체</option>
														@foreach ($prioritys as $priority)
															<option value='{{ $priority->code_id }}' @if(@$store->priority == $priority->code_id) selected @endif>{{ $priority->code_val }}</option>
														@endforeach
													</select>
												</div>
											</td>
											<th hidden>관리기준</th>
											<td style="width:35%;" hidden>
												<div class="form-inline form-radio-box">
													<div class="custom-control custom-radio">
														<input type="radio" class="custom-control-input" id="manage_type_M" name="manage_type" value="M" @if(@$store->vat_yn != 'M') checked @endif />
														<label class="custom-control-label" for="manage_type_M">중간관리식</label>
													</div>
													<div class="custom-control custom-radio">
														<input type="radio" class="custom-control-input" id="manage_type_P" name="manage_type" value="P" @if(@$store->vat_yn == 'P') checked @endif />
														<label class="custom-control-label" for="manage_type_P">사입식</label>
													</div>
												</div>
											</td>
										</tr>
										<tr>
											<th hidden>경비관리</th>
											<td style="width:35%;" hidden>
												<div class="form-inline form-radio-box">
													<div class="custom-control custom-radio">
														<input type="radio" class="custom-control-input" id="exp_manage_yn_Y" name="exp_manage_yn" value="Y" @if(@$store->vat_yn == 'Y') checked @endif />
														<label class="custom-control-label" for="exp_manage_yn_Y">Y</label>
													</div>
													<div class="custom-control custom-radio">
														<input type="radio" class="custom-control-input" id="exp_manage_yn_N" name="exp_manage_yn" value="N" @if(@$store->vat_yn != 'Y') checked @endif />
														<label class="custom-control-label" for="exp_manage_yn_N">N</label>
													</div>
												</div>
											</td>
											
										</tr>
										<tr>
											<th hidden>동종업계정보입력</th>
											<td hidden>
												<div class="form-inline form-radio-box">
													<div class="custom-control custom-radio">
														<input type="radio" class="custom-control-input" id="competitor_yn_Y" name="competitor_yn" value="Y" checked @if(@$store->competitor_yn == 'Y') checked @endif />
														<label class="custom-control-label" for="competitor_yn_Y">Y</label>
													</div>
													<div class="custom-control custom-radio">
														<input type="radio" class="custom-control-input" id="competitor_yn_N" name="competitor_yn" value="N" @if(@$store->competitor_yn != 'Y') checked @endif />
														<label class="custom-control-label" for="competitor_yn_N">N</label>
													</div>
												</div>
											</td>
											<th hidden>POS 사용여부</th>
											<td hidden>
												<div class="form-inline form-radio-box">
													<div class="custom-control custom-radio">
														<input type="radio" class="custom-control-input" id="pos_yn_Y" name="pos_yn" value="Y" checked/>
														<label class="custom-control-label" for="pos_yn_Y">Y</label>
													</div>
													<div class="custom-control custom-radio">
														<input type="radio" class="custom-control-input" id="pos_yn_N" name="pos_yn" value="N"/>
														<label class="custom-control-label" for="pos_yn_N">N</label>
													</div>
												</div>
											</td>
										</tr>
										<tr>
											<th>타매장재고조회</th>
											<td>
												<div class="form-inline form-radio-box">
													<div class="custom-control custom-radio">
														<input type="radio" class="custom-control-input" id="ostore_stock_yn_Y" name="ostore_stock_yn" value="Y" @if(@$store->ostore_stock_yn != 'N') checked @endif />
														<label class="custom-control-label" for="ostore_stock_yn_Y">Y</label>
													</div>
													<div class="custom-control custom-radio">
														<input type="radio" class="custom-control-input" id="ostore_stock_yn_N" name="ostore_stock_yn" value="N" @if(@$store->ostore_stock_yn == 'N') checked @endif />
														<label class="custom-control-label" for="ostore_stock_yn_N">N</label>
													</div>
												</div>
											</td>
											<th>판매분배분여부</th>
											<td>
												<div class="form-inline form-radio-box">
													<div class="custom-control custom-radio">
														<input type="radio" class="custom-control-input" id="sale_dist_yn_Y" name="sale_dist_yn" value="Y" @if(@$store->sale_dist_yn != 'N') checked @endif />
														<label class="custom-control-label" for="sale_dist_yn_Y">Y</label>
													</div>
													<div class="custom-control custom-radio">
														<input type="radio" class="custom-control-input" id="sale_dist_yn_N" name="sale_dist_yn" value="N" @if(@$store->sale_dist_yn == 'N') checked @endif />
														<label class="custom-control-label" for="sale_dist_yn_N">N</label>
													</div>
												</div>
											</td>
										</tr>
										<tr>
											<th>매장RT여부</th>
											<td>
												<div class="form-inline form-radio-box">
													<div class="custom-control custom-radio">
														<input type="radio" class="custom-control-input" id="rt_yn_Y" name="rt_yn" value="Y" @if(@$store->rt_yn != 'N') checked @endif />
														<label class="custom-control-label" for="rt_yn_Y">Y</label>
													</div>
													<div class="custom-control custom-radio">
														<input type="radio" class="custom-control-input" id="rt_yn_N" name="rt_yn" value="N" @if(@$store->rt_yn == 'N') checked @endif />
														<label class="custom-control-label" for="rt_yn_N">N</label>
													</div>
												</div>
											</td>
											<th>적립금지급여부</th>
											<td>
												<div class="form-inline form-radio-box">
													<div class="custom-control custom-radio">
														<input type="radio" class="custom-control-input" id="point_in_yn_Y" name="point_in_yn" value="Y" @if(@$store->point_in_yn == 'Y') checked @endif />
														<label class="custom-control-label" for="point_in_yn_Y">Y</label>
													</div>
													<div class="custom-control custom-radio">
														<input type="radio" class="custom-control-input" id="point_in_yn_N" name="point_in_yn" value="N" @if(@$store->point_in_yn != 'Y') checked @endif />
														<label class="custom-control-label" for="point_in_yn_N">N</label>
													</div>
												</div>
											</td>
											<th hidden>오픈후한달 재고보기제외 여부</th>
											<td hidden>
												<div class="form-inline form-radio-box">
													<div class="custom-control custom-radio">
														<input type="radio" class="custom-control-input" id="open_month_stock_y" name="open_month_stock_yn" value="Y" checked/>
														<label class="custom-control-label" for="open_month_stock_y">Y</label>
													</div>
													<div class="custom-control custom-radio">
														<input type="radio" class="custom-control-input" id="open_month_stock_n" name="open_month_stock_yn" value="N"/>
														<label class="custom-control-label" for="open_month_stock_n">N</label>
													</div>
												</div>
											</td>
										</tr>
										<tr>
											<th>온라인업체매칭</th>
											<td>
												<div class="form-inline form-radio-box">
													<div class="custom-control custom-radio">
														<input type="radio" class="custom-control-input" id="sale_place_match_y" name="sale_place_match_yn" value="Y" @if(@$store->sale_place_match_yn == 'Y') checked @endif />
														<label class="custom-control-label" for="sale_place_match_y">Y</label>
													</div>
													<div class="custom-control custom-radio">
														<input type="radio" class="custom-control-input" id="sale_place_match_n" name="sale_place_match_yn" value="N" @if(@$store->sale_place_match_yn != 'Y') checked @endif />
														<label class="custom-control-label" for="sale_place_match_n">N</label>
													</div>
													&nbsp;&nbsp;&nbsp;
													<select name='com_id' id="com_id" class="form-control form-control-sm" style="width:70%;">
														<option value=''>전체</option>
															@foreach ($store_match as $sm)
																<option value='{{ $sm->com_id }}' @if(@$store->com_id == $sm->com_id) selected @endif @if(@$sm->s_match != '') disabled style="background: #d2d2d2;" @endif>{{ $sm->com_nm }}</option>
															@endforeach
													</select>
												</div>
											</td>
											<th>중간관리여부</th>
											<td>
												<div class="form-inline form-radio-box">
													<div class="custom-control custom-radio">
														<input type="radio" class="custom-control-input" id="md_manage_yn_Y" name="md_manage_yn" value="Y" @if(@$store->md_manage_yn != 'N') checked @endif />
														<label class="custom-control-label" for="md_manage_yn_Y">Y</label>
													</div>
													<div class="custom-control custom-radio">
														<input type="radio" class="custom-control-input" id="md_manage_yn_N" name="md_manage_yn" value="N" @if(@$store->md_manage_yn == 'N') checked @endif />
														<label class="custom-control-label" for="md_manage_yn_N">N</label>
													</div>
												</div>
											</td>
										</tr>
										<tr>
											<th>이미지</th>
											<td colspan="3">
												<div style="text-align:center;" id="multi_img">
													<input type='file' id='btnAdd' name="file" multiple='multiple' accept=".jpg"/>
												</div>
												<div id='img_div'></div>
												@if ($cmd == 'update')
													@foreach(@$store_img as $src)
														<div id='img_show_div' data-img="{{$src->seq}}" style="display:inline-block;position:relative;width:150px;height:120px;margin:5px;z-index:1">
															<img src="{{$src->img_url}}" alt="" id="img_show" style="width:100%;height:100%;z-index:none">
															<input type="button" value="x" onclick= "delete_img('{{$src->store_cd}}','{{$src->seq}}')" style="width:20px;height:20px;position:absolute;right:0px;top:0px;border:none;font-size:large;font-weight:bolder;background:none;color:black;padding-bottom:20px;">
														</div>
													@endforeach
												@endif
											</td>
										</tr>
										@if($cmd !== "")
                                            @if(@$store->store_type !== '11' && @$store->addr1 !== null && @$store->map_code !== null)
												<tr>
													<th>지도</th>
													<td style="width:100%;">
														<div class="form-inline">
															<div id="map" style="width:100%;height:400px;"></div>
														</div>
													</td>
												</tr>
											@endif
										@endif
										<tr>
											<th>맵 코드</th>
											<td colspan="3">
												<div>
													<input type="text" class="form-control form-control-sm w-100" value="{{@$store->map_code}}" name="map_code" id="map_code">
													<span>* 위도,경도 순으로 (,)로 구분하여 입력해주세요. EX) 37.5251913154781,126.929112756574</span>
												</div>
											</td>
										</tr>
									</tbody>
                                </table>
                            </div>
                        </div>
                    </div>

					<div class="row">
						<div class="col-12" style="padding-top:30px;font-size:18px;font-weight:bold;">+ 관리자 정보</div>
					</div>

                    <div class="row">
                        <div class="col-12">
                            <div class="table-box-ty2 mobile">
                                <table class="table incont table-bordered" width="100%" cellspacing="0">
                                    <tbody>
										<tr>
											<th>매니저명</th>
											<td>
												<div class="form-inline">
													<input type="text" name="manager_nm" id="manager_nm" value="{{ @$store->manager_nm }}" class="form-control form-control-sm w-100" />
												</div>
											</td>
											<th>매니저연락처</th>
											<td>
												<div class="form-inline">
													<input type="text" name="manager_mobile" id="manager_mobile" value="{{ @$store->manager_mobile }}" class="form-control form-control-sm w-100" />
												</div>
											</td>
										</tr>
										<tr>
											<th>이메일</th>
											<td>
												<div class="form-inline">
													<input type="text" name="email" id="email" value="{{ @$store->email }}" class="form-control form-control-sm w-100" />
												</div>
											</td>
											<th></th>
											<td></td>
										</tr>
										

									</tbody>
								</table>
							</div>
						</div>
					</div>

					<div class="row">
						<div class="col-12" style="padding-top:30px;font-size:18px;font-weight:bold;">+ 정산관련 정보</div>
					</div>

                    <div class="row">
                        <div class="col-12">
                            <div class="table-box-ty2 mobile">
                                <table class="table incont table-bordered" width="100%" cellspacing="0">
                                    <tbody>
										<tr>
											<th>은행명/계좌/예금주</th>
											<td colspan="3">
												<div class="d-flex flex-column">
                                                    <div class="d-flex" style="width:100%;">
                                                        <input type="text" name="bank_nm" id="bank_nm" value="{{ @$store->bank_nm }}" class="form-control form-control-sm w-50 mr-2" />
                                                        <input type="text" name="bank_no" id="bank_no" value="{{ @$store->bank_no }}" class="form-control form-control-sm w-100 mr-2" />
                                                        <input type="text" name="depositor" id="depositor" value="{{ @$store->depositor }}" class="form-control form-control-sm w-50" />
                                                    </div>
												</div>
											</td>
										</tr>
										<tr>
											<th>매장보증금</th>
											<td>
												<div class="form-inline">
													<input type="text" name="deposit_cash" id="deposit_cash" value="{{ @$store->deposit_cash }}" class="form-control form-control-sm w-100" />
												</div>
											</td>
											<th>부동산담보</th>
											<td>
												<div class="form-inline">
													<input type="text" name="deposit_coll" id="deposit_coll" value="{{ @$store->deposit_coll }}" class="form-control form-control-sm w-100" />
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
                                    <tbody>
										<tr>
											<th>사업자등록번호</th>
											<td colspan="3">
												<div class="form-inline">
													<input type="text" name="biz_no" id="biz_no" value="{{ @$store->biz_no }}" class="form-control form-control-sm w-50" />
												</div>
											</td>
										</tr>
										<tr>
											<th>상호</th>
											<td style="width:35%;">
												<div class="form-inline">
													<input type="text" name="biz_nm" id="biz_nm" value="{{ @$store->biz_nm }}" class="form-control form-control-sm w-100" />
												</div>
											</td>
											<th>대표자명</th>
											<td style="width:35%;">
												<div class="form-inline">
													<input type="text" name="biz_no" id="biz_no" value="{{ @$store->biz_no }}" class="form-control form-control-sm w-100" />
												</div>
											</td>
										</tr>
										<tr>
											<th>주소</th>
											<td colspan="3">
												<div class="d-flex flex-column">
													<div class="d-flex mb-2">
														<input type="text" name="biz_zipcode" id="biz_zipcode" value="{{ @$store->biz_zipcode }}" class="form-control form-control-sm w-50 mr-2" style="max-width:280px;" readonly />
														<button type="button" class="btn btn-outline-primary" onclick="searchBizZipcode()"><i class="fas fa-search fa-sm mr-1"></i>검색</button>
													</div>
													<input type="text" name="biz_addr1" id="biz_addr1" value="{{ @$store->biz_addr1 }}" class="form-control form-control-sm mb-2 w-100" readonly />
													<input type="text" name="biz_addr2" id="biz_addr2" value="{{ @$store->biz_addr2 }}" class="form-control form-control-sm w-100" />
												</div>
											</td>
										</tr>
										<tr>
											<th>업태</th>
											<td style="width:35%;">
												<div class="form-inline">
													<input type="text" name="biz_uptae" id="biz_uptae" value="{{ @$store->biz_uptae }}" class="form-control form-control-sm w-100" />
												</div>
											</td>
											<th>업종</th>
											<td style="width:35%;">
												<div class="form-inline">
													<input type="text" name="biz_upjong" id="biz_upjong" value="{{ @$store->biz_upjong }}" class="form-control form-control-sm w-100" />
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
	</form>
</div>

<!-- 지도 영역 -->
<script type="text/javascript" src="//dapi.kakao.com/v2/maps/sdk.js?appkey={{ @$map_key->code_val }}&libraries=services"></script>
<script>
	$(document).ready(function() {
		var mapContainer = document.getElementById('map');
		var input = document.getElementById('map_code');
		var map_code = input ? input.value : '';
		var map_cd = map_code.split(',');
	
		var	mapOption = {
			center: new kakao.maps.LatLng(map_cd[0], map_cd[1]),
			level: 4
		};
		var map = new kakao.maps.Map(mapContainer, mapOption); 
		var geocoder = new kakao.maps.services.Geocoder();
		let address = document.getElementById('addr1').value;
		let store_nm = document.getElementById('store_nm').value;

		// 좌표 값에 마커와 인포윈도우 출력
		var markerPosition  = new kakao.maps.LatLng(map_cd[0], map_cd[1]); 

		var marker = new kakao.maps.Marker({
			position: markerPosition
		});

		marker.setMap(map);

		var iwContent = '<div style="width:150px;text-align:center;padding:6px 0;">'+store_nm+'</div>'
			iwPosition = new kakao.maps.LatLng(map_cd[0], map_cd[1]);

		var infowindow = new kakao.maps.InfoWindow({
			position : iwPosition, 
			content : iwContent 
		});
		
		infowindow.open(map, marker); 


		// 주소 값으로 키워드 검색 기능
		// geocoder.addressSearch(address, function(result, status) {
		// 	if (status === kakao.maps.services.Status.OK) {
		// 		var coords = new kakao.maps.LatLng(result[0].y, result[0].x);
		// 		var marker = new kakao.maps.Marker({
		// 			map: map,
		// 			position: coords
		// 		});
		// 		var infowindow = new kakao.maps.InfoWindow({
		// 			content: '<div style="width:150px;text-align:center;padding:6px 0;">'+store_nm+'</div>'
		// 		});
		// 		infowindow.open(map, marker);
		// 		map.setCenter(coords);
		// 	} 
		// });

});

	// 이미지

	$(document).ready(function() {
		$("input:file[name='file']").change(function() {
			var str = $(this).val();
			var fileName = str.split('\\').pop().toLowerCase();
	
			checkFileName(fileName);
		});
	});
 
	function checkFileName(str) {
	
		//1. 확장자 체크
		var ext =  str.split('.').pop().toLowerCase();
		if ($.inArray(ext, ['jpg']) == -1) {
			alert(ext+'파일은 업로드 하실 수 없습니다.');
			$("input:file[name='file']").val("");
			$('#img_div').remove();
			
			location.reload();
		}else{
			
		}
	}

	( imageView = function imageView(img_div, btn) {

		var img_div = document.getElementById(img_div);
		var btnAdd = document.getElementById(btn)
		var sel_files = [];
		var cnt = 0;

		// 이미지와 체크 박스를 감싸고 있는 div 속성
		var div_style = 'display:inline-block;position:relative;'
					+ 'width:150px;height:120px;margin:5px;z-index:1';
		// 미리보기 이미지 속성
		var img_style = 'width:100%;height:100%;z-index:none';
		// 이미지안에 표시되는 체크박스의 속성
		var chk_style = 'width:20px;height:20px;position:absolute;right:0px;top:0px;border:none;font-size:large;'
						+'font-weight:bolder;background:none;color:black;padding-bottom:20px;';

		btnAdd.onchange = function(e) {
			cnt++;
			if (cnt > 1) {
				const parent = document.getElementById('img_div');

				parent.innerHTML = "";
			}
			var files = e.target.files;
			var fileArr = Array.prototype.slice.call(files)
			for (f of fileArr) {
				imageLoader(f);
			}
		}

		/*첨부된 이미지들을 배열에 넣고 미리보기 */
		imageLoader = function(file) {
			if (cnt == 1) {
				sel_files.push(file);
				var reader = new FileReader();
				reader.onload = function(ee) {
					let img = document.createElement('img')
					img.setAttribute('style', img_style)
					img.src = ee.target.result;
					img_div.appendChild(makeDiv(img, file));
				}
			} else {
				sel_files = [];
				sel_files.push(file);
				var reader = new FileReader();
				reader.onload = function(ee) {
					let img = document.createElement('img')
					img.setAttribute('style', img_style)
					img.src = ee.target.result;
					img_div.appendChild(makeDiv(img, file));
				}
			}
			
			reader.readAsDataURL(file);
		}

		makeDiv = function(img, file) {
			if (cnt <= 1) {
				var div = document.createElement('div');
				div.setAttribute('style', div_style);
				
				var btn = document.createElement('input');
				btn.setAttribute('type', 'button');
				btn.setAttribute('value', 'x');
				btn.setAttribute('delFile', file.name);
				btn.setAttribute('style', chk_style);
				btn.onclick = function(ev) {
					var ele = ev.srcElement;
					var delFile = ele.getAttribute('delFile');
					for (var i=0 ;i<sel_files.length; i++) {
						if (delFile== sel_files[i].name) {
							sel_files.splice(i, 1);
						}
					}
					
					dt = new DataTransfer();

					for (f in sel_files) {
						var file = sel_files[f];
						dt.items.add(file);
					}
					btnAdd.files = dt.files;
					var p = ele.parentNode;
					img_div.removeChild(p);
				}
				div.appendChild(img);
				div.appendChild(btn);

				return div;
			} else {
				var div = document.createElement('div');
				div.setAttribute('style', div_style);
				
				var btn = document.createElement('input');
				btn.setAttribute('type', 'button');
				btn.setAttribute('value', 'x');
				btn.setAttribute('delFile', file.name);
				btn.setAttribute('style', chk_style);
				btn.onclick = function(ev) {
					var ele = ev.srcElement;
					var delFile = ele.getAttribute('delFile');
					for (var i=0 ;i<sel_files.length; i++) {
						if (delFile== sel_files[i].name) {
							sel_files.splice(i, 1);
						}
					}
					
					dt = new DataTransfer();

					for (f in sel_files) {
						var file = sel_files[f];
						dt.items.add(file);
					}
					btnAdd.files = dt.files;
					var p = ele.parentNode;
					img_div.removeChild(p);
				}
				div.appendChild(img);
				div.appendChild(btn);

				return div;
			}
				
	}		
}
	)('img_div', 'btnAdd')


	
</script>

<script>
	function delete_img(store_cd, seq){
        let img_show = document.querySelectorAll("#img_show_div");

        if(confirm("선택한 사진을 삭제하시겠습니까?")){
            $.ajax({
                method: 'post',
                url: '/store/standard/std02/del_img',
                data: {data_img : store_cd, seq : seq},
                success: function(data) {
                    if (data.code == '200') {
                        for (let i = 0;i<img_show.length;i++) {
                            if (img_show[i].dataset.img == seq) {
                                img_show[i].remove();
                                break;
                            }
                        }
                    } else {
                        alert('처리 중 문제가 발생하였습니다. 다시 시도하여 주십시오.');
                    }
                },
                error: function(res, status, error) {
                    console.log(error);
                }
            });
        }
    }
</script>

<script src="https://t1.daumcdn.net/mapjsapi/bundle/postcode/prod/postcode.v2.js"></script>
<script type="text/javascript" charset="utf-8">
    function Cmder(type) {
        if( type === "" )				addStore();
        else if( type === "update" )	updateStore();
        else if( type === "delete" )	deleteStore();
    }

	// 주소로 위도/경도 조회
	// const getMapCode = () => {
	// 		var geocoder = new kakao.maps.services.Geocoder();
	// 		let address = document.getElementById('addr1').value;

	// 		let store_kind = $('select[name=store_kind] option:selected').val();
	// 		let store_type = $('select[name=store_type] option:selected').val();

	// 		if (!address) return;
	// 		if (store_kind !== 02 || store_type !== 11) {
	// 			return new Promise((resolve, reject) => {
	// 				geocoder.addressSearch(address, async function(result, status) {
	// 					if (status === await kakao.maps.services.Status.OK) {
	// 						resolve({y: result[0].y, x: result[0].x});
	// 					} else {
	// 						reject(result);
	// 					}
	// 				});
	// 			});
	// 		}
	// 	}
		
    // 매장정보 등록
    async function addStore() {
		// let res = await getMapCode();
		let form = new FormData(document.querySelector("#f1"));
		
		for(let i = 0; i< $("#btnAdd")[0].files.length; i++) {
			form.append("file[]", $("#btnAdd")[0].files[i] || '');
		}

		// form.append("y", res.y || '');
		// form.append("x", res.x || '');

		for(let form_data of form.entries()) {
			form_data[0], form_data[1];
		}

		
	
        if( !validation('add') )	return;
        if( !window.confirm("매장정보를 등록하시겠습니까?") )	return;

        axios({
            url: `/store/standard/std02/update`,
            method: 'post',
            data: form,
        }).then(function (res) {
            if(res.data.code === 200) {
                alert(res.data.msg);
                opener.Search();
                location.href = "/store/standard/std02/show/" + res.data.store_cd;;
            } else if(res.data.code === 500) {
                console.log(res.data);
                alert("수정 중 오류가 발생했습니다.\n관리자에게 문의해주세요.");
            } else if(res.data.code === 400) {
                console.log(res.data);
				alert("매장이미지 중 'jpg'형식이 아닌 파일이 존재합니다.\n이미지파일 확인 후 다시 등록해주세요.");
			}
        }).catch(function (err) {
            console.log(err);
        });
    }

    // 매장정보 수정
    async function updateStore() {
		let map_code = $('#map_code').val();

		// if (map_code != '') {
		// 	const map = await getMapCode();
		// }

		let form = new FormData(document.querySelector("#f1"));

		for (let i = 0; i< $("#btnAdd")[0].files.length; i++) {
			form.append("file[]", $("#btnAdd")[0].files[i] || '');
		}

		if(!validation('update')) return;
		
		if ($('input[name=account_yn]:checked').val() == 'Y' && f1.grade_cd.selectedIndex == 0) {
				return alert('수수료 등급을 선택해주세요.');
			
		}

		for (const [key, value] of form.entries()) {
			console.log(key, value);
		}

        if(!window.confirm("매장정보를 수정하시겠습니까?")) return;

        axios({
            url: `/store/standard/std02/update`,
            method: 'post',
            data: form
        }).then(function (res) {
            if(res.data.code === 200) {
                alert(res.data.msg);
                opener.Search();
                window.close();
            } else if(res.data.code === 500) {
                console.log(res.data);
                alert("수정 중 오류가 발생했습니다.\n관리자에게 문의해주세요.");
            } else if(res.data.code === 400) {
                console.log(res.data);
				alert("매장이미지 중 'jpg'형식이 아닌 파일이 존재합니다.\n이미지파일 확인 후 다시 등록해주세요.");
			} else if(res.data.code === 201) {
				alert("업로드 가능한 파일의 크기는 2MB입니다.\n2MB보다 작은 파일을 업로드해주세요");
			}
        }).catch(function (err) {
			console.log(err);
		})
    }

    // 매장정보 삭제
    async function deleteStore() {
        if(!window.confirm("매장정보를 삭제하시겠습니까?")) return;
        axios({
            url: `/store/standard/std02/delete/` + f1.storage_cd.value,
            method: 'delete',
        }).then(function (res) {
            if(res.data.code === 200) {
                alert(res.data.msg);
                opener.Search();
                window.close();
            } else {
                console.log(res.data);
                alert("삭제 중 오류가 발생했습니다.\n관리자에게 문의해주세요.");
            }
        }).catch(function (err) {
            console.log(err);
        });
    }
    // 우편번호 검색하기
    function searchZipcode() {
        new daum.Postcode({
            oncomplete: function(data) {
                f1.zipcode.value	= data.zonecode;
                f1.addr1.value		= data.address;
            }
        }).open();
    }
    // 우편번호 검색하기 - 사업자
    function searchBizZipcode() {
        new daum.Postcode({
            oncomplete: function(data) {
                f1.biz_zipcode.value	= data.zonecode;
                f1.biz_addr1.value		= data.address;
            }
        }).open();
    }
	// 저장 시 입력값 확인
	const validation = (cmd) => {
		if(cmd === "add"){
			// 매장코드 입력여부
			if(f1.store_cd.value.trim() === '') {
				f1.store_cd.focus();
				return alert("매장코드를 입력해주세요.");
			}
		}
		// 매장명칭 입력여부
		if(f1.store_nm.value.trim() === '') {
			f1.store_nm.focus();
			return alert("매장명을 입력해주세요.");
		}
		// 매장명칭(약칭) 입력여부
		// if(f1.store_nm_s.value.trim() === '') {
		// 	f1.store_nm_s.focus();
		// 	return alert("매장명(약칭)을 입력해주세요.");
		// }
		// 판매채널 선택여부
		if(f1.store_channel.selectedIndex == 0) {
			f1.store_channel.focus();
			return alert("판매채널을 선택해주세요.");
		}
		// 매장구분 선택여부
		if(f1.store_channel_kind.selectedIndex == 0) {
			f1.store_channel_kind.focus();
			return alert("매장구분을 선택해주세요.");
		}
		// 매장종류 선택여부
		// if(f1.store_kind2.selectedIndex == 0) {
		// 	f1.store_kind2.focus();
		// 	return alert("매장종류를 선택해주세요.");
		// }

		// 주소 입력여부
		if(f1.zipcode.value === '') return alert("주소를 입력해주세요.");
		return true;

		// 매장지역 선택여부
		if(f1.store_area.selectedIndex == 0) {
			f1.store_area.focus();
			return alert("매장지역을 선택해주세요.");
		}

		// 매칭매장 선택
		if ($("input[name='sale_place_match_yn']:checked").val() == 'Y' && $('#com_id').val() == '') {
			return alert('매칭할 업체를 선택해주세요.');
		}

		
	}

	//온라인업체매칭 & 정산관리여부
    $(document).ready(function() {
		showSelectBox('sale_place_match_yn', 'com_id');
		showSelectBox('account_yn', 'grade_cd');

        $("input[name='sale_place_match_yn']").change(function(){
			showSelectBox('sale_place_match_yn', 'com_id');
        });

        $("input[name='account_yn']").change(function(){
			showSelectBox('account_yn', 'grade_cd');
        });
    });

	function showSelectBox(radio_id, select_id) {
        if($(`input[name='${radio_id}']:checked`).val() == 'Y'){
            $('#' + select_id).show();
        } else {
            $('#' + select_id).hide();
        }
	}

	// 판매채널 셀렉트박스가 선택되지 않으면 매장구분 셀렉트박스는 disabled처리
	$(document).ready(function() {
		const store_channel = document.getElementById("store_channel");
		const store_channel_kind = document.getElementById("store_channel_kind");

		store_channel.addEventListener("change", () => {
			if (store_channel.value) {
				store_channel_kind.disabled = false;
			} else {
				store_channel_kind.disabled = true;
			}
		});
	});

	// 판매채널이 변경되면 해당 판매채널의 매장구분을 가져오는 부분
	function chg_store_channel() {

		const sel_channel = document.getElementById("store_channel").value;

		$.ajax({
			method: 'post',
			url: '/store/standard/std02/show/chg-store-channel',
			data: {
				'store_channel' : sel_channel
				},
			dataType: 'json',
			success: function (res) {
				if(res.code == 200){
					$('#store_channel_kind').empty();
					let select =  $("<option value=''>전체</option>");
					$('#store_channel_kind').append(select);

					for(let i = 0; i < res.store_kind.length; i++) {
						let option = $("<option value="+ res.store_kind[i].store_kind_cd +">" + res.store_kind[i].store_kind + "</option>");
						$('#store_channel_kind').append(option);
					}

					let store_cd = document.getElementById('store_cd');
					store_cd.value = "";


				} else {
					alert('처리 중 문제가 발생하였습니다. 다시 시도하여 주십시오.');
				}
			},
			error: function(e) {
				console.log(e.responseText)
			}
		});
	}

	//매장코드 자동으로 생성하는 부분
	async function createStoreCd() {
		let store_channel = document.getElementById("store_channel").value;
		let store_channel_kind = document.getElementById("store_channel_kind").value;
		let store_code = store_channel + store_channel_kind;
		let cnt = 0;

		axios({
            url: `/store/standard/std02/create-store-cd`,
            method: 'post',
			data : {
				'store_code' : store_code
			},
			dataType: 'json',
        }).then(function (res) {
            if(res.data.code === 200) {
				if(res.data.cnt == 0) {
					cnt = 1;
					let seq = getSeq(cnt);
					let code = store_code + seq; 
					$('#store_cd').val(code)
				} else if(res.data.cnt > 0) {
					cnt = res.data.cnt + 1
					let seq = getSeq(cnt);
					let code = store_code + seq; 

					const str = code;
					const front = str.substring(0, str.length - 4);
					const str2 = $('#pre_store_cd').val();
					const front2 = str2.substring(0, str2.length - 4);

					if(front == front2) {
						$('#store_cd').val(str2);
					} else {
						$('#store_cd').val(str);
					}
				}
            } else {
                console.log(res.data);
                alert("삭제 중 오류가 발생했습니다.\n관리자에게 문의해주세요.");
            }
        }).catch(function (err) {
            console.log(err);
        });


	}
	
	function getSeq(count) {
		let seq = count.toString();
		while (seq.length < 4) {
			seq = "0" + seq;
		}
		return seq;
	}



</script>
@stop
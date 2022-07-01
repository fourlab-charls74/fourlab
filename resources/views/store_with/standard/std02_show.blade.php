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
                <span>/ 코드관리</span>
                <span>/ 매장관리</span>
            </div>
        </div>
        <div class="d-flex">
            <a href="javascript:void(0)" onclick="Cmder('{{ $cmd }}')" class="btn btn-primary mr-1"><i class="fas fa-save fa-sm text-white-50 mr-1"></i>저장</a>
            <a href="javascript:void(0)" onclick="Cmder('delete')" class="btn btn-primary mr-1"><i class="fas fa-trash fa-sm text-white-50 mr-1"></i>삭제</a>
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
		<input type="hidden" name="cmd" id="cmd" value="{{ $cmd }}">
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
                                            <th class="required">매장코드</th>
                                            <td colspan="3">
                                                <div class="d-flex flex-column">
                                                    <div class="d-flex">
                                                        <input type="text" name="store_cd" id="store_cd" value="{{ @$store->store_cd }}" onkeydown="setDupCheckValue()" class="form-control form-control-sm w-50 mr-2" style="max-width:280px;" @if($cmd == "update") readonly @endif />
                                                        @if($cmd == "") 
                                                        <button type="button" class="btn btn-primary" onclick="checkCode()">중복체크</button>
                                                        @endif
                                                    </div>
                                                    <p id="dupcheck" class="pt-1"></p>
                                                    <input type="hidden" name="store_only" />
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
											<th class="required">매장명</th>
											<td style="width:35%;">
												<div class="form-inline">
													<input type="text" name="store_nm" id="store_nm" value="{{ @$store->store_nm }}" class="form-control form-control-sm w-100" />
												</div>
											</td>
											<th class="required">매장명(약칭)</th>
											<td style="width:35%;">
												<div class="form-inline">
													<input type="text" name="store_nm_s" id="store_nm_s" value="{{ @$store->store_nm_s }}" class="form-control form-control-sm w-100" />
												</div>
											</td>
                                        </tr>
										<tr>
											<th class="required">매장구분</th>
											<td>
												<div class="flax_box">
													<select name='store_type' class="form-control form-control-sm">
														<option value=''>전체</option>
														@foreach ($store_types as $store_type)
															<option value='{{ $store_type->code_id }}' @if(@$store->store_type == $store_type->code_id) selected @endif>{{ $store_type->code_val }}</option>
														@endforeach
													</select>
												</div>
											</td>
											<th class="required">매장종류</th>
											<td>
												<div class="flax_box">
													<select name='store_kind' class="form-control form-control-sm">
														<option value=''>전체</option>
														@foreach ($store_kinds as $store_kind)
															<option value='{{ $store_kind->code_id }}' @if(@$store->store_kind == $store_kind->code_id) selected @endif>{{ $store_kind->code_val }}</option>
														@endforeach
													</select>
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
												<div class="flax_box">
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
											<th>매니져명</th>
											<td>
												<div class="form-inline">
													<input type="text" name="manager_nm" id="manager_nm" value="{{ @$store->manager_nm }}" class="form-control form-control-sm w-100" />
												</div>
											</td>
											<th>매니져연락처</th>
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
											<th>기본수수료</th>
											<td>
												<div class="form-inline">
													<input type="text" name="fee" id="fee" value="{{ @$store->fee }}" class="form-control form-control-sm w-100" />
												</div>
											</td>
										</tr>
										<tr>
											<th>판매수수료율</th>
											<td>
												<div class="form-inline">
													<input type="text" name="sale_fee" id="sale_fee" value="{{ @$store->sale_fee }}" class="form-control form-control-sm w-100" />
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
											<th>로스인정률</th>
											<td>
												<div class="d-flex flex-column">
                                                    <div class="d-flex" style="width:100%;line-height:30px;">
    													<input type="text" name="loss_rate" id="deposit_cash" value="{{ @$store->loss_rate }}" class="form-control form-control-sm mr-1 w-50" />
                                                        %
                                                    </div>
												</div>
											</td>
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
										</tr>
										<tr>
											<th>입고확인사용여부</th>
											<td>
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

					<div class="row">
						<div class="col-12" style="padding-top:30px;font-size:18px;font-weight:bold;">+ 환경 정보</div>
					</div>

                    <div class="row">
                        <div class="col-12">
                            <div class="table-box-ty2 mobile">
                                <table class="table incont table-bordered" width="100%" cellspacing="0">
                                    <tbody>
										<tr>
											<th>관리기준</th>
											<td style="width:35%;">
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
											<th>경비관리</th>
											<td style="width:35%;">
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

<script src="https://t1.daumcdn.net/mapjsapi/bundle/postcode/prod/postcode.v2.js"></script>
<script type="text/javascript" charset="utf-8">

    function Cmder(type) {
        if(type === "add") addStorage();
        else if(type === "update") updateStorage();
        else if(type === "delete") deleteStorage();
    }

    // 창고정보 등록
    async function addStorage() {
        if(!validation('add')) return;
        if(!window.confirm("창고정보를 등록하시겠습니까?")) return;

        axios({
            url: `/store/standard/std03/add`,
            method: 'post',
            data: getFormInputData(),
        }).then(function (res) {
            if(res.data.code === 200) {
                alert(res.data.msg);
                opener.Search();
                location.href = "/store/standard/std03/show/" + res.data.data.storage_cd;;
            } else {
                console.log(res.data);
                alert("등록 중 오류가 발생했습니다.\n관리자에게 문의해주세요.");
            }
        }).catch(function (err) {
            console.log(err);
        });
    }

    // 창고정보 수정
    async function updateStorage() {
        if(!validation('update')) return;
        if(!window.confirm("창고정보를 수정하시겠습니까?")) return;

        axios({
            url: `/store/standard/std03/update`,
            method: 'put',
            data: getFormInputData(),
        }).then(function (res) {
            if(res.data.code === 200) {
                alert(res.data.msg);
                opener.Search();
                window.close();
            } else {
                console.log(res.data);
                alert("수정 중 오류가 발생했습니다.\n관리자에게 문의해주세요.");
            }
        }).catch(function (err) {
            console.log(err);
        });
    }

    // 창고정보 삭제
    async function deleteStorage() {
        if(!window.confirm("해당 창고정보를 삭제하시겠습니까?")) return;

        axios({
            url: `/store/standard/std03/delete/` + f1.storage_cd.value,
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

    // 폼 입력 데이터 반환
    function getFormInputData() {
        return {
            storage_cd: f1.storage_cd.value,
            storage_nm: f1.storage_nm.value,
            storage_nm_s: f1.storage_nm_s.value,
            zipcode: f1.zipcode.value,
            addr1: f1.addr1.value,
            addr2: f1.addr2.value,
            phone: f1.phone.value,
            fax: f1.fax.value,
            ceo: f1.ceo.value,
            use_yn: f1.use_yn.value,
            loss_yn: f1.loss_yn.value,
            stock_check_yn: f1.stock_check_yn.value,
        }
    }

    // 매장코드 중복체크
    async function checkCode() {
        const store_cd = $("[name=store_cd]").val().trim();
        if( store_cd === '' )	return alert("매장코드를 입력해주세요.");

        const response = await axios({ 
            url: `/store/standard/std02/check-code/${store_cd}`, 
            method: 'get' 
        });
        const {data: {code, msg}} = response;

        $("#dupcheck").text("* " + msg);
        $("#dupcheck").css("color", code === 200 ? "#00BB00" : "#ff0000");
        $("[name=store_only]").val(code === 200 ? "true" : "false");
    }

    // 창고코드값 변경 시 중복체크 false 변경
    function setDupCheckValue() {
        $("[name=storage_only]").val("false");
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
            // 창고코드 입력여부
            if(f1.storage_cd.value.trim() === '') {
                f1.storage_cd.focus();
                return alert("창고코드를 입력해주세요.");
            }
            
            // 중복체크여부 검사
            if($("[name='storage_only']").val() !== "true") return alert("창고코드를 중복체크해주세요.");
        }

        // 창고명칭 입력여부
        if(f1.storage_nm.value.trim() === '') {
            f1.storage_nm.focus();
            return alert("창고명칭을 입력해주세요.");
        }

        // 창고명칭(약칭) 입력여부
        if(f1.storage_nm_s.value.trim() === '') {
            f1.storage_nm_s.focus();
            return alert("창고명칭(약칭)을 입력해주세요.");
        }

        // 주소 입력여부
        if(f1.zipcode.value === '') return alert("주소를 입력해주세요.");

        return true;
    }
</script>
@stop

@extends('head_with.layouts.layout-nav')
@section('title','접수 상세 내역 수정')
@section('content')
<script>
    //공통 선언
    const order_no	= '{{@$order_no}}';
    const user_code	= '{{@$user_code}}';
</script>

<div class="show_layout py-3">

	<form method="post" name="f1">
	<input type="HIDDEN" name="order_no" value="{{ $order_no }}">
	<input type="HIDDEN" name="user_code" value="{{ $user_code }}">

		<div class="card_wrap aco_card_wrap">
			<div class="card shadow">
				<div class="card-header mb-0">
					<a href="#">접수 상세 내역</a>
				</div>
				<div class="card-body mt-1">
					<div class="row_wrap">

						<div class="row">
							<div class="col-12" style="padding-top:30px;font-size:18px;font-weight:bold;">+ 결제정보</div>
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
												<th>신청번호</th>
												<td>{{ $order_no }}</td>
												<th>이벤트정보</th>
												<td>{!! $good_name !!}</td>
											</tr>
											<tr>
												<th>결제 금액</th>
												<td>{{ number_format($price) }}</td>
												<th>영수증확인</th>
												<td style="padding:0px 10px;">
													<a href="#" onClick="receiptView('{{ $tno }}','{{ $order_no }}','{{ $price }}');" class="btn-sm btn btn-secondary">영수증 확인</a>
												</td>
											</tr>
											<tr>
												<th>결제수단</th>
												<td>신용카드</td>
												<th>결제카드</th>
												<td>{{ $card_cd }} / {{ $card_name }}</td>
											</tr>
											<tr>
												<th>승인시간</th>
												<td>{{ $app_time }}</td>
												<th>승인번호</th>
												<td>{{ $app_no }}</td>
											</tr>
											<tr>
												<th>할부정보</th>
												<td>
													@if ( $noinf == "Y")무이자@endif
													@if ( $quota != "00" && $quota != ""){{ $quota }}개월@endif
												</td>
												<th>접수상태</th>
												<td style="padding:0px 10px;">
													<div class="flax_box">
														<select name="evt_state" id="evt_state" class="form-control form-control-sm mr1" style="width:50%;">
															<option value="">:: 선택 ::</option>
															@foreach ($event_state as $event_state_code => $event_state_name)
									                        <option value='{{ $event_state_code }}' @if($evt_member['evt_state'] == $event_state_code)selected @endif>{{ $event_state_name }}</option>
									                        @endforeach
														</select>
														<a href="#" onClick="changeState('{{ $evt_member['idx'] }}');" class="btn-sm btn btn-secondary">상태변경</a>
													</div>
												</td>
											</tr>
										</tbody>
									</table>
								</div>
							</div>
						</div>

						<div class="row">
							<div class="col-12" style="padding-top:30px;font-size:18px;font-weight:bold;">+ 접수자정보</div>
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
												<td>{{ $user_code }}</td>
												<th>종류</th>
												<td>
													<div class="flax_box">
														<select name="kind" class="form-control form-control-sm">
															<option value="">:: 선택 ::</option>
															<option value="0" @if($evt_member['kind'] == "0")selected @endif>성인</option>
                                                            <option value="1" @if($evt_member['kind'] == "1")selected @endif>청소년</option>
                                                            <option value="2" @if($evt_member['kind'] == "2")selected @endif>소인</option>
														</select>
													</div>
												</td>
											</tr>
											<tr>
												<th>비밀번호</th>
												<td>
													<div class="flax_box">
														<input type="text" name="passwd" class="form-control form-control-sm" value="{{ $evt_member['passwd'] }}">
													</div>
												</td>
												<th>영문명</th>
												<td>
													<div class="flax_box">
														<input type="text" name="en_nm1" class="form-control form-control-sm" value="{{ $evt_member['en_nm1'] }}">
													</div>
												</td>
											</tr>
											<tr>
												<th>영문성</th>
												<td>
													<div class="flax_box">
														<input type="text" name="en_nm2" class="form-control form-control-sm" value="{{ $evt_member['en_nm2'] }}">
													</div>
												</td>
												<th>주소</th>
												<td>
													<div class="flax_box">
														<input type="text" name="addr1" class="form-control form-control-sm" value="{{ $evt_member['addr1'] }}">
													</div>
												</td>
											</tr>
											<tr>
												<th>우편번호</th>
												<td>
													<div class="flax_box">
														<input type="text" name="zipcode" class="form-control form-control-sm" value="{{ $evt_member['zipcode'] }}">
													</div>
												</td>
												<th>지역</th>
												<td>
													<div class="flax_box">
														<input type="text" name="area" class="form-control form-control-sm" value="{{ $evt_member['area'] }}">
													</div>
												</td>
											</tr>
											<tr>
												<th>국가</th>
												<td>
													<div class="flax_box">
														<select name="country" class="form-control form-control-sm">
															<option value="">:: 선택 ::</option>
															@foreach ($country_info as $country_code => $country_name)
									                        <option value='{{ $country_code }}' @if($evt_member['country'] == $country_code)selected @endif>{{ $country_name }}</option>
									                        @endforeach
														</select>
													</div>
												</td>
												<th>휴대폰</th>
												<td>
													<div class="flax_box">
														<input type="text" name="mobile" class="form-control form-control-sm" value="{{ $evt_member['mobile'] }}">
													</div>
												</td>
											</tr>
											<tr>
												<th>이메일</th>
												<td>
													<div class="flax_box">
														<input type="text" name="email" class="form-control form-control-sm" value="{{ $evt_member['email'] }}">
													</div>
												</td>
												<th>생년월일</th>
												<td>
													<div class="flax_box">
														<input type="text" name="birthdate" class="form-control form-control-sm" value="{{ $evt_member['birthdate'] }}">
													</div>
												</td>
											</tr>
											<tr>
												<th>성별</th>
												<td>
													<div class="flax_box">
														<select name="sex" class="form-control form-control-sm">
															<option value="">:: 선택 ::</option>
															<option value="M" @if($evt_member['sex'] == "M")selected @endif>남성</option>
															<option value="F" @if($evt_member['sex'] == "F")selected @endif>여성</option>
														</select>
													</div>
												</td>
												<th>출발그룹</th>
												<td>
													<div class="flax_box">
														<select name="group_nm" class="form-control form-control-sm">
															<option value="">:: 선택 ::</option>
															<option value="1" @if($evt_member['group_nm'] == "1")selected @endif>{{ $group_info['group_nm1'] }}</option>
															<option value="2" @if($evt_member['group_nm'] == "2")selected @endif>{{ $group_info['group_nm2'] }}</option>
														</select>
													</div>
												</td>
											</tr>
											<tr>
												<th>긴급연락처</th>
												<td>
													<div class="flax_box">
														<input type="text" name="em_phone" class="form-control form-control-sm" value="{{ $evt_member['em_phone'] }}">
													</div>
												</td>
												<th>식이제한</th>
												<td>
													<div class="flax_box">
														<select name="dietary_yn" class="form-control form-control-sm">
															<option value="">:: 선택 ::</option>
															<option value="N" @if($evt_member['dietary_yn'] == "N")selected @endif>제한 없음</option>
															<option value="Y" @if($evt_member['dietary_yn'] == "Y")selected @endif>채식 주의</option>
														</select>
													</div>
												</td>
											</tr>
											<tr>
												<th>스웨덴경험</th>
												<td>
													<div class="flax_box">
														<select name="part_cnt_sweden" class="form-control form-control-sm">
															<option value="">:: 선택 ::</option>
															<option value="Y" @if($evt_member['part_cnt_sweden'] == "Y")selected @endif>참가 경험이 있다</option>
															<option value="N" @if($evt_member['part_cnt_sweden'] == "N")selected @endif>참가 경헙이 없다</option>
														</select>
													</div>
												</td>
												<th>덴마크경험</th>
												<td>
													<div class="flax_box">
														<select name="part_cnt_denmark" class="form-control form-control-sm">
															<option value="">:: 선택 ::</option>
															<option value="Y" @if($evt_member['part_cnt_denmark'] == "Y")selected @endif>참가 경험이 있다</option>
															<option value="N" @if($evt_member['part_cnt_denmark'] == "N")selected @endif>참가 경헙이 없다</option>
														</select>
													</div>
												</td>
											</tr>
											<tr>
												<th>USA경험</th>
												<td>
													<div class="flax_box">
														<select name="part_cnt_usa" class="form-control form-control-sm">
															<option value="">:: 선택 ::</option>
															<option value="Y" @if($evt_member['part_cnt_usa'] == "Y")selected @endif>참가 경험이 있다</option>
															<option value="N" @if($evt_member['part_cnt_usa'] == "N")selected @endif>참가 경헙이 없다</option>
														</select>
													</div>
												</td>
												<th>홍콩경험</th>
												<td>
													<div class="flax_box">
														<select name="part_cnt_hongkong" class="form-control form-control-sm">
															<option value="">:: 선택 ::</option>
															<option value="Y" @if($evt_member['part_cnt_hongkong'] == "Y")selected @endif>참가 경험이 있다</option>
															<option value="N" @if($evt_member['part_cnt_hongkong'] == "N")selected @endif>참가 경헙이 없다</option>
														</select>
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
		</div>

	</form>

    <div class="resul_btn_wrap mt-3 d-block">
        <a href="javascript:;" class="btn btn-sm btn-primary edit-btn">저장</a>
        <a href="javascript:;" class="btn btn-sm btn-secondary" onclick="window.close()">닫기</a>
    </div>

</div>


<script>
 /* 신용카드 영수증 */
/* 실결제시 : "https://admin8.kcp.co.kr/assist/bill.BillActionNew.do?cmd=card_bill&tno=" */
/* 테스트시 : "https://testadmin8.kcp.co.kr/assist/bill.BillActionNew.do?cmd=card_bill&tno=" */
function receiptView( tno, ordr_idxx, amount )
{
	receiptWin	= "https://admin8.kcp.co.kr/assist/bill.BillActionNew.do?cmd=card_bill&tno=";
	receiptWin	+= tno + "&";
	receiptWin	+= "order_no=" + ordr_idxx + "&";
	receiptWin	+= "trade_mony=" + amount ;

	window.open(receiptWin, "", "width=455, height=815");
}

function changeState(evt_mem_idx)
{
	if( document.f1.evt_state.selectedIndex == 0 )
	{
		alert("접수상태는 반드시 선택해야 합니다.");
		ff.evt_state.focus();

		return false;
	}

	if( confirm("접수상태를 변경 하시겠습니까?") )
	{
		evt_state	= document.f1.evt_state[document.f1.evt_state.selectedIndex].value;

		$.ajax({
            async: true,
            type: 'put',
            url: '/head/classic/cls03',
            data: {
                "evt_mem_idxs[]" : evt_mem_idx,
                s1_evt_state : evt_state
            },
            success: function (data) {
                if( data.code == "200" )
                {
                    alert("접수상태를 변경하였습니다.");
                }
                else
                {
                    alert("접수상태 변경을 실패하였습니다.");
                }
                Search();
            },
            error: function(request, status, error) {
                console.log("error")
            }
        });
	}
}

function Validate() {
	const ff = document.f1;

	if( ff.evt_state.selectedIndex == 0 )
	{
		alert("접수상태는 반드시 선택해야 합니다.");
		ff.evt_state.focus();

		return false;
	}

	if( ff.kind.selectedIndex == 0 )
	{
		alert("종류는 반드시 선택해야 합니다.");
		ff.kind.focus();

		return false;
	}

	if( ff.passwd.value == "" )
	{
		alert("비밀번호는 반드시 입력해야 합니다.");
		ff.passwd.focus();

		return false;
	}

	if( ff.en_nm1.value == "" )
	{
		alert("영문명은 반드시 입력해야 합니다.");
		ff.en_nm1.focus();

		return false;
	}

	if( ff.en_nm2.value == "" )
	{
		alert("영문성은 반드시 입력해야 합니다.");
		ff.en_nm2.focus();

		return false;
	}

	if( ff.addr1.value == "" )
	{
		alert("주소는 반드시 입력해야 합니다.");
		ff.addr1.focus();

		return false;
	}

	if( ff.zipcode.value == "" )
	{
		alert("우편번호는 반드시 입력해야 합니다.");
		ff.zipcode.focus();

		return false;
	}

	if( ff.area.value == "" )
	{
		alert("지역은 반드시 입력해야 합니다.");
		ff.area.focus();

		return false;
	}

	if( ff.country.selectedIndex == 0 )
	{
		alert("국가는 반드시 선택해야 합니다.");
		ff.country.focus();

		return false;
	}

	if( ff.mobile.value == "" )
	{
		alert("휴대폰 번호는 반드시 입력해야 합니다.");
		ff.mobile.focus();

		return false;
	}

	if( ff.email.value == "" )
	{
		alert("이메일은 반드시 입력해야 합니다.");
		ff.email.focus();

		return false;
	}

	if( ff.birthdate.value == "" )
	{
		alert("생년월일은 반드시 입력해야 합니다.");
		ff.birthdate.focus();

		return false;
	}

	if( ff.sex.selectedIndex == 0 )
	{
		alert("성별은 반드시 선택해야 합니다.");
		ff.sex.focus();

		return false;
	}

	if( ff.group_nm.selectedIndex == 0 )
	{
		alert("출발그룹은 반드시 선택해야 합니다.");
		ff.group_nm.focus();

		return false;
	}

	if( ff.em_phone.value == "" )
	{
		alert("긴급연락처는 반드시 입력해야 합니다.");
		ff.em_phone.focus();

		return false;
	}

	if( ff.dietary_yn.selectedIndex == 0 )
	{
		alert("식이제한은 반드시 선택해야 합니다.");
		ff.dietary_yn.focus();

		return false;
	}

	if( ff.part_cnt_sweden.selectedIndex == 0 )
	{
		alert("스웨덴 클래식 참가 경험유무는 반드시 선택해야 합니다.");
		ff.part_cnt_sweden.focus();

		return false;
	}

	if( ff.part_cnt_denmark.selectedIndex == 0 )
	{
		alert("덴마크 클래식 참가 경험유무는 반드시 선택해야 합니다.");
		ff.part_cnt_denmark.focus();

		return false;
	}

	if( ff.part_cnt_usa.selectedIndex == 0 )
	{
		alert("USA 클래식 참가 경험유무는 반드시 선택해야 합니다.");
		ff.part_cnt_usa.focus();

		return false;
	}

	if( ff.part_cnt_hongkong.selectedIndex == 0 )
	{
		alert("홍콩 클래식 참가 경험유무는 반드시 선택해야 합니다.");
		ff.part_cnt_honkong.focus();

		return false;
	}

	return true;
}

// 저장
$('.edit-btn').click(function(e){

    if (Validate() === false) return;

    if( confirm("변경된 내용을 수정하시겠습니까?") )
    {
        const data = $('form[name="f1"]').serialize();

        $.ajax({
            async: true,
            type: 'put',
            url: '/head/classic/cls03/show/' + order_no + '/' + user_code,
            data: data,
            success: function (data) {
                alert("수정되었습니다.");
                location.reload();
            },
            error: function(request, status, error) {
                console.log("error")
            }
        });

    }

});

</script>
@stop

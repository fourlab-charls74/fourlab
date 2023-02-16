@extends('head_with.layouts.layout-nav')
@section('title','접수 상세 내역')
@section('content')

<div class="show_layout py-3">
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
											<td>
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
											<td colspan="3">
												@if ( $noinf == "Y")무이자@endif
												@if ( $quota != "00" && $quota != ""){{ $quota }}개월@endif
											</td>
										</tr>
									</tbody>
								</table>
							</div>
						</div>
					</div>
					@foreach($evt_member as $key => $member_info)
					<div class="row">
						<div class="col-12" style="padding-top:30px;font-size:18px;font-weight:bold;">+ 접수자 정보 {{ $loop->index + 1 }}</div>
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
											<td>{{ $member_info['user_code'] }}</td>
											<th>종류</th>
											<td>
												{{ $member_info['kind'] }}
											</td>
										</tr>
										<tr>
											<th>접수상태</th>
											<td>
												{{ $member_info['evt_state']}}
											</td>
											<th>비밀번호</th>
											<td>{{ $member_info['passwd'] }}</td>
										</tr>
										<tr>
											<th>영문명</th>
											<td>{{ $member_info['en_nm1'] }} {{ $member_info['en_nm2'] }}</td>
											<th>우편번호</th>
											<td>{{ $member_info['zipcode'] }}</td>
										</tr>
										<tr>
											<th>지역</th>
											<td>{{ $member_info['area'] }}</td>
											<th>국가</th>
											<td>
                                                {{ $member_info['country'] }}
											</td>
										</tr>
										<tr>
											<th>휴대폰</th>
											<td>{{ $member_info['mobile'] }}</td>
											<th>이메일</th>
											<td>{{ $member_info['email'] }}</td>
										</tr>
										<tr>
											<th>생년월일</th>
											<td>{{ $member_info['birthdate'] }}</td>
											<th>성별</th>
											<td>{{ $member_info['sex'] }}</td>
										</tr>
										<tr>
											<th>출발그룹</th>
											<td>
												[{{ $member_info['group_nm'] }}]
											</td>
											<th>긴급연락처</th>
											<td>{{ $member_info['em_phone'] }}</td>
										</tr>
										<tr>
											<th>식이제한</th>
											<td>
												@if( $member_info['dietary_yn'] == "Y") 채식 주의 @else 제한 없음 @endif
											</td>
											<th>스웨덴경험</th>
											<td>
												@if( $member_info['part_cnt_sweden'] == "Y") 참가 경험이 있다. @else 참가 경험이 없다. @endif
											</td>
										</tr>
										<tr>
											<th>덴마크경험</th>
											<td>
												@if( $member_info['part_cnt_denmark'] == "Y") 참가 경험이 있다. @else 참가 경험이 없다. @endif
											</td>
											<th>USA경험</th>
											<td>
												@if( $member_info['part_cnt_usa'] == "Y") 참가 경험이 있다. @else 참가 경험이 없다. @endif
											</td>
										</tr>
										<tr>
											<th>홍콩경험</th>
											<td colspan="3">
												@if( $member_info['part_cnt_hongkong'] == "Y") 참가 경험이 있다. @else 참가 경험이 없다. @endif
											</td>
										</tr>
									</tbody>
								</table>
							</div>
						</div>
					</div>
					@endforeach
				</div>
			</div>
		</div>
	</div>

    <div class="resul_btn_wrap mt-3 d-block">
        <a href="javascript:;" class="btn btn-sm btn-primary" onclick="window.close()">닫기</a>
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
</script>
@stop

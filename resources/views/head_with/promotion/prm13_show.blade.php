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

					<?php
						$mem_cnt	= 0;
					?>
					@foreach($evt_mem as $key => $mem_list)
					<?php
						$mem_cnt	+= 1;
					?>
					<div class="row">
						<div class="col-12" style="padding-top:30px;font-size:18px;font-weight:bold;">+ 접수자 정보 {{ number_format($mem_cnt) }}</div>
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
											<td>{{ $mem_list->user_code }}</td>
											<th>종류</th>
											<td>
												@if($mem_list->kind == "0")성인@elseif($mem_list->kind == "1")청소년@elseif( $mem_list->kind == "2")소인@endif
											</td>
										</tr>
										<tr>
											<th>접수상태</th>
											<td>
												@if($mem_list->evt_state == "1") 입금예정 @endif
												@if($mem_list->evt_state == "5") 접수후보 @endif
												@if($mem_list->evt_state == "9") 후보결제대기 @endif
												@if($mem_list->evt_state == "10") 접수완료 @endif
												@if($mem_list->evt_state == "20") 확정대기 @endif
												@if($mem_list->evt_state == "30") 확정완료 @endif
												@if($mem_list->evt_state == "-10") 결제오류 @endif
												@if($mem_list->evt_state == "-00") 신청취소 @endif
											</td>
											<th>비밀번호</th>
											<td>{{ $mem_list->passwd }}</td>
										</tr>
										<tr>
											<th>영문명</th>
											<td>{{ $mem_list->en_nm1 }} {{ $mem_list->en_nm2 }}</td>
											<th>우편번호</th>
											<td>{{ $mem_list->zipcode }}</td>
										</tr>
										<tr>
											<th>지역</th>
											<td>{{ $mem_list->area }}</td>
											<th>국가</th>
											<td>
												@if($mem_list->country == "39178") South Korea @endif
												@if($mem_list->country == "39004") Sweden @endif
												@if($mem_list->country == "39078") HongKong @endif
												@if($mem_list->country == "39005") Denmark @endif
												@if($mem_list->country == "39006") Norway @endif
												@if($mem_list->country == "39007") Andorra @endif
												@if($mem_list->country == "39008") Angola @endif
												@if($mem_list->country == "39009") Anguilla @endif
												@if($mem_list->country == "39010") Antigua &amp; Barbuda @endif
												@if($mem_list->country == "39011") Argentina @endif
												@if($mem_list->country == "39012") Armenia @endif
												@if($mem_list->country == "39013") Aruba @endif
												@if($mem_list->country == "39014") Australia @endif
												@if($mem_list->country == "39015") Azerbaijan @endif
												@if($mem_list->country == "39016") Bahamas @endif
												@if($mem_list->country == "39017") Bahrain @endif
												@if($mem_list->country == "39018") Bangladesh @endif
												@if($mem_list->country == "39019") Barbados @endif
												@if($mem_list->country == "39020") Belgium @endif
												@if($mem_list->country == "39021") Belize @endif
												@if($mem_list->country == "39022") Benin @endif
												@if($mem_list->country == "39023") Bermuda @endif
												@if($mem_list->country == "39024") Bhutan @endif
												@if($mem_list->country == "39025") Bolivia @endif
												@if($mem_list->country == "39026") Bosnia and Herzegovina @endif
												@if($mem_list->country == "39027") Botswana @endif
												@if($mem_list->country == "39028") Brazil @endif
												@if($mem_list->country == "39029") British Virgin Islands @endif
												@if($mem_list->country == "39030") Brunei @endif
												@if($mem_list->country == "39031") Bulgaria @endif
												@if($mem_list->country == "39032") Burkina Faso @endif
												@if($mem_list->country == "39033") Burma @endif
												@if($mem_list->country == "39034") Burundi @endif
												@if($mem_list->country == "39035") Cayman Islands @endif
												@if($mem_list->country == "39036") Central African Republic @endif
												@if($mem_list->country == "39037") Chile @endif
												@if($mem_list->country == "39038") Colombia @endif
												@if($mem_list->country == "39039") Comorerna @endif
												@if($mem_list->country == "39040") Cook Islands @endif
												@if($mem_list->country == "39041") Costa Rica @endif
												@if($mem_list->country == "39042") Cypros @endif
												@if($mem_list->country == "39043") Dominica @endif
												@if($mem_list->country == "39044") Dominican Republic @endif
												@if($mem_list->country == "39045") Ecuador @endif
												@if($mem_list->country == "39046") Egypt @endif
												@if($mem_list->country == "39047") Equatorial Guinea @endif
												@if($mem_list->country == "39048") El Salvador @endif
												@if($mem_list->country == "39049") Ivory Coast (Cote d'Ivoire)  @endif
												@if($mem_list->country == "39050") England @endif
												@if($mem_list->country == "39051") Eritrea @endif
												@if($mem_list->country == "39052") Estonia @endif
												@if($mem_list->country == "39053") Ethiopia @endif
												@if($mem_list->country == "39054") Falkland Is. (Malvinas)  @endif
												@if($mem_list->country == "39055") Fiji @endif
												@if($mem_list->country == "39056") Philippines @endif
												@if($mem_list->country == "39057") Finland @endif
												@if($mem_list->country == "39058") France @endif
												@if($mem_list->country == "39059") French Guiana @endif
												@if($mem_list->country == "39060") French Polynesia @endif
												@if($mem_list->country == "39061") Faroe Islands @endif
												@if($mem_list->country == "39062") Gabon @endif
												@if($mem_list->country == "39063") Gambia @endif
												@if($mem_list->country == "39064") Georgia @endif
												@if($mem_list->country == "39065") Ghana @endif
												@if($mem_list->country == "39066") Gibraltar @endif
												@if($mem_list->country == "39067") Greece @endif
												@if($mem_list->country == "39068") Grenada @endif
												@if($mem_list->country == "39069") Greenland @endif
												@if($mem_list->country == "39070") Guadeloupe @endif
												@if($mem_list->country == "39071") Guam @endif
												@if($mem_list->country == "39072") Guatemala @endif
												@if($mem_list->country == "39073") Guinea @endif
												@if($mem_list->country == "39074") Guinea-Bissau @endif
												@if($mem_list->country == "39075") Guyana @endif
												@if($mem_list->country == "39076") Haiti @endif
												@if($mem_list->country == "39077") Honduras @endif
												@if($mem_list->country == "39078") Hongkong @endif
												@if($mem_list->country == "39079") India @endif
												@if($mem_list->country == "39080") Indonesia @endif
												@if($mem_list->country == "39081") Irac @endif
												@if($mem_list->country == "39082") Iran @endif
												@if($mem_list->country == "39083") Ireland @endif
												@if($mem_list->country == "39084") Island @endif
												@if($mem_list->country == "39085") Isle of Man @endif
												@if($mem_list->country == "39086") Israel @endif
												@if($mem_list->country == "39087") Italy @endif
												@if($mem_list->country == "39088") Jamaica @endif
												@if($mem_list->country == "39089") Japan @endif
												@if($mem_list->country == "39090") Jemen @endif
												@if($mem_list->country == "39091") Jordania @endif
												@if($mem_list->country == "39092") Cambodia @endif
												@if($mem_list->country == "39093") Cameroon @endif
												@if($mem_list->country == "39094") Canada @endif
												@if($mem_list->country == "39095") Kenya @endif
												@if($mem_list->country == "39096") China @endif
												@if($mem_list->country == "39097") Kiribati @endif
												@if($mem_list->country == "39098") Congo @endif
												@if($mem_list->country == "39099") Croatia @endif
												@if($mem_list->country == "39100") Cuba @endif
												@if($mem_list->country == "39101") Kuwait @endif
												@if($mem_list->country == "39102") Laos @endif
												@if($mem_list->country == "39103") Lesotho @endif
												@if($mem_list->country == "39104") Latvia @endif
												@if($mem_list->country == "39105") Lebanon @endif
												@if($mem_list->country == "39106") Liberia @endif
												@if($mem_list->country == "39107") Libya @endif
												@if($mem_list->country == "39108") Liechtenstein @endif
												@if($mem_list->country == "39109") Lithuania @endif
												@if($mem_list->country == "39110") Luxemburg @endif
												@if($mem_list->country == "39111") Madagascar @endif
												@if($mem_list->country == "39112") Maced @endif
												@if($mem_list->country == "39113") Malawi @endif
												@if($mem_list->country == "39114") Malaysia @endif
												@if($mem_list->country == "39115") Maldives @endif
												@if($mem_list->country == "39116") Mali @endif
												@if($mem_list->country == "39117") Malta @endif
												@if($mem_list->country == "39118") Marocko @endif
												@if($mem_list->country == "39119") Marshall Islands @endif
												@if($mem_list->country == "39120") Martinique @endif
												@if($mem_list->country == "39121") Mauritius @endif
												@if($mem_list->country == "39122") Mayotte @endif
												@if($mem_list->country == "39123") Mexico @endif
												@if($mem_list->country == "39124") Micronesia  @endif
												@if($mem_list->country == "39125") Mozambique @endif
												@if($mem_list->country == "39126") Moldova @endif
												@if($mem_list->country == "39127") Monaco @endif
												@if($mem_list->country == "39128") Mongolia @endif
												@if($mem_list->country == "39129") Namibia @endif
												@if($mem_list->country == "39130") Nauru @endif
												@if($mem_list->country == "39131") Netherlands @endif
												@if($mem_list->country == "39132") Netherlands Antilles @endif
												@if($mem_list->country == "39133") Nepal @endif
												@if($mem_list->country == "39134") Nicaragua @endif
												@if($mem_list->country == "39135") Niger @endif
												@if($mem_list->country == "39136") Nigeria @endif
												@if($mem_list->country == "39137") North Korea @endif
												@if($mem_list->country == "39138") Norway @endif
												@if($mem_list->country == "39139") New Zealand @endif
												@if($mem_list->country == "39140") Oman @endif
												@if($mem_list->country == "39141") Pakistan @endif
												@if($mem_list->country == "39142") Panama @endif
												@if($mem_list->country == "39143") Papua New Guinea @endif
												@if($mem_list->country == "39144") Paraguay @endif
												@if($mem_list->country == "39145") Peru @endif
												@if($mem_list->country == "39146") Pitcairn Island @endif
												@if($mem_list->country == "39147") Poland @endif
												@if($mem_list->country == "39148") Portugal @endif
												@if($mem_list->country == "39149") Puerto Rico @endif
												@if($mem_list->country == "39150") Reunion @endif
												@if($mem_list->country == "39151") Romania @endif
												@if($mem_list->country == "39152") Rwanda @endif
												@if($mem_list->country == "39153") Russia @endif
												@if($mem_list->country == "39154") Saint Christopher och Nevis @endif
												@if($mem_list->country == "39155") Saint Helena @endif
												@if($mem_list->country == "39156") Saint Lucia @endif
												@if($mem_list->country == "39157") Saint Vincent och Grenadinerna @endif
												@if($mem_list->country == "39158") Saint-Pierre-et-Miquelon @endif
												@if($mem_list->country == "39159") Salomonoarna @endif
												@if($mem_list->country == "39160") Samoa @endif
												@if($mem_list->country == "39161") Soo Tomo och Principe @endif
												@if($mem_list->country == "39162") Saudi Arabia @endif
												@if($mem_list->country == "39163") Schweiz @endif
												@if($mem_list->country == "39164") Senegal @endif
												@if($mem_list->country == "39165") Serbia @endif
												@if($mem_list->country == "39166") Sierra Leone @endif
												@if($mem_list->country == "39167") Singapore @endif
												@if($mem_list->country == "39168") Scottland @endif
												@if($mem_list->country == "39169") Slovakia @endif
												@if($mem_list->country == "39170") Slovenia @endif
												@if($mem_list->country == "39171") Spain @endif
												@if($mem_list->country == "39172") Sri Lanka @endif
												@if($mem_list->country == "39173") Great Britain @endif
												@if($mem_list->country == "39174") Sudan @endif
												@if($mem_list->country == "39175") Surinam @endif
												@if($mem_list->country == "39176") Swaziland @endif
												@if($mem_list->country == "39177") South Africa @endif
												@if($mem_list->country == "39178") South Korea @endif
												@if($mem_list->country == "39179") Syria @endif
												@if($mem_list->country == "39180") Taiwan @endif
												@if($mem_list->country == "39181") Tanzania @endif
												@if($mem_list->country == "39182") Tchad @endif
												@if($mem_list->country == "39183") Thailand @endif
												@if($mem_list->country == "39184") Czech Republic @endif
												@if($mem_list->country == "39185") Togo @endif
												@if($mem_list->country == "39186") Tonga @endif
												@if($mem_list->country == "39187") Trinidad &amp; Tobago @endif
												@if($mem_list->country == "39188") Tunisia @endif
												@if($mem_list->country == "39189") Turkey @endif
												@if($mem_list->country == "39190") Turkmenistan @endif
												@if($mem_list->country == "39191") Turks and Caicos Is @endif
												@if($mem_list->country == "39192") Tuvalu @endif
												@if($mem_list->country == "39193") Germany @endif
												@if($mem_list->country == "39194") Uganda @endif
												@if($mem_list->country == "39195") Ukraine @endif
												@if($mem_list->country == "39196") Hungaria @endif
												@if($mem_list->country == "39197") Uruguay @endif
												@if($mem_list->country == "39198") USA @endif
												@if($mem_list->country == "39199") Uzbekistan @endif
												@if($mem_list->country == "39200") Wales @endif
												@if($mem_list->country == "39201") Wallis and Futuna @endif
												@if($mem_list->country == "39202") Vanuatu @endif
												@if($mem_list->country == "39203") Venezuela @endif
												@if($mem_list->country == "39204") Vietnam @endif
												@if($mem_list->country == "39205") Belarus @endif
												@if($mem_list->country == "39206") Zambia @endif
												@if($mem_list->country == "39207") Zimbabwe @endif
												@if($mem_list->country == "39208") Austria @endif
												@if($mem_list->country == "39209") East Timor @endif
											</td>
										</tr>
										<tr>
											<th>휴대폰</th>
											<td>{{ $mem_list->mobile }}</td>
											<th>이메일</th>
											<td>{{ $mem_list->email }}</td>
										</tr>
										<tr>
											<th>생년월일</th>
											<td>{{ $mem_list->birthdate }}</td>
											<th>성별</th>
											<td>{{ $mem_list->sex }}</td>
										</tr>
										<tr>
											<th>출발그룹</th>
											<td>
												@if( $mem_list->group_nm == "1"){{ $group_nm1 }}@elseif($mem_list->group_nm == "2"){{ $group_nm2}}@endif
											</td>
											<th>긴급연락처</th>
											<td>{{ $mem_list->em_phone }}</td>
										</tr>
										<tr>
											<th>식이제한</th>
											<td>
												@if( $mem_list->dietary_yn == "Y") 채식 주의 @else 제한 없음 @endif
											</td>
											<th>스웨덴경험</th>
											<td>
												@if( $mem_list->part_cnt_sweden == "Y") 참가 경험이 있다. @else 참가 경험이 없다. @endif
											</td>
										</tr>
										<tr>
											<th>덴마크경험</th>
											<td>
												@if( $mem_list->part_cnt_denmark == "Y") 참가 경험이 있다. @else 참가 경험이 없다. @endif
											</td>
											<th>USA경험</th>
											<td>
												@if( $mem_list->part_cnt_usa == "Y") 참가 경험이 있다. @else 참가 경험이 없다. @endif
											</td>
										</tr>
										<tr>
											<th>홍콩경험</th>
											<td colspan="3">
												@if( $mem_list->part_cnt_hongkong == "Y") 참가 경험이 있다. @else 참가 경험이 없다. @endif
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

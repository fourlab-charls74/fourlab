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
															<option value="">----</option>
															<option value="1"	@if( $evt_mem['ord_state'] == "1") selected @endif>입금예정</option>
															<option value="5"	@if( $evt_mem['ord_state'] == "5") selected @endif>접수후보</option>
															<option value="9"	@if( $evt_mem['ord_state'] == "9") selected @endif>후보결제대기</option>
															<option value="10"	@if( $evt_mem['ord_state'] == "10") selected @endif>접수완료</option>
															<option value="20"	@if( $evt_mem['ord_state'] == "20") selected @endif>확정대기</option>
															<option value="30"	@if( $evt_mem['ord_state'] == "30") selected @endif>확정완료</option>
															<option value="-10"	@if( $evt_mem['ord_state'] == "-10") selected @endif>결제오류</option>
															<option value="-20"	@if( $evt_mem['ord_state'] == "-00") selected @endif>신청취소</option>
														</select>
														<a href="#" onClick="changeState('{{ $evt_mem['idx'] }}');" class="btn-sm btn btn-secondary">상태변경</a>
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
												<td>{{ $evt_mem['user_code'] }}</td>
												<th>종류</th>
												<td>
													<div class="flax_box">
														<select name="kind" class="form-control form-control-sm">
															<option value="">---</option>
															<option value="0" @if($evt_mem['kind'] == "0")selected @endif>성인</option>
															<option value="1" @if($evt_mem['kind'] == "1")selected @endif>청소년</option>
															<option value="2" @if($evt_mem['kind'] == "2")selected @endif>소인</option>
														</select>
													</div>
												</td>
											</tr>
											<tr>
												<th>비밀번호</th>
												<td>
													<div class="flax_box">
														<input type="text" name="passwd" class="form-control form-control-sm" value="{{ $evt_mem['passwd'] }}">
													</div>
												</td>
												<th>영문명</th>
												<td>
													<div class="flax_box">
														<input type="text" name="en_nm1" class="form-control form-control-sm" value="{{ $evt_mem['en_nm1'] }}">
													</div>
												</td>
											</tr>
											<tr>
												<th>영문성</th>
												<td>
													<div class="flax_box">
														<input type="text" name="en_nm2" class="form-control form-control-sm" value="{{ $evt_mem['en_nm2'] }}">
													</div>
												</td>
												<th>주소</th>
												<td>
													<div class="flax_box">
														<input type="text" name="addr1" class="form-control form-control-sm" value="{{ $evt_mem['addr1'] }}">
													</div>
												</td>
											</tr>
											<tr>
												<th>우편번호</th>
												<td>
													<div class="flax_box">
														<input type="text" name="zipcode" class="form-control form-control-sm" value="{{ $evt_mem['zipcode'] }}">
													</div>
												</td>
												<th>지역</th>
												<td>
													<div class="flax_box">
														<input type="text" name="area" class="form-control form-control-sm" value="{{ $evt_mem['area'] }}">
													</div>
												</td>
											</tr>
											<tr>
												<th>국가</th>
												<td>
													<div class="flax_box">
														<select name="country" class="form-control form-control-sm">
															<option value="">국가</option>
															<option value="39178" @if( $evt_mem['country'] == "39178" ) selected @endif>South Korea</option>
															<option value="39004" @if( $evt_mem['country'] == "39004" ) selected @endif>Sweden</option>
															<option value="39078" @if( $evt_mem['country'] == "39078" ) selected @endif>HongKong</option>
															<option value="39005" @if( $evt_mem['country'] == "39005" ) selected @endif>Denmark</option>
															<option value="39006" @if( $evt_mem['country'] == "39006" ) selected @endif>Norway</option>
															<option value="39007" @if( $evt_mem['country'] == "39007" ) selected @endif>Andorra</option>
															<option value="39008" @if( $evt_mem['country'] == "39008" ) selected @endif>Angola</option>
															<option value="39009" @if( $evt_mem['country'] == "39009" ) selected @endif>Anguilla</option>
															<option value="39010" @if( $evt_mem['country'] == "39010" ) selected @endif>Antigua &amp; Barbuda</option>
															<option value="39011" @if( $evt_mem['country'] == "39011" ) selected @endif>Argentina</option>
															<option value="39012" @if( $evt_mem['country'] == "39012" ) selected @endif>Armenia</option>
															<option value="39013" @if( $evt_mem['country'] == "39013" ) selected @endif>Aruba</option>
															<option value="39014" @if( $evt_mem['country'] == "39014" ) selected @endif>Australia</option>
															<option value="39015" @if( $evt_mem['country'] == "39015" ) selected @endif>Azerbaijan</option>
															<option value="39016" @if( $evt_mem['country'] == "39016" ) selected @endif>Bahamas</option>
															<option value="39017" @if( $evt_mem['country'] == "39017" ) selected @endif>Bahrain</option>
															<option value="39018" @if( $evt_mem['country'] == "39018" ) selected @endif>Bangladesh</option>
															<option value="39019" @if( $evt_mem['country'] == "39019" ) selected @endif>Barbados</option>
															<option value="39020" @if( $evt_mem['country'] == "39020" ) selected @endif>Belgium</option>
															<option value="39021" @if( $evt_mem['country'] == "39021" ) selected @endif>Belize</option>
															<option value="39022" @if( $evt_mem['country'] == "39022" ) selected @endif>Benin</option>
															<option value="39023" @if( $evt_mem['country'] == "39023" ) selected @endif>Bermuda</option>
															<option value="39024" @if( $evt_mem['country'] == "39024" ) selected @endif>Bhutan</option>
															<option value="39025" @if( $evt_mem['country'] == "39025" ) selected @endif>Bolivia</option>
															<option value="39026" @if( $evt_mem['country'] == "39026" ) selected @endif>Bosnia and Herzegovina</option>
															<option value="39027" @if( $evt_mem['country'] == "39027" ) selected @endif>Botswana</option>
															<option value="39028" @if( $evt_mem['country'] == "39028" ) selected @endif>Brazil</option>
															<option value="39029" @if( $evt_mem['country'] == "39029" ) selected @endif>British Virgin Islands</option>
															<option value="39030" @if( $evt_mem['country'] == "39030" ) selected @endif>Brunei</option>
															<option value="39031" @if( $evt_mem['country'] == "39031" ) selected @endif>Bulgaria</option>
															<option value="39032" @if( $evt_mem['country'] == "39032" ) selected @endif>Burkina Faso</option>
															<option value="39033" @if( $evt_mem['country'] == "39033" ) selected @endif>Burma</option>
															<option value="39034" @if( $evt_mem['country'] == "39034" ) selected @endif>Burundi</option>
															<option value="39035" @if( $evt_mem['country'] == "39035" ) selected @endif>Cayman Islands</option>
															<option value="39036" @if( $evt_mem['country'] == "39036" ) selected @endif>Central African Republic</option>
															<option value="39037" @if( $evt_mem['country'] == "39037" ) selected @endif>Chile</option>
															<option value="39038" @if( $evt_mem['country'] == "39038" ) selected @endif>Colombia</option>
															<option value="39039" @if( $evt_mem['country'] == "39039" ) selected @endif>Comorerna</option>
															<option value="39040" @if( $evt_mem['country'] == "39040" ) selected @endif>Cook Islands</option>
															<option value="39041" @if( $evt_mem['country'] == "39041" ) selected @endif>Costa Rica</option>
															<option value="39042" @if( $evt_mem['country'] == "39042" ) selected @endif>Cypros</option>
															<option value="39043" @if( $evt_mem['country'] == "39043" ) selected @endif>Dominica</option>
															<option value="39044" @if( $evt_mem['country'] == "39044" ) selected @endif>Dominican Republic</option>
															<option value="39045" @if( $evt_mem['country'] == "39045" ) selected @endif>Ecuador</option>
															<option value="39046" @if( $evt_mem['country'] == "39046" ) selected @endif>Egypt</option>
															<option value="39047" @if( $evt_mem['country'] == "39047" ) selected @endif>Equatorial Guinea</option>
															<option value="39048" @if( $evt_mem['country'] == "39048" ) selected @endif>El Salvador</option>
															<option value="39049" @if( $evt_mem['country'] == "39049" ) selected @endif>Ivory Coast (Cote d'Ivoire)</option>
															<option value="39050" @if( $evt_mem['country'] == "39050" ) selected @endif>England</option>
															<option value="39051" @if( $evt_mem['country'] == "39051" ) selected @endif>Eritrea</option>
															<option value="39052" @if( $evt_mem['country'] == "39052" ) selected @endif>Estonia</option>
															<option value="39053" @if( $evt_mem['country'] == "39053" ) selected @endif>Ethiopia</option>
															<option value="39054" @if( $evt_mem['country'] == "39054" ) selected @endif>Falkland Is. (Malvinas)</option>
															<option value="39055" @if( $evt_mem['country'] == "39055" ) selected @endif>Fiji</option>
															<option value="39056" @if( $evt_mem['country'] == "39056" ) selected @endif>Philippines</option>
															<option value="39057" @if( $evt_mem['country'] == "39057" ) selected @endif>Finland</option>
															<option value="39058" @if( $evt_mem['country'] == "39058" ) selected @endif>France</option>
															<option value="39059" @if( $evt_mem['country'] == "39059" ) selected @endif>French Guiana</option>
															<option value="39060" @if( $evt_mem['country'] == "39060" ) selected @endif>French Polynesia</option>
															<option value="39061" @if( $evt_mem['country'] == "39061" ) selected @endif>Faroe Islands</option>
															<option value="39062" @if( $evt_mem['country'] == "39062" ) selected @endif>Gabon</option>
															<option value="39063" @if( $evt_mem['country'] == "39063" ) selected @endif>Gambia</option>
															<option value="39064" @if( $evt_mem['country'] == "39064" ) selected @endif>Georgia</option>
															<option value="39065" @if( $evt_mem['country'] == "39065" ) selected @endif>Ghana</option>
															<option value="39066" @if( $evt_mem['country'] == "39066" ) selected @endif>Gibraltar</option>
															<option value="39067" @if( $evt_mem['country'] == "39067" ) selected @endif>Greece</option>
															<option value="39068" @if( $evt_mem['country'] == "39068" ) selected @endif>Grenada</option>
															<option value="39069" @if( $evt_mem['country'] == "39069" ) selected @endif>Greenland</option>
															<option value="39070" @if( $evt_mem['country'] == "39070" ) selected @endif>Guadeloupe</option>
															<option value="39071" @if( $evt_mem['country'] == "39071" ) selected @endif>Guam</option>
															<option value="39072" @if( $evt_mem['country'] == "39072" ) selected @endif>Guatemala</option>
															<option value="39073" @if( $evt_mem['country'] == "39073" ) selected @endif>Guinea</option>
															<option value="39074" @if( $evt_mem['country'] == "39074" ) selected @endif>Guinea-Bissau</option>
															<option value="39075" @if( $evt_mem['country'] == "39075" ) selected @endif>Guyana</option>
															<option value="39076" @if( $evt_mem['country'] == "39076" ) selected @endif>Haiti</option>
															<option value="39077" @if( $evt_mem['country'] == "39077" ) selected @endif>Honduras</option>
															<option value="39078" @if( $evt_mem['country'] == "39078" ) selected @endif>Hongkong</option>
															<option value="39079" @if( $evt_mem['country'] == "39079" ) selected @endif>India</option>
															<option value="39080" @if( $evt_mem['country'] == "39080" ) selected @endif>Indonesia</option>
															<option value="39081" @if( $evt_mem['country'] == "39081" ) selected @endif>Irac</option>
															<option value="39082" @if( $evt_mem['country'] == "39082" ) selected @endif>Iran</option>
															<option value="39083" @if( $evt_mem['country'] == "39083" ) selected @endif>Ireland</option>
															<option value="39084" @if( $evt_mem['country'] == "39084" ) selected @endif>Island</option>
															<option value="39085" @if( $evt_mem['country'] == "39085" ) selected @endif>Isle of Man</option>
															<option value="39086" @if( $evt_mem['country'] == "39086" ) selected @endif>Israel</option>
															<option value="39087" @if( $evt_mem['country'] == "39087" ) selected @endif>Italy</option>
															<option value="39088" @if( $evt_mem['country'] == "39088" ) selected @endif>Jamaica</option>
															<option value="39089" @if( $evt_mem['country'] == "39089" ) selected @endif>Japan</option>
															<option value="39090" @if( $evt_mem['country'] == "39090" ) selected @endif>Jemen</option>
															<option value="39091" @if( $evt_mem['country'] == "39091" ) selected @endif>Jordania</option>
															<option value="39092" @if( $evt_mem['country'] == "39092" ) selected @endif>Cambodia</option>
															<option value="39093" @if( $evt_mem['country'] == "39093" ) selected @endif>Cameroon</option>
															<option value="39094" @if( $evt_mem['country'] == "39094" ) selected @endif>Canada</option>
															<option value="39095" @if( $evt_mem['country'] == "39095" ) selected @endif>Kenya</option>
															<option value="39096" @if( $evt_mem['country'] == "39096" ) selected @endif>China</option>
															<option value="39097" @if( $evt_mem['country'] == "39097" ) selected @endif>Kiribati</option>
															<option value="39098" @if( $evt_mem['country'] == "39098" ) selected @endif>Congo</option>
															<option value="39099" @if( $evt_mem['country'] == "39099" ) selected @endif>Croatia</option>
															<option value="39100" @if( $evt_mem['country'] == "39100" ) selected @endif>Cuba</option>
															<option value="39101" @if( $evt_mem['country'] == "39101" ) selected @endif>Kuwait</option>
															<option value="39102" @if( $evt_mem['country'] == "39102" ) selected @endif>Laos</option>
															<option value="39103" @if( $evt_mem['country'] == "39103" ) selected @endif>Lesotho</option>
															<option value="39104" @if( $evt_mem['country'] == "39104" ) selected @endif>Latvia</option>
															<option value="39105" @if( $evt_mem['country'] == "39105" ) selected @endif>Lebanon</option>
															<option value="39106" @if( $evt_mem['country'] == "39106" ) selected @endif>Liberia</option>
															<option value="39107" @if( $evt_mem['country'] == "39107" ) selected @endif>Libya</option>
															<option value="39108" @if( $evt_mem['country'] == "39108" ) selected @endif>Liechtenstein</option>
															<option value="39109" @if( $evt_mem['country'] == "39109" ) selected @endif>Lithuania</option>
															<option value="39110" @if( $evt_mem['country'] == "39110" ) selected @endif>Luxemburg</option>
															<option value="39111" @if( $evt_mem['country'] == "39111" ) selected @endif>Madagascar</option>
															<option value="39112" @if( $evt_mem['country'] == "39112" ) selected @endif>Maced</option>
															<option value="39113" @if( $evt_mem['country'] == "39113" ) selected @endif>Malawi</option>
															<option value="39114" @if( $evt_mem['country'] == "39114" ) selected @endif>Malaysia</option>
															<option value="39115" @if( $evt_mem['country'] == "39115" ) selected @endif>Maldives</option>
															<option value="39116" @if( $evt_mem['country'] == "39116" ) selected @endif>Mali</option>
															<option value="39117" @if( $evt_mem['country'] == "39117" ) selected @endif>Malta</option>
															<option value="39118" @if( $evt_mem['country'] == "39118" ) selected @endif>Marocko</option>
															<option value="39119" @if( $evt_mem['country'] == "39119" ) selected @endif>Marshall Islands</option>
															<option value="39120" @if( $evt_mem['country'] == "39120" ) selected @endif>Martinique</option>
															<option value="39121" @if( $evt_mem['country'] == "39121" ) selected @endif>Mauritius</option>
															<option value="39122" @if( $evt_mem['country'] == "39122" ) selected @endif>Mayotte</option>
															<option value="39123" @if( $evt_mem['country'] == "39123" ) selected @endif>Mexico</option>
															<option value="39124" @if( $evt_mem['country'] == "39124" ) selected @endif>Micronesia </option>
															<option value="39125" @if( $evt_mem['country'] == "39125" ) selected @endif>Mozambique</option>
															<option value="39126" @if( $evt_mem['country'] == "39126" ) selected @endif>Moldova</option>
															<option value="39127" @if( $evt_mem['country'] == "39127" ) selected @endif>Monaco</option>
															<option value="39128" @if( $evt_mem['country'] == "39128" ) selected @endif>Mongolia</option>
															<option value="39129" @if( $evt_mem['country'] == "39129" ) selected @endif>Namibia</option>
															<option value="39130" @if( $evt_mem['country'] == "39130" ) selected @endif>Nauru</option>
															<option value="39131" @if( $evt_mem['country'] == "39131" ) selected @endif>Netherlands</option>
															<option value="39132" @if( $evt_mem['country'] == "39132" ) selected @endif>Netherlands Antilles</option>
															<option value="39133" @if( $evt_mem['country'] == "39133" ) selected @endif>Nepal</option>
															<option value="39134" @if( $evt_mem['country'] == "39134" ) selected @endif>Nicaragua</option>
															<option value="39135" @if( $evt_mem['country'] == "39135" ) selected @endif>Niger</option>
															<option value="39136" @if( $evt_mem['country'] == "39136" ) selected @endif>Nigeria</option>
															<option value="39137" @if( $evt_mem['country'] == "39137" ) selected @endif>North Korea</option>
															<option value="39138" @if( $evt_mem['country'] == "39138" ) selected @endif>Norway</option>
															<option value="39139" @if( $evt_mem['country'] == "39139" ) selected @endif>New Zealand</option>
															<option value="39140" @if( $evt_mem['country'] == "39140" ) selected @endif>Oman</option>
															<option value="39141" @if( $evt_mem['country'] == "39141" ) selected @endif>Pakistan</option>
															<option value="39142" @if( $evt_mem['country'] == "39142" ) selected @endif>Panama</option>
															<option value="39143" @if( $evt_mem['country'] == "39143" ) selected @endif>Papua New Guinea</option>
															<option value="39144" @if( $evt_mem['country'] == "39144" ) selected @endif>Paraguay</option>
															<option value="39145" @if( $evt_mem['country'] == "39145" ) selected @endif>Peru</option>
															<option value="39146" @if( $evt_mem['country'] == "39146" ) selected @endif>Pitcairn Island</option>
															<option value="39147" @if( $evt_mem['country'] == "39147" ) selected @endif>Poland</option>
															<option value="39148" @if( $evt_mem['country'] == "39148" ) selected @endif>Portugal</option>
															<option value="39149" @if( $evt_mem['country'] == "39149" ) selected @endif>Puerto Rico</option>
															<option value="39150" @if( $evt_mem['country'] == "39150" ) selected @endif>Reunion</option>
															<option value="39151" @if( $evt_mem['country'] == "39151" ) selected @endif>Romania</option>
															<option value="39152" @if( $evt_mem['country'] == "39152" ) selected @endif>Rwanda</option>
															<option value="39153" @if( $evt_mem['country'] == "39153" ) selected @endif>Russia</option>
															<option value="39154" @if( $evt_mem['country'] == "39154" ) selected @endif>Saint Christopher och Nevis</option>
															<option value="39155" @if( $evt_mem['country'] == "39155" ) selected @endif>Saint Helena</option>
															<option value="39156" @if( $evt_mem['country'] == "39156" ) selected @endif>Saint Lucia</option>
															<option value="39157" @if( $evt_mem['country'] == "39157" ) selected @endif>Saint Vincent och Grenadinerna</option>
															<option value="39158" @if( $evt_mem['country'] == "39158" ) selected @endif>Saint-Pierre-et-Miquelon</option>
															<option value="39159" @if( $evt_mem['country'] == "39159" ) selected @endif>Salomonoarna</option>
															<option value="39160" @if( $evt_mem['country'] == "39160" ) selected @endif>Samoa</option>
															<option value="39161" @if( $evt_mem['country'] == "39161" ) selected @endif>Soo Tomo och Principe</option>
															<option value="39162" @if( $evt_mem['country'] == "39162" ) selected @endif>Saudi Arabia</option>
															<option value="39163" @if( $evt_mem['country'] == "39163" ) selected @endif>Schweiz</option>
															<option value="39164" @if( $evt_mem['country'] == "39164" ) selected @endif>Senegal</option>
															<option value="39165" @if( $evt_mem['country'] == "39165" ) selected @endif>Serbia</option>
															<option value="39166" @if( $evt_mem['country'] == "39166" ) selected @endif>Sierra Leone</option>
															<option value="39167" @if( $evt_mem['country'] == "39167" ) selected @endif>Singapore</option>
															<option value="39168" @if( $evt_mem['country'] == "39168" ) selected @endif>Scottland</option>
															<option value="39169" @if( $evt_mem['country'] == "39169" ) selected @endif>Slovakia</option>
															<option value="39170" @if( $evt_mem['country'] == "39170" ) selected @endif>Slovenia</option>
															<option value="39171" @if( $evt_mem['country'] == "39171" ) selected @endif>Spain</option>
															<option value="39172" @if( $evt_mem['country'] == "39172" ) selected @endif>Sri Lanka</option>
															<option value="39173" @if( $evt_mem['country'] == "39173" ) selected @endif>Great Britain</option>
															<option value="39174" @if( $evt_mem['country'] == "39174" ) selected @endif>Sudan</option>
															<option value="39175" @if( $evt_mem['country'] == "39175" ) selected @endif>Surinam</option>
															<option value="39176" @if( $evt_mem['country'] == "39176" ) selected @endif>Swaziland</option>
															<option value="39177" @if( $evt_mem['country'] == "39177" ) selected @endif>South Africa</option>
															<option value="39178" @if( $evt_mem['country'] == "39178" ) selected @endif>South Korea</option>
															<option value="39179" @if( $evt_mem['country'] == "39179" ) selected @endif>Syria</option>
															<option value="39180" @if( $evt_mem['country'] == "39180" ) selected @endif>Taiwan</option>
															<option value="39181" @if( $evt_mem['country'] == "39181" ) selected @endif>Tanzania</option>
															<option value="39182" @if( $evt_mem['country'] == "39182" ) selected @endif>Tchad</option>
															<option value="39183" @if( $evt_mem['country'] == "39183" ) selected @endif>Thailand</option>
															<option value="39184" @if( $evt_mem['country'] == "39184" ) selected @endif>Czech Republic</option>
															<option value="39185" @if( $evt_mem['country'] == "39185" ) selected @endif>Togo</option>
															<option value="39186" @if( $evt_mem['country'] == "39186" ) selected @endif>Tonga</option>
															<option value="39187" @if( $evt_mem['country'] == "39187" ) selected @endif>Trinidad &amp; Tobago</option>
															<option value="39188" @if( $evt_mem['country'] == "39188" ) selected @endif>Tunisia</option>
															<option value="39189" @if( $evt_mem['country'] == "39189" ) selected @endif>Turkey</option>
															<option value="39190" @if( $evt_mem['country'] == "39190" ) selected @endif>Turkmenistan</option>
															<option value="39191" @if( $evt_mem['country'] == "39191" ) selected @endif>Turks and Caicos Is</option>
															<option value="39192" @if( $evt_mem['country'] == "39192" ) selected @endif>Tuvalu</option>
															<option value="39193" @if( $evt_mem['country'] == "39193" ) selected @endif>Germany</option>
															<option value="39194" @if( $evt_mem['country'] == "39194" ) selected @endif>Uganda</option>
															<option value="39195" @if( $evt_mem['country'] == "39195" ) selected @endif>Ukraine</option>
															<option value="39196" @if( $evt_mem['country'] == "39196" ) selected @endif>Hungaria</option>
															<option value="39197" @if( $evt_mem['country'] == "39197" ) selected @endif>Uruguay</option>
															<option value="39198" @if( $evt_mem['country'] == "39198" ) selected @endif>USA</option>
															<option value="39199" @if( $evt_mem['country'] == "39199" ) selected @endif>Uzbekistan</option>
															<option value="39200" @if( $evt_mem['country'] == "39200" ) selected @endif>Wales</option>
															<option value="39201" @if( $evt_mem['country'] == "39201" ) selected @endif>Wallis and Futuna</option>
															<option value="39202" @if( $evt_mem['country'] == "39202" ) selected @endif>Vanuatu</option>
															<option value="39203" @if( $evt_mem['country'] == "39203" ) selected @endif>Venezuela</option>
															<option value="39204" @if( $evt_mem['country'] == "39204" ) selected @endif>Vietnam</option>
															<option value="39205" @if( $evt_mem['country'] == "39205" ) selected @endif>Belarus</option>
															<option value="39206" @if( $evt_mem['country'] == "39206" ) selected @endif>Zambia</option>
															<option value="39207" @if( $evt_mem['country'] == "39207" ) selected @endif>Zimbabwe</option>
															<option value="39208" @if( $evt_mem['country'] == "39208" ) selected @endif>Austria</option>
															<option value="39209" @if( $evt_mem['country'] == "39209" ) selected @endif>East Timor</option>
														</select>
													</div>
												</td>
												<th>휴대폰</th>
												<td>
													<div class="flax_box">
														<input type="text" name="mobile" class="form-control form-control-sm" value="{{ $evt_mem['mobile'] }}">
													</div>
												</td>
											</tr>
											<tr>
												<th>이메일</th>
												<td>
													<div class="flax_box">
														<input type="text" name="email" class="form-control form-control-sm" value="{{ $evt_mem['email'] }}">
													</div>
												</td>
												<th>생년월일</th>
												<td>
													<div class="flax_box">
														<input type="text" name="birthdate" class="form-control form-control-sm" value="{{ $evt_mem['birthdate'] }}">
													</div>
												</td>
											</tr>
											<tr>
												<th>성별</th>
												<td>
													<div class="flax_box">
														<select name="sex" class="form-control form-control-sm">
															<option value="">---</option>
															<option value="M" @if($evt_mem['sex'] == "남성")selected @endif>남성</option>
															<option value="F" @if($evt_mem['sex'] == "여성")selected @endif>여성</option>
														</select>
													</div>
												</td>
												<th>출발그룹</th>
												<td>
													<div class="flax_box">
														<select name="group_nm" class="form-control form-control-sm">
															<option value="">---</option>
															<option value="1" @if($evt_mem['group_nm'] == "1")selected @endif>{{ $group_nm1 }}</option>
															<option value="2" @if($evt_mem['group_nm'] == "2")selected @endif>{{ $group_nm2 }}</option>
														</select>
													</div>
												</td>
											</tr>
											<tr>
												<th>긴급연락처</th>
												<td>
													<div class="flax_box">
														<input type="text" name="em_phone" class="form-control form-control-sm" value="{{ $evt_mem['em_phone'] }}">
													</div>
												</td>
												<th>식이제한</th>
												<td>
													<div class="flax_box">
														<select name="dietary_yn" class="form-control form-control-sm">
															<option value="">---</option>
															<option value="N" @if($evt_mem['dietary_yn'] == "N")selected @endif>제한 없음</option>
															<option value="Y" @if($evt_mem['dietary_yn'] == "Y")selected @endif>채식 주의</option>
														</select>
													</div>
												</td>
											</tr>
											<tr>
												<th>스웨덴경험</th>
												<td>
													<div class="flax_box">
														<select name="part_cnt_sweden" class="form-control form-control-sm">
															<option value="">---</option>
															<option value="Y" @if($evt_mem['part_cnt_sweden'] == "Y")selected @endif>참가 경험이 있다</option>
															<option value="N" @if($evt_mem['part_cnt_sweden'] == "N")selected @endif>참가 경헙이 없다</option>
														</select>
													</div>
												</td>
												<th>덴마크경험</th>
												<td>
													<div class="flax_box">
														<select name="part_cnt_denmark" class="form-control form-control-sm">
															<option value="">---</option>
															<option value="Y" @if($evt_mem['part_cnt_denmark'] == "Y")selected @endif>참가 경험이 있다</option>
															<option value="N" @if($evt_mem['part_cnt_denmark'] == "N")selected @endif>참가 경헙이 없다</option>
														</select>
													</div>
												</td>
											</tr>
											<tr>
												<th>USA경험</th>
												<td>
													<div class="flax_box">
														<select name="part_cnt_usa" class="form-control form-control-sm">
															<option value="">---</option>
															<option value="Y" @if($evt_mem['part_cnt_usa'] == "Y")selected @endif>참가 경험이 있다</option>
															<option value="N" @if($evt_mem['part_cnt_usa'] == "N")selected @endif>참가 경헙이 없다</option>
														</select>
													</div>
												</td>
												<th>홍콩경험</th>
												<td>
													<div class="flax_box">
														<select name="part_cnt_hongkong" class="form-control form-control-sm">
															<option value="">---</option>
															<option value="Y" @if($evt_mem['part_cnt_hongkong'] == "Y")selected @endif>참가 경험이 있다</option>
															<option value="N" @if($evt_mem['part_cnt_hongkong'] == "N")selected @endif>참가 경헙이 없다</option>
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

	ret	= confirm("접수상태를 변경 하시겠습니까?");

	if( ret )
	{
		order_no	= document.f1.order_no.value;
		user_code	= document.f1.user_code.value;
		evt_state	= document.f1.evt_state[document.f1.evt_state.selectedIndex].value;

		$.ajax({
			method: 'put',
			url: '/head/promotion/prm13/chgstate',
			data: {
				'order_no' : order_no,
				'user_code' : user_code,
				'evt_state' : evt_state
			},
			success: function (data) {
				console.log(data);
				if( data.code == "200" )
				{
					alert('접수상태가 수정되었습니다.');
				}
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

if ($('.edit-btn').length > 0) {
	//수정
	$('.edit-btn').click(function(e){
		
		if (Validate() === false) return;

		ret = confirm("변경된 내용을 수정하시겠습니까?");

		if( ret )
		{

			const data = $('form[name="f1"]').serialize();

			$.ajax({
				async: true,
				type: 'put',
				url: '/head/promotion/prm13/show/' + order_no + '/' + user_code,
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
}
</script>
@stop

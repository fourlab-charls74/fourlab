@extends('head_with.layouts.layout-nav')
@section('title','클래식 숙소예약 상세내역')
@section('content')
<form name="info" method="post" action="/head/classic/cls02/update">
    <input type="HIDDEN" name="regist_number" value="{{ $reserve->regist_number }}">
    <input type="hidden" name="_token" value="">
    <div class="show_layout py-3">
        <div class="card_wrap aco_card_wrap">
            <div class="card shadow">
                <div class="card-header mb-0">
                    <a href="#">숙소예약 상세 내역</a>
                </div>
                <div class="card-body mt-1">
                    <div class="row_wrap">
					    <div class="row pt30">
                            <div class="col-sm-12 form-group">
                                <input class="form-control" id="regist_number_view" name="regist_number_view" placeholder="* 참가자 등록번호" type="text" value="참가자 등록번호 : {{ $reserve->regist_number }}" readonly="">
                            </div>
                            <div class="col-sm-6 form-group">
                                <input class="form-control" id="passwd" name="passwd" placeholder="* 비밀번호" type="text" value="{{ $reserve->passwd }}">
                            </div>
						    <div class="col-sm-6 form-group">
							    <select class="form-control" id="state" name="state">
                                    <option value="">* 상태</option>
                                    @foreach($states as $state)
                                    <option value="{{ $state->code }}" @if($reserve->state == $state->code) selected @endif>{{ $state->value1 }}</option>
                                    @endforeach
                                </select>
						    </div>
                            <div class="col-sm-6 form-group">
                                <input class="form-control" id="name1" name="name1" placeholder="* 이름(First Name)" type="text" value="{{ @$reserve->name1 }}">
                            </div>
                            <div class="col-sm-6 form-group">
                                <input class="form-control" id="name2" name="name2" placeholder="* 성(Last Name)" type="text" value="{{ @$reserve->name2 }}">
                            </div>
                            <div class="col-sm-6 form-group">
                                <input class="form-control" id="mobile" name="mobile" placeholder="* Mobile : 숫자만 입력" type="text" value="{{ @$reserve->mobile }}">
                            </div>
                            <div class="col-sm-6 form-group">
                                <input class="form-control" id="email" name="email" placeholder="* E-mail" type="email" value="{{ @$reserve->email }}">
                            </div>
                            <div class="col-sm-12 form-group" style="padding-top:30px;">
                                BEFORE TREKKING
                            </div>
						    <div class="col-sm-6 form-group">
							    <select class="form-control" id="s_dm_date" name="s_dm_date" onchange="chgDmType('s','e');">
								    <option value="">* 숙박일</option>
                                    <option value="1017" class="s_chk_date 1017" @if(@$reserve->s_dm_date == 1017) selected @endif>10월 17일(10월 18일 출발그룹)</option>
                                    <option value="1018" class="s_chk_date 1018" @if(@$reserve->s_dm_date == 1018) selected @endif>10월 18일(10월 19일 출발그룹)</option>
                                    <option value="1019" class="s_chk_date 1019" @if(@$reserve->s_dm_date == 1019) selected @endif>10월 19일(10월 20일 출발그룹)</option>
                                </select>
						    </div>
						    <div class="col-sm-6 form-group">
							    <select class="form-control" id="s_dm_type" name="s_dm_type">
								    <option value="">* 객실타입</option>
                                    @foreach($dms as $dm)
                                        @if($dm->dm_type == 0)
                                        <option value="{{ $dm->dm_type }}" style="display: block;" class="s_chk s_type_{{ $dm->dm_date }}" @if($reserve->s_dm_type == $dm->dm_type) selected @endif>{{ $dm->value1 }}</option>
                                        @else
                                        <option value="{{ $dm->dm_type }}" style="display: block;" class="s_chk s_type_{{ $dm->dm_date }}" @if($reserve->s_dm_type == $dm->dm_type) selected @endif @if($dm->reserve_cnt >= $dm->dm_cnt) disabled @endif>{{ $dm->value1 }} &lpar; {{ $dm->reserve_cnt }} &sol; {{ $dm->dm_cnt }} &rpar;</option>
                                        @endif
                                    @endforeach
                                </select>
						    </div>
                            <div class="col-sm-12 form-group">
                                AFTER TREKKING
                            </div>
						    <div class="col-sm-6 form-group">
							    <select class="form-control" id="e_dm_date" name="e_dm_date" onchange="chgDmType('e','e');">
								    <option value="">* 숙박일</option>
                                    <option value="1020" class="e_chk_date 1020" @if(@$reserve->e_dm_date == 1020) selected @endif>10월 20일(10월 18일 출발그룹)</option>
                                    <option value="1021" class="e_chk_date 1021" @if(@$reserve->e_dm_date == 1021) selected @endif>10월 21일(10월 19일 출발그룹)</option>
                                    <option value="1022" class="e_chk_date 1022" @if(@$reserve->e_dm_date == 1022) selected @endif>10월 22일(10월 20일 출발그룹)</option>
                                </select>
						    </div>
                            <div class="col-sm-6 form-group">
                                <select class="form-control" id="e_dm_type" name="e_dm_type">
                                    <option value="">* 객실타입</option>
                                    @foreach($dms as $dm)
                                        @if($dm->dm_type == 0)
                                        <option value="{{ $dm->dm_type }}" style="display: block;" class="e_chk e_type_{{ $dm->dm_date }}" @if($reserve->e_dm_type == $dm->dm_type) selected @endif>{{ $dm->value1 }}</option>
                                        @else
                                        <option value="{{ $dm->dm_type }}" style="display: block;" class="e_chk e_type_{{ $dm->dm_date }}" @if($reserve->e_dm_type == $dm->dm_type) selected @endif @if($dm->reserve_cnt >= $dm->dm_cnt) disabled @endif>{{ $dm->value1 }} &lpar; {{ $dm->reserve_cnt }} &sol; {{ $dm->dm_cnt }} &rpar;</option>
                                        @endif
                                    @endforeach
                                </select>
						    </div>
					    </div>
				    </div>
				    <div class="row_wrap">
					    <div class="row pt30">
                            <div class="col-sm-12 form-group" style="font-size:16px;">
                                <strong>※ 객실현황</strong>
                            </div>
						    <div class="col-sm-12 form-group">
							    <table style="width:100%;border:1px solid #DDDDDD;">
								    <tbody>
                                        <tr style="text-align:center;height:30px;background-color:#F1F1F1;">
                                            <td style="border:1px solid #DDDDDD;">룸타입</td>
                                            <td style="border:1px solid #DDDDDD;">예약가능수</td>
                                            @foreach($rsv_date as $date)
                                            <td style="border:1px solid #DDDDDD;" value="{{ $date->code }}">{{ $date->value3 }}</td>
                                            @endforeach
                                        </tr>
                                        
                                        <tr style="text-align:center;height:25px;">
                                            <td style="padding-left:5px;border:1px solid #DDDDDD;text-align:left;">디럭스더블룸 60,000원[원룸형 16평, 최대인원2, 퀸베드1]</td>
                                            <td style="border:1px solid #DDDDDD;">4</td>
                                            @foreach($room_status as $room)
                                                @if($room->dm_type == '1')
                                                    @if($room->reserve_cnt < $room->dm_cnt)
                                                    <td style="border:1px solid #DDDDDD;">{{ $room->reserve_cnt }}</td>
                                                    @else
                                                    <td style="border:1px solid #DDDDDD; background-color:#E87D81; ">{{ $room->reserve_cnt }}</td>
                                                    @endif
                                                @endif
                                            @endforeach
                                        </tr>
                                        <tr style="text-align:center;height:25px;">
                                            <td style="padding-left:5px;border:1px solid #DDDDDD;text-align:left;">디럭스 패밀리 트윈룸 70,000원[원룸형 16평, 최대인원3, 퀸베드1, 싱글1]</td>
                                            <td style="border:1px solid #DDDDDD;">6</td>
                                            @foreach($room_status as $room)
                                                @if($room->dm_type == '2')
                                                    @if($room->reserve_cnt < $room->dm_cnt)
                                                    <td style="border:1px solid #DDDDDD;">{{ $room->reserve_cnt }}</td>
                                                    @else
                                                    <td style="border:1px solid #DDDDDD; background-color:#E87D81; ">{{ $room->reserve_cnt }}</td>
                                                    @endif
                                                @endif
                                            @endforeach
                                        </tr>
                                        <tr style="text-align:center;height:25px;">
                                            <td style="padding-left:5px;border:1px solid #DDDDDD;text-align:left;">디럭스 트리플룸 105,000원[원룸형 16평, 최대인원4, 퀸2, 싱글1]</td>
                                            <td style="border:1px solid #DDDDDD;">4</td>
                                            @foreach($room_status as $room)
                                                @if($room->dm_type == '3')
                                                    @if($room->reserve_cnt < $room->dm_cnt)
                                                    <td style="border:1px solid #DDDDDD;">{{ $room->reserve_cnt }}</td>
                                                    @else
                                                    <td style="border:1px solid #DDDDDD; background-color:#E87D81; ">{{ $room->reserve_cnt }}</td>
                                                    @endif
                                                @endif
                                            @endforeach
                                        </tr>
                                        <tr style="text-align:center;height:25px;">
                                            <td style="padding-left:5px;border:1px solid #DDDDDD;text-align:left;">주니어스위트룸 160,000원[거실겸 침실 +1룸, 20평, 최대인원5, 퀸베드2]</td>
                                            <td style="border:1px solid #DDDDDD;">8</td>
                                            @foreach($room_status as $room)
                                                @if($room->dm_type == '4')
                                                    @if($room->reserve_cnt < $room->dm_cnt)
                                                    <td style="border:1px solid #DDDDDD;">{{ $room->reserve_cnt }}</td>
                                                    @else
                                                    <td style="border:1px solid #DDDDDD; background-color:#E87D81; ">{{ $room->reserve_cnt }}</td>
                                                    @endif
                                                @endif
                                            @endforeach
                                        </tr>
                                        <tr style="text-align:center;height:25px;">
                                            <td style="padding-left:5px;border:1px solid #DDDDDD;text-align:left;">프리미어 스위트룸 160,000원[거실겸 침실 + 1룸, 34평, 최대인원6, 퀸베드2]</td>
                                            <td style="border:1px solid #DDDDDD;">4</td>
                                            @foreach($room_status as $room)
                                                @if($room->dm_type == '5')
                                                    @if($room->reserve_cnt < $room->dm_cnt)
                                                    <td style="border:1px solid #DDDDDD;">{{ $room->reserve_cnt }}</td>
                                                    @else
                                                    <td style="border:1px solid #DDDDDD; background-color:#E87D81; ">{{ $room->reserve_cnt }}</td>
                                                    @endif
                                                @endif
                                            @endforeach
                                        </tr>
                                        <tr style="text-align:center;height:25px;">
                                            <td style="padding-left:5px;border:1px solid #DDDDDD;text-align:left;">애월 스위트룸 160,000원[거실 + 2룸, 45평, 최대인원6, 퀸베드2]</td>
                                            <td style="border:1px solid #DDDDDD;">1</td>
                                            @foreach($room_status as $room)
                                                @if($room->dm_type == '6')
                                                    @if($room->reserve_cnt < $room->dm_cnt)
                                                    <td style="border:1px solid #DDDDDD;">{{ $room->reserve_cnt }}</td>
                                                    @else
                                                    <td style="border:1px solid #DDDDDD; background-color:#E87D81; ">{{ $room->reserve_cnt }}</td>
                                                    @endif
                                                @endif
                                            @endforeach
                                        </tr>
                                        <tr style="text-align:center;height:25px;">
                                            <td style="padding-left:5px;border:1px solid #DDDDDD;text-align:left;">로얄슈페리어 스위트 180,000원[거실겸 침실+1룸, 34평, 최대인원 4, 퀸베드2]</td>
                                            <td style="border:1px solid #DDDDDD;">3</td>
                                            @foreach($room_status as $room)
                                                @if($room->dm_type == '7')
                                                    @if($room->reserve_cnt < $room->dm_cnt)
                                                    <td style="border:1px solid #DDDDDD;">{{ $room->reserve_cnt }}</td>
                                                    @else
                                                    <td style="border:1px solid #DDDDDD; background-color:#E87D81; ">{{ $room->reserve_cnt }}</td>
                                                    @endif
                                                @endif
                                            @endforeach
                                        </tr>
                                        <tr style="text-align:center;height:25px;">
                                            <td style="padding-left:5px;border:1px solid #DDDDDD;text-align:left;">로얄그랜드 스위트 180,000원[거실겸 침실 + 1룸, 45평, 최대인원4, 퀸베드2]</td>
                                            <td style="border:1px solid #DDDDDD;">5</td>
                                            @foreach($room_status as $room)
                                                @if($room->dm_type == '8')
                                                    @if($room->reserve_cnt < $room->dm_cnt)
                                                    <td style="border:1px solid #DDDDDD;">{{ $room->reserve_cnt }}</td>
                                                    @else
                                                    <td style="border:1px solid #DDDDDD; background-color:#E87D81; ">{{ $room->reserve_cnt }}</td>
                                                    @endif
                                                @endif
                                            @endforeach
                                        </tr>
								    </tbody>
                                </table>
						    </div>
					    </div>
				    </div>
			    </div>
		    </div>
	    </div>
        <div class="resul_btn_wrap mt-3 d-block">
            <a href="javascript:;" class="btn btn-sm btn-primary" onclick="save()">수정</a>
            <a href="javascript:;" class="btn btn-sm btn-primary" onclick="window.close()">닫기</a>
        </div>
    </div>
</form>
<script>
function save(){

	ff	= document.info;

	if( ff.passwd.value == "" ){
		alert("비밀번호는 반드시 입력해야 합니다.");
		ff.passwd.focus();

		return false;
	}

	if( ff.state.selectedIndex == "0" ){
		alert("상태는 반드시 선택해야 합니다.");
		ff.state.focus();

		return false;
	}

	if( ff.name1.value == "" ){
		alert("신청자 성명(First Name)은 반드시 입력해야 합니다.");
		ff.name1.focus();

		return false;
	}

	if( ff.name2.value == "" ){
		alert("신청자 성명(Last Name)은 반드시 입력해야 합니다.");
		ff.name2.focus();

		return false;
	}

	if( ff.mobile.value == "" ){
		alert("휴대폰 번호는 반드시 입력해야 합니다.");
		ff.mobile.focus();

		return false;
	}

	if( ff.email.value == "" ){
		alert("이메일은 반드시 입력해야 합니다.");
		ff.email.focus();

		return false;
	}

	var exptext	= /^[A-Za-z0-9_\.\-]+@[A-Za-z0-9\-]+\.[A-Za-z0-9\-]+/;
	if( exptext.test(ff.email.value) != true ){
		alert("이메일 형식이 올바르지 않습니다.");
		ff.email.focus();

		return false;
	}

	if( ff.s_dm_date.selectedIndex == "0" ){
		alert("전날 숙박일은 반드시 선택해야 합니다.");
		ff.s_dm_date.focus();

		return false;
	}

	if( ff.s_dm_type.selectedIndex == "0" ){
		alert("전날 객실타입은 반드시 선택해야 합니다.");
		ff.s_dm_type.focus();

		return false;
	}

	if( ff.e_dm_date.selectedIndex == "0" ){
		alert("종료후 숙박일은 반드시 선택해야 합니다.");
		ff.e_dm_date.focus();

		return false;
	}

	if( ff.e_dm_type.selectedIndex == "0" ){
		alert("종료후 객실타입은 반드시 선택해야 합니다.");
		ff.e_dm_type.focus();

		return false;
	}

	ret = confirm("예약정보를 수정 하시겠습니까?");

	if( ret ){
		ff.submit();
	}

}

function chgDmType(type, init){
	ff	= document.info;

	$("." + type + "_chk").css("display","none");

	if(type == "s"){
		$("." + type + "_type_" + ff.s_dm_date[ff.s_dm_date.selectedIndex].value ).css("display","block");
		if( init != "s" )	ff.s_dm_type.selectedIndex	= 0;
	}

	if(type == "e")	{
        $("." + type + "_type_" + ff.e_dm_date[ff.e_dm_date.selectedIndex].value ).css("display","block");
        if( init != "s" )	ff.e_dm_type.selectedIndex	= 0;
    }
}
</script>
<script>
	chgDmType('s','s');
	chgDmType('e','s');
</script>
@stop

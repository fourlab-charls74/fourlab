@extends('head_with.layouts.layout-nav')
@section('title','클래식 숙소예약 상세내역')
@section('content')
<form name="info" method="post">
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
                                    @foreach($dates as $date)
                                        @if($date->code < 1020)
                                        <option value="{{ $date->code }}" class="s_chk_date {{ $date->code }}" @if($reserve->s_dm_date == $date->code) selected @endif>{{ $date->value1 }}</option>
                                        @endif
                                    @endforeach
                                </select>
						    </div>
						    <div class="col-sm-6 form-group">
							    <select class="form-control" id="s_dm_type" name="s_dm_type">
								    <option value="">* 객실타입</option>  
                                    @foreach($types as $type)
                                        @if($type->dm_type == 0)
                                            <option value="{{ $type->dm_type }}" style="display: block;" class="s_chk s_type_{{ $type->dm_date }}" 
                                            @if($reserve->s_dm_type == $type->dm_type) selected @endif
                                            >{{ $type->value1 }}</option>
                                        @else
                                            <option value="{{ $type->dm_type }}" style="display: block; 
                                            @if($type->reserve_cnt >= $type->dm_cnt) color: rgb(204, 204, 204); @endif" class="s_chk s_type_{{ $type->dm_date }}" 
                                            @if($reserve->s_dm_type == $type->dm_type) selected @endif 
                                            @if($type->reserve_cnt >= $type->dm_cnt) disabled @endif
                                            >{{ $type->value1 }} &lpar; {{ $type->reserve_cnt }} &sol; {{ $type->dm_cnt }} &rpar;</option>
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
                                    @foreach($dates as $date)
                                        @if($date->code >= 1020)
                                        <option value="{{ $date->code }}" class="e_chk_date {{ $date->code }}" @if($reserve->e_dm_date == $date->code) selected @endif>{{ $date->value1 }}</option>
                                        @endif
                                    @endforeach
                                </select>
						    </div>
                            <div class="col-sm-6 form-group">
                                <select class="form-control" id="e_dm_type" name="e_dm_type">
                                    <option value="">* 객실타입</option>
                                    @foreach($types as $type)
                                        @if($type->dm_type == 0)
                                            <option value="{{ $type->dm_type }}" style="display: block;" class="e_chk e_type_{{ $type->dm_date }}" 
                                            @if($reserve->e_dm_type == $type->dm_type) selected @endif
                                            >{{ $type->value1 }}</option>
                                        @else
                                            <option value="{{ $type->dm_type }}" style="display: block; 
                                            @if($type->reserve_cnt >= $type->dm_cnt) color: rgb(204, 204, 204); @endif" class="e_chk e_type_{{ $type->dm_date }}" 
                                            @if($reserve->e_dm_type == $type->dm_type) selected @endif 
                                            @if($type->reserve_cnt >= $type->dm_cnt) disabled @endif
                                            >{{ $type->value1 }} &lpar; {{ $type->reserve_cnt }} &sol; {{ $type->dm_cnt }} &rpar;</option>
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
                                            @foreach($dates as $date)
                                            <td style="border:1px solid #DDDDDD;" value="{{ $date->code }}">{{ $date->value3 }}</td>
                                            @endforeach
                                        </tr>

                                        @foreach(@$dm_status as $t)
                                        <tr style="text-align:center;height:25px;">
                                            <td style="padding-left:5px;border:1px solid #DDDDDD;text-align:left;">
                                                {{ $t->room_nm }}
                                            </td>
                                            <td style="border:1px solid #DDDDDD;">{{ $t->dm_cnt }}</td>
                                            @foreach(@$dm_dates as $date)
                                                <td 
                                                    @if ($t->{'reserve_' . $date->dm_date} < $t->dm_cnt)
                                                    style="border:1px solid #DDDDDD; "
                                                    @else
                                                    style="border:1px solid #DDDDDD; background-color:#E87D81; "
                                                    @endif
                                                >
                                                    {{ $t->{'reserve_' . $date->dm_date} }}
                                                </td>
                                            @endforeach
                                        </tr>
                                        @endforeach

								    </tbody>
                                </table>
						    </div>
					    </div>
				    </div>
			    </div>
		    </div>
	    </div>
        <div class="resul_btn_wrap mt-3 d-block">
            <a href="javascript:;" class="btn btn-sm btn-primary" onclick="Save()">수정</a>
            <a href="javascript:;" class="btn btn-sm btn-primary" onclick="window.close()">닫기</a>
        </div>
    </div>
</form>
<script>
function Save(){

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
        var frm = $('form[name="info"]');

        let e_dm_type = $("select[name=e_dm_type] option:selected").val();
        let s_dm_type = $("select[name=s_dm_type] option:selected").val();

        $.ajax({
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            method: 'post',
            url: '/head/classic/cls02/update',
            data: frm.serialize() + '&e_dm_type=' + e_dm_type + '&s_dm_type=' + s_dm_type,
            dataType: 'json',
            success: function (res) {
                if(res.code === 200) {
                    alert('예약정보가 수정 되었습니다.');
                    location.reload();
                    window.opener.Search();
                } else {
                    alert('처리 중 문제가 발생하였습니다. 다시 시도하여 주십시오.');
                }
            },
            error: function(request, status, error) {
                alert("에러가 발생했습니다.");
                console.log(status);
            }
        });
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

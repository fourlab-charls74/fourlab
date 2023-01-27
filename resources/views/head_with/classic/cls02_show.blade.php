@extends('head_with.layouts.layout')
@section('title','클래식 숙소예약 상세내역')
@section('content')
<form name="info" method="post" action="/head/promotion/prm14/update">
    <input type="HIDDEN" name="regist_number" value="3DNZHM764RK">
    <input type="hidden" name="_token" value="VVHpji7KQn8BhWCxllujTdBlKVRubn7zWGoKB2T2">
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
                                <input class="form-control" id="regist_number_view" name="regist_number_view" placeholder="* 참가자 등록번호" type="text" value="참가자 등록번호 : {{ @$reserve->regist_number }}" readonly="">
                            </div>
                            <div class="col-sm-6 form-group">
                                <input class="form-control" id="passwd" name="passwd" placeholder="* 비밀번호" type="text" value="{{ @$reserve->passwd }}">
                            </div>
						    <div class="col-sm-6 form-group">
							    <select class="form-control" id="state" name="state">
                                    <option value="">* 상태</option>
                                    <option value="10" @if(@$reserve->state == 10) selected @endif>접수대기</option>
                                    <option value="20" @if(@$reserve->state == 20) selected @endif>접수중</option>
                                    <option value="30" @if(@$reserve->state == 30) selected @endif>접수완료</option>
                                    <option value="40" @if(@$reserve->state == 40) selected @endif>확정완료</option>
                                    <option value="41" @if(@$reserve->state == 41) selected @endif>현장결제</option>
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
                                    @foreach(@$cnts as $cnt)
                                        @if($cnt->code == 0)
                                        <option value="{{ $cnt->code }}" style="display: block;" @if(@$reserve->s_dm_type == @$cnt->code) selected @endif>{{ $cnt-> value1 }}</option>
                                        @else
                                        <option value="{{ $cnt->code }}" style="display: block;" @if(@$reserve->s_dm_type == @$cnt->code) selected @endif>{{ $cnt-> value1 }} &lpar; 예약숫자 &sol; 가능숫자 &rpar;</option>
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
                                    <option value="1020" class="e_chk_date 1020" selected="">10월 20일(10월 18일 출발그룹)</option>
                                    <option value="1021" class="e_chk_date 1021">10월 21일(10월 19일 출발그룹)</option>
                                    <option value="1022" class="e_chk_date 1022">10월 22일(10월 20일 출발그룹)</option>
                                </select>
						    </div>
                            <div class="col-sm-6 form-group">
                                <select class="form-control" id="e_dm_type" name="e_dm_type">
                                    <option value="">* 객실타입</option>
                                    @foreach(@$cnts as $cnt)
                                        @if($cnt->code == 0)
                                        <option value="{{ $cnt->code }}" style="display: block;" @if(@$reserve->e_dm_type == @$cnt->code) selected @endif>{{ $cnt-> value1 }}</option>
                                        @else
                                        <option value="{{ $cnt->code }}" style="display: block;" @if(@$reserve->e_dm_type == @$cnt->code) selected @endif>{{ $cnt-> value1 }} &lpar; 예약숫자 &sol; 가능숫자 &rpar;</option>
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
                                            <td style="border:1px solid #DDDDDD;">10월 17일</td>
                                            <td style="border:1px solid #DDDDDD;">10월 18일</td>
                                            <td style="border:1px solid #DDDDDD;">10월 19일</td>
                                            <td style="border:1px solid #DDDDDD;">10월 20일</td>
                                            <td style="border:1px solid #DDDDDD;">10월 21일</td>
                                            <td style="border:1px solid #DDDDDD;">10월 22일</td>
                                        </tr>
                                        <tr style="text-align:center;height:25px;">
                                            <td style="padding-left:5px;border:1px solid #DDDDDD;text-align:left;">디럭스더블룸 60,000원[원룸형 16평, 최대인원2, 퀸베드1]</td>
                                            <td style="border:1px solid #DDDDDD;">4</td>
                                            <td style="border:1px solid #DDDDDD; background-color:#E87D81; ">4</td>
                                            <td style="border:1px solid #DDDDDD; background-color:#E87D81; ">4</td>
                                            <td style="border:1px solid #DDDDDD; background-color:#E87D81; ">4</td>
                                            <td style="border:1px solid #DDDDDD; background-color:#E87D81; ">4</td>
                                            <td style="border:1px solid #DDDDDD; background-color:#E87D81; ">4</td>
                                            <td style="border:1px solid #DDDDDD; background-color:#E87D81; ">4</td>
                                        </tr>
                                        <tr style="text-align:center;height:25px;">
                                            <td style="padding-left:5px;border:1px solid #DDDDDD;text-align:left;">디럭스 패밀리 트윈룸 70,000원[원룸형 16평, 최대인원3, 퀸베드1, 싱글1]</td>
                                            <td style="border:1px solid #DDDDDD;">6</td>
                                            <td style="border:1px solid #DDDDDD; background-color:#E87D81; ">6</td>
                                            <td style="border:1px solid #DDDDDD; background-color:#E87D81; ">6</td>
                                            <td style="border:1px solid #DDDDDD; background-color:#E87D81; ">6</td>
                                            <td style="border:1px solid #DDDDDD; background-color:#E87D81; ">6</td>
                                            <td style="border:1px solid #DDDDDD; background-color:#E87D81; ">6</td>
                                            <td style="border:1px solid #DDDDDD; background-color:#E87D81; ">6</td>
                                        </tr>
                                        <tr style="text-align:center;height:25px;">
                                            <td style="padding-left:5px;border:1px solid #DDDDDD;text-align:left;">디럭스 트리플룸 105,000원[원룸형 16평, 최대인원4, 퀸2, 싱글1]</td>
                                            <td style="border:1px solid #DDDDDD;">4</td>
                                            <td style="border:1px solid #DDDDDD; background-color:#E87D81; ">4</td>
                                            <td style="border:1px solid #DDDDDD; background-color:#E87D81; ">4</td>
                                            <td style="border:1px solid #DDDDDD; background-color:#E87D81; ">4</td>
                                            <td style="border:1px solid #DDDDDD; background-color:#E87D81; ">4</td>
                                            <td style="border:1px solid #DDDDDD; background-color:#E87D81; ">4</td>
                                            <td style="border:1px solid #DDDDDD; background-color:#E87D81; ">4</td>
                                        </tr>
                                        <tr style="text-align:center;height:25px;">
                                            <td style="padding-left:5px;border:1px solid #DDDDDD;text-align:left;">주니어스위트룸 160,000원[거실겸 침실 +1룸, 20평, 최대인원5, 퀸베드2]</td>
                                            <td style="border:1px solid #DDDDDD;">8</td>
                                            <td style="border:1px solid #DDDDDD; background-color:#E87D81; ">8</td>
                                            <td style="border:1px solid #DDDDDD; background-color:#E87D81; ">8</td>
                                            <td style="border:1px solid #DDDDDD; background-color:#E87D81; ">8</td>
                                            <td style="border:1px solid #DDDDDD; background-color:#E87D81; ">8</td>
                                            <td style="border:1px solid #DDDDDD;">7</td>
                                            <td style="border:1px solid #DDDDDD; background-color:#E87D81; ">8</td>
                                        </tr>
                                        <tr style="text-align:center;height:25px;">
                                            <td style="padding-left:5px;border:1px solid #DDDDDD;text-align:left;">프리미어 스위트룸 160,000원[거실겸 침실 + 1룸, 34평, 최대인원6, 퀸베드2]</td>
                                            <td style="border:1px solid #DDDDDD;">4</td>
                                            <td style="border:1px solid #DDDDDD; background-color:#E87D81; ">4</td>
                                            <td style="border:1px solid #DDDDDD; background-color:#E87D81; ">4</td>
                                            <td style="border:1px solid #DDDDDD; background-color:#E87D81; ">4</td>
                                            <td style="border:1px solid #DDDDDD; background-color:#E87D81; ">4</td>
                                            <td style="border:1px solid #DDDDDD; background-color:#E87D81; ">4</td>
                                            <td style="border:1px solid #DDDDDD; background-color:#E87D81; ">4</td>
                                        </tr>
                                        <tr style="text-align:center;height:25px;">
                                            <td style="padding-left:5px;border:1px solid #DDDDDD;text-align:left;">애월 스위트룸 160,000원[거실 + 2룸, 45평, 최대인원6, 퀸베드2]</td>
                                            <td style="border:1px solid #DDDDDD;">1</td>
                                            <td style="border:1px solid #DDDDDD; background-color:#E87D81; ">1</td>
                                            <td style="border:1px solid #DDDDDD; background-color:#E87D81; ">1</td>
                                            <td style="border:1px solid #DDDDDD; background-color:#E87D81; ">1</td>
                                            <td style="border:1px solid #DDDDDD; background-color:#E87D81; ">1</td>
                                            <td style="border:1px solid #DDDDDD; background-color:#E87D81; ">1</td>
                                            <td style="border:1px solid #DDDDDD; background-color:#E87D81; ">1</td>
                                        </tr>
                                        <tr style="text-align:center;height:25px;">
                                            <td style="padding-left:5px;border:1px solid #DDDDDD;text-align:left;">로얄슈페리어 스위트 180,000원[거실겸 침실+1룸, 34평, 최대인원 4, 퀸베드2]</td>
                                            <td style="border:1px solid #DDDDDD;">3</td>
                                            <td style="border:1px solid #DDDDDD; background-color:#E87D81; ">3</td>
                                            <td style="border:1px solid #DDDDDD; background-color:#E87D81; ">3</td>
                                            <td style="border:1px solid #DDDDDD; background-color:#E87D81; ">3</td>
                                            <td style="border:1px solid #DDDDDD; background-color:#E87D81; ">3</td>
                                            <td style="border:1px solid #DDDDDD; background-color:#E87D81; ">3</td>
                                            <td style="border:1px solid #DDDDDD; background-color:#E87D81; ">3</td>
                                        </tr>
                                        <tr style="text-align:center;height:25px;">
                                            <td style="padding-left:5px;border:1px solid #DDDDDD;text-align:left;">로얄그랜드 스위트 180,000원[거실겸 침실 + 1룸, 45평, 최대인원4, 퀸베드2]</td>
                                            <td style="border:1px solid #DDDDDD;">5</td>
                                            <td style="border:1px solid #DDDDDD;">4</td>
                                            <td style="border:1px solid #DDDDDD;">4</td>
                                            <td style="border:1px solid #DDDDDD; background-color:#E87D81; ">5</td>
                                            <td style="border:1px solid #DDDDDD; background-color:#E87D81; ">5</td>
                                            <td style="border:1px solid #DDDDDD;">4</td>
                                            <td style="border:1px solid #DDDDDD; background-color:#E87D81; ">5</td>
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

@stop

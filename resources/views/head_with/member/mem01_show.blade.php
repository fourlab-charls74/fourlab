@extends('head_with.layouts.layout-nav')
@section('title','회원관리')
@section('content')
@include('store_with.layouts.modal')
<script src="{{ URL::asset('/js/store_search.js?20220707')}}"></script>
<script>
    //공통 선언
    const name = '{{@$user->name}}';
    const user_id = '{{@$user->user_id}}';
    const mobile = '{{@$user->mobile}}';
    let id_chk = false;
</script>

<style>
	#counsel-box table tr th {background : #f5f5f5;min-width: 120px;max-width: 120px;}
</style>

<div class="container-fluid show_layout py-3">
    <div class="page_tit mb-3 d-flex align-items-center justify-content-between">
        <div>
            <h3 class="d-inline-flex">회원관리</h3>
            <div class="d-inline-flex location">
                <span class="home"></span>
                <span>/ 회원&amp;CRM</span>
                <span>/ 회원관리</span>
            </div>
        </div>
        <div>
            @if ($type == 'edit')
            <a href="#" class="btn btn-sm btn-primary shadow-sm edit-btn">수정</a>
            <div class="btn-group dropleftbtm mr-1">
                <button type="button" class="btn btn-primary waves-light waves-effect dropdown-toggle btn-sm pr-1" data-toggle="dropdown" aria-expanded="false">
                    <i class="fa fa-folder"></i> <i class="bx bx-chevron-down fs-12"></i>
                </button>
                <div class="dropdown-menu" style="">
                    <a href="#" class="dropdown-item coupon-btn">쿠폰지급</a>
                    <a href="#" class="dropdown-item point-btn">적립금지급</a>
                    <a href="#" class="dropdown-item out-btn">탈퇴</a>
                    <a href="#" class="dropdown-item sms-send-btn">SMS 발송</a>
                    <a href="#" class="dropdown-item sms-list-btn">SMS 내역</a>
                    <a href="#" class="dropdown-item crm-btn">CRM 쿠폰</a>
                </div>
                <input type="hidden" name="data" id="data" value="">
            </div>
            @elseif(@$out_yn == "I")
            <a href="#" class="btn btn-sm btn-primary shadow-sm active-btn">휴면해제</a>
            @else
            <a href="#" class="btn btn-sm btn-primary shadow-sm add-btn">등록</a>
            @endif
            <a href="#" onclick="window.close()" class="btn btn-sm btn-primary shadow-sm">닫기</a>
        </div>
    </div>

    <style> .required:after {content:" *"; color: red;}</style>

    <form method="get" name="search">
        <div class="card_wrap aco_card_wrap">
            <div class="card shadow @if ($type != 'edit') mb-0 @endif">
                <div class="card-header mb-0">
                    <a href="#" class="m-0 font-weight-bold">회원정보</a>
                </div>
                <div class="card-body">
                    <div class="row_wrap">
                        <div class="row">
                            <div class="col-12">
                                <div class="table-box mobile">
                                    <table class="table incont table-bordered" id="dataTable" width="100%" cellspacing="0">
                                        <colgroup>
                                            <col width="120px">
                                        </colgroup>
                                        <tr>
                                            <th class="required">아이디</th>
                                            <td>
                                                @if($type === 'add')
                                                <div class="flax_box inline_btn_box" style="padding-right:75px;">
                                                    <input type="text" name="user_id" id="user_id" class="form-control form-control-sm">
                                                    <a href="#" class="btn btn-sm btn-secondary check-user-btn fs-12" style="width:70px;">중복확인</a>
                                                </div>
                                                @else
                                                <div class="txt_box">{{@$user->user_id}}</div>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <th class="required">비밀번호</th>
                                            <td>
                                                <div class="flax_box inline_btn_box" @if($type==='edit') style="padding-right:95px;"@else style="padding:0px;" @endif>
                                                    <input type="password" name="pw" id="pw" class="form-control form-control-sm" autocomplete="new-password"/>
                                                    @if($type==='edit')
                                                        <a href="#" class="btn btn-sm btn-secondary change-pw-btn fs-12" style="width:90px;">비밀번호변경</a>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th class="required">이름</th>
                                            <td>
                                                <div class="flax_box">
                                                    <input type="text" name="name" id="name" class="form-control form-control-sm" value="{{@$user->name}}">
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>생년월일</th>
                                            <td>
                                                @if( $type == 'add')
                                                <div class="flax_box">
                                                    <div class="form-inline mr-0 mr-sm-1" style="width:100%;max-width:400px;vertical-align:top;">
                                                        <div class="form-inline-inner input_box" style="width:30%;">
                                                            <select name="yyyy" id="yyyy" class="form-control form-control-sm mr-1" onchange="setBirthDay()">
                                                                <option value="">년도</option>
                                                                @for($i = date("Y")-14; $i > date("Y")-114; $i--)
                                                                    <option value="{{$i}}">{{$i}}</option>
                                                                @endfor
                                                            </select>
                                                        </div>
                                                        <span class="text_line">-</span>
                                                        <div class="form-inline-inner input_box" style="width:29%;">
                                                            <select name="mm" id="mm" class="form-control form-control-sm mr-1" onchange="setBirthDay()">
                                                                <option value="">월</option>
																@for($i = 1; $i < 13; $i++)
																	<option value="{{$i}}">{{$i}}</option>
																@endfor
                                                            </select>
                                                        </div>
                                                        <span class="text_line">-</span>
                                                        <div class="form-inline-inner input_box" style="width:29%;">
                                                            <select name="dd" id="dd" class="form-control form-control-sm mr-1">
                                                                <option value="">일</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="form-inline form-radio-box mt-1 mt-sm-0">
                                                        <div class="custom-control custom-radio">
                                                            <input type="radio" name="yyyy_chk" id="yyyy_chk_y" class="custom-control-input" value="Y" checked>
                                                            <label class="custom-control-label" for="yyyy_chk_y">양력</label>
                                                        </div>
                                                        <div class="custom-control custom-radio">
                                                            <input type="radio" name="yyyy_chk" id="yyyy_chk_n" class="custom-control-input" value="n">
                                                            <label class="custom-control-label" for="yyyy_chk_n">음력</label>
                                                        </div>
                                                    </div>
                                                </div>
                                                @else
                                                    <div class="txt_box">{{@$user->yyyy}}-{{@$user->mm}}-{{@$user->dd}}</div>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>성별</th>
                                            <td>
                                                @if($type == 'add')
                                                    <div class="form-inline form-radio-box">
                                                        <div class="custom-control custom-radio">
                                                            <input type="radio" name="sex" id="sex_m" class="custom-control-input" value="M" checked>
                                                            <label class="custom-control-label" for="sex_m">남자</label>
                                                        </div>
                                                        <div class="custom-control custom-radio">
                                                            <input type="radio" name="sex" id="sex_f" class="custom-control-input" value="F">
                                                            <label class="custom-control-label" for="sex_f">여자</label>
                                                        </div>
                                                    </div>
                                                @else
                                                    <div class="txt_box">{{ @$user->sex == '' ? '' : (@$user->sex =='F' ? '여자' : '남자') }}</div>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>주민번호</th>
                                            <td>
                                                @if($type == 'add')
                                                    <div class="flax_box">
                                                        <div class="form-inline mr-0 mr-sm-1" style="width:100%;max-width:400px;vertical-align:top;">
                                                            <div class="form-inline-inner input_box">
                                                                <input type="text" name="jumin1" id="jumin1" class="form-control form-control-sm">
                                                            </div>
                                                            <span class="text_line">-</span>
                                                            <div class="form-inline-inner input_box">
                                                                <input type="password" name="jumin2" id="jumin2" class="form-control form-control-sm" autocomplete="off">
                                                            </div>
                                                        </div>
                                                        <div class="form-inline form-check-box mt-1 mt-sm-0">
                                                            <div class="custom-control custom-checkbox">
                                                                <input type="checkbox" name="jumin_chk" id="jumin_chk" class="custom-control-input" value="y">
                                                                <label class="custom-control-label" for="jumin_chk">유효성 검사</label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @else
                                                    @if(@$user->auth_type == "R")
                                                    <div class="txt_box">
                                                        {{@$user->jumin}}&nbsp;[{{@$user->name_chk == "Y" ? "실명확인" : "미확인"}}]
                                                    </div>
                                                    @endif
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>이메일</th>
                                            <td>
                                                <div class="flax_box">
                                                    <input type="email" pattern="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,}$"
                                                        name="email" id="email" class="form-control form-control-sm" value="{{@$user->email}}">
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>이메일수신여부</th>
                                            <td>
                                                <div class="flax_box">
                                                    <select name="send_mail_yn" id="send_mail_yn" class="form-control form-control-sm">
                                                        <option value="Y" @if(@$user->email_chk == "Y") selected @endif>Y</option>
                                                        <option value="N" @if(@$user->email_chk == "N") selected @endif>N</option>
                                                    </select>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>전화번호</th>
                                            <td>
                                                <div class="flax_box">
                                                    <div class="form-inline mr-0 mr-sm-1" style="width:100%;max-width:400px;vertical-align:top;">
                                                        <div class="form-inline-inner input_box" style="width:30%;">
                                                            <input type="text" name="phone1" id="phone1" class="form-control form-control-sm" maxlength="3" value="{{@$user->phone1}}" onkeyup="onlynum(this)">
                                                        </div>
                                                        <span class="text_line">-</span>
                                                        <div class="form-inline-inner input_box" style="width:29%;">
                                                            <input type="text" name="phone2" id="phone2" class="form-control form-control-sm" maxlength="4" value="{{@$user->phone2}}" onkeyup="onlynum(this)">
                                                        </div>
                                                        <span class="text_line">-</span>
                                                        <div class="form-inline-inner input_box" style="width:29%;">
                                                            <input type="text" name="phone3" id="phone3" class="form-control form-control-sm" maxlength="4" value="{{@$user->phone3}}" onkeyup="onlynum(this)">
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th class="required">휴대폰</th>
                                            <td>
                                                <div class="flax_box">
                                                    <div class="form-inline mr-0 mr-sm-1" style="width:100%;max-width:400px;vertical-align:top;">
                                                        <div class="form-inline-inner input_box" style="width:30%;">
                                                            <input type="text" name="mobile1" id="mobile1" class="form-control form-control-sm" maxlength="3" value="{{@$user->mobile1}}" onkeyup="onlynum(this)">
                                                        </div>
                                                        <span class="text_line">-</span>
                                                        <div class="form-inline-inner input_box" style="width:29%;">
                                                            <input type="text" name="mobile2" id="mobile2" class="form-control form-control-sm" maxlength="4" value="{{@$user->mobile2}}" onkeyup="onlynum(this)">
                                                        </div>
                                                        <span class="text_line">-</span>
                                                        <div class="form-inline-inner input_box" style="width:29%;">
                                                            <input type="text" name="mobile3" id="mobile3" class="form-control form-control-sm" maxlength="4" value="{{@$user->mobile3}}" onkeyup="onlynum(this)">
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>휴대폰수신여부</th>
                                            <td>
                                                <div class="flax_box">
                                                    <select name="send_mobile_yn" id="send_mobile_yn" class="form-control form-control-sm">
                                                        <option value="Y" @if(@$user->mobile_chk == 'Y') selected @endif>Y</option>
                                                        <option value="N" @if(@$user->mobile_chk == 'N') selected @endif>N</option>
                                                    </select>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>주소</th>
                                            <td>
                                                <div class="input_box flax_box address_box">
                                                    <input type="text" id="zipcode" name="zipcode" class="form-control form-control-sm" value="{{@$user->zip}}" style="width:calc(25% - 10px);margin-right:10px;" readonly="readonly">
                                                    <input type="text" id="addr1" name="addr1" class="form-control form-control-sm" value="{{@$user->addr}}" style="width:calc(25% - 10px);margin-right:10px;" readonly="readonly">
                                                    <input type="text" id="addr2" name="addr2" class="form-control form-control-sm" value="{{@$user->addr2}}" style="width:calc(25% - 10px);margin-right:10px;">
                                                    <a href="javascript:;" onclick="openFindAddress('zipcode', 'addr1')" class="btn btn-sm btn-primary shadow-sm fs-12" style="width:80px;">
                                                        <i class="fas fa-search fa-sm text-white-50"></i>
                                                        검색
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                        @if($type == 'edit')
                                        <tr>
                                            <th>인증방식</th>
                                            <td>
                                                <div class="txt_box">{{@$user->auth_type_nm}}</div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>인증여부</th>
                                            <td>
                                                <div class="txt_box">{{@$user->auth_yn}}</div>
                                            </td>
                                        </tr>
                                        @else
                                            <input type="hidden" name="auth_type" value="A">
                                            <input type="hidden" name="auth_yn" value="Y">
                                            <input type="hidden" name="auth_key" value="{{$admin_id}}">
                                        @endif
                                        <tr>
                                            <th>직업</th>
                                            <td>
                                                <div class="flax_box">
                                                    <input type="text" name="job" id="job" class="form-control form-control-sm" value="{{@$user->job}}">
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>관심분야</th>
                                            <td>
                                                <div class="txt_box">
                                                    {{@$interest}}
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>사이즈</th>
                                            <td>
                                                <div class="flax_box">
                                                    <input type="text" name="opt" id="opt" class="form-control form-control-sm" value="{{@$user->opt}}">
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>결혼유무</th>
                                            <td>
                                                <div class="form-inline form-radio-box">
                                                    <div class="custom-control custom-radio">
                                                        <input type="radio" name="married_yn" id="married_y" class="custom-control-input" value="Y" @if(@$user->married_yn == 'Y') checked @endif>
                                                        <label class="custom-control-label" for="married_y">기혼</label>
                                                    </div>
                                                    <div class="custom-control custom-radio">
                                                        <input type="radio" name="married_yn" id="married_n" class="custom-control-input" value="N" @if(@$user->married_yn == 'N') checked @endif>
                                                        <label class="custom-control-label" for="married_n">미혼</label>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>결혼기념일</th>
                                            <td>
                                                <div class="flax_box">
                                                    <input type="text" name="married_date" id="married_date" class="form-control form-control-sm" value="{{@$user->married_date}}" onkeyup="onlynum(this)">
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>기념일</th>
                                            <td>
                                                <div class="flax_box">
                                                    <input type="text" name="anniv_date" id="anniv_date" class="form-control form-control-sm" value="{{@$user->anniv_date}}" onkeyup="onlynum(this)">
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>가입경로</th>
                                            <td>
                                                <div class="txt_box">
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>추천인</th>
                                            <td>
                                                <div class="txt_box">
                                                    {{@$user->recommend_id}}
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>승인</th>
                                            <td>
											@if ($type == 'edit')
												<select name="yn" id="yn" class="form-control form-control-sm" style="width:100px;">
													<option value="Y" @if(@$user->yn == 'Y') selected @endif>Y</option>
													<option value="N" @if(@$user->yn == 'N') selected @endif>N</option>
												</select>
											@endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>가입매장</th>
                                            <td>
                                                <div class="form-inline inline_btn_box">
                                                    <input type='hidden' id="store_nm" name="store_nm" value="{{ @$user->store_nm }}">
                                                    <select id="store_no" name="store_no" class="form-control form-control-sm select2-store"></select>
                                                    <a href="javascript:void(0);" class="btn btn-sm btn-outline-primary sch-store"><i class="bx bx-dots-horizontal-rounded fs-16"></i></a>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>가입일</th>
                                            <td>
                                                <div class="txt_box">
                                                    {{@$user->regdate}}
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>최근로그인</th>
                                            <td>
                                                <div class="txt_box">
                                                    {{@$user->lastdate}}
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>로그인횟수</th>
                                            <td>
                                                <div class="txt_box">
                                                    {{@$user->visit_cnt}}
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>최근주문일</th>
                                            <td>
                                                <div class="txt_box">
                                                    {{@$user->ord_date}}
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>구매금액</th>
                                            <td>
                                                <div class="txt_box">
                                                    {{@$user->ord_amt}}
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>구매횟수</th>
                                            <td>
                                                <div class="txt_box">
                                                    {{@$user->ord_cnt}}
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>적립금(가용)</th>
                                            <td>
                                                <div class="txt_box">
                                                    {{@$user->point}}
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>적립금(사용)</th>
                                            <td>
                                                <div class="txt_box">
                                                    {{@$use_point}}
                                                </div>
                                            </td>
                                        </tr>
<!--
                                        <tr>
                                            <th>간이과세자</th>
                                            <td>
                                                <div class="form-inline form-radio-box">
                                                    <div class="custom-control custom-radio">
                                                        <input type="radio" name="taxpayer_yn" id="taxpayer_y" class="custom-control-input" value="Y" @if(@$user->taxpayer_yn == 'Y') checked @endif>
                                                        <label class="custom-control-label" for="taxpayer_y">기혼</label>
                                                    </div>
                                                    <div class="custom-control custom-radio">
                                                        <input type="radio" name="taxpayer_yn" id="taxpayer_n" class="custom-control-input" value="N" @if(@$user->taxpayer_yn == 'N') checked @endif>
                                                        <label class="custom-control-label" for="taxpayer_n">미혼</label>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
//-->
                                        <tr>
                                            <th>회원가입 종류</th>
                                            <td>
                                                <div class="form-inline form-radio-box">
                                                    <div class="custom-control custom-radio">
                                                        <input type="radio" name="type" id="type_n" class="custom-control-input" value="N">
                                                        <label class="custom-control-label" for="type_n">온라인</label>
                                                    </div>
                                                    <div class="custom-control custom-radio">
                                                        <input type="radio" name="type" id="type_b" class="custom-control-input" value="B">
                                                        <label class="custom-control-label" for="type_b">오프라인</label>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>회원그룹</th>
                                            <td>
                                                @if(@$user->out_yn != 'I')
                                                <div class="flax_box inline_btn_box" @if($type==='edit') style="padding-right:95px;"@else style="padding:0px;" @endif>
                                                    <select name="user_group" id="user_group"  class="form-control form-control-sm" style="">
                                                        <option value="">회원그룹</option>
                                                        @foreach($groups as $val)
                                                            <option value="{{$val->id}}">{{$val->val}}</option>
                                                        @endforeach
                                                    </select>
                                                    @if($type==='edit')
                                                        <a href="#" class="btn btn-sm btn-secondary add-group-btn fs-12" style="width:90px;">추가</a>
                                                    @endif
                                                </div>
                                                <div class="form-inline group-list">
                                                    @foreach($user_groups as $val)
                                                        <div class="btn-group mr-1 mt-2 group-{{$val->group_no}}">
                                                            <button type="button" class="btn btn-outline-info text-left" onclick="deleteGroup('{{$val->group_no}}')">{{$val->group_nm}}<i class="bx bx-x"></i></button>
                                                        </div>
                                                    @endforeach
                                                </div>
                                                @endif
                                            </td>
                                        </tr>
                                        @if(@$type == 'edit')
                                        <tr>
                                            <th>블랙리스트</th>
                                            <td>
                                                <div class="form-inline form-radio-box mt-1 mt-sm-0">
                                                    <div class="custom-control custom-radio">
                                                        <input type="radio" name="black_yn" id="black_yn_y" class="custom-control-input" value="Y" @if(@$user->black_yn == 'Y') checked @endif>
                                                        <label class="custom-control-label" for="black_yn_y">Y</label>
                                                    </div>
                                                    <div class="custom-control custom-radio">
                                                        <input type="radio" name="black_yn" id="black_yn_n" class="custom-control-input" value="N"  @if(@$user->black_yn == 'N') checked @endif>
                                                        <label class="custom-control-label" for="black_yn_n">N</label>
                                                    </div>
                                                    <div class="flax_box inline_btn_box">
                                                        <input type="text" name="black_reason" id="black_reason" class="form-control form-control-sm" value="{{@$user->black_reason}}" readonly>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                        @endif
                                        <tr>
                                            <th>메모</th>
                                            <td>
                                                <div class="flax_box">
                                                    <textarea name="memo" id="memo" rows="5" class="form-control form-control-sm" style="width:100%">{{@$user->memo}}</textarea>
                                                </div>
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @if($type == 'edit')
            <div class="card shadow">
                <div class="card-header mb-0">
                    <ul class="nav nav-tabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="tab-nav-1" data-toggle="tab" href="#tab-1" role="tab" aria-controls="send" aria-selected="true">상담</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="tab-nav-2" data-toggle="tab" href="#tab-2" role="tab" aria-controls="list" aria-selected="true">주문</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="tab-nav-3" data-toggle="tab" href="#tab-3" role="tab" aria-controls="list" aria-selected="true">적립금</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="tab-nav-4" data-toggle="tab" href="#tab-4" role="tab" aria-controls="list" aria-selected="true">고객문의</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="tab-nav-5" data-toggle="tab" href="#tab-5" role="tab" aria-controls="list" aria-selected="true">상품Q&A</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="tab-nav-6" data-toggle="tab" href="#tab-6" role="tab" aria-controls="list" aria-selected="true">상품평</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="tab-nav-7" data-toggle="tab" href="#tab-7" role="tab" aria-controls="list" aria-selected="true">클레임</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="tab-nav-8" data-toggle="tab" href="#tab-8" role="tab" aria-controls="list" aria-selected="true">쿠폰내역</button>
                        </li>
                    </ul>
                </div>
				<div class="card-body brtn mt-0 pt-2">
					<div id="counsel-box" class="text-center">
						<div class="table-box mobile text-left mb-2">
							<table class="table incont table-bordered">
								<tr>
									<th>주문번호</th>
									<td>
										<div class="form-inline">
											<input type="text" class="form-control form-control-sm w-100" name="counsel_ord_no" id="counsel_ord_no" />
										</div>
									</td>
								</tr>
								<tr>
									<th>상담내용</th>
									<td>
										<div class="form-inline">
											<textarea name="counsel_content" id="counsel_content" class="form-control p-2 w-100" rows="2" style="resize: none;"></textarea>
										</div>
									</td>
								</tr>
							</table>
						</div>
						<button type="button" class="btn btn-sm btn-outline-primary shadow-sm add-counsel-btn"><i class="fas fa-plus fa-sm mr-1"></i> 상담내용 등록</button>
					</div>
					<div class="tab-pane show active" id="tab-1" role="tabpanel" aria-labelledby="send-tab">
						<div class="card-title">
							<div class="filter_wrap">
								<div class="fl_box">
									<h6 class="m-0 font-weight-bold">총 : <span id="gd-total" class="text-primary">0</span> 건</h6>
								</div>
								<div class="fr_box flax_box grid-show-text">

								</div>
							</div>
						</div>
					</div>
					<div class="table-responsive">
						<div id="div-gd" style="height:calc(100vh - 50vh); width:100%;" class="ag-theme-balham"></div>
					</div>
				</div>
            </div>
        </div>
    </form>
    <form action="" name="user">
        <input type="hidden" name="user_id" value="{{@$user->user_id}}">
    </form>
</div>
<!-- 수정일 경우에만 실행되는 스크립트 -->
<script>
    // 상담 탭 컬럼
    const column1 = [
		{field: "regi_date", headerName: "상담일자", type: "DateTimeType"},
		{field: "ord_no", headerName: "주문번호", width: 140, type: 'HeadOrderNoType' },
		{field: "contents", headerName: "상담내용", width: 369, wrapText: true, autoHeight: true,
			cellRenderer: (params) => params.value?.split("\n").join("<br>") || '',
		},
		{field: "admin_nm", headerName: "처리자", width: 90, cellClass: 'hd-grid-code'},
    ];

    // 주문 탭 컬럼
    const column2 = [
        {field: "ord_date", headerName: "주문일", width:80, pinned:'left'},
        {field: "ord_no", headerName: "주문번호", type:"HeadOrderNoType"},
        {field: "goods_nm", headerName: "상품명", type:"HeadGoodsNameType"},
        {field: "opt", headerName: "옵션"},
        {field: "qty", headerName: "수량", type:"currencyType"},
        {field: "price", headerName: "판매가", type:"currencyType"},
        {field: "pay_type", headerName: "결제방법"},
        {field: "ord_state", headerName: "주문상태", cellStyle:StyleOrdState},
        {field: "clm_state", headerName: "클레임상태", cellStyle:StyleClmState},
        {field: "sale_place", headerName: "판매처", width:100},
        {field: "coupon_nm", headerName: "쿠폰", width:100},
    ];

    // 적립금 탭 컬럼
    const column3 = [
        {field: "ord_no", headerName: "주문번호", type:"HeadOrderNoType", pinned:'left'},
        {field: "point_st", headerName: "상태", width:50, pinned:'left'},
        {field: "point_nm", headerName: "포인트내용"},
        {field: "point", headerName: "결제금액", type:"currencyType"},
        {field: "regi_date", headerName: "지급일자"},
        {field: "expire_day", headerName: "유효기간"},
		{field: "admin_nm", headerName: "처리자",
			cellRenderer: function (params) {
				if (params.value !== undefined) {
					return params.data.admin_nm + ' (' + params.data.admin_id + ')';
				}
			}
		}
    ];

    // 고객문의 탭 컬럼
    const column4 = [
        {
            field: "regi_date",
            headerName: "문의일자",
            cellRenderer: function (params) {
                if (params.value) {
                    return `<a href="#" onclick="openClaimList('${user_id}', '${params.value}')">${params.value}</a>`
                }
            }
        },
        {field: "typenm", headerName: "유형"},
        {field: "subject", headerName: "제목"},
        {field: "ans_state", headerName: "상태"},
        {field: "ans_nm", headerName: "답변자"}
    ];

    // 상품Q&A 탭 컬럼
    const column5 = [
        {
            field: "q_date",
            headerName: "질문일자",
            cellRenderer: function (params) {
                if (params.value) {
                    return `<a href="#" onclick="openQAList('${user_id}', '${params.value}')">${params.value}</a>`
                }
            }
        },
        {field: "goods_nm", headerName: "상품명"},
        {field: "subject", headerName: "질문"},
        {field: "answer_yn", headerName: "답변여부"},
        {field: "admin_nm", headerName: "답변자"}
    ];

    // 상품명 탭 컬럼
    const column6 = [
        {
            field: "img_s_50",
            headerName: "이미지",
            // hide: true,
            cellRenderer: function (params) {
                if (params.value !== undefined && params.value !== "" && params.value !== null) {
                    return '<img src="{{config('shop.image_svr')}}/' + params.value + '" class="img" style="width:30px;height:30px"/>';
                }
            },
            pinned:'left',
            width:100
        },
        {field: "goods_nm", headerName: "상품명", type:"HeadGoodsNameType", pinned:'left'},
        {field: "estimate", headerName: "평점", width:100},
        {field: "best_yn", headerName: "베스트"},
        {field: "buy_yn", headerName: "구매"},
        {
            field: "goods_title",
            headerName: "제목",
			cellRenderer: function(params){
				return "<a href='#' onclick=\"openEstimateShow('"+ params.data.no +"');\">"+ params.value +"</a>";
            }
        },
        {field: "point", headerName: "적립금"},
        {field: "use_yn", headerName: "출력"},
        {field: "cnt", headerName: "조회수", type:"currencyType"},
        {field: "regi_date", headerName: "등록일시"}
    ];

    // 클레임 탭 컬럼
    const column7 = [
        {field: "regi_date", headerName: "일자"},
        {field: "ord_no", headerName: "주문번호", type:"HeadOrderNoType"},
        {field: "cs_form", headerName: "유형"},
        {field: "clm_state", headerName: "클레임상태"},
        {field: "memo", headerName: "클레임내용"},
        {field: "admin_nm", headerName: "처리자"}
    ];

    // 쿠폰내역 탭 컬럼
    const column8 = [
        {field: "coupon_nm", headerName: "쿠폰명", type:"HeadCouponType"},
        {field: "coupon_type", headerName: "쿠폰구분"},
        {field: "coupon_member_use_yn", headerName: "쿠폰사용상태"},
        {field: "ord_no", headerName: "주문번호"},
        {field: "ord_opt_no", headerName: "주문일련번호"},
        {field: "goods_nm", headerName: "상품명"},
        {field: "down_date", headerName: "취득일자"},
        {field: "use_date", headerName: "사용일자"},
        {field: "coupon_no", headerName: "오프라인쿠폰인증번호"}
    ];

    const columns = [column1, column2, column3, column4, column5, column6, column7, column8];
    const urls = ['claim_msg', 'buylist', 'point', 'claim', 'qa', 'estimate', 'claim_list', 'coupon_list'];

    const app = new App('', {gridId: "#div-gd"});
	let gx;
	
	$(document).ready(function() {
		const gridDiv = document.querySelector(app.options.gridId);
		gx = new HDGrid(gridDiv, columns[0], {
			defaultColDef: {
				suppressMenu: true,
				resizable: true,
				sortable: true,
			},
		});

		$('.nav-link').click(function(){
			const num = this.id.split('-')[2] - 1;
			if (num === 0) $("#counsel-box").removeClass('d-none');
			else $("#counsel-box").addClass('d-none');
			Search(num);
		});
		
		Search(0);
	});
	
    $('input[name="black_yn"]').change(function() {
        let value = $(this).val();   

        if(value ===  'Y'){
            $('#black_reason').removeAttr('readonly');
        } else {
            $('#black_reason').attr('readonly', true);
        }
    });

	function Search(num) {
		gx.gridOptions.api.setColumnDefs([]);
		gx.gridOptions.api.setColumnDefs(columns[num]);

        // const data = $('form[name="user"]').serialize();
        gx.Request(`/head/member/mem01/show/search/${urls[num]}/${user_id}`, '', -1, function(res){
            const data = res.head;

            if (data?.clm_amt) {
                const template = [
                    `주문수 : ${data.qty} 개`,
                    `주문금액 : ${numberFormat(data.ord_amt)} 원`,
                    `클레임수 : ${data.clm_qty} 개`,
                    `클레임금액 : ${numberFormat(data.clm_amt)} 원`
                ];

                $('.grid-show-text').html(template.join(', '));
            } else if (data?.use_cnt) {
                $('.grid-show-text').html(`사용할 수 있는 쿠폰 수 : ${numberFormat(data.use_cnt)} 개  `);
            } else {
                $('.grid-show-text').html('');
            }
        });
    };

    function openClaimList(user_id, date) {
        date = date.substr(0, 10).replace(/[.]/g, '-');

        var url = `/head/member/mem20/pop?user_id=${user_id}&date=${date}`;
        window.open(url, "_blank", "toolbar=no,scrollbars=yes,resizable=yes,status=yes,top=500,left=500,width=1024,height=768");
    }

    function openQAList(user_id, date) {
        date = date.substr(0, 10).replace(/[.]/g, '-');

        var url = `/head/member/mem21/pop?user_id=${user_id}&date=${date}`;
        window.open(url, "_blank", "toolbar=no,scrollbars=yes,resizable=yes,status=yes,top=500,left=500,width=1024,height=768");
    }

	function openEstimateShow(no){
		var url = "/head/member/mem22/"+no;
		const product=window.open(url,"_blank","toolbar=no,scrollbars=yes,resizable=yes,status=yes,top=500,left=500,width=1000,height=810");
	}

	// 상담내용등록
	$(".add-counsel-btn").on('click', function (e) {
		e.preventDefault();

		const ord_no = $("#counsel_ord_no").val();
		const content = $("#counsel_content").val();

		if (content.length < 1) return alert("상담내용을 입력해주세요.");
		$.ajax({
			async: true,
			type: 'post',
			url: `/head/member/mem01/user/counsel/${user_id}`,
			data: { ord_no, content },
			success: function (res) {
				if (res.code == 200) Search(0);
				else alert("상담내용 등록 중 오류가 발생했습니다. 다시 시도해주세요.");
			},
			error: function(request, status, error) {
				console.log(request.responseText);
			}
		});
	});
</script>
@endif

<script src="https://t1.daumcdn.net/mapjsapi/bundle/postcode/prod/postcode.v2.js"></script>

<script>

    const NOT_UPDATED_CODE = "NOT";

    function openFindAddress(zipName, addName) {
        new daum.Postcode({
            // 팝업에서 검색결과 항목을 클릭했을때 실행할 코드를 작성하는 부분입니다..
            oncomplete: function(data) {
                $("#" + zipName).val(data.zonecode);
                $("#" + addName).val(data.address);
            }
        }).open();
    }

    function setBirthDay() {
        var form = document.search;
        var oldMaxDay = form.dd.length;

        if (form.yyyy.value == "" || form.mm.value == "") {
            for (var i = oldMaxDay; i >= 1; i--) {
                form.dd.options[i] = null;
            }
        } else {
            var newMaxDay = new Date(new Date(form.yyyy.value, form.mm.value, 1) - 24 * 60 * 60 * 1000).getDate();
            if (oldMaxDay - 1 - newMaxDay > 0) {
                for (var i = oldMaxDay; i > newMaxDay; i--) {
                    form.dd.options[i] = null;
                }
            } else if (oldMaxDay - 1 - newMaxDay < 0) {
                for (var i = oldMaxDay; i <= newMaxDay; i++) {
                    var objOption = document.createElement("option");
                    objOption.text = i;
                    objOption.value = i;

                    form.dd.options.add(objOption);
                }
            }
        }
    }

    function deleteGroup(no) {
        if (confirm("해당 그룹을 삭제하시겠습니까?") === false) return;

        $.ajax({
            async: true,
            type: 'delete',
            url: `/head/member/mem01/user/group/${user_id}`,
            data: { member_group : no },
            success: function (data) {
                alert("그룹이 삭제되었습니다.");
                $(`.group-${no}`).remove();
            },
            error: function(request, status, error) {
                console.log("error")
            }
        });

    }

    
    const Validate = async () => {
        const ff = document.search;

        if (!ff.name.value) {
            alert("회원명을 입력해주세요.");
            ff.name.focus();
            return false;
        }

        // const mailReg = /^[0-9a-zA-Z]([-_\.]?[0-9a-zA-Z])*@[0-9a-zA-Z]([-_\.]?[0-9a-zA-Z])*\.[a-zA-Z]{2,3}$/i;
		//
        // if (!mailReg.test(ff.email.value)) {
        //     alert("이메일을 확인해주세요.");
        //     ff.email.focus();
        //     return false;
        // }

        // const phone_reg = /(^(0(2|3[1-3]|4[1-4]|5[1-5]|6[1-4]))$)|^01(?:0|1|[6-9])$/; // mobile 패턴 추가 (일반전화 없을 시)
		//
        // if (!phone_reg.test(ff.phone1.value)) {
        //     alert("일반전화 앞3자리를 확인해주세요.");
        //     ff.phone1.focus();
        //     return false;
        // }
		//
        // if (!ff.phone2.value) {
        //     alert("일반전화의 중간 번호를 입력해 주세요.");
        //     ff.phone2.focus();
        //     return false;
        // }
		//
        // if (!ff.phone3.value) {
        //     alert("일반전화의 나머지 번호를 입력해 주세요.");
        //     ff.phone3.focus();
        //     return false;
        // }

        const mobile_reg = /^01(?:0|1|[6-9])$/;

        if (!mobile_reg.test(ff.mobile1.value)) {
            alert("휴대전화 앞3자리를 확인해주세요.");
            ff.mobile1.focus();
            return false;
        }

        if (!ff.mobile2.value) {
            alert("휴대전화의 중간 번호를 입력해 주세요.");
            ff.mobile2.focus();
            return false;
        }

        if (!ff.mobile3.value) {
            alert("휴대전화의 나머지 번호를 입력해 주세요.");
            ff.mobile3.focus();
            return false;
        }

		// 2024-01-22 양대성 주석처리
        // if (!ff.zipcode.value) {
        //     alert("우편번호를 입력해주세요.");
        //     openFindAddress('zipcode', 'addr1');
        //     return false;
        // }

        // if (!ff.addr1.value) {
        //     alert("주소를 입력해주세요.");
        //     openFindAddress('zipcode', 'addr1');
        //     return false;
        // }

        // if (!ff.addr2.value) {
        //     alert("나머지 주소를 입력해주세요.");
        //     ff.addr2.focus();
        //     return false;
        // }

        return true;
    };

    const ValidateAdd = async () => {
        var ff = document.search;
        var pattern_1 =  /^[a-zA-Z]+$/;
        var pattern_2 =  /^[0-9]+$/;

        // input 순서대로 validation 진행
        if(ff.user_id.value == ""){
            alert("아이디을 입력하십시오.");
            ff.user_id.focus();
            return false;
        }

        if(id_chk == false){
            alert("아이디 중복체크를 하셔야 합니다.");
            return false;
        }

        if (!ff.pw.value) {
            alert("비밀번호 입력해주세요.");
            ff.pw.focus();
            return false;
        }

        if(pattern_1.test(ff.pw.value)){
            alert("비밀번호는 영문과 숫자가 포함되어야 합니다.");
            ff.pw.focus();
            return false;
        }

        if(pattern_2.test(ff.pw.value)){
            alert("비밀번호는 영문과 숫자가 포함되어야 합니다.");
            ff.pw.focus();
            return false;
        }

        if (!ff.name.value) {
            alert("회원명을 입력해주세요.");
            ff.name.focus();
            return false;
        }

        if (!ff.yyyy.value) {
            alert("[생년월일-년]을 선택해주세요.");
            ff.yyyy.focus();
            return false;
        }

        if (!ff.mm.value) {
            alert("[생년월일-월]을 선택해주세요.");
            ff.mm.focus();
            return false;
        }

        if (!ff.dd.value) {
            alert("[생년월일-일]을 선택해주세요.");
            ff.dd.focus();
            return false;
        }

        if (await Validate() === false) return false;

        if( ff.jumin_chk?.checked ){
            const jumin1_reg = /^(?:[0-9]{2}(?:0[1-9]|1[0-2])(?:0[1-9]|[1,2][0-9]|3[0,1]))$/;
            const jumin2_reg = /^[1-4][0-9]{6}$/;

            if (!jumin1_reg.test(ff.jumin1.value)) {
                alert("주민번호 앞자리를 확인해주세요.");
                ff.jumin1.focus();
                return false;
            }

            if (!jumin2_reg.test(ff.jumin2.value)) {
                alert("주민번호 뒷자리를 확인해주세요.");
                ff.jumin2.focus();
                return false;
            }
        }

        return true;
    };

    //휴면 유저일경우
    if ($('.active-btn').length > 0) {
        $('.active-btn').click(function(e){
            e.preventDefault();

            if(confirm("휴면해제를 하시겠습니까?") === false) return;

            const data = [user_id];

            $.ajax({
                async: true,
                type: 'put',
                url: `/head/member/mem02/active`,
                data: { data },
                success: function (data) {
                    if (data.return_code == 1) {
                        alert("휴면이 해제되었습니다.");
                        location.reload();
                    }
                },
                error: function(request, status, error) {
                    alert("장애가 발생했습니다.\n관리자에게 문의해 주십시오.");
                    console.log("error");
                }
            });
        });
    }

    //회원등록일 경우
    if ($('.add-btn').length > 0) {
        $('.add-btn').click(async function(e){
            e.preventDefault();

            if (await ValidateAdd() === false) return;

            const data = $('form[name="search"]').serialize();

            $.ajax({
                async: true,
                type: 'post',
                url: `/head/member/mem01/user/`,
                data: data,
                success: function (id) {
                    alert("등록되었습니다.");
                    location.href="/head/member/mem01/show/edit/"+id;
                },
                error: function(request, status, error) {
                    console.log("error")
                }
            });
        });

        $('.check-user-btn').click(function(e){
            $.ajax({
                async: true,
                type: 'get',
                url: `/head/member/mem01/check-id/${$('#user_id').val()}`,
                success: function (cnt) {
                    if (cnt > 0) {
                        alert("중복된 아이디입니다.");
                        id_chk = false;
                        return;
                    }

                    alert("사용할 수 있는 아이디입니다.");
                    id_chk = true;
                },
                error: function(request, status, error) {
                    console.log("error")
                }
            });
        });

        $('#user_id').change(function(){
            id_chk = false;
        });
    }

    //회원 수정일 경우
    if ($('.edit-btn').length > 0) {
        //수정
        $('.edit-btn').click(async function(e){
            e.preventDefault();

            if (await Validate() === false) return;

            let data = $('form[name="search"]').serialize();
            let obj_data = $('form[name="search"]').serializeArray()
                .reduce((a,c) => {
                    a[c.name] = c.value;
                    return a;
                }, {});
            data += `&store_chg=${obj_data.store_no !== NOT_UPDATED_CODE}`;

            $.ajax({
                async: true,
                type: 'put',
                url: `/head/member/mem01/user/${user_id}`,
                data: data,
                success: function (id) {
                    alert("수정되었습니다.");
                    location.reload();
                },
                error: function(request, status, error) {
                    console.log("error")
                }
            });
        });

        $('.change-pw-btn').click(function(){
            const pw = $('#pw').val();

            if (!pw) {
                alert('변경할 비밀번호를 입력해주세요.');
                return;
            }

            $.ajax({
                async: true,
                type: 'put',
                url: `/head/member/mem01/pw/${user_id}`,
                data: { pw },
                success: function (id) {
                    alert("수정되었습니다.");
                    location.reload();
                },
                error: function(request, status, error) {
                    console.log("error")
                }
            });
        });

        //`쿠폰지급`
        $('.coupon-btn').click(function(e){
            e.preventDefault();
            openCoupon(user_id);
        });

        //적립금지급
        $('.point-btn').click(function(e){
            e.preventDefault();

            openAddPoint(user_id);
        });

        // 탈퇴
        $('.out-btn').click(function(e){
            e.preventDefault();
            if(!confirm("해당 회원을 탈퇴처리 하시겠습니까?")) return;
            $.ajax({
                async: true,
                type: 'delete',
                url: `/head/member/mem01/user/${user_id}`,
                success: function (res) {
                    alert(res.message);
                    if(res.code === 200) {
                        location.reload();
                    }
                },
                error: function(request, status, error) {
                    alert("에러가 발생했습니다.");
                    console.log(request);
                }
            });
        });

        // sms 발송
        $('.sms-send-btn').click(function(e){
            e.preventDefault();
            openSmsSend(mobile, name);
        });

        //sms 목록
        $('.sms-list-btn').click(function(e){
            e.preventDefault();
            openSmsList(mobile, name);
        });

        //crm 쿠폰
        $('.crm-btn').click(function(e){
            e.preventDefault();
            openCoupon();
        });

        $('.add-group-btn').click(function(e){
            e.preventDefault();

            if (!$('#user_group').val()) {
                alert("회원 그룹을 선택해주세요.");
                return;
            }

            if (confirm("회원 그룹을 추가하시겠습니까?") === false) return;
            $.ajax({
                async: true,
                type: 'post',
                url: `/head/member/mem01/user/group/${user_id}`,
                data: {
                    member_group : $('#user_group').val()
                },
                success: function (no) {
                    alert("추가되었습니다.");
                    const name = $('#user_group option:selected').html();
                    const aTag = `<button type="button" class="btn btn-outline-info text-left" onclick="deleteGroup('${no}')">${name}<i class="bx bx-x"></i></button>`;
                    $('.group-list').append(`<div class="btn-group mr-1 mt-2 group-${no}">${aTag}</div>`);

                },
                error: function(request, status, error) {
                    console.log("error")
                }
            });
        });

        $(document).ready(function() {
            // 매장검색
            $( ".sch-store" ).on("click", function() {
                searchStore.Open(null);
            });

            // 가입매장 초기화
            const store_nm = '{{ @$user->store_nm }}';
            if(store_nm != '') {
                const option = new Option(store_nm, NOT_UPDATED_CODE, true, true);
                $('#store_no').append(option).trigger('change');
            }
        });
    }
</script>
@stop


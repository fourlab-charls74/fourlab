@extends('head_with.layouts.layout-nav')
@section('title','쿠폰')
@section('content')
<div class="container-fluid show_layout py-3">
    <div class="page_tit d-flex align-items-center justify-content-between">
        <div>
            <h3 class="d-inline-flex">쿠폰</h3>
            <div class="d-inline-flex location">
                <span class="home"></span>
                <span>/ 프로모션</span>
                <span>/ 쿠폰생성</span>
            </div>
        </div>
        <div>
            @if ($type == 'add')
                <a href="#" class="d-sm-inline-block btn btn-sm btn-primary shadow-sm save-btn">저장</a>
            @elseif($type == 'edit')
                <a href="#" class="d-sm-inline-block btn btn-sm btn-primary shadow-sm update-btn">수정</a>
                <a href="#" class="d-sm-inline-block btn btn-sm btn-primary shadow-sm delete-btn">삭제</a>
            @endif
        </div>
    </div>
    <form name="detail">
        <input type="hidden" name="goods">
        <input type="hidden" name="ex_goods">
        <input type="hidden" name="com_id">
        <input type="hidden" name="com_rat">
        <input type="hidden" name="src">
        <input type="hidden" name="old_src" value="{{@$coupon->coupon_img}}">
        <input type="hidden" name="del_image_yn" value="N">
        <div class="card_wrap aco_card_wrap">
            <div class="card">
                <div class="card-header mb-0">
                    <a href="#" class="m-0 font-weight-bold">쿠폰 정보</a>
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
                                        <tbody>
                                            <tr>
                                                <th>쿠폰이미지</th>
                                                <td>
                                                    <div class="form-inline inline_input_box">
                                                        <figure class="img-upload-box">
                                                            <div class="img-preview-div">
                                                                @if(@$coupon->coupon_img != "")
                                                                <img src="{{$coupon->coupon_img}}" alt="쿠폰이미지" style="width: 100%;">
                                                                @endif
                                                            </div>
                                                            <div class="text-center my-1">( 370 * 212 px )</div>
                                                            <input type="file" name="file" id="file" class="d-none">
                                                            <div class="text-center">
                                                                <label for="file" class="d-sm-inline-block btn btn-sm btn-primary shadow-sm upload-btn">등록</label>
                                                                <a href="#" class="btn btn-sm btn-primary shadow-sm cancel-btn">취소</a>
                                                            </div>
                                                        </figure>
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th class="required">쿠폰명</th>
                                                <td>
                                                    <div class="flax_box">
                                                        <input type="text" name="coupon_nm" id="coupon_nm" class="form-control form-control-sm" value="{{@$coupon->coupon_nm}}">
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th>URL</th>
                                                <td>
                                                    <div class="flax_box">
                                                        <input type="text" name="coupon_url" id="coupon_url" class="form-control form-control-sm" value="{{@$coupon->coupon_url}}">
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th class="required">구분</th>
                                                <td>
                                                    <div class="form-inline form-radio-box">
                                                        @foreach($types as $key => $val)
                                                        <div class="custom-control custom-radio">
                                                            <input type="radio" name="coupon_type" id="coupon_type_{{$key}}" class="custom-control-input" value="{{$key}}" @if(@$coupon->coupon_type == $key) checked @endif>
                                                            <label class="custom-control-label" for="coupon_type_{{$key}}">{{$val}}</label>
                                                        </div>
                                                        @endforeach
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th>발행시점</th>
                                                <td>
                                                    <div class="flax_box">
                                                        <select name="pub_time" id="pub_time" class="form-control form-control-sm" @if(@$coupon->coupon_type !="C") disabled @endif>
                                                            <option value="">선택</option>
                                                            @foreach($pub_kinds as $val)
                                                                <option value="{{$val->code_id}}" @if(@$coupon->pub_time == $val->code_id) selected @endif>{{$val->code_val}}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                    <div class="txt_box mt-1">※ 구분이 CRM 쿠폰 일 경우에만 발행 시점이 적용됩니다.</div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th class="required">발행기간</th>
                                                <td>
                                                    <div class="form-inline">
                                                        <div class="docs-datepicker form-inline-inner input_box">
                                                            <div class="input-group">
                                                                <input type="text" class="form-control form-control-sm docs-date" name="pub_fr_date" id="pub_fr_date" value="{{@$coupon->pub_fr_date}}" autocomplete="off" disable>
                                                                <div class="input-group-append">
                                                                    <button type="button" class="btn btn-outline-secondary docs-datepicker-trigger p-0 pl-2 pr-2" disable>
                                                                    <i class="fa fa-calendar" aria-hidden="true"></i>
                                                                    </button>
                                                                </div>
                                                            </div>
                                                            <div class="docs-datepicker-container"></div>
                                                        </div>
                                                        <span class="text_line">~</span>
                                                        <div class="docs-datepicker form-inline-inner input_box">
                                                            <div class="input-group">
                                                                <input type="text" class="form-control form-control-sm docs-date" name="pub_to_date" id="pub_to_date"  value="{{@$coupon->pub_to_date}}" autocomplete="off">
                                                                <div class="input-group-append">
                                                                    <button type="button" class="btn btn-outline-secondary docs-datepicker-trigger p-0 pl-2 pr-2">
                                                                    <i class="fa fa-calendar" aria-hidden="true"></i>
                                                                    </button>
                                                                </div>
                                                            </div>
                                                            <div class="docs-datepicker-container"></div>
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th class="required">유효기간</th>
                                                <td>
                                                    <div>
                                                        <div class="form-inline form-radio-box">
                                                            <div class="custom-control custom-radio">
                                                                <?php 
                                                                    // var_dump($coupon) 
                                                                ?>
                                                                <input type="radio" name="use_date_type" id="use_date_type_S" class="custom-control-input" value="S" @if(@$coupon->use_date_type != 'P') checked @endif />
                                                                <label class="custom-control-label" for="use_date_type_S">설정일 기준</label>
                                                            </div>
                                                            <div class="custom-control custom-radio">
                                                                <input type="radio" name="use_date_type" id="use_date_type_P" class="custom-control-input" value="P" @if(@$coupon->use_date_type == 'P') checked @endif />
                                                                <label class="custom-control-label" for="use_date_type_P">발급일 기준</label>
                                                            </div>
                                                        </div>
                                                        <div style="display: flex" class="my-1 use_date_p" @if(!isset($coupon->use_date_type) || @$coupon->use_date_type == 'S') style="display:none" @endif>
                                                            <div class="txt_box">발급일 ~&nbsp;</div>
                                                            <input 
                                                                type="text" 
                                                                id="use_date" 
                                                                name="use_date" 
                                                                value="@if(@$coupon->use_date_type == 'P') {{@$coupon->use_date}} @endif" 
                                                                maxlength="4"
                                                                class="form-control form-control-sm"
                                                                style="width:50px;text-align:right;" 
                                                                onkeyup="currency(this);"
                                                            />
                                                            <div class="txt_box">&nbsp;일 까지</div>
                                                        </div>
                                                        <div class="my-1 use_date_s" style="display:@if(@$coupon->use_date_type == 'P') none @else block @endif">
                                                            <div class="form-inline">
                                                                <div class="docs-datepicker form-inline-inner input_box">
                                                                    <div class="input-group">
                                                                        <input type="text" class="form-control form-control-sm docs-date" name="use_fr_date" id="use_fr_date" value="{{@$coupon->use_fr_date}}" autocomplete="off" disable>
                                                                        <div class="input-group-append">
                                                                            <button type="button" class="btn btn-outline-secondary docs-datepicker-trigger p-0 pl-2 pr-2" disable>
                                                                            <i class="fa fa-calendar" aria-hidden="true"></i>
                                                                            </button>
                                                                        </div>
                                                                    </div>
                                                                    <div class="docs-datepicker-container"></div>
                                                                </div>
                                                                <span class="text_line">~</span>
                                                                <div class="docs-datepicker form-inline-inner input_box">
                                                                    <div class="input-group">
                                                                        <input type="text" class="form-control form-control-sm docs-date" name="use_to_date" id="use_to_date"  value="{{@$coupon->use_to_date}}" autocomplete="off">
                                                                        <div class="input-group-append">
                                                                            <button type="button" class="btn btn-outline-secondary docs-datepicker-trigger p-0 pl-2 pr-2">
                                                                            <i class="fa fa-calendar" aria-hidden="true"></i>
                                                                            </button>
                                                                        </div>
                                                                    </div>
                                                                    <div class="docs-datepicker-container"></div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="flax_box">
                                                            <div class="form-inline form-check-box">
                                                                <div class="custom-control custom-checkbox">
                                                                    <input type="checkbox" name="use_date_alarm_yn" id="use_date_alarm_y" class="custom-control-input" value="Y" @if(@$coupon->use_date_alarm_yn == 'Y') checked @endif />
                                                                    <label class="custom-control-label" for="use_date_alarm_y">유효기간 알림 사용,</label>
                                                                </div>
                                                            </div>
                                                            <label for="use_date_alarm_day" class="flax_box">
                                                                <div class="txt_box mr-1">유효기간 알림일자 :</div>
                                                                <input 
                                                                    type="text" 
                                                                    name="use_date_alarm_day"
                                                                    id="use_date_alarm_day"
                                                                    class="form-control form-control-sm" 
                                                                    value="{{@$coupon->use_date_alarm_day}}" 
                                                                    onkeyup="currency(this);" 
                                                                    maxlength="3" 
                                                                    style="width: 50px; text-align: right;" 
                                                                />
                                                                <div class="txt_box pl-1">일</div>
                                                            </label>
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th class="required">복수발급</th>
                                                <td>
                                                    <div class="form-inline form-radio-box">
                                                        @foreach($pub_dup_yn as $val)
                                                        <div class="custom-control custom-radio">
                                                            <input type="radio" name="pub_dup_yn" id="dup_yn_{{$val->code_id}}" class="custom-control-input" value="{{$val->code_id}}" @if(@$coupon->pub_dup_yn == $val->code_id) checked @endif />
                                                            <label class="custom-control-label" for="dup_yn_{{$val->code_id}}">{{$val->code_val}}</label>
                                                        </div>
                                                        @endforeach
                                                    </div>
                                                    <div class="txt_box"><span>※ 본 쿠폰을 1회이상 발급 받을 수 있습니다.</span></div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th>발행방법</th>
                                                <td>
                                                    <div class="flax_box">
                                                        <div class="form-inline form-radio-box">
                                                            <div class="custom-control custom-radio">
                                                                <input type="radio" name="pub_type" id="pub_type_n" class="custom-control-input" value="N" @if(@$coupon->pub_type == "N") checked @endif @if(@$coupon->coupon_type != "E") disabled @endif/>
                                                                <label class="custom-control-label" for="pub_type_n">기본</label>
                                                            </div>
                                                            <div class="custom-control custom-radio">
                                                                <input type="radio" name="pub_type" id="pub_type_m" class="custom-control-input" value="M" @if(@$coupon->pub_type == "M") checked @endif @if(@$coupon->coupon_type != "E") disabled @endif/>
                                                                <label class="custom-control-label" for="pub_type_m">매월 1회</label>
                                                            </div>
                                                            <div class="custom-control custom-radio">
                                                                <input type="radio" name="pub_type" id="pub_type_d" class="custom-control-input" value="D" @if(@$coupon->pub_type == "D") checked @endif @if(@$coupon->coupon_type != "E") disabled @endif/>
                                                                <label class="custom-control-label" for="pub_type_d">매월</label>
                                                            </div>
                                                        </div>
                                                        <input 
                                                        type="text" 
                                                        name="pub_day" 
                                                        value='{{ @$coupon->pub_type == 'D' ? @$coupon->pub_day : '' }}'
                                                        maxlength="2" 
                                                        class="form-control form-control-sm mr-1"
                                                        style="width:50px;text-align:center;" 
                                                        onclick="changeRadio(document.detail.pub_type, 2);"
                                                        onkeyup="currency(this)"
                                                        @if(@$coupon->coupon_type != "E") disabled @endif />
                                                        <span>일</span>
                                                        <div class="form-inline form-radio-box ml-1">
                                                            <div class="custom-control custom-radio">
                                                                <input type="radio" name="pub_type" id="pub_type_w" class="custom-control-input" value="W" @if(@$coupon->pub_type == "W") checked @endif @if(@$coupon->coupon_type != "E") disabled @endif/>
                                                                <label class="custom-control-label" for="pub_type_w">매주</label>
                                                            </div>
                                                        </div>
                                                        <select name="pub_dayofweek" class="form-control form-control-sm" onclick="changeRadio(document.detail.pub_type, 3);" style="width:110px;" >
                                                            <option value="0" @if(@$coupon->pub_type == "W" && @$coupon->pub_day == '0') selected @endif>일요일</option>
                                                            <option value="1" @if(@$coupon->pub_type == "W" && @$coupon->pub_day == '1') selected @endif>월요일</option>
                                                            <option value="2" @if(@$coupon->pub_type == "W" && @$coupon->pub_day == '2') selected @endif>화요일</option>
                                                            <option value="3" @if(@$coupon->pub_type == "W" && @$coupon->pub_day == '3') selected @endif>수요일</option>
                                                            <option value="4" @if(@$coupon->pub_type == "W" && @$coupon->pub_day == '4') selected @endif>목요일</option>
                                                            <option value="5" @if(@$coupon->pub_type == "W" && @$coupon->pub_day == '5') selected @endif>금요일</option>
                                                            <option value="6" @if(@$coupon->pub_type == "W" && @$coupon->pub_day == '6') selected @endif>토요일</option>
                                                        </select>
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th class="required">발행금액</th>
                                                <td>
                                                    <div class="form-inline" style="max-width:200px;">
                                                        <input type="hidden" name="coupon_amt" id="coupon_amt">
                                                        <input type="hidden" name="coupon_per" id="coupon_per">
                                                        <div class="form-inline-inner input_box" style="width:70%;">
                                                            <div class="form-group mb-0">
                                                                <input 
                                                                    type="text" 
                                                                    id="coupon_amt_values"
                                                                    name="coupon_amt_values"
                                                                    value="@if(@$coupon->coupon_amt_kind == 'W'){{ number_format(@$coupon->coupon_amt)}}@elseif(@$coupon->coupon_amt_kind == 'P'){{@$coupon->coupon_per}}@endif"
                                                                    maxlength="8" 
                                                                    class="form-control form-control-sm text-right"
                                                                    onkeyup="currency(this);"
                                                                >
                                                            </div>
                                                        </div>
                                                        <div class="form-inline-inner input_box" style="width:28%;margin-left:2%;">
                                                            <div class="form-group mb-0">
                                                            <select name="coupon_amt_kind" id="coupon_amt_kind" class="form-control form-control-sm">
                                                                <option value="P" @if(@$coupon->coupon_amt_kind != 'W') selected @endif>%</option>
                                                                <option value="W" @if(@$coupon->coupon_amt_kind == 'W') selected @endif>원</option>
                                                            </select>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th class="required">발행수</th>
                                                <td>
                                                    <div class="form-inline form-radio-box">
                                                        <div class="custom-control custom-radio">
                                                            <input type="radio" name="ck_pub_cnt_kind" id="ck_pub_cnt_kind-1" class="custom-control-input" value="-1" @if(@$coupon->pub_cnt < 0 || !isset($coupon->pub_cnt)) checked @endif/>
                                                            <label class="custom-control-label" for="ck_pub_cnt_kind-1">무제한</label>
                                                        </div>
                                                        <div class="custom-control custom-radio">
                                                            <input type="radio" name="ck_pub_cnt_kind" id="ck_pub_cnt_kind1" class="custom-control-input" value="1" @if(@$coupon->pub_cnt > 0) checked @endif/>
                                                            
                                                        <label class="custom-control-label" for="ck_pub_cnt_kind1"><input 
                                                            type="text" 
                                                            id="pub_cnt" 
                                                            name="pub_cnt" 
                                                            style="width:80px;" 
                                                            class="form-control form-control-sm ml-1 text-right mr-1"
                                                            maxlength="6" 
                                                            
                                                            value="@if(@$coupon->pub_cnt > 0){{number_format(@$coupon->pub_cnt)}}@endif"
                                                            
                                                            onkeyup="currency(this);" 
                                                            onclick="changeRadio(document.detail.ck_pub_cnt_kind, 1);"
                                                        >매</label>
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th class="required">쿠폰번호</th>
                                                <td>
                                                    <div class="form-inline form-radio-box">
                                                        <div class="custom-control custom-radio">
                                                            <input type="radio" name="serial_yn" id="serial_n" class="custom-control-input" value="N" @if(@$coupon->serial_yn != 'Y') checked @endif />
                                                            <label class="custom-control-label" for="serial_n">사용안함</label>
                                                        </div>
                                                        <div class="custom-control custom-radio">
                                                            <input type="radio" name="serial_yn" id="serial_y" class="custom-control-input" value="Y" @if(@$coupon->serial_yn == 'Y') checked @endif />
                                                            <label class="custom-control-label" for="serial_y">사용</label>
                                                        </div>
                                                        <select name="serial_dup_yn" id="serial_dup_yn" class="form-control form-control-sm" style="width:100px;">
                                                            <option value="Y" @if(@$coupon->serial_dup_yn != 'N') selected @endif>동일</option>
                                                            <option value="N" @if(@$coupon->serial_dup_yn == 'N') selected @endif>개별</option>
                                                        </select>
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th class="required">판매가격</th>
                                                <td>
                                                    <div class="form-inline form-radio-box">
                                                        <div class="custom-control custom-radio">
                                                            <input type="radio" name="price_yn" id="price_n" class="custom-control-input" value="N" @if(@$coupon->price_yn == 'N' or $type == 'add') checked @endif/>
                                                            <label class="custom-control-label" for="price_n">모두</label>
                                                        </div>
                                                        <div class="custom-control custom-radio">
                                                            <input type="radio" name="price_yn" id="price_y" class="custom-control-input" value="Y" @if(@$coupon->price_yn == 'Y') checked @endif/>
                                                            <label class="custom-control-label" for="price_y">
                                                                <div class="form-inline">
                                                                    <div class="form-inline-inner input_box" style="width:calc(50% - 55px);">
                                                                        <div class="form-group mb-0">
                                                                            <input 
                                                                                type="text"
                                                                                id="low_price"
                                                                                name="low_price"
                                                                                class="form-control form-control-sm"
                                                                                style="max-width:80px;text-align:right;"
                                                                                value="{{number_format(@$coupon->low_price)}}"
                                                                                onkeyup="currency(this)"
                                                                                onclick="changeRadio(document.detail.price_yn, 1)"
                                                                            />
                                                                        </div>
                                                                    </div>
                                                                    <span class="txt_line" style="width:55px;">원 이상 ~</span>
                                                                    <div class="form-inline-inner input_box" style="width:calc(50% - 50px);">
                                                                        <div class="form-group mb-0">
                                                                            <input 
                                                                                type="text" 
                                                                                id="high_price" 
                                                                                name="high_price" 
                                                                                class="form-control form-control-sm" 
                                                                                style="max-width:80px;text-align:right;" 
                                                                                value="{{number_format(@$coupon->high_price)}}"
                                                                                onkeyup="currency(this)"
                                                                                onclick="changeRadio(document.detail.price_yn, 1)"
                                                                            />
                                                                        </div>
                                                                    </div>
                                                                    <span class="txt_line" style="width:50px;">원 이하</span>
                                                                </div>
                                                            </label>
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th class="required">사용여부</th>
                                                <td>
                                                    <div class="form-inline form-radio-box">
                                                        <div class="custom-control custom-radio">
                                                            <input type="radio" name="use_yn" id="use_y" class="custom-control-input" value="Y" @if(@$coupon->use_yn != 'N') checked @endif />
                                                            <label class="custom-control-label" for="use_y">사용</label>
                                                        </div>
                                                        <div class="custom-control custom-radio">
                                                            <input type="radio" name="use_yn" id="use_n" class="custom-control-input" value="N" @if(@$coupon->use_yn == 'N') checked @endif />
                                                            <label class="custom-control-label" for="use_n">미사용</label>
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th class="required">사용가능대상</th>
                                                <td>
                                                    <div class="form-inline form-radio-box">
                                                        <div class="custom-control custom-radio">
                                                            <input type="radio" name="coupon_apply" id="coupon_apply_ag" class="custom-control-input" value="AG" @if(@$coupon->coupon_apply != 'SG') checked @endif onclick='setAGState(true)' />
                                                            <label class="custom-control-label" for="coupon_apply_ag">전체상품</label>
                                                        </div>
                                                        <div class="custom-control custom-radio">
                                                            <input type="radio" name="coupon_apply" id="coupon_apply_sg" class="custom-control-input" value="SG" @if(@$coupon->coupon_apply == 'SG') checked @endif onclick='setAGState(false)' />
                                                            <label class="custom-control-label" for="coupon_apply_sg">상품</label>
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
            <div class="card">
                <div class="card-header mb-0 d-flex align-items-center justify-content-between">
                    <a href="#" class="m-0 font-weight-bold">
                        {{-- @if(@$coupon->coupon_apply == 'SG' or $type == 'add')
                            상품 총 <span id="gx1-total" class="text-primary">0</span> 건
                        @else
                            제외상품 총 <span id="gx1-total" class="text-primary">0</span> 건
                        @endif --}}
                        <span id="AG_or_SG">상품</span> 총 <span id="gx1-total" class="text-primary">0</span> 건
                    </a>
                    <div class="fr_box">
                        <button class="btn btn-sm btn-secondary prd-add-btn">상품추가</button>
                        <button href="#" class="btn btn-sm btn-secondary prd-del-btn">상품삭제</button>
                    </div>
                </div>
                <div class="card-body brtn mx-0">
                    <div class="table-responsive mt-1">
                        <div id="grid-gd1" style="height:300px; width:100%;" class="ag-theme-balham"></div>
                        <div style="color:red" class="mt-1">※ 상품을 추가, 삭제한 후 저장 버튼을 클릭해야만 쿠폰정보에 반영됩니다.</div>
                    </div>
                </div>
            </div>
            <div class="card">
                <div class="card-header mb-0 d-flex align-items-center justify-content-between">
                    <a href="#" class="m-0 font-weight-bold">
                        정산률
                    </a>
                    <div class="flax_box">
                        ※선택한 업체 일괄 변경 : 업체 정산비율
                        <input type="text" class="form-control form-control-sm" name="com_rat_all" id="com_rat_all" onkeyup="currency(this);" value="50" style="width:40px; text-align:right">%
                        <button class="btn btn-sm btn-secondary change-ratio-btn">비율변경</button>
                    </div>
                </div>
                <div class="card-body brtn mx-0">
                    <div class="table-responsive mt-1">
                        <div id="grid-gd2" style="height:120px; width:100%;" class="ag-theme-balham"></div>
                        <div style="color:red" class="mt-1">※ 업체 정산비율을 수정한 후 저장 버튼을 클릭해야만 쿠폰정보에 반영됩니다.</div>
                    </div>
                </div>
            </div>
		</div>
    </form>
</div>
<style>
    .img-upload-box{
        width:100%;
        max-width:370px;
    }
    .img-upload-box .img-preview-div{
        width:100%;
        min-height:212px;
        border:1px solid #ccc;
        overflow:hidden;
    }
    .flax_box label{
        margin:0 3px;
    }
</style>
<script>
    let gx1;
    let gx2;

    const type          = '{{$type}}';
    const coupon_no     = '{{$coupon_no}}';
    const searchType    = { AG : 'exProduct', SG : 'product', SC : 'company' };
    var getRadioValue   = (name) => $(`[name=${name}]:checked`).val();

    let goodsData   = null;
    let comData     = null;

    let savedComData = [];

    const validatePhoto = (target) => {
    if (target?.length === 0) {
        alert("업로드할 이미지를 선택해주세요.");
        return false;
    }

    if (!/(.*?)\.(jpg|jpeg|png|gif|JPG|JPEG|PNG|GIF)$/i.test(target[0].name)) {
        alert("이미지 형식이 아닙니다.");
        return false;
    }

    return true;
    }

    const drawImage = (e) => {
        var image = new Image();

        image.src = e.target.result;
        image.className = 'img-preview';
        image.width = $('.img-upload-box .img-preview-div').width();

        $('.img-upload-box .img-preview-div').append(image);

        $('[name=del_image_yn]').val("Y");
    }

    const changeRadio = (obj,idx) => {
        if(! obj[idx].checked) obj[idx].checked = true;
    }

    const add = (row) => {
        let ori_rows = gx1.getRows();
        if(ori_rows.filter(r => r.goods_no === row.goods_no).length > 0) return;
        
        gx1.gridOptions.api.updateRowData({add: [row]});
        $('#gx1-total').html(gx1.gridOptions.api.getDisplayedRowCount());
    }

    const setCompany = (rows, type) => {
        if($('[name=coupon_apply]:checked').val() === "AG") return;
        
        const ori_coms = gx2.getRows();

        if(type === 'add') {
            const add_coms = rows
                .filter(row => 
                    row.com_type === 2 && ori_coms.filter(c => c.com_id === row.com_id).length < 1
                ).reduce((a, c) => 
                    a.filter(n => n.com_id === c.com_id).length < 1 ? a.concat(c) : a
                , []);
            add_coms.forEach(com => {
                gx2.addRows([{
                    'c': '',
                    'com_id': com.com_id,
                    'com_nm': com.com_nm,
                    'com_rat': 50,
                    'ut': '',
                }]);
            });
        } else if(type === 'delete') {
            const left_prds = gx1.getRows();
            let del_coms = rows.filter(row => 
                left_prds.filter(p => p.com_id === row.com_id).length < 1
            );
            del_coms = ori_coms.filter(com => 
                del_coms.filter(d => d.com_id === com.com_id).length > 0
            );

            del_coms.forEach(function(row){
                gx2.gridOptions.api.updateRowData({remove: [row]});
            });
        }
    }

    const validate = () => {
        if ($('#coupon_nm').val() == "")
        {
            alert("쿠폰명을 입력해 주십시오.");
            $('#coupon_nm').focus();
            return false;
        }

        if (getRadioValue('coupon_type') == undefined)
        {
            alert("쿠폰구분을 선택해 주십시오.");
            return false;
        }

        if ($('#pub_fr_date').val() == "")
        {
            alert("쿠폰 발행 시작 기간을 입력해 주십시오.");
            $('#pub_fr_date').focus();
            return false;
        }

        if ($('#pub_to_date').val() == "")
        {
            alert("쿠폰 발행 종료 기간을 입력해 주십시오.");
            $('#pub_to_date').focus();
            return false;
        }

        //if (getRadioValue('use_date_type') == "S"){
            if ($('#use_fr_date').val() == "")
            {
                alert("쿠폰 유효 시작 기간을 입력해 주십시오.");
                return false;
            }

            if ($('#use_to_date').val() == "")
            {
                alert("쿠폰 유효 종료 기간을 입력해 주십시오.");
                return false;
            }
        //} else {
        //    if ($('#use_date').val() == "")
        //    {
        //        alert("쿠폰 유효 기간을 입력해 주십시오.");
        //        $('#use_date').focus();
        //        return false;
        //    }
        //}

        if (getRadioValue('pub_dup_yn') == undefined)
        {
            alert("복수발급 여부를 선택해 주십시오.");
            return false;
        }
        
        if (getRadioValue('ck_pub_cnt_kind') == -1) {
            $('#pub_cnt').val("");
            if ( getRadioValue('serial_yn') == "Y" && $('#serial_dup_yn').val() === 'N')
            {
                alert('쿠폰발행수가 무제한일 경우 쿠폰번호는 동일하게 사용할 수 없습니다.');
                return false;
            }
        }else if (getRadioValue('ck_pub_cnt_kind') == 1){
            if($('#pub_cnt').val() == "")
            {
                alert('쿠폰 발행수를 입력해 주십시오.');
                $('#pub_cnt').focus();
                return false;
            }
        }

        if (!$('#coupon_amt_values').val())
        {
            alert('발행금액을 입력해 주십시오.');
            $('#coupon_amt_values').focus();
            return false;
        }

        // 쿠폰 할인가
        console.log($('#coupon_amt_kind').val());
        if ($('#coupon_amt_kind').val() == "P")
        {
            if (unComma($('#coupon_amt_values').val()) > 100)
            {
                alert('쿠폰할인률은 100%넘길 수 없습니다.');
                $('#coupon_amt_values').focus();
                return false;
            }

            $('#coupon_amt').val("");
            $('#coupon_per').val($('#coupon_amt_values').val());
        }
        else if ($('coupon_amt_kind').val() == "W")
        {
            $('#coupon_amt').val($('#coupon_amt_values').val());
            $('#coupon_per').val("");
        }

        if (getRadioValue('price_yn') == 'Y')
        {
            if ($('#low_price').val() == "")
            {
                alert('판매가 최소 범위를 입력해 주십시오.');
                $('#low_price').focus();
                return false;
            }

            if ($('#high_price').val() == "")
            {
                alert('판매가 최대 범위를 입력해 주십시오.');
                $('#high_price').focus();
                return false;
            }
        }

        return true;
    }

    const getGoodsNo = (goods) => `${goods.goods_no}|${goods.goods_sub}`;

    const createSaveData = () => {
        const com_id_array = [];
        const com_rat_array = [];

        const goods_array = [];
        const ex_goods_array = [];

        const target = getRadioValue('coupon_apply') == 'SG' ? goods_array : ex_goods_array;
        
        gx1.gridOptions.api.forEachNode(node => target.push(getGoodsNo(node.data)));

        console.log(goods_array, ex_goods_array, target);

        gx2.gridOptions.api.forEachNode(node => {
            const com = node.data;
            com_id_array.push(com.com_id);
            com_rat_array.push(com.com_rat);
        });

        $('[name="goods"]').val(goods_array.join('^'));
        $('[name="ex_goods"]').val(ex_goods_array.join('^'));

        $('[name="com_id"]').val(com_id_array.join('|'));
        $('[name="com_rat"]').val(com_rat_array.join('|'));
        
        if ($('.img-preview').attr('src')) {
            document.detail.src.value = $('.img-preview').attr('src');
        }
    }

    function goodsCallback(data) {
        add(data);
        setCompany(data, 'add');
    }

    function multiGoodsCallback(datas) {
        datas.forEach(function(data){
            add(data);
        });
        setCompany(datas, 'add');
    }

    const columns1 = [
        {
            headerName: '',
            headerCheckboxSelection: true,
            checkboxSelection: true,
            width:28
        },
        {field:"goods_no", headerName:"상품코드", width:90},
        {field:"style_no", headerName:"스타일넘버", width:100},
        {field:"com_nm", headerName:"업체", width:100},
        {field:"brand_nm", headerName:"브랜드", width:100},
        {field:"goods_nm", headerName:"상품명", type:"HeadGoodsNameType"},
        {field:"sale_stat_cl", headerName:"상품상태", type:'GoodsStateType'},
        { width:"auto" }
    ];

    const columns2 = [
        {
            headerName: '',
            headerCheckboxSelection: true,
            checkboxSelection: true,
            width:28
        },
        {field:"com_nm", headerName:"업체명", width: 150},
        {field:"com_rat", headerName:"업체정산(%)", cellStyle: { 'text-align' : 'right' }},
        {field:"ut", headerName:"최종수정일시", width:140},
        { width:"auto" }
    ];

    const pApp1 = new App('', {gridId: "#grid-gd1"});
    const gridDiv1 = document.querySelector(pApp1.options.gridId);
    gx1 = new HDGrid(gridDiv1, columns1);

    const pApp2 = new App('', {gridId: "#grid-gd2"});
    const gridDiv2 = document.querySelector(pApp2.options.gridId);
    gx2 = new HDGrid(gridDiv2, columns2);

    //쿠폰 수정 버튼 클릭이벤트
    $('.update-btn').click(function(){
        if (validate() === false) return;
        if (confirm('해당내용을 수정하시겠습니까?') === false) return;
        
        createSaveData();

        const data = $('form[name="detail"]').serialize();
        
        $.ajax({    
            type: "put",
            url: `/head/promotion/prm10/${coupon_no}`,
            contentType: "application/x-www-form-urlencoded; charset=utf-8",
            data: data,
            success: function(data) {
                alert("쿠폰정보가 수정되었습니다.");
                location.reload();
            }
        });
    });

    //쿠폰 등록 버튼 클릭이벤트
    $('.save-btn').click(function(){
        if (validate() === false) return;
        if (confirm('해당내용을 저장하시겠습니까?') === false) return;

        createSaveData();

        const data = $('form[name="detail"]').serialize();
        
        $.ajax({    
            type: "post",
            url: '/head/promotion/prm10/',
            contentType: "application/x-www-form-urlencoded; charset=utf-8",
            data: data,
            success: function(data) {
                alert("쿠폰이 등록되었습니다.");
                window.opener.Search();
                location.href = `/head/promotion/prm10/show/edit/${data.coupon_no}`;
            }
        });
    });

    //쿠폰 삭제 버튼 클릭이벤트
    $('.delete-btn').click(function() {
        if (confirm('해당쿠폰을 삭제하시겠습니까?') === false) return;
        
        $.ajax({    
            type: "delete",
            url: `/head/promotion/prm10/${coupon_no}`,
            success: function(data) {
                alert("삭제되었습니다.");
                opener?.Search?.();
                window.close();
            }
        });
    });

    $('[name=coupon_apply]').change(function() {
        if (coupon_no) SearchGxGoods(this.value);

        $('.product-txt').html(this.value === "SG" ? "상품" : "제외 상품");

        if(this.value === "AG") {
            let rows = gx2.getRows();
            savedComData = rows;
            rows.forEach(row => gx2.gridOptions.api.updateRowData({remove: [row]}));
        } else if(this.value === "SG") {
            savedComData.forEach(row => gx2.gridOptions.api.updateRowData({add: [row]}));
        }
    });

    // 상품삭제
    $('.prd-del-btn').click(function(e){
        e.preventDefault();

        const rows = gx1.getSelectedRows();

        if(rows.length === 0) {
            alert("삭제할 쿠폰을 선택해주세요.");
            return;
        }

        rows.forEach(function(row){
            gx1.gridOptions.api.updateRowData({remove: [row]});
        });

        $('#gx1-total').html(gx1.gridOptions.api.getDisplayedRowCount());

        setCompany(rows, 'delete');
    });

    $('.prd-add-btn').click(function(e){
        e.preventDefault();

        const url=`/head/api/goods/show`;
        window.open(url,"_blank","toolbar=no,scrollbars=yes,resizable=yes,status=yes,top=500,left=500,width=1200,height=800");
    });

    $('.change-ratio-btn').click(function(e){
        e.preventDefault();

        const rows = gx2.getSelectedRows();

        if ( rows.length == 0 ) {
            alert('정산비율을 변경할 업체를 선택해 주십시오.');
            return;
        }

        rows.forEach(function(row){
            row.com_rat = $('#com_rat_all').val();
            gx2.gridOptions.api.updateRowData({ update: [row]});
        })
    });


    $('#file').change(function(){
        $('.img-upload-box .img-preview-div').html('');
        if (validatePhoto(this.files) === false) return; 
        
        var fr = new FileReader();

        fr.onload = drawImage;
        fr.readAsDataURL(this.files[0]);
    });


    $('.cancel-btn').click(function(){
        $('.img-preview-div').html('');
        $('[name=del_image_yn]').val("Y");
    });

    $('[name=use_date_type]').change(function(){
        $('.use_date_p').css('display', this.value === 'P' ? 'flex' : 'none');
        $('.use_date_s').css('display', this.value === 'S' ? 'flex' : 'none');
    });

    $('[name=coupon_type]').change(function(){
        $('#pub_time').attr('disabled', this.value !== "C");
        $('[name=pub_type]').attr("disabled", this.value != "E");
        $('[name=pub_day]').attr("disabled", this.value != "E");
        $('[name=pub_dayofweek]').attr("disabled", this.value != "E");

        if (this.value != "F"){
            $('[name=serial_yn]')[0].disabled = false;
            $('[name=ck_pub_cnt_kind]')[0].disabled = false;
            $('pub_cnt').readOnly = false;

        } else if (this.value == "F"){
            $('[name=serial_yn]')[1].checked = true;
            $('[name=serial_yn]')[0].disabled = true;

            $('[name=ck_pub_cnt_kind]')[1].checked = true;
            $('[name=ck_pub_cnt_kind]')[0].disabled = true;

            $('pub_cnt').readOnly = true;

            if(type != "edit") {
                $('pub_cnt').value = 1;
            } else {
                $('pub_cnt').readOnly = true;
            }
        }
    });

    $('.docs-date').change(function(){
        this.value = this.value?.replace(/-/g, '');
    })

    @if($type === 'edit')
        const SearchGxGoods = (type) => {
            gx1.Request(`/head/promotion/prm10/search/show/${searchType[type]}/${coupon_no}`, '', -1, function(res){
                $('#gx1-total').html(res.head.total);
            });
        }

        const SearchGxCompany = (type) => {
            gx2.Request(`/head/promotion/prm10/search/show/${searchType[type]}/${coupon_no}`, '', -1, function(res){
                $('#gx2-total').html(res.head.total);
                comData = res.body;
            });
        }

        SearchGxCompany('SC');
        SearchGxGoods(getRadioValue('coupon_apply'));
    @endif

    // 사용가능대상 선택 시 상품관련구문 변경
    let is_AG = type === 'add' ? true : '{{ @$coupon->coupon_apply }}' != "SG";

    function setAGState(is_ag) {
        document.querySelector("#AG_or_SG").innerText = is_ag ? "제외상품" : "상품";
    }

    setAGState(is_AG);

</script>
@stop

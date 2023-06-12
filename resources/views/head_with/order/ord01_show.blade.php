@extends('head_with.layouts.layout-nav')
@section('title','주문상세내역')
@section('content')

<style>
    .custm_tb1 th {
        padding: 7px 12px 7px 12px;
    }

    .custm_tb1 td {
        padding: 5px 12px 5px 12px;
    }

    .custm_tb2 th {
        padding: 7px 12px 7px 12px;
    }

    .custm_tb2 td {
        padding: 5px 12px 5px 12px;
        vertical-align: middle;
    }

    .custm_tb3 tr {
        padding: 0px 12px 7px 12px;
    }
</style>

<div class="container-fluid show_layout py-3">
    <div class="page_tit mb-3 d-flex align-items-center justify-content-between">
        <div>
            <h3 class="d-inline-flex">주문상세내역</h3>
            <div class="d-inline-flex location">
                <span class="home"></span>
                <span>/ 주문</span>
                <span>/ 주문상세내역</span>
            </div>
        </div>
        <div>
            <a href="#" class="btn btn-sm btn-primary shadow-sm mr-1 receipt-btn">거래명세표</a>
            <a href="#" onclick="window.close()" class="btn btn-sm btn-primary shadow-sm">닫기</a>
        </div>
    </div>
    <div class="card_wrap aco_card_wrap">
        <div class="card shadow">
            <div class="card-header mb-0">
                <a href="#" class="m-0 font-weight-bold">주문번호</a>
            </div>
            <div class="card-body">
                <div class="row_wrap">
                    <div class="row">
                        <div class="col-12">
                            <div class="table-box-ty2 mobile">
                                <table class="table incont table-bordered" id="dataTable" width="100%" cellspacing="0">
                                    <colgroup>
                                        <col width="120px">
                                    </colgroup>
                                    <tr>
                                        <th>주문번호 찾기</th>
                                        <td style="padding:0px 10px 0px 10px;">
                                            <div class="order_num_search">
                                                <input type='text' class="form-control form-control-sm search-all search-enter" id="ord_no" name='ord_no' value='{{$ord_no}}'>
                                                <a href="#" class="btn btn-sm btn-primary search-btn">검색</a>
                                            </div>
                                        </td>
                                    </tr>
                                    @if( @$p_ord_no != "" )
                                    <tr>
                                        <th>부모 주문번호</th>
                                        <td style="padding:0px 10px 0px 10px;">
                                            <a href="#" onClick="openOrder('{{ $p_ord_no }}');" style="font-weight:700;">{{ $p_ord_no }}</a>
                                        </td>
                                    </tr>
                                    @endif
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="card shadow">
            <div class="card-header mb-0">
                <a href="#" class="m-0 font-weight-bold">주문자 정보</a>
            </div>
            <div class="card-body">
                <div class="row_wrap">
                    <div class="row">
                        <div class="col-12">
                            <div class="table-box-ty2 mobile">
                                <table class="table incont table-bordered" id="dataTable" width="100%" cellspacing="0">
                                    <colgroup>
                                        <col width="9%">
                                        <col width="25%">
                                        <col width="9%">
                                        <col width="25%">
                                        <col width="9%">
                                        <col width="25%">
                                    </colgroup>
                                    <tbody>
                                        <tr>
                                            <th>주문자</th>
                                            <td>
                                                <div class="txt_box">{{ @$ord->user_nm }}</div>
                                            </td>
                                            <th>아이디</th>
                                            <td colspan="3">
                                                <div class="txt_box">
                                                    @if(@$ord->black_yn == 'Y')
                                                        <a href="#" style="color: red; background-color: transparent;text-decoration: underline;" onclick="return openUserEdit('{{ @$ord->user_id }}');return false;"> {{ @$ord->user_id }}  </a> <span>({{@$ord->black_reason}})</span> <span style="color:#ff0000;">@if(@$ord->member_memo != "")&lbbrk;{{ @$ord->member_memo }}&rbbrk;@endif</span>
                                                    @else
                                                        <a href="#" onclick="return openUserEdit('{{ @$ord->user_id }}');return false;"> {{ @$ord->user_id }}  </a> <span style="color:#ff0000;">@if(@$ord->member_memo != "")&lbbrk;{{ @$ord->member_memo }}&rbbrk;@endif</span>
                                                    @endif
                                                    <p style="color:#06a800;">{{ @$group->group_nm }}</p>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>주문상태</th>
                                            <td>
                                                <div class="txt_box">{{ @$ord->ord_state_nm }}
                                                    <div>
                                            </td>
                                            <th>주문시간</th>
                                            <td>
                                                <div class="txt_box">{{ @$ord->ord_date }}</div>
                                            </td>
                                            <th>판매처</th>
                                            <td>
                                                <div class="txt_box">@if(@$ord->sale_place != '') {{ @$ord->sale_place }} @elseif(@$ord->sale_place_nm != '') {{ @$ord->sale_place_nm }} @endif</div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>전화</th>
                                            <td>
                                                <div class="txt_box">{{ @$ord->phone }}</div>
                                            </td>
                                            <th>휴대전화</th>
                                            <td style="padding:0px 10px 0px 10px;">
                                                <div class="txt_box">{{ @$ord->mobile }} <a href="#" class="btn-sm btn btn-secondary mr-1 sms-list-btn fs-12">SMS 내역</a></div>
                                            </td>
                                            <th>이메일</th>
                                            <td>
                                                <div class="txt_box">{{ @$ord->email }}</div>
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
        <div class="card shadow">
            <div class="card-header mb-0">
                <a href="#" class="m-0 font-weight-bold">수령자 정보</a>
            </div>
            <div class="card-body">
                <div class="row_wrap">
                    <div class="row">
                        <div class="col-12">
                            <div class="table-box-ty2 mobile">
                                <table class="table incont table-bordered" id="dataTable" width="100%" cellspacing="0">
                                    <colgroup>
                                        <col width="9%">
                                        <col width="25%">
                                        <col width="9%">
                                        <col width="25%">
                                        <col width="9%">
                                        <col width="25%">
                                    </colgroup>
                                    <tbody>
                                        <tr>
                                            <th>수령자</th>
                                            <td style="padding:0px 10px 0px 10px;">
                                                <div class="txt_box">
                                                    {{ @$ord->r_nm }} <a href="#" class="btn btn-sm btn-secondary shadow-sm dlv-info-btn fs-12 ml-1">배송지변경</a>
                                                </div>
                                            </td>
                                            <th>전화</th>
                                            <td>
                                                <div class="txt_box">{{ @$ord->r_phone }}</div>
                                            </td>
                                            <th>휴대전화</th>
                                            <td>
                                                <div class="txt_box">{{ @$ord->r_mobile }}</div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>우편번호</th>
                                            <td>
                                                <div class="txt_box">{{ @$ord->r_zipcode }}</div>
                                            </td>
                                            <th>주소</th>
                                            <td colspan="3">
                                                <div class="txt_box">{{ @$ord->r_addr1 }}<br />{{ @$ord->r_addr2 }}
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>택배사</th>
                                            <td>
                                                <div class="txt_box">{{ @$ord->dlv_cd }}</div>
                                            </td>
                                            <th>송장번호</th>
                                            <td>
                                                <div class="txt_box">{{ @$ord->dlv_no }}</div>
                                            </td>
                                            <th>배송완료일</th>
                                            <td>
                                                <div class="txt_box">{{ @$ord->dlv_end_date }}</div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>배송메세지</th>
                                            <td colspan="6">
                                                <div class="txt_box">{{ @$ord->dlv_msg }}</div>
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
        @if ($track)
        <div class="card shadow">
            <div class="card-header mb-0">
                <a href="#" class="m-0 font-weight-bold">유입 정보</a>
            </div>
            <div class="card-body">
                <div class="row_wrap">
                    <div class="row">
                        <div class="col-12">
                            <div class="table-box-ty2 mobile">
                                <table class="table incont table-bordered" id="dataTable" width="100%" cellspacing="0">
                                    <colgroup>
                                        <col width="9%">
                                        <col width="25%">
                                        <col width="9%">
                                        <col width="25%">
                                        <col width="9%">
                                        <col width="25%">
                                    </colgroup>
                                    <tbody>
                                        <tr>
                                            <th class="ty2">유입경로</th>
                                            <td class="ty2" colspan="5">
                                                <div class="txt_box">
                                                    {{ @$track->name }}
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th class="ty2">방문주기</th>
                                            <td class="ty2">
                                                <div class="txt_box">{{ @$track->vp }} 일</div>
                                            </td>
                                            <th class="ty2">방문시간/횟수</th>
                                            <td class="ty2">
                                                <div class="txt_box">{{ gmdate('H:i:s', @$track->vt) }}초/{{ @$track->vc }}회</div>
                                            </td>
                                            <th class="ty2">방문 페이지 주기</th>
                                            <td class="ty2">
                                                <div class="txt_box">{{ @$track->pageview }} 페이지</div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th class="ty2">광고</th>
                                            <td class="ty2">
                                                <div class="txt_box">{{ @$track->ad }}</div>
                                            </td>
                                            <th class="ty2">키워드</th>
                                            <td class="ty2">
                                                <div class="txt_box">{{ @$track->kw }}</div>
                                            </td>
                                            <th class="ty2">내부컨텐츠</th>
                                            <td class="ty2">
                                                <div class="txt_box">{{ @$track->track }}</div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th class="ty2">도메인</th>
                                            <td class="ty2">
                                                <div class="txt_box">{{ @$track->domain }}</div>
                                            </td>
                                            <th class="ty2">브라우저</th>
                                            <td class="ty2">
                                                <div class="txt_box">{{ @$track->browser }}</div>
                                            </td>
                                            <th class="ty2">모바일 여부</th>
                                            <td class="ty2">
                                                <div class="txt_box">{{ @$track->browser }}</div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th class="ty2">Agent</th>
                                            <td class="ty2" colspan="5">
                                                <div class="txt_box">{{ @$track->agent }}</div>
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
        @endif

        <div class="card shadow">
            <div class="card-header mb-0">
                <a href="#" class="m-0 font-weight-bold">출고메시지 정보</a>
            </div>
            <div class="card-body">
                <div class="row_wrap">
                    <div class="row">
                        <div class="col-12">
                            <div class="table-box-ty2 mobile">
                                <table class="table incont table-bordered" id="dataTable" width="100%" cellspacing="0">
                                    <colgroup>
                                        <col width="9%">
                                        <col width="25%">
                                        <col width="9%">
                                        <col width="25%">
                                        <col width="9%">
                                        <col width="25%">
                                    </colgroup>
                                    <tr>
                                        <th class="ty2">출고메시지</th>
                                        <td class="ty2" colspan="5" style="padding:0px 10px 0px 10px;">
                                            <div class="order_num_search">
                                                <input type='text' class="form-control form-control-sm search-all search-enter" id="dlv_coment" name='dlv_coment' value='{{@$ord->dlv_comment}}' style="width: 758px;">
                                                <a href="#" class="btn btn-sm btn-secondary dlv-comment-btn" style="left: 765px;">등록</a>
                                            </div>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                    @if (count(array_filter($ord_lists, function($v) {
                        return $v->dlv_comment != '';
                    })) > 0)
                    <div class="row mt-2">
                        <div class="col-12">
                            <div class="table-responsive">
                                <table class="table table-bordered th_border_none custm_tb1">
                                    <thead>
                                        <tr>
                                            <th>출고메시지</th>
                                            <th>상품명</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($ord_lists as $ord_list)
                                            @if ($ord_list->dlv_comment != '')
                                            <tr @if ($ord_list->ord_opt_no == $ord_opt_no) class="checked-goods" @endif>
                                                <td>{{$ord_list->dlv_comment}}</td>
                                                <td>{{$ord_list->goods_nm}}</td>
                                            </tr>
                                            @endif
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        @if(!empty($pay->pay_stat_nm))
        <div class="card shadow">
            <div class="card-header mb-0">
                <a href="#" class="m-0 font-weight-bold">결제 정보</a>
            </div>
            <div class="card-body">
                <div class="row_wrap">
                    <div class="row">
                        <div class="col-12">
                            <div class="table-responsive">
                                <table class="table table-bordered th_border_none custm_tb1">
                                    <thead>
                                        <tr>
                                            <th>결제상태</th>
                                            <th>결제방법</th>
                                            @if(@$pay->escw_use === "Y")<th>결제구분</th>@endif
                                            @if(@$pay->card_appr_no !== NULL)<th>카드명</th>@endif
                                            <th>입금액</th>
                                            <th>적립금</th>
                                            <th>쿠폰</th>
                                            <th>할인</th>
                                            <th>결제수수료</th>
                                            {{-- 무통장 || 계좌이체 || 가상계좌의 경우 --}}
                                            @if (in_array(@$pay->pay_type, array(1, 5, 9, 13, 16, 20, 24, 28, 64, 68, 72, 76)))
                                                <th>은행명</th>
                                                {{-- 무통장 || 가상게좌의 경우 계좌, 입금자, 입금일 출력 --}}
                                                @if (in_array(@$pay->pay_type, array(1, 5, 9, 13, 64, 68, 72, 76)))
                                                    <th>계좌</th>
                                                    <th>{{ @$pay->ghost_use === "Y" ? "계좌예금주" : "입금자"}}</th>
                                                    <th>입금일</th>
                                                @endif
                                            @endif
                                            <th>거래번호</th>
                                            {{-- 카드결제 승인번호가 있는 경우 승인시간, 승인번호, 무이자 출력 --}}
                                            @if(@$pay->card_appr_no !== NULL)
                                                <th>승인시간</th>
                                                <th>승인번호</th>
                                                <th>무이자</th>
                                            @endif
                                            <th>메시지</th>
                                            {{-- 무통장 || 계좌이체 || 가상계좌 경우 현금영수증, 세금계산서 발행여부 출력 --}}
                                            @if(@$pay->pay_stat === "1" && in_array(@$pay->pay_type, array(1, 5, 9, 13, 16, 20, 24, 28, 64, 68, 72, 76)))
                                                <th>현금영수증</th>
                                                <th>세금계산서</th>
                                            @endif
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>{{@$pay->pay_stat_nm}}</td>
                                            <td>{{@$pay->pay_type_nm}}</td>
                                            @if(@$pay->escw_use === "Y")<td>에스크로</td>@endif
                                            @if(@$pay->card_appr_no !== NULL)<td>{{@$pay->card_name}}</td>@endif
                                            <td>{{number_format(@$pay->pay_amt)}}</td>
                                            <td>{{number_format(@$pay->pay_point)}}</td>
                                            <td>{{number_format(@$pay->coupon_amt)}}</td>
                                            <td>{{number_format(@$pay->dc_amt)}}</td>
                                            <td>{{number_format(@$pay->pay_fee)}}</td>
                                            {{-- 무통장 || 계좌이체 || 가상계좌의 경우 --}}
                                            @if (in_array(@$pay->pay_type, array(1, 5, 9, 13, 16, 20, 24, 28, 64, 68, 72, 76)))
                                                <td>@if (is_string(@$pay->bank_code)) <?php echo @$pay->bank_code ?> @else <?php echo @$pay->bank_code->code_val ?> @endif</td>
                                                {{-- 무통장 || 가상게좌의 경우 계좌, 입금자, 입금일 출력 --}}
                                                @if (in_array(@$pay->pay_type, array(1, 5, 9, 13, 64, 68, 72, 76)))
                                                    <td>{{@$pay->bank_number}}</td>
                                                    <td>{{@$pay->bank_inpnm}}</td>
                                                    <td>{{@$pay->pay_stat !== "0" ? @$pay->upd_dm : ""}}</td>
                                                @endif
                                            @endif
                                            <td><u><a href="#" onclick="receiptView('{{ @$pay->tno }}', '{{ $ord_no }}', '{{ @$pay->pay_amt }}')">{{@$pay->tno}}</a></u></td>
                                            {{-- 카드결제 승인번호가 있는 경우 승인시간, 승인번호, 무이자 출력 --}}
                                            @if(@$pay->card_appr_no !== NULL)
                                                <td>{{@$pay->card_appr_dm}}</td>
                                                <td>{{@$pay->card_appr_no}}</td>
                                                <td>{{@$pay->nointf}}</td>
                                            @endif
                                            <td>{{@$pay->card_msg}}</td>
                                            {{-- 무통장 || 계좌이체 || 가상계좌 경우 현금영수증, 세금계산서 발행여부 출력 --}}
                                            @if(@$pay->pay_stat === "1" && in_array(@$pay->pay_type, array(1, 5, 9, 13, 16, 20, 24, 28, 64, 68, 72, 76)))
                                                <td><u><a href="#" onclick="return openPopup(event, 'cash')">{{ @$pay->cash_yn }}</a></u></td>
                                                <td><u><a href="#" onclick="return openPopup(event, 'tax')">{{ @$pay->tax_yn }}</a></u></td>
                                            @endif
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif

        <div class="card shadow">
            <div class="card-header mb-0 d-flex justify-content-between">
                <a href="#" class="m-0 font-weight-bold">상품</a>
                @if (@$ord->ord_state === 10 || @$ord->ord_state === 20)
                    <div class="flax_box">
                        <div class="d-lg-flex" style="line-height:30px;">
                            변경할 주문상태 :
                            <div class="ml-1">
                                <select id="chg_ord_stat" class="form-control form-control-sm" style="width: 100px;">
                                    <option value="0">== 선택 ==</option>
                                    @if (@$ord->ord_state === 10)
                                        <option value="20">출고처리</option>
                                    @elseif (@$ord->ord_state === 20)
                                        <option value="30">출고완료</option>
                                    @endif
                                </select>
                            </div>
                        </div>
                        <div id="chg_ord_box" class="align-items-center" style="line-height:30px; display:none;">
                            @if (@$ord->ord_state === 10)
                                <div class="ml-2">
                                    출고차수 :
                                </div>
                                <div class="ml-1">
                                    <input type="text" id="release_num" class="form-control form-control-sm" />
                                </div>
                                <div class="ml-1">
                                    <button type="button" class="btn btn-primary waves-light waves-effect dropdown-toggle btn-sm" onclick="SetOrdState(20)">출고처리중 변경</button>
                                </div>
                            @elseif (@$ord->ord_state === 20)
                                <div class="ml-2">
                                    <input type="checkbox" id="send_sms" checked />
                                    <label for="send_sms" class="mb-0">배송 문자 발송</label>
                                </div>
                                <div class="ml-2">
                                    송장 번호 : 
                                </div>
                                <div class="ml-1">
                                    <input type="text" class="form-control form-control-sm" id="invoice_num" />
                                </div>
                                <div class="ml-1">
                                    <select id="dlv_com_id" class="form-control form-control-sm" style="width: 120px;">
                                        <option value="0">== 택배업체 ==</option>
                                        @foreach ($dlv_cds as $dlv)
                                        <option value="{{$dlv->code_id}}">{{$dlv->code_val}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="ml-1">
                                    <button type="button" class="btn btn-primary waves-light waves-effect dropdown-toggle btn-sm ml-1" onclick="SetOrdState(30)">출고완료</button>
                                </div>
                            @endif
                        </div>
                    </div>
                @endif
            </div>
            <div class="card-body">
                <div class="row_wrap">
                    <div class="row">
                        <div class="col-12">
                            <div class="table-responsive">
                                <table class="table table-bordered th_border_none custm_tb2">
                                    <colgroup>
                                        <col width="3%">
                                        <col width="7%">
                                        <col width="7%">
                                        <col width="12%">
                                        <col width="3%">
                                        <col width="30%">
                                        <col width="7%">
                                        <col width="6%">
                                        <col width="6%">
                                        <col width="6%">
                                        <col width="6%">
                                        <col width="6%">
                                    </colgroup>
                                    <thead>
                                        <tr>
                                            <th><input type="checkbox" id="goods-all"></th>
                                            <th>상태</th>
                                            <th>출고형태/<br />출고구분</th>
                                            <th>스타일넘버<br />(업체)</th>
                                            <th colspan="2" style="width:25%; overflow:hidden;">상품명/옵션/배송정보</th>
                                            <th>처리현황/<br />메모</th>
                                            <th>수량<br />(재고)</th>
                                            <th>판매가</th>
                                            <th>할인</th>
                                            <th>배송비</th>
                                            <th>환불액</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @if (count($ord_lists) > 0)
                                        <?php
                                        $t_point = 0;
                                        $t_qty = 0;
                                        $t_price = 0;
                                        $t_coupon = 0;
                                        $t_refund_amt = 0;
                                        $t_dlv_amt = 0;
                                        ?>
                                        @foreach($ord_lists as $key => $ord_list)
                                        <?php
                                        $t_point += $ord_list->add_point;
                                        $t_qty += $ord_list->wqty;
                                        $t_price += $ord_list->price * $ord_list->qty;
                                        $t_coupon += $ord_list->coupon_amt;
                                        $t_refund_amt += $ord_list->refund_amt;
                                        $t_dlv_amt += $ord_list->dlv_amt;
                                        ?>
                                        <tr @if ($ord_list->ord_opt_no == $ord_opt_no) class="checked-goods" @endif >
                                            <td>
                                                @if (($clm_state == 1 && $ord_list->clm_state == 0) || ($clm_state == $ord_list->clm_state))
                                                <input type="checkbox" name="goods" value="{{$ord_list->ord_opt_no}}" data-ord-no="{{$ord_no}}" data-ord-kind="{{ $ord_list->ord_kind }}" data-ord-state="{{$ord_list->ord_state}}" data-clm-state="{{$ord_list->clm_state}}" @if ($ord_list->ord_opt_no == $ord_opt_no) checked @endif
                                                />
                                                @endif

												@if( $ord_list->ord_state == '30' and $ord_list->point_status == 'Y' )
													[[확정]]
												@endif
                                            </td>
                                            <td><a href="/head/order/ord01/{{$ord_no}}/{{$ord_list->ord_opt_no}}">{{@$ord_list->order_state}}</a></td>
                                            <td>
                                                <a href="#" onClick="PopOrderGoods('{{$ord_no}}','{{$ord_list->ord_opt_no}}');return false;">{{@$ord_list->ord_kind_nm}}</a>/<br />
                                                {{@$ord_list->ord_kind_nm}}
                                            </td>
                                            <td style="font-weight:400;">
                                                <strong style="font-weight:bold">{{@$ord_list->style_no}}</strong><br>
                                                ({{@$ord_list->com_nm}})
                                            </td>
                                            <td style="width:50px;text-align:center;">
                                                <img src="{{config('shop.image_svr')}}/{{@$ord_list->img}}" class="img" style="width:40px" />
                                            </td>
                                            <td style="font-weight:400;">
                                                <a href="#" onclick="return openHeadProduct('{{@$ord_list->goods_no}}');">{{@$ord_list->goods_nm}}</a><br>
                                                {{@$ord_list->goods_opt}}
                                            </td>
                                            <td>
                                                {{@$ord_list->state}}
                                                /
                                                {{@$ord_list->memo}}
                                            </td>
                                            <td>
                                                {{@$ord_list->wqty}}<br>
                                                <a href="#" onClick="openHeadStock('{{$ord_list->goods_no}}','{{$ord_list->goods_opt}}');return false;">({{@$ord_list->jaego_qty}}/{{@$ord_list->stock_qty}})</a>
                                            </td>
                                            <td style="text-align:right">{{number_format(@$ord_list->price)}}</td>
                                            <td style="text-align:right">{{number_format(@$ord_list->coupon_amt)}}</td>
                                            <td style="text-align:right; vertical-align:middle">{{number_format(@$ord_list->dlv_amt)}}</td>
                                            <td style="text-align:right">{{number_format(@$ord_list->refund_amt)}}</td>
                                        </tr>
                                        @endforeach
                                        <tr>
                                            <th rowspan="2" colspan="2">합계</th>
                                            <th colspan="5"> 적립금 지급액 : {{ number_format($t_point) }}원 </th>
                                            <th>{{number_format($t_qty)}}</th>
                                            <th>{{number_format($t_price) }}</th>
                                            <th>{{number_format($t_coupon) }}</th>
                                            <th>{{number_format($t_dlv_amt)}}</th>
                                            <th>{{number_format($t_refund_amt) }}</th>
                                        </tr>
                                        <tr>
                                            <th colspan="10">
                                                결제금액 ({{ number_format(@$pay->pay_amt) }}) =
                                                주문금액 ({{ number_format($t_price) }}) +
                                                결제수수료 ({{ number_format(@$pay->pay_fee) }}) +
                                                배송비 ({{ number_format(@$pay->pay_baesong) }}) +
                                                적립금사용 ({{ number_format(@$pay->pay_point)}}) +
                                                쿠폰사용 ({{ number_format(@$pay->coupon_amt)}}) +
                                                할인금액 ({{ number_format(@$pay->dc_amt) }}) +
                                                환불금액 ({{ number_format($t_refund_amt) }}) +
                                                할인금액 ({{ number_format($ord->tax) }})
                                            </th>
                                        </tr>
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card shadow">
            <div class="card-header mb-0">
                <div class="filter_wrap">
                    <div class="fl_box">
                        <a href="#" class="m-0 font-weight-bold">클레임 정보</a>
                    </div>
                    <div class="fr_box">
                        @if( @$c_ord_no != "" )
                        <span style="color:#FF0000;">*</span> 해당건의 자식 주문번호는 <span onClick="openHeadOrder('{{ $c_ord_no }}','{{ $c_ord_opt_no }}');" style="cursor:pointer;color:#0000FF;font-weight:700;">{{ @$c_ord_no }}</span> 입니다.&nbsp;&nbsp;
                        @endif
                        <button class="btn-sm btn btn-secondary sms-send-btn fs-12">SMS 발송</button>
                        <button class="btn-sm btn btn-secondary sms-list-btn fs-12">SMS 내역</button>
                        <button class="btn-sm btn btn-secondary ord20-btn fs-12">수기판매</button>
                        <button class="btn-sm btn btn-secondary tmp-save-btn fs-12">임시저장</button>

                        @if( ($order_opt->ord_state == "5" || $order_opt->ord_state == "9") && ($order_opt->clm_state == '0' || $order_opt->clm_state == '1' || $order_opt->clm_state == '-30') )
                        <button class="btn-sm btn btn-secondary save-order-btn fs-12">출고요청</button>
                        @endif

                        @if ($order_opt->ord_state >= "5")
                            @if ($order_opt->clm_state == "0" || $order_opt->clm_state == "1" || $order_opt->clm_state == "-30")
                                <button class="btn-sm btn btn-secondary claim-save-btn fs-12" data-cmd="req">클레임요청</button>
                            @elseif ($order_opt->clm_state == "40" || $order_opt->clm_state == "41")
                                <button class="btn-sm btn btn-secondary claim-save-btn fs-12" data-cmd="proc">클레임처리중</button>
                            @elseif ($order_opt->clm_state == "50" || $order_opt->clm_state == "51")
                                <button class="btn-sm btn btn-secondary claim-save-btn fs-12" data-cmd="end">클레임처리완료</button>
                            @endif
                        @else
                            @if ($order_opt->ord_state > "0")
                            <button class="btn-sm btn btn-secondary cancel-order-btn fs-12">주문취소</button>
                            @endif
                        @endif
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="row_wrap">
                    <div class="row">
                        <div class="col-12">
                            <div class="table-responsive">
                                <table class="table table-bordered th_border_none custm_tb1">
                                    <thead>
                                        <tr>
                                            <th>최종처리일</th>
                                            <th>클레임상태</th>
                                            <th>클레임사유</th>
                                            <th>클레임수량</th>
                                            <th>재고처리</th>
                                            <th>재고 미처리(재고조정) 사유</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>{{@$claim_info->last_up_date}}</td>
                                            <td>
												<select name="clm_state" id="clm_state" class="form-control form-control-sm">
													<option value="">선택</option>
													@if (@$ord->ord_state === '1' && @$ord->ord_state === '-10')
													<!-- 입금예정 -->
													<option value="-10">주문취소</option>
													@elseif (@$order_opt->clm_state == '40')
													<!-- 교환요청 -->
													<option value="40" selected>교환요청</option>
													<option value="50">교환처리중</option>
													<option value="-30">클레임무효</option>
													@elseif (@$order_opt->clm_state == '50')
													<!-- 교환처리중 -->
													<option value="50" selected>교환처리중</option>
													<option value="60">교환완료</option>
													<option value="-30">클레임무효</option>
													@elseif (@$order_opt->clm_state == '60')
													<!-- 교환완료 -->
													<option value="60" selected>교환완료</option>
													@elseif (@$order_opt->clm_state == '41')
													<!-- 환불요청 -->
													<option value="41" selected>환불요청</option>
													<option value="51">환불처리중</option>
													<option value="-30">클레임무효</option>
													@elseif (@$order_opt->clm_state == '51')
													<!-- 환불처리중 -->
													<option value="51" selected>환불처리중</option>
													<option value="61">환불완료</option>
													<option value="-30">클레임무효</option>
													@elseif (@$order_opt->clm_state == '61')
													<!-- 환불완료 -->
													<option value="61" selected>환불완료</option>
													@elseif (@$order_opt->clm_state == '-30')
													<!-- 클레임무효 -->
													<option value="-30" selected>클레임무효</option>
													<option value="40">교환요청</option>
													<option value="41">환불요청</option>
													@elseif (@$order_opt->clm_state == '1')
													<!-- 클레임무효 -->
													<option value="1" selected>임시저장</option>
													<option value="40">교환요청</option>
													<option value="41">환불요청</option>
													@elseif (@$claim_info->clm_state == '1')
													<!-- 클레임무효 -->
													<option value="40">교환요청</option>
													<option value="41">환불요청</option>
													@else
													<option value="40">교환요청</option>
													<option value="41">환불요청</option>
													@endif
												</select>
                                            </td>
                                            <td>
                                                <select name="clm_reason" id="clm_reason" class="form-control form-control-sm">
                                                    <option value="">선택</option>
                                                    @foreach($clm_reasons as $clm_reason)
                                                    <option value="{{$clm_reason->code_id}}" @if (@$claim_info->clm_reason == $clm_reason->code_id) selected @endif
                                                        >
                                                        {{$clm_reason->code_val}}
                                                    </option>
                                                    @endforeach
                                                </select>
                                            </td>
                                            <td>
                                                <select name="clm_qty" id="clm_qty" class="form-control form-control-sm">
                                                    <option value="">선택</option>
                                                    @for($i = 1; $i <= $order_opt->qty; $i++ )
                                                        <option value="{{$i}}" @if($order_opt->clm_qty == $i) selected @endif>
                                                            {{$i}}
                                                        </option>
                                                        @endfor
                                                </select>
                                            </td>
                                            <td>
                                                <div class="custom-control custom-checkbox">
                                                    <input type="checkbox" name="jaego_yn" id="jaego_yn" class="custom-control-input" value="y" @if($order_opt->jaego_yn == 'n') checked @endif>
                                                    <label class="custom-control-label" for="jaego_yn">재고미처리</label>
                                                </div>
                                            </td>
                                            <td>
                                                <select name="jaego_reason" id="jaego_reason" class="form-control form-control-sm">
                                                    <option value="">선택</option>
                                                    @foreach($jaego_reasons as $jaego_reason)
                                                    <option value="{{$jaego_reason->id}}" @if ( $order_opt->jaego_reason === $jaego_reason->val) selected @endif
                                                        >
                                                        {{$jaego_reason->val}}
                                                    </option>
                                                    @endforeach
                                                </select>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <p class="mt-1 mb-2" style="color:red">* 임시저장 시, 클레임상태는 변경되지 않습니다.</p>

                            <input type="hidden" name="clm_no" value="{{ @$claim_info->clm_no }}">
                            <input type="hidden" name="PREV_CLM_STATE" value="{{ @$claim_info->clm_state }}">

                            <div class="table-box-ty2 mobile mb-2">
                                <table class="table incont table-bordered" id="dataTable" width="100%" cellspacing="0">
                                    <colgroup>
                                        <col width="9%">
                                        <col width="25%">
                                        <col width="9%">
                                        <col width="25%">
                                        <col width="9%">
                                        <col width="25%">
                                    </colgroup>
                                    <tbody>
                                        <tr>
                                            <th style="font-weight:bold">요청일자</th>
                                            <td>
                                                <div class="txt_box">{{@$claim_info->req_date}}</div>
                                            </td>
                                            <th style="font-weight:bold">처리중일자</th>
                                            <td>
                                                <div class="txt_box">{{@$claim_info->proc_date}}</div>
                                            </td>
                                            <th style="font-weight:bold">완료일자</th>
                                            <td>
                                                <div class="txt_box">{{@$claim_info->end_date}}</div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th style="font-weight:bold">환불정보</th>
                                            <td colspan="5">
                                                <div class="order_num_search">
                                                    <select name="refund_yn" id="refund_yn" class="form-control form-control-sm">
                                                        <option value="n" @if( (@$claim_info->clm_state != 1 and @$claim_info->refund_yn == "n") or ( $refund_yn == "n") ) selected @endif>환불안함</option>
                                                        <option value="o" @if( (@$claim_info->clm_state != 1 and @$claim_info->refund_yn == "o") or ( $refund_yn == "o") ) selected @endif>환불함(오픈마켓)</option>
                                                        <option value="y" @if( (@$claim_info->clm_state != 1 and @$claim_info->refund_yn == "y") or ( $refund_yn == "y") ) selected @endif>환불함</option>
                                                    </select>
                                                    <a href="#" class="btn-sm btn btn-secondary refund-btn">환불</a>
                                                </div>
                                                <ul class="refund_input_list">
                                                    <li>
                                                        <span>환불금액</span>
                                                        <div class="cont"><input type="text" name="refund_amt" id="refund_amt" value="{{@number_format($claim_info->refund_amt)}}" readonly="readonly" class="form-control form-control-sm text-right"></div>
                                                    </li>
                                                    <li>
                                                        <span>은행</span>
                                                        <div class="cont"><input type="text" name="refund_bank" id="refund_bank" value="{{@$claim_info->refund_bank}}" readonly="readonly" class="form-control form-control-sm"></div>
                                                    </li>
                                                    <li>
                                                        <span>계좌</span>
                                                        <div class="cont"><input type="text" name="refund_account" id="refund_account" value="{{@$claim_info->refund_account}}" readonly="readonly" class="form-control form-control-sm"></div>
                                                    </li>
                                                    <li>
                                                        <span>예금주</span>
                                                        <div class="cont"><input type="text" name="refund_nm" id="refund_nm" value="{{@$claim_info->refund_nm}}" readonly="readonly" class="form-control form-control-sm"></div>
                                                    </li>
                                                </ul>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-bordered th_border_none custm_tb1">
                                    <thead>
                                        <tr>
                                            <th style="width:10%">유형</th>
                                            <th style="width:10%">상태</th>
                                            <th style="width:20%">접수일시</th>
                                            <th>클레임내용</th>
                                            <th style="width:10%">처리자</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @if (count($claim_memos) > 0)
                                        @foreach($claim_memos as $claim_memo)
                                        <tr>
                                            <td>{{@$claim_memo->cs_form}}</td>
                                            <td><a href="#goods">{{@$claim_memo->clm_state}}</a></td>
                                            <td>{{@$claim_memo->regi_date}}</td>
                                            <td>{{@$claim_memo->memo}}</td>
                                            <td>{{@$claim_memo->admin_nm}}</td>
                                        </tr>
                                        @endforeach
                                        @else
                                        <tr>
                                            <td colspan="5">클레임내용이 없습니다.</td>
                                        </tr>
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                            <div class="table-box-ty2 mt-2">
                                <table class="table incont table-bordered custm_tb1" id="dataTable" width="100%" cellspacing="0">
                                    <colgroup>
                                        <col width="9%">
                                        <col width="93%">
                                    </colgroup>
                                    <tbody>
                                        <tr style="border-top:1px solid #ddd;">
                                            <th class="brtn">클레임 내용</th>
                                            <td class="brtn">
                                                <div class="flax_box">
                                                    <div style="width:20%;">
                                                        <select name="cs_form" id="cs_form" class="form-control form-control-sm">
                                                            @foreach($cs_forms as $cs_form)
                                                            <option value="{{$cs_form->code_id}}" @if ($cs_form->code_id === '01') selected @endif
                                                                >
                                                                {{$cs_form->code_val}}
                                                            </option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                    <div class="claim_type" style="width:78%;margin-left:2%">
                                                        <textarea style="width:100%" class="form-control form-control-sm" id="claim_str" name="memo"></textarea>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div class="mt-2">
                                <button type="button" style="width:100%" class="btn btn-sm btn-secondary claim-msg-btn">클레임 내용 등록</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="card shadow">
            <div class="card-header mb-0">
                <a href="#" class="m-0 font-weight-bold">주문 처리일자 정보</a>
            </div>
            <div class="card-body">
                <div class="row_wrap">
                    <div class="row">
                        <div class="col-12">
                            <div class="table-box-ty2 mobile">
                                <table class="table incont table-bordered custm_tb3" id="dataTable" width="100%" cellspacing="0">
                                    <colgroup>
                                        <col width="10%">
                                        <col width="24%">
                                        <col width="10%">
                                        <col width="24%">
                                        <col width="10%">
                                        <col width="24%">
                                    </colgroup>
                                    <tbody>
                                        <tr>
                                            <th>주문일시</th>
                                            <td>
                                                <div class="txt_box">{{@$ord->ord_date}}</div>
                                            </td>
                                            <th>입금일시</th>
                                            <td>
                                                <div class="txt_box">{{@$ord->dlv_start_date}}</div>
                                            </td>
                                            <th>최종처리일시</th>
                                            <td>
                                                <div class="txt_box">{{@$ord->dlv_start_date}}</div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>출고요청</th>
                                            <td>
                                                <div class="txt_box">{{@$ord->dlv_start_date}}
                                                    <div>
                                            </td>
                                            <th>출고처리중</th>
                                            <td>
                                                <div class="txt_box">{{@$ord->dlv_proc_date}}</div>
                                            </td>
                                            <th>출고완료</th>
                                            <td>
                                                <div class="txt_box">{{@$ord->dlv_end_date}}</div>
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
        <div class="card shadow">
            <div class="card-header mb-0">
                <a href="#" class="m-0 font-weight-bold">주문상태 로그 정보</a>
            </div>
            <div class="card-body">
                <div class="row_wrap">
                    <div class="row">
                        <div class="col-12">
                            <div class="table-responsive">
                                <table class="table table-bordered th_border_none">
                                    <thead>
                                        <tr>
                                            <th>이전 주문상태</th>
                                            <th>현재 주문상태</th>
                                            <th>주문상태일자</th>
                                            <th>처리자</th>
                                            <th>로그</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @if(!empty($state_logs) && count($state_logs) > 0)
                                        @foreach($state_logs as $state_log)
                                        <tr>
                                            <td>{{$state_log->p_ord_state_nm}}</td>
                                            <td>{{$state_log->ord_state_nm}}</td>
                                            <td>{{$state_log->state_date}}</td>
                                            <td>{{$state_log->admin_nm}}</td>
                                            <td>{{$state_log->comment}}</td>
                                        </tr>
                                        @endforeach
                                        @else
                                        <tr>
                                            <td colspan="5">
                                                <strong>주문상태 로그 정보가 없습니다.</strong>
                                            </td>
                                        </tr>
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="card shadow">
            <div class="card-header mb-0">
                <a href="#" class="m-0 font-weight-bold">공급업체 정보</a>
            </div>
            <div class="card-body">
                <div class="row_wrap">
                    <div class="row">
                        <div class="col-12">
                            <div class="table-box-ty2 mobile">
                                <table class="table incont table-bordered" id="dataTable" width="100%" cellspacing="0">
                                    <colgroup>
                                        <col width="120px">
                                        <col width="23%">
                                        <col width="120px">
                                        <col width="23%">
                                        <col width="120px">
                                        <col width="23%">
                                    </colgroup>
                                    <tr>
                                        <th>업체구분</th>
                                        <td>
                                            <div class="txt_box">{{@$ord->com_type_nm}}</div>
                                        </td>
                                        <th>업체</th>
                                        <td>
                                            <div class="txt_box">{{@$ord->com_nm}}</div>
                                        </td>
                                        <th>담당자</th>
                                        <td>
                                            <div class="txt_box">{{@$ord->staff_nm1}}</div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>전화</th>
                                        <td>
                                            <div class="txt_box">{{@$ord->staff_phone1}}</div>
                                        </td>
                                        <th>휴대전화</th>
                                        <td>
                                            <div class="txt_box">{{@$ord->staff_hp1}}</div>
                                        </td>
                                        <th>담당MD</th>
                                        <td>
                                            <div class="txt_box">{{@$ord->md_nm}}</div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>주소</th>
                                        <td colspan="5">
                                            <div class="txt_box">
                                                사업장 : ({{@$ord->com_r_addr1}})<br />
                                                반송지 : ({{@$ord->com_r_addr2}})
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>웹사이트</th>
                                        <td colspan="5">
                                            <div class="txt_box">{{@$ord->com_memo}}</div>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    const ord_no = '{{$ord_no}}';
    const ord_opt_no = '{{$ord_opt_no}}';
    const p_ord_opt_no = '{{$ord->p_ord_opt_no ? $ord->p_ord_opt_no : $ord_opt_no}}';
    const ord_state = '{{@$ord->ord_state}}';
    const ord_place = '{{@$ord->ord_place}}';
    const sale_place = '{{@$ord->sale_place}}';
    const ord_type = '{{@$ord->ord_type}}';
    const ord_kind = '{{@$ord->ord_kind}}';
    const goods_no = '{{@$order_opt->goods_no}}';
    const goods_sub = '{{@$order_opt->goods_sub}}';
    const clm_det_no = '{{@$order_opt->clm_det_no}}';
    const clm_state = '{{@$order_opt->clm_state}}';
    const claimSave = (cmd) => {
        if ($('[name=goods]:checked').length === 0) {
            alert("상품을 선택해주세요.");
            return;
        }

        if ($('#clm_state').val() === '') {
            alert("클레임상태를 선택해주세요.");
            return;
        }

        if ($('#clm_reason').val() === '') {
            alert("클레임 사유를 선택해주세요.");
            return;
        }

        let jaego_yn = "y";

        if (ord_state != "9") {
            if (ord_state != 1 && $('#clm_qty').val() === '') {
                alert("클레임 수량을 선택해주세요.");
                return;
            }

            if ($('#jaego_yn:checked').length > 0) {
                jaego_yn = "n";
                if ($('#jaego_reason').val() === '') {
                    alert('재고 미처리 사유를 선택해주세요.');
                    return;
                }
            }
        }

        const ord_opt_nos = [];

        $('[name=goods]:checked').each(function(obj) {
            ord_opt_nos.push(this.value);
        });

        $.ajax({
            async: true,
            type: 'put',
            url: '/head/order/ord01/claim-save',
            data: {
                "ord_opt_no": ord_opt_no,
                "ord_opt_nos": ord_opt_nos.join(','),
                "jaego_yn": jaego_yn,
                "jaego_reason": $('#jaego_reason').val(),
                "clm_state": $('#clm_state').val(),
                "clm_qty": $('#clm_qty').val(),
                "refund_yn": $('#refund_yn').val(),
                "refund_amt": $('#refund_amt').val(),
                "refund_bank": $("#refund_bank").val(),
                "refund_account": $("#refund_account").val(),
                "refund_nm": $("#refund_nm").val(),
                "clm_reason": $('#clm_reason').val(),
                "cmd": cmd,
                "clm_det_no": clm_det_no,
                "prev_clm_state": clm_state
            },
            success: function(res) {
                alert("클레임 정보가 반영 되었습니다.");
                location.reload();
            },
            error: function(xhr, status, error) {
                console.log(xhr.responseText);
            }
        });
    }

    const selectOrder = (row) => {
        $('#ord_no').val(row.ord_no);
    }

    function dlvSubmitCallback() {
        location.reload();
    }

    $('.ord-no-btn').click((e) => {
        e.preventDefault();
        const url = '/head/api/order';
        const product = window.open(url, "_blank", "toolbar=no,scrollbars=yes,resizable=yes,status=yes,top=500,left=500,width=1200,height=800");
    });

    $('.search-btn').click((e) => {
        location.href = "/head/order/ord01/" + $('#ord_no').val();
    });

    $('.dlv-comment-btn').click((e) => {
        e.preventDefault();

        $.ajax({
            async: true,
            type: 'put',
            url: '/head/order/ord01/dlv-comment',
            data: {
                "comment": $('#dlv_coment').val(),
                "ord_opt_no": ord_opt_no
            },
            success: function(data) {
                alert("출고메시지가 수정되었습니다.");
                location.reload();
            },
            error: function(request, status, error) {
                console.log("error")
            }
        });
    });

    $('.claim-msg-btn').click((e) => {
        if ($('[name=memo]').val() == "") {
            alert('클레임 내용을 입력해주세요.');
            return;
        }

        $.ajax({
            async: true,
            type: 'put',
            url: '/head/order/ord01/claim-message-save',
            data: {
                "ord_opt_no": ord_opt_no,
                "cs_form": $("#cs_form").val(),
                'msg': $('#claim_str').val(),
                'ord_state': ord_state,
                'clm_state': clm_state
            },
            success: function(data) {
                alert("클레임내용이 등록 되었습니다.");
                location.reload();
            },
            error: function(request, status, error) {
                console.log("error")
            }
        });
    });

    $('#goods-all').change(function() {
        $('[name=goods]').attr('checked', this.checked);
    })

    $('.sms-send-btn').click(function(e) {
        e.preventDefault();

        openSmsSend('{{ @$ord->mobile }}', '{{ @$ord->user_nm }}');
    });

    $('.sms-list-btn').click(function(e) {
        e.preventDefault();

        openSmsList('{{ @$ord->mobile }}', '{{ @$ord->user_nm }}');
    });

    $('.claim-save-btn').click(function(e) {
        e.preventDefault();

        claimSave($(this).attr('data-cmd'));
    });

    $('.tmp-save-btn').click(function(e) {
        e.preventDefault();

        if ($('[name=goods]:checked').length === 0) {
            alert('임시저장 할 상품을 선택해 주십시오.');
            return;
        }

        if (confirm("임시저장 하시겠습니까?") === false) return;

        const data = [];
        const checkedCnt = $('[name=goods]:checked').length;

        for (let i = 0; i < checkedCnt; i++) {
            data.push($('[name=goods]:checked')[i].value);
        }

		$.ajax({
            async: true,
            type: 'put',
            url: '/head/order/ord01/claim-save',
            data: {
                "ord_opt_no": ord_opt_no,
                "ord_opt_nos": data.join(','),
                "cmd": "save"
            },
            success: function(data) {
                console.log(data);
                alert("저장되었습니다.");
                location.reload();
            },
            error: function(request, status, error) {
                console.log("error")
            }
        });
    });

    $('.save-order-btn').click(function(e) {
        e.preventDefault();

        if ($('[name=goods]:checked').length === 0) {
            alert('출고요청으로 변경 할 상품을 선택해 주십시오.');
            return;
        }

        if (confirm("출고요청으로 변경 하시겠습니까?") === false) return;

        const data = [];
        const checkedCnt = $('[name=goods]:checked').length;

        for (let i = 0; i < checkedCnt; i++) {
            data.push($('[name=goods]:checked')[i].value);
        }

        $.ajax({
            async: true,
            type: 'put',
            url: '/head/order/ord01/order-save',
            data: {
                "ord_no": ord_no,
                "ord_opt_no": ord_opt_no,
                "ord_opt_nos": data.join(',')
            },
            success: function(data) {
                console.log(data);
                if(data.code == '200'){
                    alert("출고요청으로 변경되었습니다.");
                    location.reload();
                } else {
                    alert('처리 중 문제가 발생하였습니다. 다시 시도하여 주십시오.');
                }
            },
            error: function(request, status, error) {
                console.log("error")
            }
        });
    });

    $('.refund-btn').click(function(e) {
        e.preventDefault();

        if ($('#refund_yn').val() === 'n') return;

        const url = '/head/order/ord01/refund/' + ord_no + '/' + ord_opt_no;
        const product = window.open(url, "_blank", "toolbar=no,scrollbars=yes,resizable=yes,status=yes,top=500,left=500,width=1200,height=800");
    });

    $('.dlv-info-btn').click(function(e) {
        e.preventDefault();

        const url = '/head/order/ord01/dlv/' + ord_no + '/' + ord_opt_no;
        const product = window.open(url, "_blank", "toolbar=no,scrollbars=yes,resizable=yes,status=yes,top=500,left=500,width=1200,height=800");
    });

    $('.receipt-btn').click(function(e) {
        e.preventDefault();

        const url = '/head/order/ord01/receipt/' + ord_no;
        const product = window.open(url, "_blank", "toolbar=no,scrollbars=yes,resizable=yes,status=yes,top=500,left=500,width=1200,height=800");
    });

    $('.ord20-btn').click(function(e) {
        e.preventDefault();
        const params = [
            `p_ord_opt_no=${p_ord_opt_no}`,
        ];
        const url = `/head/order/ord20/show?${params.join('&')}`;
        const product = window.open(url, "_blank", "toolbar=no,scrollbars=yes,resizable=yes,status=yes,top=500,left=500,width=1200,height=800");
    });

    //주문취소 처리
    $('.cancel-order-btn').click(function(e) {
        const data = [];
        const ord_no = "{{ $ord_no }}";

        var ord_opt_nos = "";

        chk = 0;
        for (i = 0; i < $('[name=goods]').length; i++) {
            if ($("[name=goods]").eq(i).is(":checked") == true) {
                data.push(ord_no + "|" + $("[name=goods]").eq(i).val());
            }
        }

        if (data.length == 0) {
            alert("주문 취소 처리할 상품을 선택해 주십시요.");
            return false;
        }

        if (confirm('선택하신 주문을 취소하시겠습니까?') === false) {
            return;
        }

        $.ajax({
            async: true,
            type: 'put',
            url: '/head/order/ord01/cancel-order',
            data: {
                "datas": data
            },
            success: function(data) {
                alert("주문이 취소되었습니다.");
                window.location.reload();
            },
            error: function(request, status, error) {
                console.log("error")
            }
        });

    });

    function PopOrderGoods(ord_no,ord_opt_no){
		const url = `/head/order/ord01/order-goods/${ord_no}/${ord_opt_no}`;
		window.open(url,"_blank","toolbar=no,scrollbars=yes,resizable=yes,status=yes,top=500,left=500,width=1200,height=700")
    }

    function SetRefund(refund_amt, refund_bank, refund_account, refund_nm, msg) {
        $('input[name="refund_amt"]').val(refund_amt);
        $('input[name="refund_bank"]').val(refund_bank);
        $('input[name="refund_account"]').val(refund_account);
        $('input[name="refund_nm"]').val(refund_nm);
        $('#claim_str').val(msg);

        SaveClaimMsg();
    }

    /*
        주문상태 변경 (출고요청->출고처리 / 출고처리중->출고완료)
        -------------------------------
    */
    $('#chg_ord_stat').on("change", function() {
        $("#chg_ord_box").css("display", this.value === '0' ? "none" : "flex");
    })

    function SetOrdState(stat_num) {
        const checkRows = $('[name=goods]:checked');
        if(checkRows.length < 1) return alert("상태를 변경할 주문건을 선택해주세요.");

        let opt_list = [];

        for(let i = 0; i<checkRows.length; i++) {
            if(parseInt(checkRows[i].dataset.ordKind) === 30) return alert("출고보류 주문은 출고처리중으로 변경이 불가능합니다.");
            if(stat_num === 20 && parseInt(checkRows[i].dataset.ordState) !== 10) return alert("선택하신 주문건 중 출고요청상태가 아닌 주문건이 포함되어있습니다.");
            opt_list.push(checkRows[i].dataset.ordNo + "||" + checkRows[i].defaultValue);
        }

        if(stat_num === 20) { // 출고처리중으로 변경
            const dlv_series_no = $("#release_num").val();
            if(dlv_series_no === '') return alert("출고차수를 입력해주세요.");
            if(!confirm("선택하신 주문건을 출고처리중으로 변경하시겠습니까?")) return;
            updateOrderState({
                "ord_opt_nos[]": opt_list,
                ord_state: stat_num,
                dlv_series_no: dlv_series_no,
            });
        } else if(stat_num === 30) { // 출고완료로 변경
            const invoice_num = $("#invoice_num").val();
            if(invoice_num === '') return alert("송장번호를 입력해주세요.");
            const dlv_com_cd = $("#dlv_com_id").val();
            if(dlv_com_cd === '0') return alert("택배업체를 선택해주세요.");
            if(!confirm("선택하신 주문건을 출고완료 처리하시겠습니까?")) return;
            updateOrderState({
                "ord_opt_nos[]": opt_list,
                ord_state: stat_num,
                dlv_no: invoice_num,
                dlv_cd: dlv_com_cd,
                send_sms_yn: $("#send_sms").is(":checked"),
            });
        }
    }

    function updateOrderState(data) {
        $.ajax({
            async: true,
            type: 'put',
            url: '/head/order/ord01/update/order-state',
            data: data,
            success: function(res) {
                console.log(res);
                alert(res.msg);
                if(res.code === 200) location.reload();
            },
            error: function(request, status, error) {
                if(request.responseJSON) alert(request.responseJSON.message);
            }
        });
    }

    /*
        ---------------------------------
    */

    function SaveClaimMsg() {
        if ($('#claim_str').val() == "") {
            alert('클레임 내용을 입력해주세요.');
            return;
        }

        var refund_yn   = $('#refund_yn').val();

        $.ajax({
            async: true,
            type: 'put',
            url: '/head/order/ord01/claim-message-save',
            data: {
                "ord_opt_no": ord_opt_no,
                "cs_form": $("#cs_form").val(),
                'msg': $('#claim_str').val(),
                'ord_state': ord_state,
                'clm_state': clm_state
            },
            success: function(data) {
                alert("클레임 내용이 등록되었습니다.");
                //location.reload();
				location.href	= `/head/order/ord01/${ord_no}/${ord_opt_no}?refund_yn=${refund_yn}`;
            },
            error: function(request, status, error) {
                console.log("error")
            }
        });
    }

    /*
        주문상태 변경 (출고요청->출고처리 / 출고처리중->출고완료)
        -------------------------------
    */
    $('#chg_ord_stat').on("change", function() {
        $("#chg_ord_box").css("display", this.value === '0' ? "none" : "flex");
    })

    function SetOrdState(stat_num) {
        const checkRows = $('[name=goods]:checked');
        if(checkRows.length < 1) return alert("상태를 변경할 주문건을 선택해주세요.");

        let opt_list = [];

        for(let i = 0; i<checkRows.length; i++) {
            if(parseInt(checkRows[i].dataset.ordKind) === 30) return alert("출고보류 주문은 출고처리중으로 변경이 불가능합니다.");
            if(stat_num === 20 && parseInt(checkRows[i].dataset.ordState) !== 10) return alert("선택하신 주문건 중 출고요청상태가 아닌 주문건이 포함되어있습니다.");
            opt_list.push(checkRows[i].dataset.ordNo + "||" + checkRows[i].defaultValue);
        }

        if(stat_num === 20) { // 출고처리중으로 변경
            const dlv_series_no = $("#release_num").val();
            if(dlv_series_no === '') return alert("출고차수를 입력해주세요.");
            if(!confirm("선택하신 주문건을 출고처리중으로 변경하시겠습니까?")) return;
            updateOrderState({
                "ord_opt_nos[]": opt_list,
                ord_state: stat_num,
                dlv_series_no: dlv_series_no,
            });
        } else if(stat_num === 30) { // 출고완료로 변경
            const invoice_num = $("#invoice_num").val();
            if(invoice_num === '') return alert("송장번호를 입력해주세요.");
            const dlv_com_cd = $("#dlv_com_id").val();
            if(dlv_com_cd === '0') return alert("택배업체를 선택해주세요.");
            if(!confirm("선택하신 주문건을 출고완료 처리하시겠습니까?")) return;
            updateOrderState({
                "ord_opt_nos[]": opt_list,
                ord_state: stat_num,
                dlv_no: invoice_num,
                dlv_cd: dlv_com_cd,
                send_sms_yn: $("#send_sms").is(":checked"),
            });
        }
    }

    function updateOrderState(data) {
        $.ajax({
            async: true,
            type: 'put',
            url: '/head/order/ord01/update/order-state',
            data: data,
            success: function(res) {
                console.log(res);
                alert(res.msg);
                if(res.code === 200) location.reload();
            },
            error: function(request, status, error) {
                if(request.responseJSON) alert(request.responseJSON.message);
            }
        });
    }

    function openPopup(e, type) {
        e.preventDefault();
        if(type === "cash") {
            // 현금영수증 발행내역 오픈
            const cash_no = '{{ @$pay->cash_yn }}' === "Y" ? 1 : '';
            const url = `/head/order/ord01/${ord_no}/${ord_opt_no}/cash?cash_no=${cash_no}`;
		    window.open(url,"_blank","toolbar=no,scrollbars=yes,resizable=yes,status=yes,top=500,left=500,width=900,height=700");
        } else if(type === "tax") {
            // 세금계산서 내역 오픈
            const tax_no = '{{ @$pay->tax_yn }}' === "Y" ? 1 : '';
            const url = `/head/order/ord01/${ord_no}/${ord_opt_no}/tax?tax_no=${tax_no}`;
		    window.open(url,"_blank","toolbar=no,scrollbars=yes,resizable=yes,status=yes,top=500,left=500,width=900,height=700");
        }
    }

    /*
        ---------------------------------
    */

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

    $(document).ready(function(e) {
        $("#release_num").val(getReleaseNum());
    });

    function getReleaseNum() {
        const today = new Date();
        return today.getFullYear()
            +''+(today.getMonth()+1 < 10 ? '0' : '')
            +''+(today.getMonth()+1)
            +''+(today.getDate() < 10 ? '0' : '')
            +''+today.getDate()
            +''+(today.getHours() < 10 ? '0' : '')
            +''+today.getHours();
    }   
</script>
<style>
    .checked-goods td:not([rowspan='{{count($ord_lists)}}']) {
        background: yellow;
    }

    .goods-list tbody th {
        border: 1px solid #ddd !important;
        text-align: right;
    }

    .goods-list tbody th[rowspan='2'] {
        text-align: center;
        vertical-align: middle;
    }

    .claim-list td[colspan='5'] {
        font-weight: bold;
        text-align: center;
    }
</style>
@stop

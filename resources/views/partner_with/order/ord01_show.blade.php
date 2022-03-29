@extends('partner_with.layouts.layout-nav')
@section('title','주문')
@section('content')
    <div class="container-fluid show_layout py-3">
        <div class="page_tit mb-3 d-flex align-items-center justify-content-between">
            <div>
                <h3 class="d-inline-flex">주문</h3>
                <div class="d-inline-flex location">
                    <span class="home"></span>
                    <span>/ 주문 -
                        {{ $ord_no }}
                        @if($order["p_ord_opt_no"] > 0)
                            < 부모주문 - <a href="/partner/order/ord01/{{ $order["p_ord_no"] }}/{{ $order["p_ord_opt_no"] }}">{{ $order["p_ord_no"] }}</a>
                        @endif
                        @if($order["c_ord_opt_no"] > 0)
                            > 자식주문 - <a href="/partner/order/ord01/{{ $order["c_ord_no"] }}/{{ $order["c_ord_opt_no"] }}">{{ $order["c_ord_no"] }}</a>
                        @endif
                    </span>
                </div>
            </div>
        </div>
        <div class="card_wrap aco_card_wrap">
            <div class="card shadow">
                <div class="card-header mb-0">
                    <a href="#" class="m-0 font-weight-bold">주문자</a>
                </div>
                <div class="card-body">
                    <div class="row_wrap">
                        <div class="row">
                            <div class="col-12">
                                <div class="table-box-ty2 mobile">
                                    <table class="table incont table-bordered" id="dataTable" width="100%" cellspacing="0">
                                        <tr>
                                            <th>주문번호</th>
                                            <td>
                                                <div class="txt_box">{{ $order["ord_no"] }}</div>
                                            </td>
                                            <th>주문자</th>
                                            <td>
                                                <div class="txt_box">{{ $order["r_nm"] }}</div>
                                            </td>
                                            <th>아이디</th>
                                            <td>
                                                <div class="txt_box">{{ $order["user_id"] }}</div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>주문상태</th>
                                            <td>
                                                <div class="txt_box">{{ $order["ord_state_nm"] }}</div>
                                            </td>
                                            <th>주문시간</th>
                                            <td>
                                                <div class="txt_box">{{ $order["ord_date"] }}</div>
                                            </td>
                                            <th>판매처</th>
                                            <td>
                                                <div class="txt_box">{{ $order["sale_place"] }}</div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>전화</th>
                                            <td>
                                                <div class="txt_box">{{ $order["phone"] }}</div>
                                            </td>
                                            <th>휴대전화</th>
                                            <td>
                                                <div class="txt_box">{{ $order["mobile"] }}</div>
                                            </td>
                                            <th>이메일</th>
                                            <td>
                                                <div class="txt_box">{{ $order["email"] }}</div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>유입경로</th>
                                            <td colspan="6">
                                                <div class="txt_box">{{ $order["url"] }}</div>
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card shadow">
                <div class="card-header mb-0">
                    <a href="#" class="m-0 font-weight-bold">수령자</a>
                </div>
                <div class="card-body">
                    <div class="row_wrap">
                        <div class="row">
                            <div class="col-12">
                                <div class="table-box-ty2 mobile">
                                    <table class="table incont table-bordered" id="dataTable" width="100%" cellspacing="0">
                                        <tr>
                                            <th>수령자</th>
                                            <td>
                                                <div class="txt_box">
                                                    {{ $order["r_nm"] }} <a href="#" class="btn btn-sm btn-secondary shadow-sm dlv-info-btn fs-12 ml-1">배송지변경</a>
                                                </div>
                                            </td>
                                            <th>전화</th>
                                            <td>
                                                <div class="txt_box">{{ $order["r_phone"] }}</div>
                                            </td>
                                            <th>휴대전화</th>
                                            <td>
                                                <div class="txt_box">{{ $order["r_mobile"] }}</div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>우편번호</th>
                                            <td>
                                                <div class="txt_box">{{ $order["r_zipcode"] }}</div>
                                            </td>
                                            <th>주소</th>
                                            <td colspan="3">
                                                <div class="txt_box">{{ $order["r_addr1"] }} {{ $order["r_addr2"] }}</div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>택배사</th>
                                            <td>
                                                <div class="txt_box">{{ $order["dlv_cd"] }}</div>
                                            </td>
                                            <th>송장번호</th>
                                            <td>
                                                <div class="txt_box">{{ $order["dlv_no"] }}</div>
                                            </td>
                                            <th>배송완료일</th>
                                            <td>
                                                <div class="txt_box">{{ $order["dlv_end_date"] }}</div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>배송메세지</th>
                                            <td colspan="6">
                                                <div class="txt_box">{{ $order["dlv_msg"] }}</div>
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card shadow">
                <div class="card-header mb-0">
                    <a href="#" class="m-0 font-weight-bold">결제</a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered th_border_none">
                            <thead>
                            <tr>
                                <th>결제상태</th>
                                <th>결제방법</th>
                                <th>은행명</th>
                                <th>입금액</th>
                                <th>적립금</th>
                                <th>쿠폰</th>
                                <th>계좌</th>
                                <th>입금자</th>
                                <th>처리일자</th>
                            </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>{{ $payment["pay_stat_nm"] ?? '' }}</td>
                                    <td>{{ $payment["pay_type_nm"] ?? '' }}</td>
                                    <td>{{ $payment["bank_code"] }}</td>
                                    <td>{{ $payment["pay_amt"] }}</td>
                                    <td>{{ $payment["pay_point"] }}</td>
                                    <td>{{ $payment["coupon_amt"] }}</td>
                                    <td>{{ $payment["bank_number"] }}</td>
                                    <td>{{ $payment["bank_inpnm"] }}</td>
                                    <td>{{ $payment["pay_upd_dm"] }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="card shadow">
                <div class="card-header mb-0">
                    <a href="#" class="m-0 font-weight-bold">상품</a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered th_border_none">
                            <thead>
                                <tr>
                                    <th>상태/출고구분</th>
                                    <th>상품명</th>
                                    <th>수량(재고)</th>
                                    <th>판매가</th>
                                    <th>쿠폰</th>
                                    <th>배송비</th>
                                    <th>환불액</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($order_products as $order_product)
                                <tr style="@if(count($order_products) > 1 && $ord_opt_no == $order_product["ord_opt_no"]) background-color:#ffff96 @endif">
                                    <td class="txt">
                                        <div><a href="/partner/order/ord01/{{ $order_product["ord_no"] }}/{{ $order_product["ord_opt_no"] }}">{{ $order_product["state"] }}</a></div>
                                        <div>{{ $order_product["ord_kind_nm"] }}</div>
                                    </td>
                                    <td>
                                        <div class="row">
                                            <div class="col-lg-2 pl-1">
                                                <img src='{{ $order_product["img"] }}' height="40" width="40" border="0" align="middle">
                                            </div>
                                            <div class="col-lg-10">
                                                <div>{{ $order_product["style_no"] }}</div>
                                                <a href="#" onclick="PopPrdDetail({{ $order_product['goods_no'] }},{{ $order_product['goods_sub'] }});" title="{{ $order_product['goods_nm'] }}">{{ $order_product["goods_nm_short"] }}</a>
                                                {{ $order_product["opt_val"] }}
                                                @if( $order_product["opt_amt"]  > 0)(+{{ $order_product["opt_amt"] }}원)@endif
                                                @foreach($order_product["addopts"] as $opts)
                                                    , {{ $opts["addopt"] ?? '' }} { if $opts.addopt_amt > 0 }(+{$opts.addopt_amt|number_format}원) { /if }
                                                @endforeach
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        {{ $order_product["qty"] }}
                                        (<a href="#" onclick="PopJaego('{{ $order_product["goods_no"] }}','{{ $order_product["goods_sub"] }}','{{ $order_product["goods_opt"] }}');return false;">{{ $order_product["jaego_qty"] }}</a>)
                                    </td>
                                    <td class="text-right">{{ number_format($order_product["price"]) }}</td>
                                    <td class="text-right">{{ number_format($order_product["coupon_amt"]) }}</td>
                                    <td class="text-right">{{ number_format($order_product["dlv_amt"]) }}</td>
                                    <td class="text-right">{{ number_format($order_product["refund_amt"]) }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
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
                            @if($order["c_ord_opt_no"] > 0)
                                <span style="color:#FF0000;">*</span> 해당건의 자식 주문번호는 <span onClick="openOrder('{{ $order["c_ord_no"] }}','{{ $order["c_ord_opt_no"] }}');" style="cursor:pointer;color:#0000FF;font-weight:700;">{{ $order["c_ord_no"] }}</span> 입니다.&nbsp;&nbsp;
                            @endif
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    @if ( $claim_yn == "Y")
                        <div class="row_wrap">
                            <div class="row">
                                <div class="col-12">
                                    <div class="table-box-ty2 mobile">
                                        <table class="table incont table-bordered" id="dataTable" width="100%" cellspacing="0">
                                            <tr>
                                                <th>클레임상태</th>
                                                <td>
                                                    <div class="txt_box">{{@$claim->clm_state_nm}}</div>
                                                </td>
                                                <th>클레임사유</th>
                                                <td>
                                                    <div class="txt_box">{{@$claim->clm_reason_nm}}</div>
                                                </td>
                                                <th>최종처리일</th>
                                                <td>
                                                    <div class="txt_box">{{@$claim->last_up_date}}</div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th>요청일자</th>
                                                <td>
                                                    <div class="txt_box">{{@$claim->req_date}} ({{@$claim->req_nm}})</div>
                                                </td>
                                                <th>처리중일자</th>
                                                <td>
                                                    <div class="txt_box">{{@$claim->proc_date}} ({{@$claim->proc_nm}})</div>
                                                </td>
                                                <th>완료일자</th>
                                                <td>
                                                    <div class="txt_box">{{@$claim->end_date}} ({{@$claim->end_nm}})</div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th>환불여부</th>
                                                <td>
                                                    <div class="txt_box">@if(@$claim->refund_yn=="y")환불함@else환불안함@endif</div>
                                                </td>
                                                <th>환불금액</th>
                                                <td>
                                                    <div class="txt_box">{{@$claim->refund_amt}} 원</div>
                                                </td>
                                                <th>예금주</th>
                                                <td>
                                                    <div class="txt_box">{{@$claim->refund_nm}}</div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th>환불은행</th>
                                                <td>
                                                    <div class="txt_box">{{@$claim->refund_bank}}</div>
                                                </td>
                                                <th>계좌번호</th>
                                                <td colspan="3">
                                                    <div class="txt_box">{{@$claim->refund_account}}</div>
                                                </td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                    <div class="table-responsive mt-4">
                        <table class="table table-bordered th_border_none">
                            <thead>
                                <tr>
                                    <th width="100px">유형</th>
                                    <th width="100px">상태</th>
                                    <th width="100px">접수일자</th>
                                    <th>클레임내용</th>
                                    <th width="100px">처리자</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($claim_msgs as $claim_msg)
                                <tr>
                                    <td>{{ $claim_msg->cs_form }} </td>
                                    <td>{{ $claim_msg->clm_state }} </td>
                                    <td>{{ $claim_msg->regi_date }} </td>
                                    <td style="text-align: left;">{{ $claim_msg->memo }} </td>
                                    <td>{{ $claim_msg->admin_nm }} </td>
                                </tr>
                                @empty
                                <tr><td colspan=99 style="text-align: center">클레임 내역이 없습니다.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="row_wrap mt-4">
                        <div class="row">
                            <div class="col-12">
                                <div class="table-box-ty2">
                                    <table class="table incont table-bordered" id="dataTable" width="100%" cellspacing="0">
                                        <tr>
                                            <th>클레임 구분</th>
                                            <td>
                                                <div class="form-inline form-radio-box">
                                                    <div class="custom-control custom-radio">
                                                        <input type="radio" name="req_claim_gubun" id="req_claim_gubun_refund" class="custom-control-input" value="REFUND" onclick="select_claim_gubun('refund')" />
                                                        <label class="custom-control-label" for="req_claim_gubun_refund">반품관련</label>
                                                    </div>
                                                    <div class="custom-control custom-radio mr-3">
                                                        <input type="radio" name="req_claim_gubun" id="req_claim_gubun_refund2" onclick="select_claim_gubun('change')" class="custom-control-input" />
                                                        <label class="custom-control-label" for="req_claim_gubun_refund2">품절관련</label>
                                                    </div>
                                                    <div class="btn-group btn-group-sm" role="group">
                                                        <button type="button" class="btn btn-outline-secondary" onclick="claim_insert()">클레임 내용에 적용</button>
                                                        <button type="button" class="btn btn-outline-secondary" onclick="claim_init();">클레임 내용 초기화</button>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="table-box-ty2 gubun_detail" id="gubun_detail_refund" style="display:none">
                                    <table class="table incont table-bordered brtn" id="dataTable" width="100%" cellspacing="0">
                                        <tr>
                                            <th class="brtn">상세 항목</th>
                                            <td class="brtn">
                                                <div class="claim_type">
                                                    <form name="f1" id="frm_refund">
                                                        @csrf
                                                        <ul>
                                                            <li class="form-inline">
                                                                <div class="custom-control custom-checkbox mr-2 form-check-box" onClick="check_form('chk1','chk1_val');">
                                                                    <input type="checkbox" class="custom-control-input" id="chk1" value="택배사">
                                                                    <label class="custom-control-label" for="chk1">택배사</label>
                                                                </div>
                                                                <input type="text" id="chk1_val" name="chk1_val" readonly class="form-control form-control-sm">
                                                            </li>
                                                            <li class="form-inline">
                                                                <div class="custom-control custom-checkbox form-check-box">
                                                                    <input type="checkbox" class="custom-control-input" id="chk3" value="선불">
                                                                    <label class="custom-control-label" for="chk3">선불</label>
                                                                </div>
                                                            </li>
                                                            <li class="form-inline">
                                                                <div class="custom-control custom-checkbox mr-2 form-check-box" onClick="check_form('chk5','chk5_val');">
                                                                    <input type="checkbox" class="custom-control-input" id="chk5" value="착불">
                                                                    <label class="custom-control-label" for="chk5">착불</label>
                                                                </div>
                                                                <input type="text" id="chk5_val" name="chk5_val" readonly class="form-control form-control-sm" value="" placeholder="금액(원)">
                                                            </li>
                                                            <li class="form-inline">
                                                                <div class="custom-control custom-checkbox mr-2 form-check-box" onClick="check_form('chk7','chk7_val');">
                                                                    <input type="checkbox" class="custom-control-input" id="chk7" value="동봉액">
                                                                    <label class="custom-control-label" for="chk7">동봉액</label>
                                                                </div>
                                                                <input type="text" id="chk7_val" name="chk7_val" readonly class="form-control form-control-sm" value="" placeholder="금액(원)">
                                                            </li>
                                                            <li class="form-inline">
                                                                <div class="custom-control custom-checkbox form-check-box">
                                                                    <input type="checkbox" class="custom-control-input" id="chk2" value="상품이상무">
                                                                    <label class="custom-control-label" for="chk2">상품이상무</label>
                                                                </div>
                                                            </li>
                                                            <li class="form-inline">
                                                                <div class="custom-control custom-checkbox mr-2 form-check-box" onClick="check_form('chk4','chk4_val');">
                                                                    <input type="checkbox" class="custom-control-input" id="chk4" value="상품이상">
                                                                    <label class="custom-control-label" for="chk4">상품이상</label>
                                                                </div>
                                                                <input type="text" id="chk4_val" name="chk4_val" readonly class="form-control form-control-sm" value="">
                                                            </li>
                                                            <li class="form-inline">
                                                                <div class="custom-control custom-checkbox form-check-box">
                                                                    <input type="checkbox" class="custom-control-input" id="chk8" value="사은품">
                                                                    <label class="custom-control-label" for="chk8">사은품여부</label>
                                                                </div>
                                                            </li>
                                                            <li class="form-inline">
                                                                <div class="custom-control custom-checkbox mr-2 form-check-box" onClick="check_form('chk6','chk6_val');">
                                                                    <input type="checkbox" class="custom-control-input" id="chk6" value="환불정보">
                                                                    <label class="custom-control-label" for="chk6">환불정보</label>
                                                                </div>
                                                                <input type="text" id="chk6_val" name="chk6_val" readonly class="form-control form-control-sm" value="" placeholder="예금주, 은행, 계좌번호">
                                                            </li>
                                                            <li class="form-inline">
                                                                <div class="custom-control custom-checkbox mr-2 form-check-box" onClick="check_form('chk9','chk9_val');">
                                                                    <input type="checkbox" class="custom-control-input" id="chk9" value="기타">
                                                                    <label class="custom-control-label" for="chk9">기타</label>
                                                                </div>
                                                                <input type="text" id="chk9_val" name="chk9_val" readonly class="form-control form-control-sm" value="">
                                                            </li>
                                                        </ul>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                                <form name="f2" id="frm_change" action="/partner/order/ord01/claim/comments" method="post">
                                    <input type="hidden" name="ord_opt_no" value="{{ $ord_opt_no }}"/>
                                    <div class="table-box-ty2 gubun_detail" id="gubun_detail_change" style="display:none">
                                        <table class="table incont table-bordered brtn" id="dataTable" width="100%" cellspacing="0">
                                            <tr>
                                                <th class="brtn">상세 항목</th>
                                                <td class="brtn">
                                                    <div class="claim_type">
                                                        <ul>
                                                            <li class="form-inline">
                                                                <div class="custom-control custom-checkbox form-check-box">
                                                                    <input type="checkbox" class="custom-control-input" id="chk11"
                                                                        value="통화">
                                                                    <label class="custom-control-label" for="chk11">통화</label>
                                                                </div>
                                                            </li>
                                                            <li class="form-inline">
                                                                <div class="custom-control custom-checkbox form-check-box">
                                                                    <input type="checkbox" class="custom-control-input" id="chk12"
                                                                        value="통화안됨">
                                                                    <label class="custom-control-label" for="chk12">통화안됨</label>
                                                                </div>
                                                            </li>
                                                            <li class="form-inline">
                                                                <div class="custom-control custom-checkbox form-check-box">
                                                                    <input type="checkbox" class="custom-control-input" id="chk13"
                                                                        value="문자발송">
                                                                    <label class="custom-control-label" for="chk13">문자발송</label>
                                                                </div>
                                                            </li>
                                                            <li class="form-inline">
                                                                <div class="custom-control custom-checkbox form-check-box">
                                                                    <input type="checkbox" class="custom-control-input" id="chk14"
                                                                        value="교환요청">
                                                                    <label class="custom-control-label" for="chk14">교환요청</label>
                                                                </div>
                                                            </li>
                                                            <li class="form-inline">
                                                                <div class="custom-control custom-checkbox form-check-box">
                                                                    <input type="checkbox" class="custom-control-input" id="chk15"
                                                                        value="환불-카드">
                                                                    <label class="custom-control-label" for="chk15">환불-카드</label>
                                                                </div>
                                                            </li>
                                                            <li class="form-inline">
                                                                <div class="custom-control custom-checkbox mr-2 form-check-box"
                                                                    onclick="check_form('chk16','chk16_val');">
                                                                    <input type="checkbox" class="custom-control-input" id="chk16"
                                                                        value="환불-현금">
                                                                    <label class="custom-control-label" for="chk16">환불-현금</label>
                                                                </div>
                                                                <input type="text" id="chk16_val" name="chk16_val" readonly
                                                                    class="form-control form-control-sm" value=""
                                                                    placeholder="예금주, 은행, 계좌번호"/>
                                                            </li>
                                                            <li class="form-inline">
                                                                <div class="custom-control custom-checkbox mr-2 form-check-box"
                                                                    onclick="check_form('chk17','chk17_val');">
                                                                    <input type="checkbox" class="custom-control-input" id="chk17"
                                                                        value="기타">
                                                                    <label class="custom-control-label" for="chk17">기타</label>
                                                                </div>
                                                                <input type="text" id="chk17_val" name="chk17_val" readonly
                                                                    class="form-control form-control-sm" value=""/>
                                                            </li>
                                                        </ul>
                                                    </div>
                                                </td>
                                            </tr>
                                        </table>
                                    </div>
                                    <div class="table-box-ty2">
                                        <table class="table incont table-bordered brtn" id="dataTable" width="100%" cellspacing="0">
                                            <tr>
                                                <th class="brtn">클레임 내용</th>
                                                <td class="brtn">
                                                    <div class="claim_type">
                                                        <textarea style="width:100%" class="form-control form-control-sm" id="claim_str" name="memo"></textarea>
                                                    </div>
                                                </td>
                                            </tr>
                                        </table>
                                    </div>
                                    <div class="mt-2">
                                        <button type="button" style="width:100%" class="btn btn-secondary" onclick="SaveClaim()">클레임 내용 등록</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card shadow">
                <div class="card-header mb-0">
                    <a href="#" class="m-0 font-weight-bold">정산</a>
                </div>
                <div class="card-body">
                    <div style="text-align: right;" class="pt-2 mb-2">("<font color=blue>월별 입점업체정산</font>" 시 <font color=red>기타정산금액</font>에 반영됩니다.)</div>
                    <div class="table-responsive">
                        <table class="table table-bordered th_border_none">
                            <thead>
                            <tr>
                                <th>정산일자</th>
                                <th>정산내용</th>
                                <th>정산금액</th>
                                <th>처리자</th>
                                <th>등록일</th>
                            </tr>
                            </thead>
                            <tfoot>
                            </tfoot>
                            <tbody>
                            @forelse($accounts as $account)
                                <tr>
                                    <td>{{ $account->etc_day }}</td>
                                    <td>{{ $account->etc_memo }}</td>
                                    <td class="text-right">{{ number_format($account->etc_amt)  }} </td>
                                    <td>{{ $account->admin_nm }}</td>
                                    <td>{{ $account->regi_date }}</td>
                                </tr>
                            @empty
                                <tr><td colspan=99 style="text-align: center">기타 정산 내역이 없습니다.</td></tr>
                            @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="card shadow">
                <div class="card-header mb-0">
                    <a href="#" class="m-0 font-weight-bold">공급업체</a>
                </div>
                <div class="card-body">
                    <div class="row_wrap">
                        <div class="row">
                            <div class="col-12">
                                <div class="table-box-ty2 mobile">
                                    <table class="table incont table-bordered" id="dataTable" width="100%" cellspacing="0">
                                        <tr>
                                            <th>업체구분</th>
                                            <td>
                                                <div class="txt_box">{{ $order["com_type_nm"] }}</div>
                                            </td>
                                            <th>업체</th>
                                            <td>
                                                <div class="txt_box">{{ $order["com_nm"] }}</div>
                                            </td>
                                            <th>담당자</th>
                                            <td>
                                                <div class="txt_box">{{ $order["staff_nm1"] }}</div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>전화</th>
                                            <td>
                                                <div class="txt_box">{{ $order["staff_phone1"] }}</div>
                                            </td>
                                            <th>휴대전화</th>
                                            <td>
                                                <div class="txt_box">{{ $order["staff_hp1"] }}</div>
                                            </td>
                                            <th>담당MD</th>
                                            <td>
                                                <div class="txt_box">{{ $order["md_nm"] }}</div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>주소</th>
                                            <td colspan="6">
                                                <div class="txt_box">
                                                    사업장 : {{ $order["com_addr"] }} <br>
                                                    반송지 : {{ $order["com_r_addr"] }}
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
            <div class="card shadow">
                <div class="card-header mb-0">
                    <a href="#" class="m-0 font-weight-bold">주문처리일자</a>
                </div>
                <div class="card-body">
                    <div class="row_wrap">
                        <div class="row">
                            <div class="col-12">
                                <div class="table-box-ty2 mobile">
                                    <table class="table incont table-bordered" id="dataTable" width="100%" cellspacing="0">
                                        <tr>
                                            <th>주문일자</th>
                                            <td>
                                                <div class="txt_box">{{ $order["ord_date"] }}</div>
                                            </td>
                                            <th>입금일자</th>
                                            <td>
                                                <div class="txt_box">{{ isset($payment["pay_upd_dm"])? $payment["pay_upd_dm"]:"" }}</div>
                                            </td>
                                            <th>최종처리일자</th>
                                            <td>
                                                <div class="txt_box">{{ $order["upd_date"] }}</div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>주문완료</th>
                                            <td>
                                                <div class="txt_box">{{ $order["dlv_start_date"] }}</div>
                                            </td>
                                            <th>상품준비중</th>
                                            <td>
                                                <div class="txt_box">{{ $order["dlv_proc_date"] }}</div>
                                            </td>
                                            <th>상품출고완료</th>
                                            <td>
                                                <div class="txt_box">{{ $order["dlv_end_date"] }}</div>
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


    <script type="text/javascript">

        const ord_no = '{{$ord_no}}';
        const ord_opt_no = '{{$ord_opt_no}}';

        function PopPrdDetail(goods_no, goods_sub){
            window.open("/partner/product/prd01/"+goods_no,"Product Detail", "_blank");
        }

        function PopJaego(goods_no, goods_opt){
            window.open("/partner/stock/stk01/"+goods_no,"stock Detail", "_blank");
        }

        function select_claim_gubun(gubun){

            $(".gubun_detail").css('display','none');
            $("#gubun_detail_"+gubun).css('display','');
        }

        function check_form(obj, target){
            target = $("#"+target);
            if($("#"+obj).is(':checked')){
                target.val("");
                target.attr('readonly',false);
            }else{
                target.val("");
                target.attr('readonly',true);
            }
        }

        function claim_init(){
            $("#claim_str").val('');
        }

        function claim_insert(){
            var gubun = "CHANGE";
            if($("#req_claim_gubun_refund").is(":checked")){
                gubun = "REFUND";
            }

            var form = "";
            var result = true;
            var str = "";

            gubun == "REFUND" ? str += "반품관련 / " : str += "품절관련 / ";

            if(gubun == "REFUND") {

                if(!$("#chk1").is(":checked") || $("#chk1_val").val() == ""){
                    alert("택배사를 입력해 주십시오.");
                    $("#chk1_val").focus();
                    return false;
                }else if($("#chk1").is(":checked")){
                    str += $("#chk1").val() + "(" + $("#chk1_val").val() +") / ";
                }

                if(!$("#chk3").is(":checked") && !$("#chk5").is(":checked")) {
                    alert("선불/착불 여부를 선택해 주십시오.");
                    return false;
                } else {

                    const both_checked 
                        = $("#chk3").is(":checked") && $("#chk5").is(":checked") ? true : false

                    if (both_checked) {
                        alert("선불/착불 여부를 하나만 선택해 주십시오.");
                        return false;
                    }

                    if($("#chk3").is(":checked")) {
                        str += $("#chk3").val() + " / ";
                    } else if($("#chk5").is(":checked")) {
                        if($("#chk5_val").val() == "") {
                            alert("착불금액을 입력해 주십시오.");
                            $("#chk5_val").focus();
                            return false;
                        } else {
                            str += $("#chk5").val() + " ("+Comma($("#chk5_val").val()) +"원) / ";
                        }
                    }
                }

                if($("#chk7").is(":checked") && $("#chk7_val").val() == "") {
                    alert("동봉액을 입력해 주십시오.");
                    $("#chk7_val").focus();
                    return false;
                } else if($("#chk7").is(":checked")){
                    str += $("#chk7").val() + " ("+Comma($("#chk7_val").val()) +"원) / ";
                }

                if(!$("#chk2").is(":checked") && !$("#chk4").is(":checked")){
                    alert("상품이상 유/무를 선택해 주십시오.");
                    return false;
                } else {

                    const both_checked 
                        = $("#chk2").is(":checked") && $("#chk4").is(":checked") ? true : false

                    if (both_checked) {
                        alert("상품이상 유/무 여부를 하나만 선택해 주십시오.");
                        return false;
                    }

                    if($("#chk2").is(":checked")) {
                        str += $("#chk2").val() + " / ";
                    } else if($("#chk4").is(":checked")) {
                        if($("#chk4_val").val() == "") {
                            alert("상품이상 사유를 입력해 주십시오.");
                            $("#chk4_val").focus();
                            return false;
                        } else {
                            str += $("#chk4").val() + "("+ $("#chk4_val").val() + ") / ";
                        }
                    }

                }

                if(!$("#chk6").is(":checked") || $("#chk6_val").val() == ""){
                    alert("환불정보를 입력해 주십시오.");
                    $("#chk6_val").focus();
                    return false;
                }else if($("#chk6").is(":checked")){
                    str += $("#chk6").val() + "(" + $("#chk6_val").val() +") / ";
                }

                if($("#chk8").is(":checked")){
                    str += $("#chk8").val() + " / ";
                }

                if($("#chk9").is(":checked") && $("#chk9_val").val() == ""){
                    alert("기타사유를 입력해 주십시오.");
                    $("#chk9_val").focus();
                    return false;
                }else if($("#chk9").is(":checked")){
                    str += $("#chk9").val() + "(" + $("#chk9_val").val() +") / ";
                }

            }else{
                if($("#chk11").is(":checked")){
                    str += $("#chk11").val() + " / ";
                }
                if($("#chk12").is(":checked")){
                    str += $("#chk12").val() + " / ";
                }
                if($("#chk13").is(":checked")){
                    str += $("#chk13").val() + " / ";
                }
                if($("#chk14").is(":checked")){
                    str += $("#chk14").val() + " / ";
                }
                if($("#chk15").is(":checked")){
                    str += $("#chk15").val() + " / ";
                }

                if($("#chk16").is(":checked") && $("#chk16_val").val() == "" ){
                    alert("환불정보를 입력해 주십시오.");
                    $("#chk16_val").focus();
				    return false;
                }else{ if($("#chk16").is(":checked"))
                    str += $("#chk16").val() + "( "+ $("#chk16_val").val() + ") / ";
                }

                if($("#chk17").is(":checked") && $("#chk17_val").val() == "" ){
                    alert("기타사유를 입력해 주십시오.");
                    $("#chk17_val").focus();
				    return false;
                }else{ if($("#chk17").is(":checked"))
                    str += $("#chk17").val() + "( "+ $("#chk17_val").val() + ") / ";
                }
            }

            $("#claim_str").val(str);
        }

        /**
         * @return {boolean}
         */
        function SaveClaim(){
            var gubun = "change";
            if($("#req_claim_gubun_refund").is(":checked")){
                gubun = "refund";
            }
            if($("#claim_str").val() === ""){
                alert('클레임 내용을 입력해 주세요.');
                $("#claim_str").focus();
                return false;
            }

            var frm = $("#frm_change");
            //frm.submit();
            $.ajax({
                async: true,
                type: 'post',
                url: '/partner/order/ord01/claim/comments',
                data: frm.serialize(),
                success: function (data) {
                    if(data.code == "200"){
                        document.location.reload();
                    }
                },
                error: function(request, status, error) {
                    console.log("error")
                }
            });

        }

        function dlvSubmitCallback() {
            location.reload();
        }

        $('.dlv-info-btn').click(function(e) {
            e.preventDefault();
            const url = '/partner/order/ord01/dlv/' + ord_no + '/' + ord_opt_no;
            const product = window.open(url, "_blank", "toolbar=no,scrollbars=yes,resizable=yes,status=yes,top=500,left=500,width=1200,height=800");
        });

    </script>
@stop

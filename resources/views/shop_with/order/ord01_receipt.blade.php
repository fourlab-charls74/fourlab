@extends('shop_with.layouts.layout-nav')
@section('title','거래명세표')
@section('content')
<div class="container-fluid show_layout py-3">
    <div class="page_tit mb-3 d-flex align-items-center justify-content-between">
        <div>
            <h3 class="d-inline-flex">거래명세표</h3>
            <div class="d-inline-flex location">
                <span class="home"></span>
                <span>/ 주문</span>
                <span>/ 거래명세표</span>
            </div>
        </div>
        <div>
            <a href="#" onclick="window.close()" class="btn btn-sm btn-primary shadow-sm">닫기</a>
            <a href="#" onclick="window.print()" class="btn btn-sm btn-primary shadow-sm mr-1">인쇄</a>
        </div>
    </div>
    <div class="card_wrap aco_card_wrap">
        <div class="card shadow">
            <div class="card-header mb-0">
                <a href="#" class="m-0 font-weight-bold">주문정보</a>
            </div>
            <div class="card-body">
                <div class="row_wrap">
                    <div class="row">
                        <div class="col-12">
                            <div class="table-box-ty2 mobile">
                                <table class="table incont table-bordered" id="dataTable" width="100%" cellspacing="0">
                                    <colgroup>
                                        <col width="100px">
                                        <col width="23%">
                                        <col width="100px">
                                        <col width="23%">
                                        <col width="100px">
                                        <col width="23%">
                                    </colgroup>
                                    <tr>
                                        <th>출고형태</th>
                                        <td>
                                            <div class="txt_box">{{$ord->ord_type_nm}}</div>
                                        </td>
                                        <th>판매구분</th>
                                        <td>
                                            <div class="txt_box">{{$ord->ord_kind_nm}}</div>
                                        </td>
                                        <th>주문상태</th>
                                        <td>
                                            <div class="txt_box">{{$ord->ord_state_nm}}</div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>판매처</th>
                                        <td>
                                            <div class="txt_box">{{$ord->sale_place}}</div>
                                        </td>
                                        <th>결제수단</th>
                                        <td>
                                            <div class="txt_box">
                                                    @if ($ord->pay_type_nm == "")
                                                    <span style="color:red">결제 정보가 존재하지 않습니다.</span>
                                                @else
                                                    {{$ord->pay_type_nm}}
                                                @endif
                                            </div>
                                        </td>
                                        <th>입금정보</th>
                                        <td>
                                            <div class="txt_box">
                                                입금자 : {{$ord->bank_inpnm}}<br/>
                                                은행 : {{$ord->bank_code}}<br/>
                                                계좌 : {{$ord->bank_number}}
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
                <a href="#" class="m-0 font-weight-bold">주문자</a>
            </div>
            <div class="card-body">
                <div class="row_wrap">
                    <div class="row">
                        <div class="col-12">
                            <div class="table-box-ty2 mobile">
                                <table class="table incont table-bordered" id="dataTable" width="100%" cellspacing="0">
                                    <colgroup>
                                        <col width="100px">
                                        <col width="23%">
                                        <col width="100px">
                                        <col width="23%">
                                        <col width="100px">
                                        <col width="23%">
                                    </colgroup>
                                    <tr>
                                        <th>ID</th>
                                        <td>
                                            <div class="txt_box">{{$ord->user_id}}</div>
                                        </td>
                                        <th>이름</th>
                                        <td>
                                            <div class="txt_box">{{$ord->user_nm}}</div>
                                        </td>
                                        <th>전화</th>
                                        <td>
                                            <div class="txt_box">{{$ord->phone}}</div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>휴대전화</th>
                                        <td>
                                            <div class="txt_box">{{$ord->mobile}}</div>
                                        </td>
                                        <th>Email</th>
                                        <td colspan="4">
                                            <div class="txt_box"></div>
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
                                    <colgroup>
                                        <col width="100px">
                                        <col width="23%">
                                        <col width="100px">
                                        <col width="23%">
                                        <col width="100px">
                                        <col width="23%">
                                    </colgroup>
                                    <tr>
                                        <th>이름</th>
                                        <td>
                                            <div class="txt_box">{{$ord->r_nm}}</div>
                                        </td>
                                        <th>전화</th>
                                        <td>
                                            <div class="txt_box">{{$ord->r_phone}}</div>
                                        </td>
                                        <th>휴대전화</th>
                                        <td>
                                            <div class="txt_box">{{$ord->r_mobile}}</div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>주소</th>
                                        <td colspan="5">
                                            <div class="txt_box">
                                                {{$ord->r_zipcode}} {{$ord->r_addr1}} {{$ord->r_addr2}}
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>메시지</th>
                                        <td colspan="5">
                                            <div class="txt_box">{{$ord->dlv_msg}}</div>
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
                <a href="#" class="m-0 font-weight-bold">주문정보</a>
            </div>
            <div class="card-body">
                <div class="row_wrap">
                    <div class="row">
                        <div class="col-12">
                            <div class="table-responsive">
                                <table class="table table-bordered th_border_none">
                                    <thead>
                                        <tr>
                                            <th>주문상태</th>
                                            <th>스타일넘버</th>
                                            <th>상품명</th>
                                            <th>옵션</th>
                                            <th>수량</th>
                                            <th>판매가</th>
                                            <th>주문액</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @if(count($opts) > 0)
                                            @foreach($opts as $opt)
                                                <tr>
                                                    <td class="text-center">{{$opt->ord_state}}</td>
                                                    <td class="text-center">{{$opt->style_no}}</td>
                                                    <td class="text-center">{{$opt->goods_nm}}</td>
                                                    <td class="text-center">{{$opt->goods_opt}}</td>
                                                    <td class="text-right">{{$opt->qty}}</td>
                                                    <td class="text-right">{{number_format($opt->price)}}</td>
                                                    <td class="text-right">{{number_format($opt->ord_amt)}}</td>
                                                </tr>
                                            @endforeach
                                            <tr>
                                                <td colspan='3'></td>
                                                <td class="text-center" style="background:#FFECEC">적립금사용액</td>
                                                <td class="text-right">{{number_format($ord->point_amt)}}</td>
                                                <td class="text-center" style="background:#F4F5FF">주문총액</td>
                                                <td class="text-right">{{number_format($tot_ord_amt)}}</td>
                                            </tr>
                                            <tr>
                                                <td colspan='3'></td>
                                                <td class="text-center" style="background:#FFECEC">쿠폰사용액</td>
                                                <td class="text-right">{{number_format($ord->coupon_amt)}}</td>
                                                <td class="text-center" style="background:#F4F5FF">배송비</td>
                                                <td class="text-right">{{number_format($ord->dlv_amt)}}</td>
                                            </tr>
                                            <tr>
                                                <td colspan='5' class="hdx">
                                                    <font color="red">*</font>
                                                    <font color="blue">
                                                        <b>주문총액</b> = 매출액 (배송비 제외)<br>
                                                        <font color="red">*</font> 
                                                        <b>입금총액</b>(VAT포함) = 주문총액 + 배송비 - (적립금사용액 + 쿠폰사용액)
                                                    </font>
                                                </td>
                                                <td class="text-center" rowspan="2">입금총액</td>
                                                <td class="text-right" rowspan="2">{{number_format($tot_recv_amt)}}</td>
                                            </tr>
                                        @else
                                            <tr>
                                                <td colspan="8">구매내역이 없습니다.</td>
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
    </div>
</div>
<style>
    .opt-list thead th{
        text-align:center
    }
    .opt-list td,
    .opt-list th{
        vertical-align:middle;
    }
</style>
@stop
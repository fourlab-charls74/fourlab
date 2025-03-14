@extends('shop_with.layouts.layout-nav')
@section('title','POS')
@section('content')

<link href="{{ URL::asset('css/pos.css')}}" rel="stylesheet" type="text/css" />

<div id="pos" class="d-flex flex-column w-100 m-0" style="min-height: 100vh;max-width: 100vw;">
    {{-- 포스 헤더 --}}
    <div id="pos_header" class="d-flex justify-content-between align-items-center bg-white pl-3" style="border-bottom: 1px solid #999;">
        <h1 style="width:90px;"><img src="/theme/{{config('shop.theme')}}/images/pc_logo_white.png" alt="" class="w-100"></h1>
        <div class="d-flex align-items-center">
            <p class="fw-b mr-5">[{{ @$store->store_cd }}] {{ @$store->store_nm }}</p>
            <p id="clock" class="fw-sb mr-4" style="width: 230px;"></p>
            <button type="button" id="home_btn" onclick="return setScreen('pos_main');" class="butt butt-close bg-trans" style="width:55px;height:50px;border-left:1px solid #999"><i class="fa fa-home" aria-hidden="true"></i></button>
            <button type="button" onclick="return window.close();" class="butt butt-close bg-trans" style="width:55px;height:50px;border-left:1px solid #999"><i class="fa fa-times" aria-hidden="true"></i></button>
        </div>
    </div>

    {{-- 메인화면 --}}
    <div id="pos_main" class="flex-1 d-flex justify-content-center align-items-center">
        <div class="main-grid fc-white">
            <button type="button" class="butt fs-20 fw-sb bg-orange" style="grid-area:a;" onclick="return setScreen('pos_order');">
                <i class="fa fa-shopping-bag d-block mb-3" aria-hidden="true" style="font-size:100px;"></i>
                주문등록
            </button>
            <div class="d-flex flex-column fc-white fs-12 bg-brown p-1" style="grid-area:b;">
                <p class="text-center fs-16 fw-sb p-4" style="border-bottom:2px solid #999;">오늘 매출분석</p>
                <ul class="p-3">
                    <li class="d-flex justify-content-between fs-14 fw-sb mb-2"><p>총매출액</p><p class="fc-red"><span id="to_pay_amt">0</span>원</p></li>
                    <li class="d-flex justify-content-between mb-2"><p>주문금액</p><p><span id="to_ord_amt">0</span>원</p></li>
                    <li class="d-flex justify-content-between mb-2"><p>주문건수</p><p><span id="to_ord_cnt">0</span>건</p></li>
                    <li class="d-flex justify-content-between"><p>판매수량</p><p><span id="to_qty">0</span>개</p></li>
                </ul>
            </div>
            <button type="button" class="butt fs-14 fw-sb bg-blue" style="grid-area:c;" onclick="return setScreen('pos_today')">
                <i class="fa fa-search d-block mb-3" aria-hidden="true" style="font-size:50px;"></i>
                당일판매내역
            </button>
            <button type="button" class="butt fs-14 fw-sb bg-gray" style="grid-area:d;" onclick="return alert('준비중입니다.');">
                <i class="fa fa-plus-circle d-block mb-3" aria-hidden="true" style="font-size:50px;"></i>
                부가기능
            </button>
            <div class="d-flex flex-column justify-content-between align-items-stretch align-items-center fs-12 bg-navy p-1" style="grid-area:e;">
                <div class="w-100">
                    <p class="text-center fs-16 fw-sb p-4" style="border-bottom:2px solid #999;">직전결제내역</p>
                    <ul class="p-3">
                        <li class="d-flex justify-content-between fs-14 fw-sb mb-2"><p>총 결제금액</p><p class="fc-red"><span id="po_recv_amt">0</span>원</p></li>
                        <li class="d-flex justify-content-between mb-2"><p>주문금액</p><p><span id="po_ord_amt">0</span>원</p></li>
                        <li class="d-flex justify-content-between mb-2"><p>판매할인금액</p><p><span id="po_dc_amt">0</span>원</p></li>
                        <li class="d-flex justify-content-between mb-2"><p>쿠폰할인금액</p><p><span id="po_coupon_amt">0</span>원</p></li>
                        <li class="d-flex justify-content-between mb-2"><p>적립금사용</p><p><span id="po_point_amt">0</span>원</p></li>
                        <li class="d-flex justify-content-between"><p>결제시간</p><p id="po_ord_date"></p></li>
                    </ul>
                    <input type="hidden" name="po_ord_no" value="">
                </div>
                <button type="button" class="butt fc-navy fw-b bg-white m-2" style="height:60px;border-radius:12px;" onclick="return viewPrevOrder();">영수증 조회</button>
            </div>
            {{-- <button type="button" class="butt fs-14 fw-sb bg-mint" style="grid-area:f;" data-toggle="modal" data-target="#searchWaitingModal">
                <i class="fa fa-bookmark d-block mb-3" aria-hidden="true" style="font-size:50px;"></i>
                대기 <span id="waiting_cnt">0</span>
            </button> --}}
            <button type="button" class="butt fs-14 fw-sb bg-mint" style="grid-area:f;" data-toggle="modal" data-target="#addCouponModal">
                <i class="fa fa-credit-card d-block mb-3" aria-hidden="true" style="font-size:50px;"></i>
                쿠폰등록
            </button>
            <button type="button" class="butt fs-14 fw-sb bg-red" style="grid-area:g;" data-toggle="modal" data-target="#searchOrdNoModal">
                <i class="fa fa-reply d-block mb-3" aria-hidden="true" style="font-size:50px;"></i>
                환불
            </button>
        </div>
    </div>

    {{-- 주문등록화면 --}}
    <div id="pos_order" class="flex-1 d-none">
        {{-- cur_ord_state: new(신규), waiting(대기) --}}
        <input type="hidden" name="cur_ord_state" value="new">
        {{-- removed_goods: 주문대기 판매처리 시 삭제할 ord_opt_no배열 --}}
        <input type="hidden" name="removed_goods" value="">
        <div class="flex-5 p-3">
            <div class="d-flex flex-column">
                {{-- <button type="button" class="butt w-100 fc-white fs-14 br-1 bg-gray mb-3" style="height: 60px;" data-toggle="modal" data-target="#searchProductModal"><i class="fa fa-search mr-2" aria-hidden="true"></i>상품 검색</button> --}}
                <div class="butt w-100 br-1 mb-3 b-none">
                    <div class="d-flex align-items-center br-2 b-1-gray bg-white shadow-box p-2 pl-4">
                        <select name="search_prd_type_out" id="search_prd_type_out" class="sel fs-12" style="min-width: 120px;">
                            <option value="prd_cd">상품코드</option>
                            <option value="goods_nm">상품명</option>
                            <option value="style_no">스타일넘버</option>
                        </select>
                        <input type="text" class="flex-1 inp h-40 fs-12 mr-1" id="search_prd_keyword_out" name="search_prd_keyword_out" placeholder="검색어를 입력하세요">
                        <button type="button" id="search_btn_out" class="butt br-2 bg-lightgray p-2 pl-3 pr-3"><i class="fa fa-search fc-black fs-10" aria-hidden="true"></i></button>
                    </div>
                </div>
                <div class="d-flex mb-4">
                    <div class="table-responsive">
                        <div id="div-gd" class="ag-theme-balham" style="font-size: 18px;"></div>
                    </div>
                </div>
                <div class="d-flex mb-4">
                    <div class="flex-1 mr-4">
                        <div class="d-flex justify-content-between align-items-center fs-15 fw-b mb-3">
                            <p>총 주문금액</p>
                            <p><strong id="total_order_amt" class="fc-red fs-20 fw-b mr-1">0</strong>원</p>
                        </div>
                        <div class="d-flex justify-content-between align-items-center fs-12 fw-sb mb-2">
                            <p>결제한 금액</p>
                            <p><strong id="payed_amt" class="fw-b mr-1">0</strong>원</p>
                        </div>
                        <div class="d-flex justify-content-between align-items-center fs-12 fw-sb">
                            <p>거스름돈</p>
                            <p><strong id="change_amt" class="fc-red fw-b mr-1">0</strong>원</p>
                        </div>
                        <div class="d-flex justify-content-between align-items-center fs-12 fw-sb mt-3 mb-2">
                            <p>총 주문수량</p>
                            <p><strong id="total_order_qty" class="fw-b mr-1">0</strong>개</p>
                        </div>
                        <div class="d-flex justify-content-between align-items-center fs-12 fw-sb">
                            <p>쿠폰할인금액</p>
                            <p><strong id="total_coupon_discount_qty" class="fw-b mr-1">0</strong>원</p>
                        </div>
                    </div>
                    <div class="flex-2 d-flex">
                        <button type="button" class="butt flex-2 fc-white fs-20 fw-b br-2 bg-blue p-2 mr-3" data-toggle="modal" data-target="#payModal" data-title="신용카드 결제" data-pay-type="card_amt">
                            <span id="card_amt">0</span>
                            <input type="hidden" name="card_amt" value="0">
                            <span class="d-block fs-14 fw-sb mt-1">신용카드</span>
                        </button>
                        <div class="flex-3 d-flex flex-column mr-3">
                            <button type="button" class="butt flex-1 fc-white fs-20 fw-b br-2 bg-blue p-2 mb-3" data-toggle="modal" data-target="#payModal" data-title="현금 결제" data-pay-type="cash_amt">
                                <span id="cash_amt">0</span>
                                <input type="hidden" name="cash_amt" value="0">
                                <span class="d-block fs-14 fw-sb mt-1">현금</span>
                            </button>
                            <button type="button" class="butt flex-1 fc-white fs-20 fw-b br-2 bg-gray p-2" data-toggle="modal" data-target="#payModal" data-title="적립금 사용" data-pay-type="point_amt">
                                <span id="point_amt">0</span>
                                <input type="hidden" name="point_amt" value="0">
                                <span class="d-block fs-14 fw-sb mt-1">적립금</span>
                            </button>
                        </div>
                        <button type="button" class="butt flex-2 fc-white fs-20 fw-b br-2 bg-mint p-2" onclick="return sale();">판매</button>
                    </div>
                </div>
                <div class="d-flex">
                    <div class="flex-1 d-flex mr-4">
                        <button type="button" class="butt flex-1 fc-white fs-16 fw-sb br-1 bg-red p-4" onclick="return cancelOrder();">주문 초기화</button>
                        {{-- <button type="button" class="butt flex-1 fc-white fs-16 fw-sb br-1 bg-gray p-4" onclick="return waiting();">대기</button> --}}
                    </div>
                    <div class="flex-2">
                        <textarea name="memo" id="memo" rows="2" class="w-100 h-100 fs-12 p-2 mr-2 noresize" placeholder="특이사항"></textarea>
                    </div>
                </div>
            </div>
        </div>
        <div class="flex-3 p-3">
            <div class="d-flex justify-content-between fs-16 fw-sb mt-2 mb-3">
                <p>주문번호</p>
                <p id="ord_no" class="fc-red"></p>
            </div>
            <div class="d-flex mb-4">
                <div class="flex-1 position-relative fw-sb b-2-gray p-4" style="min-height:250px;">
                    <div id="no_user" class="d-flex justify-content-center align-items-center h-100 fc-gray fw-m">고객정보가 없습니다.</div>
                    <div id="user" class="d-none">
                        <p class="fs-18 fw-b mb-3"><span id="user_nm"></span> <span id="user_info" class="fs-16 fw-sb"></span> <span class="fs-14 fw-m">- <span id="user_id_txt"></span></span></p>
                        <div class="d-flex align-items-center fs-12 mb-2">
                            <p style="min-width: 80px;">연락처</p>
                            <p id="user_phone"></p>
                        </div>
                        <div class="d-flex align-items-center fs-12 mb-2">
                            <p style="min-width: 80px;">이메일</p>
                            <p id="user_email"></p>
                        </div>
                        <div class="d-flex align-items-center fs-12 mb-2">
                            <p style="min-width: 80px;">주소</p>
                            <p id="user_address" class="fs-10"></p>
                        </div>
                        <div class="d-flex align-items-center fs-12">
                            <p style="min-width: 80px;">적립금</p>
                            <p id="user_point" class="fc-red fw-b">0</p>
                        </div>
                    </div>
                    <div class="d-flex" style="position:absolute;bottom:12px;right:12px;">
                        <button type="button" class="butt fc-white fs-10 fw-sb br-1 bg-gray pb-2 pt-2 pl-3 pr-3 mr-2" data-toggle="modal" data-target="#searchMemberModal">고객검색</button>
                        <button type="button" class="butt fc-white fs-10 fw-sb br-1 bg-blue pb-2 pt-2 pl-3 pr-3" data-toggle="modal" data-target="#addMemberModal">고객등록</button>
                    </div>
                </div>
            </div>
            <div class="d-flex align-items-center mb-4">
                <div class="d-flex b-2-gray mr-4" style="width:150px;height:150px;">
                    <img src="" alt="" id="cur_img" class="w-100">
                </div>
                <div class="flex-1">
                    <ul class="fs-12 fw-sb">
                        <li class="d-flex justify-content-between mb-2">
                            <p class="fc-blue fw-b" style="min-width: 80px;">상품명</p>
                            <p class="text-right" id="cur_goods_nm"></p>
                        </li>
                        <li class="d-flex justify-content-between mb-2">
                            <p class="fc-blue fw-b" style="min-width: 80px;">컬러명</p>
                            <p class="text-right" id="cur_goods_color"></p>
                        </li>
                        <li class="d-flex justify-content-between mb-2">
                            <p class="fc-blue fw-b" style="min-width: 80px;">사이즈명</p>
                            <p class="text-right" id="cur_goods_size"></p>
                        </li>
                        <li class="d-flex justify-content-between">
                            <p class="fc-blue fw-b" style="min-width: 80px;">상품코드</p>
                            <p class="text-right" id="cur_prd_cd"></p>
                        </li>
                    </ul>
                </div>
            </div>
            <div class="d-flex">
                <div class="flex-1 b-2-gray">
                    <table class="prd_info_table w-100 fs-10">
                        <tr>
                            <th>수량</th>
                            <td class="pl-3 pr-3"><input type="text" id="cur_qty" class="inp_num" value=""></td>
                        </tr>
                        <tr>
                            <th>단가</th>
                            <td id="cur_price" class="pr-3">-</td>
                        </tr>
                        <tr>
                            <th>TAG가</th>
                            <td id="cur_goods_sh" class="pr-3">-</td>
                        </tr>
                        <tr>
                            <th>판매유형</th>
                            <td>
                                <div class="d-flex pl-3 pr-1">
                                    <select name="sale_type" id="sale_type" class="sel w-100" onchange="return updateOrderValue('sale_type', event.target.value);"></select>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <th>행사명</th>
                            <td>
                                <div class="d-flex pl-3 pr-1">
                                    <select name="pr_code" id="pr_code" class="sel w-100" onchange="return updateOrderValue('pr_code', event.target.value)">
                                        @foreach (@$pr_codes as $pr_code)
                                            <option value="{{ $pr_code->pr_code }}">{{ $pr_code->pr_code_nm }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <th>쿠폰</th>
                            <td>
                                <div class="d-flex pl-3 pr-1">
                                    <select name="coupon_no" id="coupon_no" class="sel w-100" onchange="return updateOrderValue('coupon_no', event.target.value, event)">
                                        <option value=''>-- 선택 안함 --</option>
                                    </select>
                                </div>
                            </td>
                        </tr>
                    </table>
                </div>
                {{-- <div class="flex-2 d-flex justify-content-end">
                    <div id="product_calculator" class="calculator-grid product fs-20">
                        <input type="text" id="product_press_amt" class="inp fc-black fs-20 fw-b text-right pr-3" style="grid-area:a;border:2px solid #bbb;" value="0">
                        <button type="button" class="butt bg-white" value="1" style="grid-area:b;">1</button>
                        <button type="button" class="butt bg-white" value="2" style="grid-area:c;">2</button>
                        <button type="button" class="butt bg-white" value="3" style="grid-area:d;">3</button>
                        <button type="button" class="butt bg-white" value="4" style="grid-area:e;">4</button>
                        <button type="button" class="butt bg-white" value="5" style="grid-area:f;">5</button>
                        <button type="button" class="butt bg-white" value="6" style="grid-area:g;">6</button>
                        <button type="button" class="butt bg-white" value="7" style="grid-area:h;">7</button>
                        <button type="button" class="butt bg-white" value="8" style="grid-area:i;">8</button>
                        <button type="button" class="butt bg-white" value="9" style="grid-area:j;">9</button>
                        <button type="button" class="butt bg-white" value="0" style="grid-area:k;">0</button>
                        <button type="button" class="butt bg-white" value="00" style="grid-area:l;">00</button>
                        <button type="button" class="butt bg-white" value="000" style="grid-area:m;">000</button>
                        <button type="button" class="butt fs-14 bg-lightgray" value="removeAll" style="grid-area:n;">clear</button>
                        <button type="button" class="butt fs-14 bg-lightgray" value="remove" style="grid-area:o;"><i class="fa fa-arrow-left" aria-hidden="true"></i></button>
                        <button type="button" class="butt fs-14 fc-white bg-gray" value="qty" style="grid-area:p;">수량변경</button>
                        <button type="button" class="butt fs-14 fc-white bg-gray" value="price" style="grid-area:q;">단가변경</button>
                    </div>
                </div> --}}
            </div>
        </div>
    </div>

    {{-- 당일판매내역화면 --}}
    <div id="pos_today" class="flex-1 d-none flex-column p-3">
        <div class="d-flex">
            <div class="flex-3">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h6 class="m-0 fs-12 fw-b">총 <span id="gd-order-total" class="text-primary">0</span> 건</h6>
                    <div class="d-flex fs-08">
                        <div class="d-flex align-items-center date-select-inbox mr-2">
                            <div class="docs-datepicker form-inline-inner input_box" style="width: 130px;">
                                <div class="input-group">
                                    <input type="text" class="form-control form-control-sm docs-date" name="ord_sdate" value="{{ @$today }}" autocomplete="off" disable>
                                    <div class="input-group-append">
                                        <button type="button" class="btn btn-outline-secondary docs-datepicker-trigger p-0 pl-2 pr-2" disable>
                                            <i class="fa fa-calendar" aria-hidden="true"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="docs-datepicker-container"></div>
                            </div>
                            <span class="text_line ml-2 mr-2">~</span>
                            <div class="docs-datepicker form-inline-inner input_box" style="width:130px;">
                                <div class="input-group">
                                    <input type="text" class="form-control form-control-sm docs-date" name="ord_edate" value="{{ @$today }}" autocomplete="off">
                                    <div class="input-group-append">
                                        <button type="button" class="btn btn-outline-secondary docs-datepicker-trigger p-0 pl-2 pr-2">
                                            <i class="fa fa-calendar" aria-hidden="true"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="docs-datepicker-container"></div>
                            </div>
                        </div>
                        <select name="ord_field" id="ord_field" class="sel b-1-gray br-05 pl-2 mr-2" style="width:130px;min-height:30px;">
                            <option value="desc">최신순</option>
                            <option value="asc">오래된순</option>
                        </select>
                        <select name="limit" id="limit" class="sel b-1-gray br-05 pl-2 mr-2" style="width:130px;min-height:30px;">
                            <option value="100">100개씩 보기</option>
                            <option value="200">200개씩 보기</option>
                            <option value="500">500개씩 보기</option>
                        </select>
                        <button type="button" class="butt fc-white fs-10 br-05 bg-navy" style="width:80px;" onclick="return SearchOrder();">검색</button>
                    </div>
                </div>
                <div class="flex-1 table-responsive">
                    <div id="div-gd-order" class="ag-theme-balham" style="font-size: 18px;"></div>
                </div>
            </div>
            <div class="flex-2 d-flex justify-content-center">
                <div class="d-flex flex-column justify-content-between w-100 h-100 p-5" style="min-width:300px;max-width:650px;max-height:890px;border:7px solid #222;overflow:auto;">
                    <div class="d-flex flex-column align-items-center">
                        <div class="mb-5"><img src="/theme/{{config('shop.theme')}}/images/pc_logo_white.png" alt="" class="w-100"></div>
                        <div class="d-flex flex-column w-100 fs-12 mb-4">
                            <div class="d-flex justify-content-between">
                                <p>주문번호</p>
                                <p id="od_ord_no" class="fw-sb">-</p>
                            </div>
                            <div class="d-flex justify-content-between">
                                <p>주문일자</p>
                                <p id="od_ord_date">-</p>
                            </div>
                            <div class="d-flex justify-content-between">
                                <p>매장명</p>
                                <p>{{ @$store->store_nm }}</p>
                            </div>
                        </div>
                        <table class="w-100 fs-10 b-1-gray mb-2" style="border-width:0 0 1px;">
                            <colgroup>
                                <col width="50%">
                                <col width="15%">
                                <col width="7%">
                                <col width="28%">
                            </colgroup>
                            <thead>
                                <tr class="b-1-gray" style="border-width:1px 0;">
                                    <th class="fw-m pt-1 pb-1 pl-1">상품명</th>
                                    <th class="text-center fw-m pt-1 pb-1">단가</th>
                                    <th class="text-center fw-m pt-1 pb-1">수량</th>
                                    <th class="text-right fw-m pt-1 pb-1 pr-1">금액</th>
                                </tr>
                            </thead>
                            <tbody id="od_prd_list"></tbody>
                        </table>
                        <div class="d-flex flex-column w-100 fs-12 b-1-gray pb-2 mb-4" style="border-width: 0 0 1px;">
                            <div class="d-flex justify-content-between mb-1">
                                <p>주문합계</p>
                                <p id="od_ord_amt" class="fw-sb">-</p>
                            </div>
                            <div class="d-flex justify-content-between fs-10 pl-2 mb-2">
                                <p>&#8722; 판매할인금액</p>
                                <p id="od_dc_amt">-</p>
                            </div>
                            <div class="d-flex justify-content-between fs-10 pl-2 mb-2">
                                <p>&#8722; 쿠폰할인금액</p>
                                <p id="od_coupon_amt">-</p>
                            </div>
                            <div class="d-flex justify-content-between fs-10 pl-2 mb-2">
                                <p>&#8722; 적립금사용금액</p>
                                <p id="od_point_amt">-</p>
                            </div>
                            <div class="d-flex justify-content-between fs-16 fw-b">
                                <p>Total</p>
                                <p id="od_recv_amt">-</p>
                            </div>
                        </div>
                        <div class="d-flex flex-column w-100 fs-12">
                            <div class="d-flex justify-content-between mb-1">
                                <p>[ 결제수단 ]</p>
                                <p id="od_pay_type">-</p>
                            </div>
                            <div class="d-flex justify-content-between mb-1">
                                <p>[ 주문자정보 ]</p>
                                <p id="od_user_info">-</p>
                            </div>
                            <div class="d-flex justify-content-between mb-1">
                                <p>[ 주문자연락처 ]</p>
                                <p id="od_phone">-</p>
                            </div>
                            <div class="d-flex justify-content-between">
                                <p>[ 특이사항 ]</p>
                                <p id="od_dlv_comment" class="text-right w-75">-</p>
                            </div>
                        </div>
                    </div>
                    <div class="d-flex justify-content-center">
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- MODAL --}}
<div id="pos-modal" class="show_layout">
    {{-- 대기내역 검색 --}}
    <div class="modal fade" id="searchWaitingModal" tabindex="-1" role="dialog" aria-labelledby="searchWaitingModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document" style="max-width: 33%;min-width: 500px;">
            <div class="modal-content">
                <div class="modal-body">
                    <div class="card">
                        <div class="card-header">
                            <button type="button" class="fs-20 close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                            <h5 class="mt-1 fs-14 fw-b">대기 내역</h5>
                        </div>
                        <div class="card-body b-none mt-4">
                            <div class="d-flex mb-3">
                                <div class="table-responsive">
                                    <div id="div-gd-waiting" class="ag-theme-balham" style="font-size: 18px;"></div>
                                </div>
                            </div>
                            <div class="d-flex justify-content-center align-items-center fs-12">
                                <button type="button" class="butt fc-white fw-sb br-05 bg-mint p-2 pl-4 pr-4 mr-2" onclick="return applyWaiting();">선택</button>
                                <button type="button" class="butt fc-white fw-sb br-05 bg-red p-2 pl-4 pr-4 mr-2" onclick="return removeWaiting();">삭제</button>
                                <button type="button" class="butt fc-white fw-sb br-05 bg-gray p-2 pl-4 pr-4" data-dismiss="modal" aria-label="Close">취소</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    {{-- 상품검색모달 --}}
    <div class="modal fade" id="searchProductModal" tabindex="-1" role="dialog" aria-labelledby="searchProductModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document" style="max-width: 90%;">
            <div class="modal-content">
                <div class="modal-body">
                    <div class="card">
                        <div class="card-header">
                            <button type="button" class="fs-20 close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                            <h5 class="mt-1 fs-14 fw-b">상품 검색</h5>
                        </div>
                        <div class="card-body b-none mt-4">
                            <div class="d-flex align-items-center br-2 b-1-gray bg-white shadow-box p-2 pl-4 mb-3">
                                <select name="search_prd_type" id="search_prd_type" class="sel fs-12" style="min-width: 120px;">
                                    <option value="prd_cd">상품코드</option>
                                    <option value="goods_nm">상품명</option>
                                    <option value="style_no">스타일넘버</option>
                                </select>
                                <input type="text" class="flex-1 inp h-40 fs-12 mr-1" id="search_prd_keyword" name="search_prd_keyword" placeholder="검색어를 입력하세요">
                                <button type="button" class="butt br-2 bg-lightgray p-3" onclick="return Search();"><i class="fa fa-search fc-black fs-10" aria-hidden="true"></i></button>
                            </div>
                            <div class="d-flex">
                                <div class="table-responsive">
                                    <div id="div-gd-product" class="ag-theme-balham" style="font-size: 18px;"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    {{-- 고객등록모달 --}}
    <div class="modal fade" id="addMemberModal" tabindex="-1" role="dialog" aria-labelledby="addMemberModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-body">
                    <div class="card">
                        <div class="card-header">
                            <button type="button" class="fs-20 close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                            <h5 class="mt-1 fs-14 fw-b">고객 등록</h5>
                        </div>
                        <div class="card-body b-none">
                            <form name="add_member">
                                <table class="table incont table-bordered mt-2" id="dataTable" width="100%" cellspacing="0">
                                    <colgroup>
                                        <col width="130px">
                                    </colgroup>
                                    <tr>
                                        <th class="required">아이디</th>
                                        <td>
                                            <div class="flax_box inline_btn_box" style="padding-right:75px;">
                                                <input type="text" name="user_id" id="user_id" class="form-control form-control-sm">
                                                <input type="hidden" name="user_id_check" id="user_id_check" value="N">
                                                <a href="#" onclick="return checkUserId();" class="butt d-flex justify-content-center align-items-center fc-white fs-08 fw-sb br-05 bg-gray" style="width:70px;">중복확인</a>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th class="required">이름</th>
                                        <td>
                                            <div class="flax_box">
                                                <input type="text" name="name" id="name" class="form-control form-control-sm" value="">
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th class="required">성별</th>
                                        <td>
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
                                        </td>
                                    </tr>
                                    <tr>
                                        <th class="required">휴대폰</th>
                                        <td>
                                            <div class="d-flex flex-column flex-sm-row">
                                                <div class="form-inline mr-1 mb-2 mb-sm-0" style="width:100%;max-width:400px;vertical-align:top;">
                                                    <div class="form-inline-inner input_box" style="width:30%;">
                                                        <input type="text" name="mobile1" id="mobile1" class="form-control form-control-sm" maxlength="3" onkeyup="onlynum(this)">
                                                    </div>
                                                    <span class="text_line">-</span>
                                                    <div class="form-inline-inner input_box" style="width:29%;">
                                                        <input type="text" name="mobile2" id="mobile2" class="form-control form-control-sm" maxlength="4" onkeyup="onlynum(this)">
                                                    </div>
                                                    <span class="text_line">-</span>
                                                    <div class="form-inline-inner input_box" style="width:29%;">
                                                        <input type="text" name="mobile3" id="mobile3" class="form-control form-control-sm" maxlength="4" onkeyup="onlynum(this)">
                                                    </div>
                                                </div>
                                                <input type="hidden" name="user_mobile_check" id="user_mobile_check" value="N">
                                                <a href="#" onclick="return checkUserPhone();" class="butt d-flex justify-content-center align-items-center fc-white fs-08 fw-sb br-05 bg-gray p-1" style="min-width:70px;">중복확인</a>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>이메일</th>
                                        <td>
                                            <div class="flax_box">
                                                <input type="email" pattern="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,}$"
                                                    name="email" id="email" class="form-control form-control-sm">
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>마케팅정보 수신여부</th>
                                        <td>
                                            <div class="flax_box">
                                                <div class="custom-control custom-checkbox mr-3">
                                                    <input type="checkbox" name="send_mail_yn" id="send_mail_yn" class="custom-control-input" value="Y" checked>
                                                    <label class="custom-control-label" for="send_mail_yn">이메일 수신</label>
                                                </div>
                                                <div class="custom-control custom-checkbox">
                                                    <input type="checkbox" name="send_mobile_yn" id="send_mobile_yn" class="custom-control-input" value="Y" checked>
                                                    <label class="custom-control-label" for="send_mobile_yn">SMS 수신</label>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>주소</th>
                                        <td>
                                            <div class="d-flex flex-column input_box flax_box address_box">
                                                <div class="d-flex w-100 mb-2">
                                                    <input type="text" id="zipcode" name="zipcode" class="flex-1 form-control form-control-sm mr-2" readonly="readonly">
                                                    <a href="javascript:;" onclick="openFindAddress('zipcode', 'addr1')" class="butt d-flex justify-content-center align-items-center fc-white fs-08 fw-sb br-05 bg-navy" style="width:70px;">
                                                        <i class="fas fa-search fa-sm text-white-50 mr-1"></i>
                                                        검색
                                                    </a>
                                                </div>
                                                <input type="text" id="addr1" name="addr1" class="form-control form-control-sm w-100 mb-2" readonly="readonly">
                                                <input type="text" id="addr2" name="addr2" class="form-control form-control-sm w-100">
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>생년월일</th>
                                        <td>
                                            <div class="flax_box">
                                                <div class="form-inline mr-0 mr-sm-1 mb-1" style="width:100%;max-width:400px;vertical-align:top;">
                                                    <div class="form-inline-inner input_box" style="width:30%;">
                                                        <select name="yyyy" id="yyyy" class="form-control form-control-sm mr-1">
                                                            <option value="">년도</option>
                                                            @for($i = date("Y")-14; $i > date("Y")-114; $i--)
                                                                <option value="{{$i}}">{{$i}}</option>
                                                            @endfor
                                                        </select>
                                                    </div>
                                                    <span class="text_line">-</span>
                                                    <div class="form-inline-inner input_box" style="width:29%;">
                                                        <select name="mm" id="mm" class="form-control form-control-sm mr-1">
                                                            <option value="">월</option>
                                                            @for($i = 1; $i <= 12; $i++)
                                                                <option value="{{$i}}">{{$i}}</option>
                                                            @endfor
                                                        </select>
                                                    </div>
                                                    <span class="text_line">-</span>
                                                    <div class="form-inline-inner input_box" style="width:29%;">
                                                        <select name="dd" id="dd" class="form-control form-control-sm mr-1">
                                                            <option value="">일</option>
                                                            @for($i = 1; $i <= 31; $i++)
                                                                <option value="{{$i}}">{{$i}}</option>
                                                            @endfor
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="form-inline form-radio-box mt-1 mt-sm-0">
                                                    <div class="custom-control custom-radio">
                                                        <input type="radio" name="yyyy_chk" id="yyyy_chk_y" class="custom-control-input" value="Y" checked>
                                                        <label class="custom-control-label" for="yyyy_chk_y">양력</label>
                                                    </div>
                                                    <div class="custom-control custom-radio">
                                                        <input type="radio" name="yyyy_chk" id="yyyy_chk_n" class="custom-control-input" value="N">
                                                        <label class="custom-control-label" for="yyyy_chk_n">음력</label>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                </table>
                            </form>
                            <p class="fc-red fs-08 mb-2">* 고객 등록 시 비밀번호는 '휴대폰 뒷자리 + *' 로 초기화됩니다.</p>
                            <div class="text-center w-100">
                                <button type="button" onclick="return addMember();" class="butt fc-white fs-12 fw-sb br-1 bg-blue w-100 p-3">등록</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    {{-- 고객검색모달 --}}
    <div class="modal fade" id="searchMemberModal" tabindex="-1" role="dialog" aria-labelledby="searchMemberModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-body">
                    <div class="card">
                        <div class="card-header">
                            <button type="button" class="fs-20 close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                            <h5 class="mt-1 fs-14 fw-b">고객 검색</h5>
                        </div>
                        <div class="card-body b-none mt-4">
                            <div class="d-flex align-items-center br-2 b-1-gray bg-white shadow-box p-2 pl-4 mb-3">
                                <select name="search_member_type" id="search_member_type" class="sel fs-10" style="min-width: 120px;">
                                    <option value="phone">휴대폰뒷자리</option>
                                    <option value="user_nm">고객명</option>
                                </select>
                                <input type="text" class="flex-1 inp h-40 fs-12 mr-1" id="search_member_keyword" name="search_member_keyword" placeholder="검색어를 입력하세요">
                                <button type="button" class="butt br-2 bg-lightgray p-3" onclick="return SearchMember();"><i class="fa fa-search fc-black fs-10" aria-hidden="true"></i></button>
                            </div>
                            <p class="d-flex mb-2">* <span class="d-block ml-2 mr-1" style="width: 50px;height: 15px;background-color:#c9f9f9;"></span> : 본 매장 고객</p>
                            <div class="d-flex">
                                <div class="table-responsive">
                                    <div id="div-gd-member" class="ag-theme-balham" style="font-size: 18px;"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    {{-- 신용카드모달 --}}
    <div class="modal fade" id="payModal" tabindex="-1" role="dialog" aria-labelledby="payModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document" style="max-width: 500px;">
            <div class="modal-content">
                <div class="modal-body">
                    <div class="card">
                        <div class="card-header">
                            <button type="button" class="fs-20 close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                            <h5 id="paymodal_title" class="mt-1 fs-14 fw-b"></h5>
                            <input type="hidden" name="paymodal_paytype">
                        </div>
                        <div class="card-body b-none mt-4">
                            <div class="d-flex flex-column align-items-center">
                                <div class="d-flex justify-content-between align-items-center fs-15 fw-b w-100 mb-3">
                                    <p>남은 금액</p>
                                    <p class="butt curson-pointer" onclick="return setDueAmt();"><strong id="due_amt" class="fc-red fs-20 fw-b mr-1">0</strong>원</p>
                                </div>
                                <div class="d-flex justify-content-between align-items-center fs-12 fw-sb w-100">
                                    <p>총 주문금액</p>
                                    <p><strong id="total_order_amt2" class="fw-b mr-1">0</strong>원</p>
                                </div>
                                <div id="payment_calculator" class="calculator-grid payment fs-20 mt-4">
                                    <input type="text" id="pay_press_amt" class="inp fc-black fs-20 fw-b text-right pr-3" style="grid-area:a;border:2px solid #bbb;" value="0">
                                    <button type="button" class="butt bg-white" value="1" style="grid-area:b;">1</button>
                                    <button type="button" class="butt bg-white" value="2" style="grid-area:c;">2</button>
                                    <button type="button" class="butt bg-white" value="3" style="grid-area:d;">3</button>
                                    <button type="button" class="butt bg-white" value="4" style="grid-area:e;">4</button>
                                    <button type="button" class="butt bg-white" value="5" style="grid-area:f;">5</button>
                                    <button type="button" class="butt bg-white" value="6" style="grid-area:g;">6</button>
                                    <button type="button" class="butt bg-white" value="7" style="grid-area:h;">7</button>
                                    <button type="button" class="butt bg-white" value="8" style="grid-area:i;">8</button>
                                    <button type="button" class="butt bg-white" value="9" style="grid-area:j;">9</button>
                                    <button type="button" class="butt bg-white" value="0" style="grid-area:k;">0</button>
                                    <button type="button" class="butt bg-white" value="00" style="grid-area:l;">00</button>
                                    <button type="button" class="butt fs-14 bg-white" value="remove" style="grid-area:m;"><i class="fa fa-arrow-left" aria-hidden="true"></i></button>
                                    <button type="button" class="butt fs-14 bg-lightgray" value="removeAll" style="grid-area:n;">clear</button>
                                    <button type="button" class="butt fs-18 fc-white bg-blue" value="active" style="grid-area:o;">적용</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    {{-- 매장환불 모달 --}}
    <div id="StoreClaimModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="StoreClaimModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title mt-0" id="myModalLabel">매장환불</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body show_layout" style="background:#f5f5f5;">
                    <div class="card_wrap search_cum_form write">
                        <div class="card shadow">
                            <div class="card-body m-0">
                                <form name="store_refund">
                                    <input type="hidden" name="ord_no" value="">
                                    <input type="hidden" name="ord_opt_no" value="">
                                    <div class="card-body">
                                        <div class="row_wrap mb-2">
                                            <div class="row">
                                                <div class="col-lg-12 inner-td">
                                                    <div class="form-group h-100">
                                                        <label style="min-width:80px;">환불일자</label>
                                                        <div class="d-flex align-items-center h-100">
                                                            <p class="fs-09">{{ @$today }}</p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-lg-12 inner-td">
                                                    <div class="form-group">
                                                        <label class="required" style="min-width:80px;">환불사유</label>
                                                        <div class="flex_box">
                                                            <select name="store_clm_reason" id="store_clm_reason" class="form-control form-control-sm">
                                                                <option value="">선택</option>
                                                                @foreach(@$clm_reasons as $clm_reason)
                                                                <option value="{{$clm_reason->code_id}}" @if (@$claim_info->clm_reason == $clm_reason->code_id) selected @endif
                                                                    >
                                                                    {{$clm_reason->code_val}}
                                                                </option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-lg-12 inner-td">
                                                    <div class="form-group h-100">
                                                        <label style="min-width:80px;">환불수량</label>
                                                        <div class="d-flex align-items-center h-100">
                                                            <p class="fs-09"><span style="color: red;font-weight: 700;" id="store_clm_qty"></span>개 <span class="fs-08 ml-3">( 매장환불 시 해당상품의 모든수량이 환불처리됩니다. )</span></p>
                                                        </div>
                                                        {{-- <div class="flex_box">
                                                            <select name="store_clm_qty" id="store_clm_qty" class="form-control form-control-sm">
                                                                @for($i = 1; $i <= $order_opt->qty; $i++ )
                                                                    <option value="{{$i}}" @if($order_opt->clm_qty == $i) selected @endif>
                                                                        {{$i}}
                                                                    </option>
                                                                    @endfor
                                                            </select>
                                                        </div> --}}
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-lg-6 inner-td">
                                                    <div class="form-group h-100">
                                                        <label style="min-width:80px;">환불금액</label>
                                                        <div class="d-flex align-items-center h-100">
                                                            <p class="fs-09 text-right w-100"><span id="store_refund_amt"></span>원</p>
                                                        </div>
                                                        {{-- <div class="flex_box">
                                                            <input type="text" name="store_refund_amt" id="store_refund_amt" value="{{ @number_format(@$order_opt->recv_amt / @$order_opt->qty) }}" readonly="readonly" class="form-control form-control-sm text-right">
                                                        </div> --}}
                                                    </div>
                                                </div>
                                                <div class="col-lg-6 inner-td">
                                                    <div class="form-group h-100">
                                                        <label style="min-width:80px;">환불포인트</label>
                                                        <div class="d-flex align-items-center h-100">
                                                            <p class="fs-09 text-right w-100"><span id="store_refund_point_amt"></span>원</p>
                                                        </div>
                                                        {{-- <div class="flex_box">
                                                            <input type="text" name="store_refund_point_amt" id="store_refund_point_amt" value="{{ @number_format(@$order_opt->point_amt / @$order_opt->qty) }}" readonly="readonly" class="form-control form-control-sm text-right">
                                                        </div> --}}
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-lg-6 inner-td">
                                                    <div class="form-group">
                                                        <label style="min-width:80px;">은행</label>
                                                        <div class="flex_box">
                                                            <input type="text" name="store_refund_bank" id="store_refund_bank" value="" class="form-control form-control-sm">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-lg-6 inner-td">
                                                    <div class="form-group">
                                                        <label style="min-width:80px;">예금주</label>
                                                        <div class="flex_box">
                                                            <input type="text" name="store_refund_nm" id="store_refund_nm" value="" class="form-control form-control-sm">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-lg-12 inner-td">
                                                    <div class="form-group">
                                                        <label style="min-width:80px;">환불계좌</label>
                                                        <div class="flex_box">
                                                            <input type="text" name="store_refund_account" id="store_refund_account" value="" class="form-control form-control-sm">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-lg-12 inner-td">
                                                    <div class="form-group">
                                                        <label style="min-width:80px;">메모</label>
                                                        <div class="flex_box">
                                                            <input type="text" name="store_refund_memo" id="store_refund_memo" value="" class="form-control form-control-sm">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="text-center mt-3">
                                            <button class="btn-sm btn btn-primary store-refund-save-btn fs-09 mr-1">환불</button>
                                            <button class="btn-sm btn btn-secondary fs-09" data-dismiss="modal" aria-label="Close">취소</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    {{-- 환불 시, 주문번호 검색모달 --}}
    <div class="modal fade" id="searchOrdNoModal" tabindex="-1" role="dialog" aria-labelledby="searchOrdNoModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-body">
                    <div class="card">
                        <div class="card-header">
                            <button type="button" class="fs-20 close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                            <h5 class="mt-1 fs-14 fw-b">판매내역 검색</h5>
                        </div>
                        <div class="card-body b-none mt-4">
                            <div class="d-flex align-items-center fs-12 mb-4">
                                <input type="text" id="search_ord_no" class="inp fs-14 fw-sb pl-2 w-100" style="height: 50px;border: solid #ED2939;border-width: 2px 0 2px 2px;" placeholder="주문번호로 검색">
                                <button type="button" id="search_ord_no_btn" class="butt bg-red" style="width: 60px;height: 50px;"><i class="fa fa-search fc-white fs-14" aria-hidden="true"></i></button>
                            </div>
                            <div id="search_ord_no_result"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    {{-- 쿠폰등록 모달 --}}
    <div class="modal fade" id="addCouponModal" tabindex="-1" role="dialog" aria-labelledby="addCouponModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document" style="max-width: 600px;">
            <div class="modal-content">
                <div class="modal-body">
                    <div class="card">
                        <div class="card-header">
                            <button type="button" class="fs-20 close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                            <h5 class="mt-1 fs-14 fw-b">쿠폰 등록</h5>
                        </div>
                        <div class="card-body b-none mt-4">
                            <form name="add_coupon">
                                <table class="table incont table-bordered mt-2" width="100%" cellspacing="0">
                                    <tr>
                                        <th>휴대폰뒷자리 검색</th>
                                        <td colspan="3">
                                            <div class="flax_box inline_btn_box" style="padding-right:75px;">
                                                <input type="text" name="cp_user_phone" id="cp_user_phone" class="form-control form-control-sm">
                                                <a href="javascript:void(0);" onclick="return searchUserInCouponModal();" class="butt d-flex justify-content-center align-items-center fc-white fs-08 fw-sb br-05 bg-gray" style="width:70px;"><i class="fas fa-search fa-sm mr-1"></i> 검색</a>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th class="required">고객 선택</th>
                                        <td>
                                            <select name="cp_user_id" id="cp_user_id" class="form-control form-control-sm mr-1">
                                                <option value="">---</option>
                                            </select>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th class="required">쿠폰 시리얼넘버</th>
                                        <td colspan="3">
                                            <div class="flax_box inline_btn_box p-0">
                                                <input type="text" name="cp_serial_num" id="cp_serial_num" class="form-control form-control-sm">
                                            </div>
                                        </td>
                                    </tr>
                                </table>
                            </form>
                            <div class="text-center w-100">
                                <button type="button" onclick="return addCoupon();" class="butt fc-white fs-12 fw-sb br-1 bg-mint w-100 p-3">등록</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript" charset="utf-8">
    let AlignCenter = {"text-align": "center"};
    let LineHeight50 = {"line-height": "50px"};

    // 주문등록화면 - 선택상품리스트
	const pApp = new App('', {gridId: "#div-gd"});
	let gx;

    const columns = [
        // {headerName: "No", pinned: "left", valueGetter: "node.id", cellRenderer: "loadingRenderer", width: 40, cellStyle: {...AlignCenter, ...LineHeight50}},
        {field: "prd_cd", hide: true},
        // {field: "img", headerName: "이미지", width: 50, cellStyle: {...AlignCenter, ...LineHeight50},
        //     cellRenderer: (params) => {
        //         return `
        //             <div class="d-flex justify-content-center align-items-center" style="width:50px;height:50px;overflow:hidden;">
        //                 <img src="${params.value}" alt="${params.data.goods_nm}" class="w-100">
        //             </div>
        //         `;
        //     }
        // },
        {field: "goods_nm", headerName: "상품명", width: "auto", cellStyle: LineHeight50, wrapText: true, autoHeight: true,
            // cellRenderer: (params) => `<a href="javascript:void(0);" onclick="setProductDetail('${params.data.prd_cd}');">${params.value}</a>`,
        },
        // {field: "prd_cd_sm", headerName: "코드일련", width: 130, cellStyle: {...AlignCenter, ...LineHeight50}},
        {field: "color", headerName: "컬러", width: 180, cellStyle: {...LineHeight50}, wrapText: true, autoHeight: true},
        {field: "size", headerName: "사이즈", width: 150, cellStyle: {...LineHeight50}, wrapText: true, autoHeight: true},
        {field: "qty", headerName: "수량", width: 60, type: "currencyType", cellStyle: LineHeight50},
        {field: "price", headerName: "단가", width: 80, type: "currencyType", cellStyle: LineHeight50},
        {field: "total", headerName: "금액", width: 100, type: "currencyType", cellStyle: {...LineHeight50, "font-size": "18px", "font-weight": "700"},
            cellRenderer: (params) => `
                <div class="d-flex flex-column">
                    ${(params.data.ori_price * params.data.qty) !== params.value ? `<del class="fs-08 font-weight-normal" style="line-height:23px;">${Comma(params.data.ori_price * params.data.qty)}</del>` : ''}
                    <p style="${(params.data.ori_price * params.data.qty) !== params.value ? 'line-height:20px;' : 'line-height:50px;' }">${Comma(params.value)}</p>
                </div>`,
        },
        {headerName: "삭제", width: 80, cellStyle: {...AlignCenter, ...LineHeight50},
            cellRenderer: (params) => `<a href="javascript:void(0);" onclick="return removeProduct('${params.data.prd_cd}')"><i class="fa fa-trash fc-red fs-12" aria-hidden="true"></i></a>`,
        }
    ];

    // 주문등록화면 - 상품조회리스트
    const pApp2 = new App('', {gridId: "#div-gd-product"});
    let gx2;

    const product_columns = [
        {field: "prd_cd" , headerName: "상품코드", width: 180, cellStyle: {...AlignCenter, ...LineHeight50}},
        {field: "prd_cd_sm", headerName: "코드일련", width: 130, cellStyle: {...AlignCenter, ...LineHeight50}},
        {field: "color", headerName: "컬러", width: 180, cellStyle: {...LineHeight50}},
        {field: "size", headerName: "사이즈", width: 150, cellStyle: {...LineHeight50}},
        {field: "style_no", headerName: "스타일넘버", width: 100, cellStyle: {...AlignCenter, ...LineHeight50}},
        {field: "goods_nm",	headerName: "상품명", width: "auto", cellStyle: LineHeight50,
            cellRenderer: (params) => `<a href="javascript:void(0);" onclick="return addProduct('${params.data.prd_cd}')">${params.value}</a>`,
        },
        {field: "wqty", headerName: "매장수량", type: "currencyType", width: 100, cellStyle: LineHeight50},
        {field: "goods_sh", headerName: "TAG가", type: "currencyType", width: 100, cellStyle: LineHeight50},
        {field: "price", headerName: "판매가", type: "currencyType", width: 100, cellStyle: LineHeight50},
    ];

    // 주문등록화면 - 고객조회리스트
    const pApp3 = new App('', {gridId: "#div-gd-member"});
    let gx3;

    const member_columns = [
        {field: "user_id", headerName: "아이디", width: 120, cellStyle: (params) => ({...AlignCenter, ...LineHeight50, "background-color": params.data.store_member == 'Y' ? '#c9f9f9 !important' : 'none'}),
            cellRenderer: (params) => `<a href="javascript:void(0);" onclick="return setMember('${params.value}')">${params.value}</a>`,
        },
        {field: "user_nm", headerName: "이름", width: 120, cellStyle: (params) => ({...AlignCenter, ...LineHeight50, "background-color": params.data.store_member == 'Y' ? '#c9f9f9 !important' : 'none'})},
        {field: "mobile", headerName: "연락처", width: 160, cellStyle: (params) => ({...AlignCenter, ...LineHeight50, "background-color": params.data.store_member == 'Y' ? '#c9f9f9 !important' : 'none'})},
        {width: "auto", cellStyle: (params) => ({"background-color": params.data.store_member == 'Y' ? '#c9f9f9 !important' : 'none'})}
    ];

    // 당일판매내역 - 판매내역리스트
    const pApp4 = new App('', {gridId: "#div-gd-order"});
    let gx4;

    const order_columns = [
        {field: "ord_type", headerName: "주문타입", hide: true},
        {field: "ord_date", headerName: "주문일자", width: 180, cellStyle: {...AlignCenter, ...LineHeight50}},
        {field: "ord_no", headerName: "주문번호", width: 230, cellStyle: {...AlignCenter, ...LineHeight50},
            cellRenderer: (params) => `${params.data.ord_type == '4' ? '<span class="text-danger">[예약]</span> ' : ''}${params.value}`,
        },
        {field: "user_id", headerName: "고객명", width: 150, cellStyle: LineHeight50,
            cellRenderer: (params) => (!!params.data.user_nm ? params.data.user_nm : '비회원') + (params.data.user_id ? ' (' + params.data.user_id + ')' : ''),
        },
        {field: "mobile", headerName: "고객연락처", width: 150, cellStyle: {...AlignCenter, ...LineHeight50}},
        {field: "pay_type_nm", headerName: "결제수단", width: 100, cellStyle: {...AlignCenter, ...LineHeight50},
            cellRenderer: (params) => params.value.replaceAll("무통장", "현금"),
        },
        {field: "recv_amt", headerName: "결제금액", width: 150, type: "currencyType", cellStyle: {"font-size": "1.1rem", "font-weight": "700", ...LineHeight50},
            cellRenderer: (params) => Comma(params.value) + "원",
        },
        {width: "auto"}
    ];

    // 메인화면 - 대기내역
    const pApp5 = new App('', {gridId: "#div-gd-waiting"});
    let gx5;

    const waiting_columns = [
        {field: "ord_date", headerName: "대기일자", width: 180, cellStyle: {...AlignCenter, ...LineHeight50}},
        {field: "user_nm", headerName: "고객명", width: 150, cellStyle: {...AlignCenter, ...LineHeight50}},
        {field: "qty", headerName: "수량", width: 80, type: "currencyType", cellStyle: LineHeight50},
        {field: "recv_amt", headerName: "결제금액", width: "auto", type: "currencyType", cellStyle: LineHeight50,
            cellRenderer: (params) => Comma(params.value) + "원",
        },
    ];

    const sale_types = <?= json_encode(@$sale_types) ?>; // 판매유형
    const pr_codes = <?= json_encode(@$pr_codes) ?>; // 행사명

	$(document).ready(function() {
        window.onkeyup = null;
        
		pApp.ResizeGrid(275, 470);
		let gridDiv = document.querySelector(pApp.options.gridId);
		gx = new HDGrid(gridDiv, columns, {
            rowSelection: 'single',
            suppressRowClickSelection: false,
            onSelectionChanged: function(e) {
                let goods = e.api.getSelectedRows();
                if(goods.length > 0) {
                    setProductDetail(goods[0].prd_cd);
                } else {
                    setProductDetail();
                }
                // updateOrderValue();
            }
        });

		pApp2.ResizeGrid(275, 400);
		let gridDiv2 = document.querySelector(pApp2.options.gridId);
		gx2 = new HDGrid(gridDiv2, product_columns);

		pApp3.ResizeGrid(275, 400);
		let gridDiv3 = document.querySelector(pApp3.options.gridId);
		gx3 = new HDGrid(gridDiv3, member_columns);

		pApp4.ResizeGrid(125);
		let gridDiv4 = document.querySelector(pApp4.options.gridId);
		gx4 = new HDGrid(gridDiv4, order_columns, {
            rowSelection: 'single',
            suppressRowClickSelection: false,
            onSelectionChanged: function(e) {
                let order = e.api.getSelectedRows();
                if(order.length > 0) {
                    setOrderDetail(order[0].ord_no);
                }
            }
        });

        pApp5.ResizeGrid(275, 300);
        let gridDiv5 = document.querySelector(pApp5.options.gridId);
        gx5 = new HDGrid(gridDiv5, waiting_columns, {
            rowSelection: 'single',
            suppressRowClickSelection: false,
        });

        searchWaiting();
        setNewOrdNo(true);
        getOrderAnalysisData();

        getClock();
        setInterval(getClock, 1000);

        // ELEMENT EVENT
        $("#search_prd_keyword_out").on("keypress", function (e) {
            if(e.keyCode === 13) {
                $("#search_prd_type").val($("#search_prd_type_out").val());
                $("#search_prd_keyword").val(e.target.value);
                $("#searchProductModal").modal("show");
                Search();
            }
        });
        $("#search_btn_out").on("click", function (e) {
            $("#search_prd_type").val($("#search_prd_type_out").val());
            $("#search_prd_keyword").val($("#search_prd_keyword_out").val());
            $("#searchProductModal").modal("show");
            Search();
        });
        $("#search_prd_keyword").on("keypress", function (e) {
            if(e.keyCode === 13) Search();
        });
        $('#searchProductModal').on('shown.bs.modal', function () {
            $('#search_prd_keyword').trigger('focus');
        });
        $("#product_calculator").on({
            click: function({target}) {
                let tg = target;
                if(target.parentNode.nodeName == "BUTTON") tg = target.parentNode;
                if(tg.nodeName == "BUTTON") {
                    let str = $("#product_press_amt").val().replaceAll(",", "");
                    switch (tg.value) {
                        case 'remove':
                            str = str.slice(0, str.length - 1);
                            break;
                        case 'removeAll':
                            str = '';
                            break;
                        case 'qty':
                            updateOrderValue('cur_qty', str * 1);
                            str = '';
                            break;
                        case 'price':
                            updateOrderValue('cur_price', str * 1);
                            str = '';
                            break;
                        default:
                            str += tg.value;
                            break;
                    }
                    $("#product_press_amt").val(isNaN(str * 1) ? 0 : Comma(str * 1));
                }
            },
            keyup: function(e) {
                if((e.keyCode >= 48 && e.keyCode <= 57) || e.keyCode == 8 || (e.keyCode >= 37 && e.keyCode <= 40)) {
                    let num = unComma(e.target.value);
                    e.target.value = Comma(isNaN(num) ? 0 : num);
                }
            }
        });
        $("#cur_qty").on("keypress", function(e) {
            if(e.keyCode === 13) {
                const val = e.target.value;
                if (!isNaN(val * 1)) updateOrderValue('cur_qty', val * 1);
                else e.target.value = '';
            }
        });

        $("#search_member_keyword").on("keypress", function (e) {
            if(e.keyCode === 13) SearchMember();
        });
        $('#searchMemberModal').on('shown.bs.modal', function () {
            $('#search_member_keyword').trigger('focus');
        });

        $('#payModal').on('show.bs.modal', function(e) {
            let title = $(e.relatedTarget).data('title');
            let paytype = $(e.relatedTarget).data('pay-type');
            $(e.currentTarget).find('#paymodal_title').text(title);
            $(e.currentTarget).find('[name=paymodal_paytype]').val(paytype);
        });
        $('#payModal').on('hide.bs.modal', function(e) {
            $("#pay_press_amt").val(0);
        });
        $("#payment_calculator").on({
            click: function(e) {
                let tg = e.target;
                if(e.target.parentNode.nodeName == "BUTTON") tg = e.target.parentNode;
                if(tg.nodeName == "BUTTON") {
                    let str = $("#pay_press_amt").val().replaceAll(",", "");
                    switch (tg.value) {
                        case 'remove':
                            str = str.slice(0, str.length - 1);
                            break;
                        case 'removeAll':
                            str = '';
                            break;
                        case 'active':
                            let paytype = $('[name=paymodal_paytype]').val();
                            updateOrderValue(paytype, str * 1);
                            $('#payModal').modal('hide');
                            str = '';
                            break;
                        default:
                            str += tg.value;
                            break;
                    }
                    $("#pay_press_amt").val(isNaN(str * 1) ? 0 : Comma(str * 1));
                }
            },
            keyup: function(e) {
                if((e.keyCode >= 48 && e.keyCode <= 57) || e.keyCode == 8 || (e.keyCode >= 37 && e.keyCode <= 40)) {
                    let num = unComma(e.target.value);
                    e.target.value = Comma(isNaN(num) ? 0 : num);
                } else if(e.keyCode == 13) {
                    updateOrderValue('card_amt', str * 1);
                    $('#payModal').modal('hide');
                }
            }
        });

        $('#searchWaitingModal').on('shown.bs.modal', async function () {
            let list = gx5.getRows();
            if(list.length > 0) {
                await gx5.gridOptions.api.applyTransaction({
                    remove: [list[0]]
                });
                await gx5.gridOptions.api.applyTransaction({
                    add: [list[0]],
                    addIndex: 0,
                });
            }
        });

        $('#searchOrdNoModal').on('shown.bs.modal', function () {
            $('#search_ord_no').trigger('focus');
        });
        $(".store-refund-save-btn").on("click", function(e) {
            e.preventDefault();
            refundStoreOrder();
        });

        $("#search_ord_no").on("keyup", function(e) {
            if(e.keyCode === 13) {
                searchOrderByOrdNo(e.target.value);
            }
        });
        $("#search_ord_no_btn").on("click", function(e) {
            e.preventDefault();
            searchOrderByOrdNo($("#search_ord_no").val());
        });

        $('#addMemberModal').on('hide.bs.modal', function() {
            initAddMemberModal();
        });

        /** 쿠폰등록모달 관련 */
        $('#addCouponModal').on('shown.bs.modal', function () {
            $('#cp_user_phone').trigger('focus');
        });
        $('#addCouponModal').on('hide.bs.modal', function() {
            $("#cp_user_phone").val('');
            $("#cp_user_id").html("<option value=''>---</option>");
            $("#cp_serial_num").val('');
        });
        $("#cp_user_phone").on("keypress", function(e) {
            if (e.keyCode === 13) searchUserInCouponModal();
        });
	});
</script>

@include('shop_with.pos.pos_js')

@stop

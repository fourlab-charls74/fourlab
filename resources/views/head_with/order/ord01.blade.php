@extends('head_with.layouts.layout')
@section('title','주문내역')
@section('content')

<div class="page_tit">
    <h3 class="d-inline-flex">주문내역</h3>
    <div class="d-inline-flex location">
        <span class="home"></span>
        <span>/ 주문&amp;배송</span>
        <span>/ 주문내역</span>
    </div>
</div>
<div id="search-area" class="search_cum_form">
    <form method="get" name="search">
		<input type="hidden" name="o" id="o" value="{{ $o }}">
		<input type="hidden" name="ismt" id="ismt" value="{{ $ismt }}">
        <div class="card mb-3">
            <div class="d-flex card-header justify-content-between">
                <h4>검색</h4>
                <div class="flax_box">
                    <div class="d-none d-lg-flex" style="line-height:30px;">
                        새로고침 :
                        <div class="ml-1">
                            <select id="auto-search" class="form-control form-control-sm" style="width:60px; text-align:center">
                                <option value="1">자동</option>
                                <option value="0" selected>수동</option>
                            </select>
                        </div>
                        <input type="text" class="form-control form-control-sm mx-1" id="load-time" maxLength="3" value="60" style="width:60px; text-align:center">
                        초
                    </div>
                    <a href="#" id="search_sbtn" onclick="Search();" class="btn btn-sm btn-primary shadow-sm pl-2 mx-1"><i class="fas fa-search fa-sm text-white-50"></i> 조회</a>
                    <a href="#" onclick="formReset()" class="d-none search-area-ext d-sm-inline-block btn btn-sm btn-outline-primary mr-1 shadow-sm">검색조건 초기화</a>
                    <div class="btn-group dropleftbtm mr-1">
                        <button type="button" class="btn btn-primary waves-light waves-effect dropdown-toggle btn-sm pr-1" data-toggle="dropdown" aria-expanded="false">
                            <i class="fa fa-folder"></i> <i class="bx bx-chevron-down fs-12"></i>
                        </button>
                        <div class="dropdown-menu" style="">
                            <a class="dropdown-item" onclick="openClaimPopup()" href="#" >업체클레임 조회</a>
                            <a class="dropdown-item" onclick="openSms()" href="#" >SMS 발송</a>
                            <a class="dropdown-item" onclick="gridDownload()" href="#" >자료받기</a>
                        </div>
                        <input type="hidden" name="data" id="data" value=""/>
                    </div>
                    <div id="search-btn-collapse" class="btn-group mb-0 mb-sm-0"></div>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-lg-4 inner-td">
                        <div class="form-group">
                            <label for="user_yn">주문일자</label>
                            <div class="date-switch-wrap form-inline">
                                <div class="docs-datepicker form-inline-inner input_box">
                                    <div class="input-group">
                                        <input type="text" class="form-control form-control-sm docs-date" name="sdate" value="{{ $sdate }}" autocomplete="off" disable>
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
                                        <input type="text" class="form-control form-control-sm docs-date" name="edate" value="{{ $edate }}" autocomplete="off">
                                        <div class="input-group-append">
                                            <button type="button" class="btn btn-outline-secondary docs-datepicker-trigger p-0 pl-2 pr-2">
                                            <i class="fa fa-calendar" aria-hidden="true"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="docs-datepicker-container"></div>
                                </div>
                                <div class="custom-control custom-switch date-switch-pos"  data-toggle="tooltip" data-placement="top" data-original-title="주문일자 사용">
                                    <input type="checkbox" class="custom-control-input" name="s_nud" id="s_nud" checked="" value="N" onClick="ManualNotUseData();">
                                    <label class="" for="s_nud" data-on-label="ON" data-off-label="OFF"></label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 inner-td">
                        <div class="form-group">
                            <label for="type">주문번호</label>
                            <div class="flax_box">
                                <input type='text' class="form-control form-control-sm search-all search-enter" name='ord_no' value=''>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-4 inner-td">
                        <div class="form-group">
                            <label for="type">주문자/아이디</label>
                            <div class="form-inline">
                                <div class="form-inline-inner input_box">
                                    <div class="form-group">
                                        <input type="text" class="form-control form-control-sm search-all search-enter" name="user_nm" value="">
                                    </div>
                                </div>
                                <span class="text_line">/</span>
                                <div class="form-inline-inner input_box">
                                    <div class="form-group">
                                        <input type="text" class="form-control form-control-sm search-all search-enter" name="user_id" value="">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-4 inner-td">
                        <div class="form-group">
                            <label for="name">스타일넘버/온라인코드</label>
                            <div class="form-inline">
                                <div class="form-inline-inner input_box">
                                    <input type='text' class="form-control form-control-sm ac-style-no search-enter" name='style_no' id="style_no" value="">
                                </div>
                                <span class="text_line">/</span>
                                <div class="form-inline-inner input-box" style="width:47%">
                                    <div class="form-inline-inner inline_btn_box">
                                        <input type='text' class="form-control form-control-sm w-100" name='goods_no' id='goods_no' value=''>
                                        <a href="#" class="btn btn-sm btn-outline-primary sch-goods_nos"><i class="bx bx-dots-horizontal-rounded fs-16"></i></a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 inner-td">
                        <div class="form-group">
                            <label for="name">수령자/입금자</label>
                            <div class="form-inline">
                                <div class="form-inline-inner input_box">
                                    <div class="form-group">
                                        <input type='text' class="form-control form-control-sm search-all search-enter" name='r_nm' value=''>
                                    </div>
                                </div>
                                <span class="text_line">/</span>
                                <div class="form-inline-inner input_box">
                                    <div class="form-group">
                                        <input type='text' class="form-control form-control-sm search-all search-enter" name='bank_inpnm' value=''>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-4 inner-td">
                        <div class="form-group">
                            <label for="name">검색항목</label>
                            <div class="form-inline">
                                <div class="form-inline-inner input_box">
                                    <div class="form-group">
                                        <select name="cols" id="cols" class="form-control form-control-sm ">
                                            <option value="">선택하세요.</option>
                                            <option value="b.mobile">주문자핸드폰번호</option>
                                            <option value="b.phone">주문자전화번호</option>
                                            <option value="b.r_mobile">수령자핸드폰번호</option>
                                            <option value="b.r_phone">수령자전화번호</option>
                                            <option value="b.email">주문자이메일</option>
                                            <option value="b.r_addr1">주소(동명)</option>
                                            <option value="b.ord_amt">주문총금액</option>
                                            <option value="a.recv_amt">단일주문금액</option>
                                            <option value="a.dlv_end_date">배송일자</option>
                                            <option value="b.dlv_msg">배송메세지</option>
                                            <option value="a.dlv_cd">택배사</option>
                                            <option value="memo">처리상태/메모</option>
                                        </select>
                                    </div>
                                </div>
                                <span class="text_line">/</span>
                                <div class="form-inline-inner input_box">
                                    <div class="form-group">
                                        <input type='text' class="form-control form-control-sm search-all search-enter" name='key' value=''>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
                <!-- end row -->
                <div class="row">
                <!-- <div class="row d-none search-area-ext"> -->
                    <div class="col-lg-4 inner-td">
                        <div class="form-group">
                            <label for="name">주문/입금상태</label>
                            <div class="form-inline">
                                <div class="form-inline-inner input_box">
                                    <div class="form-group">
                                        <select name='ord_state' class="form-control form-control-sm">
                                            <option value=''>전체</option>
                                            @foreach ($ord_states as $ord_state)
                                                <option value='{{ $ord_state->code_id }}'>
                                                    {{ $ord_state->code_val }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <span class="text_line">/</span>
                                <div class="form-inline-inner input_box">
                                    <div class="form-group">
                                        <select name='pay_stat' class="form-control form-control-sm">
                                            <option value=''>전체</option>
                                            <option value="0">예정</option>
                                            <option value="1">입금</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- end col -->
                    <div class="col-lg-4 inner-td">
                        <div class="form-group">
                            <label for="name">클레임 상태</label>
                            <div class="flax_box">
                                <select name='clm_state' class="form-control form-control-sm">
                                    <option value=''>전체</option>
                                    @foreach ($clm_states as $clm_state)
                                        <option value='{{ $clm_state->code_id }}'>
                                            {{ $clm_state->code_val }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <!-- end col -->
                    <div class="col-lg-4 inner-td">
                        <div class="form-group">
                            <label for="name">결제방법/현금영수증</label>
                            <div class="form-inline">
                                <div class="form-inline-inner" style="width:74%;">
                                    <div class="form-group flax_box">
                                        <div style="width:calc(100% - 177px);">
                                            <select name="stat_pay_type" class="form-control form-control-sm mr-2" style="width:100%;">
                                                <option value="">전체</option>
                                                @foreach ($stat_pay_types as $stat_pay_type)
                                                    <option value='{{ $stat_pay_type->code_id }}'>
                                                        {{ $stat_pay_type->code_val }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div style="height:30px;margin-left:5px;">
                                            <div class="custom-control custom-switch date-switch-pos" data-toggle="tooltip" data-placement="top" data-original-title="복합결제 제외">
                                                <input type="checkbox" class="custom-control-input" id="not_complex" name="not_complex" value="Y">
                                                <label for="not_complex" data-on-label="ON" data-off-label="OFF" style="margin-top:2px;"></label>
                                            </div>
                                        </div>
                                        <div style="height:30px;margin-left:2px;">
                                            <div class="custom-control custom-switch date-switch-pos" data-toggle="tooltip" data-placement="top" data-original-title="결제수수료 주문(개발예정)">
                                                <input type="checkbox" class="custom-control-input" id="pay_fee" value="Y" disabled>
                                                <label for="pay_fee" data-on-label="ON" data-off-label="OFF" style="margin-top:2px;"></label>
                                            </div>
                                        </div>
                                        <div style="height:30px;margin-left:2px;">
                                            <div class="custom-control custom-switch date-switch-pos" data-toggle="tooltip" data-placement="top" data-original-title="간편결제(개발예정)">
                                                <input type="checkbox" class="custom-control-input" id="fintech" value="Y" disabled>
                                                <label for="fintech" data-on-label="ON" data-off-label="OFF" style="margin-top:2px;"></label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <span class="text_line">/</span>
                                <div class="form-inline-inner input_box" style="width:20%;">
                                    <div class="form-group">
                                        <select name="receipt" class="form-control form-control-sm">
                                            <option value="">전체</option>
                                            <option value="R">신청</option>
                                            <option value="Y">발행</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- end col -->
                </div>
                <!-- end row -->
                <div class="row d-none search-area-ext">
                    <div class="col-lg-4 inner-td">
                        <div class="form-group">
                            <label for="name">주문구분</label>
                            <div class="flax_box">
                                <select name='ord_type' class="form-control form-control-sm">
                                    <option value=''>전체</option>
                                    @foreach ($ord_types as $ord_type)
                                        <option value='{{ $ord_type->code_id }}'>{{ $ord_type->code_val }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <!-- end col -->
                    <div class="col-lg-4 inner-td">
                        <div class="form-group">
                            <label for="name">출고구분/배송방식</label>
                            <div class="form-inline">
                                <div class="form-inline-inner input_box">
                                    <div class="form-group">
                                        <select name='ord_kind' class="form-control form-control-sm">
                                            <option value=''>전체</option>
                                            @foreach ($ord_kinds as $ord_kind)
                                                <option value='{{ $ord_kind->code_id }}'>{{ $ord_kind->code_val }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <span class="text_line">/</span>
                                <div class="form-inline-inner input_box">
                                    <div class="form-group">
                                        <select name='dlv_type' class="form-control form-control-sm">
                                            <option value=''>전체</option>
                                            @foreach ($dlv_types as $dlv_type)
                                                <option value='{{ $dlv_type->code_id }}'>{{ $dlv_type->code_val }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- end col -->
                    <div class="col-lg-4 inner-td">
                        <div class="form-group">
                            <label for="name">송장번호</label>
                            <div class="flax_box">
                                <input type="text" name="dlv_no" id="dlv_no" class="form-control form-control-sm">
                            </div>
                        </div>
                    </div>
                    <!-- end col -->
                </div>
                <!-- end row -->
                <div class="row d-none search-area-ext">
                    <div class="col-lg-4 inner-td">
                        <div class="form-group">
                            <label for="name">업체</label>
                            <div class="form-inline inline_select_box">
                                <div class="form-inline-inner input-box w-25 pr-1">
                                    <select id="com_type" name="com_type" class="form-control form-control-sm w-100">
                                        <option value="">전체</option>
                                        @foreach ($com_types as $com_type)
                                            <option value="{{ $com_type->code_id }}">{{ $com_type->code_val }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-inline-inner input-box w-75">
                                    <div class="form-inline inline_btn_box">
                                        <input type="hidden" id="com_id" name="com_id">
                                        <input type="text" id="com_nm" name="com_nm" class="form-control form-control-sm ac-company" style="width:100%;">
                                        <a href="#" class="btn btn-sm btn-outline-primary sch-company"><i class="bx bx-dots-horizontal-rounded fs-16"></i></a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- end col -->
                    <div class="col-lg-4 inner-td">
                        <div class="form-group">
                            <label for="name">판매처</label>
                            <div class="flax_box">
                                <select name='sale_place' class="form-control form-control-sm">
                                    <option value=''>전체</option>
                                    @foreach ($sale_places as $sale_place)
                                        <option value='{{ $sale_place->com_id }}'>{{ $sale_place->com_nm }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <!-- end col -->
                    <div class="col-lg-4 inner-td">
                        <div class="form-group">
                            <label for="name">판매처 주문번호</label>
                            <div class="flax_box">
                                <input type="text" name="out_ord_no" id="out_ord_no" class="form-control form-control-sm">
                            </div>
                        </div>
                    </div>
                    <!-- end col -->
                </div>
                <!-- end row -->
                <div class="row d-none search-area-ext">
                    <div class="col-lg-4 inner-td">
                        <div class="form-group">
                            <label for="name">품목</label>
                            <div class="flax_box">
                                <select name="item" class="form-control form-control-sm">
                                    <option value="">전체</option>
                                    @foreach ($items as $item)
                                        <option value="{{ $item->cd }}">{{ $item->val }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <!-- end col -->
                    <div class="col-lg-4 inner-td">
                        <div class="form-group">
                            <label for="name">브랜드</label>
                            <div class="form-inline inline_btn_box">
                                <select id="s_brand_cd" name="s_brand_cd" class="form-control form-control-sm select2-brand"></select>
                                <a href="#" class="btn btn-sm btn-outline-primary sch-brand"><i class="bx bx-dots-horizontal-rounded fs-16"></i></a>
                            </div>
                        </div>
                    </div>
                    <!-- end col -->
                    <div class="col-lg-4 inner-td">
                        <div class="form-group">
                            <label for="name">상품명</label>
                            <div class="flax_box">
                                <input type="text" class="form-control form-control-sm ac-goods-nm search-enter" name="goods_nm" value="">
                            </div>
                        </div>
                    </div>
                    <!-- end col -->
                </div>
                <!-- end row -->
                <div class="row d-none search-area-ext">
                    <div class="col-lg-4 inner-td">
                        <div class="form-group">
                            <label for="name">상품구분/부가수수료</label>
                            <div class="form-inline">
                                <div class="form-inline-inner input_box">
                                    <div class="form-group">
                                        <select name='goods_type' class="form-control form-control-sm">
                                            <option value=''>전체</option>
                                            @foreach ($goods_types as $goods_type)
                                                <option value='{{ $goods_type->code_id }}'>{{ $goods_type->code_val }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <span class="text_line">/</span>
                                <div class="form-inline-inner input_box">
                                    <div class="form-group">
                                        <div style="height:30px;margin-left:2px;">
                                            <div class="custom-control custom-switch date-switch-pos" data-toggle="tooltip" data-placement="top" data-original-title="부가수수료 주문(개발예정)">
                                                <input type="checkbox" class="custom-control-input" id="extra_fee" value="Y" disabled>
                                                <label for="extra_fee" data-on-label="ON" data-off-label="OFF" style="margin-top:2px;"></label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- end col -->
                    <div class="col-lg-4 inner-td">
                        <div class="form-group">
                            <label for="name">상단홍보글</label>
                            <div class="flax_box">
                                <input type="text" class="form-control form-control-sm search-enter" name="head_desc" value="">
                            </div>
                        </div>
                    </div>
                    <!-- end col -->
                    <div class="col-lg-4 inner-td">
                        <div class="form-group">
                            <label for="name">자료/정렬순서</label>
                            <div class="form-inline">
                                <div class="form-inline-inner input_box" style="width:24%;">
                                    <select name="limit" class="form-control form-control-sm">
                                        <option value="100">100</option>
                                        <option value="500">500</option>
                                        <option value="1000">1000</option>
                                        <option value="2000">2000</option>
                                        <option value=-1>모두</option>
                                    </select>
                                </div>
                                <span class="text_line">/</span>
                                <div class="form-inline-inner input_box" style="width:45%;">
                                    <select name="ord_field" class="form-control form-control-sm">
                                        <option value="a.ord_date" selected>주문일자</option>
                                        <option value="b.user_nm" >주문자</option>
                                        <option value="b.r_nm" >수령자</option>
                                        <option value="c.goods_nm" >상품명</option>
                                        <option value="c.style_no" >스타일넘버</option>
                                        <option value="a.head_desc" > 상단홍보글</option>
                                    </select>
                                </div>
                                <div class="form-inline-inner input_box sort_toggle_btn" style="width:24%;margin-left:1%;">
                                    <div class="btn-group" role="group">
                                        <label class="btn btn-primary primary" for="sort_desc" data-toggle="tooltip" data-placement="top" title="" data-original-title="내림차순"><i class="bx bx-sort-down"></i></label>
                                        <label class="btn btn-secondary" for="sort_asc" data-toggle="tooltip" data-placement="top" title="" data-original-title="오름차순"><i class="bx bx-sort-up"></i></label>
                                    </div>
                                    <input type="radio" name="ord" id="sort_desc" value="desc" checked="">
                                    <input type="radio" name="ord" id="sort_asc" value="asc">
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- end col -->
                </div>
                <!-- end row -->
            </div>
        </div>
        <div class="resul_btn_wrap mb-3">
            <a href="javascript:;" class="btn btn-sm w-xs btn-primary shadow-sm apply-btn mr-1" onclick="return Search();"><i class="fas fa-search fa-sm text-white-50"></i> 조회</a>

            <a href="#" onclick="openClaimPopup()" class="search-area-ext d-sm-inline-block btn btn-sm btn-outline-primary mr-1 shadow-sm">업체클레임</a>
            <a href="#" onclick="openSms()" class="search-area-ext d-sm-inline-block btn btn-sm btn-outline-primary mr-1 shadow-sm">SMS</a>
            <div class="search_mode_wrap btn-group mr-2 mb-0 mb-sm-0"></div>
        </div>
    </form>
</div>

<!-- DataTales Example -->
<div id="filter-area" class="card shadow-none mb-0 ty2 last-card">
    <div class="card-body">
        <div class="card-title mb-3">
            <div class="filter_wrap">
                <div class="fl_box">
                    <h6 class="m-0 font-weight-bold">총 <span id="gd-total" class="text-primary">0</span> 건</h6>
                </div>
                <div class="fr_box">
                    <div class="flex_box">
                        <div class="box mr-2">
                            <div class="custom-control custom-checkbox form-check-box" style="display:inline-block;">
                                <input type="checkbox"  name="chk_to_class" id="chk_to_class" value="Y" class="custom-control-input">
                                <label class="custom-control-label text-left" for="chk_to_class">이미지출력</label>
                            </div>
                        </div>
                        <a href="#" class="btn-sm btn btn-primary order-memo-btn mr-1">변경내용저장</a>
                        <a href="#" class="btn-sm btn btn-primary cancel-order-btn">주문취소</a>
                        <!--
                        <a href="#" class="btn-sm btn btn-primary @if($o == 'pop') @endif confirm-order-btn">구매확정</a>
                        //-->
                        @if($o == 'pop')
                            <a href="#" class="btn-sm btn btn-primary confirm-choice-btn">주문선택</a>
                            <input type="checkbox" name="isclose" value="Y" checked style='display:none;'>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        <div class="table-responsive">
            <div id="div-gd" style="height:calc(100vh - 50vh); width:100%;" class="ag-theme-balham"></div>
        </div>
    </div>
</div>
<style> /* 상품 이미지 사이즈 강제 픽스 */ .img { height:20px; } </style>
<script>
    const editCellStyle = {
        'background' : '#ffff99',
        'border-right' : '1px solid #e0e7e7'
    };

    const columns = [
        {
            headerName: '',
            headerCheckboxSelection: true,
            checkboxSelection: true,
            width:28,
            pinned: 'left',
        },
        {field: "ord_no", headerName: "주문번호", width: 130, cellStyle: StyleOrdNo, type: 'HeadOrderNoType', pinned: 'left'},
        {field:"ord_opt_no" , headerName:"일련번호", width:58,sortable:"ture", pinned: 'left', type:'HeadOrderNoType'},
        {field:"ord_state_nm" , headerName:"주문상태", width:70,cellStyle:StyleOrdState, pinned: 'left'  },
        {field:"clm_state" , headerName:"클레임상태", width:70,cellStyle:StyleClmState, pinned: 'left'  },
        {field:"pay_stat" , headerName:"입금상태", width:58, cellStyle:{"text-align" : "center"} },
        {field:"goods_type_nm" , headerName:"상품구분", width:58, cellStyle:StyleGoodsType  },
        {field:"style_no" , headerName:"스타일넘버", width:70, cellStyle: {'text-align':'center'} },
        {field: "goods_nm", headerName: "상품명", type: "HeadGoodsNameType", width: 200},
        {field:"img" , headerName:"이미지", type:'GoodsImageType', width: 65, hide: true},
        {field: "opt_val", headerName: "옵션", width: 100},
        {field:"goods_addopt" , headerName:"추가옵션", width:72  },
        {field:"qty" , headerName:"수량", width:46, cellStyle:{"text-align" : "right"}},
        {field: "user_nm", headerName: "주문자(아이디)", width: 100,
            cellRenderer: function(params) {
                let orderer = params.data.user_nm + '(' + params.data.user_id + ')';
                if(params.data.user_nm == '비회원'){
                    return params.data.user_nm;
                } else if (params.data.user_id == '') {
                    return params.data.user_nm;
                } else {
                    return '<a href="#" onclick="return openUserInfo(\'' + orderer + '\');">' + orderer + '</a>';
                }
            }
        },
        {field: "r_nm", headerName: "수령자", width: 60, cellClass: 'hd-grid-code'},
        {field:"price" , headerName:"판매가", width:60, type: 'currencyType'  },
        {field:"sale_amt" , headerName:"쿠폰/할인", width:72, type: 'currencyType'  },
        {field:"gift" , headerName:"사은품", width:60  },
        {field:"dlv_amt" , headerName:"배송비", width:60 , type: 'currencyType'  },
        {field: "pay_fee", headerName: "결제수수료", width: 65, type: 'currencyType'  },
        {field: "pay_type", headerName: "결제방법", width: 72, cellClass: 'hd-grid-code'},
        {field:"fintech" , headerName:"간편결제", width:72   },
        {field:"cash_apply_yn" , headerName:"현금영수증신청", width:72   },
        {field:"cash_yn" , headerName:"현금영수증발행", width:72   },
        {
            field:"ord_type",
            headerName:"주문구분",
            width:72,
            cellClass: 'hd-grid-code'
        },
        {
            field:"ord_kind",
            headerName:"출고구분",
            width:72,
            cellStyle:StyleOrdKind
        },
        {field: "sale_place", headerName: "판매처", width: 80},
        {field: "out_ord_no", headerName: "판매처주문번호", width: 100},
        {field: "com_nm", headerName: "업체", width: 100},
        {field: "baesong_kind", headerName: "배송구분", width: 70, cellClass: 'hd-grid-code'},
        {field:"dlv_type" , headerName:"배송방식", width:72  },
        {field:"dlv_nm" , headerName:"택배업체", width:72  },
        {field: "dlv_no", headerName: "송장번호", width: 100},
        {field:"state" , headerName:"처리현황", editable: true, cellStyle: editCellStyle  },
        {field:"memo" , headerName:"메모", editable: true, cellStyle: editCellStyle  },
        {field:"coupon_nm" , headerName:"쿠폰"  },
        {field: "mobile_yn", headerName: "모바일여부", width: 65, 
            cellRenderer: function(params) {
                if(params.value == 'Y') return "예"
                else if(params.value == 'N') return "아니오"
                else return params.value
            }
        },
        {field:"app_yn" , headerName:"앱여부"  },
        {field:"browser" , headerName:"브라우저"  },
        {field: "ord_date", headerName: "주문일시", type: 'DateTimeType'},
        {field: "pay_date", headerName: "입금일시", type: 'DateTimeType'},
        {field: "dlv_end_date", headerName: "배송일시", type: 'DateTimeType'},
        {field: "last_up_date", headerName: "클레임일시", type: 'DateTimeType'},
		{field:"sms_name", headerName:"SMS_주문자명", hide:true},
		{field:"sms_mobile", headerName:"SMS_주문자휴대폰", hide:true},
        {width: "auto"}
    ];


    const pApp = new App('', { gridId: "#div-gd" });
    const gridDiv = document.querySelector(pApp.options.gridId);
    // gx.gridOptions.getRowNodeId = function(data) {
    //     return data.id;
    // }
	let gx;
	$(document).ready(function() {
		gx = new HDGrid(gridDiv, columns);
		pApp.ResizeGrid(275);
		pApp.BindSearchEnter();
		Search();
        $("#chk_to_class").click(function() {
            gx.gridOptions.columnApi.setColumnVisible("img", $("#chk_to_class").is(":checked"));
        });
	});

    function Search(){
        let data = $('form[name="search"]').serialize();
        gx.Request('/head/order/ord01/search/list', data, 1, ord1Callback);
    }

	function ord1Callback(data){
		//console.log(data.responseText);
	}

    function openClaimPopup(){
        const url='/head/cs/cs21';
        const product=window.open(url,"_blank","toolbar=no,scrollbars=yes,resizable=yes,status=yes,top=500,left=500,width=1200,height=800");
    }

    function openUserInfo(user_nm){

        var userinfo = user_nm;
        var userinfo2 = userinfo.split('(');
        var user_id = userinfo2[1].replace(')', '');
        
        const url='/head/member/mem01/show/edit/'+ user_id;
        const product=window.open(url,"_blank","toolbar=no,scrollbars=yes,resizable=yes,status=yes,top=500,left=500,width=1200,height=800");
    }

    function formReset() {
        document.search.reset();
    }

    function gridDownload() {
        gx.Download("주문내역.csv");
    }

    $('.order-memo-btn').click(function(){
        const rows = gx.getSelectedRows();
        const cnt = rows.length;

        if (rows.length === 0) {
            alert("수정할 주문을 선택해주세요.");
            return;
        }

        rows.forEach(function(row, idx){
            $.ajax({
                async: true,
                type: 'put',
                url: '/head/order/ord01/order-memo',
                data: row,
                success: function (data) {
                    if (cnt == idx + 1)  {
                        alert("수정되었습니다.");
                        Search();
                    }
                },
                error: function(request, status, error) {
                    console.log("error")
                }
            });
        });
    });

    $('.cancel-order-btn').click(function(){
        const data = [];
        const rows = gx.getSelectedRows();

        let isCancel = true;

        if (rows.length === 0) {
            alert("주문을 선택해주세요.");
            return;
        }

        rows.forEach(function(row){
            data.push(row.ord_no + "|" + row.ord_opt_no);
            if (row.ord_state != "1") isCancel = false;
        });

        if (isCancel === false) {
            alert("주문취소는 입금예정인 상태에만 가능합니다.");
            return;
        }

        if (confirm('선택하신 주문을 취소하시겠습니까?') === false) {
            return;
        }

		$.ajax({
            async: true,
            type: 'put',
            url: '/head/order/ord01/cancel-order',
            data: { "datas" : data },
            success: function (data) {
                alert("주문이 취소되었습니다.");
				//alert(data.data);
				//console.log(data.data);
                Search();
            },
            error: function(request, status, error) {
                console.log("error")
            }
        });
    });

	$('.confirm-order-btn').click(function() {
		const data		= [];
		const rows		= gx.getSelectedRows();
		let isCancel	= true;

		if( rows.length === 0 ){
			alert("주문을 선택해주세요.");
			return;
		}

		rows.forEach(function(row){
			data.push(row.ord_opt_no);
			if (!(row.ord_state >= 30 && row.ord_state < 50)) isCancel = false;
		});

		if( isCancel === false ){
			alert("구매확정은 출고완료 상태의 주문만 가능합니다.");
			return;
		}

		if( confirm('선택하신 주문을 구매확정 상태로 바꾸시겠습니까?') === false ){
			return;
		}

		$.ajax({
			async: true,
			type: 'put',
			url: '/head/order/ord01/confirm-orders',
			data: { "ord_opt_nos" : data },
			success: function (data) {
				alert("주문이 구매확정 상태로 변경되었습니다.");
				Search();
			},
			error: function(request, status, error) {
				console.log("error")
			}
		});
	});

    const ONE_SECOND = 1000;

    let autoSearchFnc = null;
    let loadTime = 60;

    function autoSearch() {
        Search();
        autoSearchFnc = setTimeout(autoSearch, loadTime * ONE_SECOND);
    }

    $('#auto-search').change(function() {
        if(autoSearchFnc !== null) clearTimeout(autoSearchFnc);

        if(loadTime === 0) {
            alert("새로고침 초수는 0초 이상으로 설정해주세요.");
            return;
        }

        console.log(this.value);

        if (this.value == 1) {
            if(!confirm('자동검색 하시겠습니까?')) {
                this.value = 0;
                return;
            } else {
                this.value = 1;
                autoSearchFnc = setTimeout(autoSearch, loadTime * ONE_SECOND);
            }
        } else {
            if(!confirm('수동검색 하시겠습니까?')) {
                this.value = 1;
                autoSearchFnc = setTimeout(autoSearch, loadTime * ONE_SECOND);
            }
        }

        // if(this.value) {
        //     if (!confirm('자동검색 하시겠습니까?')) {
        //         this.value = 0;
        //         return;
        //     }
        //     autoSearchFnc = setTimeout(autoSearch, loadTime * ONE_SECOND);
        // } else if (confirm('자동검색을 그만하시겠습니까?') === false) {
        //     this.value = 1;
        // }
    });

    $('#load-time').change(function(e){
        if (isNaN(this.value * 1)) {
            this.value = 0;
            loadTime = 0;
            return;
        }

        if (confirm('검색 시간을 변경하시겠습니까?') === false) {
            this.value = loadTime;
            return;
        }

        loadTime = this.value;

        clearTimeout(autoSearchFnc);
        autoSearchFnc = setTimeout(autoSearch, loadTime * ONE_SECOND);
    });

	$(".confirm-choice-btn").click(function(){
		SetChoiceOrder();
	});

    const openSms = () => {
        const rows = gx.getSelectedRows();
        if (rows.length === 0) {
            openSmsSend();
            return;
        }

		/*
        const ord_nos = [];

        row s.forEach((row) => {
            ord_nos.push(row.ord_no);
        });

        openSmsSend(ord_nos);
		*/

		if( rows[0].sms_mobile != undefined )
		{
			phone	= rows[0].sms_mobile;
			name	= rows[0].sms_name;

			openSmsSend(phone, name);
		}
    }




	/*##############################################################
	# 2020.12.07 추가
	##############################################################*/
	function SetChoiceOrder() {

		var ff = document.f1;
		var cnt = 0;
		var ord_no = new Array();
		var ord_opt_no = new Array();
		var lrow = 0;

		const data = [];
		const rows = gx.getSelectedRows();
        let isCancel = true;

        if (rows.length === 0) {
            alert('선택한 주문이 없습니다.');
			return;
        }

        rows.forEach(function(row){
			var ord_info_arr = {'ord_no': row.ord_no, 'ord_opt_no': row.ord_opt_no};
            data.push(ord_info_arr);
			ord_no.push(row.ord_no);
			ord_opt_no.push(row.ord_opt_no);
            if (!(row.ord_state >= 30 && row.ord_state < 50)) isCancel = false;
        });


		if($("#ismt").val() == "Y"){
			if(window.opener.closed == false){
				try {
					window.opener.SetOrd(ord_no, ord_opt_no);
					if($("[name=isclose]").is(":checked")) self.close();
				} catch(e){
					alert(e.message);
					alert('Error : Not Found SetGoods Function');
				}
			}else{
				alert("부모창이 없습니다.다시 시작해 주십시오.");
				self.close();
			}
		} else {
			if(rows.length == 1){
				SetChoiceOrderOne(lrow);
			} else {
				alert('1개이상의 주문을 선택할 수 없습니다.');
			}
		}



	}

	function SetChoiceOrderOne(rows) {

		var ff = document.f1;
		var data = new Array();


		if(rows == null){
			rows = gx.getSelectedRows();
		}
		rows.forEach(function(row){
			var ord_info_arr = {'ord_no': row.ord_no, 'ord_opt_no': row.ord_opt_no};
			ord_no.push(row.ord_no);
			ord_opt_no.push(row.ord_opt_no);
            data.push(ord_info_arr);
        });

		if(window.opener.closed == false){
			try {
				window.opener.SetOrd(ord_no, ord_opt_no);
				if($("[name=isclose]").is(":checked")) self.close();
			} catch(e){
				alert('Error : Not Found SetGoods Function');
			}
		}else{
			alert("부모창이 없습니다.다시 시작해 주십시오.");
			self.close();
		}
	}

    $(document).ready(function() {
		document.search.user_id.onkeyup	= checkNotUseDate;
        document.search.user_nm.onkeyup	= checkNotUseDate;
        document.search.ord_no.onkeyup	= checkNotUseDate;
		document.search.r_nm.onkeyup	= checkNotUseDate;
		document.search.cols.onchange	= checkNotUseDate;
		document.search.key.onkeyup		= checkNotUseDate;
    });

	function IsNotUseDate()
	{
		var ff = document.search;
		var is_not_use_date = false;

		// 주문번호, 회원아이디, 주문자, 수령자, 주문자핸드폰/전화, 수령자 핸드폰 일때 날짜 검색 무시

		if( ff.user_id.value != "" )
			is_not_use_date = true;
		else if( ff.user_nm.value != "" )
			is_not_use_date = true;
		else if( ff.ord_no.value != "" )
			is_not_use_date = true;
		else if( ff.r_nm.value.length >= 2 )
			is_not_use_date = true;
		else if(ff.cols.value == "b.mobile" && ff.key.value.length >= 8)
			is_not_use_date = true;
		else if(ff.cols.value == "b.phone" && ff.key.value.length >= 8)
			is_not_use_date = true;
		else if(ff.cols.value == "b.r_mobile" && ff.key.value.length >= 8)
			is_not_use_date = true;

		return is_not_use_date;
	}

    function checkNotUseDate()
    {

		var is_not_use_date = IsNotUseDate();

		if( is_not_use_date )
		{
			$("[name=sdate]").prop("disabled", true);
			$("[name=edate]").prop("disabled", true);
			$('#s_nud').prop("checked", false);
		}
		else
		{
			$("[name=sdate]").prop("disabled", false);
			$("[name=edate]").prop("disabled", false);
			$('#s_nud').prop("checked", true);
		}

    }

	function ManualNotUseData()
	{
		if( $("[name=s_nud]").is(":checked") == true )
		{
			$("[name=sdate]").prop("disabled", false);
			$("[name=edate]").prop("disabled", false);
		}
		else
		{
			$("[name=sdate]").prop("disabled", true);
			$("[name=edate]").prop("disabled", true);
		}
	}
</script>
@stop

@extends('shop_with.layouts.layout-nav')
@section('title','주문 선택')
@section('content')
<div class="container-fluid py-3">
    <div class="page_tit">
        <h3 class="d-inline-flex">주문 선택</h3>
        <div class="d-inline-flex location">
            <span class="home"></span>
            <span>/ 주문 선택</span>
        </div>
    </div>
    <div id="search-area" class="search_cum_form">
        <form method="get" name="search">
        <div class="card mb-3">
            <div class="d-flex card-header justify-content-between">
                <h4>검색</h4>
                <div class="flax_box">
                    <a href="#" onclick="Search()" class="btn btn-sm btn-primary mr-1 shadow-sm">검색</a>
                    <a href="#" onclick="formReset()" class="d-none search-area-ext d-sm-inline-block btn btn-sm btn-outline-primary mr-1 shadow-sm">검색조건 초기화</a>
                    <a href="#" onclick="window.close()" class="btn btn-sm btn-outline-primary mr-1 shadow-sm">닫기</a>
                    <div id="search-btn-collapse" class="btn-group mr-2 mb-0 mb-sm-0"></div>
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
                                    <div class="custom-control custom-switch date-switch-pos" data-toggle="tooltip" data-placement="top" data-original-title="주문일자 사용">
                                        <input type="checkbox" class="custom-control-input" id="switch4" checked="">
                                        <label class="" for="switch4" data-on-label="ON" data-off-label="OFF"></label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4 inner-td">
                            <div class="form-group">
                                <label for="type">주문번호</label>
                                <div class="flax_box">
                                    <input type='text' class="form-control form-control-sm search-all search-enter" name='ord_no' value='{{$ord_no}}'>
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
                                <label for="name">스타일넘버/상품코드</label>
                                <div class="form-inline">
                                    <div class="form-inline-inner input_box">
                                        <div class="form-group">
                                            <input type="text" class="form-control form-control-sm search-all ac-style-no search-enter" name="style_no" value="">
                                        </div>
                                    </div>
                                    <span class="text_line">/</span>
                                    <div class="form-inline-inner input_box">
                                        <div class="form-group">
                                            <input type="text" class="form-control form-control-sm search-all search-enter" name="goods_no" value="">
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
                    <div class="row d-none search-area-ext">
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
                                                    <input type="checkbox" class="custom-control-input" id="not_complex" value="Y">
                                                    <label for="not_complex" data-on-label="ON" data-off-label="OFF" style="margin-top:2px;"></label>
                                                </div>
                                            </div>
                                            <div style="height:30px;margin-left:2px;">
                                                <div class="custom-control custom-switch date-switch-pos" data-toggle="tooltip" data-placement="top" data-original-title="결제수수료 주문">
                                                    <input type="checkbox" class="custom-control-input" id="pay_fee" value="Y">
                                                    <label for="pay_fee" data-on-label="ON" data-off-label="OFF" style="margin-top:2px;"></label>
                                                </div>
                                            </div>
                                            <div style="height:30px;margin-left:2px;">
                                                <div class="custom-control custom-switch date-switch-pos" data-toggle="tooltip" data-placement="top" data-original-title="간편결제">
                                                    <input type="checkbox" class="custom-control-input" id="fintech" value="Y">
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
                                <div class="form-inline inline_input_box">
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
                                            <input type="text" id="com_nm" name="com_nm" class="form-control form-control-sm ac-company" style="width:100%">
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
                                    <select id="brand_cd" name="brand_cd" class="form-control form-control-sm select2-brand"></select>
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
                                            <select name="goods_type" class="form-control form-control-sm">
                                                <option value="">전체</option>
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
                                                <div class="custom-control custom-switch date-switch-pos" data-toggle="tooltip" data-placement="top" data-original-title="부가수수료 주문">
                                                    <input type="checkbox" class="custom-control-input" id="extra_fee" value="Y">
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
                                        </select>
                                    </div>
                                    <span class="text_line">/</span>
                                    <div class="form-inline-inner input_box" style="width:45%;">
                                        <select name="ord_field" class="form-control form-control-sm">
                                            <option value="a.ord_date" selected="">주문일자</option>
                                            <option value="b.user_nm">주문자</option>
                                            <option value="b.r_nm">수령자</option>
                                            <option value="c.goods_nm">상품명</option>
                                            <option value="c.style_no">스타일넘버</option>
                                            <option value="a.head_desc"> 상단홍보글</option>
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
                <input type="reset" id="search_reset" value="검색조건 초기화" class="btn btn-sm btn-outline-primary shadow-sm" onclick="formReset()">
                <div class="search_mode_wrap btn-group mr-2 mb-0 mb-sm-0"></div>
            </div>
        </form>
    </div>
    <div class="card shadow mb-3">
        <div class="card-body shadow">
            <div class="card-title">
                <div class="filter_wrap">
                    <div class="fl_box">
                        <h6 class="m-0 font-weight-bold">총 : <span id="gd-total" class="text-primary">0</span> 건</h6>
                    </div>
                </div>
            </div>
            <div class="table-responsive">
                <div id="div-gd" style="height:calc(100vh - 50vh); width:100%;" class="ag-theme-balham"></div>
            </div>
        </div>
    </div>
</div>

<script>
/**
 * 파일 : 상품 검색 팝업
 *
 * [사용법]
 * window open 한 php 파일에
 * selectOrder 메서드를 만든다.
 *
 * selectOrder 메서드에 파라메터로 선택한 템플릿의 제목 및 내용을 담은 json 데이터가 들어감
 *
 */
    let selectRow = null;

    const columns = [
        {
            field: "",
            headerName: "선택",
            cellRenderer: function(params) {
                return `<a href="#" onClick="selectOrder('${params.node.rowIndex}')">선택</a>`;
            },
            pinned: 'left'
        },
        {field:"ord_date" , headerName:"주문일", pinned: 'left',
            cellRenderer: function(params) {
                return params.value.substr(0, 10);
            }
         },
        {field:"ord_no" , headerName:"주문번호", width:170, cellStyle:StyleOrdNo, type:'ShopOrderNoType', pinned: 'left'},
        {field:"goods_nm" , headerName:"상품명", width:150,
            cellRenderer: function (params) {
                if (params.value !== undefined) {
                    if(params.data.goods_no == null) return '존재하지 않는 상품입니다.';
                    return '<a href="#" onclick="return openShopProduct(\'' + params.data.goods_no + '\');">' + params.value + '</a>';
                }
            }
        },
        {field:"style_no" , headerName:"스타일넘버"  },
        {field:"opt_val" , headerName:"옵션"  },
        {field:"qty" , headerName:"수량"},
        {field:"user_nm" , headerName:"주문자"  },
        {field:"r_nm" , headerName:"수령자"  },
        {field:"price" , headerName:"판매가", type: 'currencyType'  },
        {field:"pay_type" , headerName:"결제방법"   },
        {field:"head_desc" , headerName:"상단홍보글", width:180},
        {field:"coupon_nm" , headerName:"쿠폰"  },
        {field:"ord_type" , headerName:"출고형태", width:180},
        {field:"ord_kind",headerName:"출고구분",cellStyle:StyleOrdKind},
        {field:"ord_state" , headerName:"주문상태",cellStyle:StyleOrdState},
        {field:"clm_state" , headerName:"클레임상태",cellStyle:StyleClmState },
        {field:"com_nm" , headerName:"업체구분" },
        {field:"com_nm" , headerName:"업체" },
        {field:"baesong_kind" , headerName:"배송구분"  },
        {field:"baesong_info" , headerName:"배송정보"  },
        {field:"dlv_end_date" , headerName:"배송완료일"  },
        {field:"upd_date" , headerName:"최종처리일"  },
    ];
    const pApp = new App('', { gridId: "#div-gd" });
    const gridDiv = document.querySelector(pApp.options.gridId);
    const gx = new HDGrid(gridDiv, columns);

    pApp.ResizeGrid();

    function Search(){
        let data = $('form[name="search"]').serialize();
        gx.Request('/shop/order/ord01/search2/popup', data, 1);
    }

    function selectOrder(idx) {
        if (idx === '') return;

        if (opener && opener.selectOrder) opener.selectOrder(gx.gridOptions.api.getRowNode(idx).data)

        window.close();
    }

    function formReset() {
        document.search.reset();
    }

    Search();

    function openShopProduct(prd_no){
        var url = '/shop/product/prd01/' + prd_no;
        var product = window.open(url, "_blank", "toolbar=no,scrollbars=yes,resizable=yes,status=yes,top=500,left=500,width=1024,height=900");
    }
</script>
@stop

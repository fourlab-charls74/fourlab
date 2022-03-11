@extends('head_with.layouts.layout')
@section('title','수기판매')
@section('content')

<div class="page_tit">
    <h3 class="d-inline-flex">수기판매</h3>
    <div class="d-inline-flex location">
        <span class="home"></span>
        <span>/ 주문&amp;배송</span>
        <span>/ 수기판매</span>
    </div>
</div>
<div id="search-area" class="search_cum_form">
    <form method="get" name="search">
        <div class="card mb-3">
            <div class="d-flex card-header justify-content-between">
                <h4>검색</h4>
                <div class="flax_box">
                    <a href="#" id="search_sbtn" onclick="Search();" class="btn btn-sm btn-primary shadow-sm pl-2 mx-1"><i class="fas fa-search fa-sm text-white-50"></i> 조회</a>
                    <a href="#" onclick="reset()" class="d-none search-area-ext d-sm-inline-block btn btn-sm btn-outline-primary mr-1 shadow-sm">검색조건 초기화</a>
                    <a href="#" onclick="openOrd02Show()" class="d-none search-area-ext d-sm-inline-block btn btn-sm btn-outline-primary mr-1 shadow-sm">수기등록</a>
                    <a href="/head/order/ord03" class="d-none search-area-ext d-sm-inline-block btn btn-sm btn-outline-primary mr-1 shadow-sm">수기일괄등록</a>
                    <a href="#" onclick="" class="d-none search-area-ext d-sm-inline-block btn btn-sm btn-outline-primary mr-1 shadow-sm">SMS 발송</a>
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
                                <input type='text' class="form-control form-control-sm search-all search-enter search-enter" name='ord_no' value=''>
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
                                        <input type="text" id="com_nm" name="com_nm" class="form-control form-control-sm ac-company" style="width:100%;">
                                        <a href="#" class="btn btn-sm btn-outline-primary sch-company"><i class="bx bx-dots-horizontal-rounded fs-16"></i></a>
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
                            <label for="name">결제방법</label>
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
                            <label for="name">출고구분</label>
                            <div class="flax_box">
                                <select name='ord_kind' class="form-control form-control-sm">
                                    <option value=''>전체</option>
                                    @foreach ($ord_kinds as $ord_kind)
                                        <option value='{{ $ord_kind->code_id }}'>{{ $ord_kind->code_val }}</option>
                                    @endforeach
                                </select>
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
                            <label for="name">상품구분</label>
                            <div class="flax_box">
                                <select name='goods_type' class="form-control form-control-sm">
                                    <option value=''>전체</option>
                                    @foreach ($goods_types as $goods_type)
                                        <option value='{{ $goods_type->code_id }}'>{{ $goods_type->code_val }}</option>
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
                            <label for="name">상단홍보글</label>
                            <div class="flax_box">
                                <input type="text" class="form-control form-control-sm search-enter" name="head_desc" value="">
                            </div>
                        </div>
                    </div>
                    <!-- end col -->
                    <div class="col-lg-4 inner-td">
                        <div class="form-group">
                            <label for="name">출력자료수</label>
                            <div class="form-inline">
                              <select name="limit" class="form-control form-control-sm">
                                  <option value="100">100</option>
                                  <option value="500">500</option>
                                  <option value="1000">1000</option>
                                  <option value="2000">2000</option>
                              </select>
                            </div>
                        </div>
                    </div>
                    <!-- end col -->
                    <div class="col-lg-4 inner-td">
                        <div class="form-group">
                            <label for="name">정렬순서</label>
                            <div class="form-inline">
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
                <!--<a href="#" class="btn-sm btn btn-primary order-test-btn">Common_return Test</a>//-->
                <a href="#" class="btn-sm btn btn-primary order-del-btn">출고 전 주문삭제</a>
                </div>
            </div>
        </div>
        <div class="table-responsive">
            <div id="div-gd" style="height:calc(100vh - 50vh); width:100%;" class="ag-theme-balham"></div>
        </div>
    </div>
</div>

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
        {field:"ord_no" , headerName:"주문번호", width:130, cellStyle:StyleOrdNo, type:'HeadOrderNoType', pinned: 'left'},
        {field:"ord_opt_no" , headerName:"일련번호", width:58, sortable:"ture", pinned: 'left', type:'HeadOrderNoType'},
        {field:"ord_state" , headerName:"주문상태", width:70, cellStyle:StyleOrdState, pinned: 'left'  },
        {field:"clm_state" , headerName:"클레임상태", width:70, cellStyle:StyleClmState, pinned: 'left'  },
        {field:"pay_stat" , headerName:"입금상태", width:58  },
        {field:"goods_type_nm" , headerName:"상품구분", width:58, cellStyle:StyleGoodsType  },
        {field:"style_no" , headerName:"스타일넘버"  },
        {field:"goods_nm" , headerName:"상품명",type:"HeadGoodsNameType"},
        {
            field:"img" ,
            headerName:"이미지", width:80, hide:true,
            cellRenderer: function(params) {
                if (params.value !== undefined) {
                    return '<img src="' + params.data.img + '"/>';
                }
            }
        },
        {field:"opt_val" , headerName:"옵션", width:84  },
        {field:"qty" , headerName:"수량", width:46},
        {field:"user_nm" , headerName:"주문자(아이디)"  },
        {field:"r_nm" , headerName:"수령자", width:60  },
        {field:"goods_price" , headerName:"자사몰 판매가", width:84, type: 'currencyType'  },
        {field:"price" , headerName:"판매가", width:60, type: 'currencyType'  },
        {field:"dlv_amt" , headerName:"배송비", width:46, type: 'currencyType'  },
        {field:"sales_com_fee" , headerName:"판매수수료", width:72 , type: 'currencyType'  },
        {field:"pay_type" , headerName:"결제방법", wdith:72   },
        {
            field:"ord_type",
            headerName:"주문구분",
            cellStyle:StyleOrdKind,
            width:58
        },
        {
            field:"ord_kind",
            headerName:"출고구분",
            cellStyle:StyleOrdKind,
            width:58
        },
        {field:"sale_place" , headerName:"판매처", width:72 },
        {field:"out_ord_no" , headerName:"판매처주문번호" },
        {field:"com_nm" , headerName:"업체" },
        {field:"baesong_kind" , headerName:"배송구분", width:58  },
        {field:"dlv_nm" , headerName:"택배업체", width:72  },
        {field:"dlv_no" , headerName:"송장번호", width:72  },
        {field:"state" , headerName:"처리현황", editable: true, cellStyle: editCellStyle  },
        {field:"memo" , headerName:"메모", editable: true, cellStyle: editCellStyle  },
        {field:"ord_date" , headerName:"주문일시", width:110 },
        {field:"pay_date" , headerName:"입금일시", width:110 },
        {field:"dlv_end_date" , headerName:"배송일시", width:110},
        {field:"last_up_date" , headerName:"클레임일시", width:110}
    ];


    const pApp = new App('', { gridId: "#div-gd" });
    const gridDiv = document.querySelector(pApp.options.gridId);
    const gx = new HDGrid(gridDiv, columns, {
      isRowSelectable : function(node){
        return node.data.ord_state === "출고요청" || node.data.ord_state === "입금예정";
        //return node.data.ord_state != "";
      }
    });

    pApp.ResizeGrid(275);

    pApp.BindSearchEnter();

    function Search(){
        let data = $('form[name="search"]').serialize();
        gx.Request('/head/order/ord02/search', data, 1);
    }

    $('.order-del-btn').click(function(e){
        e.preventDefault();
        const rows = gx.getSelectedRows();

        if (rows.length === 0) {
            alert("삭제할 주문을 선택해주세요.");
            return;
        }

        let msg = '삭제된 주문은 다시 복원할 수 없습니다.\n';
            msg += '주문을 삭제 하시겠습니까?';

        if (confirm(msg) === false) return;

        const ord_nos = [];

        rows.forEach((row) =>{
            ord_nos.push(row.ord_no);
        });

        $.ajax({
            async: true,
            type: 'delete',
            url: '/head/order/ord02',
            data: { ord_nos },
            dataType:"json",
            success: function (data) {
                alert("삭제되었습니다.");
                Search();
            },
            error: function(xhr, status, error) {
                console.log(xhr.responseText);
            }
        });
    });

    $('.order-test-btn').click(function(e){
        e.preventDefault();
        const rows = gx.getSelectedRows();
        if (rows.length === 0) {
            alert("테스트할 주문을 선택해주세요.");
            return;
        }

        if (confirm("입금확인 하시겠습니까?") === false) return;

        /*
            // 입금확인
            $site_cd	= "T0000";			// 사이트 코드
            $tno		= "20120726908184";	// KCP 거래번호
            $order_no	= "201207261411561241";	// 주문번호
            $tx_cd		= "TX00";				// 업무처리 구분 코드
            $tx_tm		= "20120726133505";		// 업무처리 완료 시간
            $ipgm_name	= "이희천";
            $remitter	= "이희천";
            $ipgm_mnyx	= "1000000";
            $bank_code	= "신한은행";
            $account	= "T0400000040177";
            $op_cd		= 1;

            // 입금취소
            $site_cd	= "W7019";				// 사이트 코드
            $tno		= "20080804342354";		// KCP 거래번호
            $order_no	= "2008080410599ec6a5";	// 주문번호
            $tx_cd		= "TX00";				// 업무처리 구분 코드
            $tx_tm		= "20080429104422";		// 업무처리 완료 시간
            $account	= "12301230123";
            $ipgm_name	= "손상모";
            $remitter	= "손상모";
            $ipgm_mnyx	= "50000";
            $bank_code	= "국민";
            $account	= "12345";
            $op_cd		= 13;
        */

        rows.forEach((row) =>{

            var post = {
                "site_cd"   : "N3748",         // 사이트 코드
                "tno"      : "20210827000001",   // KCP 거래번호
                "order_no"   : row.ord_no,   // 주문번호
                "tx_cd"      : "TX00",            // 업무처리 구분 코드
                "tx_tm"      : "20210827104705",      // 업무처리 완료 시간
                "ipgm_name"   : "김용남",
                "remitter"   : "김용냄",
                "ipgm_mnyx"   : "1000000",
                "bank_code"   : "신한은행",
                "account"   : "T0400000040177",
                "op_cd"      : 13,
            };

            $.ajax({
                type: 'post',
                url: '/ext/kcp/common_return',
                data: post,
                dataType:"json",
                success: function (data) {
                    console.log(data);
                },
                error: function(xhr, status, error) {
                    console.log(xhr.responseText);
                }
            });

        });
    });

    Search();

    function reset() {
        event.preventDefault();
        document.search.reset();
    }

    function openOrd02Show() {
        const url='/head/order/ord02/show';
        window.open(url,"_blank","toolbar=no,scrollbars=yes,resizable=yes,status=yes,top=500,left=500,width=1200,height=800");
    }
</script>
@stop

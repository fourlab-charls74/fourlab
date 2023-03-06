@extends('head_with.layouts.layout')
@section('title','쿠폰')
@section('content')
<div class="page_tit">
    <h3 class="d-inline-flex">쿠폰</h3>
    <div class="d-inline-flex location">
        <span class="home"></span>
        <span>/ 프로모션</span>
        <span>/ 쿠폰</span>
    </div>
</div>
<form method="get" name="search">
    <input type="hidden" name="fields">
    <div id="search-area" class="search_cum_form">
        <div class="card mb-3">
            <div class="d-flex card-header justify-content-between">
                <h4>검색</h4>
                <div class="flax_box">
                    <a href="#" id="search_sbtn" onclick="Search();" class="btn btn-sm btn-primary shadow-sm mr-1"><i class="fas fa-search fa-sm text-white-50"></i> 조회</a>
                    <a href="#" class="btn btn-sm btn-outline-primary shadow-sm mr-1 pl-2 add-btn"><i class="bx bx-plus fs-16"></i> 추가</a>
                    <div id="search-btn-collapse" class="btn-group mb-0 mb-sm-0"></div>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <!-- 아이디 -->
                    <div class="col-lg-4 inner-td">
                        <div class="form-group">
                            <label for="">구분</label>
                            <div class="flax_box">
                                <select name="coupon_type" id="coupon_type" class="form-control form-control-sm">
                                    <option value="">전체</option>
                                    @foreach($types as $key => $val)
                                        <option value="{{$key}}">{{$val}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <!-- 회원명 -->
                    <div class="col-lg-4 inner-td">
                        <div class="form-group">
                            <label for="dlv_kind">쿠폰명</label>
                            <div class="flax_box">
                                <input type="text" name="coupon_nm" id="coupon_nm" class="form-control form-control-sm">
                            </div>
                        </div>
                    </div>
                    <!-- 상품상태 -->
                    <div class="col-lg-4 inner-td">
                        <div class="form-group">
                            <label for="">사용여부</label>
                            <div class="flax_box">
                                <select name="use_yn" id="use_yn" class="form-control form-control-sm">
                                    <option value="">전체</option>
                                    @foreach($use_yn as $val)
                                        <option value="{{$val->code_id}}">{{$val->code_val}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- end row -->
                <div class="row">
                    <!-- 아이디 -->
                    <div class="col-lg-4 inner-td">
                        <div class="form-group">
                            <label for="">사용가능 대상</label>
                            <div class="flax_box">
                                <select name="apply" id="apply" class="form-control form-control-sm">
                                    @foreach($apply as $key => $val)
                                        <option value="{{$key}}">{{$val}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <!-- 회원명 -->
                    <div class="col-lg-4 inner-td">
                        <div class="form-group">
                            <label for="dlv_kind">스타일넘버/상품코드</label>
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
                    <!-- 상품상태 -->
                    <div class="col-lg-4 inner-td">
                        <div class="form-group">
                            <label for="">상품명</label>
                            <div class="flax_box">
                                <input type="text" class="form-control form-control-sm ac-goods-nm search-enter" name="goods_nm" value="">
                            </div>
                        </div>
                    </div>
                </div>
                <!-- end row -->

            </div>
        </div>
        <div class="resul_btn_wrap mb-3">
            <a href="#" id="search_sbtn" onclick="Search();" class="btn btn-sm btn-primary shadow-sm mr-1"><i class="fas fa-search fa-sm text-white-50"></i> 조회</a>
            <a href="#" class="btn btn-sm btn-outline-primary shadow-sm mr-1 pl-2 add-btn"><i class="bx bx-plus fs-16"></i> 추가</a>
            <div class="search_mode_wrap btn-group mr-2 mb-0 mb-sm-0"></div>
        </div>
    </div>
</form>
<div id="filter-area" class="card shadow-none mb-0 ty2 last-card">
    <div class="card-body shadow">
        <div class="card-title">
            <div class="filter_wrap">
                <div class="fl_box">
                    <h6 class="m-0 font-weight-bold">총 <span id="gd-total" class="text-primary">0</span> 건</h6>
                </div>
                <div class="fr_box">
                    @if($type === 'pop')
                        <a href="#" class="btn-sm btn btn-primary coupon-selected-btn">쿠폰선택</a>
                    @else
                        <a href="#" class="btn-sm btn btn-primary gift-coupon-btn">쿠폰지급</a>
                        <!--<a href="#" class="btn-sm btn btn-primary gift-auto-btn">쿠폰자동지급</a>//-->
                    @endif
                </div>
            </div>
        </div>
        <div class="table-responsive">
            <div id="div-gd" style="height:calc(100vh - 50vh); width:100%;" class="ag-theme-balham"></div>
        </div>
    </div>
</div>

<script>
    let gx;

    $(document).ready(function() {
        let columns = [
            {
                headerName: '',
                headerCheckboxSelection: true,
                checkboxSelection: true,
                width:28,
            },
            {field:"coupon_no" , headerName:"번호"},
            {
                field:"coupon_nm",
                headerName:"쿠폰명",
                width:220,
                type:"HeadCouponType"
            },
            {field:"coupon_type_nm" , headerName:"구분"},
            {
                headerName:"발행기간",
                children : [
                    {headerName:"시작", field:"pub_fr_date", width:80},
                    {headerName:"종료", field:"pub_to_date", width:80}
                ]
            },
            {
                headerName:"유효기간",
                children : [
                    {headerName:"시작", field:"use_fr_date", width:100},
                    {headerName:"종료", field:"use_to_date", width:100}
                ]
            },
            {field:"pub_dup_yn" , headerName:"복수발급", width: 70, cellStyle:{"text-align" : "center"},
                cellRenderer: function(params) {
                    if(params.value == 'Y') return "예"
                    else if(params.value == 'N') return "아니오"
                    else return params.value
                }
            },
            {field:"coupon_amt" , headerName:"발행금액", cellClass:'hd-grid-number'},
            {field:"pub_time" , headerName:"발행시점"},
            {field:"coupon_apply" , headerName:"사용가능 대상"},
            {
                field:"pub_cnt",
                headerName:"발행수",
                cellStyle : function(p){
                    if (p.value && p.value != "무제한")
                        return { 'text-align' : 'right' }
                },
                cellRenderer: function(p) {
                    if (p.value && p.value != "무제한") {
                        return `<a href="#" onclick="openSerial('${p.data.coupon_no}')">${numberFormat(parseInt(p.value))}</a>`;
                    }
                    return p.value;
                }
            },
            {
                field:"coupon_pub_cnt",
                headerName:"다운로드",
                type: 'currencyType',
                cellRenderer: function(p) {
                    if (p.value) {
                        return `<a href="#" onclick="openUsedList('${p.data.coupon_no}')">${numberFormat(parseInt(p.value))}</a>`;
                    }
                }
            },
            {field:"coupon_order_cnt" , headerName:"사용수", type: 'currencyType'},
            {field:"use_yn" , headerName:"사용여부", width: 70, cellStyle:{"text-align" : "center"},
                cellRenderer: function(params) {
                    if(params.value == 'Y') return "예"
                    else if(params.value == 'N') return "아니오"
                    else return params.value
                }
            },
            {field:"admin_nm" , headerName:"발행자"},
            {field:"regi_date" , headerName:"등록일시"},
            {width:"auto"}
        ];
        
        const pApp = new App('', {gridId: "#div-gd"});
        const gridDiv = document.querySelector(pApp.options.gridId);
        gx = new HDGrid(gridDiv, columns);
        
        pApp.ResizeGrid(265);

        $('.add-btn').click(() => openCouponDetail());

        $('.gift-coupon-btn').click(() => {
            const rows = gx.getSelectedRows();
            if (rows.length === 0) {
                alert("지급할 쿠폰을 선택해주세요.");
                return;
            }
            coupons = rows.map((row) => row.coupon_no);

            openCoupon('', coupons.join(','));
        });

        $('.coupon-selected-btn').click((e) => {
            e.preventDefault();
            const rows = gx.getSelectedRows();

            if (rows.length === 0) {
                alert("쿠폰을 선택해주세요.");
                return;
            }

            //팝업을 오픈한 페이지에 couponSelectedCallback 메서드가 등록되어 있다면 실행.
            opener?.couponSelectedCallback?.(rows);

            window.close();
        });

        $('.gift-auto-btn').click(function(){
            const url=`/head/promotion/prm10/gift/auto`;
            window.open(url,"_blank","toolbar=no,scrollbars=yes,resizable=yes,status=yes,top=500,left=500,width=600,height=300");
        });

        Search();
    });

    function Search() {
        let data = $('form[name="search"]').serialize();
        gx.Request(`/head/promotion/prm10/search`, data, 1);
    }

    const openUsedList = (no) => {
        const url=`/head/promotion/prm10/used/${no}`;
        window.open(url,"_blank","toolbar=no,scrollbars=yes,resizable=yes,status=yes,top=500,left=500,width=1200,height=800");
    }
    const openSerial = (no) => {
        const url=`/head/promotion/prm10/serial/${no}`;
        window.open(url,"_blank","toolbar=no,scrollbars=yes,resizable=yes,status=yes,top=500,left=500,width=1200,height=800");
    }
</script>
@stop

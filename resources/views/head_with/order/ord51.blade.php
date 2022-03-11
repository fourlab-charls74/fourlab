@extends('head_with.layouts.layout')
@section('title','광고주문내역')
@section('content')
<div class="page_tit">
    <h3 class="d-inline-flex">광고주문내역</h3>
    <div class="d-inline-flex location">
        <span class="home"></span>
        <span>/ 주문&amp;배송</span>
        <span>/ 광고주문내역</span>
    </div>
</div>

<div id="search-area" class="search_cum_form">
    <form method="get" name="search">
        <input type="hidden" name="page" id="1page" value="">
        <div class="card mb-3">
            <div class="d-flex card-header justify-content-between">
                <h4>검색</h4>
                <div>
                    <a href="#" id="search_sbtn" onclick="return Search();" class="btn btn-sm btn-primary shadow-sm pl-2"><i class="fas fa-search fa-sm text-white-50"></i> 조회</a>
                    <div id="search-btn-collapse" class="btn-group mb-0 mb-sm-0"></div>
                </div>
            </div>
            <div class="card-body">
                <!-- 주문일자/광고/유입사이트 -->
                <div class="row">
                    <!-- 주문일자 -->
                    <div class="col-lg-4 inner-td">
                        <div class="form-group">
                            <label for="user_yn">주문일자</label>
                            <div class="form-inline date-select-inbox">
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
                            </div>
                        </div>
                    </div>

                    <!-- 광고 -->
                    <div class="col-lg-4 inner-td">
                        <div class="form-group">
                            <label for="type">광고</label>
                            <div class="form-inline">
                                <div class="form-inline-inner input_box">
                                    <div class="form-group">
                                        <select name="ad_type" id="ad_type" class="sch-ad_type form-control form-control-sm">
                                            <option value="">광고구분</option>
                                            @foreach($ad_types as $ad_type)
                                            <option value="{{ $ad_type->code_id }}">{{ $ad_type->code_val }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <span class="text_line"><i class="bx bx-right-arrow-alt fs-12"></i></span>
                                <div class="form-inline-inner input_box sort_select">
                                    <div class="form-group">
                                        <select name="ad" id="ad" class="sch_ad form-control form-control-sm">
                                            <option value="">선택</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- 유입사이트 -->
                    <div class="col-lg-4 inner-td">
                        <div class="form-group">
                            <label for="type">유입사이트</label>
                            <div class="flax_box">
                                <input type="text" class="form-control form-control-sm" name="referer">
                            </div>
                        </div>
                    </div>

                </div>

                <!-- 키워드/브랜드/상품명 -->
                <div class="row">
                    <div class="col-lg-4 inner-td">
                        <div class="form-group">
                            <label for="name">키워드</label>
                            <div class="flax_box">
                                <input type="text" class="form-control form-control-sm search-all search-enter" name="keyword" value="">
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-4 inner-td">
                        <div class="form-group">
                            <label for="name">브랜드</label>
                            <div class="form-inline inline_btn_box">
                                <select id="brand_cd" name="brand_cd" class="form-control form-control-sm select2-brand"></select>
                                <a href="#" class="btn btn-sm btn-outline-primary sch-brand"><i class="bx bx-dots-horizontal-rounded fs-16"></i></a>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-4 inner-td">
                        <div class="form-group">
                            <label for="name">상품명</label>
                            <div class="flax_box">
                                <input id="goods_nm" class="form-control form-control-sm search-all search-enter" onkeydown=o_ac.ack(); value="" name="goods_nm" autocomplete="off">
                            </div>
                        </div>
                    </div>

                </div>


                <!-- end row -->

                <!-- 업체/판매처/자료수/정렬순서 -->
                <div class="row search-area-ext">
                    <!-- 업체 -->
                    <div class="col-lg-4 inner-td">
                        <div class="form-group">
                            <label for="name">업체</label>
                            <div class="form-inline inline_select_box">
                                <div class="form-inline-inner select-box">
                                    <select name="com_type" id="" class="form-control form-control-sm">
                                        <option value="">전체</option>
                                        @foreach($com_types as $com_type)
                                        <option value="{{ $com_type->code_id }}">{{ $com_type->code_val }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-inline-inner input-box">
                                    <div class="form-inline inline_btn_box">
                                        <input type="hidden" name="cat_cd" id="cat_cd" value="">
                                        <input type="text" class="form-control form-control-sm search-all search-enter ac-company2" name='com_nm' id='com_nm' value='' autocomplete='off'>
                                        <a href="#" class="btn btn-sm btn-outline-primary company-add-btn"><i class="bx bx-dots-horizontal-rounded fs-16"></i></a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- 판매처-->
                    <div class="col-lg-4 inner-td">
                        <div class="form-group">
                            <label for="name">판매처</label>
                            <div class="flax_box">
                                <select name='sale_place' class="form-control form-control-sm">
                                    <option value=''>전체</option>
                                    @foreach($sale_place_list as $sale_place)
                                    <option value="{{ $sale_place->id }}">{{ $sale_place->val }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- 자료수/정렬순서 -->
                    <div class="col-lg-4 inner-td">
                        <div class="form-group">
                            <label for="name">자료수/정렬순서</label>
                            <div class="form-inline">
                                <div class="form-inline-inner input_box" style="width:24%;">
                                    <select name="limit" class="form-control form-control-sm">
                                        <option selected value=100>100</option>
                                        <option value=500>500</option>
                                        <option value=1000>1000</option>
                                        <option value=2000>2000</option>
                                        <option value=-1>모두</option>
                                    </select>
                                </div>
                                <span class="text_line">/</span>
                                <div class="form-inline-inner input_box" style="width:45%;">
                                    <select name="ord_field" class="form-control form-control-sm">
                                        <option value="t.ord_no">주문번호</option>
                                        <option value="t.type">광고구분</option>
                                        <option value="name">광고명</option>
                                        <option value="t.kw">키워드</option>
                                        <option value="goods_nm">상품명</option>
                                        <option value="com_nm">공급업체</option>
                                        <option value="upd_dm">수정일</option>
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
    </form>
    <div class="resul_btn_wrap mb-3">
        <a href="#" id="search_sbtn" onclick="return Search();" class="btn btn-sm btn-primary shadow-sm pl-2"><i class="fas fa-search fa-sm text-white-50"></i> 조회</a>
        <div class="search_mode_wrap btn-group mr-2 mb-0 mb-sm-0"></div>
    </div>
</div>

<div id="filter-area" class="card shadow-none mb-0 search_cum_form ty2 last-card">
    <div class="card-body shadow">
        <div class="card-title form-inline text-right">
            <h6 class="m-0 font-weight-bold">총 : <span id="gd-total" class="text-primary">0</span> 건</h6>
        </div>
        <div class="table-responsive">
            <div id="div-gd" style="height:calc(100vh - 370px);width:100%;" class="ag-theme-balham"></div>
        </div>
    </div>
</div>


<script language="javascript">
    //const pageNo = -1;
    const pageNo = 1;
    var columns = [
        // this row shows the row index, doesn't use any data from the row
        {
            headerName: '#',
            width: 35,
            pinned: 'left',
            maxWidth: 100,
            // it is important to have node.id here, so that when the id changes (which happens
            // when the row is loaded) then the cell is refreshed.
            valueGetter: 'node.id',
            cellRenderer: 'loadingRenderer',
        },
        {
            field: "ord_date",
            headerName: "주문일",
            width: 120,
            cellStyle: StyleGoodsTypeNM,
            editable: true,
            pinned: 'left',
        },
        {
            field: "ord_no",
            headerName: "주문번호",
            width: 130,
            cellStyle: StyleOrdNo,
            type: 'HeadOrderNoType',
            pinned: 'left'
        },
        {
            field: "ord_opt_no",
            headerName: "주문일련번호",
            sortable: "ture",
            width: 80,
            pinned: 'left',
            type: 'HeadOrderNoType'
        },
        {
            field: "goods_nm",
            headerName: "상품명",
            type: "HeadGoodsNameType",
            pinned: 'left'
        },

        {
            field: "goods_opt",
            headerName: "옵션",
            width: 80,
        },
        {
            field: "qty",
            headerName: "수량",
            width: 48,
        },
        {
            field: "price",
            headerName: "판매가",
            width: 60,
            type: 'currencyType'
        },
        {
            field: "ord_state",
            headerName: "주문상태",
            width: 72,
            cellStyle: StyleOrdState,
        },
        {
            field: "clm_state",
            headerName: "클레임상태",
            width: 72,
            cellStyle: StyleClmState,
        },
        {
            field: "ad_type",
            headerName: "광고구분",
            width: 72,
        },
        {
            field: "name",
            headerName: "광고명",
            width: 60,
        },
        {
            field: "se",
            headerName: "검색엔진",
            width: 72,
        },
        {
            field: "site_type",
            headerName: "구분",
            width: 72,
        },
        {
            field: "kw",
            headerName: "키워드",
            width: 72,
        },
        {
            field: "track",
            headerName: "주문경로",
            width: 72,
        },
        {
            field: "vt",
            headerName: "방문시간",
            width: 72,
            cellRenderer: function(params) {
                if (params.value !== undefined) {
                    var time_str = Math.floor((params.value / 60)) + ":" + (params.value % 60);
                    //console.log(time_str);
                    //return params.value;
                    return time_str;
                }
            }
        },
        {
            field: "vc",
            headerName: "방문횟수",
            width: 72,
        },
        {
            field: "pageview",
            headerName: "페이지뷰",
            width: 72,
        },
        {
            field: "referer",
            headerName: "유입경로",
            width: 230,

            cellRenderer: function(params) {
                return "<a href='#' onClick='window.open(\"" + params.value + "\",\"referer\",\"width=800,height=600\");return false;'>" + params.value + "</a>";
            }

        },

        {
            field: "goods_no",
            headerName: "goods_no",
            hide: true,
        },
        {
            field: "goods_sub",
            headerName: "goods_sub",
            hide: true,
        },
        {
            field: "ad",
            headerName: "ad",
            hide: true,
        },
        {
            field: "diff",
            headerName: "diff",
            hide: true,
        },
    ];

    const pApp = new App('', {
        gridId: "#div-gd"
    });
    const gridDiv = document.querySelector(pApp.options.gridId);
    const gx = new HDGrid(gridDiv, columns);

    pApp.ResizeGrid(250);

    function Search() {
        let formData = $('form[name="search"]').serialize();
        gx.Request('/head/order/ord51/search', formData, 1, adCallback);
    }

    function adCallback(data) {
        //console.log(data);
    }
    $(function() {
        Search(pageNo);
    });
</script>

<script language="javascript">
    $(function() {

        $(".ac-brand2")
            .autocomplete({
                //keydown 됬을때 해당 값을 가지고 서버에서 검색함.
                source: function(request, response) {
                    $.ajax({
                        method: 'get',
                        url: '/head/auto-complete/brand',
                        data: {
                            "keyword": this.term
                        },
                        success: function(data) {
                            //console.log(data);
                            response(data);
                        },
                        error: function(request, status, error) {
                            console.log("error");
                            console.log("code:" + request.status + "\n" + "message:" + request.responseText + "\n" + "error:" + error);
                        }
                    });
                },
                minLength: 1,
                autoFocus: true,
                delay: 100,
                focus: function(event, ui) {},
                select: function(event, ui) {
                    //console.log(ui.item);
                    $("#brand_cd").val(ui.item.id);
                }

            });

        $('.ac-company2').autocomplete({
            //keydown 됬을때 해당 값을 가지고 서버에서 검색함.
            source: function(request, response) {
                $.ajax({
                    method: 'get',
                    url: '/head/auto-complete/company',
                    data: {
                        keyword: this.term
                    },
                    success: function(data) {
                        //response(data);
                    },
                    error: function(request, status, error) {
                        console.log("error")
                    }
                });
            },
            minLength: 1,
            autoFocus: true,
            delay: 100,
            select: function(event, ui) {
                //console.log(ui.item);
                $("#com_id").val(ui.item.id);
            }
        });


        $('.brand-add-btn').click((e) => {
            e.preventDefault();

            searchBrand.Open((code, name) => {
                if (confirm("선택한 브랜드를 추가하시겠습니까?") === false) return;

                $("#brand_cd").val(code);
                $("#brand_nm").val(name);

            });
        });

        $(".company-add-btn").click((e) => {
            e.preventDefault();

            searchCompany.Open((code, name) => {
                if (confirm("선택한 업체를 추가하시겠습니까?") === false) return;

                $("#com_nm").val(name);
                $("#com_id").val(code);

            });
        });

    });
</script>
@stop
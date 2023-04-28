@extends('head_with.layouts.layout')
@section('title','발주')
@section('content')
    <div class="page_tit">
        <h3 class="d-inline-flex">발주</h3>
        <div class="d-inline-flex location">
            <span class="home"></span>
            <span>/ 재고</span>
        </div>
    </div>
    <form method="get" name="search">
        <div id="search-area" class="search_cum_form">
            <div class="card mb-3">
                <div class="d-flex card-header justify-content-between">
                    <h4>검색</h4>
                    <div>
                        <a href="#" id="search_sbtn" onclick="Search();" class="btn btn-sm btn-primary shadow-sm pl-2"><i class="fas fa-search fa-sm text-white-50"></i> 검색</a>
                        <a href="#" onclick="add();" class="btn btn-sm btn-outline-primary shadow-sm pl-2"><i class="bx bx-plus fs-16"></i> 추가</a>
                        <a href="#" onclick="gx.Download();" class="btn btn-sm btn-outline-primary shadow-sm pl-2"><i class="bx bx-download fs-16"></i> 엑셀다운로드</a>
                        <div id="search-btn-collapse" class="btn-group mb-0 mb-sm-0"></div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-4">
                            <div class="form-group">
                                <label for="formrow-firstname-input">발주일자</label>
                                <div class="form-inline">
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
                                            <input type="text" class="form-control form-control-sm docs-date search-enter" name="edate" value="{{ $edate }}" autocomplete="off">
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
                        <div class="col-lg-4">
                            <div class="form-group">
                                <label for="buy_ord_no">발주번호/발주상태</label> 
                                <div class="form-inline inline_select_box">
                                    <div class="form-inline-inner input_box">
                                        <input type='text' class="form-control form-control-sm ac-style-no search-enter" name='buy_ord_no' id="buy_ord_no" value="">
                                    </div>
                                    <span class="text_line">/</span>
                                    <div class="form-inline-inner input-box w-25 pr-1">
                                        <select name="buy_order_state" class="form-control form-control-sm w-100">
                                            <option value="">모두</option>
                                            @foreach ($buy_order_states as $buy_order_state)
                                                <option value="{{ $buy_order_state->code_id }}">{{ $buy_order_state->code_val }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="form-group">
                                <label for="brand_cd">브랜드</label>
                                <div class="form-inline inline_btn_box">
                                    <select id="brand_cd" name="brand_cd" class="form-control form-control-sm select2-brand"></select>
                                    <a href="#" class="btn btn-sm btn-outline-primary sch-brand"><i class="bx bx-dots-horizontal-rounded fs-16"></i></a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-4">
                            <div class="form-group">
                                <label for="">상품상태</label>
                                <div class="form-inline">
                                    <div class="form-inline-inner input-box w-50 pr-2">
                                        <select name="goods_stat" class="form-control form-control-sm w-100">
                                            <option value='' selected>전체</option>
                                            @foreach ($goods_stats as $goods_stat)
                                                <option value='{{ $goods_stat->code_id }}'>{{ $goods_stat->code_val }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="form-inline-inner form-check-box">
                                        <div class="form-inline">
                                            <div class="custom-control custom-checkbox">
                                                <input type="checkbox" name="ex_trash" id="ex_trash" class="custom-control-input" value="Y" checked>
                                                <label class="custom-control-label" for="ex_trash" style="font-weight: 400;">휴지통제외</label>
                                            </div>
                                        </div>
                                    </div>
                                    <span>　</span>
                                    <div class="form-inline-inner form-check-box">
                                        <div class="form-inline">
                                            <div class="custom-control custom-checkbox">
                                                <input type="checkbox" name="ex_soldout" id="ex_soldout" class="custom-control-input" value="Y">
                                                <label class="custom-control-label" for="ex_soldout" style="font-weight: 400;">품절제외</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="form-group">
                                <label for="style_no">스타일넘버</label>
                                <div class="flax_box">
                                    <div class="form-inline-inner input-box w-100">
                                        <div class="form-inline inline_btn_box">
                                            <input type="text" class="form-control form-control-sm search-all search-enter ac-style-no w-100" name="style_no" value="">
                                            <a href="#" class="btn btn-sm btn-outline-primary sch-style_nos"><i class="bx bx-plus fs-16"></i></a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="form-group">
                                <label for="goods_nm">상품명</label>
                                <div class="flax_box">
                                    <input type='text' class="form-control form-control-sm ac-goods-nm search-enter" id="goods_nm" name='goods_nm' value=''>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-4">
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
                                            <select id="com_cd" name="com_cd" class="form-control form-control-sm select2-company"></select>
                                            <a href="#" class="btn btn-sm btn-outline-primary sch-company"><i class="bx bx-dots-horizontal-rounded fs-16"></i></a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="form-group">
                                <label for="f_sqty">판매수량</label>
                                <div class="form-inline inline_select_box">
                                    <div class="form-inline-inner input-box w-50 pr-1">
                                        <select id="formula_type" name="formula_type" class="form-control form-control-sm w-100">
                                            @foreach ($formula_types as $formula_type)
                                                <option value="{{ $formula_type }}">{{ $formula_type }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="form-inline-inner input-box w-50">
                                        <input type='text' class="form-control form-control-sm search-all search-enter" name='formula_val' id='formula_val' value='' style="width:100%;">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="form-group">
                                <label>출력자료수/정렬</label>
                                <div class="form-inline">
                                    <div class="form-inline-inner input_box" style="width:30%;">
                                        <select name="limit" class="form-control form-control-sm">
                                            <option value="100">100</option>
                                            <option value="500">500</option>
                                            <option value="1000">1000</option>
                                            <option value="2000">2000</option>
                                        </select>
                                    </div>
                                    <span class="text_line">/</span>
                                    <div class="form-inline-inner input_box" style="width:30%;">
                                        <select name="ord_field" class="form-control form-control-sm">
                                            <option value="goods_no">상품번호</option>
                                            <option value="now_qty">현재고</option>
                                            <option value="sale_qty">최근30일 판매수</option>
                                            <option value="expect_day">소진예상일</option>
                                            <option value="buy_ord_prd_no" selected>발주일시</option>
                                        </select>
                                    </div>
                                    <div class="form-inline-inner input_box sort_toggle_btn pr-2" style="width:30%;margin-left:2%;">
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
                    </div>
                </div>
            </div>
            <div class="resul_btn_wrap mb-3">
                <a href="#" id="search_sbtn" onclick="Search();" class="btn btn-sm btn-primary shadow-sm pl-2"><i class="fas fa-search fa-sm text-white-50"></i> 검색</a>
                <a href="#" onclick="add();" class="btn btn-sm btn-outline-primary shadow-sm pl-2"><i class="bx bx-plus fs-16"></i> 추가</a>
                <a href="#" onclick="gx.Download();" class="btn btn-sm btn-outline-primary shadow-sm pl-2"><i class="bx bx-download fs-16"></i> 엑셀다운로드</a>
                <div class="search_mode_wrap btn-group mr-2 mb-0 mb-sm-0"></div>
            </div>
        </div>
        <div id="filter-area" class="card shadow-none mb-0 ty2 last-card">
            <div class="card-body">
                <div class="card-title mb-3">
                    <div class="filter_wrap">
                        <div class="fl_box">
                            <h6 class="m-0 font-weight-bold">총 : <span id="gd-total" class="text-primary">0</span> 건 , </h6> 
                            <h6 class="m-0 font-weight-bold">총 재고수량 <span id="sum_buy_qty" class="text-primary">0</span>개 , </h6>
                            <h6 class="m-0 font-weight-bold">총 원가금액 <span id="sum_buy_cost" class="text-primary">0</span>원</h6>
                        </div>
                        <div class="fr_box">
                            <div class="custom-control custom-checkbox form-check-box pr-2" style="display:inline-block;">
                                <input type="checkbox" class="custom-control-input" name="goods_img" id="goods_img" value="Y" checked>
                                <label class="custom-control-label font-weight-light" for="goods_img">이미지출력</label>
                            </div>
                            <select id='change_buy_order_state' name='state' class="form-control form-control-sm" style='width:130px; display:inline'>
                                <option value=''>선택</option>
                                @foreach ($buy_order_states as $buy_order_state)
                                    <option value="{{ $buy_order_state->code_id }}">{{ $buy_order_state->code_val }}</option>
                                @endforeach
                            </select>
                            <a href="javascript:void(0);" onclick="changeState();" class="btn btn-sm btn-primary shadow-sm pl-2"><i class="bx bx-sync fs-16 mr-1"></i>발주상태변경</a>
                            <a href="#" onclick="del()" class="btn-sm btn btn-primary">발주삭제</a>
                            <span style="color: blue">※ 일평균판매수</span>는 <span style="color: red">최근 30일의 일평균 판매수량</span>
                        </div>
                    </div>
                </div>
                <div class="table-responsive">
                    <div id="div-gd" style="height:calc(100vh - 370px);width:100%;" class="ag-theme-balham"></div>
                </div>
            </div>
        </div>
    </form>
    <style>
        /* 전시카테고리 상품 이미지 사이즈 픽스 */
        .img {
            height:30px;
        }
    </style>
    <script type="text/javascript" charset="utf-8">

        // ag-grid set

        var columns= [
            {field:"chk", headerName: '', cellClass: 'hd-grid-code', headerCheckboxSelection: true, checkboxSelection: true, width: 28, sort: null, pinned:'left'},
            {field:"buy_ord_date" ,headerName:"발주일자",pinned:'left',width:72},
            {field:"buy_ord_no" ,headerName:"발주번호",pinned:'left',width:100},
            {field:"state" ,headerName:"발주상태",pinned:'left',width:72},
            {field:"com_nm",headerName:"업체",pinned:'left',width:100},
            {field:"opt_kind_nm" ,headerName:"품목",pinned:'left',width:72},
            {field:"brand_nm" ,headerName:"브랜드",pinned:'left',width:84},
            {field:"style_no" ,headerName:"스타일넘버",pinned:'left',width:85, 
                cellStyle:{'text-align':'center'}
            },
            {field:"org_nm" ,headerName:"원산지",pinned:'left',width:84},
            {headerName:"상품코드",
                children: [
                    {headerName: "번호", field: "goods_no", width: 46, pinned:'left', cellStyle:{'text-align': 'right'}},
                    {headerName: "보조", field: "goods_sub", width: 34, pinned:'left', cellStyle:{'text-align': 'center'}}
                ]
            },
            {field:"img", headerName:"이미지", type:'GoodsImageType', pinned:'left', width:50},
            {field:"img", headerName:"이미지_url", hide:true},
            {field:"goods_nm" , headerName:"상품명", type:"HeadGoodsNameType", width:220, pinned:'left'},
            {field:"sale_stat_cl" ,headerName:"상태",width:58,cellStyle:StyleGoodsState, pinned:'left'},
            {field:"goods_opt" ,headerName:"옵션",width:100,
                cellRenderer: function(params) {
                        return '<p>' + params.value + '</p>';
                }
            },
            {field:"now_qty",headerName:"현재고", width:60, type:'numberType'},
            {headerName:"발주",
                children: [
                    {headerName: "수량", field: "buy_qty", width: 60, cellStyle:{'text-align': 'right'}},
                    {headerName: "단가", field: "buy_unit_cost", width: 60, type:'currencyType'},
                    {headerName: "금액", field: "buy_cost", width: 60, type:'currencyType'}
                ], pinned: 'left'
            },
            {headerName:"판매",
                children: [
                    {headerName: "{{$month1}} 월", field: "sale_qty1", width:60, type:'currencyType'},
                    {headerName: "{{$month2}} 월", field: "sale_qty2", width:60, type:'currencyType'},
                    {headerName: "{{$month3}} 월", field: "sale_qty3", width:60, type:'currencyType'},
                    {headerName: "최근30일", field: "sale_qty", width:84, type:'currencyType'},
                    {headerName: "일평균판매수", field: "avg_qty", wdith:84,type:'percentType'},
                    {field:"expect_day",headerName:"소진예상일", width:84,cellStyle:{"color": "red", "text-align": "right"}},
                ]
            },
            {field:"max_wonga",headerName:"최대원가", width:72,type:'currencyType'},
            {field:"avg_wonga",headerName:"평균원가", width:72,type:'currencyType'},
            {field:"tot_wonga",headerName:"총원가", width:60,type:'currencyType'},
            {field:"last_input_date",headerName:"최종입고일자"},
            {field:"price",headerName:"현재판매가",type:'currencyType'},
            {field:"margin_amt",headerName:"마진",type:'currencyType'},
            {field:"margin_rate",headerName:"마진율(%)",type:'percentType'},
            // {headerName:"테스트등급", // 16
            //     children: [
            //         {field:"group_16_price",headerName:"판매가",type:'currencyType'},
            //         {field:"group_16_ratio",headerName:"마진율(0%)",type:'percentType'},
            //         {field:"group_16_dc_ratio",headerName:"할인율",type:'percentType'},
            //     ]
            // },
            {headerName:"A 등급 회원 (원가 + 14%마진)", // 5
                children: [
                    {field:"group_5_price",headerName:"판매가",type:'currencyType'},
                    {field:"group_5_ratio",headerName:"마진율(14%)",type:'percentType'},
                    {field:"group_5_dc_ratio",headerName:"할인율",type:'percentType'},
                ]
            },
            {headerName:"B 등급 회원 (C등급 + 511 서브딜러)", // 6
                children: [
                    {field:"group_6_price",headerName:"판매가",type:'currencyType'},
                    {field:"group_6_ratio",headerName:"마진율(18%)",type:'percentType'},
                    {field:"group_6_dc_ratio",headerName:"할인율",type:'percentType'},
                ]
            },
            {headerName:"C 등급 회원 (원가 + 18%마진)", // 7
                children: [
                    {field:"group_7_price",headerName:"판매가",type:'currencyType'},
                    {field:"group_7_ratio",headerName:"마진율(18%)",type:'percentType'},
                    {field:"group_7_dc_ratio",headerName:"할인율",type:'percentType'},
                ]
            },
            {width: "auto"}
        ];

        const pApp = new App('', {
            gridId: "#div-gd",
        });
        let gx;

        $(document).ready(function() {
            pApp.ResizeGrid(275);
            pApp.BindSearchEnter();
            let gridDiv = document.querySelector(pApp.options.gridId);
            let options = {};
            gx = new HDGrid(gridDiv, columns, options);
            Search();

            $("#img").click(function() {
                gx.gridOptions.columnApi.setColumnVisible('img',$("#img").is(":checked"));
            });
        });

        // logics

        const strNumToPrice = (price) => {
            return price.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ',');
        };

        function Search() {
            let data = $("form[name=search]").serialize();
            gx.Request('/head/stock/stk10/search', data, 1, (d) => {
                const { sum_buy_qty, sum_buy_cost } = d.sum_buy_info;
                const [ sum_buy_qty_dom, sum_buy_cost_dom ] 
                    = [ document.querySelector('#sum_buy_qty'), document.querySelector('#sum_buy_cost') ];
                sum_buy_qty_dom.innerText = sum_buy_qty;
                sum_buy_cost_dom.innerText = strNumToPrice(sum_buy_cost);
            });
        };

        const add = () => {
            const url = '/head/stock/stk10/buy';
            const [ width, height ] = [ 1700 , 1000 ];
            const pop = window.open(url,"_blank","toolbar=no,scrollbars=no,resizable=yes,status=yes,top=100,left=100,width="+width+",height="+height);
        };

        const changeState = () => {
            let arr;
            let state;
            let buy_ord_prd_nos = [];
            const select = document.querySelector('#change_buy_order_state');
            if (select.value) {
                state = select.value;
            } else {
                alert('발주상태를 선택 해 주십시오.')
                return false;
            };
            arr = gx.getSelectedRows();
            if(arr.length < 1) return alert('상태를 변경할 발주건을 선택해주세요.');
            if (confirm('발주 상태를 변경하시겠습니까?')) {
                if (Array.isArray(arr) && !(arr.length > 0)) {
                    alert('항목을 선택 해 주십시오.')
                    select.focus();
                    return false;
                } else {
                    arr.map((obj, idx) => {
                        obj.hasOwnProperty('buy_ord_prd_no')
                            ? buy_ord_prd_nos[idx] = obj.buy_ord_prd_no
                            : null;
                    });
                    axios({
                        url: '/head/stock/stk10/update',
                        method: 'put',
                        data: { state : state, buy_ord_prd_nos : buy_ord_prd_nos }
                    }).then((response) => {
                        if (response.status == 200) Search();
                    }).catch((error) => {
                        console.log(error.response.data);
                    });
                };
            };
        };

        const del = () => {
            let arr;
            let buy_ord_prd_nos = [];
            if (confirm('삭제하시겠습니까?')) {
                arr = gx.getSelectedRows();
                if (Array.isArray(arr) && !(arr.length > 0)) {
                    alert('항목을 선택 해 주십시오.')
                    return false;
                } else {
                    arr.map((obj, idx) => {
                        obj.hasOwnProperty('buy_ord_prd_no')
                            ? buy_ord_prd_nos[idx] = obj.buy_ord_prd_no
                            : null;
                    });
                    axios({
                        url: '/head/stock/stk10/delete',
                        method: 'delete',
                        data: { buy_ord_prd_nos : buy_ord_prd_nos }
                    }).then((response) => {
                        if (response.status == 200) Search();
                    }).catch((error) => {
                        console.log(error.response.data);
                    });
                }
            };
        };

        $("#goods_img").click(function() {
            gx.gridOptions.columnApi.setColumnVisible("img", $("#goods_img").is(":checked"));
        });

    </script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/lodash.js/4.17.4/lodash.min.js"></script>
<script src="https://d3js.org/d3.v4.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-sparklines/2.1.2/jquery.sparkline.min.js"></script>        
@stop

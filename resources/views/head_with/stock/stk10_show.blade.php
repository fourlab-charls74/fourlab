@extends('head_with.layouts.layout-nav')
@section('title','발주')
@section('content')
<div class="py-3 px-sm-3">
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
                        <a href="#" onclick="add()" class="btn btn-sm btn-primary shadow-sm pl-2"><i class="bx bx-save mr-1"></i>저장</a>
                        <a href="#" onclick="gx.Download();" class="btn btn-sm btn-outline-primary shadow-sm pl-2"><i class="bx bx-download fs-16"></i> 엑셀다운로드</a>
                        <div id="search-btn-collapse" class="btn-group mb-0 mb-sm-0"></div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-4">
                            <div class="form-group">
                                <label for="item">품목</label>
                                <div class="flax_box">
                                    <select id="item" name="item" class="form-control form-control-sm">
                                        <option value="">전체</option>
                                        @foreach ($items as $item)
                                            <option value="{{ $item->cd }}">{{ $item->val }}</option>
                                        @endforeach
                                    </select>
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
                        <div class="col-lg-4">
                            <div class="form-group">
                                <label for="">상품상태</label>
                                <div class="form-inline">
                                    <div class="form-inline-inner input-box w-50 pr-2">
                                        <select name="goods_stat" class="form-control form-control-sm w-100">
                                            <option value=''>전체</option>
                                            @foreach ($goods_stats as $goods_stat)
                                                @if ($goods_stat->code_id == '40')
                                                <option value='{{ $goods_stat->code_id }}' selected>{{ $goods_stat->code_val }}</option>
                                                @else
                                                <option value='{{ $goods_stat->code_id }}'>{{ $goods_stat->code_val }}</option>
                                                @endif
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
                    </div>
                    <div class="row">
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
                        <div class="col-lg-4">
                            <div class="form-group">
                                <label for="name">업체</label>
                                <div class="form-inline inline_select_box">
                                    <div class="form-inline-inner input-box w-25 pr-1">
                                        <select id="com_type" name="com_type" class="form-control form-control-sm w-100">
                                            <option value="">전체</option>
                                            @foreach ($com_types as $com_type)
                                                @if ($com_type->code_id == '1')
                                                <option value="{{ $com_type->code_id }}" selected>{{ $com_type->code_val }}</option>
                                                @else
                                                <option value="{{ $com_type->code_id }}">{{ $com_type->code_val }}</option>
                                                @endif
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
                    </div>
                    <div class="row">
                        <div class="col-lg-4">
                            <div class="form-group">
                                <label for="f_sqty">판매수량</label>
                                <div class="form-inline inline_select_box">
                                    <div class="form-inline-inner input-box w-25 pr-1">
                                        <select id="formula_type" name="formula_type" class="form-control form-control-sm w-100">
                                            @foreach ($formula_types as $formula_type)
                                                <option value="{{ $formula_type }}">{{ $formula_type }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="form-inline-inner input-box w-25">
                                        <input type='text' class="form-control form-control-sm search-all search-enter" name='formula_val' id='formula_val' value='' style="width:100%;">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="form-group">
                                <label>출력자료수/정렬</label>
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
                                    <div class="form-inline-inner input_box" style="width:24%;">
                                        <select name="ord_field" class="form-control form-control-sm">
                                            <option value="goods_no">상품번호</option>
                                            <option value="now_qty">현재고</option>
                                            <option value="sale_qty">최근30일 판매수</option>
                                            <option value="expect_day" selected>소진예상일</option>
                                            <option value="buy_ord_prd_no">발주일시</option>
                                        </select>
                                    </div>
                                    <div class="form-inline-inner input_box sort_toggle_btn pr-2" style="width:24%;margin-left:2%;">
                                        <div class="btn-group" role="group">
                                            <label class="btn btn-secondary" for="sort_desc" data-toggle="tooltip" data-placement="top" title="" data-original-title="내림차순"><i class="bx bx-sort-down"></i></label>
                                            <label class="btn btn-primary primary" for="sort_asc" data-toggle="tooltip" data-placement="top" title="" data-original-title="오름차순"><i class="bx bx-sort-up"></i></label>
                                        </div>
                                        <input type="radio" name="ord" id="sort_desc" value="desc">
                                        <input type="radio" name="ord" id="sort_asc" value="asc" checked>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="resul_btn_wrap mb-3">
                <a href="#" id="search_sbtn" onclick="Search();" class="btn btn-sm btn-primary shadow-sm pl-2"><i class="fas fa-search fa-sm text-white-50"></i> 검색</a>
                <a href="#" onclick="add()" class="btn btn-sm btn-primary shadow-sm pl-2"><i class="bx bx-save mr-1"></i>저장</a>
                <a href="#" onclick="gx.Download();" class="btn btn-sm btn-outline-primary shadow-sm pl-2"><i class="bx bx-download fs-16"></i> 엑셀다운로드</a>
                <div class="search_mode_wrap btn-group mr-2 mb-0 mb-sm-0"></div>
            </div>
        </div>
        <div id="filter-area" class="card shadow-none mb-0 ty2 last-card">
            <div class="card-body">
                <div class="card-title mb-3">
                    <div class="filter_wrap">
                        <div class="fl_box">
                            <h6 class="m-0 font-weight-bold">총 : <span id="gd-total" class="text-primary">0</span> 건</h6> 
                        </div>
                        <div class="fr_box">
                            <div class="flax_box">
                                <div class="form-inline form-check-box">
                                    <div class="custom-control custom-checkbox mr-1">
                                        <input type="checkbox" name="apply_avg_wonga" id="apply_avg_wonga" class="custom-control-input" value="Y" checked>
                                        <label class="custom-control-label" for="apply_avg_wonga">발주단가를 평균원가로 적용</label>
                                    </div>
                                </div>
                                <span style="color: blue">※ 일평균판매수</span>는 <span style="color: red">최근 30일의 일평균 판매수량</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="table-responsive">
                    <div id="div-gd" style="height:calc(100vh - 370px);width:100%;" class="ag-theme-balham"></div>
                </div>
            </div>
        </div>
    </form>
</div>
    <script type="text/javascript" charset="utf-8">
        // ag-grid set field

        const YELLOW = { backgroundColor: '#ffff99' };

        var columns= [
            {field:"chk", headerName: '', cellClass: 'hd-grid-code', headerCheckboxSelection: true, checkboxSelection: true, width: 28, sort: null, pinned:'left'},
            {field:"com_nm",headerName:"업체",pinned:'left',width:120},
            {field:"opt_kind_nm" ,headerName:"품목",pinned:'left',width:84},
            {field:"brand_nm" ,headerName:"브랜드",pinned:'left',width:84},
            {field:"style_no" ,headerName:"스타일넘버",pinned:'left',width:130},
            {field:"org_nm" ,headerName:"원산지",pinned:'left',width:72},            
            // {field:"img" , headerName:"이미지",
            //     cellRenderer: function(params) {
            //         if (params.value !== undefined && params.data.img != "") {
            //             return '<img src="{{config('shop.image_svr')}}' + params.data.img + '"/>';
            //         }
            //     }
            // },
            {headerName:"상품코드",
                children: [
                    {headerName: "번호", field: "goods_no", width: 46, pinned:'left', cellStyle:{'text-align': 'right'}},
                    {headerName: "보조", field: "goods_sub", width: 34, pinned:'left', cellStyle:{'text-align': 'center'}}
                ]
            },
            {field:"goods_nm" , headerName:"상품명", type:"HeadGoodsNameType", width:220, pinned:'left'},
            {field:"sale_stat_cl" ,headerName:"상태",width:58,cellStyle:StyleGoodsState, pinned:'left'},
            {field:"goods_opt" ,headerName:"옵션",width:100,
                cellRenderer: function(params) {
                        return '<p>' + params.value + '</p>';
                }
            },
            {field:"wqty",headerName:"현재고", width:60, type:'numberType'},
            {headerName:"발주",
                children: [
                    {headerName: "예상수량", field: "exp_buy_qty", width: 72, cellStyle:{'text-align': 'right'},
                        valueFormatter: (params) => Math.ceil(params.value)
                    },
                    {headerName: "수량", field: "qty", width: 60, cellStyle:{'text-align': 'right'}, editable: true,
                        type:'currencyType', cellStyle: (params) => params.colDef.editable == true ? YELLOW : null
                    },
                    {headerName: "단가", field: "buy_unit_cost", width: 60, type:'currencyType', editable: true,
                        cellStyle: (params) => params.colDef.editable == true ? YELLOW : null
                    },
                    {headerName: "금액", field: "buy_cost", width: 60, type:'currencyType'}
                ], pinned: 'left'
            },
            {headerName:"판매",
                children: [
                    {headerName: "{{$month1}} 월", field: "sale_qty1", width:60,type:'currencyType'},
                    {headerName: "{{$month2}} 월", field: "sale_qty2", width:60,type:'currencyType'},
                    {headerName: "{{$month3}} 월", field: "sale_qty3", width:60,type:'currencyType'},
                    {headerName: "최근30일", field: "sale_qty", width:84,type:'currencyType'},
                    {headerName: "일평균판매수", field: "avg_qty", width:96, type:'percentType'},
                    {field:"expect_day",headerName:"소진예상일", width:84,cellStyle:{"color": "red", "text-align": "right"}},
                ]
            },
            {field:"max_wonga",headerName:"최대원가", width:72,type:'currencyType'},
            {field:"avg_wonga",headerName:"평균원가", width:72,type:'currencyType'},
            {field:"tot_wonga",headerName:"총원가", width:60,type:'currencyType'},
            {field:"last_input_date",headerName:"최종입고일자", width:96, cellStyle:{"text-align": "center"}},
            {field:"price",headerName:"현재판매가", width:84,type:'currencyType'},
            {field:"margin_amt",headerName:"마진", width:60,type:'currencyType'},
            {field:"margin_rate",headerName:"마진율(%)", width:84,type:'percentType'},
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
        ];

        // logics

        const pApp = new App('', {
            gridId: "#div-gd",
        });
        let gx;

        $(document).ready(function() {
            pApp.ResizeGrid(275);
            pApp.BindSearchEnter();
            let gridDiv = document.querySelector(pApp.options.gridId);
            let options = {
                onCellValueChanged: params => evtAfterEdit(params),
            };
            gx = new HDGrid(gridDiv, columns,options);
            Search();

            $("#img").click(function() {
                gx.gridOptions.columnApi.setColumnVisible('img',$("#img").is(":checked"));
            });

        });

        const evtAfterEdit = (params) => { // edit 가능한 셀 수정시 계산하고 고정 row를 업데이트합니다.
            if (params.oldValue !== params.newValue) {
                if (params.colDef.field == 'qty' || params.colDef.field == 'buy_unit_cost') {
                    const row = params.data;
                    row.buy_cost = parseFloat(row.qty) * row.buy_unit_cost;
                    gx.gridOptions.api.applyTransaction({ update: [row] });
                };
            }
        };
        
        const strNumToPrice = (price) => {
            return price.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ',');
        };

        function Search() {
            $('[name=search]').val(10);
            let data = $('form[name="search"]').serialize();
            gx.Request('/head/stock/stk10/buy/search', data, 1, (data) => {
                // console.log(data);
            });
        };

        const add = () => {
            let arr;
            if (confirm('저장하시겠습니까?')) {
                arr = gx.getSelectedRows();
                if (Array.isArray(arr) && !(arr.length > 0)) {
                    alert('항목을 선택 해 주십시오.')
                    return false;
                } else {
                    let data_string = "";
                    const apply_avg_wonga = document.querySelector('#apply_avg_wonga');
                    let apply_avg_wonga_checked = ( apply_avg_wonga && apply_avg_wonga.checked ) ? true : false;
                    arr.map((obj) => {
                        const goods_no = obj.hasOwnProperty('goods_no') ? obj.goods_no : "";
                        const goods_sub = obj.hasOwnProperty('goods_sub') ? obj.goods_sub : "";
                        const goods_opt = obj.hasOwnProperty('goods_opt') ? obj.goods_opt : "";
                        const qty = obj.hasOwnProperty('qty') ? obj.qty : "";
                        const opt_kind_nm = obj.hasOwnProperty('opt_kind_nm') ? obj.opt_kind_nm : "";

                        // console.log(opt_kind_nm);
                        let buy_unit_cost = obj.hasOwnProperty('buy_unit_cost') ? obj.buy_unit_cost : "";
                        obj.hasOwnProperty('avg_wonga') && apply_avg_wonga_checked
                            ? buy_unit_cost = obj.avg_wonga
                            : null;
                        
                        data_string += goods_no + "\t" +  goods_sub + "\t" + goods_opt + "\t" + qty + "\t" + buy_unit_cost + "\t" + opt_kind_nm +"\n";
                    });
                    axios({
                        url: '/head/stock/stk10/buy/add',
                        method: 'post',
                        data: { data : data_string }
                    }).then((response) => {
                        console.log(response);
                        if (response.status == 201) {
                            window.opener.Search();
                            window.close();
                        }
                    }).catch((error) => {
                        console.log(error);
                    });
                };
            };
        };

    </script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/lodash.js/4.17.4/lodash.min.js"></script>
<script src="https://d3js.org/d3.v4.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-sparklines/2.1.2/jquery.sparkline.min.js"></script>        
@stop

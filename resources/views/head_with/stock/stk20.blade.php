@extends('head_with.layouts.layout')
@section('title','상품 판매율')
@section('content')
    <div class="page_tit">
        <h3 class="d-inline-flex">상품 판매율</h3>
        <div class="d-inline-flex location">
            <span class="home"></span>
            <span>/ 재고</span>
        </div>
    </div>
    <style>
        .select2.select2-container .select2-selection {
            border: 1px solid rgb(210, 210, 210);
            /*-webkit-border-radius: 3px;*/
            /*-moz-border-radius: 3px;*/
            /*border-radius: 3px;*/
            /*outline: none !important;*/
            /*transition: all .15s ease-in-out;*/
        }

        ::placeholder {
            font-size: 13px;
            font-family: "Montserrat","Noto Sans KR",'mg', Dotum,"돋움",Helvetica,AppleSDGothicNeo,sans-serif;
            font-weight: 300;
            padding: 0px 2px 1px;
            color: black;
        }
    </style>
    <script>
        //멀티 셀렉트 박스2
        $(document).ready(function() {
            $('.multi_select').select2({
                placeholder :'전체',
                multiple: true,
                width : "100%",
                closeOnSelect: false,
            });
        });
    </script>

    <form method="get" name="search">
        <div id="search-area" class="search_cum_form">
            <div class="card mb-3">
                <div class="d-flex card-header justify-content-between">
                    <h4>검색</h4>
                    <div>
                        <a href="#" id="search_sbtn" onclick="return Search();" class="btn btn-sm btn-primary shadow-sm pl-2"><i class="fas fa-search fa-sm text-white-50"></i> 조회</a>
                        <a href="#" onclick="gx.Download();" class="btn btn-sm btn-outline-primary shadow-sm pl-2"><i class="bx bx-download fs-16"></i> 자료받기</a>
                        <div id="search-btn-collapse" class="btn-group mb-0 mb-sm-0"></div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-4 inner-td">
                            <div class="form-group">
                                <label for="">상품상태</label>
                                <div class="form-inline">
                                    <div class="form-inline-inner input-box w-75 pr-2">
                                        <select name="goods_stat[]" class="form-control form-control-sm multi_select w-100" multiple>
                                            <option value=''>전체</option>
                                            @foreach ($goods_stats as $goods_stat)
                                                <option value='{{ $goods_stat->code_id }}'>{{ $goods_stat->code_val }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="form-inline-inner form-check-box">
                                        <div class="form-inline">
                                            <div class="custom-control custom-checkbox">
                                                <input type="checkbox" name="ex_trash" id="ex_trash" class="custom-control-input" value="1" checked>
                                                <label class="custom-control-label" for="ex_trash" style="font-weight: 400;">휴지통제외</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4 inner-td">
                            <div class="form-group">
                                <label for="style_no">스타일넘버/상품코드</label>
                                <div class="form-inline">
                                    <div class="form-inline-inner input_box">
                                        <input type='text' class="form-control form-control-sm ac-style-no search-enter" name='style_no' id="style_no" value="">
                                    </div>
                                    <span class="text_line">/</span>
                                    <div class="form-inline-inner input-box" style="width:47%">
                                        <div class="form-inline-inner inline_btn_box">
                                            <input type='text' class="form-control form-control-sm w-100 search-enter" name='goods_no' id='goods_no' value=''>
                                            <a href="#" class="btn btn-sm btn-outline-primary sch-goods_nos"><i class="bx bx-dots-horizontal-rounded fs-16"></i></a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4 inner-td">
                            <div class="form-group">
                                <label for="goods_nm">상품명</label>
                                <div class="flax_box">
                                    <input type='text' class="form-control form-control-sm ac-goods-nm search-enter" id="goods_nm" name='goods_nm' value=''>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
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
											<select id="com_cd" name="com_cd" class="form-control form-control-sm select2-company" style="width:100%;"></select>
											<a href="#" class="btn btn-sm btn-outline-primary sch-company"><i class="bx bx-dots-horizontal-rounded fs-16"></i></a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4 inner-td">
                            <div class="form-group">
                                <label for="goods_stat">재고구분</label>
                                <div class="flax_box">
                                    <select name='is_unlimited' class="form-control form-control-sm">
                                        <option value=''>전체</option>
                                        @foreach ($is_unlimiteds as $is_unlimited)
                                            <option value='{{ $is_unlimited->code_id }}'>{{ $is_unlimited->code_val }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4 inner-td">
                            <div class="form-group">
                                <label for="goods_stat">보유재고수</label>
                                <div class="form-inline">
                                    <div class="form-inline-inner input_box">
                                        <div class="form-group">
                                            <input type='text' class="form-control form-control-sm" name='wqty_l' value=''>
                                        </div>
                                    </div>
                                    <span class="text_line">~</span>
                                    <div class="form-inline-inner input_box">
                                        <div class="form-group">
                                            <input type='text' class="form-control form-control-sm" name='wqty_h' value=''>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-4 inner-td">
                            <div class="form-group">
                                <label for="goods_stat">품목</label>
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
                        <div class="col-lg-4 inner-td">
                            <div class="form-group">
                                <label for="brand_cd">브랜드</label>
                                <div class="form-inline inline_btn_box">
                                    <select id="brand_cd" name="brand_cd" class="form-control form-control-sm select2-brand"></select>
                                    <a href="#" class="btn btn-sm btn-outline-primary sch-brand"><i class="bx bx-dots-horizontal-rounded fs-16"></i></a>
                                </div>
                            </div>
                        </div>
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
                                    <div class="form-inline-inner input_box" style="width:24%;">
                                        <select name="ord_field" class="form-control form-control-sm">
                                            <option value="g.goods_no" selected>상품코드</option>
                                            <option value="g.goods_nm">상품명</option>
                                            <option value="s.wqty" >보유재고</option>
                                            <option value="gsr.sale_qty" >판매수</option>
                                            <option value="expect_day">소진예상일</option>
                                            <option value="gs.totalwonga" >현재고총원가</option>
                                            <option value="gs.req_date" > 최근출시일자</option>
                                        </select>
                                    </div>
                                    <div class="form-inline-inner input_box sort_toggle_btn pr-2" style="width:24%;margin-left:2%;">
                                        <div class="btn-group" role="group">
                                            <label class="btn btn-primary primary" for="sort_desc" data-toggle="tooltip" data-placement="top" title="" data-original-title="내림차순"><i class="bx bx-sort-down"></i></label>
                                            <label class="btn btn-secondary" for="sort_asc" data-toggle="tooltip" data-placement="top" title="" data-original-title="오름차순"><i class="bx bx-sort-up"></i></label>
                                        </div>
                                        <input type="radio" name="ord" id="sort_desc" value="desc" checked="">
                                        <input type="radio" name="ord" id="sort_asc" value="asc">
                                    </div>
                                    <div class="form-inline-inner form-check-box">
                                        <div class="form-inline">
                                            <div class="custom-control custom-checkbox">
                                                <input type="checkbox" name="ex_soldout" id="ex_soldout" class="custom-control-input" value="1" checked>
                                                <label class="custom-control-label" for="ex_soldout" style="font-weight: 400;">품절제외</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="resul_btn_wrap mb-3">
                <a href="#" id="search_sbtn" onclick="return Search();" class="btn btn-sm btn-primary shadow-sm pl-2"><i class="fas fa-search fa-sm text-white-50"></i> 조회</a>
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
                            <div class="fl_inner_box">
                                <div class="box">
                                    <div class="custom-control custom-checkbox form-check-box pr-2" style="display:inline-block; padding-left: 32px;">
                                        <input type="checkbox" class="custom-control-input" name="img" id="img" value="Y">
                                    <label class="custom-control-label font-weight-light" for="img">이미지출력</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="fr_box">
                            <div class="form-inline">
                                <span class="text_line" style="width:14%">기준기간 : </span>
                                <div class="docs-datepicker form-inline-inner input_box" style="width:40%">
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
                                <div class="docs-datepicker form-inline-inner input_box" style="width:40%">
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
                </div>
                <div class="table-responsive">
                    <div id="div-gd" style="height:calc(100vh - 370px);width:100%;" class="ag-theme-balham"></div>
                </div>
            </div>
        </div>
    </form>
    <script language="javascript">
		var columns= [
            {field:"goods_no" ,headerName:"상품코드",pinned:'left',width:58, cellStyle:{'text-align':'center'}},
            {field:"style_no" ,headerName:"스타일넘버",pinned:'left',width:70, cellStyle:{'text-align':'center'}},
            {field:"goods_nm" ,headerName:"상품명",pinned:'left', type:"HeadGoodsNameType", width:200},
            {field:"img2",headerName:"img2",hide:true},
            {field:"img" , headerName:"이미지", width:46,
                cellRenderer: function(params) {
                    if (params.value !== undefined && params.data.img != "") {
                        return '<img src="{{config('shop.image_svr')}}' + params.data.img + '" style="height:40px;" />';
                    }
                }
            },
            {field:"head_desc" ,headerName:"상단홍보글", width:84},
            {field:"goods_nm_eng" ,headerName:"상품명(영문)", width:96},
            {field:"opt_kind_nm" ,headerName:"품목", width:58},
            {field:"brand_nm" ,headerName:"브랜드", width:84 },
            {field:"style_no" ,headerName:"스타일넘버", width:70, cellStyle:{'text-align':'center'}},
            {field:"goods_type_nm",headerName:"상품구분",width:70, 
                cellStyle: function(params) {
                    var state = { 위탁: "#ff0000", 매입: "#669900", 해외: "#0000FF" };
                    if (params.value !== undefined) {
                        if (state[params.value]) {
                            var color = state[params.value];
                            return { color: color, "text-align": "center" };
                        }
                    }
                }
            },
            {field:"is_unlimited_nm",headerName:"재고구분",width:70},
            {field:"com_nm",headerName:"업체",width:84},
            {field:"sale_stat_cl_nm" ,headerName:"상태",width:84,cellStyle:StyleGoodsState},
            {field:"wonga" ,headerName:"원가", type: 'currencyType', width:60},
            {field:"goods_opt" ,headerName:"옵션",width:150,
                cellRenderer: function(params) {
                        return '<a href="#" onclick="return openHeadStock(' + params.data.goods_no + ',\'' + params.value +'\');">' + params.value + '</a>';
                }
            },
            {field:"good_qty",headerName:"온라인재고",type:'numberType', width:70},
            {field:"wqty",headerName:"보유재고",type:'numberType', width:58},
            {headerName:"판매수",
                children: [
                    {headerName: "{{$month1}} 월", field: "sale_qty1",type:'currencyType',width:58},
                    {headerName: "{{$month2}} 월", field: "sale_qty2",type:'currencyType',width:58},
                    {headerName: "{{$month3}} 월", field: "sale_qty3",type:'currencyType',width:58},
                    {headerName: "최근30일", field: "sale_qty",type:'currencyType', width:70},
                    {headerName: "할인판매수", field: "dc_sale_qty",type:'currencyType'},
                    {headerName: "소매", field: "sale_ord_type_12_qty",type:'currencyType'},
                    {headerName: "도매", field: "sale_ord_type_13_qty",type:'currencyType'},
                    {headerName: "쿠팡(로켓)", field: "sale_ord_type_roket_qty",type:'currencyType'},
                    {headerName: "납품", field: "sale_ord_type_17_qty",type:'currencyType'},
                    {field:"avg_qty",headerName:"일평균판매수",type:'percentType'},
                    {headerName: "수량", field: "sale_sum_qty",type:'currencyType'},
                    {headerName: "금액", field: "sale_sum_amt",type:'currencyType'},
                    {headerName: "판매상위20%(금액)", field: "top20p_sale_amt",cellClass:'hd-grid-code',},
                ]
            },
            {field:"expect_day",headerName:"소진예상일",type:'percentType'},
            {headerName:"클레임",
                children: [
                    {headerName: "수량", field: "clm_qty",type:'currencyType'},
                    {headerName: "상품불량", field: "clm_5_qty",type:'currencyType'},
                    {headerName: "품절", field: "clm_4_qty",type:'currencyType'},
                ]
            },

            {field:"price",headerName:"판매가",type:'currencyType',filter: 'agNumberColumnFilter' ,filter:true},
            {field:"wonga",headerName: "원가",type:'currencyType'},
            {field:"margin_amt",headerName:"마진",type:'currencyType'},
            {field:"margin_rate",headerName:"마진율",type:'percentType'},
            {field:"totalwonga",headerName:"현재고총원가",type:'currencyType'},
            {field:"tot_sales",headerName:"예상총매출액",type:'currencyType'},
            {field:"tot_margin",headerName:"예상총마진",type:'currencyType'},
            {field:"goods_sh",headerName:"시중가격",type:'currencyType'},
            {field:"new_product_day",headerName:"신상품적용일",type:'DayType'},
            {field:"max_ord_date",headerName:"최종판매일자",type:'DayType'},
            {field:"maxinputdate",headerName:"최종입고일자",type:'DayType'},
            {field:"stock_qty",headerName:"입고수량",type:'numberType'},
            {field:"stock_buy_qty",headerName:"발주수량",type:'numberType'},
            {field:"req_date",headerName:"최근출시일자",type:'DayType'},
            {field:"nvl",headerName:" "}

            ];

			function EditQty(params){
				if (params.oldValue !== params.newValue) {
					params.data[params.colDef.field + '_chg_yn'] = 'Y';
					var rowNode = params.node;
					rowNode.setSelected(true);
					gx.gridOptions.api.redrawRows({rowNodes:[rowNode]});
					//gridOptions.api.refreshCells({rowNodes:[rowNode]});
					gx.gridOptions.api.setFocusedCell(rowNode.rowIndex, params.colDef.field);
				}
			}

			function WEditQty(params){
				if (params.oldValue !== params.newValue) {
					params.data[params.colDef.field + '_chg_yn'] = 'Y';
					var rowNode = params.node;
					rowNode.setSelected(true);

					// 보유재고 수정시 온라인 재고도 자동 수정
					params.data.edit_good_qty = params.data.edit_good_qty  * 1 + (params.newValue - params.oldValue);
					gx.gridOptions.api.updateRowData({ update: [params.data]});

					gx.gridOptions.api.redrawRows({rowNodes:[rowNode]});
					//gridOptions.api.refreshCells({rowNodes:[rowNode]});
					gx.gridOptions.api.setFocusedCell(rowNode.rowIndex, params.colDef.field);
				}
			}
    </script>
    {{-- <script type="text/javascript" charset="utf-8">
            const pApp = new App('',{
                gridId:"#div-gd",
            });
            let gx;
            $(document).ready(function() {
                pApp.ResizeGrid(275);
                pApp.BindSearchEnter();
                let gridDiv = document.querySelector(pApp.options.gridId);
                gx = new HDGrid(gridDiv, columns);
                
                $("#img").click(function() {
                    gx.gridOptions.columnApi.setColumnVisible('img',$("#img").is(":checked"));
                });

                Search();
            });
            function Search() {
                let data = $('form[name="search"]').serialize();
                gx.Aggregation({
                    "sum":"top",
                });
                gx.Request('/head/stock/stk20/search', data,1);
            }
        </script> --}}


<script type="text/javascript" charset="utf-8">
    const pApp = new App('', {
        gridId: "#div-gd",
    });
    let gx;

    const CELL_DIMENSION_SIZE = 40;

    $(document).ready(function() {
        pApp.ResizeGrid(275);
        pApp.BindSearchEnter();
        let gridDiv = document.querySelector(pApp.options.gridId);
        let options = {
            autoHeight:false,
            rowHeight:CELL_DIMENSION_SIZE,
            getRowHeight:100
        };
        gx = new HDGrid(gridDiv, columns,options);
        //console.log(gx.gridOptions);
        Search();

        gx.gridOptions.columnApi.setColumnVisible('img',$("#img").is(":checked"));

        $("#img").click(function() {
             gx.gridOptions.columnApi.setColumnVisible('img',$("#img").is(":checked"));
        });
    });

    function Search() {
        $('[name=search]').val(10);
        let data = $('form[name="search"]').serialize();
        gx.Request('/head/stock/stk20/search', data, 1, function(data) {});
        // console.log(data);
    }



</script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/lodash.js/4.17.4/lodash.min.js"></script>
<script src="https://d3js.org/d3.v4.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-sparklines/2.1.2/jquery.sparkline.min.js"></script>        
@stop

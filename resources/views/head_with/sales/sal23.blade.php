@extends('head_with.layouts.layout')
@section('title','상품별 주문 통계')
@section('content')
    <div class="page_tit">
        <h3 class="d-inline-flex">상품별 주문 통계</h3>
        <div class="d-inline-flex location">
            <span class="home"></span>
            <span>/ 상품별 주문 통계</span>
        </div>
    </div>
    <form method="get" name="search" id="search">
        <div id="search-area" class="search_cum_form">
            <div class="card mb-3">
                <div class="d-flex card-header justify-content-between">
                    <h4>검색</h4>
                    <div>
                        <a href="#" id="search_sbtn" onclick="return Search();" class="btn btn-sm btn-primary shadow-sm pl-2"><i class="fas fa-search fa-sm text-white-50"></i> 조회</a>
                        <div id="search-btn-collapse" class="btn-group mb-0 mb-sm-0"></div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-4 inner-td">
                            <div class="form-group">
                                <label for="formrow-firstname-input">일자</label>
                                <div class="form-inline">
                                    <div class="docs-datepicker form-inline-inner input_box">
                                        <div class="input-group">
                                            <input type="text" class="form-control form-control-sm docs-date" name="sdate" value="{{ $sdate }}" autocomplete="off">
                                            <div class="input-group-append">
                                                <button type="button" class="btn btn-outline-secondary docs-datepicker-trigger p-0 pl-2 pr-2" disable="">
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
                        <div class="col-lg-4 inner-td">
                            <div class="form-group">
                                <label for="style_no">스타일넘버/온라인코드</label>
                                <div class="form-inline">
                                <div class="form-inline-inner input_box">
                                    <input type='text' class="form-control form-control-sm ac-style-no search-enter" name='style_no' id="style_no" value="">
                                </div>
                                <span class="text_line">/</span>
                                <div class="form-inline-inner input-box" style="width:47%">
                                    <div class="form-inline-inner inline_btn_box">
                                        <input type='text' class="form-control form-control-sm w-100" name='goods_no' id='goods_no' value=''>
                                        <a href="#" class="btn btn-sm btn-outline-primary sch-goods_nos"><i class="bx bx-dots-horizontal-rounded fs-16" style="line-height: 28px;"></i></a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
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
                    </div>
                    <div class="search-area-ext d-none row">
                        <div class="col-lg-4 inner-td">
                            <div class="form-group">
                                <label for="formrow-inputCity">품목</label>
                                <div class="flax_box">
                                    <select name='item' class="form-control form-control-sm">
                                        <option value=''>전체</option>
                                        @foreach ($items as $item)
                                            <option value='{{ $item->cd }}'>{{ $item->val }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4 inner-td">
                            <div class="form-group">
                                <label for="formrow-inputState">브랜드</label>
                                <div class="form-inline inline_btn_box">
                                    <select id="brand_cd" name="brand_cd" class="form-control form-control-sm select2-brand"></select>
                                    <a href="#" class="btn btn-sm btn-outline-primary sch-brand"><i class="bx bx-dots-horizontal-rounded fs-16" style="line-height: 28px;"></i></a>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4 inner-td">
                            <div class="form-group">
                                <label for="formrow-inputZip">상품명</label>
                                <div class="flax_box">
                                    <input type='text' class="form-control form-control-sm ac-goods-nm search-enter" name='goods_nm' value=''>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="search-area-ext d-none row">
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
                                            <a href="#" class="btn btn-sm btn-outline-primary sch-company"><i class="bx bx-dots-horizontal-rounded fs-16" style="line-height: 28px;"></i></a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
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
                    </div>
                </div>
            </div>
            <div class="resul_btn_wrap mb-3">
                <a href="javascript:;" class="btn btn-sm w-xs btn-primary shadow-sm apply-btn" onclick="return Search();"><i class="fas fa-search fa-sm text-white-50"></i> 조회</a>

                <div class="search_mode_wrap btn-group mr-2 mb-0 mb-sm-0"></div>
            </div>
        </div>
    </form>
    <div id="filter-area" class="card shadow-none mb-0 ty2 last-card">
        <div class="card-body shadow">
            <div class="card-title">
                <div class="filter_wrap">
                    <div class="fl_box">
                        <h6 class="m-0 font-weight-bold">총 : <span id="gd-total" class="text-primary">0</span> 건</h6>
                    </div>
                    <div class="fr_box flax_box">
                    </div>
                </div>
            </div>
            <div class="table-responsive">
                <div id="div-gd" style="width:100%;height:calc(100vh - 370px);" class="ag-theme-balham"></div>
            </div>
        </div>
    </div>
    <script src="https://unpkg.com/ag-charts-community@2.1.0/dist/ag-charts-community.min.js"></script>
    <script language="javascript">
        var columns = [
            {headerName: "품목", field: "item",width:100,cellClass:'hd-grid-code',pinned:'left',aggSum:"합계",aggAvg:"평균"},
            {headerName: "브랜드", field: "brand_nm",width:100,cellClass:'hd-grid-code',pinned:'left'},
            {headerName: "스타일넘버", field: "style_no",width:100,cellClass:'hd-grid-code',pinned:'left'},
            {headerName: "상품명", field: "goods_nm",type:'HeadGoodsNameType',cellClass:'hd-grid-code',pinned:'left'},
            {headerName: '매출',pinned:'left',
                children: [
                    {headerName: "건수", field: "qty_cnt",type: 'numberType',aggregation:true},
                    {headerName: "수량", field: "qty_all",type: 'numberType',aggregation:true},
                    {headerName: "금액", field: "price_all",type: 'currencyType',aggregation:true},
                ]
            },
            {headerName: '주문',
                children: [
                    {headerName: "건수", field: "qty_cnt",type: 'numberType',aggregation:true},
                    {headerName: "수량", field: "qty_all",type: 'numberType',aggregation:true},
                    {headerName: "금액", field: "price_all",type: 'currencyType',aggregation:true},
                ]
            },
            {headerName: '결제오류',
                children: [
                    {headerName: "수량", field: "qty_20_err",type: 'numberType',aggregation:true},
                    {headerName: "금액", field: "price_20_err",type: 'currencyType',aggregation:true},
                ]
            },
            {headerName: '임금전주문취소',
                children: [
                    {headerName: "수량", field: "qty_10_cancel",type: 'numberType',aggregation:true},
                    {headerName: "금액", field: "price_10_cancel",type: 'currencyType',aggregation:true},
                ]
            },
            {headerName: '입금완료',
                children: [
                    {headerName: "수량", field: "qty_10",type: 'numberType',aggregation:true},
                    {headerName: "금액", field: "price_10",type: 'currencyType',aggregation:true},
                ]
            },
            {headerName: '교환',
                children: [
                    {headerName: "수량", field: "qty_60",type: 'numberType',aggregation:true},
                    {headerName: "금액", field: "price_60",type: 'currencyType',aggregation:true},
                ]
            },
            {headerName: '환불',
                children: [
                    {headerName: "수량", field: "qty_61",type: 'numberType',aggregation:true},
                    {headerName: "금액", field: "price_61",type: 'currencyType',aggregation:true},
                ]
            },
            {headerName: '매출(순판매)',
                children: [
                    {headerName: "수량", field: "qty_sale",type: 'numberType',aggregation:true},
                    {headerName: "금액", field: "price_sale",type: 'currencyType',aggregation:true},
                ]
            },

        ];
    </script>
    <script type="text/javascript" charset="utf-8">
        let chart_data = null;

        const pApp = new App('',{
            gridId:"#div-gd",
        });
        let gx;
        $(document).ready(function() {
            pApp.ResizeGrid(285);
            pApp.BindSearchEnter();
            let gridDiv = document.querySelector(pApp.options.gridId);
            let options = {
                getRowStyle: (params) => {
                    if (params.node.rowPinned === 'top') {
                        return { 'background': '#eee' }
                    }
                }
            };
            gx = new HDGrid(gridDiv, columns, options);
            Search();
        });

        function Search() {
            $('[name=ord_state]').val(10);

            let data = $('form[name="search"]').serialize();

            gx.Aggregation({
                "sum":"top",
                "avg":"top"
            });

            gx.Request('/head/sales/sal23/search', data, -1, function(data){
                chart_data = data.body;
            });
        }
    </script>

@stop

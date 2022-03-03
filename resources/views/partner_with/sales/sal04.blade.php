@extends('partner_with.layouts.layout')
@section('title','상품별 매출 통계')
@section('content')

    <div class="page_tit">
        <h3 class="d-inline-flex">상품별 매출 통계</h3>
        <div class="d-inline-flex location">
            <span class="home"></span>
            <span>/ 상품별 매출 통계</span>
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
                                <label for="formrow-firstname-input">매출일자</label>
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
                                <label for="formrow-email-input">매출시점</label>
                                <div class="form-inline form-radio-box">
                                    <div class="custom-control custom-radio">
                                        <input type="radio" name="ord_state" id="ord_state1" value="10" class="custom-control-input" checked="">
                                        <label class="custom-control-label" for="ord_state1" value="10">출고요청</label>
                                    </div>
                                    <div class="custom-control custom-radio">
                                        <input type="radio" name="ord_state" id="ord_state2" value="30" class="custom-control-input">
                                        <label class="custom-control-label" for="ord_state2" value="30">출고완료</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4 inner-td">
                            <div class="form-group">
                                <label for="formrow-inputCity">카테고리</label>
                                <div class="form-inline inline_select_box">
                                    <div class="form-inline-inner input-box w-25 pr-1">
                                        <select name='cat_type' id='cat_type' class="form-control form-control-sm w-100">
                                            <option value='DISPLAY'>전시</option>
                                            <option value='ITEM'>용도</option>
                                        </select>
                                    </div>
                                    <div class="form-inline-inner input-box w-75">
                                        <div class="form-inline inline_btn_box">
                                            <select id="cat_cd" name="cat_cd" class="form-control form-control-sm select2-category"></select>
                                            <a href="#" class="btn btn-sm btn-outline-primary sch-brand"><i class="bx bx-dots-horizontal-rounded fs-16"></i></a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-4 inner-td">
                            <div class="form-group">
                                <label for="formrow-inputState">결제방법</label>
                                <div class="form-inline form-check-box">
                                    <div class="custom-control custom-checkbox">
                                        <input type="checkbox" class="custom-control-input" name="stat_pay_type[0]" id="stat_pay_type_16" value="16">
                                        <label class="custom-control-label" for="stat_pay_type_16">계좌이체</label>
                                    </div>
                                    <div class="custom-control custom-checkbox">
                                        <input type="checkbox" class="custom-control-input" name="stat_pay_type[1]" id="stat_pay_type_32" value="32">
                                        <label class="custom-control-label" for="stat_pay_type_32">핸드폰</label>
                                    </div>
                                    <div class="custom-control custom-checkbox">
                                        <input type="checkbox" class="custom-control-input" name="stat_pay_type[2]" id="stat_pay_type_1" value="1">
                                        <label class="custom-control-label" for="stat_pay_type_1">현금</label>
                                    </div>
                                    <div class="custom-control custom-checkbox">
                                        <input type="checkbox" class="custom-control-input" name="stat_pay_type[3]" id="stat_pay_type_2" value="2">
                                        <label class="custom-control-label" for="stat_pay_type_2">카드</label>
                                    </div>
                                    <div class="custom-control custom-checkbox">
                                        <input type="checkbox" class="custom-control-input" name="stat_pay_type[4]" id="stat_pay_type_4" value="4">
                                        <label class="custom-control-label" for="stat_pay_type_4">포인트</label>
                                    </div>
                                    <div class="custom-control custom-checkbox">
                                        <input type="checkbox" class="custom-control-input" name="stat_pay_type[5]" id="stat_pay_type_8" value="8">
                                        <label class="custom-control-label" for="stat_pay_type_8">쿠폰</label>
                                    </div>
                                    <div class="custom-control custom-checkbox">
                                        <input type="checkbox" class="custom-control-input" name="stat_pay_type[6]" id="stat_pay_type_64" value="64">
                                        <label class="custom-control-label" for="stat_pay_type_64">가상계좌</label>
                                    </div>
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
                </div>
            </div>
            <div class="resul_btn_wrap mb-3">
                <a href="javascript:;" class="btn btn-sm w-xs btn-primary shadow-sm apply-btn" onclick="return Search();"><i class="fas fa-search fa-sm text-white-50"></i> 조회</a>
                <div class="search_mode_wrap btn-group mr-2 mb-0 mb-sm-0"></div>
            </div>
        </div>
    </form>
<!-- DataTales Example -->
<div id="filter-area" class="card shadow-none mb-4 ty2 last-card">
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
            <div id="div-gd" style="width:100%;min-height:300px;" class="ag-theme-balham"></div>
        </div>
    </div>
</div>
<div class="card shadow mb-4">
    <div class="card-body">
        <div class="card-title">
            <h6 class="m-0 font-weight-bold text-primary fas fa-question-circle"> Help</h6>
        </div>
        <ul class="mb-0">
            <li>매출액 = 과세 + 비과세</li>
            <li>매출원가 = 실제판매원가</li>
            <li>부가세 = 과세 - ( 과세 / 1.1 )</li>
            <li>세전 매출이익 = 매출액 - 매출원가</li>
        </ul>
    </div>
</div>
<script language="javascript">
    var columns = [
        {headerName: "상품상태", field: "goods_state",type:'GoodsStateType',pinned:'left'},
        {headerName: "품목", field: "opt_kind_nm",width:120,pinned:'left'},
        {headerName: "브랜드", field: "brand_nm",width:120,pinned:'left'},
        {headerName: "스타일넘버", field: "style_no",width:120,pinned:'left',
            cellRenderer: function (params) {
                if (params.value !== undefined) {
                    return "<a href='#' onclick='openDayStatistics(" + JSON.stringify(params.data) + ")'>" + params.value + "</a>";
                }
            }
        },
        {headerName: "상품명", field: "goods_nm",type:'GoodsNameType', pinned:'left',  aggSum:"합계", aggAvg:"평균",
            cellRenderer: function (params) {
                if (params.node.rowPinned === 'top') {
                    return params.value;
                }
                if (params.value !== undefined) {
                    return '<a href="#" onclick="return openProduct(\'' + params.data.goods_no + '\');">' + params.value + '</a>';
                }
            }
        },
        {headerName: "재고", field: "jaego_qty",type:'numberType',pinned:'left',aggregation:true,
            cellRenderer: function(params) {
                if (params.node.rowPinned === 'top') {
                    return params.value;
                }
                if (params.value !== undefined) {
                    return '<a href="#" onclick="return openStock(' + params.data.goods_no + ',\'\');">' + params.value + '</a>';
                }
            }
        },
        {headerName: '매출액구분',
            children: [
                {headerName: "수량", field: "sum_qty",type:'numberType',aggregation:true},
                {headerName: "적립금", field: "sum_point_amt",type:'currencyType',aggregation:true},
                {headerName: "할인", field: "sum_dc_amt",type:'currencyType',aggregation:true},
                {headerName: "쿠폰", field: "sum_coupon_amt",type:'currencyType',aggregation:true},
                {headerName: "수수료", field: "sum_fee_amt",type:'currencyType',aggregation:true},
                {headerName: "결제금액", field: "sum_recv_amt",type:'currencyType',aggregation:true},
                {headerName: "과세", field: "sum_taxation_amt",type:'currencyType',aggregation:true},
                {headerName: "비과세", field: "sum_taxfree",type:'currencyType',aggregation:true},
            ]
        },
        {headerName: "부가세", field: "vat",type:'currencyType',aggregation:true},
        {headerName: "매출액", field: "sum_amt",type:'currencyType',aggregation:true},
        {headerName: "매출원가", field: "wonga_60",type:'currencyType',aggregation:true},
        {headerName: "마진율", field: "margin",type:'percentType',
            valueGetter:function(params){
                if(params.data.goods_nm === "합계" || params.data.goods_nm === "평균") {
                    const data = params.data;
                    return (1- parseInt(data.sum_wonga) / ( parseInt(data.sum_recv_amt) + parseInt(data.sum_point_amt) - parseInt(data.sum_fee_amt) )) * 100;
                }
                return params.data.margin;
            }},
        {headerName: '매출이익',
            children: [
                {headerName: "세전", field: "margin1",type:'currencyType',aggregation:true},
                {headerName: "세후", field: "margin2",type:'currencyType',aggregation:true},
            ]
        },
        {headerName: '판매',
            children: [
                {headerName: "수량", field: "sum_qty",type:'numberType',aggregation:true},
                {headerName: "적립금", field: "point_amt_10",type:'currencyType',aggregation:true},
                {headerName: "할인", field: "dc_amt_10",type:'currencyType',aggregation:true},
                {headerName: "쿠폰", field: "coupon_amt_10",type:'currencyType',aggregation:true},
                {headerName: "수수료", field: "fee_amt_10",type:'currencyType',aggregation:true},
                {headerName: "결제금액", field: "sum_wonga",type:'currencyType',aggregation:true},
            ]
        },
        {headerName: '교환',
            children: [
                {headerName: "수량", field: "qty_60",type:'numberType',aggregation:true},
                {headerName: "적립금", field: "point_amt_60",type:'currencyType',aggregation:true},
                {headerName: "할인", field: "dc_amt_60",type:'currencyType',aggregation:true},
                {headerName: "쿠폰", field: "coupon_amt_60",type:'currencyType',aggregation:true},
            ]
        },
    ];

</script>
<script type="text/javascript" charset="utf-8">
    const pApp = new App('',{
        gridId:"#div-gd",
    });
    let gx;

    $(document).ready(function() {
        pApp.ResizeGrid(300);
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
        let data = $('form[name="search"]').serialize();
        gx.Aggregation({
            "sum":"top",
            "avg":"top"
        });
        gx.Request('/partner/sales/sal04/search', data);
    }

    const openDayStatistics = (obj) => {
        const { goods_nm, opt_kind_cd } = obj;
        const sdate = document.search.sdate.value;
        const edate = document.search.edate.value;

        let url = `/partner/sales/sal02/popup/?`;
        const params = "goods_nm=" + encodeURIComponent(goods_nm) + "&opt_kind_cd=" + encodeURIComponent(opt_kind_cd) + "&sdate=" + sdate + "&edate=" + edate;
        url = url + params;

        const OPENER = { TOP: 100, LEFT: 100, WIDTH: 1700, HEIGHT: 1000 };
        window.open(url, "_blank",
            `toolbar=no,scrollbars=yes,resizable=yes,status=yes,top=${OPENER.TOP},left=${OPENER.LEFT},width=${OPENER.WIDTH},height=${OPENER.HEIGHT}`
        );
    };

</script>

@stop

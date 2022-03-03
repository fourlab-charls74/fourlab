@extends('partner_with.layouts.layout')
@section('title','월별 주문 통계')
@section('content')

<div class="page_tit">
    <h3 class="d-inline-flex">월별 주문 통계</h3>
    <div class="d-inline-flex location">
        <span class="home"></span>
        <span>/ 월별 주문 통계</span>
    </div>
</div>


<form method="get" name="search">
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
                                        <input type="text" class="form-control form-control-sm docs-date month" name="sdate" value="{{ $sdate }}" autocomplete="off">
                                        <div class="input-group-append">
                                            <button type="button" class="btn btn-outline-secondary docs-datepicker-trigger p-0 pl-2 pr-2">
                                            <i class="fa fa-calendar" aria-hidden="true"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="docs-datepicker-container"></div>
                                </div>
                                <span class="text_line">~</span>
                                <div class="docs-datepicker form-inline-inner input_box">
                                    <div class="input-group">
                                        <input type="text" class="form-control form-control-sm docs-date month" name="edate" value="{{ $edate }}" autocomplete="off">
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
                            <label for="formrow-inputState">스타일넘버</label>
                            <div class="flax_box">
                                <input type='text' class="form-control form-control-sm ac-style-no search-enter" name='style_no' id="style_no" value="{{ $style_no }}">
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 inner-td">
                        <div class="form-group">
                            <label for="formrow-email-input">상품명</label>
                            <div class="flax_box">
                                <input type='text' class="form-control form-control-sm ac-goods-nm" name='goods_nm' value=''>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="search-area-ext d-none row">
                    <div class="col-lg-4 inner-td">
                        <div class="form-group">
                            <label for="formrow-inputCity">품목</label>
                            <div class="flax_box">
                                <input type='text' class="form-control form-control-sm" name='opt_kind_cd' value=''>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 inner-td">
                        <div class="form-group">
                            <label for="formrow-inputState">브랜드</label>
                            <div class="flax_box">
                                <input type='text' class="form-control form-control-sm" name='brand_cd' value=''>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 inner-td">
                        <div class="form-group">
                            <label for="formrow-inputZip">판매업체</label>
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
            </div>
        </div>
        <div class="resul_btn_wrap mb-3">
            <a href="javascript:;" class="btn btn-sm w-xs btn-primary shadow-sm apply-btn" onclick="return Search();"><i class="fas fa-search fa-sm text-white-50"></i> 조회</a>
            <div class="search_mode_wrap btn-group mr-2 mb-0 mb-sm-0"></div>
        </div>
    </div>
</form>

<!-- 차트 -->
<div class="card shadow mb-3">
    <div class="card-body">
        <input type="hidden" id="chart-type" value="date">
        <div id="opt_chart" style="height: 100%; min-height:300px;"></div>
    </div>
</div>

<!-- DataTales Example -->
<div id="filter-area" class="card shadow-none mb-4 ty2 last-card">
    <div class="card-body shadow">
        <div class="card-title">
            <div class="filter_wrap">
                <div class="fl_box">
                    <h6 class="m-0 font-weight-bold">총 : <span id="gd-total" class="text-primary">0</span> 건</h6>
                </div>
                <div class="fr_box">
                </div>
            </div>
        </div>
    <div class="table-responsive">
        <div id="div-gd" style="height:calc(100vh - 500px);width:100%;" class="ag-theme-balham"></div>
    </div>
  </div>
</div>

<script src="https://unpkg.com/ag-charts-community@2.1.0/dist/ag-charts-community.min.js"></script>

<script type="text/javascript" charset="utf-8">

    var columns = [
        {headerName: "주문일", field: "ord_date", aggAvg: "평균", aggSum: "합계", cellStyle: {"text-align": "center"},
            cellRenderer: (params) => {
                if (params.node.rowPinned === 'top') {
                    return params.value;
                }
                if (params.value !== undefined) {
                    return "<a href='#' onclick='openDayOrdStatistics(" + JSON.stringify(params.data) + ")'>" + params.value + "</a>";
                }
            }
        },
        {headerName: "총 주문 건수", field: "qty_cnt", type: 'numberType', aggregation:true, hide:true},
        {headerName: "총 주문 수량", field: "qty_all", type: 'numberType', aggregation:true},
        {headerName: "총 주문 금액", field: "price_all", type: 'currencyType', aggregation:true},
        {headerName: "결제 오류", field: "qty_20_err", type: 'numberType', aggregation:true},
        {headerName: "결제 오류 금액", field: "price_20_err", type: 'currencyType', aggregation:true},
        {headerName: "주문 취소", field: "qty_10_cancel", type: 'numberType', aggregation:true},
        {headerName: "주문 취소 금액", field: "price_10_cancel", type: 'currencyType', aggregation:true},
        {headerName: "입금 완료", field: "qty_10", type: 'numberType', aggregation:true},
        {headerName: "입금 완료 금액", field: "price_10", type: 'currencyType', aggregation:true},
        {headerName: "교환 수량", field: "qty_61", type: 'numberType', aggregation:true},
        {headerName: "교환 금액", field: "price_61", type: 'currencyType', aggregation:true},
        {headerName: "환불", field: "qty_60", type: 'numberType', aggregation:true},
        {headerName: "환불 금액", field: "price_60", type: 'currencyType', aggregation:true},
        {headerName: "주문 취소", field: "qty_10_cancel", type: 'numberType', aggregation:true},
        {headerName: "판매 수량", field: "qty_sale", type: 'numberType', aggregation:true},
        {headerName: "판매금액", field: "price_sale", type: 'currencyType', aggregation:true},
        {headerName: "", field: "", width: "auto"}
    ];

    const pApp = new App('', {
        gridId: "#div-gd",
    });
    const gridDiv = document.querySelector(pApp.options.gridId);
    let gx;
    let chart_data = null;
    let options = {
        getRowStyle: (params) => {
            if (params.node.rowPinned === 'top') {
                return { 'background': '#eee' }
            }
        }
    };

    $(document).ready(function () {

        gx = new HDGrid(gridDiv, columns, options);
        pApp.ResizeGrid(300);
        Search();

        $('.search-all').keyup(function(){
            date_use_check();
        });

    });

    function Search() {
        let data = $('form[name="search"]').serialize();
        gx.Aggregation({
            "sum":"top",
            "avg":"top"
        });
        gx.Request('/partner/order/ord72/search', data, -1, (data) => {
            chart_data = data.body;
            drawCanvas();
        });
    }

    const drawCanvas = () => {
        $('#opt_chart').html('');
        chart_data.sort((a, b) => {
            if (a.ord_date < b.ord_date) {
                return -1;
            }
            if (a.ord_date > b.ord_date) {
                return 1;
            }
            return 0;
        });

        let beforeRowYear = null;
        chart_data.map((item, idx) => {
            const year = item.ord_date.substring(2, 4);
            const month = item.ord_date.substring(4, 6);

            if (beforeRowYear !== year || beforeRowYear === null) {
                chart_data[idx].chart_x_str = year + "." + month;
                beforeRowYear = year;
                return;
            }
            chart_data[idx].chart_x_str = month;
        });

        var options = {
            container: document.getElementById('opt_chart'),
            title: {
                text: "월별 주문 통계",
            },
            data: chart_data,
            series: [{
                type: 'column',
                xKey: 'chart_x_str',
                yKeys: ['qty_cnt', 'qty_all'],
                yNames: ['총주문건수', '총주문수량'],
                grouped: true,
                fills: ['#556ee6', '#2797f6'],
                strokes: ['#556ee6', '#2797f6']
                // highlightStyle : {
                //   fill :
                // }
            }],
        };
        agCharts.AgChart.create(options);
        
    };

    const openDayOrdStatistics = (obj) => {

        let date = obj.ord_date;

        const year = date.substring(0, 4);
        const month = date.substring(4, 6);

        const d = new Date(year, month, 0); // 세번째 파라미터 0 -> 해당 달의 마지막 날을 가리킴
        const sdate = year + "-" + month + "-" + "01"; // First date of month
        const edate = year + "-" + month + "-" + d.getDate(); // End date of month

        let url = `/partner/order/ord71`;
        const params = "?sdate=" + sdate + "&edate=" + edate;
        url = url + params;

        window.open(url, "_blank");
        
    };

</script>


@stop

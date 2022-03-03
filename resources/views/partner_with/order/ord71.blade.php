@extends('partner_with.layouts.layout')
@section('title','일별 주문 통계')
@section('content')
<div class="page_tit">
    <h3 class="d-inline-flex">일별 주문 통계</h3>
    <div class="d-inline-flex location">
        <span class="home"></span>
        <span>/ 일별 주문 통계</span>
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
                <div class="search-area-ext d-none  row">
                    <div class="col-lg-4 inner-td">
                        <div class="form-group mb-0">
                            <label for="formrow-inputCity">품목</label>
                            <div class="flax_box">
                                <input type='text' class="form-control form-control-sm" name='opt_kind_cd' value=''>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 inner-td">
                        <div class="form-group mb-0">
                            <label for="formrow-inputState">브랜드</label>
                            <div class="flax_box">
                                <input type='text' class="form-control form-control-sm" name='brand_cd' value=''>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 inner-td">
                        <div class="form-group mb-0">
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
        <ul class="nav nav-tabs" id="myTab" role="tablist">
            <li class="nav-item" role="presentation">
                <a class="nav-link active" id="date-tab" data-toggle="tab" href="#home" role="tab" aria-controls="date" aria-selected="true">일별</a>
            </li>
            <li class="nav-item" role="presentation">
                <a class="nav-link" id="yoil-tab" data-toggle="tab" href="#contact" role="tab" aria-controls="yoil" aria-selected="false">요일별</a>
            </li>
        </ul>
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
                    return "<a href='#' onclick='openOrdList(" + JSON.stringify(params.data) + ")'>" + params.value + "</a>";
                }
            }
        },
        {headerName: "요일", field: "yoil", hide: true},
        {headerName: "총 주문 건수", field: "qty_cnt", type: 'numberType', hide: true},
        {headerName: "총 주문 수량", field: "qty_all", aggregation:true, type: 'numberType'},
        {headerName: "총 주문 금액", field: "price_all", aggregation:true, type: 'currencyType'},
        {headerName: "결제 오류", field: "qty_20_err", aggregation:true, type: 'numberType'},
        {headerName: "결제 오류 금액", field: "price_20_err", aggregation:true, type: 'currencyType'},
        {headerName: "주문 취소", field: "qty_10_cancel", aggregation:true, type: 'numberType'},
        {headerName: "주문 취소 금액", field: "price_10_cancel", aggregation:true, type: 'currencyType'},
        {headerName: "입금 완료", field: "qty_10", aggregation:true, type: 'numberType'},
        {headerName: "입금 완료 금액", field: "price_10", aggregation:true, type: 'currencyType'},
        {headerName: "교환 수량", field: "qty_61", aggregation:true, type: 'numberType'},
        {headerName: "교환 금액", field: "price_61", aggregation:true, type: 'currencyType'},
        {headerName: "환불", field: "qty_60", aggregation:true, type: 'numberType'},
        {headerName: "환불 금액", field: "price_60", aggregation:true, type: 'currencyType'},
        {headerName: "주문 취소", field: "qty_10_cancel", aggregation:true, type: 'numberType'},
        {headerName: "판매 수량", field: "qty_sale", aggregation:true, type: 'numberType'},
        {headerName: "판매금액", field: "price_sale", aggregation:true, type: 'currencyType'},
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
        },
        onGridReady(params) { // 정렬 안되는 문제 수정
            this.gridApi = params.api;
            var sort = [
                {
                    colId: "ord_date",
                    sort: "asc"
                }
            ];
            this.gridApi.setSortModel(sort);
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
        $('[name=ord_state]').val(10);

        let data = $('form[name="search"]').serialize();

        gx.Aggregation({
            "sum": "top",
            "avg": "top"
        });
        
        gx.Request('/partner/order/ord71/search', data, -1, searchCallback);
    }

    const searchCallback = (data) => {
        chart_data = data.body;
        drawCanvas();
    };

    const drawCanvas = () => {
        switch ($("#chart-type").val()) {
            case "date":
                drawCanvasByDate();
                break;
            case "yoil":
                drawCanvasByYoil();
                break;
        }
    };

    function drawCanvasByDate() {
        $('#opt_chart').html('');

        chart_data.sort(function(a, b) {
            if (a.ord_date < b.ord_date) {
                return -1;
            }
            if (a.ord_date > b.ord_date) {
                return 1;
            }

            return 0;
        });

        let beforeRowMonth = null;
        chart_data.map((item, idx) => {
            const month = item.ord_date.substring(4, 6);
            const day = item.ord_date.substring(6, 8);

            if (beforeRowMonth !== month || beforeRowMonth === null) {
                chart_data[idx].chart_x_str = month + "." + day;
                beforeRowMonth = month;
                return;
            }
            chart_data[idx].chart_x_str = day;
        });

        var options = {
            container: document.getElementById('opt_chart'),
            title: {
                text: "일별 주문 통계",
            },
            data: chart_data,
            series: [{
                type: 'column',
                xKey: 'chart_x_str',
                yKeys: ['qty_cnt', 'qty_all'],
                yNames: ['주문건수', '주문수량'],
                grouped: true,
                fills: ['#556ee6', '#2797f6'],
                strokes: ['#556ee6', '#2797f6']
                // highlightStyle : {
                //   fill :
                // }
            }],
        };
        agCharts.AgChart.create(options);
    }

    function drawCanvasByTime() {
        $('#opt_chart').html('차트를 생성할 수 없습니다.');
    }

    function drawCanvasByYoil() {
        $('#opt_chart').html('');

        let data = [{
                name: '일요일',
                qty_cnt: 0,
                qty_all: 0
            },
            {
                name: '월요일',
                qty_cnt: 0,
                qty_all: 0
            },
            {
                name: '화요일',
                qty_cnt: 0,
                qty_all: 0
            },
            {
                name: '수요일',
                qty_cnt: 0,
                qty_all: 0
            },
            {
                name: '목요일',
                qty_cnt: 0,
                qty_all: 0
            },
            {
                name: '금요일',
                qty_cnt: 0,
                qty_all: 0
            },
            {
                name: '토요일',
                qty_cnt: 0,
                qty_all: 0
            }
        ];

        chart_data.forEach(function(c_data) {
            data[c_data.yoil - 1].qty_cnt += Number(c_data.qty_cnt);
            data[c_data.yoil - 1].qty_all += Number(c_data.qty_all);
        });
        var options = {
            container: document.getElementById('opt_chart'),
            title: {
                text: "요일별 주문 통계",
            },
            data: data,
            series: [{
                type: 'column',
                xKey: 'name',
                yKeys: ['qty_cnt', 'qty_all'],
                yNames: ['주문건수', '주문수량'],
                grouped: true,
                fills: ['#556ee6', '#2797f6'],
                strokes: ['#556ee6', '#2797f6']
            }],
        };

        agCharts.AgChart.create(options);
    }
    $("#date-tab").click(function() {
        $("#chart-type").val('date');
        drawCanvasByDate();
    });

    $("#yoil-tab").click(function() {
        $("#chart-type").val('yoil');
        drawCanvasByYoil();
    });

    const openOrdList = (obj) => {

        let date = obj.ord_date;

        const year = date.substring(0, 4);
        const month = date.substring(4, 6);
        const day = date.substring(6, 8);

        date = year + "-" + month + "-" + day;

        let url = `/partner/order/ord01`;
        const params = "?sdate=" + date + "&edate=" + date;
        url = url + params;

        window.open(url, "_blank");

    };

</script>



@stop

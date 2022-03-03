@extends('partner_with.layouts.layout-nav')
@section('title','일별 매출 통계')
@section('content')

<style>
    .card-body { border-top: none !important; }
</style>
<div class="show_layout py-3 px-sm-3">
    <div class="page_tit mb-3 d-flex align-items-center justify-content-between">
        <div>
            <h3 class="d-inline-flex">일별 매출 통계</h3>
            <div class="d-inline-flex location">
                <span class="home"></span>
                <span>/ 일별 매출 통계</span>
            </div>
        </div>
        <div>
            <a href="#" id="search_sbtn" onclick="return Search();" class="btn btn-sm btn-primary shadow-sm pl-2"><i class="fas fa-search fa-sm text-white-50"></i> 조회</a>
        </div>
    </div>
    <div class="card_wrap aco_card_wrap">
        <form method="get" name="search" id="search">
            <div class="card shadow">
                <div class="card-body">
                    <div class="row_wrap">
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
                            <div class="col-lg-8 inner-td">
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
                        </div>
                        <div class="row">
                            <div class="col-lg-4 inner-td">
                                <div class="form-group">
                                    <label for="formrow-inputCity">품목</label>
                                    <div class="flax_box">
                                        <select name='item' class="form-control form-control-sm">
                                            <option value=''>전체</option>
                                            <?php
                                            foreach ($items as $item) {
                                                $selected = $item->cd == $opt_kind_cd ? 'selected' : '';
                                                $cd = $item->cd;
                                                $val = $item->val;
                                                echo "<option value='${cd}' ${selected}>${val}</option>";
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-4 inner-td">
                                <div class="form-group">
                                    <label for="formrow-inputState">브랜드</label>
                                    <div class="form-inline inline_btn_box">
                                        <select id="brand_cd" name="brand_cd" class="form-control form-control-sm select2-brand"></select>
                                        <a href="#" class="btn btn-sm btn-outline-primary sch-brand"><i class="bx bx-dots-horizontal-rounded fs-16"></i></a>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-4 inner-td">
                                <div class="form-group">
                                    <label for="formrow-inputZip">상품명</label>
                                    <div class="flax_box">
                                        <input type='text' class="form-control form-control-sm ac-goods-nm search-enter" name='goods_nm' value='<?=$goods_nm ? $goods_nm : ''?>'>
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
        </form>
        <!-- 차트 -->
        <div class="card shadow my-3">
            <div class="card-body">
            <input type="hidden" id="chart-type" value="date">
            <ul class="nav nav-tabs" id="myTab" role="tablist">
                <li class="nav-item" role="presentation">
                <a class="nav-link active" id="date-tab"" data-toggle="tab" href="#home" role="tab" aria-controls="date" aria-selected="true">일별</a>
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
                        <div class="fr_box flax_box">
                        </div>
                    </div>
                </div>
                <div class="table-responsive">
                    <div id="div-gd" style="width:100%;min-height:600px;" class="ag-theme-balham"></div>
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
    </div>
</div>
<script src="https://unpkg.com/ag-charts-community@2.1.0/dist/ag-charts-community.min.js"></script>
<script language="javascript">
    var columns = [
        {headerName: "입금일", field: "date",width:120,cellClass:'hd-grid-code',pinned:'left',aggSum:"합계",aggAvg:"평균"},
        {headerName: '매출액구분',
            children: [
                {headerName: "수량", field: "sum_qty",type: 'numberType',aggregation:true},
                {headerName: "적립금", field: "sum_point_amt",type: 'currencyType',aggregation:true},
                {headerName: "할인", field: "sum_dc_amt",type: 'currencyType',aggregation:true},
                {headerName: "쿠폰", field: "sum_coupon_amt",type: 'currencyType',aggregation:true},
                {headerName: "수수료", field: "sum_fee_amt",type: 'currencyType',aggregation:true},
                {headerName: "결제금액", field: "sum_recv_amt",type: 'currencyType',aggregation:true},
                {headerName: "과세", field: "sum_taxation_amt",type: 'currencyType',aggregation:true},
                {headerName: "비과세", field: "sum_taxfree",type: 'currencyType',aggregation:true},
            ]
        },
        {headerName: "부가세", field: "vat",type: 'currencyType',aggregation:true},
        {headerName: "매출액", field: "sum_amt",type: 'currencyType',aggregation:true},
        {headerName: "매출원가", field: "sum_wonga",type: 'currencyType',aggregation:true},
        {headerName: "마진율", field: "margin",type: 'percentType',
            valueGetter:function(params){
                if(params.data.date === "합계" || params.data.date === "평균"){
                    const data = params.data;
                    return (1- parseInt(data.sum_wonga) / ( parseInt(data.sum_recv_amt) + parseInt(data.sum_point_amt) - parseInt(data.sum_fee_amt) )) * 100;
                }
                return params.data.margin;
            }},
        {headerName: '매출이익',
            children: [
                {headerName: "세전", field: "margin1",type: 'currencyType',aggregation:true},
                {headerName: "세후", field: "margin2",type: 'currencyType',aggregation:true},
            ]
        },
        {headerName: '판매',
            children: [
                {headerName: "수량", field: "qty_30",type: 'numberType',aggregation:true},
                {headerName: "적립금", field: "point_amt_30",type: 'currencyType',aggregation:true},
                {headerName: "할인", field: "dc_amt_30",type: 'currencyType',aggregation:true},
                {headerName: "쿠폰", field: "coupon_amt_30",type: 'currencyType',aggregation:true},
                {headerName: "수수료", field: "fee_amt_30",type: 'currencyType',aggregation:true},
                {headerName: "결제금액", field: "recv_amt_30",type: 'currencyType',aggregation:true},
            ]
        },
        {headerName: '교환',
            children: [
                {headerName: "수량", field: "qty_60",type: 'numberType',aggregation:true},
                {headerName: "적립금", field: "point_amt_60",type: 'currencyType',aggregation:true},
                {headerName: "할인", field: "dc_amt_60",type: 'currencyType',aggregation:true},
                {headerName: "쿠폰", field: "coupon_amt_60",type: 'currencyType',aggregation:true},
                {headerName: "수수료", field: "fee_amt_60",type: 'currencyType',aggregation:true},
                {headerName: "결제금액", field: "recv_amt_60",type: 'currencyType',aggregation:true},
            ]
        },
        {headerName: '환불',
            children: [
                {headerName: "수량", field: "qty_61",type: 'numberType',aggregation:true},
                {headerName: "적립금", field: "point_amt_61",type: 'currencyType',aggregation:true},
                {headerName: "할인", field: "dc_amt_61",type: 'currencyType',aggregation:true},
                {headerName: "쿠폰", field: "coupon_amt_61",type: 'currencyType',aggregation:true},
                {headerName: "수수료", field: "fee_amt_61",type: 'currencyType',aggregation:true},
                {headerName: "결제금액", field: "recv_amt_61",type: 'currencyType',aggregation:true},
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
        $('[name=ord_state]').val(10);
        let data = $('form[name="search"]').serialize();
        gx.Aggregation({
            "sum":"top",
            "avg":"top"
        });

        gx.Request('/partner/sales/sal02/search', data, -1, searchCallback);
    }

    function searchCallback(data) {
        chart_data = data.body;
            drawCanvas();
    }

    function drawCanvas() {
      console.log($("#chart-type").val());
      switch($("#chart-type").val()) {
        case "date" :
          drawCanvasByDate();
          break;
        case "time" :
          drawCanvasByTime();
          break;
        case "yoil" :
          drawCanvasByYoil();
          break;
      }
    }

    function drawCanvasByDate() {
      $('#opt_chart').html('');

      let beforeRowMonth = null;

      chart_data.sort(function (a, b) {
        if (a.date < b.date) {
          return -1;
        }
        if (a.date > b.date) {
          return 1;
        }

        return 0;
      });

      chart_data.forEach(function(row){
        if (beforeRowMonth !== row.month || beforeRowMonth === null) {
          row.chart_x_str = row.month + "." + row.day;
          beforeRowMonth = row.month;
          return;
        }

        row.chart_x_str = row.day;
      });

      var options = {
        container: document.getElementById('opt_chart'),
        title: {
          text: "일별 매출 통계",
        },
        data: chart_data,
        series: [
          {
            type: 'column',
            xKey: 'chart_x_str',
            yKeys: ['sum_amt', 'sum_wonga', 'margin'],
            yNames: ['매출액', '매출원가', '마진율'],
            grouped: true,
            fills : [ '#556ee6', '#2797f6', '#beccfd' ],
            strokes: [ '#556ee6', '#2797f6', '#beccfd' ]
            // highlightStyle : {
            //   fill :
            // }
          }
        ],
      };

      agCharts.AgChart.create(options);
    }

    function drawCanvasByYoil() {
      $('#opt_chart').html('');

      let data = [
        { name : '일요일', sum_amt : 0, sum_wonga : 0, margin : 0 },
        { name : '월요일', sum_amt : 0, sum_wonga : 0, margin : 0 },
        { name : '화요일', sum_amt : 0, sum_wonga : 0, margin : 0 },
        { name : '수요일', sum_amt : 0, sum_wonga : 0, margin : 0 },
        { name : '목요일', sum_amt : 0, sum_wonga : 0, margin : 0 },
        { name : '금요일', sum_amt : 0, sum_wonga : 0, margin : 0 },
        { name : '토요일', sum_amt : 0, sum_wonga : 0, margin : 0 }
      ];

      chart_data.forEach(function(c_data){
        data[c_data.yoil - 1].sum_amt += Number(c_data.sum_amt);
        data[c_data.yoil - 1].sum_wonga += Number(c_data.sum_wonga);
        data[c_data.yoil - 1].margin += Number(c_data.margin);
      });
      console.log(data);
      var options = {
        container: document.getElementById('opt_chart'),
        title: {
          text: "요일별 매출 통계",
        },
        data: data,
        series: [
          {
            type: 'column',
            xKey: 'name',
            yKeys: ['sum_amt', 'sum_wonga', 'margin'],
            yNames: ['매출액', '매출원가', '마진율'],
            grouped: true,
            fills : [ '#556ee6', '#2797f6', '#beccfd' ],
            strokes: [ '#556ee6', '#2797f6', '#beccfd' ]
          }
        ],
      };

      agCharts.AgChart.create(options);
    }
    $("#date-tab").click(function(){
      $("#chart-type").val('date');
      drawCanvasByDate();
    });

    $("#time-tab").click(function(){
      $("#chart-type").val('time');
      drawCanvasByTime();
    });

    $("#yoil-tab").click(function(){
      $("#chart-type").val('yoil');
      drawCanvasByYoil();
    });
</script>

@stop

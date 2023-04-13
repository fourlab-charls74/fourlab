@extends('store_with.layouts.layout')
@section('content')
<!-- 메인페이지 4분할
    왼쪽 상단 - 일별 매출통계/매장별 매출통계의 차트를 탭으로 구성
    오른쪽 상단 - 자주가는 메뉴 추가예정(추후 논의)
    왼쪽 하단 - 공지사항
    오른쪽 하단 - 알리미
-->

<style>
    #main_grid {
        display: grid;
        height: calc(100vh - 115px);
        grid-template-columns: repeat(2, 50%);
        grid-template-rows: 60% 40%;
        gap: 12px;
        grid-template-areas:
            'a b'
            'c d'
    }
    .my-chart {
        min-height: 300px;
    }
    @media (max-width: 740px) {
        #main_grid {
            display: flex;
            flex-direction: column;
            height: auto;
        }
        .ag-theme-balham {
            height: 400px !important;
        }
        .my-chart {
            min-height: 400px;
        }
    }
</style>

<div id="main_grid">
    <div class="card shadow" style="grid-area: a;">
        <div class="card-body h-100">
            <ul class="nav nav-tabs" id="myTab" role="tablist">
                <li class="nav-item" role="presentation">
                    <a class="nav-link" id="bar-tab" data-toggle="tab" href="#bar" role="tab" aria-controls="bar" aria-selected="false">일별 매출</a>
                </li>
                <li class="nav-item" role="presentation">
                    <a class="nav-link" id="pie-tab" data-toggle="tab" href="#pie" role="tab" aria-controls="pie" aria-selected="false">매장별 매출</a>
                </li>
            </ul>
            <div class="tab-content h-100" id="myTabContent">
                <div class="tab-pane fade h-100" id="bar" role="tabpanel" aria-labelledby="bar-tab">
                    <div class="h-100" style="max-height: 83%;">
                        <div style="margin-top:20px;">
                            <a href="#" id="msg_del_btn" onclick="sale_amt_days()"class="btn btn-sm btn-primary shadow-sm" style="float:right;">더보기</a>
                        </div>
                        <canvas id="myChart" class="my-chart"></canvas>
                    </div>
                </div>
                <div class="tab-pane fade h-100" id="pie" role="tabpanel" aria-labelledby="pie-tab">
                    <div class="w-100 h-100" style="margin-top: 20px;max-height: 83%;">
                        <a href="#" id="msg_del_btn" onclick="sale_amt_store()"class="btn btn-sm btn-primary shadow-sm mr-1" style="float:right;">더보기</a>
                        <canvas id="myChart2" class="my-chart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="card shadow" style="grid-area: b;">
        <div class="card-body h-100">
            <ul class="nav nav-tabs" id="myTab" role="tablist">
                <li class="nav-item" role="presentation">
                    <a class="nav-link" id="order_amt-tab" data-toggle="tab" href="#order_amt" role="tab" aria-controls="order_amt" aria-selected="false">주문금액</a>
                </li>
                <li class="nav-item" role="presentation">
                    <a class="nav-link" id="order_qty-tab" data-toggle="tab" href="#order_qty" role="tab" aria-controls="order_qty" aria-selected="false">주문수량</a>
                </li>
            </ul>
            <div class="tab-content h-100" id="myTabContent" style="margin-top:12px;">
                <div class="tab-pane fade h-100" id="order_amt" role="tabpanel" aria-labelledby="order_amt-tab">
                    <div class="h-100" style="max-height: 83%;">
                        <div style="text-align:right">
                            <span style="font-size: 17px;">[ {{@$sdate2}} ~ {{@$edate2}} ]</span>
                        </div>
                        <canvas id="myChart3" class="my-chart"></canvas>
                    </div>
                </div>
                <div class="tab-pane fade h-100" id="order_qty" role="tabpanel" aria-labelledby="order_qty-tab">
                    <div class="h-100" style="max-height: 83%;">
                        <div style="text-align:right">
                            <span style="font-size: 17px;">[ {{@$sdate2}} ~ {{@$edate2}} ]</span>
                        </div>
                        <canvas id="myChart4" class="my-chart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="card shadow" style="grid-area: c;">
        <div class="card-body">
            <div class="filter_wrap w-100 h-100">
                <div id="filter-area" class="card shadow-none mb-0 search_cum_form ty2 last-card h-100">
                    <div class="h-100">
                        <div class="card-title">
                            <div class="filter_wrap">
                                <div class="fl_box">
                                    <h6 class="m-0 font-weight-bold">총 <span id="gd-total" class="text-primary">0</span> 건</h6>
                                </div>
                                <div class="fr_box">
                                    <a href="#" id="msg_del_btn" onclick="notice()"class="btn btn-sm btn-primary shadow-sm" style="float:right;">더보기</a>
                                </div>
                            </div>
                        </div>
                        <div class="table-responsive" style="height: 90%;">
                            <div id="div-gd" class="ag-theme-balham" style="height: 100%;"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="card shadow" style="grid-area: d;">
        <div class="card-body">
            <div class="filter_wrap w-100 h-100">
                <div id="filter-area" class="card shadow-none mb-0 search_cum_form ty2 last-card h-100">
                    <div class="h-100">
                        <div class="card-title">
                            <div class="filter_wrap">
                                <div class="fl_box">
                                    <h6 class="m-0 font-weight-bold">총 <span id="gd-alarm-total" class="text-primary">0</span> 건</h6>
                                </div>
                                <a href="#" id="msg_del_btn" onclick="msg()"class="btn btn-sm btn-primary shadow-sm" style="float:right;">더보기</a>
                            </div>
                        </div>
                        <div class="table-responsive" style="height: 90%;">
                            <div id="div-gd-alarm" style="height: 100%" class="ag-theme-balham darkmode"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- 공지사항 -->
<script type="text/javascript" charset="utf-8">
    const columns = [
        {headerName: "제목", field: "subject", width: 300,
            cellRenderer: function(params) {
                return '<a href="/store/community/comm01/notice' + params.data.ns_cd +'" rel="noopener">'+ params.value+'</a>';
            }
        },
        {headerName: "이름", field: "admin_nm",  width: 60, cellClass: 'hd-grid-code'},
        {headerName: "조회수", field: "cnt", type:'numberType',width: 50, cellClass: 'hd-grid-code'},
        {headerName: "등록일시", field: "rt", type:"DateTimeType"},
        {headerName: "전체 공지 여부", field: "all_store_yn",width: 90, cellClass: 'hd-grid-code',
            cellStyle: params => {
                if(params.data.all_store_yn == 'Y'){
                    return {color:'red'}
                }else{
                    return {color:'blue'}
                }
            },
            cellRenderer: function(params){
                if(params.data.stores == null){
                    return params.data.all_store_yn = "Y";
                }else{
                    return params.data.all_store_yn = "N";
                }
            }
        },
        {headerName: "공지매장", field: "store_nm", width: 340, cellClass: 'hd-grid-code',
            cellRenderer: function(params) {
                return params.data.stores;
            }
        },
        {headerName: "글번호", field: "ns_cd", hide:true },
        {width: 'auto'}
    ];

    const pApp = new App('', { gridId:"#div-gd" });
    let gx;

    $(document).ready(function() {
        let gridDiv = document.querySelector(pApp.options.gridId);
        gx = new HDGrid(gridDiv, columns);
        pApp.BindSearchEnter();
        Search();
    });

    function Search() {
        gx.Request('/store/main');
    }
</script>


<!-- 알리미 -->
<script type="text/javascript" charset="utf-8">
    const columns2 = [
        {headerName: "발신처", field: "sender_nm", width:150},
        {headerName: "연락처", field: "mobile", width: 80, cellClass: 'hd-grid-code'},
        {headerName: "내용", field: "content", width: 300, cellStyle: {'text-overflow': 'ellipsis'},
            cellRenderer: params => {
                return "<a href='#' onclick='showContent(" + params.data.msg_cd +")'>"+params.value+"</a>";
            },
        },
        {headerName: "받은 날짜", field: "rt", width: 110, cellClass: 'hd-grid-code'},
        {headerName: "확인여부", field: "check_yn", width: 110, cellClass: 'hd-grid-code',
            cellStyle: (params) => ({color: params.data.check_yn == 'Y' ? 'green' : 'none'})
        },
        {headerName: "알림 번호", field: "msg_cd", hide: true},
        {width: 'auto'}
    ];

    const pApp2 = new App('', { gridId:"#div-gd-alarm" });
    let gx2;

    $(document).ready(function() {
        let gridDiv2 = document.querySelector(pApp2.options.gridId);
        gx2 = new HDGrid(gridDiv2, columns2);
        pApp2.BindSearchEnter();
        Search_alarm();
    });

    function Search_alarm() {
        gx2.Request('/store/main_alarm');
    }
</script>

<script>
    $(document).ready(function(){
        $('#bar-tab').trigger("click");
        $('#order_amt-tab').trigger("click");
    });

     function showContent(msg_cd) {
        const url = '/store/stock/stk32/showContent?msg_type=receive&msg_cd=' + msg_cd;
        const msg = window.open(url, "_blank", "toolbar=no,scrollbars=yes,resizable=yes,status=yes,top=500,left=500,width=800,height=615");
    }

    function notice() {
        window.location.href = "/store/community/comm01/notice";
    }

    function msg() {
        window.location.href = "/store/stock/stk32";
    }

    function sale_amt_store() {
        window.location.href = "/store/sale/sal26";
    }

    function sale_amt_days() {
        window.location.href = "/store/sale/sal24";
    }
</script>

<!-- 차트 -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.3/Chart.bundle.min.js"></script>
<script>
    const ctx = document.getElementById('myChart').getContext('2d');

    let edate = '{{$edate}}';
    let sdate = '{{$sdate}}';

    let chartData = <?= json_encode($result)?>;
    let all_date = getDatesStartToLast(sdate, edate);

    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: [
                all_date[0],
                all_date[1],
                all_date[2],
                all_date[3],
                all_date[4],
                all_date[5],
                all_date[6],
                all_date[7],
            ],
            datasets: [
                {
                    label: '매출액',
                    data: [
                        chartData[0].sum_amt,
                        chartData[1].sum_amt,
                        chartData[2].sum_amt,
                        chartData[3].sum_amt,
                        chartData[4].sum_amt,
                        chartData[5].sum_amt,
                        chartData[6].sum_amt,
                        chartData[7].sum_amt,
                    ],
                    borderColor: '#36A2EB',
                    backgroundColor: '#9BD0F5',
                    borderWidth: 1
                },
                {
                    label: '매출원가',
                    data: [
                        chartData[0].sum_wonga,
                        chartData[1].sum_wonga,
                        chartData[2].sum_wonga,
                        chartData[3].sum_wonga,
                        chartData[4].sum_wonga,
                        chartData[5].sum_wonga,
                        chartData[6].sum_wonga,
                        chartData[7].sum_wonga,
                    ],
                    borderColor: '#FF6384',
                    backgroundColor: '#FFB1C1',
                    borderWidth: 1
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            animation: {
            // easing:'easeOutElastic',
            },
            legend:{
                position : 'top'
            },
            tooltips: {
                callbacks: {
                    label: function (tooltipItem, data) {
                        return " " + Comma(data.datasets[tooltipItem.datasetIndex].data[tooltipItem.index]) + "원";
                    }
                },
            },
            scales: {
                y: {
                    beginAtZero: true
                },
                yAxes: [{
                    ticks: {
                        beginAtZero: true,
                        callback: function(value, index) {
                            return Comma(value);
                        }
                    }
                }]
            }
        }
    });

    function getDatesStartToLast(sdate, edate) {
        var regex = RegExp(/^\d{4}-(0[1-9]|1[012])-(0[1-9]|[12][0-9]|3[01])$/);
        if(!(regex.test(sdate) && regex.test(edate))) return "Not Date Format";
        var result = [];
        var curDate = new Date(sdate);
        while(curDate <= new Date(edate)) {
            result.push(curDate.toISOString().split("T")[0]);
            curDate.setDate(curDate.getDate() + 1);
        }
        return result;
    }

    let pieChartData = <?= json_encode($pieResult)?>;

    const ctx2 = document.getElementById('myChart2');

    const pieChart = new Chart(ctx2, {
        type: 'pie',
        data: {
            labels: [
                pieChartData[0].store_nm,
                pieChartData[1].store_nm,
                pieChartData[2].store_nm,
                pieChartData[3].store_nm,
                pieChartData[4].store_nm,
                pieChartData[5].store_nm,
                pieChartData[6].store_nm,
                pieChartData[7].store_nm,
                pieChartData[8].store_nm,
                pieChartData[9].store_nm,
            ],
            datasets: [{
                label: '매출액',
                data: [
                    pieChartData[0].sum_amt,
                    pieChartData[1].sum_amt,
                    pieChartData[2].sum_amt,
                    pieChartData[3].sum_amt,
                    pieChartData[4].sum_amt,
                    pieChartData[5].sum_amt,
                    pieChartData[6].sum_amt,
                    pieChartData[7].sum_amt,
                    pieChartData[8].sum_amt,
                    pieChartData[9].sum_amt,
                ],
                backgroundColor: [
                    'rgb(255, 99, 132)',
                    'rgb(54, 162, 235)',
                    'rgb(255, 205, 86)',
                    'rgb(130, 36, 227)',
                    'rgb(129, 215, 66)',
                    'rgb(57, 233, 215)',
                    'rgb(144, 141, 135)',
                    'rgb(221, 51, 51)',
                    'rgb(33, 145, 51)',
                    'rgb(249, 213, 158)'
                ],
                hoverOffset: 4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            legend: {
                position: 'right',
            },
            tooltips: {
                callbacks: {
                    label: function (tooltipItem, data) {
                        return " " + Comma(data.datasets[tooltipItem.datasetIndex].data[tooltipItem.index]) + "원";
                    }
                },
            },
        }
    });

    // 상위 6~10위는 hidden 처리
    for (let i = 0; i < 5; i++) {
        pieChart.getDatasetMeta(0).data[i + 5].hidden = true;
    }
    pieChart.update();

    const ctx3 = document.getElementById('myChart3');

    let chartData2 = <?= json_encode($chart2Result)?>;

    new Chart(ctx3, {
        type: 'bar',
        data: {
            labels: [
                chartData2[0].prd_nm,
                chartData2[1].prd_nm,
                chartData2[2].prd_nm,
                chartData2[3].prd_nm,
                chartData2[4].prd_nm,
                chartData2[5].prd_nm,
                chartData2[6].prd_nm,
                chartData2[7].prd_nm,
                chartData2[8].prd_nm,
                chartData2[9].prd_nm,
            ],
            datasets: [
                {
                    label: '매출액',
                    data: [
                        chartData2[0].recv_amt,
                        chartData2[1].recv_amt,
                        chartData2[2].recv_amt,
                        chartData2[3].recv_amt,
                        chartData2[4].recv_amt,
                        chartData2[5].recv_amt,
                        chartData2[6].recv_amt,
                        chartData2[7].recv_amt,
                        chartData2[8].recv_amt,
                        chartData2[9].recv_amt,
                    ],
                    borderColor: '#36A2EB',
                    backgroundColor: '#9BD0F5',
                    borderWidth: 1
                },
                {
                    label: '매출원가',
                    data: [
                        chartData2[0].wonga,
                        chartData2[1].wonga,
                        chartData2[2].wonga,
                        chartData2[3].wonga,
                        chartData2[4].wonga,
                        chartData2[5].wonga,
                        chartData2[6].wonga,
                        chartData2[7].wonga,
                        chartData2[8].wonga,
                        chartData2[9].wonga,
                    ],
                    borderColor: '#FF6384',
                    backgroundColor: '#FFB1C1',
                    borderWidth: 1
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            legend:{
                position : 'top'
            },
            tooltips: {
                callbacks: {
                    label: function (tooltipItem, data) {
                        return " " + Comma(data.datasets[tooltipItem.datasetIndex].data[tooltipItem.index]) + "원";
                    }
                },
            },
            scales: {
                y: {
                    beginAtZero: true
                },
                yAxes: [{
                    ticks: {
                        beginAtZero: true,
                        callback: function(value, index) {
                            return Comma(value);
                        }
                    }
                }]
            }
        }
    });

    const ctx4 = document.getElementById('myChart4');

    let chartData3 = <?= json_encode($chart3Result)?>;

    new Chart(ctx4, {
        type: 'bar',
        data: {
            labels: [
                chartData3[0].prd_nm,
                chartData3[1].prd_nm,
                chartData3[2].prd_nm,
                chartData3[3].prd_nm,
                chartData3[4].prd_nm,
                chartData3[5].prd_nm,
                chartData3[6].prd_nm,
                chartData3[7].prd_nm,
                chartData3[8].prd_nm,
                chartData3[9].prd_nm,
            ],
            datasets: [
                {
                    label: '주문수량',
                    data: [
                        chartData3[0].qty,
                        chartData3[1].qty,
                        chartData3[2].qty,
                        chartData3[3].qty,
                        chartData3[4].qty,
                        chartData3[5].qty,
                        chartData3[6].qty,
                        chartData3[7].qty,
                        chartData3[8].qty,
                        chartData3[9].qty,
                    ],
                    borderColor: '#36A2EB',
                    backgroundColor: '#9BD0F5',
                    borderWidth: 1
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            legend:{
                position : 'top'
            },
            tooltips: {
                callbacks: {
                    label: function (tooltipItem, data) {
                        return " " + Comma(data.datasets[tooltipItem.datasetIndex].data[tooltipItem.index]);
                    }
                },
            },
            scales: {
                y: {
                    beginAtZero: true
                },
                yAxes: [{
                    ticks: {
                        beginAtZero: true,
                        callback: function(value, index) {
                            return Comma(value);
                        }
                    }
                }]
            }
        }
    });
</script>

@stop

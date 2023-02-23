@extends('store_with.layouts.layout')
@section('content')
<!-- 메인페이지 4분할 
    왼쪽 상단 - 일별 매출통계/매장별 매출통계의 차트를 탭으로 구성
    오른쪽 상단 - 자주가는 메뉴 추가예정(추후 논의)
    왼쪽 하단 - 공지사항
    오른쪽 하단 - 알리미
-->
   
    <div class="row">
        <div class="col-lg-6">
            <div class="card shadow-none mb-3">
                <div class="filter_wrap" style="width: 100%; height:100%; padding:10px 10px 10px 10px;">
                    <ul class="nav nav-tabs" id="myTab" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link" id="bar-tab" data-toggle="tab" href="#bar" role="tab" aria-controls="bar" aria-selected="false">일별 매출</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="pie-tab" data-toggle="tab" href="#pie" role="tab" aria-controls="pie" aria-selected="false">매장별 매출</a>
                        </li>
                    </ul>
                    <div class="tab-content" id="myTabContent">
                        <div class="tab-pane fade" id="bar" role="tabpanel" aria-labelledby="bar-tab">
                            <div class="card_wrap aco_card_wrap">
                                <div class="card shadow">
                                    <div class="card shadow mb-1">
                                        <div style="margin-top:10px; margin-right:10px">
                                            <a href="#" id="msg_del_btn" onclick="sale_amt_days()"class="btn btn-sm btn-primary shadow-sm mr-1" style="float:right;">더보기</a>
                                        </div>
                                        <div class="chart-container" style="height:30vw; width:42vw">
                                            <canvas id="myChart" ></canvas>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="pie" role="tabpanel" aria-labelledby="pie-tab">
                            <div class="card_wrap aco_card_wrap">
                                <div class="card shadow">
                                    <div class="card-body mt-1">
                                        <a href="#" id="msg_del_btn" onclick="sale_amt_store()"class="btn btn-sm btn-primary shadow-sm mr-1" style="float:right;">더보기</a>
                                        <div class="chart-container" style="height:30vw; width:42vw">
                                            <canvas id="myChart2" ></canvas>
                                        </div>
                                        
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-6" >
            <div class="card shadow-none mb-3">
                <div style="text-align:center; width:100%; height:100%">

                    <h5>자주 사용하는 메뉴 COMMING SOON</h5>
                
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card shadow-none mb-3">
                    <div class="filter_wrap" style="width: 100%; height:100%;">
                        <div id="filter-area" class="card shadow-none mb-0 search_cum_form ty2 last-card">
                            <div class="card-body shadow">
                                <div class="card-title">
                                    <div class="filter_wrap">
                                        <div class="fl_box">
                                            <h6 class="m-0 font-weight-bold">총 <span id="gd-total" class="text-primary">0</span> 건</h6>
                                        </div>
                                        <a href="#" id="msg_del_btn" onclick="notice()"class="btn btn-sm btn-primary shadow-sm mr-1" style="float:right;">더보기</a>
                                        <div class="fr_box">

                                        </div>
                                    </div>
                                </div>
                                <div class="table-responsive">
                                    <div id="div-gd" style="height:auto;width:100%;" class="ag-theme-balham"></div>
                                </div>
                            </div>
                        </div>
                    </div>
            </div>
        </div>
        <div class="col-lg-6" >
            <div class="card shadow-none mb-3">
                <div class="filter_wrap" style="width: 100%; height:100%;">
                    <div id="filter-area" class="card shadow-none mb-0 search_cum_form ty2 last-card">
                        <div class="card-body shadow">
                            <div class="card-title">
                                <div class="filter_wrap">
                                    <div class="fl_box">
                                        <h6 class="m-0 font-weight-bold">총 <span id="gd-alarm-total" class="text-primary">0</span> 건</h6>
                                    </div>
                                    <a href="#" id="msg_del_btn" onclick="msg()"class="btn btn-sm btn-primary shadow-sm mr-1" style="float:right;">더보기</a>
                                </div>
                            </div>
                            <div class="table-responsive">
                                <div id="div-gd-alarm" style="height:calc(100vh - 370px);width:100%;" class="ag-theme-balham"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

<!-- 공지사항 -->
<script language="javascript">
    let columns = [
        {headerName: "제목", field: "subject", width: 300,
            cellRenderer: function(params) {
                return '<a href="/store/stock/stk31/' + params.data.ns_cd +'" rel="noopener">'+ params.value+'</a>';
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

</script>

<script type="text/javascript" charset="utf-8">
    const pApp = new App('',{
        gridId:"#div-gd",
    });
    let gx;

    $(document).ready(function() {
        pApp.ResizeGrid(275, 450);
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
<script language="javascript">
    let columns2 = [
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

</script>

<script type="text/javascript" charset="utf-8">
    const pApp2 = new App('', {
        gridId:"#div-gd-alarm",
    });
    let gx2;

    $(document).ready(function() {
        pApp2.ResizeGrid(275, 450);
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
    }); 

     function showContent(msg_cd) {
        const url = '/store/stock/stk32/showContent?msg_type=receive&msg_cd=' + msg_cd;
        const msg = window.open(url, "_blank", "toolbar=no,scrollbars=yes,resizable=yes,status=yes,top=500,left=500,width=800,height=615");
    }

    function notice() {
        window.location.href = "/store/stock/stk31";
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

<script>
  const ctx = document.getElementById('myChart');


  let edate = '{{$edate}}';
  let sdate = '{{$sdate}}';

  let chartData = <?= json_encode($result)?>;

  let all_date = getDatesStartToLast(sdate, edate);

  new Chart(ctx, {
    type: 'bar',
    data: {
      datasets: [{
        label: '매출액', 
        data: [
            {x: all_date[0], y: chartData[7].sum_amt},
            {x: all_date[1], y: chartData[8].sum_amt},
            {x: all_date[2], y: chartData[9].sum_amt},
            {x: all_date[3], y: chartData[10].sum_amt},
            {x: all_date[4], y: chartData[11].sum_amt},
            {x: all_date[5], y: chartData[12].sum_amt},
            {x: all_date[6], y: chartData[13].sum_amt},
            {x: all_date[7], y: chartData[14].sum_amt},
        ],
        borderWidth: 3
      },{
        label: '매출원가',
        data: [
            {x: all_date[0], y: chartData[7].sum_wonga},
            {x: all_date[1], y: chartData[8].sum_wonga},
            {x: all_date[2], y: chartData[9].sum_wonga},
            {x: all_date[3], y: chartData[10].sum_wonga},
            {x: all_date[4], y: chartData[11].sum_wonga},
            {x: all_date[5], y: chartData[12].sum_wonga},
            {x: all_date[6], y: chartData[13].sum_wonga},
            {x: all_date[7], y: chartData[14].sum_wonga},
        ],
        borderWidth: 3
    }]
    },
    options: {
      responsive: true,
      legend:{
        position : 'right'
      },
      scales: {
        y: {
          beginAtZero: true
        }
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

    new Chart(ctx2, {
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
            legend: {
                position: 'right',
            }
        }
    });
</script>

@stop

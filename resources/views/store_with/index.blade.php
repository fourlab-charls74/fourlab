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
                <div class="card-title">
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
                                            <div class="chart-container" style="position: relative; height:auto; width:auto">
                                                <canvas id="myChart"></canvas>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane fade" id="pie" role="tabpanel" aria-labelledby="pie-tab">
                                <div class="card_wrap aco_card_wrap">
                                    <div class="card shadow">
                                        <div class="card-body mt-1">
                                            
                                            매장별매출통계 그래프 자리
                                            
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card shadow-none mb-3">
                <div class="card-title">
                    <div class="filter_wrap" style="width: 100%; height:100%;">
                        <div style="text-align:center; padding-top: 200px">

                            <h5>자주 사용하는 메뉴 COMMING SOON</h5>
                        
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card shadow-none mb-3">
                <div class="card-title">
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
        </div>
        <div class="col-lg-6">
            <div class="card shadow-none mb-3">
                <div class="card-title">
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
        pApp.ResizeGrid(800);
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
        pApp2.ResizeGrid(800);
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


</script>
<script src="https://unpkg.com/ag-charts-community@2.1.0/dist/ag-charts-community.min.js"></script>
<script language="javascript">
    // var columns3 = [
    // ];
</script>
<script type="text/javascript" charset="utf-8">
    // let chart_data = null;

    // const pApp3 = new App('', {
    //     gridId: "#div-gd-chart1",
    // });
    // let gx3;
    // $(document).ready(function() {
    //     pApp3.ResizeGrid(1000);
    //     pApp3.BindSearchEnter();
    //     let gridDiv3 = document.querySelector(pApp3.options.gridId);
    //     let options = {
    //         getRowStyle: (params) => {
    //             if (params.node.rowPinned === 'top') {
    //                 return { 'background': '#eee' }
    //             }
    //         }
    //     };
    //     gx3 = new HDGrid(gridDiv3, columns3, options);
        
    //     Search3();

    // });

    // function Search3() {
    //     //$('[name=ord_state]').val(10);

    //     let data = $('form[name="search"]').serialize();

    //     // console.log(data);


    //     gx3.Request('/store/main_chart1', data, -1, function(data) {
    //         chart_data = data.body;
    //         drawCanvas();
    //     });
    // }

    // function drawCanvas() {
    //     drawCanvasByDate();
    // }

    // function drawCanvasByDate() {
    //     $('#opt_chart').html('');

    //     let beforeRowMonth = null;

    //     chart_data.sort(function(a, b) {
    //         if (a.date < b.date) {
    //             return -1;
    //         }
    //         if (a.date > b.date) {
    //             return 1;
    //         }

    //         return 0;
    //     });

    //     chart_data.forEach(function(row) {
    //         if (beforeRowMonth !== row.month || beforeRowMonth === null) {
    //             row.chart_x_str = row.month + "." + row.day;
    //             beforeRowMonth = row.month;
    //             return;
    //         }

    //         row.chart_x_str = row.day;
    //     });

    //     var options = {
    //         container: document.getElementById('opt_chart'),
    //         title: {
    //             text: "일별 매출 통계",
    //         },
    //         data: chart_data,
    //         series: [{
    //             type: 'column',
    //             xKey: 'chart_x_str',
    //             yKeys: ['sum_amt', 'sum_wonga'],
    //             yNames: [' 매출액', '매출원가'],
    //             grouped: true,
    //             fills: ['#556ee6', '#2797f6'],
    //             strokes: ['#556ee6', '#2797f6']
    //             // highlightStyle : {
    //             //   fill :
    //             // }
    //         }],
    //     };
    //     agCharts.AgChart.create(options);
    // }

    function notice() {
        window.location.href = "/store/stock/stk31";
    }

    function msg() {
        window.location.href = "/store/stock/stk32";
    }

</script>

<!-- 차트 -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
  const ctx = document.getElementById('myChart');


  let edate = '{{$edate}}';
  let sdate = '{{$sdate}}';

  let all_date = getDatesStartToLast(sdate, edate);

  new Chart(ctx, {
    type: 'bar',
    data: {
      datasets: [{
        label: '매출액', 
        data: [
            {x: all_date[0], y: 10},
            {x: all_date[1], y: 10},
            {x: all_date[2], y: 10},
            {x: all_date[3], y: 10},
            {x: all_date[4], y: 10},
            {x: all_date[5], y: 10},
            {x: all_date[6], y: 10},
            {x: all_date[7], y: 10},
        ],
        borderWidth: 3
      },{
        label: '매출원가',
        data: [
            {x: all_date[0], y: 10},
            {x: all_date[1], y: 10},
            {x: all_date[2], y: 10},
            {x: all_date[3], y: 10},
            {x: all_date[4], y: 10},
            {x: all_date[5], y: 10},
            {x: all_date[6], y: 10},
            {x: all_date[7], y: 10},
        ],
        borderWidth: 3
    }]
    },
    options: {
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

</script>

@stop

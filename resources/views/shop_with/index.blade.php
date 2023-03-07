@extends('shop_with.layouts.layout')
@section('content')
<!-- 메인페이지 4분할 
    왼쪽 상단 - 일별 매출통계/매장별 매출통계의 차트를 탭으로 구성
    오른쪽 상단 - 자주가는 메뉴 추가예정(추후 논의)
    왼쪽 하단 - 공지사항
    오른쪽 하단 - 알리미
-->
    <div class="row">
        <div class="col-lg-6">
            <div class="card shadow mb-3">
                <div class="card-body">
                    <ul class="nav nav-tabs" id="myTab" role="tablist">
                        <li class="nav-item" role="presentation">
                            <a class="nav-link" id="bar-tab" data-toggle="tab" href="#bar" role="tab" aria-controls="bar" aria-selected="false">일별 매출</a>
                        </li>
                    </ul>
                    <div class="tab-content" id="myTabContent" style="height:100%">
                        <div class="tab-pane fade" id="bar" role="tabpanel" aria-labelledby="bar-tab">
                            <div class="card shadow mb-1">
                                <div style="margin-top:24px; margin-right:20px">
                                    <a href="#" id="msg_del_btn" onclick="sale_amt_days()"class="btn btn-sm btn-primary shadow-sm mr-1" style="float:right;">더보기</a>
                                </div>
                                <canvas id="myChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-6" >
            <div class="card shadow mb-3">
                <div class="card-body">
                    <ul class="nav nav-tabs" id="myTab" role="tablist">
                        <li class="nav-item" role="presentation">
                            <a class="nav-link" id="order_amt-tab" data-toggle="tab" href="#order_amt" role="tab" aria-controls="order_amt" aria-selected="false">주문금액</a>
                        </li>
                        <li class="nav-item" role="presentation">
                            <a class="nav-link" id="order_qty-tab" data-toggle="tab" href="#order_qty" role="tab" aria-controls="order_qty" aria-selected="false">주문수량</a>
                        </li>
                    </ul>
                    <div class="tab-content" id="myTabContent" style="height:100%">
                        <div class="tab-pane fade" id="order_amt" role="tabpanel" aria-labelledby="order_amt-tab">
                            <div class="card shadow mb-1" style="margin-top:26px">
                                <div style="text-align: right;">
                                    <span style="font-size: 17px; font-weight:bold">[ {{@$sdate}} ~ {{@$edate}} ]</span>
                                </div>
                                <canvas id="myChart3"></canvas>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="order_qty" role="tabpanel" aria-labelledby="order_qty-tab">
                            <div class="card shadow mb-1" style="margin-top:26px">
                                <div style="text-align: right;">
                                    <span style="font-size: 17px; font-weight:bold">[ {{@$sdate}} ~ {{@$edate}} ]</span>
                                </div>
                                <canvas id="myChart4" ></canvas>
                            </div>
                        </div>
                    </div>
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
                                <div id="div-gd-alarm" style="height:calc(100vh - 370px);width:100%;" class="ag-theme-balham darkmode"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

<script>
    let store_cd = "{{Auth('head')->user()->store_cd}}";
    let grade = "{{Auth('head')->user()->grade}}";
        
    $(document).ready(function(){
        openNoticePopup();
        openMsgPopup();
        $('#order_amt-tab').trigger("click");
        $('#bar-tab').trigger("click");
    }); 
    
    function openNoticePopup() {
        if( grade=="P" && store_cd != "" ) {
            $.ajax({
				async: true,
				type: 'get',
				url: '/shop/stock/stk31/popup_chk',
				data: {
					"store_cd": store_cd
				},
				success: function(data) {
					if (data.code == 200) {
                        $.each(data.nos, function(i, item){
                            const url = '/shop/stock/stk31/popup_notice/' + item.ns_cd;
                            const msg = window.open(url, "_blank", "toolbar=no,scrollbars=yes,resizable=yes,status=yes,top=300,left=300,width=600,height=450");
                        });
					} else {
						alert("공지사항 팝업을 표시할 수 없습니다.\n관리자에게 문의해 주십시오.");
					}
				},
				error: function(request, status, error) {
					alert("공지사항 팝업을 표시할 수 없습니다.\n관리자에게 문의해 주십시오.");
					console.log("error")
				}
			});
        }
    }

    function openMsgPopup() {
        if( grade=="P" && store_cd != "" ) {
            $.ajax({
				async: true,
				type: 'get',
				url: '/shop/stock/stk32/popup_chk',
				data: {
					"store_cd": store_cd
				},
				success: function(data) {
					if (data.code == 200) {
                        $.each(data.msgs, function(i, item){
                            const url = '/shop/stock/stk32/showContent?msg_type=pop&msg_cd=' + item.msg_cd;
                            const msg = window.open(url, "_blank", "toolbar=no,scrollbars=yes,resizable=yes,status=yes,top=500,left=500,width=800,height=615");
                        });
					} else {
						alert("메세지 팝업을 표시할 수 없습니다.\n관리자에게 문의해 주십시오.");
					}
				},
				error: function(request, status, error) {
					alert("메세지 팝업을 표시할 수 없습니다.\n관리자에게 문의해 주십시오.");
					console.log("error")
				}
			});
        }
    }    
</script>

<!-- 공지사항 -->
<script language="javascript">
     let columns = [
        {headerName: "제목", field: "subject", width: 300,
            cellRenderer: function(params) {
                return '<a href="/shop/stock/stk31/notice/' + params.data.ns_cd +'" rel="noopener">'+ params.value+'</a>';
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

                console.log(params.data);
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
        pApp.ResizeGrid(790);
        let gridDiv = document.querySelector(pApp.options.gridId);
        gx = new HDGrid(gridDiv, columns);
        pApp.BindSearchEnter();
        Search();
    });

    function Search() {
        gx.Request('/shop/main');
    }

</script>


<!-- 알리미 -->
<script language="javascript">
    let columns2 = [
           
            {field: "sender_cd", hide: true},
            {headerName: "발신처", field: "sender_nm", width:150},
            {headerName: "연락처", field: "mobile", width: 80, cellClass: 'hd-grid-code'},
            {headerName: "내용", field: "content", width: 300, cellStyle: {'text-overflow': 'ellipsis'},
                cellRenderer: params => {
                    return "<a href='#' onclick='showContent(" + params.data.msg_cd +")'>"+params.value+"</a>";
                },
            },
            {headerName: "받은 날짜", field: "rt", width: 110, cellClass: 'hd-grid-code'},
            {headerName: "확인여부", field: "check_yn", width: 110, cellClass: 'hd-grid-code',
                cellStyle: (params) => ({color: params.data.check_yn == 'Y' ? 'blue' : 'red'})
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
        pApp2.ResizeGrid(790);
        let gridDiv2 = document.querySelector(pApp2.options.gridId);
        gx2 = new HDGrid(gridDiv2, columns2);
        pApp2.BindSearchEnter();
        Search_alarm();
    });

    function Search_alarm() {
        gx2.Request('/shop/main_alarm');
    }
</script>


<script>

     function showContent(msg_cd) {
        const url = '/shop/stock/stk32/showContent?msg_type=receive&msg_cd=' + msg_cd;
        const msg = window.open(url, "_blank", "toolbar=no,scrollbars=yes,resizable=yes,status=yes,top=500,left=500,width=800,height=615");
    }

    function notice() {
        window.location.href = "/shop/stock/stk31";
    }

    function msg() {
        window.location.href = "/shop/stock/stk32";
    }

    function sale_amt_store() {
        window.location.href = "/shop/sale/sal26";
    }
   
    function sale_amt_days() {
        window.location.href = "/shop/sale/sal24";
    }


</script>

<!-- 차트 -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.3/Chart.bundle.min.js"></script>

<script>
  const ctx = document.getElementById('myChart');


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
      datasets: [{
        label: '매출액',
        data: [
            chartData[7].sum_amt,
            chartData[8].sum_amt,
            chartData[9].sum_amt,
            chartData[10].sum_amt,
            chartData[11].sum_amt,
            chartData[12].sum_amt,
            chartData[13].sum_amt,
            chartData[14].sum_amt,
        ],
        borderColor: '#36A2EB',
        backgroundColor: '#9BD0F5',
        borderWidth: 1
      }]
    },
    options: {
        // responsive: true,
        animation: {
            easing:'easeInOutQuad',
        }, 
        legend:{
            position : 'top'
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
    ]
    },
    options: {
        legend:{
            position : 'top'
        },
        scales: {
            yAxes: [{
                ticks: {
                    beginAtZero: true,
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
        // responsive: true,
        legend:{
            position : 'top'
        },
        scales: {
            yAxes: [{
                ticks: {
                    beginAtZero: true,
                }
            }]
        }
    }
  });

</script>

@stop

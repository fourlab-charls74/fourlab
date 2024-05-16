@extends('shop_with.layouts.layout')
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
            </ul>
            <div class="tab-content h-100" id="myTabContent">
                <div class="tab-pane fade h-100" id="bar" role="tabpanel" aria-labelledby="bar-tab">
                    <div class="h-100" style="max-height: 83%;">
                        <div style="margin-top:20px;">
                            <a href="#" id="msg_del_btn" onclick="sale_amt_days()"class="btn btn-sm btn-primary shadow-sm mr-1" style="float:right;">더보기</a>
                        </div>
                        <canvas id="myChart" class="my-chart"></canvas>
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
            <div class="tab-content h-100" id="myTabContent" style="margin-top: 12px;">
                <div class="tab-pane fade h-100" id="order_amt" role="tabpanel" aria-labelledby="order_amt-tab">
                    <div class="h-100" style="max-height: 83%;">
                        <div style="text-align: right;">
                            <span style="font-size: 17px;">[ {{@$sdate2}} ~ {{@$edate2}} ]</span>
                        </div>
                        <canvas id="myChart3" class="my-chart"></canvas>
                    </div>
                </div>
                <div class="tab-pane fade h-100" id="order_qty" role="tabpanel" aria-labelledby="order_qty-tab">
                    <div class="h-100" style="max-height: 83%;">
                        <div style="text-align: right;">
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
                                    <!-- <h6 class="m-0 font-weight-bold">총 <span id="gd-total" class="text-primary">0</span> 건</h6> -->
	                                <h6 class="m-0 font-weight-bold"><i class="bx bx-notepad fs-14 mr-2"></i>공지사항</h6>
                                </div>
                                <a href="#" id="msg_del_btn" onclick="notice()"class="btn btn-sm btn-primary shadow-sm" style="float:right;">더보기</a>
                                <div class="fr_box">

                                </div>
                            </div>
                        </div>
                        <div class="table-responsive" style="height: 90%;">
                            <div id="div-gd" style="height:100%;" class="ag-theme-balham"></div>
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
                                    <!-- <h6 class="m-0 font-weight-bold">총 <span id="gd-alarm-total" class="text-primary">0</span> 건</h6> -->
	                                <h6 class="m-0 font-weight-bold"><i class="bx bx-bell fs-14 mr-2"></i>알리미</h6>
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

<script>
    $(document).ready(function(){
        $('#order_amt-tab').trigger("click");
        $('#bar-tab').trigger("click");
        //openNoticePopup();
    });
</script>

<!-- 공지사항 -->
<script type="text/javascript" charset="utf-8">
     const columns = [
        {headerName: "제목", field: "subject", width: 0,
            cellRenderer: function(params) {
				if (params.data.check_new_notice == 'true') {
					return '<a href="/shop/community/comm01/notice/' + params.data.ns_cd +'" rel="noopener">'+ `<span class="blink" style="color:red;font-weight: bold" >[ NEW ] </span>` + params.value +`${params.data.attach_file_yn === 'Y' ? `<i class="bi bi-paperclip"></i>` : '' }</a>`;
				} else {
					return '<a href="/shop/community/comm01/notice/' + params.data.ns_cd +'" rel="noopener">'+ params.value +`${params.data.attach_file_yn === 'Y' ? `<i class="bi bi-paperclip"></i>` : '' }</a>`;
				}

                //return '<a href="/shop/community/comm01/notice/' + params.data.ns_cd +'" rel="noopener">'+ params.value+'</a>';
            }
        },
        {headerName: "이름", field: "admin_nm",  width: 70, cellClass: 'hd-grid-code'},
        {headerName: "조회수", field: "cnt", type: 'currencyType',width: 50},
        {headerName: "등록일시", field: "rt", type:"DateTimeType"},
        // {headerName: "전체공지여부", field: "all_store_yn", width: 80, cellClass: 'hd-grid-code',
        //     cellStyle: params => {
        //         if(params.data.all_store_yn == 'Y'){
        //             return {color:'red'}
        //         }else{
        //             return {color:'blue'}
        //         }
        //     },
        //     cellRenderer: function(params){
        //         if(params.data.stores == null){
        //             return params.data.all_store_yn = "Y";
        //         }else{
        //             return params.data.all_store_yn = "N";
        //         }
        //     }
        // },
        // {headerName: "공지매장", field: "store_nm", width: 0, cellRenderer: (params) => params.data.stores},
        {headerName: "글번호", field: "ns_cd", hide:true },
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
        gx.Request('/shop/main');
    }

</script>

<!-- 알리미 -->
<script type="text/javascript" charset="utf-8">
    const columns2 = [

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

    const pApp2 = new App('', { gridId:"#div-gd-alarm" });
    let gx2;

    $(document).ready(function() {
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
        const url = '/shop/community/comm02/showContent?msg_type=receive&msg_cd=' + msg_cd;
        const msg = window.open(url, "_blank", "toolbar=no,scrollbars=yes,resizable=yes,status=yes,top=500,left=500,width=800,height=615");
    }

    function notice() {
        window.location.href = "/shop/community/comm01/notice";
    }

    function msg() {
        window.location.href = "/shop/community/comm02";
    }

    function sale_amt_store() {
        window.location.href = "/shop/sale/sal26";
    }

    function sale_amt_days() {
        window.location.href = "/shop/sale/sal24";
    }

    // 공지사항팝업
    function openNoticePopup() {
        if( grade=="P" && store_cd != "" ) {
            $.ajax({
				async: true,
				type: 'get',
				url: '/shop/community/comm01/popup_chk',
				data: {
					"store_cd": store_cd
				},
				success: function(data) {
					if (data.code === 200) {
                        $.each(data.nos, function(i, item){
                            const url = '/shop/community/comm01/popup_notice/' + item.ns_cd;
                            const msg = window.open(url, "_blank", "toolbar=no,scrollbars=yes,resizable=yes,status=yes,top=300,left=300,width=600,height=500");
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
  
  let labels = [];
  for (let i=1;i<all_date.length;i++) {
	  labels.push(all_date[i]);
  }
  
  let sum_amt_datas = [];
  
  for (let i=1;i<chartData.length;i++) {
	  const sum_amt = chartData[i]?.sum_amt ?? 0;
	  sum_amt_datas.push(sum_amt);
  }

  new Chart(ctx, {
    type: 'bar',
    data: {
      labels: labels,
      datasets: [{
        label: '매출액',
        data: sum_amt_datas,
        borderColor: '#36A2EB',
        backgroundColor: '#9BD0F5',
        borderWidth: 1
      }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
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
        },
        tooltips: {
            callbacks: {
                label: function (tooltipItem, data) {
                    return " " + Comma(data.datasets[tooltipItem.datasetIndex].data[tooltipItem.index]) + "원";
                }
            },
        },
        scales: {
            yAxes: [{
                ticks: {
                    beginAtZero: true,
                    callback: function(value, index) {
                        return Comma(value);
                    }
                }
            }]
        }
    },
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

  let prd_nm_labels = [];
  let recv_amt_datas = [];
  for (let i=0;i<chartData2.length;i++) {
	  const prd_nm = chartData2[i]?.prd_nm ?? '';
	  const recv_amt = chartData2[i]?.recv_amt ?? 0;

	  prd_nm_labels.push(prd_nm);
	  recv_amt_datas.push(recv_amt);
  }

  new Chart(ctx3, {
    type: 'bar',
    data: {
      labels: prd_nm_labels,
      datasets: [
        {
        label: '매출액',
        data: recv_amt_datas,
        borderColor: '#36A2EB',
        backgroundColor: '#9BD0F5',
        borderWidth: 1
      },
    ]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        legend:{
            position : 'top'
        },
        scales: {
            yAxes: [{
                ticks: {
                    beginAtZero: true,
                }
            }]
        },
        tooltips: {
            callbacks: {
                label: function (tooltipItem, data) {
                    return " " + Comma(data.datasets[tooltipItem.datasetIndex].data[tooltipItem.index]) + "원";
                }
            },
        },
        scales: {
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

  let prd_nm_chart3_labels = [];
  let qty_datas = [];
  for (let i=0;i<chartData3.length;i++) {
	  const prd_nm = chartData3[i]?.prd_nm ?? '';
	  const qty = chartData3[i]?.qty ?? 0;

	  prd_nm_chart3_labels.push(prd_nm);
	  qty_datas.push(qty);
  }


  new Chart(ctx4, {
    type: 'bar',
    data: {
      labels: prd_nm_chart3_labels,
      datasets: [
        {
        label: '주문수량',
        data: qty_datas,
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
        scales: {
            yAxes: [{
                ticks: {
                    beginAtZero: true,
                }
            }]
        },
        tooltips: {
            callbacks: {
                label: function (tooltipItem, data) {
                    return " " + Comma(data.datasets[tooltipItem.datasetIndex].data[tooltipItem.index]);
                }
            },
        },
        scales: {
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

	@if( $expire_release_cnt > 0)
		alert('한달이 지난 입고처리해야 할 출고관리 자료가 {{ $expire_release_cnt }}건 존재합니다. \n출고관리 페이지로 이동합니다.');
		location.href	= '/shop/stock/stk10';
	@endif

</script>

@stop

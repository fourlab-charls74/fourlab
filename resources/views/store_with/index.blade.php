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
                    <div class="filter_wrap" style="height:500px; padding:10px 10px 10px 10px;">
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
                                        <div class="card-body mt-1">
                                            
                                            일별매출통계 그래프 자리
                                            @include('store_with.sale.sal24_chart')
                                            
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
                    <div class="filter_wrap" style="height:500px;">
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
                    <div class="filter_wrap" style="height:500px;">
                        <div style="text-align:center; padding-top: 200px">
                            <h5>공지사항 COMMING SOON</h5>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card shadow-none mb-3">
                <div class="card-title">
                    <div class="filter_wrap" style="height:500px;">
                        <div style="text-align:center; padding-top: 200px">
                            <h5>알리미 COMMING SOON</h5>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>



<script>
    $(document).ready(function(){
        $('#bar-tab').trigger("click");
        
        drawCanvas();
    }); 


    function drawCanvas() {
        switch ($("#chart-type").val()) {
            case "date":
                drawCanvasByDate();
                break;
        }
    }

    function drawCanvasByDate() {
        $('#opt_chart').html('');

        let beforeRowMonth = null;

        chart_data.sort(function(a, b) {
            if (a.date < b.date) {
                return -1;
            }
            if (a.date > b.date) {
                return 1;
            }

            return 0;
        });

        chart_data.forEach(function(row) {
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
            series: [{
                type: 'column',
                xKey: 'chart_x_str',
                yKeys: ['sum_amt', 'sum_wonga'],
                yNames: [' 매출액', '매출원가'],
                grouped: true,
                fills: ['#556ee6', '#2797f6'],
                strokes: ['#556ee6', '#2797f6']
            }],
        };
        agCharts.AgChart.create(options);
    }

</script>

@stop

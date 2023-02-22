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
            <div class="card shadow-none mb-3">
                <div class="card-title">
                    <div class="filter_wrap" style="height:390px; padding:10px 10px 10px 10px;">
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
                    <div class="filter_wrap" style="height:390px;">
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
                    <div class="filter_wrap" style="height:390px;">
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
                    <div class="filter_wrap" style="height:390px;">
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
        openNoticePopup();
    }); 
    
    function openNoticePopup() {
        let store_cd = "{{Auth('head')->user()->store_cd}}";
        let grade = "{{Auth('head')->user()->grade}}";

        if( grade=="P" && store_cd != "" ) {
            $.ajax({
				async: true,
				type: 'get',
				url: '/shop/stock/stk31/popup_notice',
				data: {
					"store_cd": store_cd
				},
				success: function(data) {
					if (data.code == 200) {
                        $.each(data.nos, function(i, item){
                            const url = '/shop/stock/stk31/popup_notice/' + item.ns_cd;
                            const msg = window.open(url, "_blank", "toolbar=no,scrollbars=yes,resizable=yes,status=yes,top=500,left=500,width=600,height=450");
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

@stop

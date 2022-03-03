@extends('head_with.layouts.layout-nav')
@section('title','출첵이벤트')
@section('content')
<div class="container-fluid py-3">
    <div class="page_tit d-flex align-items-center justify-content-between">
        <div>
            <h3 class="d-inline-flex">출첵이벤트</h3>
            <div class="d-inline-flex location">
                <span class="home"></span>
                <span>/ 프로모션</span>
                <span>/ 출첵이벤트</span>
            </div>
        </div>
        <div>
        <a href="#" onclick="window.close()" class="btn btn-sm btn-primary shadow-sm">닫기</a>
        </div>
    </div>
    @csrf

	<form method="get" name="search" onsubmit="return false;">
		<input type="hidden" name="idx" id="idx" value="{{ $idx }}">
		<div id="search-area" class="search_cum_form">
            <div class="card mb-3">
                <div class="d-flex card-header justify-content-between">
                    <h4>검색</h4>
                    <div class="flax_box">
                        <button id="search_sbtn" onclick="Search();" class="btn btn-sm btn-primary shadow-sm mr-1"><i class="fas fa-search fa-sm text-white-50"></i> 조회</button>
                        <div id="search-btn-collapse" class="btn-group mb-0 mb-sm-0"></div>
                    </div>
                </div>
				<div class="card-body">
					<div class="row">
						<div class="col-lg-4 inner-td">
							<div class="form-group">
								<label for="">아이디</label>
								<div class="flax_box">
									<input type='text' class="form-control form-control-sm search-all" name='userid' value=''>
								</div>
							</div>
						</div>

						<div class="col-lg-4 inner-td">
							<div class="form-group">
								<label for="ord_no">출석횟수</label>
								<div class="form-inline">
									<div class="form-inline-inner input_box">
										<div class="form-group">
											<input type='text' class="form-control form-control-sm search-all ac-style-no2" name='attd_cnt_from' value="">
										</div>
									</div>
									<span class="text_line">~</span>
									<div class="form-inline-inner input_box">
										<div class="form-group">
											<input type="text" class="form-control form-control-sm search-all" name="attd_cnt_to" value="">
										</div>
									</div>
								</div>
							</div>
						</div>
						<div class="col-lg-4 inner-td">
							<div class="form-group">
								<label for="">출석일자</label>
								<div class="form-inline">
									<div class="docs-datepicker form-inline-inner input_box">
										<div class="input-group">
											<input type="text" class="form-control form-control-sm docs-date" name="sdate" value="{{ $sdate }}" autocomplete="off" disable>
											<div class="input-group-append">
												<button type="button" class="btn btn-outline-secondary docs-datepicker-trigger p-0 pl-2 pr-2" disable>
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
					</div>
				</div>
			</div>
			<div class="resul_btn_wrap mb-3">
				<a href="#" id="search_sbtn" onclick="Search();" class="btn btn-sm btn-primary shadow-sm mr-1"><i class="fas fa-search fa-sm text-white-50"></i> 조회</a>
				<div class="search_mode_wrap btn-group mr-2 mb-0 mb-sm-0"></div>
			</div>
		</div>
	</form>


	<div id="filter-area" class="card shadow-none mb-0 search_cum_form ty2 last-card">
		<div class="card-body shadow">
			<div class="card-title">
				<div class="filter_wrap">
					<div class="fl_box">
						<h6 class="m-0 font-weight-bold">총 <span id="gd-total" class="text-primary">0</span> 건</h6>
					</div>
				</div>
			</div>
			<div class="table-responsive">
				<div id="div-gd" style="height:calc(100vh - 370px);width:100%;" class="ag-theme-balham"></div>
			</div>
		</div>
	</div>
</div>

<script type="text/javascript" charset="utf-8">
	var columns = [
		{headerName: '#', width:50, maxWidth: 90,type:'NumType'},
		{field:"user_id" , headerName:"아이디", width:100, type:"HeadUserType"  },
		{field:"attend_cnt" , headerName:"출석횟수",
			cellRenderer: function(params) {
				if(params.value !== undefined && params.value!== null){
					return '<a href="#" onClick="popAttend(\''+ params.data.user_id +'\')">'+ params.value +'</a>';
				}
			}
		},
		
		{field:"regular_attend_cnt" , headerName:"개근횟수"},
		{field:"attend_point" , headerName:"출석적립금", type: 'currencyType', width:90},
		{field:"regular_attend_point", headerName:"개근적립금", width:90, type: 'currencyType'},
		{field:"attend" , headerName:"최근출석일자", width:100},
		
		{field:"support_amt" , headerName:"쇼핑지원금", type: 'currencyType', },
		
		{field:"is_winner" , headerName:"당첨여부",
			cellRenderer: function(params) {
				if(params.value !== undefined && params.value!== null){
					return '<a href="#" onClick="winnerChange(\''+ params.data.user_id +'\', \''+ params.value +'\')">'+ params.value +'</a>';
				}
			}
		},
		{headerName: "", field: "nvl"}
	];

	const pApp = new App('', { gridId: "#div-gd" });
	const gridDiv = document.querySelector(pApp.options.gridId);
	const gx = new HDGrid(gridDiv, columns);

	pApp.ResizeGrid();


	function Search() {
		var idx	= "{{ $idx }}";
		let data = $('form[name="search"]').serialize();
		gx.Request('/head/promotion/prm20/mem_search/'+idx, data,1);
	}

	function popAttend(val){
		var idx	= "{{ $idx }}";
		const url='/head/promotion/prm20/attend/' + idx +"?user_id="+ val;
		window.open(url,"_blank","toolbar=no,scrollbars=yes,resizable=yes,status=yes,top=500,left=500,width=1200,height=800");
	}
	
	//당첨여부 변경 함수

	function winnerChange(user_id,is_winner){
		var idx	= "{{ $idx }}";
		var return_winner = (is_winner == "Y")? "N":"Y";
		
		$.ajax({
            async: true,
            type: 'put',
            url: '/head/promotion/prm20/winner/'+user_id,
			data: {
				'idx' : idx,
				'is_winner' : is_winner
			},
            success: function (data) {
				if(data.return_code == 1){
					gx.gridOptions.api.forEachNode(function (node) {
						if(node.data.user_id == user_id){
							node.setDataValue('is_winner', return_winner);
						}
					});
				}else{
					alert("당첨여부 변경 시 장애가 발생했습니다. 다시 시도하여 주십시요..");

				}

            },
            complete:function(){
                //_grid_loading = false;
            },
            error: function(request, status, error) {
                console.log("error");
                console.log("code:"+request.status+"\n"+"message:"+request.responseText+"\n"+"error:"+error);
            }
        });
		/*
		var param = "&CMD=winner";
		param += "&IDX=" + idx;
		param += "&USER_ID=" + user_id;
		param += "&IS_WINNER=" + is_winner;
		var http = new xmlHttp();
		http.onexec("prm04.php", "POST", param, true, cbWinner);

		ProcessingPopupShowHide("show");
		*/
	}

	function cbWinner(res){
		
		ProcessingPopupShowHide();

		if(res.responseText){
			var row = gx.getRow();
			var col = gx.getCol();
			if(gx.Cell(0, row, col) == "Y"){
				gx.Cell(0, row, col, "N");
			}else{
				gx.Cell(0, row, col, "Y");
			}
		}else{
			alert("당첨여부 변경 시 장애가 발생했습니다. 다시 시도하여 주십시요..");
		}
	}


	$(function(){
		Search();

	});

</script>
@stop

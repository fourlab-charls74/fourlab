@extends('head_with.layouts.layout')
@section('title','출첵이벤트')
@section('content')

<div class="page_tit">
    <h3 class="d-inline-flex">출첵</h3>
    <div class="d-inline-flex location">
        <span class="home"></span>
        <span>/ 프로모션</span>
        <span>/ 출첵이벤트</span>
    </div>
</div>
<form method="get" name="search" onsubmit="return false">
	<div id="search-area" class="search_cum_form">
        <div class="card mb-3">
            <div class="d-flex card-header justify-content-between">
                <h4>검색</h4>
                <div class="flax_box">
                    <a href="#" id="search_sbtn" onclick="Search();" class="btn btn-sm btn-primary shadow-sm mr-1"><i class="fas fa-search fa-sm text-white-50"></i> 조회</a>
                    <a href="#" onclick="Add();" class="btn btn-sm btn-outline-primary shadow-sm mr-1 pl-2"><i class="bx bx-plus fs-16"></i> 추가</a>
                    <div id="search-btn-collapse" class="btn-group mb-0 mb-sm-0"></div>
                </div>
            </div>
			<div class="card-body">
				
				<!-- 제목/사용여부 -->
				<div class="search-area-ext  row">
					<div class="col-lg-4 inner-td harf">
						<div class="form-group">
							<label for="">제목</label>
							<div class="flax_box">
								<input type='text' class="form-control form-control-sm search-all" name='subject' value=''>
							</div>
						</div>
					</div>

					<div class="col-lg-4 inner-td harf">
						<div class="form-group">
							<label for="ord_no">사용여부</label>
							<div class="flax_box">
								 <select name="use_yn" id="use_yn" class="form-control form-control-sm">
									<option value="">모두</option>
									<option value="Y" selected>사용</option>
									<option value="N">미사용</option>
								</select>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
        <div class="resul_btn_wrap mb-3">
            <a href="#" id="search_sbtn" onclick="Search();" class="btn btn-sm btn-primary shadow-sm mr-1"><i class="fas fa-search fa-sm text-white-50"></i> 조회</a>
            <a href="#" class="btn btn-sm btn-outline-primary shadow-sm mr-1 pl-2 add-btn"><i class="bx bx-plus fs-16"></i> 추가</a>
            <div class="search_mode_wrap btn-group mr-2 mb-0 mb-sm-0"></div>
        </div>
	</div>
</form>

<div class="card shadow mb-3">
    <div class="card-body shadow">
        <div class="card-title">
            <div class="filter_wrap">
                <div class="fl_box">
                    <h6 class="m-0 font-weight-bold">총 <span id="gd-total" class="text-primary">0</span> 건</h6>
                </div>
            </div>
        </div>
        <div class="table-responsive">
            <div id="div-gd" style="height:calc(100vh - 370px); width:100%;" class="ag-theme-balham"></div>
        </div>
    </div>
</div>

<script language="javascript">
	var columns = [
		{headerName: '#', width:50, maxWidth: 70,type:'NumType'},
		{field:"idx" , headerName:"이벤트번호", width: 90  },
		{field:"title" , headerName:"이벤트제목", width: 200,
			cellRenderer: function(params) {
				if(params.value !== undefined && params.value!== null){
	                return '<a href="#" onClick="popEvent('+ params.data.idx +')">'+ params.value+'</a>'
				}
            }
		},
		{headerName:"기간", width:120, 
			children : [
				{
					headerName : "시작일자",
					field : "start_dt",
					width:80
				},
				{
					headerName : "종료일자",
					field : "end_dt",
					width:80
				}
			]
		},
		{field:"attend_point_type" , headerName:"적립금지급시점"},
		{field:"attend_point" , headerName:"적립금액", type: 'currencyType'},
		{field:"first_attend_yn", headerName:"첫출근", width:80},
		{field:"first_attend_point" , headerName:"첫출근 적립금액", type: 'currencyType'},
		{headerName:"개근", width:120, 
			children : [
				{
					headerName : "개근일자",
					field : "regular_attend_day",
					width:90,
					type: 'currencyType'
				},
				{
					headerName : "적립금액",
					field : "regular_attend_point",
					type: 'currencyType',
					width:90
				}
			]
		},
		{field:"attend_cnt" , headerName:"참여회원수", type: 'currencyType',
			cellRenderer: function(params) {
				if(params.value !== undefined && params.value!== null){
					return '<a href="#" onClick="popMember('+ params.data.idx +')">'+ params.value +'</a>';
				}
			}
		},
		{field:"attend_today_cnt" , headerName:"금일출석체크회원수", type: 'currencyType'},
		{field:"bet" , headerName:"배팅", type: 'currencyType'},
		{headerName:"쇼핑지원금", width:120, 
			children : [
				{
					headerName : "사용여부",
					field : "support_point_yn",
					width:80
				},
				{
					headerName : "시작일자",
					field : "support_point_sday",
					width:80
				},
				{
					headerName : "종료일자",
					field : "support_point_eday",
					width:80
				},
				{
					headerName : "지원금액",
					field : "support_point",
					type: 'currencyType',
					width:80
				},
				{
					headerName : "유효기간",
					field : "support_point_expireday",
					width:80
				},
				{
					headerName : "총지원금액",
					field : "support_point_amt",
					type: 'currencyType',
					width:100
				},
				{
					headerName : "금일지원금액",
					field : "support_point_today_amt",
					type: 'currencyType',
					width:110
				}
			]
		},
		{field:"is_use" , headerName:"사용여부"},
		{field:"ut" , headerName:"최근수정일시", width:130},
		{headerName: "", field: "nvl"}
	];

	const pApp = new App('', { gridId: "#div-gd" });
	const gridDiv = document.querySelector(pApp.options.gridId);
	const gx = new HDGrid(gridDiv, columns);
	pApp.ResizeGrid();

	function Add(){
		const url='/head/promotion/prm20/detail/';
		window.open(url,"_blank","toolbar=no,scrollbars=yes,resizable=yes,status=yes,top=500,left=500,width=970,height=800");
	}

	function Search() {
		let data = $('form[name="search"]').serialize();
		gx.Request('/head/promotion/prm20/search', data,1);
	}

	function popMember(val){
		const url='/head/promotion/prm20/member/' + val;
		window.open(url,"_blank","toolbar=no,scrollbars=yes,resizable=yes,status=yes,top=500,left=500,width=970,height=800");
	}

	function popEvent(val){
		const url='/head/promotion/prm20/detail/' + val;
		window.open(url,"_blank","toolbar=no,scrollbars=yes,resizable=yes,status=yes,top=500,left=500,width=970,height=800");
	}

	$(document).ready(function() {
		Search();

		$("[name=subject]").on("keypress", function(e) {
			if(e.originalEvent.code !== "Enter") return;
			Search();
		});
	});
</script>
@stop
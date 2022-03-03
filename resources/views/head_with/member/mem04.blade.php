@extends('head_with.layouts.layout')
@section('title','적립금현황')
@section('content')
<div class="page_tit">
    <h3 class="d-inline-flex">적립금현황</h3>
    <div class="d-inline-flex location">
        <span class="home"></span>
        <span>/ 회원&amp;CRM</span>
        <span>/ 적립금현황</span>
    </div>
</div>

<form method="get" name="search">
	<div id="search-area" class="search_cum_form">
        <div class="card mb-3">
            <div class="d-flex card-header justify-content-between">
                <h4>검색</h4>
                <div class="flax_box">
                    <a href="#" id="search_sbtn" onclick="Search();" class="btn btn-sm btn-primary shadow-sm mr-1"><i class="fas fa-search fa-sm text-white-50"></i> 조회</a>
                    <div id="search-btn-collapse" class="btn-group mb-0 mb-sm-0"></div>
                </div>
            </div>
			<div class="card-body">
				<!-- 일자 -->
				<div class="search-area-ext  row">
					<div class="col-lg-4 inner-td">
						<div class="form-group">
							<label for="">일자</label>
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


<div id="filter-area" class="card shadow-none mb-4 search_cum_form ty2 last-card">
	<div class="card-body shadow">
		<div class="card-title">
            <div class="filter_wrap">
                <div class="fl_box">
                    <h6 class="m-0 font-weight-bold">총 <span id="gd-total" class="text-primary">0</span> 건</h6>
                </div>
                <div class="fr_box">
					<h6 class="m-0 font-weight-bold">누적 적립금액 : <span id="gd-total" class="text-primary">{{ number_format($total_point) }}</span> 원</h6>
                </div>
            </div>
        </div>
		<div class="table-responsive">
			<div id="div-gd" style="height:calc(100vh - 370px);width:100%;" class="ag-theme-balham"></div>
		</div>
	</div>
</div>

<script language="javascript">
	var columns = [
		{headerName: '#', width:50, maxWidth: 90,type:'NumType'	},
	
		{field:"date" , headerName:"일자",width: 80, 
			cellRenderer: function(params) {
				return '<a href="#" onClick="goPointList(\''+ params.value +'\')">'+ params.value+'</a>'
			}
		},
		{headerName:"지급", width:120, 
			children : [
				{
					headerName : "회원가입",
					field : "g_member",
					type: 'currencyType',
					width:80
				},
				{
					headerName : "주문",
					field : "g_order",
					type: 'currencyType',
					width:80
				},
				{
					headerName : "환불",
					field : "g_refund",
					type: 'currencyType',
					width:80
				},
				{
					headerName : "교환",
					field : "g_change",
					type: 'currencyType',
					width:80
				},
				{
					headerName : "취소",
					field : "g_cancel",
					type: 'currencyType',
					width:80
				},
				{
					headerName : "상품후기",
					field : "g_review",
					type: 'currencyType',
					width:80
				},
				{
					headerName : "기타",
					field : "g_else",
					type: 'currencyType',
					width:80
				},
				{
					headerName : "소계",
					field : "g_sum",
					type: 'currencyType',
					width:100
				},

			]
		},

		{headerName:"사용", width:120, 
			children : [
				{
					headerName : "주문",
					field : "t_order",
					type: 'currencyType',
					width:80
				},
				{
					headerName : "환불/교환",
					field : "t_claim",
					type: 'currencyType',
					width:80
				},
				{
					headerName : "기타",
					field : "t_else",
					type: 'currencyType',
					width:80
				},
				{
					headerName : "소계",
					field : "t_sum",
					type: 'currencyType',
					width:100
				},
			]
		},
		{field:"g_sum" , headerName:"적립금", type: 'currencyType'},

		{headerName: "", field: "nvl"}
	];
	const pApp = new App('', { gridId: "#div-gd" });
	const gridDiv = document.querySelector(pApp.options.gridId);
	const gx = new HDGrid(gridDiv, columns);

	pApp.ResizeGrid();

	function Search() {
        let formData = $('form[name="search"]').serialize();
        gx.Request('/head/member/mem04/search', formData, 1);
    }

	function goPointList(edate){
		
		var e_year = edate.substring(0,4);
		var e_month = edate.substring(4,6);
		var e_day= edate.substring(6,8);
		var end_date = e_year +"-"+ e_month +"-"+ e_day;
		var start_date = new Date(end_date);
		var s_year, s_month, s_day, sdate;

		edate = new Date(end_date);
		start_date.setMonth(start_date.getMonth()-1);
		s_year = start_date.getFullYear();
		s_month = (start_date.getMonth()+1);
		s_day = start_date.getDate();

		if(s_month<10){
			s_month = "0"+ s_month;
		}

		if(s_day<10){
			s_day = "0"+ s_day;
		}

		sdate = s_year +"-"+ s_month +"-"+ s_day;
		
		
		
		const url='/head/member/mem05?sdate='+ sdate +"&edate="+ end_date;
        //const product=window.open(url,"_blank","toolbar=no,scrollbars=yes,resizable=yes,status=yes,top=500,left=500,width=1200,height=810");
		const Pop=window.open(url,"_blank");

	}

	$(function(){
		//Search();
        
	});
</script>
@stop

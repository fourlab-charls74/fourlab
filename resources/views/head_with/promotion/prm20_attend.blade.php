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
	<form method="get" name="search">
		<input type="hidden" name="idx" idx="idx" value="{{ $idx }}">
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
					<!-- 아이디/출석횟수 -->
					<div class="row">
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
						<div class="col-lg-4 inner-td">
							<div class="form-group">
								<label for="ord_no">아이디</label>
								<div class="flax_box">
									<input type='text' class="form-control form-control-sm search-all" name='userid' value='{{ $user_id }}'>
								</div>
							</div>
						</div>
					</div>
				</div>
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
					<!--div class="fr_box flax_box">
						<input type="checkbox" name="checkAll" id="checkAll">전체선택&nbsp;&nbsp;
						선택된 사은품을&nbsp;
 						<a href="#" class="btn-sm btn btn-primary confirm-del-btn">삭제</a>
						<<a href="#" onclick="Search()" class="btn-sm btn btn-primary confirm-refund-btn">검색</a>
					</div-->
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
		{field:"attend" , headerName:"참석일자", width:80},
		
		{field:"is_winner" , headerName:"당첨여부", width:80},
		{field:"attend_point" , headerName:"출석적립금", type: 'currencyType', width:90},
		{field:"attend_point_date", headerName:"출석적립금지급일시"},
		{field:"regular_attend_point" , headerName:"개근적립금", width:120, type: 'currencyType', width:90},
		
		{field:"regular_attend_point_date" , headerName:"개근적립금 지급일시", },
		
		{field:"support_point" , headerName:"쇼핑지원금", type: 'currencyType'},
		{field:"rt" , headerName:"출석일시", width:140,},
		{headerName: "", field: "nvl"}
	];

	const pApp = new App('', { gridId: "#div-gd" });
	const gridDiv = document.querySelector(pApp.options.gridId);
	const gx = new HDGrid(gridDiv, columns);

	pApp.ResizeGrid();


	function Search() {
		var idx	= "{{ $idx }}";
		let data = $('form[name="search"]').serialize();
		gx.Request('/head/promotion/prm20/attend_search/'+idx, data,1);
	}


	$(function(){
		Search();

	});

</script>
@stop

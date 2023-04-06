@extends('head_with.layouts.layout')
@section('title','적립금내역')
@section('content')

<div class="page_tit">
    <h3 class="d-inline-flex">적립금내역</h3>
    <div class="d-inline-flex location">
        <span class="home"></span>
        <span>/ 회원&amp;CRM</span>
        <span>/ 적립금내역</span>
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

				<!-- 구분/종류/내용 -->
				<div class="search-area-ext  row">
					<div class="col-lg-4 inner-td">
						<div class="form-group">
							<label for="">구분</label>
							<div class="flax_box">
								<select  name="point_st" class="form-control form-control-sm">
									@foreach($point_st_items as $point_st)
									<option value="{{ $point_st->code_id }}">{{ $point_st->code_val }}</option>
									@endforeach
								</select>
							</div>
						</div>
					</div>

					<div class="col-lg-4 inner-td">
						<div class="form-group">
							<label for="ord_no">종류</label>
							<div class="flax_box">
								<select  name="point_kind" class="form-control form-control-sm">
									<option value="">전체</option>
									@foreach($point_type_items as $point_type)
										<option value="{{ $point_type->code_id }}">{{ $point_type->code_val }}</option>
									@endforeach
								</select>
							</div>
						</div>
					</div>

					<div class="col-lg-4 inner-td">
						<div class="form-group">
							<label for="user_nm">내용</label>
							<div class="flax_box">
								 <input type='text' class="form-control form-control-sm search-all" name='point_nm' value=''>
							</div>
						</div>
					</div>

				</div>

				<!-- 지급일/아이디/적립상태 -->
				<div class="search-area-ext  row">
					<div class="col-lg-4 inner-td">
						<div class="form-group">
							<label for="">지급일</label>
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
								 <input type='text' class="form-control form-control-sm search-all" name='user_id' value=''>
							</div>
						</div>
					</div>

					<div class="col-lg-4 inner-td">
						<div class="form-group">
							<label for="user_nm">적립상태</label>
							<div class="flax_box">
								<select  name="point_status" class="form-control form-control-sm">
									<option value="">전체</option>
									<option value="Y">지급</option>
									<option value="N">대기</option>
								</select>
							</div>
						</div>
					</div>

				</div>

				<!-- 정렬 -->
				<div class="search-area-ext row">
					<div class="col-lg-4 inner-td">
						<div class="form-group">
							<label for="">정렬</label>
							<div class="form-inline">
                                <div class="form-inline-inner input_box" style="width:24%;">
                                    <select name="limit" class="form-control form-control-sm">
										<option selected value=100>100</option>
										<option value=500>500</option>
										<option value=1000>1000</option>
										<option value=2000>2000</option>
										<option value=-1>모두</option>
                                    </select>
                                </div>
                                <span class="text_line">/</span>
                                <div class="form-inline-inner input_box" style="width:45%;">
                                    <select name="ord_field" class="form-control form-control-sm">
										<option value="p.point_date" selected>지급일</option>
										<option value="p.user_id" >아이디</option>
                                    </select>
                                </div>
                                <div class="form-inline-inner input_box sort_toggle_btn" style="width:24%;margin-left:1%;">
                                    <div class="btn-group" role="group">
                                        <label class="btn btn-primary primary" for="sort_desc" data-toggle="tooltip" data-placement="top" title="" data-original-title="내림차순"><i class="bx bx-sort-down"></i></label>
                                        <label class="btn btn-secondary" for="sort_asc" data-toggle="tooltip" data-placement="top" title="" data-original-title="오름차순"><i class="bx bx-sort-up"></i></label>
                                    </div>
                                    <input type="radio" name="ord" id="sort_desc" value="desc" checked="">
                                    <input type="radio" name="ord" id="sort_asc" value="asc">
                                </div>
                            </div>
						</div>
					</div>


				</div>


				<div class="resul_btn_wrap d-sm-none">
					<a href="javascript:void(0);" class="btn btn-sm w-xs btn-primary shadow-sm apply-btn" onclick="return Search();"><i class="fas fa-search fa-sm text-white-50"></i> 검색</a>
				</div>
			</div>
		</div>
	</div>
</form>

<div id="filter-area" class="card shadow-none search_cum_form ty2 last-card">
	<div class="card-body shadow">
		<div class="card-title">
            <div class="filter_wrap">
                <div class="fl_box">
                    <h6 class="m-0 font-weight-bold">총 : <span id="gd-total" class="text-primary">0</span> 건</h6>
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
		{headerName: '#', width:50, maxWidth: 90,type:'NumType'},
		{field:"point_st" , headerName:"구분"},
		{field:"point_kind" , headerName:"종류", width:100},
		{field:"point_nm" , headerName:"내용", width:200},
		{field:"point", headerName:"적립금", width:80, type: 'currencyType', },
		{field:"user_id" , headerName:"아이디", width:100, type:"HeadUserType"},
		{field:"name", headerName:"이름", width:80},
		{field:"ord_no" , headerName:"주문번호", width:150, type:'HeadOrderNoType'  },
		{field:"ord_opt_no" , headerName:"주문일련번호" },
		{field:"point_status" , headerName:"지급여부" },
		{field:"point_date" , headerName:"지급일시", width:140,},
		{field:"admin_id" , headerName:"지급자", width:100,},
		{field:"no", headerName:"no", hide:true},
		{ width: "auto" }
	];
	const pApp = new App('', { gridId: "#div-gd" });
	const gridDiv = document.querySelector(pApp.options.gridId);
	const gx = new HDGrid(gridDiv, columns);
	gx.gridOptions.suppressRowClickSelection = true;

	pApp.ResizeGrid(275);

	function Search() {
		let data = $('form[name="search"]').serialize();
		gx.Request('/head/member/mem05/search', data,1);

	}

	$(function(){
		//Search();
		$("select[name=point_status]").val('Y');

	});


</script>
@stop

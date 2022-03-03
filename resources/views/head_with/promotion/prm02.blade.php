@extends('head_with.layouts.layout')
@section('title','제휴문의')
@section('content')

<div class="page_tit">
    <h3 class="d-inline-flex">제휴문의</h3>
    <div class="d-inline-flex location">
        <span class="home"></span>
        <span>/ 프로모션</span>
        <span>/ 제휴문의</span>
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
				
				<!-- 문의일/담당자/담당자 -->
				<div class="search-area-ext  row">
					<div class="col-lg-4 inner-td">
						<div class="form-group">
							<label for="">문의일</label>
                            <div class="form-inline">
                                <div class="docs-datepicker form-inline-inner input_box">
                                    <div class="input-group">
										<input type="text" class="form-control form-control-sm docs-date" name="sdate" value="{{ $sdate }}" autocomplete="off"  disable>
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
										<input type="text" class="form-control form-control-sm docs-date" name="edate" value="{{ $edate }}"  autocomplete="off">
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
							<label for="ord_no">회사</label>
							<div class="flax_box">
								 <input type='text' class="form-control form-control-sm search-all" name='comany_nm' value=''>
							</div>
						</div>
					</div>

					<div class="col-lg-4 inner-td">
						<div class="form-group">
							<label for="user_nm">담당자</label>
							<div class="flax_box">
								<input type='text' class="form-control form-control-sm search-all" name='name' value=''>
							</div>
						</div>
					</div>

				</div>

				<!-- 유형/상태/정렬순서 -->
				<div class="search-area-ext  row">
					<div class="col-lg-4 inner-td">
						<div class="form-group">
							<label for="">유형</label>
							<div class="flax_box">
								<select name="type" id="type" class="form-control form-control-sm">
									<option value="">전체</option>
									@foreach($pattypes as $types)
										<option value="{{ $types->code_id }}">{{ $types->code_val }}</option>
									@endforeach
								</select>
							</div>
						</div>
					</div>

					<div class="col-lg-4 inner-td">
						<div class="form-group">
							<label for="ord_no">상태</label>
							<div class="flax_box">
								<select name="state" id="state" class="form-control form-control-sm">
									<option value="">전체</option>
									@foreach($patstates as $states)
										<option value="{{ $states->code_id }}">{{ $states->code_val }}</option>
									@endforeach
								</select>
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
            </div>
        </div>
		<div class="table-responsive">
			<div id="div-gd" style="height:calc(100vh - 370px);width:100%;" class="ag-theme-balham"></div>
		</div>
	</div>
</div>

<script language="javascript">
	var columns = [
		{headerName: '#', width:50, maxWidth: 90,type:'NumType' },
		
		{field:"regi_date" , headerName:"문의일",},
		{field:"state" , headerName:"상태",
			cellRenderer: function(params) {
				return '<a href="#" onClick="popDetail(\''+ params.data.idx +'\')">'+ params.value+'</a>'
			}
		},
		{field:"type", headerName:"type", hide:true},
		{field:"type", headerName:"유형", width:120},
		{field:"company_nm", headerName:"회사", width:150},
		{field:"name" , headerName:"담당자"  },
		{field:"email", headerName:"이메일", width:150},
		{field:"phone" , headerName:"연락처"  },
		{field:"mobile" , headerName:"핸드폰", width:100 },
		{field:"address" , headerName:"주소", width:200 },
		{field:"idx", headerName:"idx", hide:true},
		{field:"url" , headerName:"URL"},
		{headerName: "", field: "nvl"}
	];
	const pApp = new App('', { gridId: "#div-gd" });
	const gridDiv = document.querySelector(pApp.options.gridId);
	const gx = new HDGrid(gridDiv, columns);

	pApp.ResizeGrid();
	

	function Search() {
		let data = $('form[name="search"]').serialize();
		gx.Request('/head/promotion/prm02/search', data,1);
	}

	function popDetail(val){
		const url='/head/promotion/prm02/'+val;
		const boardView=window.open(url,"_blank","toolbar=no,scrollbars=yes,resizable=yes,status=yes,top=500,left=500,width=1000,height=810");
	}

	$(function(){
		Search();
	});
</script>
제휴문의
@stop

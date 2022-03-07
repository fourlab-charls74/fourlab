{{-- init --}}
@extends($layout)
@section('title','게시글관리')
@section('content')
@if($layout == 'head_with.layouts.layout-nav') 
	<?php $pop_up = true ?>
@else
	<?php $pop_up = false ?>
@endif
@if($pop_up)	
	<style> body { overflow: hidden; padding: 1rem; } </style>
	<script> var resize_grid = 200; </script>
@else
	<script> var resize_grid = 290; </script>
@endif

{{-- blade --}}
<div class="page_tit">
	<h3 class="d-inline-flex">게시판</h3>
	<div class="d-inline-flex location">
		<span class="home"></span>
		<span>/ 커뮤니티</span>
		<span>/ 게시판</span>
	</div>
</div>
<form method="get" name="search">
	<div id="search-area" class="search_cum_form">
		<div class="card mb-3">
			<div class="d-flex card-header justify-content-between">
				<h4>검색</h4>
				<div>
					<a href="#" id="search_sbtn" onclick="return Search();" class="btn btn-sm btn-primary shadow-sm pl-2"><i class="fas fa-search fa-sm text-white-50"></i> 검색</a>
					<a href="#" onclick="Add();" class="btn btn-sm btn-outline-primary shadow-sm mr-1 pl-2"><i class="bx bx-plus fs-16"></i> 추가</a>
					@if(!$pop_up) <div id="search-btn-collapse" class="btn-group mb-0 mb-sm-0"></div> @endif
				</div>
			</div>
				<div class="card-body">
				<!-- 구분/제목/내용 -->
				<div class="search-area-ext row">
					<div class="col-lg-4 inner-td">
						<div class="form-group">
							<label for="board_id">구분 :</label>
							<div class="form-inline inline_input_box">
								<select name="board_id" id="board_id" class="form-control form-control-sm" style="width:auto;">
									<option value="">전체</option>
									@foreach($boards as $board)
										<option value="{{ $board->board_id }}" @if ($board->board_id == $board_id) selected @endif>{{ $board->board_nm }}</option>
									@endforeach
								</select>
							</div>
						</div>
					</div>

					<div class="col-lg-4 inner-td">
						<div class="form-group">
							<label for="subject">제목 :</label>
							<div class="flax_box">
								<input id="subject" type='text' class="form-control form-control-sm search-all search-enter" name='subject' value=''>
							</div>
						</div>
					</div>

					<div class="col-lg-4 inner-td">
						<div class="form-group">
							<label for="board_content">내용 :</label>
							<div class="flax_box">
								<input id="board_content" type='text' class="form-control form-control-sm search-all search-enter" name='content' value=''>
							</div>
						</div>
					</div>

				</div>

				<!-- 아이디/작성자/자료수/정렬순서 -->
				<div class="search-area-ext row">
					<div class="col-lg-4 inner-td">
						<div class="form-group">
							<label for="">아이디 :</label>
							<div class="form-inline inline_input_box">
								<input type='text' class="form-control form-control-sm search-all search-enter" name='id' value=''>
							</div>
						</div>
					</div>

					<div class="col-lg-4 inner-td">
						<div class="form-group">
							<label for="ord_no">작성자 :</label>
							<div class="flax_box">
								<input type='text' class="form-control form-control-sm search-all search-enter" name='name' value=''>
							</div>
						</div>
					</div>

					<div class="col-lg-4 inner-td">
						<div class="form-group">
							<label for="user_nm">자료수/정렬순서 :</label>
							<div class="form-inline">
								<div class="form-inline-inner input_box" style="width:24%;">
									<div class="form-group">
										<select name='limit' class="form-control form-control-sm">
											<option selected value=100>100</option>
											<option value=200>200</option>
											<option value=500>500</option>
										</select>
									</div>
								</div>
								<span class="text_line">/</span>
								<div class="form-inline-inner input_box" style="width:45%;">
									<div class="form-group">
										<select name="order_type" class="form-control form-control-sm">
											<option value="" selected>기본</option>
											<option value="new">최신글</option>
										</select>
									</div>
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
			</div>
		</div>
		<div class="resul_btn_wrap mb-3">
            <a href="#" id="search_sbtn" onclick="Search();" class="btn btn-sm btn-primary shadow-sm mr-1"><i class="fas fa-search fa-sm text-white-50"></i> 검색</a>
            <a href="javascript:;" onclick="Add()" class="btn btn-sm btn-outline-primary shadow-sm mr-1 pl-2"><i class="bx bx-plus fs-16"></i> 추가</a>
            <div class="search_mode_wrap btn-group mr-2 mb-0 mb-sm-0"></div>
        </div>
	</div>
</form>

<div id="filter-area" class="card shadow-none mb-4 search_cum_form ty2 last-card">
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
		{field:"b_no" , headerName:"번호", },
		{field:"board_nm" , headerName:"구분"},
		{field:"subject" , headerName:"제목", width:250,
			cellRenderer: function(params) {
				return '<a href="#" onClick="PopBoardView(\''+ params.data.b_no +'\')">'+ params.value+'</a>'
			}
		},
		{field:"hit", headerName:"조회수", width:80 },
		{field:"comment_cnt" , headerName:"덧글수"  },
		{field:"file_cnt", headerName:"파일수"},
		{field:"user_nm" , headerName:"작성자",},
		{field:"user_id", headerName:"아이디", type:"HeadUserType"},
		{field:"regi_date" , headerName:"작성일", width:130 },
		{field:"ip" , headerName:"IP", width:100 },
		{field:"is_notice" , headerName:"공지", },
		{field:"is_secret" , headerName:"비밀", },
		{field:"board_id", headerName:"board_id", hide:true},
		{field:"step", headerName:"step", hide:true, },
		{field:"points" , headerName:"적립금", },
		{ width: "auto" }
	];
	const pApp = new App('', { gridId: "#div-gd" });
	const gridDiv = document.querySelector(pApp.options.gridId);
	const gx = new HDGrid(gridDiv, columns);
	//gx.gridOptions.suppressRowClickSelection = true;
	//gx.gridOptions.suppressExcelExport = true;

	function Search() {
		let data = $('form[name="search"]').serialize();
		gx.Request('/head/community/com02/search', data,1, com02CallBack);
	}
	
	function com02CallBack(data){
		console.log("com02CallBack");
	}

	function PopBoardView(val){
		const url='/head/community/com02/'+val;
        const boardView=window.open(url,"_blank","toolbar=no,scrollbars=yes,resizable=yes,status=yes,top=100,left=100,width=1000,height=810");
	}

	$(function(){
		Search();
		pApp.ResizeGrid(resize_grid);
		pApp.BindSearchEnter();
	});

	function Add(){
		var board_id = document.search.board_id.value;

		var url = "/head/community/com02/detail?cmd=detail&b_no=&board_id=" + board_id;
		//openWindow(url,"contents","status=1,resizable=1,scrollbars=1",1024,768);
		const boardView=window.open(url,"_blank","toolbar=no,scrollbars=yes,resizable=yes,status=yes,top=100,left=100,width=1000,height=810");
	}

</script>

@stop

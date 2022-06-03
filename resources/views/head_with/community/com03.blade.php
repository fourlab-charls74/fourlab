{{-- init --}}
@extends($layout)
@section('title','댓글')
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
	<script> var resize_grid = 275; </script>
@endif

{{-- blade --}}
<div class="page_tit">
	<h3 class="d-inline-flex">댓글</h3>
	<div class="d-inline-flex location">
		<span class="home"></span>
		<span>/ 커뮤니티</span>
		<span>/ 댓글</span>
	</div>
</div>

<div id="search-area" class="search_cum_form">
	<form method="get" name="search">
		<div class="card mb-3">
		<div class="d-flex card-header justify-content-between">
			<h4>검색</h4>
			<div>
				<a href="#" id="search_sbtn" onclick="Search();" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm"><i class="fas fa-search fa-sm text-white-50"></i> 검색</a>
				@if(!$pop_up) <div id="search-btn-collapse" class="btn-group mb-0 mb-sm-0"></div> @endif
			</div>
		</div>
			<div class="card-body">
				<!-- 구분/제목/내용 -->
				<div class="row">
					<div class="col-lg-4 inner-td">
						<div class="form-group">
							<label for="">구분</label>
							<div class="d-flex">
								<select name="board_id" id="board_id" class="form-control form-control-sm">
									<option value="">전체</option>
									@foreach($boards as $board)
										<option value="{{ $board->board_id }}" @if($board->board_id === $board_id) selected @endif>{{ $board->board_nm }}</option>
									@endforeach
								</select>
							</div>
						</div>
					</div>

					<div class="col-lg-4 inner-td">
						<div class="form-group">
							<label for="subject">게시글</label>
							<div class="flax_box">
								<input type='text' id="subject" class="form-control form-control-sm search-all search-enter" name='subject' value=''>
							</div>
						</div>
					</div>

					<div class="col-lg-4 inner-td">
						<div class="form-group">
							<label for="user_nm">댓글</label>
							<div class="flax_box">
								<input type='text' class="form-control form-control-sm search-all search-enter" name='content' value=''>
							</div>
						</div>
					</div>

				</div>

				<!-- 아이디/작성자/자료수/정렬순서 -->
				<div class="row">
					<div class="col-lg-4 inner-td">
						<div class="form-group">
							<label for="">아이디</label>
							<div class="d-flex">
								<input type='text' class="form-control form-control-sm search-all search-enter" name='user_id' value=''>
								
							</div>
						</div>
					</div>

					<div class="col-lg-4 inner-td">
						<div class="form-group">
							<label for="ord_no">작성자</label>
							<div class="flax_box">
								<input type='text' class="form-control form-control-sm search-all search-enter" name='name' value=''>
							</div>
						</div>
					</div>

					<div class="col-lg-4 inner-td">
						<div class="form-group">
							<label for="user_nm">자료수/정렬순서</label>
							<div class="form-inline">
								<div class="form-inline-inner input_box" style="width:24%;">
									<div class="form-group">
										<select name='limit' class="form-control form-control-sm">
											<option value=100>100</option>
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
        </div>
	</form>
</div>


<div id="filter-area" class="card shadow-none search_cum_form ty2 last-card">
	<div class="card-body shadow">
		<div class="card-title">
			<div class="filter_wrap">
				<div class="fl_box">
					<h6 class="m-0 font-weight-bold">총 <span id="gd-total" class="text-primary">0</span> 건</h6>
				</div>
				<div class="fr_box flax_box">
					<input type="checkbox" name="checkAll" id="checkAll">전체선택 ,&nbsp;
					<select name="use_yn_value" style="width:40px; text-indent: 8px;" onchange="">
						<option value="1">Y</option>
						<option value="0">N</option>
					</select>&nbsp;
					<a href="#" onclick="EditIsSecret();" class="btn-sm btn btn-primary confirm-clm-no-btn">비밀글 변경</a>&nbsp;
					<a href="#" onclick="DelComment();" class="btn-sm btn btn-primary confirm-clm-no-btn">삭제</a>&nbsp;
								
					<input type="checkbox" class="checkbox" name="ord_opt_no_ex" checked> 적립급지급 댓글 제외 
					{{-- <a href="#" class="btn-sm btn btn-primary confirm-clm-no-btn point-btn" >적립금 지급</a> --}}
					<button class="btn-sm btn btn-primary confirm-clm-no-btn point-btn" disabled>적립금 지급</button>
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
		{
		  field: "blank",
		  headerName: '선택',
		  checkboxSelection: true,
		  headerCheckboxSelectionFilteredOnly: true,
		  width: 28,
		},
	
		{field:"c_no" , headerName:"번호", },
		{field:"b_no", headerName:"b_no", hide:true},
		{field:"board_id", headerName:"board_id", hide:true},

		{field:"board_nm" , headerName:"게시판", width:130,
			cellRenderer: function(params) {
				return '<a href="#" onClick="popBoard(\''+ params.data.b_no +'\', \''+ params.data.board_id +'\')">'+ params.value+'</a>'
			}
		},
		{field:"subject" , headerName:"게시글", width:200,
			cellRenderer: function(params) {
				return '<a href="#" onClick="PopBoardView(\''+ params.data.b_no +'\')">'+ params.value+'</a>'
			}
		},
		{field:"is_secret", headerName:"비밀글", width:80 },
		{field:"content" , headerName:"댓글", width:200 },
		{field:"user_nm" , headerName:"작성자",},
		{field:"user_id", headerName:"아이디", type:"HeadUserType"},
		
		{field:"ip" , headerName:"IP", width:100 },
		{field:"regi_date" , headerName:"작성일", width:130 },
		{ width: "auto" },
	];
	const pApp = new App('', { gridId: "#div-gd" });
	let gx;
	//gx.gridOptions.suppressRowClickSelection = true;
	//gx.gridOptions.suppressExcelExport = true;

	var Search = function () {
		let data = $('form[name="search"]').serialize();
		gx.Request('/head/community/com03/search', data,1, com03CallBack);
	};

	document.addEventListener('DOMContentLoaded', function() {
		pApp.ResizeGrid(resize_grid);
		pApp.BindSearchEnter();
		let gridDiv = document.querySelector(pApp.options.gridId);
		gx = new HDGrid(gridDiv, columns);
		Search();
	});


	$(function(){
		

		$("#checkAll").click(function(){
			checkAll();
		});

		$('.point-btn').click(function(e){
			const rows = gx.getSelectedRows();

			if (rows.length === 0) {
				alert("메시지 보낼 유저를 선택해주세요.");
				return;
			}
			const user_ids = [];

			rows.forEach(function(data){
				user_ids.push(data.user_id);
			});
			//console.log(user_ids);
			openAddPoint(user_ids.join(','), 12);
		});

	});

	function com03CallBack(data){
		console.log(data);
	}

	function checkAll(){
		var selectedRowData = gx.gridOptions.api.getSelectedRows();
		var displayRowCnt = gx.gridOptions.api.getDisplayedRowCount();

		if(selectedRowData.length == displayRowCnt){
			gx.gridOptions.api.deselectAll();
		}else{
			gx.gridOptions.api.selectAll();
		}
	}

	function EditIsSecret(){

        var ff = document.search;
		var use_yn = $("[name=use_yn_value]").val();
        var get_nos = new Array();
		var selectedRowData = gx.gridOptions.api.getSelectedRows();
		
		selectedRowData.forEach( function(selectedRowData, index) {
			get_nos.push(selectedRowData.c_no);
		});


        if(get_nos != ""){
            if(!confirm("변경 하시겠습니까?")){
                return false;
            }

			$.ajax({
				method: 'put',
				url: '/head/community/com03/editsecret',
				data:{
					'data': get_nos,
					'use_yn': use_yn
				},
				success: function (data) {
					if(data.code === 200){
						Search();
					}else{
						alert(data.message);
					}
				},
				error: function(request, status, error) {
					console.log("error");
					//console.log("code:"+request.status+"\n"+"message:"+request.responseText+"\n"+"error:"+error);
				}
			});

        } else {
            alert("변경할 항목을 선택해 주십시오.");
            return false;
        }
    }


	function DelComment(){

        var ff = document.search;
        var get_nos = new Array();
		var selectedRowData = gx.gridOptions.api.getSelectedRows();
		
		selectedRowData.forEach( function(selectedRowData, index) {
			get_nos.push(selectedRowData.c_no);
		});

        if(get_nos != ""){
            if(!confirm("삭제 하시겠습니까?")){
                return false;
            }
			$.ajax({
				method: 'put',
				url: '/head/community/com03/delcomment',
				data:{
					'data': get_nos
				},
				success: function (data) {
					if(data.return_code == 1){
						Search();
					}else{
						alert("장애가 발생했습니다.\n관리자에게 문의해 주십시오.\n"+data.return_code);
					}
				},
				error: function(request, status, error) {
					console.log("error");
					//console.log("code:"+request.status+"\n"+"message:"+request.responseText+"\n"+"error:"+error);
				}
			});

			/*
            var param = "CMD=delcomment";
            param += "&DATA=" + get_nos;
            param += "&UID=" + Math.random();

            var http = new xmlHttp();
            http.onexec('brd03.php','POST', param, true, cbDelComment);

            ProcessingPopupShowHide("show");
			*/
        } else {
            alert("삭제할 항목을 선택해 주십시오.");
            return false;
        }
    }

	function PopBoardView(val){
		const url='/head/community/com02/'+val;
        const boardView=window.open(url,"_blank","toolbar=no,scrollbars=yes,resizable=yes,status=yes,top=100,left=100,width=1000,height=810");
	}

	function popBoard(b_no, board_id){
		var url = "{{config('shop.front_url')}}/app/boards/views/"+board_id+"/"+b_no;
		//console.log(val);
		const potho_view=window.open(url,"_blank");
	}


</script>
@stop

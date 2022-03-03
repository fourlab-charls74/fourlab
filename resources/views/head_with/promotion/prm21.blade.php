@extends('head_with.layouts.layout')
@section('title','댓글이벤트')
@section('content')

<script type="text/javascript" src="/handle/editor/editor.js"></script>
<script type="text/javascript" src="/handle/editor/summernote/summernote-lite.min.js"></script>
<script type="text/javascript" src="/handle/editor/summernote/lang/summernote-ko-KR.js"></script>
{{-- <script type="text/javascript" src="/handle/editor/summernote/plugin/summernote-ext-ssm-emoji/summernote-ext-ssm-emoji.js?v=2020081801"></script> --}}
<link rel="stylesheet" href="/handle/editor/summernote/summernote-lite.min.css">
{{-- <link rel="stylesheet" href="/handle/editor/summernote/plugin/summernote-ext-ssm-emoji/summernote-ext-ssm-emoji.css?v=2020081821"> --}}

<div class="page_tit">
    <h3 class="d-inline-flex">댓글</h3>
    <div class="d-inline-flex location">
        <span class="home"></span>
        <span>/ 프로모션</span>
        <span>/ 댓글이벤트</span>
    </div>
</div>

<form method="get" name="search" onsubmit="return false">
    <div id="search-area" class="search_cum_form">
        <div class="card mb-3">
            <div class="d-flex card-header justify-content-between">
                <h4>검색</h4>
                <div class="flax_box">
                    <a href="#" id="search_sbtn" onclick="Search_event();" class="btn btn-sm btn-primary shadow-sm mr-1"><i class="fas fa-search fa-sm text-white-50"></i> 조회</a>
                    <a href="javascript:;" onclick="Cmder('add');" class="btn btn-sm btn-outline-primary shadow-sm mr-1 pl-2"><i class="bx bx-plus fs-16"></i> 추가</a>
                    <div id="search-btn-collapse" class="btn-group mb-0 mb-sm-0"></div>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <!-- 이벤트 제목 -->
                    <div class="col-lg-4 inner-td">
                        <div class="form-group">
                            <label for="formrow-firstname-input">이벤트 제목</label>
                            <div class="flax_box">
                                <input type="text" name="evt_sbj" class="form-control form-control-sm search-all search-enter" style="width:100%;">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>

<div class="show_layout">
    <form name="f1" id="f1">
        <input type="hidden" name="c">
        <div class="row">
            <div class="col-sm-4">
                <div class="card_wrap">
                    <div class="card shadow">
                        <div class="card-header mb-0">
							<h5 class="m-0 font-weight-bold">총 <span id="gd-total">0</span> 건</h6>
                        </div>
                        <div class="card-body brtn">
                            <div class="row_wrap">
                                <div class="row">
                                    <div class="col-12">
                                        <div class="table-responsive">
                                            <div id="div-gd" style="height:calc(100vh - 370px);width:100%;" class="ag-theme-balham"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-8">
				<div class="tabs">
					<ul class="nav nav-tabs" role="tablist">
						<li class="nav-item" role="presentation">
							<a class="nav-link active" id="tab-nav-1" data-toggle="tab" href="#tab-1" role="tab" aria-controls="send" aria-selected="true">댓글</a>
						</li>
						<li class="nav-item" role="presentation">
							<a class="nav-link" id="tab-nav-2" data-toggle="tab" href="#tab-2" role="tab" aria-controls="list" aria-selected="true"">이벤트 상세정보</a>
						</li>
					</ul>
					<div class="tab-content">
						<!-- 이벤트 참여정보 시작 -->
						<div class="tab-pane show active" id="tab-1" role="tabpanel" aria-labelledby="send-tab">
							<div class="card">
								<div class="card-body shadow">
									<!-- 검색영역2 시작 -->
									<form name="search_comment">
										<input type="hidden" name="event_idx" id="event_idx">
										<div class="row_wrap">
											<div class="row">
												<div class="col-12">
													<div class="table-box mobile">
														<table class="table incont table-bordered" width="100%" cellspacing="0">
															<colgroup>
																<col width="120px">
																<col width="40%">
																<col width="120px">
																<col width="40%">
															</colgroup>
															<tbody>
																<tr>
																	<th>아이디</th>
																	<td>
																		<div class="flax_box inline_btn_box" style="padding-right:65px;">
																			<input type="text" name="userid" class="form-control form-control-sm" />
																			<a href="#" id="comment_sch_btn" onclick="Search_comment();" style="width:60px;" class="btn btn-sm btn-primary shadow-sm"><i class="fas fa-search fa-sm text-white-50"></i> 조회</a>
																		</div>
																	</td>
																	<th>찜한선물</th>
																	<td>
																		<div class="flax_box">
																			<select name="slct_award" class="form-control form-control-sm" onchange="Cmder('listsearch');">
																				<option value=""> 전체 보기 </option>
																			</select>
																		</div>
																	</td>
																</tr>
															</tbody>
														</table>
													</div>
												</div>
											</div>
										</div>
									</form>
									<!-- 검색영역2 끝 -->
									<div class="card-title mt-3">
										<div class="filter_wrap">
											<div class="fl_box">
												<h6 class="m-0 font-weight-bold">총 <span id="gd-total" class="text-primary">0</span> 건</h6>
											</div>
											<div class="fr_box flax_box grid-show-text">
												<span class="mr-1">선택된 건들을</span>
												<div class="mr-1" style="min-width:120px;">
													<select name="show_yn" class="form-control form-control-sm">
														<option value="Y">모두 보이도록</option>
														<option value="N">모두 숨기도록</option>
													</select>
												</div>
												<a href="#" onclick="Cmder('changelist');" class="btn-sm btn btn-primary confirm-clm-no-btn">상태변경</a>
											</div>
										</div>
									</div>
									<div class="table-responsive">
										<div id="div-gd2" style="height:calc(100vh - 50vh); width:100%;" class="ag-theme-balham"></div>
									</div>
								</div>
							</div>
						</div>
						<!-- 이벤트 참여정보 끝 -->
						<!-- 이벤트 상세 정보 시작 -->
						<div class="tab-pane show" id="tab-2" role="tabpanel" aria-labelledby="send-tab">
							<div class="card shadow mb-3">
								<div class="card-body shadow">
									<form name="f2">
										<div class="row_wrap">
											<div class="row">
												<div class="col-12">
													<div class="table-box mobile">
														<table class="table incont table-bordered" width="100%" cellspacing="0">
															<colgroup>
																<col width="120px">
															</colgroup>
															<tbody>
																<tr>
																	<th>이벤트번호</th>
																	<td>
																		<div class="txt_box">
																			<span id="event_no"></span>
																		</div>
																	</td>
																</tr>
																<tr>
																	<th>제목</th>
																	<td>
																		<div class="input_box">
																			<input type="text" name="subject" value="" class="form-control form-control-sm" id="event_subject" autocomplete='off' >
																		</div>
																	</td>
																</tr>
																<tr>
																	<th>내용</th>
																	<td>
																		<div class="area_box">
																			<input type="hidden" name="content" />
																			<textarea id="editor1"></textarea>
																		</div>
																	</td>
																</tr>
																<tr>
																	<th>사용여부</th>
																	<td>
																		<div class="form-inline form-radio-box">
																			<div class="custom-control custom-radio">
																				<input type="radio" name="use_yn" id="use_yn_y" class="custom-control-input" value="Y">
																				<label class="custom-control-label" for="use_yn_y">사용</label>
																			</div>
																			<div class="custom-control custom-radio">
																				<input type="radio" name="use_yn" id="use_yn_n" class="custom-control-input" value="N" checked> 
																				<label class="custom-control-label" for="use_yn_n">미사용</label>
																			</div>
																		</div>
																	</td>
																</tr>
															</tbody>
														</table>
													</div>
												</div>
											</div>
										</div>
									</form>
								</div>
							</div>
							<!-- 저장/수정/취소 -->
							<div style="text-align:center;">
								<input type="button" class="btn btn-primary" id="btn_add" value='등록' onclick="Cmder('addcmd');">
								<input type="button" class="btn btn-primary" id="btn_edit" value='수정' onclick="Cmder('editcmd');" style="display:none;">
								{{-- <input type="button" class="btn btn-outline-primary" value="취소" onclick="Cmder('reset');"> --}}
							</div>
						</div>
						<!-- 이벤트 상세 정보 끝 -->
					</div>
				</div>
            </div>
        </div>
    </form>
</div>
<script language="javascript">
	/*****************************************
	* 댓글 이벤트 목록 시작
	*****************************************/
	var columns_event = [
        // this row shows the row index, doesn't use any data from the row
        {headerName: '#', width:50, maxWidth: 90,type:'NumType'},
        {field:"subject",headerName:"이벤트", width:200 },
        {field:"total",headerName:"댓글수",width:80, },
        {field:"is_use",headerName:"사용여부", width:90 },
        {field:"regi_date",headerName:"등록일",},
		{field:"idx", headerName:"idx", hide:true}
	];

	const pApp = new App('', { gridId: "#div-gd" });
	const gridDiv_event = document.querySelector(pApp.options.gridId);
	const gx_event = new HDGrid(gridDiv_event, columns_event);
	gx_event.gridOptions.suppressRowClickSelection = true;
	gx_event.gridOptions.onRowClicked = showEventInfo;
	pApp.ResizeGrid();
	/*****************************************
	* 댓글 이벤트 목록 끝
	*****************************************/

	function showEventInfo(event){
		var idx = event.node.data.idx;
		$.ajax({
			async: true,
			type: 'get',
			url: '/head/promotion/prm21/event_info/'+idx,
			success: function (data) {
				cbClickInfo(data.event_info);
				EnableAdd(false);
			},
			complete:function(){
				//_grid_loading = false;
			},
			error: function(request, status, error) {
				console.log("error");
				//console.log("code:"+request.status+"\n"+"message:"+request.responseText+"\n"+"error:"+error);
			}
		});
	}

	function cbClickInfo(res){
		if(res){	
			$("#event_no").html(res.idx);
			$("#event_subject").val(res.subject);
			$("#editor1").summernote('code', res.content);
			
			if(res.is_use =="Y") document.getElementById("use_yn_y").checked = true;
			if(res.is_use =="N") document.getElementById("use_yn_n").checked = true;
			$("#event_idx").val(res.idx);

			Search_comment();

			if(res.idx == 7 || res.idx == 8){
				ArrayGift();
			}
		}
	}

	function ArrayGift(){
		var cmd = "arraygift";
		var event = $("#event_idx").val();

		$.ajax({
			async: true,
			type: 'get',
			url: '/head/promotion/prm21/arraygift/'+event,
			success: function (data) {
				//console.log(data);
				cbArrayGift(data);
			},
			complete:function(){
				//_grid_loading = false;
			},
			error: function(request, status, error) {
				console.log("error");
				//console.log("code:"+request.status+"\n"+"message:"+request.responseText+"\n"+"error:"+error);
			}
		});
	}
	
	function cbArrayGift(res){
		var obj = $("[name=slct_award]");
		var gift_list = res.gift_list;
		var gift_cnt = res.gift_cnt;
		
		for(i=1; i<=gift_cnt; i++){
			var val = i;
			var text = gift_list[i];
			obj.append('<option value="'+ val +'">'+ text +'</option>');
		}
	}



	function Search_event() {
		let data = $('form[name="search"]').serialize();
		gx_event.Request('/head/promotion/prm21/search', data,1);
		
	}

	/*****************************************
	* 이벤트 댓글 목록 시작
	*****************************************/
	var columns_comment = [
        // this row shows the row index, doesn't use any data from the row
        {headerName: '#', width:50, maxWidth: 90,type:'NumType'},
		{
			headerName: '선택',
			checkboxSelection: true,
			width:50,
		},
        {field:"user_id",headerName:"아이디" },
        {field:"gift",headerName:"찜한선물", width:90 },
        {field:"comment",headerName:"코멘트", width:500, wrapText: true },
        {field:"regi_date",headerName:"등록일", width:120, },
		{field:"is_show",headerName:"출력여부", width:90},
		{field:"award_level",headerName:"시상",},
		{field:"idx", headerName:"idx", hide:true}
	];

	const pApp_comment = new App('', { gridId: "#div-gd2" });
	const gridDiv_comment = document.querySelector(pApp_comment.options.gridId);
	const gx_comment = new HDGrid(gridDiv_comment, columns_comment);
	pApp_comment.ResizeGrid();
	
	function Search_comment() {
		var event_idx = $("#event_idx").val();
		let data = "userid=" + $("[name=userid]").val() + "&slct_award=" + $("[name=slct_award]").val();
		gx_comment.Request('/head/promotion/prm21/search_comment/'+ event_idx, data, 1);
	}

	/*****************************************
	* 이벤트 댓글 목록 끝
	*****************************************/
	

	function Cmder(cmd){

		if(cmd == "add"){

			SetClickTab(2);
			FormReset(document.f2);
			if(document.search_comment) FormReset(document.search_comment);
			$("#event_idx").val('');
			Search_comment();
			EnableAdd(true);
			
		}else if(cmd == "addcmd" || cmd == "editcmd"){
			
			if(Validate(cmd)){
				SaveCmd(cmd);
			}
			
		}else if(cmd == "changelist"){

			CmdListShow(cmd);

		}
	}


	function SetClickTab(show_index){

		$("div.tabs>ul.nav-tabs li a").removeClass("active");
		$("div.tab-content>div.tab-pane").removeClass("active");
		
		
		$("#tab-"+show_index).addClass("active");
		$("#tab-nav-"+ show_index +"").addClass("active");
		
	}

	function FormReset(form){
		$("#event_no").html('');
		$("#editor1").summernote('code', '');
		form.reset();
	}

	function EnableAdd(flag){
		if(flag){
			$("#btn_add").show();
			$("#btn_edit").hide();
		}else{
			$("#btn_add").hide();
			$("#btn_edit").show();
		}
	}

	function SaveCmd(cmd){
		if(!confirm((cmd === "editcmd" ? "수정" : "등록") + "하시겠습니까?")) return;
		var f2 = $('form[name="f2"]');
		var event_idx = $("#event_idx").val();
		$('[name=content]').val($("#editor1").val());

		$.ajax({
			async: true,
			type: 'put',
			url: '/head/promotion/prm21/store',
			data: f2.serialize()+ '&cmd='+ cmd + "&event_idx=" + event_idx,
			success: function (data) {
				if(data.return_code == 1){
					Search_event();
				}else{
					alert(data.responseText);
				}
			},
			complete:function(){
                //_grid_loading = false;
			},
			error: function(request, status, error) {
				console.log("error");
				//console.log("code:"+request.status+"\n"+"message:"+request.responseText+"\n"+"error:"+error);
			}
		});
	}

	function CmdListShow(cmd){
		var event_idx = $("#event_idx").val();
		var show_yn = $("[name=show_yn]").val();
		const rows = gx_comment.getSelectedRows();

		if (rows.length === 0) {
			alert("메시지 보낼 유저를 선택해주세요.");
			return;
		}
		const user_ids = [];

		rows.forEach(function(data){
			user_ids.push(data.idx);
		});
		
		$.ajax({
			async: true,
			type: 'put',
			url: '/head/promotion/prm21/change_list/'+ event_idx,
			data: {
				"data" : user_ids,
				"show_yn": show_yn
			},
			success: function (data) {
				if(data.return_code == 1){
					Search_comment();
				}else{
					alert(data.responseText);
				}
			},
			complete:function(){
                //_grid_loading = false;
			},
			error: function(request, status, error) {
				console.log("error");
				//console.log("code:"+request.status+"\n"+"message:"+request.responseText+"\n"+"error:"+error);
			}
		});

	}


	function Validate(cmd){

		if ($("#event_subject").val() == '') {
			alert('이벤트 제목을 입력해 주십시오.');
			$("#event_subject").focus();
			return false;
		}
		return true;
	}

	let ed;

	$(document).ready(function() {
		Search_event();

        var editorToolbar = [
            ['font', ['bold', 'underline', 'clear']],
            ['color', ['color']],
            ['para', ['paragraph']],
			['insert', ['picture', 'video']],
            ['emoji', ['emoji']],
            ['view', ['undo', 'redo', 'codeview', 'help']]
        ];
        var editorOptions = {
            lang: 'ko-KR',
            minHeight: 300,
            dialogsInBody: true,
            disableDragAndDrop: false,
            toolbar: editorToolbar,
			imageupload:{
                dir:'/images/prd_img/image',
                maxWidth:1280,
                maxSize:10
            }
        }
        ed = new HDEditor('#editor1', editorOptions, true);

		$("[name=evt_sbj]").on("keypress", function(e) {
			if(e.originalEvent.code !== "Enter") return;
			Search_event();
		});
	});

</script>

@stop

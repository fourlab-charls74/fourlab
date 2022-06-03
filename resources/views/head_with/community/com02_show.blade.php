@extends('head_with.layouts.layout-nav')
@section('title','게시글관리')
@section('content')
<style>
	.th { background: #f5f5f5; border: 1px solid #ddd; box-sizing: border-box; }
	.td { border: 1px solid #eff2f7; border-color: #ddd; border-right: 1px solid #ddd; box-sizing: border-box; }
	.row { margin: 0; }
	.row a { text-decoration: underline !important; }
	.row p { font-size: 14px; font-weight: 500; }
</style>
<script type="text/javascript" src="/handle/editor/editor.js"></script>
<script type="text/javascript" src="/handle/editor/summernote/summernote-lite.min.js"></script>
<script type="text/javascript" src="/handle/editor/summernote/lang/summernote-ko-KR.js"></script>

<div class="container-fluid show_layout py-3">
	<div class="page_tit mb-3 d-flex align-items-center justify-content-between">
		<div>
			<div class="d-inline-flex location">
				<span class="home"></span>
				<span>/ 커뮤니티</span>
				<span>/ 게시글</span>
				<span>/ 게시글관리</span>
			</div>
		</div>
		
		<div>
			@if($config['functions'] & 2 )
			<a href="#" onclick="Detail('reply','{{ $contents->b_no }}','{{ $contents->board_id }}');" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm">답변</a>
			@endif
			<a href="#" onclick="Detail('detail','{{ $contents->b_no }}','{{ $contents->board_id }}');" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm">수정</a>
			<a href="#" onclick="Delete('{{ $contents->b_no }}','{{ $contents->board_id }}');" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm">삭제</a>
			<a href="#"  class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm point-btn">적립금 지급</a>
			<a href="#" onclick="self.close()" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm">닫기</a>
		</div>
    </div>
	<div class="page_tit">
		<h3 class="d-inline-flex">게시글관리 - {{ $contents->subject }}</h3>
	</div>
	<div class="card_wrap">
		<!-- 업체 기본 정보 -->
		<div class="card shadow">
			<!-- 제목 -->
			<div class="row" style="line-height: 50px;">
				<div class="col-2 th border-bottom-0" style="text-align: right;">
					<p>제목</p>
				</div>
				<div class="col td border-left-0 border-bottom-0">
					<a href="{{config('shop.front_url')}}/app/boards/views/{{ $contents->board_id }}/{{ $contents->b_no }}" target="_blank">{{ $contents->subject }}</a>
				</div>
			</div>
			<div class="row" style="line-height: 50px;">
				<div class="col-2 th border-bottom-0" style="text-align: right;">
					<p>공지글 여부</p>
				</div>
				<div class="col-4 td border-left-0 border-bottom-0">
					@if($contents->is_notice == "0") 일반 @else 공지 @endif
				</div>
				<div class="col-2 th border-left-0 border-bottom-0" style="text-align: right;">
					<p>비밀글 여부</p>
				</div>
				<div class="col-4 td border-left-0 border-bottom-0">
					@if($contents->is_secret == "0") 공개 @else 비밀 @endif
				</div>
			</div>
			<!-- 작성자/IP -->
			<div class="row" style="line-height: 50px;">
				<div class="col-2 th border-bottom-0" style="text-align: right;">
					<p>작성자</p>
				</div>
				<div class="col-4 td border-left-0 border-bottom-0">
					{{ $contents->user_nm }} <a href="#" onclick="PopUserInfo('{{ $contents->user_id }}');">({{ $contents->user_id }})</a>
				</div>
				<div class="col-2 th border-left-0 border-bottom-0" style="text-align: right;">
					<p>IP</p>
				</div>
				<div class="col-4 td border-left-0 border-bottom-0">
					{{ $contents->ip }}
				</div>
			</div>
			<div class="row" style="line-height: 50px;">
				<div class="col-2 th" style="text-align: right;">
					<p>조회수</p>
				</div>
				<div class="col-4 td border-left-0">
					{{ number_format($contents->hit) }}
				</div>
				<div class="col-2 th border-left-0" style="text-align: right;">
					<p>작성일</p>
				</div>
				<div class="col-4 td border-left-0">
					{{ $contents->regi_date }}
				</div>
			</div>
			<div class="row" style="padding-top: 30px;">
				<div style="width:100%;">
					{!! $contents->content !!}
				</div>
			</div>

			{{-- @if(trim($config['cfg_reserve1']) != "" )
			<div class="row form-inline no-gutters">
				<label for="">{{ $config['cfg_reserve1'] }}</label>
				<div class="inline-inner-box triple" style="padding-left:0;">
					<div class="input_box flax_box">
						<div style="width:100%;">
						{{ $contents->reserve1 }}
						</div>
					</div>
				</div>
			</div>
			@endif

			@if($config['cfg_reserve2'] != "")
			<div class="row form-inline no-gutters">
				<label for="">{{ $config['cfg_reserve2'] }}</label>
				<div class="inline-inner-box triple" style="padding-left:0;">
					<div class="input_box flax_box">
						<div style="width:100%;">
							{{ $contents->reserve2 }}
						</div>
					</div>
				</div>
			</div>
			@endif
			@if ($config['cfg_reserve3'] != "" )
			<div class="row form-inline no-gutters">
				<label for="">{{ $config['cfg_reserve3'] }}</label>
				<div class="inline-inner-box triple" style="padding-left:0;">
					<div class="input_box flax_box">
						<div style="width:100%;">
							{{ $contents->reserve3 }}
						</div>
					</div>
				</div>
			</div>
			@endif --}}
		</div>

		
		<!-- 첨부이미지 -->
		@if ($config['functions'] & 16 and $files_total > 0 )
		<div class="card shadow">
			<!-- 공지글 여부/비밀글 여부 -->
			<div class="row">
				<div class="col-2 th" style="text-align: right; line-height: 100px;">
					<p>첨부이미지</p>
				</div>
				<div class="col td border-left-0">
					@foreach ($files as $file)
						<div id="{{ $file->file_nm }}" class="d-flex">
							<a href="/data/board/{{ $board_id }}/{{ $file->file_nm_enc }}" target="_blank" >
								<img src="/data/board/{{ $board_id }}/{{ $file->file_nm_enc }}" style="width:100px;" alt="{{ $file->file_nm }}" style="cursor: hand;" style="text-decoration:none;border: 0 !important;"/>
							</a>
						</div>
					@endforeach
				</div>
			</div>
		</div>
		@endif
		

		<!-- 관련 글 출력 //-->
		@if(($config['functions'] & 2) && $relate_total > 1)

		<div class="card shadow">
			<div class="card-header mb-0">
				<div>
					<textarea name="content" rows="5" style="width:100%; padding-left: 0;"></textarea>
					<input type="button" name="addComment" id="addComment" onclick="AddComment();" value="덧글 작성" style="width:100%;">
				</div>
			</div>
			<div class="card-body">
				<div class="table-responsive">
					<table class="table table-bordered" id="dyntbl1">
						<thead>
							<tr>
								<th>관련글</th>
								<th>작성자</th>
								<th>작성일</th>
							</tr>
						</thead>
						<tbody>
							@if(count($relate_contents)>0)

								@foreach ($relate_contents as $rel)
									<tr>
										<td class="bd"><a href="/head/community/com02/{{ $rel->b_no }}">{{ $rel->subject }}</a> @if($rel->comment_cnt > 0) ({{ $rel->comment_cnt }})@endif</td>
										<td class="bd">{{ $rel->user_nm }} @if($rel->user_id != "") ({{ $rel->user_id }})@endif</td>
										<td class="bd">{{ $rel->regi_date }}</td>
									</tr>

								@endforeach

							@endif
							
						</tbody>
					</table>


				</div>
				
			</div>
		</div>
		@endif


		<form name="f1">
		<input type="hidden" name="b_no" value="{{ $contents->b_no }}" />
		<input type="hidden" name="board_id" value="{{ $contents->board_id }}" />
		<input type="hidden" name="user_id" value="{{ $contents->user_id }}" />
		<input type="hidden" name="data" />
		<input type="hidden" name="cmd" value="addcomment" />
		<input type="hidden" name="display_comment_writer" value="{{ $config['display_comment_writer'] }}" />

		@if ($config['functions'] & 4)
		

		<div class="card shadow">
			<div class="card-header mb-0">
				<div>
					<textarea name="content" rows="5" style="width:100%;"></textarea>
					<input type="button" name="addComment" id="addComment" onclick="AddComment();" value="덧글 작성" style="width:100%;">
				</div>
			</div>
			<div class="card-body">
				<div class="table-responsive">
					<table class="table table-bordered" id="dyntbl1">
						<thead>
							<tr>
								<th style="text-align:center; width:50%;">내용</th>
								<th style="text-align:center;">비밀글</th>
								<th style="text-align:center;">작성자</th>
								<th style="text-align:center;">IP</th>
								<th style="text-align:center;">작성일</th>
								<th style="text-align:center;">관리</th>
							</tr>
						</thead>
						<tbody>
							@if(count($comments)>0)

								@foreach($comments as $data)
									<tr>
										<td class="bd">{!! nl2br($data->content) !!}</td>
										<td class="bd" style="text-align:center">
											<a href="javascript:void(0);" onclick="EditSecret('{{ $data->c_no }}','{{ $contents->b_no }}')">@if ($data->is_secret == 1) Y @else N @endif</a>
										</td>
										<td class="bd">{{ $data->user_nm }}</td>
										<td class="bd">{{ $data->ip }}</td>
										<td class="bd" style="text-align:center">{{ $data->regi_date }}</td>
										<td class="bd" style="text-align:center"><a href="javascript:void(0);" onclick="DeleteComment('{{ $data->c_no }}','{{ $contents->b_no }}')">삭제</a></td>
									</tr>

								@endforeach
							@else
							<tr>
								<td colspan="6">등록된 덧글이 없습니다.</td>
							</tr>

							@endif
							
						</tbody>
					</table>


				</div>
				
			</div>
		</div>
		
		@endif
		</form>
		
	</div>

</div>
<script type="text/javascript" charset="utf-8">
    $(function(){
		var return_code = "{{ $return_code }}";
		
		if(return_code == 0){
			alert("처리할 수 없는 요청입니다.");
			self.close();

		}

		$('.point-btn').click(function(e){
			var user_id = $("[name=user_id]").val();
			const user_ids = [];
			//console.log(user_ids);
			user_ids.push(user_id);
			openAddPoint(user_ids.join(','), 12);
		});
    });

	function PopUserInfo(memId){
        //const url='/head/member/mem01?cmd=edit&user_id='+memId;
        const url='/head/member/mem01/show/edit/'+memId;
        const product=window.open(url,"_blank","toolbar=no,scrollbars=yes,resizable=yes,status=yes,top=500,left=500,width=1000,height=810");
    }

	 
	function AddComment(){
		var ff = $("[name=f1]");
		var content = $("[name=content]").val();
		if(content.trim() == ""){
			alert("덧글을 입력해주십시오.");
			$("[name=content]").val("").focus();
			return false;
		}


		$.ajax({
			method: 'put',
			url: '/head/community/com02/add_comment/',
			data: ff.serialize(),
			success: function (data) {
				if(data.return_code == true){
					alert("덧글이 등록되었습니다.");
					location.reload();
				}else{
					alert("저장에 실패하였습니다. 다시 시도하여 주십시오.");
				}
			},
			error: function(request, status, error) {
				console.log("error");
				console.log("code:"+request.status+"\n"+"message:"+request.responseText+"\n"+"error:"+error);
			}
		});

	}


	function EditSecret( c_no, b_no ){
		if( confirm("비밀글을 변경하시겠습니까?") ){
			$.ajax({
				method: 'put',
				url: '/head/community/com02/editsecret/'+c_no,
				success: function (data) {
					if(data.return_code == 1){
						alert("변경되었습니다.");
						location.reload();
					}else{
						alert("변경을 실패하였습니다. 다시 시도하여 주십시오.");
					}
				},
				error: function(request, status, error) {
					console.log("error");
					//console.log("code:"+request.status+"\n"+"message:"+request.responseText+"\n"+"error:"+error);
				}
			});

		}
	}

	function DeleteComment( c_no, b_no ){
		if( confirm("정말 삭제하시겠습니까?") ){
			$.ajax({
				method: 'put',
				url: '/head/community/com02/delcomment/'+c_no,
				data: "b_no="+b_no,
				success: function (data) {
					if(data.return_code == 1){
						alert("삭제되었습니다.");
						location.reload();
					}else{
						alert("삭제를 실패하였습니다. 다시 시도하여 주십시오.");
					}
				},
				error: function(request, status, error) {
					console.log("error");
					console.log("code:"+request.status+"\n"+"message:"+request.responseText+"\n"+"error:"+error);
				}
			});

			/*
			var http = new xmlHttp();
			var param = "cmd=delcomment&c_no=" + c_no + "&b_no=" + b_no;
			http.onexec('brd02.php','POST',param, true, cbDeleteComment);
			*/
		}
	}

	function Detail(cmd, b_no, board_id){
		document.location.href = "/head/community/com02/detail?b_no=" + b_no + "&cmd="+ cmd +"&board_id=" + board_id;
	}


	function cbDeleteComment(res){
		if(res.responseText == 1){
			document.location.reload();
		}else{
			alert("삭제를 실패하였습니다. 다시 시도하여 주십시오.");
		}
	}

	function Delete(b_no, board_id){
		if( confirm("정말 삭제하시겠습니까?") ){
			$.ajax({
				method: 'put',
				url: '/head/community/com02/del',
				data: "b_no="+b_no+"&board_id="+board_id,
				success: function (data) {
					if(data.return_code == 1){
						alert("삭제되었습니다.");
						//location.reload();
						window.opener.Search();
						self.close();
					}else{
						alert("삭제를 실패하였습니다. 다시 시도하여 주십시오.");
					}
				},
				error: function(request, status, error) {
					console.log("error");
					//console.log("code:"+request.status+"\n"+"message:"+request.responseText+"\n"+"error:"+error);
				}
			});
		}
	}
	

</script>
    
@stop

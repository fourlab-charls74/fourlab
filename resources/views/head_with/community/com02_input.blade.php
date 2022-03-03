@extends('head_with.layouts.layout-nav')
@section('title','게시글관리')
@section('content')
<script type="text/javascript" src="/handle/editor/editor.js"></script>
<script type="text/javascript" src="/handle/editor/summernote/summernote-lite.min.js"></script>
<script type="text/javascript" src="/handle/editor/summernote/lang/summernote-ko-KR.js"></script>
<style>
	.th { background: #f5f5f5; border: 1px solid #ddd; box-sizing: border-box; }
	.th label { margin : 0; }
	.td { border: 1px solid #eff2f7; border-color: #ddd; border-right: 1px solid #ddd; box-sizing: border-box; }
	.row { margin: 0; line-height: 40px; }
	.row a { text-decoration: underline !important; }
	.row p { font-size: 14px; font-weight: 500; }
</style>
<div class="container-fluid show_layout py-3">

	<div class="d-sm-flex align-items-center justify-content-between mb-2">
		<h1 class="h3 mb-0 text-gray-800">게시글관리 - {{ $contents['subject'] }}</h1>
		<div>
			<a href="#" id="search_sbtn" onclick="Save();" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm">저장</a>
			<a href="#" class="btn btn-sm btn-secondary" onclick="window.close()">취소</a>
		</div>
	</div>

<form name="detail">
<input type="hidden" name="cmd" value="save">
<input type="hidden" name="type" value="{{ $type }}">
<input type="hidden" name="functions" value="{{ $config['functions'] }}">
<input type="hidden" name="board" value="{{ $board_id }}">
<input type="hidden" name="b_no" value="{{ $b_no }}">
<input type="hidden" name="gidx" value="{{ $contents['gidx'] }}">
<input type="hidden" name="step" value="{{ $contents['step'] }}">
<input type="hidden" name="loc" value="{{ $contents['loc'] }}">
<input type=hidden name="data">
<input type=hidden name="ac_id">
<div class="card_wrap">
	<!-- 업체 기본 정보 -->
	<div class="card shadow">
		<div class="card-body">
			<div class="row_wrap">
				<!-- 구분 -->
				<div class="row">
					<div class="col-2 th">
						<label for="division">구분</label>
					</div>
					<div class="col-10 td border-left-0">
						<select id="division" name="board_id" onchange="ResetForm(this);" class="form-control-sm">
							<option value="">선택</option>
							@foreach($board_ids as $board)
							<option value="{{ $board->board_id }}" @if($board->board_id == $board_id) selected @endif>{{ $board->board_nm }}</option>
							@endforeach
						</select>
					</div>
				</div>
				<!-- 제목 -->
				<div class="row">
					<div class="col-2 th">
						<label for="title">제목</label>
					</div>
					<div class="col-10 td border-left-0">
						<input id="title" type="text" name="subject" value="{{ $contents['subject'] }}" class="form-control-sm" style="width:100%" maxlength="125">
					</div>
				</div>
				<!-- 공지글 여부 -->
				<div class="row">
					<div class="col-2 th">
						<label for="">공지글 여부</label>
					</div>
					<div class="col-10 td border-left-0">
						<div class="form-inline form-radio-box">
							<div class="custom-control custom-radio">
								<input id="is_notice_false" type="radio" name="is_notice" value="0" style="width:auto;" class="custom-control-input" @if ($contents['is_notice'] == "0") checked @endif>
								<label for="is_notice_false" class="custom-control-label">일반</label>
							</div>
							<div class="custom-control custom-radio">
								<input id="is_notice_true" type="radio" name="is_notice" value="1" style="width:auto;" class="custom-control-input" @if ($contents['is_notice'] == "1") checked @endif>
								<label for="is_notice_true" class="custom-control-label">공지</label>
							</div>
							<span style="color:red;">[ <i>공지글 선택 시 게시판 맨 위쪽에 노출 됩니다.</i> ]</span>
						</div>
					</div>
				</div>

				@if ($config['functions']!= "" && ($config['functions'] & 8 ) == 8)
				<!-- 비밀글 여부 -->
				<div class="row">
					<div class="col-2 th">
						<label for="">비밀글 여부</label>
					</div>
					<div class="col-10 td border-left-0">
						<div class="form-inline form-radio-box" style="margin-top: 5px;">
							<div class="custom-control custom-radio">
								<input id="is_secret_false" type="radio" name="is_secret" value="0" style="width:auto;" class="custom-control-input" @if ($contents['is_secret'] == "0") checked @endif>
								<label for="is_secret_false" class="custom-control-label">일반</label>
							</div>
							<div class="custom-control custom-radio">
								<input id="is_secret_true" type="radio" name="is_secret" value="1" style="width:auto;" class="custom-control-input" @if ($contents['is_secret'] == "1") checked @endif>
								<label for="is_secret_true" class="custom-control-label">비밀</label>
							</div>
						</div>
					</div>
				</div>
				@endif
				<!-- 내용 -->
				<div class="input_box flax_box" style="width: 100%;">
					<textarea name="content" id="content" class="form-control editor1" >{{ $contents['content'] }}</textarea>
				</div>
				<!-- 파일 첨부 -->
			</div>
		</div>
	</div>
</div>
</form>
	<div class="row justify-content-center mb-3" style="margin-top:20px;">
		<div class="col text-center">
			<a href="#" onclick="Save();" class="btn btn-sm btn-primary shadow-sm mr-1" style="text-decoration: none !important;">저장</a>
			<a href="#" class="btn btn-sm btn-outline-primary shadow-sm mr-1 pl-2" style="text-decoration: none !important;" onclick="window.close()">취소</a>
		</div>
	</div>
</div>

	<link rel="stylesheet" href="/handle/editor/summernote/summernote-lite.min.css" >
    <link rel="stylesheet" href="/handle/editor/summernote/plugin/summernote-ext-ssm-emoji/summernote-ext-ssm-emoji.css?v=2020081821" >

<script type="text/javascript" charset="utf-8">
    var ed;

    $(document).ready(function() {
        var editorToolbar = [
            ['font', ['bold', 'underline', 'clear']],
            ['color', ['color']],
            ['para', ['paragraph']],
            ['insert', ['picture', 'video']],
            ['emoji', ['emoji']],
            ['view', ['undo', 'redo', 'codeview','help']]
        ];
        var editorOptions = {
            lang: 'ko-KR', // default: 'en-US',
            minHeight: 100,
            height: 400,
            dialogsInBody: true,
            disableDragAndDrop: false,
            toolbar: editorToolbar,
            imageupload:{
                dir:'/data/head',
                maxWidth:1280,
                maxSize:10
            }
        }
        ed = new HDEditor('.editor1', editorOptions);
    });

	function Validate(){
		var ff = document.detail;
		var content = $("#content").val();

		if(ff.board_id.value == ""){
			alert("구분을 선택해 주십시오.");
			ff.board_id.focus();
			return false;
		}

		if(ff.subject.value == ""){
			alert("제목을 입력하십시오.");
			ff.subject.focus();
			return false;
		}
		if(content == ""){
			alert("내용을 입력하십시오.");
			return false;
		}

		return true;
	}

	var _is_saving = false;

	function Save(){
		if( Validate() ){
			if(_is_saving == false){
				_is_saving = true;

				$.ajax({
					method: 'put',
					url: '/head/community/com02/store/',
					data: $("[name=detail]").serialize(),
					success: function (res) {
						console.log(res);
						if(res.return_code == 1){
							console.log(res.b_no);
							var url = "/head/community/com02/"+res.b_no;
							location.href=url;
						}
					},
					error: function(request, status, error) {
						console.log("error");
						//console.log("code:"+request.status+"\n"+"message:"+request.responseText+"\n"+"error:"+error);
					}
				});

				/*
				var http = new xmlHttp();
				var param = formData2QueryString(document.f1);
				http.onexec('brd02.php','POST',param,true,cbSave);
				*/
			}
		}
	}

	function cbSave(res){
		var b_no = document.f1.b_no.value;
		var result = res.responseText;

		if(result > 1){
			b_no = result;
		}

		_is_saving = false;

		if(b_no > 0){
			document.location.href = "/head/webapps/board/brd02.php?cmd=view&b_no=" + b_no;
		} else {
			alert("저장에 실패하였습니다. 다시 시도하여 주십시오.");
		}
	}

	function ResetForm(obj){
		var board_id = obj.value;
		document.location.href = "/head/community/com02/detail?cmd=detail&b_no=&board_id="+board_id;
	}

	$(function(){
		$(".submit-btn").click(function(){
			Save();

		});

	});

</script>
@stop

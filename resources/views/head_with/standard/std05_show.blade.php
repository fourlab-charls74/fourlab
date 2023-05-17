@extends('head_with.layouts.layout-nav')
@section('title','FAQ 상세')
@section('content')
<script type="text/javascript" src="/handle/editor/editor.js"></script>
<script type="text/javascript" src="/handle/editor/summernote/summernote-lite.min.js"></script>
<script type="text/javascript" src="/handle/editor/summernote/lang/summernote-ko-KR.js"></script>
<div class="show_layout py-3">
    <!-- FAQ 세부 정보 -->
    <form name="detail">
        <div class="card_wrap aco_card_wrap">
            <div class="card shadow">
                <div class="card-header mb-0">
					<a href="#">FAQ 상세</a>
				</div>
                <div class="card-body mt-1">
                    <div class="row_wrap">
                        <!-- 업체아이디/비밀번호/업체 -->
                        <div class="row">
							<div class="col-12">
								<div class="table-box-ty2 mobile">
									<table class="table incont table-bordered" width="100%" cellspacing="0">
										<colgroup>
											<col width="94px">
										</colgroup>
										<tbody>
											<tr>
                                                <th>유형</th>
                                                <td>
                                                    <div class="flax_box">
                                                        <select name="type" id="type" id="type" class="form-control form-control-sm">
                                                            <option value="">전체</option>
                                                            @foreach ($faq_types as $faq_type)
                                                                <option value='{{ $faq_type->id }}' 
                                                                    @if($faq_type->id == $type) selected @endif
                                                                    >{{ $faq_type->val }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </td>
                                            </tr>
											<tr>
                                                <th>작성자</th>
                                                <td>
                                                    <div class="flax_box">
                                                        <input type='text' class="form-control form-control-sm search-enter" name='admin_nm' id="admin_nm" value='{{$admin_nm}}'>
                                                    </div>
                                                </td>
                                            </tr>
											<tr>
                                                <th>제목</th>
                                                <td>
                                                    <div class="flax_box">
                                                        <input type='text' class="form-control form-control-sm search-enter" name='question' id="question" value='{{$question}}'>
                                                    </div>
                                                </td>
                                            </tr>
											<tr>
                                                <th>답변</th>
                                                <td>
                                                    <div class="flax_box">
                                                        <textarea name="answer" id="faq_cont" class="form-control editor1">{{ $answer }}</textarea>
                                                    </div>
                                                </td>
                                            </tr>
											<tr>
                                                <th>베스트여부</th>
                                                <td>
                                                    <div class="form-inline form-radio-box">
                                                        <div class="custom-control custom-checkbox">
                                                            <input type="checkbox" name="best_yn" id="best_yn" class="custom-control-input" value="Y" @if(!empty($best_yn)) checked @endif />
                                                            <label class="custom-control-label" for="best_yn">예</label>
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
											<tr>
                                                <th>공개여부</th>
                                                <td>
                                                    <div class="form-inline form-radio-box">
                                                        <div class="custom-control custom-radio">
                                                            <input type="radio" name="show_yn" id="show_yn_y" class="custom-control-input" value="Y" @if($show_yn == 'Y') checked @endif>
                                                            <label class="custom-control-label" for="show_yn_y">예</label>
                                                        </div>
                                                        <div class="custom-control custom-radio">
                                                            <input type="radio" name="show_yn" id="show_yn_n" class="custom-control-input" value="N" @if($show_yn == 'N') checked @endif>
                                                            <label class="custom-control-label" for="show_yn_n">아니요</label>
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
                </div>
            </div>
        </div>
    </form>
    <div class="resul_btn_wrap mt-3 d-block">
        <a href="javascript:;" class="btn btn-sm btn-primary submit-btn">저장</a>
        @if ($idx !== '')
        <a href="javascript:;" class="btn btn-sm btn-secondary delete-btn">삭제</a>
        @endif
        <a href="javascript:;" class="btn btn-sm btn-secondary" onclick="window.close()">취소</a>
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
            height: 150,
            dialogsInBody: true,
            disableDragAndDrop: false,
            toolbar: editorToolbar,
            imageupload:{
                dir:'/data/head/std05',
                maxWidth:1280,
                maxSize:10
            }
        }
        ed = new HDEditor('.editor1',editorOptions);
    });
</script>
<script>
    const IDX = '{{$idx}}';
    function validate() {
        var show_yn = 0;

        if ($('#type').val() === '') {
            alert('유형을 선택해주세요.');
            return false;
        }

        if (IDX !== '') return true;

        if ($('#admin_nm').val() === '') {
            alert('작성자를 입력해주세요.');
            return false;
        }

        if ($('#admin_nm').val() === '') {
            alert('제목을 입력해주세요.');
            return false;
        }
        //console.log($("input[name=show_yn]").length);
        for(i=0; i<$("input[name=show_yn]").length; i++){
            if($("input[name=show_yn]").eq(i).is(":checked") == true){
                show_yn = (i+1);
            }
        }
        if(show_yn == 0){
            alert("공개여부를 체크하세요.");
            return false;
        }

        return true;
    }

	$(function(){
		$('.submit-btn').click(function(e){
			e.preventDefault();
			if (!validate()) return;
			var faq_cont = $("#faq_cont").val();
			//console.log(faq_cont);
			faq_cont = faq_cont.replace(/(<([^>]+)>)/ig,"");
			
			if(faq_cont.trim() == ""){
				alert("내용을 입력하세요.");
				return false;
			}
			const data = $('form[name=detail]').serialize();
			
			$.ajax({
				async: true,
				type: 'put',
				url: `/head/standard/std05/show/${IDX}`,
				data: data,
				success: function (res) {
                    if (IDX) {
                        alert("변경된 내용이 정상적으로 저장되었습니다.");
                    } else {
                        alert("정상적으로 등록되었습니다.");
                    }
					opener.Search(1);
					window.close();
				},
				error: function(request, status, error) {
					//alert(request.responseJSON.msg);
					console.log("error");
					//console.log("code:"+request.status+"\n"+"message:"+request.responseText+"\n"+"error:"+error);
				}
			});
		});

		$('.delete-btn').click(function(e){
			e.preventDefault();
			if(!confirm('FAQ를 삭제하시겠습니까?')){
			return false;
			}

			$.ajax({
				async: true,
				type: 'delete',
				url: `/head/standard/std05/show/${IDX}`,
				success: function (res) {
					alert('삭제되었습니다.');
					opener.Search(1);
					window.close();
				},
				// PLAN_KKO_BET
				error: function(request, status, error) {
					console.log(request, status, error);
					console.log("error")
				}
			});
		});
	});
</script>
    
@stop

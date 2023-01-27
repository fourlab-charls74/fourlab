@extends('head_with.layouts.layout')
@section('title','트레킹 공지사항')
@section('content')

<script type="text/javascript" src="/handle/editor/editor.js"></script>
<script type="text/javascript" src="/handle/editor/summernote/summernote-lite.min.js"></script>
<script type="text/javascript" src="/handle/editor/summernote/lang/summernote-ko-KR.js"></script>
<script type="text/javascript" src="/handle/editor/summernote/plugin/summernote-ext-ssm-emoji/summernote-ext-ssm-emoji.js?v=2020081801"></script>
<link rel="stylesheet" href="/handle/editor/summernote/summernote-lite.min.css">
<link rel="stylesheet" href="/handle/editor/summernote/plugin/summernote-ext-ssm-emoji/summernote-ext-ssm-emoji.css?v=2020081821">

<div class="show_layout">
    <div class="page_tit">
        <h3 class="d-inline-flex">트레킹 공지사항</h3>
        <div class="d-inline-flex location">
            <span class="home"></span>
            <span>/ 공지사항</span>
        </div>
    </div>
    <form method="post" name="f1">
        <div class="card_wrap aco_card_wrap">
            <div class="card">
                <div class="card-header mb-0 justify-content-between d-flex">
                    <div></div>
                    <div>
                        @if($type=='edit')
                        <input type="button" class="btn btn-sm btn-primary shadow-sm" value="수정" onclick="Save();">
                        <input type="button" class="btn btn-sm btn-primary shadow-sm" value="삭제" onclick="Destroy();">
                        @else
                        <input type="button" class="btn btn-sm btn-primary shadow-sm" value="저장" onclick="Save();">
                        @endif
                        <button href="#" onclick="location.href='/head/classic/cls01';return false;" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm">목록</button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-12">
                            <div class="table-box-ty2 mobile">
                                <table class="table incont table-bordered" id="dataTable" width="100%" cellspacing="0">
                                    <colgroup>
                                        <col width="15%">
                                        <col width="35%">
                                        <col width="15%">
                                        <col width="35%">
                                    </colgroup>
                                    <tbody>
                                        <tr>
                                            <th>작성자</th>
                                            <td>
                                                <div class="input_box">
                                                    @if ($type=='edit')  
                                                    <input type='text' class="form-control form-control-sm wd100" name='name' value='{{ $evt_notice[0]->admin_nm }}' required>
                                                    @else 
                                                    <input type='text' class="form-control form-control-sm wd100" name='name' value='{{ $name }}' required>
                                                    @endif
                                                </div>
                                            </td>
                                            <th>이메일</th>
                                            <td>
                                                <div class="input_box">
                                                    @if ($type=='edit')
                                                    <input type='text' class="form-control form-control-sm wd100" name='email' value='{{ $evt_notice[0]->admin_email }}' required>
                                                    @else
                                                    <input type='text' class="form-control form-control-sm wd100" name='email' value='{{ $email }}' required>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>사용여부</th>
                                            <td>
                                                <div class="form-inline form-radio-box">
                                                    <div class="custom-control custom-radio">
                                                        @if ($type=='edit')
                                                        <input type="radio" name="useyn" id="useyn1" class="custom-control-input" value="Y" {{ ($evt_notice[0]->use_yn=="Y") ? "checked" : "" }}/>
                                                        @else
                                                        <input type="radio" name="useyn" id="useyn1" class="custom-control-input" value="Y" checked/>
                                                        @endif
                                                        <label class="custom-control-label" for="useyn1">예</label>
                                                    </div>
                                                    <div class="custom-control custom-radio">
                                                        @if ($type=='edit')
                                                        <input type="radio" name="useyn" id="useyn2" class="custom-control-input" value="N" {{ ($evt_notice[0]->use_yn=="N") ? "checked" : "" }}/>
                                                        @else
                                                        <input type="radio" name="useyn" id="useyn2" class="custom-control-input" value="N"/>
                                                        @endif
                                                        <label class="custom-control-label" for="useyn2">아니요</label>
                                                    </div>
                                                </div>
                                            </td>
                                            <th>이벤트</th>
                                            <td>
                                                <div class="input_box">
                                                    @if ($type=='edit')
                                                    <input type="hidden" name="evt_idx" value="{{$evt_notice[0]->evt_idx}}">
                                                    @else            
                                                    <input type="hidden" name="evt_idx" value="">
                                                    @endif
                                                    <button onclick="select_event();return false;" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm mr-1" style="float:left;">선택</button>
                                                    @if ($type=='edit')
                                                    <div id="evt_nm" style="float:left;line-height:25px;">{{ $evt_notice[0]->title }}</div>
                                                    @else
                                                    <div id="evt_nm" style="float:left;line-height:25px;"></div>                
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>제목</th>
                                            <td colspan="3">
                                                <div class="txt_box">
                                                    @if ($type=='edit')
                                                    <input type="text" class="form-control form-control-sm wd100" name="subject" value="{{ $evt_notice[0]->subject }}">
                                                    @else
                                                    <input type="text" class="form-control form-control-sm wd100" name="subject" value="">
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>타이틀 이미지[JPG]</th>
                                            <td>
                                                <ul style="padding:0; list-style:none; margin:0; list-style:none;">
                                                    <li>
                                                        <span id="preview_thumb_img" style="width:352px; height:352px; border:1px solid #b3b3b3; display:block;">
                                                        @if ($type=='edit')
                                                        <img src="{{ $evt_notice[0]->thumb_img }}" style="width:352px; height:352px;">
                                                        @else
                                                        @endif
                                                        </span>
                                                    </li>
                                                    <li style="padding-top:5px;">
                                                        <input type="file" name="title_file">
                                                    </li>
                                                </ul>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>코멘트</th>
                                            <td colspan="3">
                                                <div class="txt_box">
                                                    @if ($type=='edit')
                                                    <textarea name="comment" rows="5" style="width:100%">{{ $evt_notice[0]->comment }}</textarea>
                                                    @else
                                                    <textarea name="comment" rows="5" style="width:100%"></textarea>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>내용</th>
                                            <td colspan="3">
                                                <div class="area_box">
                                                    @if ($type=='edit')
                                                    <textarea name="content" id="content" class="form-control editor1" style="display: none;">{{ $evt_notice[0]->content }}</textarea>
                                                    @else
                                                    <textarea name="content" id="content" class="form-control editor1" style="display: none;"></textarea>
                                                    @endif
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
    </form>
</div>
<script type="text/javascript" charset="utf-8">
    var ed;

    $(document).ready(function() {
        var editorToolbar = [
            ['font', ['bold', 'underline', 'clear']],
            ['color', ['color']],
            ['para', ['paragraph']],
            ['insert', ['picture', 'video']],
            ['emoji', ['emoji']],
            ['view', ['undo', 'redo', 'codeview', 'help']]
        ];
        var editorOptions = {
            lang: 'ko-KR', // default: 'en-US',
            minHeight: 100,
            height: 200,
            width: 500,
            dialogsInBody: true,
            disableDragAndDrop: false,
            toolbar: editorToolbar,
            imageupload: {
                dir: '/data/head/classic',
                maxWidth: 1280,
                maxSize: 10
            }
        }
        ed = new HDEditor('.editor1', editorOptions, true);     //true : summernote사이즈 조절 가능
    });

    function select_event() {
        const url = '/head/classic/cls01/event-pop';
        window.open(url, "_blank", "toolbar=no, scrollbars=yes, resizable=yes, status=yes, top=500, left=500, width=800, height=600");
    }

    function Save() {
        var frm = $('form');
        //console.log(frm.serialize());

		if( $('input[name="name"]').val() === "" )
		{
			alert('작성자명을 입력하세요.');
			$('input[name="name"]').focus();

			return false;
		}

        const mailReg = /^[0-9a-zA-Z]([-_\.]?[0-9a-zA-Z])*@[0-9a-zA-Z]([-_\.]?[0-9a-zA-Z])*\.[a-zA-Z]{2,3}$/i;

        if (!mailReg.test($('input[name="email"]').val())) {
            alert("이메일을 확인해 주십시요.");
			$('input[name="email"]').focus();

			return false;
        }

		if( $('input[name="evt_idx"]').val() === "" )
		{
			alert("이벤트를 선택하세요.");

			return false;
		}
	
		if( $('input[name="subject"]').val() === "" )
		{
            alert("제목을 입력해 주십시요.");
			$('input[name="subject"]').focus();

			return false;
		}

        // editor value
        $('textarea[name="content"]').val(ed.html());
	
		if( $('textarea[name="content"]').val() === "" || $('textarea[name="content"]').val() == "<p><br></p>" )
		{
            alert("내용을 입력해 주십시요.");
			$('textarea[name="content"]').focus();

			return false;
		}
		var f1 = $("form[name=f1]")[0];
		var formData = new FormData(f1);

        let evt_nm = document.getElementById('evt_nm').innerHTML;
        formData.append('evt_nm', evt_nm);

        
        let type = "<?=$type?>";
        formData.append('type', type);

        if (type=='add') {
            $.ajax({
                method: 'post',
                url: '/head/classic/cls01/create',
                processData: false,
                contentType: false,
                data: formData,
                success: function (res) {
                    if(res.code === 200) {
                        alert('공지사항이 등록 되었습니다.');
                        document.location.href = '/head/classic/cls01'
                    } else {
                        alert('처리 중 문제가 발생하였습니다. 다시 시도하여 주십시오.');
                    }
                },
                error: function(request, status, error) {
                    alert("에러가 발생했습니다.");
                    console.log(request);
                }
            });
        } else if (type == 'edit') {
            $.ajax({
                method: 'post',
                url: '/head/classic/cls01/update/{{@$evt_notice[0]->idx}}',
                processData: false,
                contentType: false,
                data: formData,
                success: function (res) {
                    if(res.code === 200) {
                        alert('공지사항이 수정 되었습니다.');
                        document.location.href = '/head/classic/cls01'
                    } else {
                        alert('처리 중 문제가 발생하였습니다. 다시 시도하여 주십시오.');
                    }
                },
                error: function(request, status, error) {
                    alert("에러가 발생했습니다.");
                    console.log(request);
                }
            });
        }
    }
    
    function Destroy() {
        var frm = $('form');
		if (! confirm("삭제 하시겠습니까?")) {
			return false;
		}

        $.ajax({
            async: true,
            method: 'get',
            url: '/head/classic/cls01/del/{{@$evt_notice[0]->idx}}',
            data: frm.serialize(),
            success: function (res) {
                if (res.code === 200) {
					alert('공지사항이 삭제되었습니다.');
                    document.location.href = '/head/classic/cls01'
                } else {
                    alert('처리 중 문제가 발생하였습니다. 다시 시도하여 주십시오.');
                }
            },
            error: function(request, status, error) {
                console.log(request);
            }
        });
    }

    let target_file = null;

    function validatePhoto() {
        // if (target_file === null || target_file.length === 0) {
		// 	alert("업로드할 이미지를 선택해주세요.");
		// 	return false;
		// }

        if(!/(.*?)\.(jpg|jpeg|png|gif|JPG|JPEG|PNG|GIF)$/i.test(target_file[0].name)) {
            alert("이미지 형식이 아닙니다.");
            return false;
        }

        return true;
    }

    function appendCanvas(size, id, type) {
        var canvas = $("<canvas></canvas>").attr({
            id : id,
            name : id,
            width : size,
			height : size,
            style : "margin:10px",
            "data-type" : type
        });
        $("#preview_thumb_img").empty();
        $("#preview_thumb_img").append(canvas);
	}

	function drawImage(e) {
        $('#preview_thumb_img canvas').each(function(idx) {
			var size = this.width;
			var canvas = this;
			var ctx = canvas.getContext('2d');
			var image = new Image();

			image.src = e.target.result;

			image.onload = function() {
				ctx.drawImage(this, 0, 0, size, size);
			}
        });
    }

	$(function() {
		$("[name=title_file]").change(function() {
			target_file = this.files;
			if (validatePhoto() === false) return;
			var fr = new FileReader();
			appendCanvas(330, 'c_80', 'a');

			fr.onload = drawImage;
			fr.readAsDataURL(target_file[0]);
		});
	});
</script>
@stop

@extends('head_with.layouts.layout-nav')
@section('title','배너')
@section('content')
<script type="text/javascript" src="/handle/editor/editor.js"></script>
<script type="text/javascript" src="/handle/editor/summernote/summernote-lite.min.js"></script>
<script type="text/javascript" src="/handle/editor/summernote/lang/summernote-ko-KR.js"></script>
<script type="text/javascript" src="/handle/editor/summernote/plugin/summernote-ext-ssm-emoji/summernote-ext-ssm-emoji.js?v=2020081801"></script>
<link rel="stylesheet" href="/handle/editor/summernote/summernote-lite.min.css" >
<link rel="stylesheet" href="/handle/editor/summernote/plugin/summernote-ext-ssm-emoji/summernote-ext-ssm-emoji.css?v=2020081821" >

<div class="container-fluid show_layout py-3">
    <div class="page_tit mb-3 d-flex align-items-center justify-content-between">
        <div>
            <h3 class="d-inline-flex">배너</h3>
            <div class="d-inline-flex location">
                <span class="home"></span>
                <span>/ 프로모션</span>
                <span>/ 배너 관리</span>
            </div>
        </div>
        <div>
            @if ($type == 'add')
                <a href="#" class="d-sm-inline-block btn btn-sm btn-primary shadow-sm save-btn">저장</a>
            @elseif($type == 'edit')
                <a href="#" class="d-sm-inline-block btn btn-sm btn-primary shadow-sm update-btn">수정</a>
                <a href="#" class="d-sm-inline-block btn btn-sm btn-primary shadow-sm delete-btn">삭제</a>
            @endif
        </div>
    </div>
    <form name="detail">
        <input type="hidden" name="src" value="">
        <div class="card_wrap mb-3">
            <div class="card shadow">
                <div class="card-header mb-0">
                    <a href="#" class="m-0 font-weight-bold">배너 정보</a>
                </div>
                <div class="card-body">
                    <div class="row_wrap">
                        <div class="row">
                            <div class="col-12">
                                <div class="table-box mobile">
                                    <table class="table incont table-bordered" id="dataTable" width="100%" cellspacing="0">
                                        <colgroup>
                                            <col width="120px">
                                        </colgroup>
                                        <tbody>
                                            <tr>
                                                <th>페이지</th>
                                                <td>
                                                    <div class="flax_box">
                                                        <select name="page" id="page" class="form-control form-control-sm">
                                                            <option value="">전체</option>
                                                            @foreach($pages as $key => $val)
                                                                <option value="{{$key}}" @if(@$banner->page == $key) selected @endif>{{$val}}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th>영역코드</th>
                                                <td>
                                                    <div class="flax_box">
                                                        <input type="text" name="arcd" id="arcd" class="form-control form-control-sm" value="{{@$banner->arcd}}">
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th>순서</th>
                                                <td>
                                                    <div class="flax_box">
                                                        <input type="text" name="seq" id="seq" class="form-control form-control-sm" value="{{@$banner->seq}}">
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th>영역</th>
                                                <td>
                                                    <div class="flax_box">
                                                        <input type="text" name="area" id="area" class="form-control form-control-sm" value="{{@$banner->area}}">
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th>코드</th>
                                                <td>
                                                    <div class="flax_box">
                                                        @if ($type == 'add')
                                                            <input type="text" name="code" id="code" class="form-control form-control-sm">
                                                            <strong class="code-check-text mt-1"></strong>
                                                        @else
                                                            <div class="txt_box">{{$code}}</div>
                                                        @endif
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th>내용</th>
                                                <td>
                                                    <div class="flax_box">
                                                        <input type="text" name="subject" id="subject" class="form-control form-control-sm" value="{{@$banner->subject}}">
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th>구분</th>
                                                <td>
                                                    <div class="form-inline form-radio-box">
                                                        <div class="custom-control custom-radio">
                                                            <input type="radio" name="type" id="type" class="custom-control-input" value="H" checked>
                                                            <label class="custom-control-label" for="type">HTML</label>
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th>배너</th>
                                                <td>
                                                    <div class="flax_box mb-1">
                                                        <a href="#" class="btn btn-sm btn-primary shadow-sm image-upload-btn fs-12">배너이미지 선택</a>
                                                        <a href="#" class="btn btn-sm btn-primary ml-1 shadow-sm preview-btn fs-12">미리보기</a>
                                                    </div>
                                                    <div class="image-editer">
                                                        <textarea name="contents" id="contents" style="width:100%" class="form-control form-control-sm">{{@$banner->contents}}</textarea>

                                                        <!-- editor1는 에디터 기능을 사용하기 위해 존재 지우지 말아주세요. -->
                                                        <textarea id="editor1"></textarea>
                                                    </div>
                                                    <div class="image-preview">

                                                    </div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th>링크</th>
                                                <td>
                                                    <ul class="links">
                                                        <li class="form-inline inline_input_box d-flex mb-1">
                                                            <input type="text" name="url1" id="url1" class="form-control form-control-sm" style="width:50%" value="{{@$banner->url1}}">
                                                            <div class="form-inline form-check-box ml-1">
                                                                <div class="custom-control custom-checkbox">
                                                                    <input type="checkbox" name="target1" id="target1" class="custom-control-input" value="_blank" @if(@$banner->target1=='_blank') checked @endif>
                                                                    <label class="custom-control-label" for="target1">새창열기</label>
                                                                </div>
                                                            </div>
                                                            <a href="#" onclick="addLink('1')" class="btn btn-sm btn-primary shadow-sm ml-1 link-1 fs-12">link1 삽입</a>
                                                        </li>
                                                        @for($i=2; $i <= 10; $i++)
                                                            <li class=" form-inline inline_input_box mb-1 @if(empty(@$banner->{'url'.$i})) d-none @else d-flex @endif" data-num="{{$i}}">
                                                                <input type="text" name="url{{$i}}" id="url{{$i}}" class="form-control form-control-sm" style="width:50%" value="{{@$banner->{'url'.$i} }}">
                                                                <div class="form-inline form-check-box ml-1">
                                                                    <div class="custom-control custom-checkbox">
                                                                        <input type="checkbox" name="target{{$i}}" id="target{{$i}}" class="custom-control-input" value="_blank" @if(@$banner->{'target'.$i}=='_blank') checked @endif>
                                                                        <label class="custom-control-label" for="target{{$i}}">새창열기</label>
                                                                    </div>
                                                                </div>

                                                                <a href="#" onclick="addLink('{{$i}}')" class="btn btn-sm btn-primary shadow-sm ml-1 fs-12 link-{{$i}}">link{{$i}} 삽입</a>
                                                            </li>
                                                        @endfor
                                                        <li>
                                                            <a href="#" class="btn btn-sm btn-primary shadow-sm add-link fs-12">링크추가</a>
                                                        </li>
                                                    </ul>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th>사용여부</th>
                                                <td>
                                                    <div class="form-inline form-radio-box">
                                                        <div class="custom-control custom-radio">
                                                            <input type="radio" name="use_yn" id="use_y" class="custom-control-input" value="Y" checked>
                                                            <label class="custom-control-label" for="use_y">사용</label>
                                                        </div>
                                                        <div class="custom-control custom-radio">
                                                            <input type="radio" name="use_yn" id="use_n" class="custom-control-input" value="N"  @if(@$banner->use_yn == 'N') checked @endif>
                                                            <label class="custom-control-label" for="use_n">미사용</label>
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                            @if ($type == 'edit')
                                                <tr>
                                                    <th>등록일시</th>
                                                    <td>
                                                        <div class="txt_box">{{$banner->rt}}</div>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <th>수정일시</th>
                                                    <td>
                                                        <div class="txt_box">{{$banner->ut}}</div>
                                                    </td>
                                                </tr>
                                                @if ($banner->page === 'main')
                                                <tr>
                                                    <th>메인페이지 코드</th>
                                                    <td>
                                                        <div class="txt_box">
                                                            @if ($banner->arcd != "")
                                                                <p>영역에 배너가 한개라면</p>
                                                                {{$main_code_htmls[0]}}<br>
                                                                <p class="mt-3">또는 영역에 배너가 여러개라면</p>
                                                                {{$main_code_htmls[1]}}<br>
                                                                {{$main_code_htmls[2]}}<br>
                                                                {{$main_code_htmls[3]}}
                                                            @else

                                                            @endif
                                                        </div>
                                                    </td>
                                                </tr>
                                                @endif
                                            @endif
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
</div>
<style>
.links{
    list-style:none;
    padding:0;
    margin:0;
}
.links .d-none{
    display:none;
}
.note-frame{
    display:none;
}
</style>
<script type="text/javascript" charset="utf-8">
    const code = '{{$code}}';
    let isCodeCheck = false;
    var editorToolbar = [
        ['insert', ['picture']],
        ['emoji', ['emoji']],
        ['view', ['codeview']]
    ];

    var editorOptions = {
        lang: 'ko-KR', // default: 'en-US',
        minHeight: 200,
        dialogsInBody: true,
        disableDragAndDrop: false,
        toolbar: editorToolbar,
        imageupload:{
<<<<<<< HEAD
            dir:'/data/head/prm05',
=======
            dir:'/data/head/prm06',
>>>>>>> main
            maxWidth:1280,
            maxSize:10,
            callback : function(image) {
                if ($('#contents')[0].value != "") {
                    $('#contents')[0].value += `\n<img src="${image.url}"/>`;
                } else {
                    $('#contents')[0].value = `<img src="${image.url}"/>`;
                }
            }
        }
    }

    var ed = new HDEditor('#editor1',editorOptions);

    // TEXTAREA 선택영역 코드 삽입처리
    const wrapText = (beginTag, endTag) => {
        const obj = $('#contents')[0];
        if(typeof obj.selectionStart == 'number')
        {
            // Mozilla, Opera, and other browsers
            var start = obj.selectionStart;
            var end   = obj.selectionEnd;
            obj.value = obj.value.substring(0, start) + beginTag + obj.value.substring(start, end) + endTag + obj.value.substring(end, obj.value.length);
        }
        else if(document.selection)
        {
            // Internet Explorer
            // make sure it's the textarea's selection
            obj.focus();
            var range = document.selection.createRange();
            if(range.parentElement() != obj) return false;
            if(typeof range.text == 'string')
                document.selection.createRange().text = beginTag + range.text + endTag;
        }
    };

    const addLink = (num) => {
        event.preventDefault();

        const val = $(`#url${num}`).val();

        if (!val) {
            alert(`URL${num}항목을 입력해주세요.`);
            return;
        }

        wrapText(`<link${num}>`, `</link${num}>`);
    }

    // 이미지 업로드 버튼
    $('.image-upload-btn').click(function(e){
        e.preventDefault();

        if ($('.preview-btn').html() === '배너수정') {
            $('.preview-btn').click();
        }

        $('.note-insert .note-btn').click();
    });

    $('.preview-btn').click(function(e){
        e.preventDefault();

        if (this.innerHTML === '미리보기') {
            this.innerHTML = "배너수정";
            $('.image-preview').html($('#contents').val());
            $('.image-preview').css('display', 'block');
            $('.image-editer').css('display', 'none');
        } else {
            this.innerHTML = "미리보기";
            $('.image-editer').css('display', 'block');
            $('.image-preview').css('display', 'none');
        }
    });

    const validate = () => {
        if ($("#page").val() == "") {
            alert("페이지를 선택해 주세요");
            $("#page")[0].focus();
            return false;
        }

        if($("#area").value == ""){
            alert("영역을 입력해 주세요.");
            $("#area")[0].focus();
            return false;
        }

        if($('#code').length > 0){
            if($("#code").val() == ""){
                alert("코드를 입력하십시오.");
                $("#code")[0].focus();
                return false;
            }

            if (isCodeCheck === false){
                alert("사용할 수 없는 코드입니다.");
                $("#check")[0].focus();
                return false;
            }
        }

        if ($("#subject").val() == "")
        {
            alert("내용을 입력해 주세요");
            $("#subject")[0].focus();
            return false;
        }

        if ($('[name=type]:checked').length == 0)
        {
            alert("구분을 선택해 주세요.");
            $("type").focus();
            return false;
        }

        if($('#contents').val() == ""){
            alert("배너를 입력해 주세요.");
            return false;
        }

        if ($("url1").value == "")
        {
            alert("링크를 입력해 주세요");
            $("url1").focus();
            return false;
        }

        if ($('[name=use_yn]:checked').length == 0)
        {
            alert("사용여부를 선택해 주세요.");
            $("use_yn").focus();
            return false;
        }
    }

    let linkMax = false;

    $('.add-link').click(function(e){
        e.preventDefault();
        if (linkMax) {
            alert("링크는 10개까지 추가가 가능합니다.");
            return;
        }

        let isAdd = false;

        $('.links .d-none').each(function(){
            if (!isAdd && this.className?.indexOf('d-none') > 0) {
                isAdd = true;
                $(this).removeClass('d-none').addClass('d-flex');
                linkMax = $(this).attr('data-num') == 10;
            }
        });
    });

    $('.update-btn').click(function(e){
        if (validate() === false) return;
        if (confirm('해당내용을 수정하시겠습니까?') === false) return;

        const data = $('form[name="detail"]').serialize();
        
        $.ajax({    
            type: "put",
            url: `/head/promotion/prm05/${code}`,
            data: data,
            success: function(data) {
                alert("그룹이 수정되었습니다.");
                location.reload();
            },
            error: function(res){
                alert(res.responseJSON.message);
            }
        });
    });
    
    $('.save-btn').click(function(e){
        if (validate() === false) return;
        if (confirm('해당내용을 저장하시겠습니까?') === false) return;

        const data = $('form[name="detail"]').serialize();
        
        $.ajax({    
            type: "post",
            url: `/head/promotion/prm05`,
            data: data,
            success: function(code) {
                alert("배너가 등록되었습니다.");
                location.href=`/head/promotion/prm05/show/edit/${code}`;
            },
            error: function(res){
                alert(res.responseJSON.message);
            }
        });
    });

    $('.delete-btn').click(function(e){
        if (confirm('해당내용을 삭제하시겠습니까?') === false) return;

        $.ajax({    
            type: "delete",
            url: `/head/promotion/prm05/${code}`,
            success: function(data) {
                alert("삭제되었습니다.");
                window.close();
            },
            fail: function(){
                console.log('에러');
            }
        });
    });

    if ($('#code').length > 0) {
        $('#code').change(() => {
            $.ajax({    
                type: "get",
                url: `/head/promotion/prm05/code/${$('#code').val()}`,
                success: function(data) {
                    isCodeCheck = data.cnt === 0;

                    if (isCodeCheck) {
                        $('.code-check-text').html('사용할 수 있는 코드입니다.').css('color', 'blue');
                    } else {
                        $('.code-check-text').html('사용할 수 없는 코드입니다.').css('color', 'red');
                    }
                }
            });
        });
    }
</script>
@stop

@extends('head_with.layouts.layout')
@section('title','파트너 공지사항')
@section('content')

<script type="text/javascript" src="/handle/editor/editor.js"></script>
<script type="text/javascript" src="/handle/editor/summernote/summernote-lite.min.js"></script>
<script type="text/javascript" src="/handle/editor/summernote/lang/summernote-ko-KR.js"></script>
<script type="text/javascript" src="/handle/editor/summernote/plugin/summernote-ext-ssm-emoji/summernote-ext-ssm-emoji.js?v=2020081801"></script>
<link rel="stylesheet" href="/handle/editor/summernote/summernote-lite.min.css">
<link rel="stylesheet" href="/handle/editor/summernote/plugin/summernote-ext-ssm-emoji/summernote-ext-ssm-emoji.css?v=2020081821">

<div class="show_layout">
    <div class="page_tit">
        <h3 class="d-inline-flex">파트너 공지사항</h3>
        <div class="d-inline-flex location">
            <span class="home"></span>
            <span>/ 입점/정산</span>
            <span>/ 파트너 공지사항</span>
        </div>
    </div>
    <form method="post" name="store">
        @csrf
        <div class="card_wrap aco_card_wrap">
            <div class="card">
                <div class="card-header mb-0 justify-content-between d-flex">
                    <div></div>
                    <div>
                        @if($no > 0)
                        <button type="button" onclick="return Update('{{ $no }}');" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm"><i class="bx bx-save fs-14 mr-1"></i> 저장</button>
                        <button type="button" href="#" onclick="return Destroy('{{ $no }}');" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm"><i class="far fa-trash-alt fs-12 mr-1"></i> 삭제</button>
                        @else
                        <button type="button" href="#" onclick="Create();return false;" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm"><i class="bx bx-save fs-14 mr-1"></i> 저장</button>
                        @endif
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-12">
                            <div class="table-box-ty2 mobile">
                                <table class="table incont table-bordered" id="dataTable" width="100%" cellspacing="0">
                                    <tr>
                                        <th>작성자</th>
                                        <td>
                                            <div class="txt_box">
                                                <input type='text' class="form-control form-control-sm wd100" name='name' value='{{$user->name}}' required>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>제목</th>
                                        <td>
                                            <div class="txt_box">
                                                <input type='text' class="form-control form-control-sm wd100" name='subject' value='{{$user->subject}}' required>
                                                @error(' subject') <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                                </span>
                                                @enderror
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>내용</th>
                                        <td>
                                            <div>
                                                <input type="hidden" name="content" value='{{$user->content}}' />
                                                <textarea id="editor1">{{$user->content}}</textarea>
                                            </div>
                                        </td>
                                    </tr>
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
            minHeight: 200,
            dialogsInBody: true,
            disableDragAndDrop: false,
            toolbar: editorToolbar,
            imageupload: {
                dir: '/data/partner',
                maxWidth: 1280,
                maxSize: 10
            }
        }
        ed = new HDEditor('#editor1', editorOptions, true);
    });

    function Create() {
        var frm = $('form[name=store]');
        //console.log(frm.serialize());

        if ($('input[name="subject"]').val() === '') {
            $('input[name="subject"]').focus();
            alert('제목을 입력해 주세요.');
            return false;
        }

        if ($('#editor1').val() === '') {
            $('#editor1').focus();
            alert('내용을 입력해 주세요.');
            return false;
        }

        $('input[name="content"]').val(ed.html());

        $.ajax({
            method: 'put',
            url: '/head/partner/pat01/store',
            data: frm.serialize(),
            dataType: 'json',
            success: function(data) {
                if (data.code == '200') {
                    document.location.href = '/head/partner/pat01'
                } else {
                    alert('처리 중 문제가 발생하였습니다. 다시 시도하여 주십시오.');
                }
            },
            error: function(e) {
                    console.log(e.responseText)
            }
        });
    }

    function Update(no) {
        var frm = $('form[name=store]');
        //console.log(frm.serialize());

        // editor value
        //console.log(ed.html());
        $('input[name="content"]').val(ed.html());

        $.ajax({
            method: 'put',
            url: '/head/partner/pat01/edit/' + no,
            data: frm.serialize(),
            success: function(data) {
                if (data.code == '200') {
                    document.location.href = '/head/partner/pat01'
                } else {
                    alert('처리 중 문제가 발생하였습니다. 다시 시도하여 주십시오.');
                }
            },
            error: function(res, status, error) {
                console.log(res.responseText);
            }
        });
    }

    function Destroy(no) {
        var frm = $('form');
        //console.log(frm.serialize());
        if (!confirm("삭제 하시겠습니까?")) {
            return false;
        }

        $.ajax({
            method: 'get',
            url: '/head/partner/pat01/del/' + no,
            data: frm.serialize(),
            success: function(data) {
                if (data.code == '200') {
                    document.location.href = '/head/partner/pat01'
                } else {
                    alert('처리 중 문제가 발생하였습니다. 다시 시도하여 주십시오.');
                }
            },
            error: function(xhr, status, error) {
                console.log(xhr.responseText);
            }
        });
    }
</script>
@stop

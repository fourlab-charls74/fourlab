@extends('partner_with.layouts.layout')
@section('title','공지사항')
@section('content')
<script type="text/javascript" src="/handle/editor/editor.js"></script>
<script type="text/javascript" src="/handle/editor/summernote/summernote-lite.min.js"></script>
<script type="text/javascript" src="/handle/editor/summernote/lang/summernote-ko-KR.js"></script>
<script type="text/javascript" src="/handle/editor/summernote/plugin/summernote-ext-ssm-emoji/summernote-ext-ssm-emoji.js?v=2020081801"></script>
<link rel="stylesheet" href="/handle/editor/summernote/summernote-lite.min.css" >
<link rel="stylesheet" href="/handle/editor/summernote/plugin/summernote-ext-ssm-emoji/summernote-ext-ssm-emoji.css?v=2020081821" >
<div class="show_layout">
    <div class="page_tit">
        <h3 class="d-inline-flex">공지사항</h3>
        <div class="d-inline-flex location">
            <span class="home"></span>
            <span>/ 공지사항</span>
        </div>
    </div>

    <form method="post" name="store" action="/partner/promotion/prm01">
        @csrf
        <div class="card_wrap aco_card_wrap">
            <div class="card">
                <div class="card-header mb-0 justify-content-between d-flex">
                    <div></div>
                    <div>
                        <button type="button" onclick="document.location.href = '/partner/support/spt01';" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm"><i class="bx fs-14 mr-1"></i>목록</button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-12">
                            <div class="table-box-ty2 mobile">
                                <table class="table incont table-bordered" id="dataTable" width="100%" cellspacing="0">
                                    <tr>
                                        <th>제목</th>
                                        <td colspan="3">
                                            {{$user->subject}}
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>작성자</th>
                                        <td>
                                            {{$user->admin_nm}}
                                        </td>
                                        <th>작성일시</th>
                                        <td>
                                            {{$user->regi_date}}
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>내용</th>
                                        <td colspan="3">
                                            {!! $user->content !!}
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
            ['view', ['undo', 'redo', 'codeview','help']]
        ];
        var editorOptions = {
            lang: 'ko-KR', // default: 'en-US',
            minHeight: 200,
            dialogsInBody: true,
            disableDragAndDrop: false,
            toolbar: editorToolbar,
            imageupload:{
                dir:'/data/partner',
                maxWidth:1280,
                maxSize:10
            }
        }
        ed = new HDEditor('#editor1',editorOptions);
    });

    function Create() {
        var frm = $('form');
        //console.log(frm.serialize());

        $.ajax({
            async: true,
            method: 'post',
            url: '/partner/support/spt01',
            data: frm.serialize(),
            success: function (data) {
                var res = jQuery.parseJSON(data);
                if(res.code == '200'){
                    document.location.href = '/partner/support/spt01'
                } else {
                    alert('처리 중 문제가 발생하였습니다. 다시 시도하여 주십시오.');
                }
            },
            error: function(res, status, error) {
                res_err = jQuery.parseJSON(res.responseText);
                if(res_err.message){
                    alert(res_err.message);
                }
                console.log(error);
            }
        });
    }

    function Update(no) {
        var frm = $('form');
        //console.log(frm.serialize());

        // editor value
        $('input[name="content"]').val(ed.html());

        $.ajax({
            async: true,
            method: 'put',
            url: '/partner/support/spt01/' + no,
            data: frm.serialize(),
            success: function (data) {
                var res = jQuery.parseJSON(data);
                if(res.code == '200'){
                    document.location.href = '/partner/support/spt01'
                } else {
                    alert('처리 중 문제가 발생하였습니다. 다시 시도하여 주십시오.');
                }
            },
            error: function(res, status, error) {
                res_err = jQuery.parseJSON(res.responseText);
                if(res_err.message){
                    alert(res_err.message);
                }
                console.log(error);
            }
        });
    }

    function Destroy(no) {
        var frm = $('form');
        //console.log(frm.serialize());

        $.ajax({
            async: true,
            method: 'delete',
            url: '/partner/support/spt01/' + no,
            data: frm.serialize(),
            success: function (data) {
                var res = jQuery.parseJSON(data);
                if(res.code == '200'){
                    document.location.href = '/partner/support/spt01'
                } else {
                    alert('처리 중 문제가 발생하였습니다. 다시 시도하여 주십시오.');
                }
            },
            error: function(request, status, error) {
                console.log("error")
            }
        });
    }
</script>
@stop

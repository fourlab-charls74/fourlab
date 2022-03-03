@extends('head_with.layouts.layout-nav')
@section('title','입금자 찾기')
@section('content')

<script type="text/javascript" src="/handle/editor/editor.js"></script>
<script type="text/javascript" src="/handle/editor/summernote/summernote-lite.min.js"></script>
<script type="text/javascript" src="/handle/editor/summernote/lang/summernote-ko-KR.js"></script>
{{-- <script type="text/javascript" src="/handle/editor/summernote/plugin/summernote-ext-ssm-emoji/summernote-ext-ssm-emoji.js?v=2020081801"></script> --}}
<link rel="stylesheet" href="/handle/editor/summernote/summernote-lite.min.css">
{{-- <link rel="stylesheet" href="/handle/editor/summernote/plugin/summernote-ext-ssm-emoji/summernote-ext-ssm-emoji.css?v=2020081821"> --}}

<div class="show_layout py-3 px-sm-3">
    <div class="page_tit mb-3 d-flex align-items-center justify-content-between">
        <div>
            <h3 class="d-inline-flex">입금자 찾기</h3>
            <div class="d-inline-flex location">
                <span class="home"></span>
                <span>/ 프로모션</span>
                <span>/ 입금자 찾기</span>
            </div>
        </div>
        <div class="card-header mb-0 justify-content-between d-flex">
            <div>
                <button onclick="return Save();" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm"><i class="bx bx-save fs-14 mr-1"></i> 저장</button>
                @if(@$brd->type !== 'create')
                    <button href="#" onclick="return Del();" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm"><i class="far fa-trash-alt fs-12 mr-1"></i> 삭제</button>
                @endif
            </div>
        </div>
    </div>
    <form method="post" name="store" action="/head/promotion/prm04">
        @csrf
        <div class="card_wrap aco_card_wrap">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-12">
                            <div class="table-box-ty2 mobile">
                                <table class="table incont table-bordered" id="dataTable" width="100%" cellspacing="0">
                                    <tr>
                                        <th class="required">작성자</th>
                                        <td>
                                            <div class="flax_box">
                                                <input type='text' class="form-control form-control-sm search-all" name='author_nm' value='{{ @$brd->name }}'>
                                            </div>
                                        </td>
                                        <th class="required">이메일</th>
                                        <td>
                                            <div class="flax_box">
                                                <input type='text' class="form-control form-control-sm search-all" name='author_email' value='{{ @$brd->email }}'>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th class="required">제목</th>
                                        <td colspan="3">
                                            <div class="txt_box">
                                                <input type='text' class="form-control form-control-sm wd100" name='subject' value='{{ @$brd->subject }}' required>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th class="required">내용</th>
                                        <td colspan="3">
                                            <div>
                                                <input type="hidden" name="content" value='{{ @$brd->content }}' />
                                                <textarea id="editor1">{{ @$brd->content }}</textarea>
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
    let ed;
    const type = '{{ @$brd->type }}';
    const idx = '{{ @$brd->idx }}';

    $(document).ready(function() {
        var editorToolbar = [
            ['font', ['bold', 'underline', 'clear']],
            ['color', ['color']],
            ['para', ['paragraph']],
            ['emoji', ['emoji']],
            ['view', ['undo', 'redo', 'codeview', 'help']]
        ];
        var editorOptions = {
            lang: 'ko-KR',
            minHeight: 300,
            dialogsInBody: true,
            disableDragAndDrop: false,
            toolbar: editorToolbar,
        }
        ed = new HDEditor('#editor1', editorOptions, true);
    });
</script>

<script type="text/javascript" charset="utf-8">
    function Save() {
        const form = $("form[name='store']");
        let url = '';

        if($("input[name='subject']").val() === '') return alert("제목을 입력해주세요.");
        if($("#editor1").val() === '') return alert("내용을 입력해주세요.");
        if(!confirm("저장하시겠습니까?")) return;
        
        $("input[name='content']").val(ed.html());
        if(type === 'create') url = '/add';
        else url = '/update/' + idx;

        $.ajax({
            method: 'put',
            url: '/head/promotion/prm04' + url,
            data: form.serialize(),
            dataType: 'json',
            success: function(res) {
                alert(res.message);
                if (res.code === 200) {
                    window.opener.Search();
                    window.close();
                }
            },
            error: function(err) {
                console.log(err);
            }
        });
    }

    function Del() {
        if(!confirm("삭제하시겠습니까?")) return;

        $.ajax({
            method: 'delete',
            url: '/head/promotion/prm04/delete/' + idx,
            success: function(res) {
                alert(res.message);
                if (res.code === 200) {
                    window.opener.Search();
                    window.close();
                }
            },
            error: function(err) {
                console.log(err);
            }
        });  
    }
</script>

@stop
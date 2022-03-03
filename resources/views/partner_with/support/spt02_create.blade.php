@extends('partner_with.layouts.layout')
@section('title','Q&A')
@section('content')

<!-- summer note -->
<script type="text/javascript" src="/handle/editor/editor.js"></script>
<script type="text/javascript" src="/handle/editor/summernote/summernote-lite.min.js"></script>
<script type="text/javascript" src="/handle/editor/summernote/lang/summernote-ko-KR.js"></script>
<link rel="stylesheet" href="/handle/editor/summernote/summernote-lite.min.css" >
<link rel="stylesheet" href="/handle/editor/summernote/plugin/summernote-ext-ssm-emoji/summernote-ext-ssm-emoji.css?v=2020081821" >

<div class="show_layout">
    <div class="page_tit">
        <h3 class="d-inline-flex">Q&A</h3>
        <div class="d-inline-flex location">
            <span class="home"></span>
            <span>/ Q&A</span>
        </div>
    </div>
    <form method="post" name="store">
        @csrf
        <div class="card_wrap aco_card_wrap">
            <div class="card">
                <div class="card-header mb-0 justify-content-between d-flex">
                    <div></div>
                    <div>
                        <button type="button" href="#" onclick="return create('{{ @$idx }}');" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm"><i class="bx bx-save fs-14 mr-1"></i> 저장</button>
                        <button type="button" onclick="document.location.href = '/partner/support/spt02';" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm"><i class="bx fs-14 mr-1"></i>목록</button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-12">
                            <div class="table-box-ty2 mobile">
                                <table class="table incont table-bordered" id="dataTable" width="100%" cellspacing="0">
                                    <tr>
                                        <th><label for="type">분류</label></th>
                                        <td>
                                            <select id='type' name='type' class="form-control form-control-sm">
                                                <option value=''>선택</option>
                                                @foreach ($qna_types as $qna_type)
                                                    <option value='{{ $qna_type->code_id }}'>
                                                        {{ $qna_type->code_val }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th><label for="subject">제목</label></th>
                                        <td>
                                            <input type='text' class="form-control form-control-sm" id='subject' name='subject' value='{{@$list->subject}}'>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th><label for="question">내용</label></th>
                                        <td>
                                            <dd>
                                            <div class="area_box edit_box">
                                                <textarea name="question" id="question" class="form-control editor1"></textarea>
                                            </div>
                                            </dd>
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
            ['emoji', ['emoji']],
            ['view', ['undo', 'redo', 'codeview','help']]
        ];
        var editorOptions = {
            lang: 'ko-KR', // default: 'en-US',
            minHeight: 100,
            height: 150,
            dialogsInBody: true,
            disableDragAndDrop: false,
            toolbar: editorToolbar
        }
        ed = new HDEditor('.editor1',editorOptions, true);
    });

    /**
     * @return {boolean}
     */
    function create() {
        if($('#type').val() == ""){
            alert('분류를 선택해 주십시오.');
            $('#type').focus();
            return false;
        }
        if($('#subject').val() == ""){
            alert('제목을 입력해 주십시오.');
            $('#subject').focus();
            return false;
        }
        if($('#question').val() == ""){
            alert('문의내용을 입력해 주십시오.');
            $('#question').focus();
            return false;
        }

        if(confirm('저장하시겠습니까?')){
            var frm = $('form[name=store]');
            $.ajax({
                method: 'post',
                url: '/partner/support/spt02/store',
                data: frm.serialize(),
                success: function (data) {
                    if(data.result == 1){
                        document.location.href = '/partner/support/spt02'
                    } else {
                        console.log(data.msg);
                    }
                },
                error: function(xhr, status, error) {
                    console.log(xhr.responseText)
                }
            });
        }
    }
</script>
@stop

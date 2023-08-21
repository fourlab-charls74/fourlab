@extends('head_with.layouts.layout-nav')
@section('title','공지사항')
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
            <h3 class="d-inline-flex">공지사항</h3>
            <div class="d-inline-flex location">
                <span class="home"></span>
                <span>/ 프로모션</span>
                <span>/ 공지사항</span>
            </div>
        </div>
        <div class="card-header mb-0 justify-content-between d-flex">
            <div>
                @if($no > 0)
                <button onclick="return Update('{{ $no }}');" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm"><i class="bx bx-save fs-14 mr-1"></i> 저장</button>
                <button href="#" onclick="return Destroy('{{ $no }}');" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm"><i class="far fa-trash-alt fs-12 mr-1"></i> 삭제</button>
                @else
                <button href="#" onclick="Create();return false;" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm"><i class="bx bx-save fs-14 mr-1"></i> 저장</button>
                @endif
            </div>
        </div>
    </div>

    <form method="post" name="store" action="/head/promotion/prm01">
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
                                                <input type='text' class="form-control form-control-sm search-all" name='admin_nm' value='{{ @$user->admin_nm }}'>
                                            </div>
                                        </td>
                                        <th class="required">이메일</th>
                                        <td>
                                            <div class="flax_box">
                                                <input type='text' class="form-control form-control-sm search-all" name='admin_email' value='{{ @$user->admin_email }}'>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th class="required">공개여부</th>
                                        <td>
                                            <div class="form-inline form-radio-box">
                                                <div class="custom-control custom-radio">
													<input type="radio" name="use_yn" id="use_yn_y" class="custom-control-input" value = 'Y' @if ($user->use_yn == 'Y') checked @endif>
													<label class="custom-control-label" for="use_yn_y">예</label>
                                                </div>
                                                <div class="custom-control custom-radio">
													<input type="radio" name="use_yn" id="use_yn_n" class="custom-control-input" value = 'N' @if ($user->use_yn == 'N') checked @endif>
													<label class="custom-control-label" for="use_yn_n">아니요</label>
                                                </div>
                                            </div>
                                        </td>
                                        <th class="required">공지</th>
                                        <td>
                                            <div class="form-inline form-check-box">
                                                <div class="custom-control custom-checkbox">
                                                    <input type="checkbox" id="main_yn" class="custom-control-input" name="main_yn" value="Y" @if ($user->main_yn == 'Y') checked @endif>
                                                    <label class="custom-control-label" for="main_yn">메인 공지</label>
                                                </div>
                                                <div class="custom-control custom-checkbox">
                                                    <input type="checkbox" id="notice_yn" class="custom-control-input" name="notice_yn" value="Y" @if ($user->notice_yn == 'Y') checked @endif>
                                                    <label class="custom-control-label" for="notice_yn">게시판 공지</label>
                                                </div>
                                                <div class="custom-control custom-checkbox">
                                                    <input type="checkbox" id="popup_yn" class="custom-control-input" name="popup_yn" value="Y" @if ($user->popup_yn == 'Y') checked @endif>
                                                    <label class="custom-control-label" for="popup_yn">팝업 공지</label>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>팝업유형</th>
                                        <td colspan="12">
                                            <div class="form-inline form-radio-box">
                                                <div class="custom-control custom-radio">
                                                    <input type="radio" name="popup_type" id="popup_type_A" class="custom-control-input" value="A" @if ($user->popup_type == 'A') checked @endif>
                                                    <label class="custom-control-label" for="popup_type_A">A형(헤드제목 + 컨텐츠 형태)</label>
                                                </div>
                                                <div class="custom-control custom-radio">
                                                    <input type="radio" name="popup_type" id="popup_type_B" class="custom-control-input" value="B" @if ($user->popup_type == 'B') checked @endif>
                                                    <label class="custom-control-label" for="popup_type_B">B형(본문제목 + 컨텐츠 형태)</label>
                                                </div>
                                                <div class="custom-control custom-radio">
                                                    <input type="radio" name="popup_type" id="popup_type_C" class="custom-control-input" value="C" @if ($user->popup_type == 'C') checked @endif>
                                                    <label class="custom-control-label" for="popup_type_C">C형(컨텐츠 형태)</label>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th class="required">제목</th>
                                        <td colspan="3">
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
                                        <th class="required">내용</th>
                                        <td colspan="3">
                                            <div>
                                                <input type="hidden" name="content" value='{{$user->content}}' />
                                                <textarea id="editor1">{{$user->content}}</textarea>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>상품상세출력여부</th>
                                        <td colspan="12">
                                            <div class="form-inline form-radio-box">
                                                <div class="custom-control custom-radio">
                                                    <input type="radio" name="disp_prd_yn" id="disp_prd_Y" class="custom-control-input" value="Y" @if ($user->disp_prd_yn == 'Y') checked @endif>
                                                    <label class="custom-control-label" for="disp_prd_Y">예</label>
                                                </div>
                                                <div class="custom-control custom-radio">
                                                    <input type="radio" name="disp_prd_yn" id="disp_prd_N" class="custom-control-input" value="N" @if ($user->disp_prd_yn == 'N') checked @endif>
                                                    <label class="custom-control-label" for="disp_prd_N">아니오</label>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>상품상세출력</th>
                                        <td colspan="12">
                                            <div class="form-inline form-radio-box">
                                                <div class="custom-control custom-radio">
                                                    <input type="radio" name="disp_prd_type" id="disp_prd_type_A" class="custom-control-input" value="A" @if ($user->disp_prd_type == 'A') checked @endif>
                                                    <label class="custom-control-label" for="disp_prd_type_A">전체</label>
                                                </div>
                                                <div class="custom-control custom-radio">
                                                    <input type="radio" name="disp_prd_type" id="disp_prd_type_B" class="custom-control-input" value="B" @if ($user->disp_prd_type == 'B') checked @endif>
                                                    <label class="custom-control-label" for="disp_prd_type_B">브랜드</label>
                                                </div>
                                                <div class="custom-control custom-radio">
                                                    <input type="radio" name="disp_prd_type" id="disp_prd_type_P" class="custom-control-input" value="P" @if ($user->disp_prd_type == 'P') checked @endif>
                                                    <label class="custom-control-label" for="disp_prd_type_P">상품(최대 100개)</label>
                                                </div>
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
                dir: '/data/head/board/notice_shop',
                maxWidth: 1280,
                maxSize: 10
            }
        }
        ed = new HDEditor('#editor1', editorOptions, true);
    });

    function Create() {
        var frm = $('form');
        console.log(frm.serialize());

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
            url: '/head/promotion/prm01/store',
            data: frm.serialize(),
            dataType: 'json',
            success: function(data) {
                if (data.code == '200') {
                    alert(data.message);
                    window.opener.Search();
                    window.close();
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
        $('input[name="content"]').val(ed.html());

        $.ajax({
            async: true,
            method: 'put',
            url: '/head/promotion/prm01/edit/' + no,
            data: frm.serialize(),
            success: function(data) {
                var res = jQuery.parseJSON(data);
                if (res.code == '200') {
                    alert(res.message);
                    window.opener.Search();
                    window.close();
                } else {
                    alert('처리 중 문제가 발생하였습니다. 다시 시도하여 주십시오.');
                }
            },
            error: function(res, status, error) {
                res_err = jQuery.parseJSON(res.responseText);
                if (res_err.message) {
                    alert(res_err.message);
                }
                console.log(error);
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
            async: true,
            method: 'get',
            url: '/head/promotion/prm01/del/' + no,
            data: frm.serialize(),
            success: function(data) {
                var res = jQuery.parseJSON(data);
                if (res.code == '200') {
                    alert(res.message);
                    window.opener.Search();
                    window.close();
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

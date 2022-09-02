@extends('store_with.layouts.layout')
@section('title','매장 공지사항')
@section('content')

<script type="text/javascript" src="/handle/editor/editor.js"></script>
<script type="text/javascript" src="/handle/editor/summernote/summernote-lite.min.js"></script>
<script type="text/javascript" src="/handle/editor/summernote/lang/summernote-ko-KR.js"></script>
<script type="text/javascript" src="/handle/editor/summernote/plugin/summernote-ext-ssm-emoji/summernote-ext-ssm-emoji.js?v=2020081801"></script>

<div class="show_layout">
    <div class="page_tit">
        <h3 class="d-inline-flex">매장 공지사항</h3>
        <div class="d-inline-flex location">
            <span class="home"></span>
            <span>/ 매장관리</span>
            <span>/ 매장 공지사항</span>
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
                        <button type="button" href="#" onclick="history.back();" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm"> 목록</button>
                        <!-- <button type="button" href="#" onclick="return Destroy('{{ $no }}');" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm"><i class="far fa-trash-alt fs-12 mr-1"></i> 삭제</button> -->
                        @else
                        <button type="button" href="#" onclick="Create();return false;" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm"><i class="bx bx-save fs-14 mr-1"></i> 저장</button>
                        <button type="button" href="#" onclick="location.href = document.referrer; " class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm"> 취소</button>
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
                                                <input type='text' class="form-control form-control-sm wd100" name='name' value='{{$user->name}}' required readonly>
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
                                    <tr>
                                        <th>매장 선택</th>
                                        <td>
                                            @if($no > 0)
                                            <div class="form-inline inline_btn_box">
                                                <input type='hidden' id="store_nm" name="store_nm">
                                                        @foreach($storeCode as $sc)
                                                            @if($sc->store_nm == null)
                                                                <b style="color:red;">※전체 매장 공지입니다.</b>
                                                            @elseif($sc->check_yn == 'Y')
                                                                <div class="store_sel" data-store='{{$sc->store_cd}}' style="border:0.5px solid gray; border-radius: 5px; margin-right:15px;margin-bottom:10px;padding-left:10px;">
                                                                    <span>{{$sc->store_nm}}
                                                                        <b style="color:blue;">[확인]</b>&nbsp;&nbsp;
                                                                    </span>
                                                                </div>
                                                            @elseif($sc->check_yn == 'N')
                                                                <div class="store_sel" data-store='{{$sc->store_cd}}' style="border:0.5px solid gray; border-radius: 5px; margin-right:15px;margin-bottom:10px;padding-left:10px;">
                                                                    <span>{{$sc->store_nm}}
                                                                        <b style="color:red;">[미확인]</b>&nbsp;
                                                                        <button type= "button" class= "select_store_delete" onclick="select_store_delete('{{$sc->store_cd}}','{{$sc->ns_cd}}')" style="border:none;background:none;color:#153066;padding-bottom:3px;">x</button>&nbsp;&nbsp;
                                                                    </span>
                                                                </div>
                                                            @endif
                                                        @endforeach
                                                        
                                            </div>
                                            @endif
                                            <div class="form-inline inline_btn_box">
                                                <input type='hidden' id="store_nm" name="store_nm">
                                                <select id="store_no" name="store_no[]" class="form-control form-control-sm select2-store multi_select" multiple ></select>
                                                <a href="javascript:void(0);" class="btn btn-sm btn-outline-primary sch-store"><i class="bx bx-dots-horizontal-rounded fs-16"></i></a>
                                            </div>
                                            @if($no == "")
                                            <span style="color:red">※매장 미선택시 전체 매장 공지로 등록됩니다.</span>
                                            @endif
                                               
                                            
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

    let editor1;

    $(document).ready(function() {
        let editorToolbar = [
            ['font', ['bold', 'underline', 'clear']],
            ['color', ['color']],
            ['para', ['paragraph']],
            ['insert', ['picture', 'video']],
            ['emoji', ['emoji']],
            ['view', ['undo', 'redo', 'codeview', 'help']]
        ];
        let editorOptions = {
            lang: 'ko-KR', // default: 'en-US',
            minHeight: 200,
            dialogsInBody: true,
            disableDragAndDrop: false,
            toolbar: editorToolbar,
            imageupload: {
                dir: '/data/store',
                maxWidth: 1280,
                maxSize: 10
            }
        }
        editor1 = new HDEditor('#editor1', editorOptions, true);
    });

    function Create() {
        let frm = $('form[name=store]');
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

        $('input[name="content"]').val(editor1.html());

        $.ajax({
            method: 'put',
            url: '/store/stock/stk31/store',
            data: frm.serialize(),
            dataType: 'json',
            success: function(data) {
                if (data.code == '200') {
                    alert('게시물 등록에 성공하였습니다.');
                    document.location.href = '/store/stock/stk31'
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
        
        let frm = $('form[name=store]');
        
        $('input[name="content"]').val(editor1.html());
        
        $.ajax({
            method: 'put',
            url: '/store/stock/stk31/edit/' + no,
            data: frm.serialize(),
            success: function(data) {
                if (data.code == '200') {
                    alert('게시물 수정에 성공하였습니다.');
                    location.reload();
                } else {
                    alert('처리 중 문제가 발생하였습니다. 다시 시도하여 주십시오.');
                }
            },
            error: function(res, status, error) {
                console.log(res.responseText);
            }
        });
    }
    
    $( ".sch-store" ).on("click", function() {
        searchStore.Open(null, "multiple");
    });
        
</script>

<script>
    
    function select_store_delete(store_cd, ns_cd){
        let ss = document.querySelectorAll(".store_sel");
       
        if(confirm("삭제하시겠습니까?")){
            $.ajax({
                method: 'post',
                url: '/store/stock/stk31/del_store',
                data: {data_store : store_cd, ns_cd : ns_cd},
                success: function(data) {
                    if (data.code == '200') {
                        for(let i = 0;i<ss.length;i++){
                            if(ss[i].dataset.store == store_cd){
                                data_store = ss[i].dataset.store;
                                ss[i].remove();
                                break;
                            }
                        }
                        alert('공지매장 삭제에 성공하였습니다.');
                    } else {
                        alert('처리 중 문제가 발생하였습니다. 다시 시도하여 주십시오.');
                    }
                },
                error: function(res, status, error) {
                    console.log(error);
                }
            });
        }
    }
    
</script>

<link rel="stylesheet" href="/handle/editor/summernote/summernote-lite.min.css">
<link rel="stylesheet" href="/handle/editor/summernote/plugin/summernote-ext-ssm-emoji/summernote-ext-ssm-emoji.css?v=2020081821">
@stop
